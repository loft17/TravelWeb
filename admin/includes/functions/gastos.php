<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/activity_log.php';

// Crear tabla si no existe (migración automática para installs existentes)
function _ensure_gastos_table(): void
{
    $conn = conectar_bd();
    $conn->query("CREATE TABLE IF NOT EXISTS gastos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        categoria VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NOT NULL,
        importe DECIMAL(10,2) NOT NULL,
        divisa VARCHAR(10) NOT NULL DEFAULT 'EUR',
        fecha DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $conn->close();
}

_ensure_gastos_table();

$gastos_error   = '';
$gastos_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $categoria   = trim($_POST['categoria']   ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $importe     = floatval($_POST['importe']  ?? 0);
        $divisa      = trim($_POST['divisa']       ?? 'EUR');
        $fecha       = trim($_POST['fecha']        ?? '');

        if ($categoria === '' || $descripcion === '' || $importe <= 0 || $fecha === '') {
            $gastos_error = 'Todos los campos son obligatorios y el importe debe ser positivo.';
        } else {
            $conn = conectar_bd();
            $stmt = $conn->prepare("INSERT INTO gastos (categoria, descripcion, importe, divisa, fecha) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('ssdss', $categoria, $descripcion, $importe, $divisa, $fecha);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            log_activity('gasto_agregado', "$categoria: $importe $divisa — $descripcion");
            $gastos_success = 'Gasto añadido correctamente.';
        }
    } elseif ($accion === 'borrar') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $conn = conectar_bd();
            $stmt = $conn->prepare("DELETE FROM gastos WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            log_activity('gasto_borrado', "ID: $id");
            $gastos_success = 'Gasto eliminado.';
        }
    }
}

// Cargar todos los gastos
$conn    = conectar_bd();
$result  = $conn->query("SELECT * FROM gastos ORDER BY fecha DESC, id DESC");
$gastos  = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Totales por divisa
$totales = [];
foreach ($gastos as $g) {
    $totales[$g['divisa']] = ($totales[$g['divisa']] ?? 0) + $g['importe'];
}

// Totales por categoría
$porCategoria = [];
foreach ($gastos as $g) {
    $key = $g['categoria'] . '_' . $g['divisa'];
    if (!isset($porCategoria[$key])) {
        $porCategoria[$key] = ['categoria' => $g['categoria'], 'divisa' => $g['divisa'], 'total' => 0];
    }
    $porCategoria[$key]['total'] += $g['importe'];
}

$conn->close();

$categorias = ['Transporte', 'Alojamiento', 'Comida', 'Actividades', 'Compras', 'Otros'];
$divisas    = ['EUR', 'USD', 'GBP', 'JPY', 'MXN', 'COP', 'ARS', 'BRL', 'CLP'];
