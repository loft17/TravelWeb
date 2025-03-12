<?php
// Inicia la sesión si no está ya iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Genera un token CSRF aleatorio y lo guarda en la sesión
}
?>


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