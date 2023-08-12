<?php

require __DIR__ . '/vendor/autoload.php'; // remove this line if you use a PHP Framework.
require 'vendor/autoload.php'; // Include the necessary use statements outside the function

use Smalot\PdfParser\Parser;
use thiagoalessio\TesseractOCR\TesseractOCR;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;

use Orhanerday\OpenAi\OpenAi;

$open_ai = new OpenAi('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');


// header('Content-type: text/event-stream');
// header('Cache-Control: no-cache');
header('Content-Type: application/json');
header('Content-Type: application/octet-stream');


$data = file_get_contents("php://input");
$data = json_decode($data, true);

if(isset($data["prompt"])) {
    generateResponse($data["prompt"]);
}

if(isset($data["image"])) {
    generateImage($data["image"]);
}

if(isset($_FILES['audioFile'])) {
    $targetDirectory = 'resources/';
    $targetFilePath = $targetDirectory . basename($_FILES['audioFile']['name']);
    move_uploaded_file($_FILES['audioFile']['tmp_name'], $targetFilePath);
    transcriptAudio();
}

if(isset($_FILES['fileSummary'])) {
    $targetDirectory = 'resources/';
    $targetFilePath = $targetDirectory . basename($_FILES['fileSummary']['name']);
    move_uploaded_file($_FILES['fileSummary']['tmp_name'], $targetFilePath);
    summaryAudio();
}

function generateResponse($text) {
    global $open_ai;
    $chat = $open_ai->chat([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                "role" => "user",
                "content" => $text
            ]
        ],
        'temperature' => 1.0,
        'max_tokens' => 4000,
        'frequency_penalty' => 0,
        'presence_penalty' => 0,
     ]);

     $d = json_decode($chat);
     echo $d->choices[0]->message->content;
}

function generateImage($description) {
    global $open_ai;
    $complete = $open_ai->image([
        "prompt" => $description,
        "n" => 1,
        "size" => "256x256",
        "response_format" => "url",
     ]);

     $d = json_decode($complete);
        echo $d->data[0]->url;
}

function transcriptAudio() {
    global $open_ai;

    $c_file = curl_file_create('resources/'.$_FILES['audioFile']['name']);

    $result = $open_ai->transcribe([
        "model" => "whisper-1",
        "file" => $c_file,
    ]);

    $d = json_decode($result);
    echo $d->text;
}

function summaryAudio() {
    global $open_ai;

    $tmp_file = 'resources/'.$_FILES['fileSummary']['name'];

    $content = extractReadableContent($tmp_file);



    $prompt = "Resumen del siguiente texto: $content";
    
   //echo $prompt;
    $completion = $open_ai->completion([
        'prompt' => $prompt,
        'temperature' => 0.7,
        'max_tokens' => 1000, // Número de tokens en el resumen
    ]);

    $d = json_decode($completion);
    var_dump($d);
    echo $d->choices[0]->text;

} 

function extractReadableContent($file) {
    $fileType = mime_content_type($file);

    if (file_exists($file)) {
        switch ($fileType) {
            case 'application/pdf':
                $parser = new Parser();
                $pdf = $parser->parseFile($file);

                $text = '';

                foreach ($pdf->getPages() as $page) {
                    $text .= $page->getText();
                }
                return $text;

            case 'image/jpeg':
            case 'image/png':
                return (new TesseractOCR($file))->run();

            case 'application/msword': // Word DOC
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': // Word DOCX
                $phpWord = IOFactory::load($file);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                            foreach ($element->getElements() as $textElement) {
                                if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                    $text .= $textElement->getText();
                                }
                            }
                        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text .= $element->getText();
                        }
                    }
                }
                return $text;

            case 'application/vnd.ms-excel': // Excel XLS
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': // Excel XLSX
                $spreadsheet = SpreadsheetIOFactory::load($file);
                return $spreadsheet->getActiveSheet()->getCell('A1')->getValue();

            case 'text/plain': // Plain Text TXT
                return file_get_contents($file);

            default:
                return "Unsupported file type.";
        }
    } else {
        return 'File does not exist.';
    }
}

?>