<?php
require_once("../config/conexion.php");
require_once("../models/Reporte.php");

// 🔹 Agregar librerías correctamente para PDF y Excel (deben estar instaladas con Composer)
require_once("../include/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$reporte = new Reporte();

// Verificar si el parámetro "op" está presente
if (!isset($_GET["op"])) {
    echo json_encode(["error" => "Operación no especificada"]);
    exit;
}

switch ($_GET["op"]) {

    case "listar":
        $usuario_id = isset($_GET["usuario_id"]) ? $_GET["usuario_id"] : null;
        $bien_id = isset($_GET["bien_id"]) ? $_GET["bien_id"] : null;
        $fecha = isset($_GET["fecha"]) ? $_GET["fecha"] : null;
    
        $datos = $reporte->get_reportes($usuario_id, $bien_id, $fecha);
    
        // Asegurar que la respuesta sea un JSON válido
        header("Content-Type: application/json");
    
        if (!empty($datos)) {
            echo json_encode(["data" => $datos]);
        } else {
            echo json_encode(["data" => []]); // Devolver un array vacío en caso de no haber datos
        }
        break;
    

    case "exportar_pdf":
        $pdf = new TCPDF();
        $pdf->SetTitle("Reporte de Bienes");
        $pdf->AddPage();
        $pdf->SetFont("Helvetica", "", 12);

        $datos = $reporte->get_reportes($_GET["usuario_id"] ?? null, $_GET["bien_id"] ?? null, $_GET["fecha"] ?? null);
        if (count($datos) == 0) {
            die("No hay datos para exportar.");
        }

        $html = "<h2>Reporte de Bienes</h2><table border='1' cellpadding='5'>";
        foreach ($datos as $row) {
            $html .= "<tr><td>{$row['id']}</td><td>{$row['usuario']}</td><td>{$row['bien']}</td><td>{$row['fecha']}</td></tr>";
        }
        $html .= "</table>";

        $pdf->writeHTML($html);
        $pdf->Output("reporte.pdf", "D");
        break;

    case "exportar_excel":
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Reportes");

        $sheet->setCellValue("A1", "ID");
        $sheet->setCellValue("B1", "Usuario");
        $sheet->setCellValue("C1", "Bien");
        $sheet->setCellValue("D1", "Fecha");

        $datos = $reporte->get_reportes($_GET["usuario_id"] ?? null, $_GET["bien_id"] ?? null, $_GET["fecha"] ?? null);
        $fila = 2;
        foreach ($datos as $row) {
            $sheet->setCellValue("A" . $fila, $row["id"]);
            $sheet->setCellValue("B" . $fila, $row["usuario"]);
            $sheet->setCellValue("C" . $fila, $row["bien"]);
            $sheet->setCellValue("D" . $fila, $row["fecha"]);
            $fila++;
        }

        header("Content-Disposition: attachment; filename=reporte.xlsx");
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Cache-Control: max-age=0");

        $writer = new Xlsx($spreadsheet);
        $writer->save("php://output");
        exit;
        break;
}
