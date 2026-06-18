<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Si ya está autenticado, redirigir
if (!empty($_SESSION['user_id'])) {
    header('Location: /plan/index.php');
    exit();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Token de seguridad inválido.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $conn = conectar_bd();
        $stmt = $conn->prepare("SELECT id, name, password, rol FROM users WHERE email = ? AND active = 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hash, $rol);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $stmt->close();
                $conn->close();

                session_regenerate_id(true);
                $_SESSION['user_id']   = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = $rol;

                include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/session_manager.php';
                register_session($id);

                header('Location: /plan/index.php');
                exit();
            }
        }
        $stmt->close();
        $conn->close();
        $error = 'Email o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceder</title>
    <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/plan/assets/style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; background: #f9f9f9; }
        .login-box {
            background: #fff;
            border: 1px solid #e4e4e7;
            border-radius: 10px;
            padding: 36px 32px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
        }
        .login-box h2 { font-size: 1.3em; margin-bottom: 6px; color: #18181b; }
        .login-box p  { font-size: .85em; color: #71717a; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: .8em; font-weight: 600; color: #18181b; margin-bottom: 5px; }
        .form-group input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            font-family: 'Gidole', sans-serif;
            font-size: .9em;
            color: #18181b;
            background: #fafafa;
            outline: none;
            box-sizing: border-box;
        }
        .form-group input:focus { border-color: #18181b; background: #fff; }
        .btn-login {
            width: 100%;
            padding: 10px;
            background: #18181b;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-family: 'Gidole', sans-serif;
            font-size: .95em;
            cursor: pointer;
            margin-top: 8px;
        }
        .btn-login:hover { background: #333; }
        .error-msg {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: .85em;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Acceder</h2>
        <p>Introduce tus credenciales para continuar.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>
