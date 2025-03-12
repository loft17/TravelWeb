<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado y si es admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    // Si el usuario no está logueado o no tiene el rol de admin y está en login.php, evitar redirección
    if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
        $_SESSION['login_error'] = "Acceso no autorizado. Necesitas ser administrador.";
        header("Location: /admin/login.php"); // Redirigir al login si no está logueado
        exit();
    }
}
?>