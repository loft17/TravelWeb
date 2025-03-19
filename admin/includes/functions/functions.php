<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// ---------------------------------------------------------------------------------------------------------------
// FOOTER.php
// ---------------------------------------------------------------------------------------------------------------
// Obtenemos la conexión a la base de datos
$conn = conectar_bd();

// Valor por defecto en caso de error o si no se encuentra el registro
$footer_text = "Default Footer text";

// Consulta para obtener el valor del footer desde la base de datos
$result = $conn->query("SELECT config_value FROM configurations WHERE config_key = 'footer_text' LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    $footer_text = $row['config_value'];
    $result->free();
}

// ---------------------------------------------------------------------------------------------------------------
// XXX.php
// ---------------------------------------------------------------------------------------------------------------
//
?>