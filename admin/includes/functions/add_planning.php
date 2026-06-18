<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    // Recoger y sanitizar los datos recibidos vía POST
    $fecha         = $_POST['fecha'] ?? '';
    $ciudad        = $_POST['ciudad'] ?? '';
    $visita_manana = $_POST['visita_manana'] ?? '';
    $visita_tarde  = $_POST['visita_tarde'] ?? '';
    $visita_noche  = $_POST['visita_noche'] ?? '';

    // Validar que la fecha no esté vacía
    if (!$fecha) {
        die("La fecha es obligatoria.");
    }

    $viaje_id = (int)($_SESSION['viaje_id'] ?? 1);

    // Comprobar si ya existe un registro para la fecha y viaje
    $stmt = $conn->prepare("SELECT id FROM calendar_events WHERE fecha = ? AND viaje_id = ?");
    $stmt->bind_param("si", $fecha, $viaje_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $stmt = $conn->prepare("UPDATE calendar_events SET ciudad = ?, visita_manana = ?, visita_tarde = ?, visita_noche = ? WHERE fecha = ? AND viaje_id = ?");
        $stmt->bind_param("sssssi", $ciudad, $visita_manana, $visita_tarde, $visita_noche, $fecha, $viaje_id);
        if ($stmt->execute()) {
            header("Location: /admin/pages/atracciones/planning.php?msg=updated");
            exit();
        } else {
            die("Error al actualizar el evento: " . $stmt->error);
        }
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO calendar_events (fecha, ciudad, visita_manana, visita_tarde, visita_noche, viaje_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $fecha, $ciudad, $visita_manana, $visita_tarde, $visita_noche, $viaje_id);
        if ($stmt->execute()) {
            header("Location: /admin/pages/atracciones/planning.php?msg=inserted");
            exit();
        } else {
            die("Error al insertar el evento: " . $stmt->error);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Método no permitido.";
}
?>
