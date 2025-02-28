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
        
}
