<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = trim($_POST['accion'] ?? '');

    if ($accion === 'crear') {
        $titulo      = trim($_POST['titulo'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin    = trim($_POST['fecha_fin'] ?? '');
        $info         = trim($_POST['info'] ?? '');
        $url          = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL) ?: null;

        $stmt = $conn->prepare("INSERT INTO tareas (titulo, fecha_inicio, fecha_fin, info, url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $titulo, $fecha_inicio, $fecha_fin, $info, $url);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'borrar') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'completar') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("UPDATE tareas SET completado=1, fecha_terminada=NOW() WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

    } elseif ($accion === 'editar') {
        $id           = intval($_POST['id'] ?? 0);
        $titulo       = trim($_POST['titulo'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin    = trim($_POST['fecha_fin'] ?? '');
        $info         = trim($_POST['info'] ?? '');
        $url          = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL) ?: null;

        $stmt = $conn->prepare("UPDATE tareas SET titulo=?, fecha_inicio=?, fecha_fin=?, info=?, url=? WHERE id=?");
        $stmt->bind_param("sssssi", $titulo, $fecha_inicio, $fecha_fin, $info, $url, $id);
        $stmt->execute();
        $stmt->close();
    }
}

$tareas = $conn->query("SELECT * FROM tareas ORDER BY completado ASC, fecha_creada DESC");

date_default_timezone_set('Europe/Madrid');
$hoy = date('Y-m-d');
?>
