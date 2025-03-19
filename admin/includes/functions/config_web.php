<?php
// /admin/includes/functions/config_web.php

// Incluimos el archivo de configuración para usar la función conectar_bd()
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

/**
 * Procesa la actualización de las configuraciones y retorna los valores actuales.
 *
 * @return array Retorna un arreglo asociativo con dos claves:
 *               - 'fields': arreglo de campos definidos (config_key => etiqueta)
 *               - 'currentValues': arreglo con los valores actuales (config_key => config_value)
 */
function process_config_web() {
    // Creamos la conexión a la base de datos.
    $conn = conectar_bd();

    // Definimos los campos a editar: clave de la tabla => etiqueta a mostrar.
    $fields = [
        'title_web'   => 'Título',
        'footer_text' => 'Pie de Página'
    ];

    // Si se envía el formulario, se actualizan los valores.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        foreach ($fields as $key => $label) {
            $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
            $stmt = $conn->prepare("UPDATE configurations SET config_value = ? WHERE config_key = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error en la consulta para $label: " . $conn->error . "</div>";
            }
        }
        echo "<div class='alert alert-success'>Las configuraciones se han actualizado correctamente.</div>";
    }

    // Recuperamos los valores actuales de la base de datos.
    $currentValues = [];
    // Creamos una lista de claves entre comillas para la consulta SQL.
    $keysList = implode(",", array_map(function($k) use ($conn) {
        return "'" . $conn->real_escape_string($k) . "'";
    }, array_keys($fields)));

    $result = $conn->query("SELECT config_key, config_value FROM configurations WHERE config_key IN ($keysList)");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $currentValues[$row['config_key']] = $row['config_value'];
        }
        $result->free();
    } else {
        echo "<div class='alert alert-danger'>Error en la consulta: " . $conn->error . "</div>";
    }

    return ['fields' => $fields, 'currentValues' => $currentValues];
}
