<?php
require_once("../config/conexion.php");
require_once("../models/Reporte.php");

$reporte = new Reporte();

// Determinar la operación solicitada
if (isset($_GET["op"])) {
    switch ($_GET["op"]) {

        case "listar":
            $usuario_id = isset($_POST["usuario_id"]) ? $_POST["usuario_id"] : null;
            $bien_id = isset($_POST["bien_id"]) ? $_POST["bien_id"] : null;
            $fecha = isset($_POST["fecha"]) ? $_POST["fecha"] : null;

            $datos = $reporte->get_reportes($usuario_id, $bien_id, $fecha);
            if (count($datos) > 0) {
                echo json_encode($datos);
            } else {
                echo json_encode(["error" => "No se encontraron reportes"]);
            }
            break;

        case "exportar_pdf":
            require_once("../../libs/tcpdf/tcpdf.php");
            $datos = $reporte->get_reportes($_POST["usuario_id"], $_POST["bien_id"], $_POST["fecha"]);

            if (count($datos) == 0) {
                die("No hay datos para exportar.");
            }

            // Crear PDF
            $pdf = new TCPDF();
            $pdf->SetTitle("Reporte de Bienes");
            $pdf->AddPage();
            $pdf->SetFont("Helvetica", "", 12);

            $html = "<h2>Reporte de Bienes</h2><table border='1' cellpadding='5'><tr><th>ID</th><th>Usuario</th><th>Bien</th><th>Fecha</th></tr>";
            foreach ($datos as $row) {
                $html .= "<tr><td>{$row['id']}</td><td>{$row['usuario']}</td><td>{$row['bien']}</td><td>{$row['fecha']}</td></tr>";
            }
            $html .= "</table>";

            $pdf->writeHTML($html);
            $pdf->Output("reporte.pdf", "D");
            break;

        case "exportar_excel":
            require_once("../../libs/PHPExcel.php");
            $datos = $reporte->get_reportes($_POST["usuario_id"], $_POST["bien_id"], $_POST["fecha"]);

            if (count($datos) == 0) {
                die("No hay datos para exportar.");
            }

            // Crear archivo Excel
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->setTitle("Reportes");

            // Encabezados
            $sheet->setCellValue("A1", "ID");
            $sheet->setCellValue("B1", "Usuario");
            $sheet->setCellValue("C1", "Bien");
            $sheet->setCellValue("D1", "Fecha");

            // Datos
            $fila = 2;
            foreach ($datos as $row) {
                $sheet->setCellValue("A" . $fila, $row["id"]);
                $sheet->setCellValue("B" . $fila, $row["usuario"]);
                $sheet->setCellValue("C" . $fila, $row["bien"]);
                $sheet->setCellValue("D" . $fila, $row["fecha"]);
                $fila++;
            }

            // Enviar el archivo al navegador
            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment;filename=\"reporte.xlsx\"");
            header("Cache-Control: max-age=0");

            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $writer->save("php://output");
            exit;
            break;
    }
}
?>
