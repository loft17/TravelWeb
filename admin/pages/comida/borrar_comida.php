<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Método no permitido.");
}

csrf_check();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    exit("ID no especificado.");
}

$conn = conectar_bd();

$stmt = $conn->prepare("SELECT id FROM comida WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $del = $conn->prepare("DELETE FROM comida WHERE id = ?");
    $del->bind_param("i", $id);
    if ($del->execute()) {
        header("Location: show-foods.php?message=Registro+eliminado+correctamente.");
    } else {
        echo "Error al eliminar el registro.";
    }
    $del->close();
} else {
    $stmt->close();
    echo "Registro no encontrado.";
}

$conn->close();
?>
