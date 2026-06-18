<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

$viaje_id = (int)($_SESSION['viaje_id'] ?? 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = trim($_POST['accion'] ?? '');

    if ($accion === 'crear') {
        $titulo      = trim($_POST['titulo'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin    = trim($_POST['fecha_fin'] ?? '');
        $info         = trim($_POST['info'] ?? '');
        $url          = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL) ?: null;

        $stmt = $conn->prepare("INSERT INTO tareas (titulo, fecha_inicio, fecha_fin, info, url, viaje_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $titulo, $fecha_inicio, $fecha_fin, $info, $url, $viaje_id);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'borrar') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id=? AND viaje_id=?");
        $stmt->bind_param("ii", $id, $viaje_id);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'completar') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("UPDATE tareas SET completado=1, fecha_terminada=NOW() WHERE id=? AND viaje_id=?");
        $stmt->bind_param("ii", $id, $viaje_id);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'editar') {
        $id           = intval($_POST['id'] ?? 0);
        $titulo       = trim($_POST['titulo'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin    = trim($_POST['fecha_fin'] ?? '');
        $info         = trim($_POST['info'] ?? '');
        $url          = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL) ?: null;

        $stmt = $conn->prepare("UPDATE tareas SET titulo=?, fecha_inicio=?, fecha_fin=?, info=?, url=? WHERE id=? AND viaje_id=?");
        $stmt->bind_param("sssssii", $titulo, $fecha_inicio, $fecha_fin, $info, $url, $id, $viaje_id);
        $stmt->execute();
        $stmt->close();
    }
}

$stmt_t = $conn->prepare("SELECT * FROM tareas WHERE viaje_id = ? ORDER BY completado ASC, fecha_creada DESC");
$stmt_t->bind_param("i", $viaje_id);
$stmt_t->execute();
$tareas = $stmt_t->get_result();
$stmt_t->close();

date_default_timezone_set('Europe/Madrid');
$hoy = date('Y-m-d');
?>
