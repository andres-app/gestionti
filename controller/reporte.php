<?php
require_once("../config/conexion.php");
require_once("../models/Reporte.php");

$reporte = new Reporte();

switch ($_GET["op"]) {

    case "listar":
        $usuario_id = isset($_GET["usuario"]) && !empty($_GET["usuario"]) ? $_GET["usuario"] : null;
        $activo_id = isset($_GET["activo"]) && !empty($_GET["activo"]) ? $_GET["activo"] : null;
        $fecha = isset($_GET["fecha"]) && !empty($_GET["fecha"]) ? $_GET["fecha"] : null;
    
        $datos = $reporte->get_reportes($usuario_id, $activo_id, $fecha);
    
        if (!$datos || count($datos) == 0) {
            echo json_encode(["data" => []]); // DataTables necesita esta estructura para evitar errores
            exit;
        }
    
        echo json_encode([
            "draw" => intval($_GET['draw'] ?? 1),  // Para compatibilidad con DataTables
            "recordsTotal" => count($datos),
            "recordsFiltered" => count($datos),
            "data" => $datos // Cambiado de `aaData` a `data`
        ]);
        exit;
    
    


        // 🔹 Agregar columna de acciones a cada fila
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
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data // Aseguramos que "acciones" esté en cada fila
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


    case "obtener_activos":
        require_once("../models/Activo.php");
        $activo = new Activo();
        $datos = $activo->get_activos();
        echo json_encode($datos);
        break;

    default:
        echo json_encode(["error" => "Operación no válida."]);
        break;
}
