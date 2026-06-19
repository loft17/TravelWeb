<?php
// functions.php

include_once  $_SERVER['DOCUMENT_ROOT'] . '/config.php';
$conn = conectar_bd();

// Fecha: parámetro URL o hoy por defecto
$fecha = isset($_GET['fecha']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['fecha'])
    ? $_GET['fecha']
    : date('Y-m-d');

// Viaje público: BD tiene prioridad sobre la constante de config.php
$_r = $conn->query("SELECT config_value FROM configurations WHERE config_key = 'viaje_id_public' LIMIT 1");
$viaje_id = ($_r && $row_cfg = $_r->fetch_assoc()) ? (int)$row_cfg['config_value'] : (int)VIAJE_ID;
unset($_r, $row_cfg);

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
