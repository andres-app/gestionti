<?php
require_once("../config/conexion.php");
require_once("../models/Prestamo.php");

$prestamo = new Prestamo();

switch ($_GET["op"]) {

    case "usuarios_destino":
        require_once("../models/Usuario.php");
        $usuario = new Usuario();
        $datos = $usuario->get_usuarios();
        echo json_encode($datos);
        break;

    case "insertar":
        // Validaciones mínimas
        if (
            empty($_POST["activo_id"]) ||
            empty($_POST["usuario_destino"]) ||
            empty($_POST["fecha_prestamo"])
        ) {
            echo json_encode(["success" => false, "error" => "Faltan datos obligatorios"]);
            exit;
        }

        $activo_id = $_POST["activo_id"];
        $usuario_destino_id = $_POST["usuario_destino"];
        $usuario_origen_id = $_SESSION["usu_id"];
        $fecha_prestamo = $_POST["fecha_prestamo"];
        $fecha_devolucion_estimada = $_POST["fecha_devolucion_estimada"] ?? null;
        $observaciones = $_POST["observaciones"] ?? '';

        // Validación extra: la fecha de devolución debe ser posterior
        if ($fecha_devolucion_estimada && strtotime($fecha_devolucion_estimada) <= strtotime($fecha_prestamo)) {
            echo json_encode(["success" => false, "error" => "La fecha de devolución debe ser posterior a la de préstamo"]);
            exit;
        }

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
        $observaciones = $_POST["observaciones"] ?? '';

        if (!$prestamo_id) {
            echo json_encode(["success" => false, "error" => "ID no recibido"]);
            exit;
        }

        $resultado = $prestamo->marcar_como_devuelto($prestamo_id, $observaciones);
        echo json_encode(["success" => $resultado]);
        break;

    case "activos_osin":
        require_once("../models/Activo.php");
        $activo = new Activo();
        $datos = $activo->get_activos_disponibles_osin(); // Solo los activos no prestados
        echo json_encode($datos);
        break;

    default:
        echo json_encode(["success" => false, "error" => "Operación no válida"]);
        break;
}
