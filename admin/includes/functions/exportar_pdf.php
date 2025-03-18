<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/includes/tcpdf/tcpdf.php');

class CustomPDF extends TCPDF {
    public function Header() {
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(0, 10, 'TAILANDIA 25', 0, 1, 'C');
        $this->Ln(5);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 7);
        $this->Cell(0, 10, 'Pagina ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$conn = conectar_bd();
$pdf = new CustomPDF('L', 'mm', 'A4'); // 'L' para orientación horizontal
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 8);
$pdf->Ln(5);

// Definir categorías
$categorias = ['Ropa', 'Electronica', 'Documentacion', 'Neceser', 'Botiquin', 'Varios'];
$items_por_categoria = array_fill_keys($categorias, []);

$query = "SELECT nombre, categoria, cantidad FROM maleta WHERE categoria IN ('" . implode("','", $categorias) . "') ORDER BY categoria";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $items_por_categoria[$row['categoria']][] = $row;
}

$pdf->SetFont('Helvetica', 'B', 9);
$pdf->Ln(5);

// Mostrar encabezados de categoría con color azul claro
foreach ($categorias as $categoria) {
    $pdf->SetFillColor(173, 216, 230); // Establece el color de fondo azul claro
    $pdf->Cell(45, 5, $categoria, 1, 0, 'C', true);
}
$pdf->Ln();
$pdf->Ln(); // Línea en blanco para espacio

$max_items = max(array_map('count', $items_por_categoria));
$pdf->SetFont('Helvetica', '', 8);

for ($i = 0; $i < $max_items; $i++) {
    foreach ($categorias as $categoria) {
        if (isset($items_por_categoria[$categoria][$i])) {
            $item = $items_por_categoria[$categoria][$i];
            
            // Cuadro negro a la izquierda
            $pdf->Rect($pdf->GetX(), $pdf->GetY(), 3, 3, 'D');
            $pdf->Cell(5, 5, '', 0, 0, 'C'); // Espacio para el cuadro
            
            // Cantidad en rojo
            $pdf->SetTextColor(255, 0, 0);
            $cantidad = $item['cantidad'] > 1 ? $item['cantidad'] . ' ' : '';
            $pdf->Cell(7, 5, $cantidad, 0, 0, 'L');
            
            // Nombre del ítem en negro
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(33, 5, $item['nombre'], 0, 0, 'L');
        } else {
            $pdf->Cell(45, 5, '', 0, 0, 'C');
        }
    }
    $pdf->Ln();
}

$conn->close();
$pdf->Output('maleta.pdf', 'D');
?>
