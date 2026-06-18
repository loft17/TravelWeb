<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/helpers.php';

if (isset($_GET['action']) && $_GET['action'] === 'upload_image') {
    header('Content-Type: application/json');
    echo json_encode(uploadImageFile('imagen_file'));
    exit;
}
?>
