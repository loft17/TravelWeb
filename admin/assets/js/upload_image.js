document.addEventListener("DOMContentLoaded", function () {
    let uploadContainer = document.getElementById('upload-container');
    let fileInput = document.getElementById('fileInput');

    function showNotification(message, type) {
        let notification = document.getElementById('notification');
        notification.className = 'notification alert ' + type;
        notification.innerText = message;
        notification.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    uploadContainer.addEventListener("dragover", function (event) {
        event.preventDefault();
        uploadContainer.style.background = "rgba(0, 0, 0, 0.1)";
    });

    uploadContainer.addEventListener("dragleave", function () {
        uploadContainer.style.background = "transparent";
    });

    uploadContainer.addEventListener("drop", function (event) {
        event.preventDefault();
        uploadContainer.style.background = "transparent";

        let file = event.dataTransfer.files[0];
        if (file) {
            uploadFile(file);
        }
    });

    fileInput.addEventListener("change", function () {
        let file = fileInput.files[0];
        if (file) {
            uploadFile(file);
        }
    });

    function uploadFile(file) {
        let formData = new FormData();
        formData.append("file", file);

        fetch("/admin/includes/functions/upload_imagen.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                showNotification("Imagen subida correctamente", "alert-success");
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification("Error al subir la imagen: " + data, "alert-danger");
            }
        })
        .catch(() => {
            showNotification("Error de conexión", "alert-danger");
        });
    }
});
