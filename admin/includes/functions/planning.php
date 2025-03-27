<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/templates/head.php';
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/functions/export_sql.php';

/**
 * Función para generar el calendario en formato HTML con estilo "table dark".
 * Cada celda muestra una tabla interna con:
 *   - Una fila (class="table-primary") con la fecha (solo si está dentro del rango configurado).
 *   - Las siguientes filas con los contenidos de: ciudad, visita_manana, visita_tarde y visita_noche.
 * Además, solo en las celdas dentro del rango se asignan atributos data-* para facilitar la edición mediante el modal.
 *
 * @return string HTML del calendario.
 */
function generate_calendar() {
    $conn = conectar_bd();
    $sqlConfig = "SELECT config_key, config_value FROM configurations WHERE config_key IN ('date_start', 'date_finish')";
    $resultConfig = $conn->query($sqlConfig);
    $date_start = $date_finish = '';

    while ($row = $resultConfig->fetch_assoc()) {
        if ($row['config_key'] == 'date_start') {
            $date_start = $row['config_value'];
        }
        if ($row['config_key'] == 'date_finish') {
            $date_finish = $row['config_value'];
        }
    }

    $startDate = new DateTime($date_start);
    $endDate   = new DateTime($date_finish);

    // Ajustar el inicio para que sea lunes y el fin para que sea domingo.
    if ($startDate->format('N') != 1) {
        $startDate->modify('last monday');
    }
    if ($endDate->format('N') != 7) {
        $endDate->modify('next sunday');
    }

    // Consultar la tabla calendar_events para obtener los datos de cada fecha.
    $sqlEvents = "SELECT * FROM calendar_events WHERE fecha BETWEEN '" . $startDate->format('Y-m-d') . "' AND '" . $endDate->format('Y-m-d') . "'";
    $resultEvents = $conn->query($sqlEvents);
    $events = array();
    while ($row = $resultEvents->fetch_assoc()) {
        $events[$row['fecha']] = $row;
    }

    // Construir la tabla del calendario.
    $calendar_html  = "<table class='table text-center'>";
    $calendar_html .= "<thead class='text-uppercase bg-dark'>";
    $calendar_html .= "<tr class='text-white'>";
    $calendar_html .= "<th scope='col'>Lunes</th>";
    $calendar_html .= "<th scope='col'>Martes</th>";
    $calendar_html .= "<th scope='col'>Miércoles</th>";
    $calendar_html .= "<th scope='col'>Jueves</th>";
    $calendar_html .= "<th scope='col'>Viernes</th>";
    $calendar_html .= "<th scope='col'>Sábado</th>";
    $calendar_html .= "<th scope='col'>Domingo</th>";
    $calendar_html .= "</tr>";
    $calendar_html .= "</thead>";
    $calendar_html .= "<tbody>";

    $currentDate = clone $startDate;
    while ($currentDate <= $endDate) {
        $calendar_html .= "<tr>";
        for ($i = 0; $i < 7; $i++) {
            $fechaCell = $currentDate->format('Y-m-d');
            $ciudad        = isset($events[$fechaCell]) ? $events[$fechaCell]['ciudad'] : '';
            $visita_manana = isset($events[$fechaCell]) ? $events[$fechaCell]['visita_manana'] : '';
            $visita_tarde  = isset($events[$fechaCell]) ? $events[$fechaCell]['visita_tarde'] : '';
            $visita_noche  = isset($events[$fechaCell]) ? $events[$fechaCell]['visita_noche'] : '';

            // Solo para fechas dentro del rango original se muestra la tabla interna con la fecha y se asignan atributos para la edición.
            if ($fechaCell >= $date_start && $fechaCell <= $date_finish) {
                $cellContent  = "<table class='table table-borderless' style='margin-bottom: 0;'>";
                $cellContent .= "<tr class='table-primary'><td>" . $currentDate->format('d/m/Y') . "</td></tr>";
                $cellContent .= "<tr class='table-success'><td>" . $ciudad . "</td></tr>";
                $cellContent .= "<tr><td>" . $visita_manana . "</td></tr>";
                $cellContent .= "<tr><td>" . $visita_tarde . "</td></tr>";
                $cellContent .= "<tr><td>" . $visita_noche . "</td></tr>";
                $cellContent .= "</table>";

                $tdAttributes = "data-date='{$fechaCell}' " .
                                "data-ciudad='" . addslashes($ciudad) . "' " .
                                "data-visita_manana='" . addslashes($visita_manana) . "' " .
                                "data-visita_tarde='" . addslashes($visita_tarde) . "' " .
                                "data-visita_noche='" . addslashes($visita_noche) . "'";
                $calendar_html .= "<td $tdAttributes>$cellContent</td>";
            } else {
                // Para fechas fuera del rango original, no se muestra la fecha ni se asignan atributos de edición.
                $calendar_html .= "<td style='color: #ccc;'></td>";
            }
            $currentDate->modify('+1 day');
        }
        $calendar_html .= "</tr>";
    }
    $calendar_html .= "</tbody>";
    $calendar_html .= "</table>";

    return $calendar_html;
}

$calendar = generate_calendar();
?>
