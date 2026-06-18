<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/login.php');
    exit();
}

if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['login_error'] = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
    header('Location: /admin/login.php');
    exit();
}

// Comprobación de bloqueo por intentos fallidos
$maxAttempts  = 5;
$lockDuration = 15 * 60; // 15 minutos en segundos

if (isset($_SESSION['login_lockout_until']) && time() < $_SESSION['login_lockout_until']) {
    $remaining = ceil(($_SESSION['login_lockout_until'] - time()) / 60);
    $_SESSION['login_error'] = "Demasiados intentos fallidos. Espera {$remaining} minuto(s) antes de volver a intentarlo.";
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
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
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
        log_activity('login_blocked', "Email: $email — IP bloqueada por exceso de intentos");
    } else {
        $left = $maxAttempts - $_SESSION['login_attempts'];
        $_SESSION['login_error'] = "Credenciales incorrectas. Te quedan {$left} intento(s).";
        log_activity('login_failed', "Email: $email");
    }
    header('Location: /admin/login.php');
    exit();
}

// Login correcto: limpiar contadores y regenerar sesión
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

header('Location: /admin/dashboard.php');
exit();
