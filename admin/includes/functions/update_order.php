<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$conn = conectar_bd();

if (isset($_POST['order'])) {
    foreach ($_POST['order'] as $item) {
        $id = intval($item['id']);
        $orden = intval($item['orden']);
        // Actualizamos el campo 'orden' para cada registro
        $sql = "UPDATE atracciones SET orden = $orden WHERE id = $id";
        $conn->query($sql);
    }
    echo json_encode(['status' => 'ok', 'message' => 'Orden actualizado']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibió datos']);
}

$conn->close();
?>
