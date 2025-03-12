<?php
// Iniciar la sesión lo antes posible
session_start();

// Verificar si el usuario ya está logueado
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    // Redirigir si el usuario ya está logueado y es admin
    header("Location: /admin/dashboard.php"); // Redirige a la página de administración o la que desees
    exit();
}

// Evitar que se envíen cabeceras si ya hubo salida
ob_start();

// Verificar si hay un mensaje de error de login
if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Eliminar el mensaje después de mostrarlo
}
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
                <form action="includes/auth/auth.php" method="POST">
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

<?php
// Finalizar el buffer de salida y enviarlo al navegador
ob_end_flush();
?>
