<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Procesar creación de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $rol      = in_array($_POST['rol'] ?? '', ['admin', 'usuario']) ? $_POST['rol'] : 'usuario';
    $active   = isset($_POST['active']) ? 1 : 0;

    $errors = [];
    if ($name === '')           $errors[] = 'El nombre es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email no válido.';
    if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden.';

    if (empty($errors)) {
        $conn = conectar_bd();
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = 'Ya existe un usuario con ese email.';
        }
        $check->close();

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, rol, active) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssi', $name, $email, $hash, $rol, $active);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            header('Location: show-users.php?message=Usuario+creado+correctamente');
            exit();
        }
        $conn->close();
    }
}

include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
?>
<!doctype html>
<html class="no-js" lang="en">
<head><meta charset="UTF-8"><title>Crear usuario</title></head>
<body>
<div class="page-container">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/sidebar.php'; ?>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/user-profile.php'; ?>

    <div class="main-content">
        <div class="main-content-inner">
            <div class="row justify-content-center">
                <div class="col-lg-6 mt-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title">Crear usuario</h4>

                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <?php foreach ($errors as $e): ?>
                                        <div><?= htmlspecialchars($e) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" name="name"
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Rol</label>
                                    <select class="form-control" name="rol" required>
                                        <option value="usuario" <?= (($_POST['rol'] ?? '') === 'usuario') ? 'selected' : '' ?>>
                                            Usuario — solo acceso a /plan
                                        </option>
                                        <option value="admin" <?= (($_POST['rol'] ?? '') === 'admin') ? 'selected' : '' ?>>
                                            Admin — acceso completo
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Contraseña <small class="text-muted">(mínimo 8 caracteres)</small></label>
                                    <input type="password" class="form-control" name="password" required minlength="8">
                                </div>

                                <div class="form-group">
                                    <label>Confirmar contraseña</label>
                                    <input type="password" class="form-control" name="password2" required>
                                </div>

                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" name="active" id="active"
                                           <?= !isset($_POST['name']) || isset($_POST['active']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="active">Cuenta activa</label>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                                    <a href="show-users.php" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/footer.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/libraries/scripts.php'; ?>
</body>
</html>
