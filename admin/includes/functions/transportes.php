<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

function _ensure_transportes_table(): void {
    $conn = conectar_bd();
    $conn->query("CREATE TABLE IF NOT EXISTS transportes (
        id           INT AUTO_INCREMENT PRIMARY KEY,
        tipo         VARCHAR(50)  NOT NULL DEFAULT 'avion',
        origen       VARCHAR(255) NOT NULL,
        destino      VARCHAR(255) NOT NULL,
        fecha        DATE         NOT NULL,
        hora_salida  TIME         DEFAULT NULL,
        hora_llegada TIME         DEFAULT NULL,
        numero       VARCHAR(100) DEFAULT NULL,
        notas        TEXT         DEFAULT NULL,
        viaje_id     INT          NOT NULL DEFAULT 1,
        created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $conn->close();
}
_ensure_transportes_table();

$viaje_id = (int)($_SESSION['viaje_id'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $tipo         = trim($_POST['tipo']         ?? 'otro');
        $origen       = trim($_POST['origen']       ?? '');
        $destino      = trim($_POST['destino']      ?? '');
        $fecha        = trim($_POST['fecha']        ?? '');
        $hora_salida  = trim($_POST['hora_salida']  ?? '') ?: null;
        $hora_llegada = trim($_POST['hora_llegada'] ?? '') ?: null;
        $numero       = trim($_POST['numero']       ?? '') ?: null;
        $notas        = trim($_POST['notas']        ?? '') ?: null;

        if ($origen === '' || $destino === '' || $fecha === '') {
            $_SESSION['tr_error'] = 'Origen, destino y fecha son obligatorios.';
        } else {
            $conn = conectar_bd();
            $stmt = $conn->prepare(
                "INSERT INTO transportes (tipo,origen,destino,fecha,hora_salida,hora_llegada,numero,notas,viaje_id)
                 VALUES (?,?,?,?,?,?,?,?,?)"
            );
            $stmt->bind_param('ssssssssi', $tipo, $origen, $destino, $fecha, $hora_salida, $hora_llegada, $numero, $notas, $viaje_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            log_activity('transporte_agregado', "$tipo: $origen → $destino ($fecha)");
            $_SESSION['tr_success'] = 'Transporte añadido correctamente.';
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } elseif ($accion === 'borrar') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $conn = conectar_bd();
            $stmt = $conn->prepare("DELETE FROM transportes WHERE id = ? AND viaje_id = ?");
            $stmt->bind_param('ii', $id, $viaje_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            log_activity('transporte_borrado', "id=$id");
            $_SESSION['tr_success'] = 'Transporte eliminado.';
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Leer mensajes flash de sesión
$tr_error   = $_SESSION['tr_error']   ?? '';
$tr_success = $_SESSION['tr_success'] ?? '';
unset($_SESSION['tr_error'], $_SESSION['tr_success']);

$conn = conectar_bd();
$stmt = $conn->prepare(
    "SELECT * FROM transportes WHERE viaje_id = ? ORDER BY fecha ASC, hora_salida ASC"
);
$stmt->bind_param('i', $viaje_id);
$stmt->execute();
$transportes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
