<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/session_manager.php';

destroy_current_session();

session_unset();
session_destroy();

header('Location: /admin/login.php');
exit();
