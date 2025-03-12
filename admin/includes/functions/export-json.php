<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Llamar a la función para conectar a la base de datos
$conn = conectar_bd();  // Obtener la conexión a la base de datos

// Función para exportar la base de datos a JSON
function exportar_a_json($conn) {
    // Crear un array para almacenar todas las tablas y sus registros
    $database_data = array();

    // Obtener todas las tablas de la base de datos
    $tables_result = $conn->query("SHOW TABLES");

    if ($tables_result->num_rows > 0) {
        while ($table_row = $tables_result->fetch_row()) {
            $table_name = $table_row[0];

            // Obtener los registros de cada tabla
            $sql = "SELECT * FROM $table_name";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $table_data = array();

                // Almacenar los datos de cada tabla en un array
                while ($row = $result->fetch_assoc()) {
                    $table_data[] = $row;
                }

                // Añadir la tabla y sus datos al array principal
                $database_data[$table_name] = $table_data;
            }
        }

        // Convertir el array a JSON
        $json_data = json_encode($database_data, JSON_PRETTY_PRINT);

        // Asegúrate de que no haya salida previa al enviar los encabezados
        ob_clean();  // Limpiar el buffer de salida

        // Definir las cabeceras para descargar el archivo JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="exportado_completo.json"');
        echo $json_data;  // Aquí se genera el archivo JSON

        exit;  // Termina el script para evitar que se ejecute más código o contenido HTML
    } else {
        echo "No se encontraron tablas en la base de datos.";
    }
}
?>
