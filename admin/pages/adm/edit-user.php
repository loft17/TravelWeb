<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/get-user.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/upload_img.php';

// Verificar que el parámetro 'id' está presente en la URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $user = obtener_usuario($user_id); // Obtener los datos del usuario

    // Verificar si el usuario existe
    if (!$user) {
        echo "Usuario no encontrado.";
        exit();
    }
} else {
    echo "ID de usuario no especificado.";
    exit();
}
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>
<body>
    <!-- page container area start -->
    <div class="page-container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php';?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php';?>

        <!-- main content area start -->
        <div class="main-content">
            <div class="main-content-inner">
                <div class="row">
                    <!-- No gutters start -->
                    <div class="col-12 mt-5">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="header-title">Editar Usuario</h4>
                                <form method="POST" action="../../includes/functions/edit_user.php?id=<?php echo $user['id']; ?>">
                                    <div class="form-group">
                                        <label for="name">Nombre</label>
                                        <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="rol">Rol</label>
                                        <select class="form-control" name="rol" id="rol" required>
                                            <option value="admin" <?php echo ($user['rol'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                            <option value="usuario" <?php echo ($user['rol'] == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
                                            <option value="invitado" <?php echo ($user['rol'] == 'invitado') ? 'selected' : ''; ?>>Invitado</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Nueva Contraseña</label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese nueva contraseña">
                                    </div>

                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="active" id="active" <?php echo ($user['active'] == 1) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="active">Activo</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary mt-4">Guardar cambios</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- No gutters end -->
                </div>
            </div>
        </div>
        <!-- main content area end -->
    </div>
    <!-- page container area end -->

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php';?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php';?>
    <!-- JavaScript para la vista previa de la imagen -->
    <script src="../../assets/js/previewImage.js"></script>

</body>
</html>
