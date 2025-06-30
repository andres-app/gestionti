<?php
require_once("../config/conexion.php");
require_once("../models/Encargatura.php");

$encargatura = new Encargatura();

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
            empty($_POST["glpi"]) ||
            empty($_POST["titular"]) ||
            empty($_POST["encargado"]) ||
            empty($_POST["fecha_inicio"])
        ) {
            echo json_encode(["success" => false, "error" => "Faltan datos obligatorios"]);
            exit;
        }

        $glpi           = $_POST["glpi"];
        $titular        = $_POST["titular"];
        $encargado      = $_POST["encargado"];
        $fecha_inicio   = $_POST["fecha_inicio"];
        $fecha_fin      = $_POST["fecha_finalizacion"] ?? null;
        $registrado     = $_SESSION["usu_nomape"];
        $estado         = "En Curso"; // por defecto
        $observaciones = $_POST["observaciones"] ?? '';

 

        $resultado = $encargatura->registrar_encargatura(
            $glpi,
            $titular,
            $encargado,
            $fecha_inicio,
            $fecha_fin,
            $registrado,
            $estado,
            $observaciones
        );

        echo json_encode(["success" => $resultado]);
        break;


    case "listar":
        $estado = $_GET['estado'] ?? 'En Curso'; // Valor por defecto
        $datos = $encargatura->listar_encargaturas($estado);
        echo json_encode(["data" => $datos]);
        break;

    case "marcar_devuelto":
        $encargatura_id       = $_POST["id"] ?? null;
        $observaciones        = $_POST["observaciones"] ?? '';
        $fecha_devolucion_real = $_POST["fecha_devolucion_real"] ?? null;

        if (!$encargatura_id) {
            echo json_encode(["success" => false, "error" => "ID no recibido"]);
            exit;
        }

        $resultado = $encargatura->marcar_como_finalizado($encargatura_id, $observaciones, $fecha_devolucion_real);
        echo json_encode(["success" => $resultado]);
        break;

    default:
        echo json_encode(["success" => false, "error" => "Operación no válida"]);
        break;
}
