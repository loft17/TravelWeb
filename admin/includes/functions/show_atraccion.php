<?php
// /admin/includes/functions/show_atraccion.php

// Incluir la configuración para conectarse a la BBDD
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

/**
 * Función para obtener todas las atracciones.
 *
 * @return array Un arreglo con los registros de atracciones.
 */
function getAtracciones(int $viaje_id = 0): array {
    $conn = conectar_bd();
    if ($viaje_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM atracciones WHERE viaje_id = ?");
        $stmt->bind_param("i", $viaje_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } else {
        $result = $conn->query("SELECT * FROM atracciones");
    }
    $atracciones = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $conn->close();
    return $atracciones;
}
?>
