<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

if (isset($_POST['order'])) {
    $stmt = $conn->prepare("UPDATE atracciones SET orden = ? WHERE id = ?");
    foreach ($_POST['order'] as $item) {
        $id    = intval($item['id']);
        $orden = intval($item['orden']);
        $stmt->bind_param("ii", $orden, $id);
        $stmt->execute();
    }
    $stmt->close();
    echo json_encode(['status' => 'ok', 'message' => 'Orden actualizado']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibió datos']);
}

$conn->close();
?>
