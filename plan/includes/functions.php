<?php
// functions.php

include_once  $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$conn = conectar_bd();

// Fecha: parámetro URL o hoy por defecto
$fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');

// Viaje público: selección automática por fecha
include_once __DIR__ . '/viaje.php';

$sql = "SELECT * FROM atracciones WHERE activo = TRUE AND viaje_id = ? AND fecha = ? ORDER BY orden ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $viaje_id, $fecha);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Transportes del día (la tabla puede no existir si aún no se ha usado el admin)
$transportes_dia = [];
$tbl_check = $conn->query("SHOW TABLES LIKE 'transportes'");
if ($tbl_check && $tbl_check->num_rows > 0) {
    $stmt_t = $conn->prepare(
        "SELECT * FROM transportes WHERE viaje_id = ? AND fecha = ? ORDER BY hora_salida ASC"
    );
    $stmt_t->bind_param("is", $viaje_id, $fecha);
    $stmt_t->execute();
    $transportes_dia = $stmt_t->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_t->close();
}

// Load carriers from JSON file (indexed by IATA/company code)
$carriers_map = [];
$carriers_json_path = $_SERVER['DOCUMENT_ROOT'] . '/data/carriers.json';
if (file_exists($carriers_json_path)) {
    $carriers_raw = json_decode(file_get_contents($carriers_json_path), true) ?? [];
    foreach ($carriers_raw as $c) {
        if (!empty($c['codigo'])) {
            $carriers_map[strtoupper($c['codigo'])] = $c;
        }
    }
}

// Load airlines from DB as fallback for legacy records with aerolinea_id
$aerolineas_map = [];
$al_check = $conn->query("SHOW TABLES LIKE 'aerolineas'");
if ($al_check && $al_check->num_rows > 0) {
    $al_res = $conn->query("SELECT * FROM aerolineas");
    if ($al_res) {
        while ($al_row = $al_res->fetch_assoc()) {
            $aerolineas_map[$al_row['id']] = $al_row;
        }
    }
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
