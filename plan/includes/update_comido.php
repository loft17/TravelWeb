<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['action'] ?? '') !== 'toggle_comido') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    exit;
}

$id       = intval($_POST['id'] ?? 0);
$comido   = ($_POST['comido'] ?? 'false') === 'true';
$newValue = $comido ? 0 : 1;
$viaje_id = (int)VIAJE_ID;

$conn = conectar_bd();
$stmt = $conn->prepare("UPDATE comida SET comido = ? WHERE id = ? AND viaje_id = ?");
$stmt->bind_param('iii', $newValue, $id, $viaje_id);
$stmt->execute();
$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode(['success' => true, 'comido' => (bool)$newValue]);
exit;
