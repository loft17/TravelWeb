function editarArticulo(id, nombre, categoria, cantidad, importante) {
    const form = document.querySelector('form');

    // Cambiar el título del formulario
    const headerTitle = document.querySelector('.header-title');
    if (headerTitle) headerTitle.textContent = "Editar Artículo";

    // Modificar los valores de los campos existentes
    form.querySelector('[name="nombre"]').value = nombre;
    form.querySelector('[name="categoria"]').value = categoria;
    form.querySelector('[name="cantidad"]').value = cantidad;
    form.querySelector('[name="importante"]').checked = importante;

    // Agregar campo oculto para el ID si no existe
    let inputId = form.querySelector('[name="id"]');
    if (!inputId) {
        inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id';
        form.appendChild(inputId);
    }
    inputId.value = id;

    // Modificar el botón de envío
    let submitButton = form.querySelector('[name="agregar"]');
    if (submitButton) {
        submitButton.textContent = "Actualizar";
        submitButton.name = "editar"; // Cambiar el nombre del botón para enviar como edición
    }
}