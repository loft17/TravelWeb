<?php
// functions.php

include_once  $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$conn = conectar_bd();

// Fecha: parámetro URL o hoy por defecto
$fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');

$viaje_id = VIAJE_ID;

$sql = "SELECT * FROM atracciones WHERE activo = TRUE AND viaje_id = ? AND fecha = ? ORDER BY orden ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $viaje_id, $fecha);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

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
