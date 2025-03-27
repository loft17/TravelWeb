<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

$conn = conectar_bd();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Comprobar si ya existe un registro para la fecha
    $stmt = $conn->prepare("SELECT id FROM calendar_events WHERE fecha = ?");
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si existe, actualizamos el registro
        $stmt->close();
        $stmt = $conn->prepare("UPDATE calendar_events SET ciudad = ?, visita_manana = ?, visita_tarde = ?, visita_noche = ? WHERE fecha = ?");
        $stmt->bind_param("sssss", $ciudad, $visita_manana, $visita_tarde, $visita_noche, $fecha);
        if ($stmt->execute()) {
            // Redirigir a la página del calendario (ajusta la ruta según corresponda)
            header("Location: /admin/pages/atracciones/planning.php?msg=updated");
            exit();
        } else {
            die("Error al actualizar el evento: " . $stmt->error);
        }
    } else {
        // Si no existe, insertamos un nuevo registro
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO calendar_events (fecha, ciudad, visita_manana, visita_tarde, visita_noche) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fecha, $ciudad, $visita_manana, $visita_tarde, $visita_noche);
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
