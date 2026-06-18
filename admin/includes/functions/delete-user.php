<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Método no permitido.");
}

csrf_check();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    exit("ID de usuario no especificado.");
}

// Evitar que un admin se borre a sí mismo
if ($id === intval($_SESSION['user_id'])) {
    exit("No puedes eliminar tu propia cuenta.");
}

$conn = conectar_bd();

$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $del = $conn->prepare("DELETE FROM users WHERE id = ?");
    $del->bind_param("i", $id);
    if ($del->execute()) {
        log_activity('delete_user', "ID: $id");
        header("Location: /admin/pages/adm/show-users.php?message=Usuario+eliminado+correctamente.");
    } else {
        echo "Error al eliminar el usuario.";
    }
    $del->close();
} else {
    $stmt->close();
    echo "Usuario no encontrado.";
}

$conn->close();
?>
