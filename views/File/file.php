<div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <form>
                    <div class="form-group">
                        <label for="fileInput">Seleccionar archivo:</label>
                        <input type="file" class="form-control-file" id="fileInput">
                    </div>
                    <button type="button" class="btn btn-primary" onclick="uploadFile()">Subir Archivo</button>
                </form>
            </div>
            <div id="result"></div>
        </div>
    </div>

    <!-- Agrega el enlace al JS de Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];

            if (!file) {
                alert('Selecciona un archivo antes de subirlo.');
                return;
            }

            const formData = new FormData();
            formData.append('fileSummary', file);

            axios.post('server.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                $("#result").html("<h4>Resumen: </h4>" + response.data);
            })
            .catch(error => {
                console.error('Error al subir el archivo:', error);
            });
        }
    </script>