function previewImage(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const imagePreview = document.getElementById('imagePreview');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');

        // Establecer la fuente de la imagen
        imagePreview.src = e.target.result;

        // Hacer visible el contenedor de la vista previa
        imagePreviewContainer.style.display = 'block';
    }

    // Leer la imagen seleccionada
    if (file) {
        reader.readAsDataURL(file);
    }
}