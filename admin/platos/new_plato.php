<!-- new_plato.php -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include '../includes/templates/head.php';  
include '../../config.php';  
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/upload_img.php';

// Variable para los mensajes
$message = '';
$imagen_url = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = conectar_bd();

    $nombre = $conn->real_escape_string($_POST['nombre']);
    $info = $conn->real_escape_string($_POST['info']);
    $puntuacion = (int) $_POST['puntuacion'];
    $comido = isset($_POST['comido']) ? 1 : 0;

    if (isset($_FILES['imagen'])) {
        $resultadoSubida = procesarSubidaImagen($_FILES['imagen']);
        if ($resultadoSubida['status'] == 'success') {
            $imagen_url = $resultadoSubida['ruta'];
        } else {
            $message = "<div class='alert alert-danger'>" . $resultadoSubida['mensaje'] . "</div>";
        }
    }

    $sql = "INSERT INTO comida (nombre, info, puntuacion, imagen_url, comido)
            VALUES ('$nombre', '$info', '$puntuacion', '$imagen_url', '$comido')";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Comida añadida correctamente.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error al agregar comida: " . $conn->error . "</div>";
    }

    $conn->close();
}
?>

<!doctype html>
<html lang="es">
<body>
    <div class="page-container">
        <?php include '../includes/templates/sidebar.php'; ?>
        <?php include '../includes/templates/user-profile.php'; ?>

        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="header-title">Nueva comida</div>

                                <?php if ($message != ''): ?>
                                    <div class="message-container">
                                        <?php echo $message; ?>
                                    </div>
                                <?php endif; ?>

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
                                        <label for="puntuacion">Puntuación</label>
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
                                        <label class="col-form-label" for="imagen">Imagen</label>
                                        <input type="file" id="fileInput" name="imagen" class="form-control" accept="image/*" required>
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
    </div>
    <?php include '../includes/templates/footer.php'; ?>
    <?php include '../includes/libraries/scripts.php'; ?>
</body>
</html>
