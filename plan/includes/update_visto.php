<?php
// update_visto.php
include_once  $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_visto') {
    $id = intval($_POST['id']);
    // Se espera que se envíe el estado "actual" en el parámetro 'visto'
    $currentVisto = $_POST['visto'] === 'true';
    $newVisto = !$currentVisto;

    $updateSql = "UPDATE atracciones SET visto = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ii", $newVisto, $id);
    $stmt->execute();
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'visto' => $newVisto]);
    exit;
}

// Si no se envía la petición correcta, devolver error
header('Content-Type: application/json');
echo json_encode(['success' => false, 'error' => 'Acción no válida']);
exit;
?>
