<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/viajes.php';

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $conn = conectar_bd();
    $stmt = $conn->prepare("SELECT id FROM viajes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['viaje_id'] = $id;
    }
    $stmt->close();
    $conn->close();
}

$back = $_SERVER['HTTP_REFERER'] ?? '/admin/dashboard.php';
header("Location: $back");
exit;
