<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

if (empty($_SESSION['user_id'])) {
    header('Location: /plan/login.php');
    exit();
}
