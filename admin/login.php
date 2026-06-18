<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirigir si ya está logueado
if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin') {
    header("Location: /admin/dashboard.php");
    exit();
}

// Procesar POST del formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/session_manager.php';

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['login_error'] = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
        header('Location: /admin/login.php');
        exit();
    }

    $maxAttempts  = 5;
    $lockDuration = 15 * 60;

    if (isset($_SESSION['login_lockout_until']) && time() < $_SESSION['login_lockout_until']) {
        $remaining = ceil(($_SESSION['login_lockout_until'] - time()) / 60);
        $_SESSION['login_error'] = "Demasiados intentos fallidos. Espera {$remaining} minuto(s).";
        header('Location: /admin/login.php');
        exit();
    }

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $conn = conectar_bd();
    $stmt = $conn->prepare("SELECT id, name, password, rol FROM users WHERE email = ? AND active = 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    $loginOk = false;

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $rol);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            if ($rol !== 'admin') {
                $_SESSION['login_error'] = 'No tienes permisos de administrador.';
                header('Location: /admin/login.php');
                exit();
            }
            $loginOk = true;
        }
    }
    $stmt->close();

    if (!$loginOk) {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= $maxAttempts) {
            $_SESSION['login_lockout_until'] = time() + $lockDuration;
            unset($_SESSION['login_attempts']);
            $_SESSION['login_error'] = 'Demasiados intentos fallidos. Cuenta bloqueada 15 minutos.';
            log_activity('login_blocked', "Email: $email");
        } else {
            $left = $maxAttempts - $_SESSION['login_attempts'];
            $_SESSION['login_error'] = "Credenciales incorrectas. Te quedan {$left} intento(s).";
            log_activity('login_failed', "Email: $email");
        }
        $conn->close();
        header('Location: /admin/login.php');
        exit();
    }

    unset($_SESSION['login_attempts'], $_SESSION['login_lockout_until']);
    session_regenerate_id(true);

    $now = date('Y-m-d H:i:s');
    $ip  = $_SERVER['REMOTE_ADDR'];
    $upd = $conn->prepare("UPDATE users SET last_conection = ?, ip_conection = ? WHERE id = ?");
    $upd->bind_param('ssi', $now, $ip, $id);
    $upd->execute();
    $upd->close();
    $conn->close();

    $_SESSION['user_id']   = $id;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = $rol;

    log_activity('login_success', "Usuario: $name ($email)");
    register_session($id);

    header('Location: /admin/dashboard.php');
    exit();
}

// Leer y limpiar mensaje de error
$login_error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>

<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login - srtdash</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="../assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="/admin/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/admin/assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="/admin/assets/css/themify-icons.css">
    <link rel="stylesheet" href="/admin/assets/css/metisMenu.css">
    <link rel="stylesheet" href="/admin/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/admin/assets/css/slicknav.min.css">
    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <!-- others css -->
    <link rel="stylesheet" href="/admin/assets/css/typography.css">
    <link rel="stylesheet" href="/admin/assets/css/default-css.css">
    <link rel="stylesheet" href="/admin/assets/css/styles.css">
    <link rel="stylesheet" href="/admin/assets/css/responsive.css">
    <!-- modernizr css -->
    <script src="/admin/assets/js/vendor/modernizr-2.8.3.min.js"></script>
    <!-- FONT-AWESONME -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->

    <!-- login area start -->
    <div class="login-area login-s2">
        <div class="container">
            <div class="login-box ptb--100">
                <form action="/admin/login.php" method="POST">
                    <!-- Campo oculto para el token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="login-form-head">
                        <h4>Acceder</h4>
                        <p>Hola, Inicia sesión y empieza a gestionar tu viaje.</p>
                    </div>
                    <!-- Mostrar mensaje de error si existe -->
                    <?php if (isset($login_error)): ?>
                        <div class="alert alert-danger"><?php echo $login_error; ?></div>
                    <?php endif; ?>

                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Dirección email</label>
                            <input type="email" id="exampleInputEmail1" name="email" required>
                            <i class="ti-email"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Contraseña</label>
                            <input type="password" id="exampleInputPassword1" name="password" required>
                            <i class="ti-lock"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="row mb-4 rmber-area">
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input" id="customControlAutosizing">
                                    <label class="custom-control-label" for="customControlAutosizing">Recordarme</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <a href="#">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="submit-btn-area">
                            <button id="form_submit" type="submit">Entrar <i class="ti-arrow-right"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- login area end -->

    <?php include 'includes/libraries/scripts.php'; ?>
</body>

</html>

