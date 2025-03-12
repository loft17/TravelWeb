<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';

// Función para exportar a SQL
function exportar_a_sql($conn) {
    // Crear una variable para almacenar el contenido SQL
    $sql_data = '';

    // Obtener todas las tablas de la base de datos
    $tables_result = $conn->query("SHOW TABLES");

    if ($tables_result->num_rows > 0) {
        // Recorremos todas las tablas
        while ($table_row = $tables_result->fetch_row()) {
            $table_name = $table_row[0];
            
            // Comenzamos a crear la instrucción CREATE TABLE
            $create_table_result = $conn->query("SHOW CREATE TABLE $table_name");
            $create_table_row = $create_table_result->fetch_row();
            $sql_data .= "\n\n-- Estructura de la tabla $table_name\n";
            $sql_data .= $create_table_row[1] . ";\n\n";  // Sentencia CREATE TABLE
            
            // Obtener los registros de la tabla
            $result = $conn->query("SELECT * FROM $table_name");
            if ($result->num_rows > 0) {
                $sql_data .= "-- Insertar datos en la tabla $table_name\n";
                while ($row = $result->fetch_assoc()) {
                    $columns = array_keys($row);
                    $values = array_map(function($value) {
                        return "'" . addslashes($value) . "'";  // Escapamos los valores para prevenir problemas con comillas
                    }, array_values($row));

                    // Comando INSERT INTO
                    $sql_data .= "INSERT INTO $table_name (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
                }
                $sql_data .= "\n";
            }
        }

        // Definir las cabeceras para descargar el archivo SQL
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="exportado_completo.sql"');
        
        // Mostrar el contenido SQL generado
        echo $sql_data;
        exit;  // Termina el script para evitar que se imprima cualquier otro contenido
    } else {
        echo "No se encontraron tablas en la base de datos.";
    }
}
?>
