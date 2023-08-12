<div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="prompt">Ingrese su mensaje:</label>
                    <input type="text" class="form-control" id="prompt" style="width: 100%;">
                </div>
                <button class="btn btn-primary" onclick="generateResponse()">Enviar</button>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h2>Respuesta:</h2>
                <p id="response"></p>
            </div>
        </div>
    </div>

    <script>
        function generateResponse() {
            const promptInput = document.getElementById("prompt").value;

            const responseElement = document.getElementById("response");
            responseElement.textContent = "Generando respuesta...";

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/server.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        responseElement.textContent = xhr.responseText;
                    } else {
                        console.error('Error en la respuesta:', xhr.statusText);
                    }
                }
            };

            xhr.send(JSON.stringify({ prompt: promptInput }));
        }
    </script>