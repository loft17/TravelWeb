<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        $_SESSION['login_error'] = "Acceso no autorizado. Necesitas ser administrador.";
        header("Location: /admin/login.php");
        exit();
    }
}

if (!function_exists('csrf_check')) {
    function csrf_check(): void
    {
        if (!isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            http_response_code(403);
            die('Token CSRF inválido. Vuelve atrás e inténtalo de nuevo.');
        }
    }
}
?>
