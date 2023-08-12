
    <div>
        <label for="description">Ingrese una descripción:</label>
        <input type="text" id="description" style="width: 300px;">
        <button onclick="generateImage()">Generar Imagen</button>
    </div>
    <div id="imageGenerated">
    </div>

    <script>
        async function generateImage() {
            const descriptionInput = document.getElementById("description").value;
            //const generatedImageElement = document.getElementById("generatedImage");

            // Aquí deberías hacer la llamada a la API de OpenAI para generar la imagen
            // y obtener la URL de la imagen generada.

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/server.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        $('#imageGenerated').html('<h3>Imagen Generada</h3><br><img id="generatedImage" src="'+ xhr.responseText +'" alt="Imagen Generada">')
                    } else {
                        console.error('Error en la respuesta:', xhr.statusText);
                    }
                }
            };

            xhr.send(JSON.stringify({ image: descriptionInput }));

            // Por ahora, usaremos una imagen de ejemplo
            const exampleImageUrl = "https://via.placeholder.com/300";

            // Mostrar la imagen generada en pantalla
            generatedImageElement.src = exampleImageUrl;
        }
    </script>