<?php
// functions.php

include_once  $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$conn = conectar_bd();

// Obtener la fecha desde la URL (formato YYYY-MM-DD)
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;

if ($fecha) {
    $sql = "SELECT * FROM atracciones WHERE activo = TRUE AND fecha = ? ORDER BY ciudad, orden ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $sql = "SELECT * FROM atracciones WHERE activo = TRUE ORDER BY ciudad, orden ASC";
    $result = $conn->query($sql);
}

// Procesar la acción de toggle de "visto"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_visto') {
    $id = intval($_POST['id']);
    $currentVisto = $_POST['visto'] === 'true';
    $newVisto = !$currentVisto;

    $updateSql = "UPDATE atracciones SET visto = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ii", $newVisto, $id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'visto' => $newVisto]);
    exit;
}
?>
