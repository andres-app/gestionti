<?php
require_once("../config/conexion.php");
require_once("../models/Prestamo.php");
$prestamo = new Prestamo();

switch ($_GET["op"]) {

    case "usuarios_destino":
        require_once("../models/Usuario.php");
        $usuario = new Usuario();
        $datos = $usuario->get_usuarios(); // Ya existe en tu sistema
        echo json_encode($datos);
        break;

    case "insertar":
        $activo_id = $_POST["activo_id"];
        $usuario_origen_id = $_SESSION["usu_id"];
        $usuario_destino_id = $_POST["usuario_destino"];
        $fecha_prestamo = $_POST["fecha_prestamo"];
        $fecha_devolucion_estimada = $_POST["fecha_devolucion_estimada"] ?? null;
        $observaciones = $_POST["observaciones"] ?? "";

        $resultado = $prestamo->registrar_prestamo(
            $activo_id,
            $usuario_origen_id,
            $usuario_destino_id,
            $fecha_prestamo,
            $fecha_devolucion_estimada,
            $observaciones
        );

        echo json_encode(["success" => $resultado]);
        break;

    case "listar":
        $datos = $prestamo->listar_prestamos();
        echo json_encode(["data" => $datos]);
        break;

    case "marcar_devuelto":
        $prestamo_id = $_POST["id"] ?? null;

        if ($prestamo_id) {
            $resultado = $prestamo->marcar_como_devuelto($prestamo_id);
            echo json_encode(["success" => $resultado]);
        } else {
            echo json_encode(["success" => false, "error" => "ID no recibido"]);
        }
        break;

    case "activos_osin":
        require_once("../models/Activo.php");
        $activo = new Activo();
        $datos = $activo->get_activos_disponibles_osin(); // función que agregamos abajo
        echo json_encode($datos);
        break;


    default:
        echo json_encode(["success" => false, "error" => "Operación no válida"]);
        break;
}
