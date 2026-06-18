<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();
$stmt = $conn->prepare("SELECT id, name, email, rol, date_reg, active FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users  = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
