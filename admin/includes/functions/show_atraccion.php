<?php
// /admin/includes/functions/show_atraccion.php

// Incluir la configuración para conectarse a la BBDD
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

/**
 * Función para obtener todas las atracciones.
 *
 * @return array Un arreglo con los registros de atracciones.
 */
function getAtracciones() {
    $conn = conectar_bd();
    $sql = "SELECT * FROM atracciones";
    $result = $conn->query($sql);
    $atracciones = array();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $atracciones[] = $row;
        }
    }
    $conn->close();
    return $atracciones;
}
?>
