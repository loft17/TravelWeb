// Función para copiar el emoji al portapapeles
function copiarEmoji(emoji) {
  // Crear un campo de texto temporal
  var textoTemporal = document.createElement('textarea');
  textoTemporal.value = emoji; // Asignamos el emoji al campo de texto
  document.body.appendChild(textoTemporal); // Añadimos el campo al DOM

  // Seleccionamos y copiamos el contenido del campo de texto
  textoTemporal.select();
  
  // Intentamos ejecutar el comando 'copy'
  var successful = document.execCommand('copy');
  
  // Eliminamos el campo de texto temporal
  document.body.removeChild(textoTemporal);

  // Mostrar la notificación de copiado si la acción fue exitosa
  var notification = document.getElementById('copyNotification');
  
  if (successful) {
      notification.innerHTML = '¡Emoji copiado con éxito!'; // Mensaje de éxito
      notification.classList.add('alert-success');  // Aplicamos la clase de éxito
      notification.classList.remove('alert-danger');  // Elimina la clase de error
  } else {
      notification.innerHTML = 'Error al copiar el emoji.';  // Mensaje de error
      notification.classList.add('alert-danger');  // Aplicamos la clase de error
      notification.classList.remove('alert-success');  // Elimina la clase de éxito
  }

  notification.style.display = 'block';  // Mostrar la notificación
  setTimeout(function() {
      notification.style.display = 'none';  // Ocultar la notificación después de 2 segundos
  }, 2000);
}
