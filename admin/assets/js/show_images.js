document.addEventListener("DOMContentLoaded", function () {
    console.log("Script cargado correctamente.");

    function showNotification(message, type) {
        let notification = document.getElementById('notification');
        notification.className = 'notification alert ' + type;
        notification.innerText = message;
        notification.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    document.querySelectorAll('.image-copy').forEach(img => {
        img.addEventListener('click', function () {
            let url = window.location.origin + this.getAttribute('data-url');
            console.log("Intentando copiar URL:", url);

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    showNotification("URL copiada al portapapeles", "alert-success");
                }).catch(err => {
                    console.error("Error al copiar la URL:", err);
                    showNotification("Error al copiar la URL", "alert-danger");
                });
            } else {
                // Método alternativo para navegadores antiguos
                let tempInput = document.createElement("input");
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                showNotification("URL copiada al portapapeles (método alternativo)", "alert-success");
            }
        });
    });

    window.deleteImage = function (imagePath) {
        if (confirm("¿Seguro que deseas eliminar esta imagen?")) {
            fetch('/admin/includes/functions/delete_image.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'image=' + encodeURIComponent(imagePath)
            })
            .then(response => response.text())
            .then(data => {
                if (data === "success") {
                    showNotification("Imagen eliminada correctamente", "alert-success");
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification("Error al eliminar la imagen", "alert-danger");
                }
            })
            .catch(() => {
                showNotification("Error de conexión", "alert-danger");
            });
        }
    };
});