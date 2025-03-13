<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_STRING);

    if ($accion === 'crear') {
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
        $fecha_fin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);
        $info = filter_input(INPUT_POST, 'info', FILTER_SANITIZE_STRING);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL) ?: null;

        $stmt = $conn->prepare("INSERT INTO tareas (titulo, fecha_inicio, fecha_fin, info, url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $titulo, $fecha_inicio, $fecha_fin, $info, $url);
        $stmt->execute();

    } elseif ($accion === 'borrar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("DELETE FROM tareas WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

    } elseif ($accion === 'completar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $stmt = $conn->prepare("UPDATE tareas SET completado=1, fecha_terminada=NOW() WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

    } elseif ($accion === 'editar') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
        $fecha_inicio = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_STRING);
        $fecha_fin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_STRING);
        $info = filter_input(INPUT_POST, 'info', FILTER_SANITIZE_STRING);
        $url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL) ?: null;

        $stmt = $conn->prepare("UPDATE tareas SET titulo=?, fecha_inicio=?, fecha_fin=?, info=?, url=? WHERE id=?");
        $stmt->bind_param("sssssi", $titulo, $fecha_inicio, $fecha_fin, $info, $url, $id);
        $stmt->execute();
    }
}

$tareas = $conn->query("SELECT * FROM tareas ORDER BY completado ASC, fecha_creada DESC");

date_default_timezone_set('Europe/Madrid');
$hoy = date('Y-m-d');
?>