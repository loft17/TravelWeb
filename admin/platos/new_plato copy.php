<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';  // Asegúrate de que head.php tenga los elementos <head> y <meta> adecuados.
include '../../config.php';  // Incluir la configuración de la base de datos

// Variable para los mensajes
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Conectar a la base de datos utilizando la función conectar_bd() desde config.php
    $conn = conectar_bd();

    // Obtener datos del formulario y limpiarlos
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $info = $conn->real_escape_string($_POST['info']);
    $puntuacion = (int) $_POST['puntuacion'];  // Ahora obtenemos la puntuación del select
    $imagen_url = $conn->real_escape_string($_POST['imagen_url']);
    $comido = isset($_POST['comido']) ? 1 : 0; // Si está marcado, comido será 1

    // Insertar los datos en la tabla 'comida'
    $sql = "INSERT INTO comida (nombre, info, puntuacion, imagen_url, comido)
            VALUES ('$nombre', '$info', '$puntuacion', '$imagen_url', '$comido')";

    if ($conn->query($sql) === TRUE) {
        // Mensaje de éxito
        $message = "<div class='alert alert-success'>Comida añadida correctamente.</div>";
    } else {
        // Mensaje de error
        $message = "<div class='alert alert-danger'>Error al agregar comida: " . $conn->error . "</div>";
    }

    // Cerrar la conexión
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
                                <form action="" method="POST">
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

                                    <div class="form-group">
                                        <label for="imagen_url">URL de la imagen</label>
                                        <input type="text" id="imagen_url" name="imagen_url" class="form-control" required>
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" id="comido" name="comido" class="form-check-input">
                                        <label class="form-check-label" for="comido">Comido</label>
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
</body>

</html>
