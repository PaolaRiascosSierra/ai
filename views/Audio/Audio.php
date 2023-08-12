
<div class="container mt-5">
        <h1 class="mb-4">Grabador y Transcriptor de Audio</h1>
        <div class="row">
            <div class="col">
                <button id="startRecording" class="btn btn-primary">Comenzar a Grabar</button>
                <button id="stopRecording" class="btn btn-danger" disabled>Detener Grabación</button>
            </div>
            <div class="col">
                <button id="transcribeAudio" class="btn btn-success">Transcribir Audio</button>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <audio id="audioPlayer" controls></audio>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <div id="transcriptionResult">

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const startButton = document.getElementById("startRecording");
        const stopButton = document.getElementById("stopRecording");
        const audioPlayer = document.getElementById("audioPlayer");
        const transcribeButton = document.getElementById("transcribeAudio");
        const transcriptionResult = document.getElementById("transcriptionResult");
        let mediaRecorder;
        let audioChunks = [];

        startButton.addEventListener("click", startRecording);
        stopButton.addEventListener("click", stopRecording);
        transcribeButton.addEventListener("click", transcribeAudio);

        async function startRecording() {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = event => {
                if (event.data.size > 0) {
                    audioChunks.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: "audio/wav" });
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPlayer.src = audioUrl;
            };

            mediaRecorder.start();
            startButton.disabled = true;
            stopButton.disabled = false;
        }

        function stopRecording() {
            mediaRecorder.stop();
            startButton.disabled = false;
            stopButton.disabled = true;
        }

        function transcribeAudio() {
            const formData = new FormData();
            formData.append("audioFile", new Blob(audioChunks, { type: "audio/wav" }), "audio.wav");

            const a = new Blob(audioChunks, { type: "audio/wav" });

            // Cambia la ruta de la URL según corresponda
            const url = '/server.php';

            axios.post(url, formData, {
                headers: {
                    'Content-Type': 'application/octet-stream'
                }
            })
            .then(response => {
                const responseData = response.data;
                transcriptionResult.textContent = "Transcripción: " + responseData;
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
            });
        }

    </script>
