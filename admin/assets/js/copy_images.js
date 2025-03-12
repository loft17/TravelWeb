function copyImageUrl(url) {
    // Crear un campo de texto temporal
    var tempInput = document.createElement("input");
    tempInput.value = url;
    document.body.appendChild(tempInput);

    // Seleccionar el contenido del campo de texto
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // Para dispositivos móviles

    // Intentar copiar al portapapeles
    var successful = document.execCommand("copy");

    // Eliminar el campo de texto temporal
    document.body.removeChild(tempInput);

    // Obtener el div de notificación
    var notification = document.getElementById('copyNotification');

    // Mostrar la notificación de copiado si la acción fue exitosa
    if (successful) {
        notification.innerHTML = '¡La URL de la imagen ha sido copiada al portapapeles!'; // Mensaje de éxito
        notification.classList.add('alert-success');  // Aplicamos la clase de éxito
        notification.classList.remove('alert-danger');  // Elimina la clase de error
    } else {
        notification.innerHTML = 'Error al copiar la URL de la imagen.';  // Mensaje de error
        notification.classList.add('alert-danger');  // Aplicamos la clase de error
        notification.classList.remove('alert-success');  // Elimina la clase de éxito
    }

    // Mostrar la notificación
    notification.style.display = 'block';

    // Ocultar la notificación después de 2 segundos
    setTimeout(function() {
        notification.style.display = 'none';
    }, 2000);
}
