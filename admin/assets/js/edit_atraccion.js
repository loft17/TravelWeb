document.addEventListener("DOMContentLoaded", function() {
    // Inicializar el editor Quill
    var quill = new Quill('#editor-container', {
        theme: 'snow'
    });

    // Función para sincronizar el contenido del editor en el campo oculto antes de enviar el formulario
    window.updateDescription = function() {
        var descripcionHtml = quill.root.innerHTML;
        document.getElementById('descripcion').value = descripcionHtml;
    };

    // Manejo de la subida asíncrona de imagen y actualización de miniatura y campo URL
    var fileInput = document.getElementById('imagen_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (fileInput.files && fileInput.files[0]) {
                var formData = new FormData();
                formData.append('imagen_file', fileInput.files[0]);

                fetch('/admin/includes/functions/edit_atraccion.php?action=upload_image', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else if (data.imagen_url) {
                        document.getElementById('imagen_preview').src = data.imagen_url;
                        document.getElementById('imagen_url').value = data.imagen_url;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
    }
});
