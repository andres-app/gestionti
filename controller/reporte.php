<?php
require_once("../config/conexion.php");
require_once("../models/Reporte.php");

$reporte = new Reporte();

switch ($_GET["op"]) {

    case "listar":
        $usuario_id = isset($_GET["usuario"]) && !empty($_GET["usuario"]) ? $_GET["usuario"] : null;
        $tipo_activo = isset($_GET["tipo_activo"]) && !empty($_GET["tipo_activo"]) ? $_GET["tipo_activo"] : null;
        $fecha = isset($_GET["fecha"]) && !empty($_GET["fecha"]) ? $_GET["fecha"] : null;

        $datos = $reporte->get_reportes($usuario_id, $tipo_activo, $fecha);

        if (!$datos || count($datos) == 0) {
            echo json_encode(["data" => []]);
            exit;
        }

        // Agregar acciones a cada fila
        $data = [];
        foreach ($datos as $row) {
            $row["acciones"] = '
                <button type="button" class="btn btn-info btn-sm" onClick="verDetalle(' . $row["id"] . ')">
                    <i class="bx bx-show-alt"></i> Ver
                </button>
                <button type="button" class="btn btn-danger btn-sm" onClick="eliminarReporte(' . $row["id"] . ')">
                    <i class="bx bx-trash"></i> Eliminar
                </button>';
            $data[] = $row;
        }

        echo json_encode([
            "draw" => intval($_GET['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ]);
        exit;


    case "obtener_usuarios":
        require_once("../models/Usuario.php");
        $usuario = new Usuario();
        $datos = $usuario->get_usuarios();

        // 🔹 Depuración: Ver datos antes de enviarlos
        error_log("📌 Usuarios obtenidos: " . json_encode($datos));

        // Verificar que los datos tengan la estructura correcta
        $usuarios = [];
        foreach ($datos as $row) {
            if (isset($row["usu_id"]) && isset($row["usu_nomape"])) {
                $usuarios[] = [
                    "usu_id" => $row["usu_id"],
                    "usu_nomape" => $row["usu_nomape"]
                ];
            }
        }

        echo json_encode($usuarios);
        exit;


    case "obtener_tipos_activos":
        require_once("../models/Activo.php");
        $activo = new Activo();
        $datos = $activo->get_tipos_activos();

        if (!$datos) {
            echo json_encode(["error" => "No se encontraron tipos de activos"]);
        } else {
            echo json_encode($datos);
        }
        break;

    case "exportar_pdf":
        require_once("../include/tcpdf/tcpdf.php"); // Asegúrate de que la ruta es correcta

        $usuario_id = isset($_GET["usuario"]) ? $_GET["usuario"] : null;
        $tipo_activo = isset($_GET["tipo_activo"]) ? $_GET["tipo_activo"] : null;
        $fecha = isset($_GET["fecha"]) ? $_GET["fecha"] : null;

        $datos = $reporte->get_reportes($usuario_id, $tipo_activo, $fecha);

        if (!$datos || count($datos) == 0) {
            echo json_encode(["error" => "No hay datos para exportar"]);
            exit;
        }

        // Crear PDF con orientación horizontal (landscape)
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema de Reportes');
        $pdf->SetTitle('Reporte de Activos');
        $pdf->SetHeaderData('', 0, 'Reporte de Activos', "Generado el: " . date('d/m/Y'));
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 8);

        // Encabezados de la tabla
        $html = '<table border="1" cellpadding="4">
                        <thead>
                            <tr style="background-color:#cccccc;">
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>SBN</th>
                                <th>Serie</th>
                                <th>Tipo Activo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Ubicación</th>
                                <th>Hostname</th>
                                <th>Procesador</th>
                                <th>Sis. Ope.</th>
                                <th>RAM</th>
                                <th>Disco</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>';

        // Agregar los datos
        foreach ($datos as $row) {
            $html .= '<tr>
                            <td>' . $row["id"] . '</td>
                            <td>' . utf8_decode($row["usuario"]) . '</td>
                            <td>' . $row["sbn"] . '</td>
                            <td>' . $row["serie"] . '</td>
                            <td>' . utf8_decode($row["tipo_activo"]) . '</td>
                            <td>' . utf8_decode($row["marca"]) . '</td>
                            <td>' . utf8_decode($row["modelo"]) . '</td>
                            <td>' . utf8_decode($row["ubicacion"]) . '</td>
                            <td>' . utf8_decode($row["hostname"]) . '</td>
                            <td>' . utf8_decode($row["procesador"]) . '</td>
                            <td>' . utf8_decode($row["sisopera"]) . '</td>
                            <td>' . utf8_decode($row["ram"]) . '</td>
                            <td>' . utf8_decode($row["disco"]) . '</td>
                            <td>' . $row["fecha"] . '</td>
                        </tr>';
        }

        $html .= '</tbody></table>';

        // Agregar el contenido al PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Descargar el archivo
        $pdf->Output('Reporte_Activos.pdf', 'D');
        exit;

    case "exportar_excel":
        $usuario_id = isset($_GET["usuario"]) ? $_GET["usuario"] : null;
        $tipo_activo = isset($_GET["tipo_activo"]) ? $_GET["tipo_activo"] : null;
        $fecha = isset($_GET["fecha"]) ? $_GET["fecha"] : null;

        $datos = $reporte->get_reportes($usuario_id, $tipo_activo, $fecha);

        if (!$datos || count($datos) == 0) {
            die("No hay datos para exportar.");
        }

        // Definir las cabeceras para Excel
        header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
        header("Content-Disposition: attachment; filename=Reporte_Activos.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Construir la tabla
        echo "<table border='1'>";
        echo "<thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>SBN</th>
                        <th>Serie</th>
                        <th>Tipo Activo</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ubicación</th>
                        <th>Hostname</th>
                        <th>Procesador</th>
                        <th>Sis. Ope.</th>
                        <th>RAM</th>
                        <th>Disco</th>
                        <th>Fecha</th>
                    </tr>
                  </thead>";

        echo "<tbody>";
        foreach ($datos as $row) {
            echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['usuario']}</td>
                        <td>{$row['sbn']}</td>
                        <td>{$row['serie']}</td>
                        <td>{$row['tipo_activo']}</td>
                        <td>{$row['marca']}</td>
                        <td>{$row['modelo']}</td>
                        <td>{$row['ubicacion']}</td>
                        <td>{$row['hostname']}</td>
                        <td>{$row['procesador']}</td>
                        <td>{$row['sisopera']}</td>
                        <td>{$row['ram']}</td>
                        <td>{$row['disco']}</td>
                        <td>{$row['fecha']}</td>
                      </tr>";
        }
        echo "</tbody></table>";

        exit;
}
