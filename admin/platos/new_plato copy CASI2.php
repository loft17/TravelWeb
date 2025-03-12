<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';  // Asegúrate de que head.php tenga los elementos <head> y <meta> adecuados.
include '../../config.php';  // Incluir la configuración de la base de datos
include '../includes/functions/upload_img.php';  // Incluir la función para subir imágenes

// Variable para los mensajes
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = conectar_bd();

    // Recoger los datos del formulario
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $info = $conn->real_escape_string($_POST['info']);
    $puntuacion = (int) $_POST['puntuacion'];
    $comido = isset($_POST['comido']) ? 1 : 0;

    // Aseguramos que la variable $imagen_url esté definida
    $imagen_url = isset($_POST['imagen_url']) ? $_POST['imagen_url'] : '';

    // Procesar la imagen si se ha subido
    if ($imagen_url != '') {
        $sql = "INSERT INTO comida (nombre, info, puntuacion, imagen_url, comido)
                VALUES ('$nombre', '$info', '$puntuacion', '$imagen_url', '$comido')";
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>Comida añadida correctamente.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error al agregar comida: " . $conn->error . "</div>";
        }
    }

    $conn->close();
}


?>

<!doctype html>
<html class="no-js" lang="en">

<body>

    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->
    <!-- page container area start -->
    <div class="page-container">
        <?php include '../includes/templates/sidebar.php'; ?>
        <?php include '../includes/templates/user-profile.php'; ?>

        <!-- main content area start -->
        <div class="main-content">
            
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Nueva comida</div>

                                <!-- Mostrar el mensaje de éxito o error -->
                                <?php if ($message != ''): ?>
                                    <div class="message-container">
                                        <?php echo $message; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Formulario para agregar nueva comida -->
                                
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="nombre">Nombre de la comida</label>
                                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="info">Descripción de la comida</label>
                                        <textarea id="info" name="info" class="form-control" rows="4" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label" for="puntuacion">Puntuación</label>
                                        <select class="form-control" id="puntuacion" name="puntuacion">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" id="comido" name="comido" class="form-check-input">
                                        <label class="form-check-label" for="comido">Comido</label>
                                    </div>

                                    <!-- Campo para subir una imagen -->
                                    <div class="form-group">
                                        <label for="imagen">Subir imagen</label>
                                        <input type="file" id="imagen" name="imagen" class="form-control" onchange="updateImageUrl()">
                                    </div>

                                    <!-- Campo para agregar la URL de la imagen -->
                                    <div class="form-group">
                                        <label for="imagen_url">URL imagen</label>
                                        <input type="text" id="imagen_url" name="imagen_url" class="form-control" value="<?= htmlspecialchars($imagen_url); ?>" placeholder="O ingresa una URL manualmente">
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-3">Añadir comida</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- main content area end -->
    </div>
    <!-- page container area end -->
    <?php include '../includes/templates/footer.php'; ?>
    <?php include '../includes/libraries/scripts.php'; ?>

    <script>
function updateImageUrl() {
    var fileInput = document.getElementById('imagen');
    var urlInput = document.getElementById('imagen_url');
    var messageContainer = document.querySelector('.message-container'); // Seleccionamos el contenedor de mensajes

    // Limpiar cualquier mensaje previo
    if (messageContainer) {
        messageContainer.innerHTML = '';  // Limpiamos cualquier mensaje anterior
    }

    // Verificamos si el archivo es válido
    if (fileInput.files && fileInput.files[0]) {
        var formData = new FormData();
        formData.append('imagen', fileInput.files[0]);

        // Realizamos una solicitud AJAX para subir la imagen al servidor
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../includes/functions/upload_img.php', true); // Asegúrate de que la ruta es correcta

        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText); // Parseamos la respuesta JSON

                    // Limpiar cualquier mensaje de error previo antes de mostrar un nuevo mensaje
                    if (messageContainer) {
                        messageContainer.innerHTML = ''; // Limpiar mensaje previo si existía
                    }

                    // Aquí mostramos el mensaje de éxito o error según la respuesta
                    if (response.status === 'success') {
                        // Si la imagen se subió correctamente, actualizamos el campo de URL
                        urlInput.value = response.ruta;  // La URL de la imagen subida

                        // Mostrar el mensaje de éxito dentro del contenedor de mensajes
                        messageContainer.innerHTML = "<div class='alert alert-success'>" + response.mensaje + "</div>";
                    } else {
                        // Si hubo un error, lo mostramos en el contenedor de mensajes
                        messageContainer.innerHTML = "<div class='alert alert-danger'>" + response.mensaje + "</div>";
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    messageContainer.innerHTML = "<div class='alert alert-danger'>Hubo un error al procesar la respuesta del servidor.</div>";
                }
            } else {
                // Si la solicitud no fue exitosa
                alert('Error al realizar la solicitud');
            }
        };

        // Enviar los datos al servidor
        xhr.send(formData);
    }
}

    </script>

</body>

</html>
