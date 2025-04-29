<?php
require_once("../config/conexion.php");
require_once("../models/Activo.php");

// Crear instancia del tipo Vehículo
$activo = new Activo();
$total_activos = $activo->get_total_activos(); // Obtenemos el total de vehículos


// Obtener vehículos con próximos mantenimientos
$proximos_mantenimientos = $activo->get_proximos_mantenimientos();


// Evaluar el valor del parámetro "op" para determinar qué operación realizar
switch ($_GET["op"]) {

    // Caso para listar todos los vehículos
    case "listar":
        $datos = $activo->get_activos();
        if ($datos === false) {
            echo json_encode(["error" => "Error al obtener los activos"]);
            exit;
        }

        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array["id"] = $row["id"];
            $sub_array["sbn"] = $row["sbn"];
            $sub_array["serie"] = $row["serie"];
            $sub_array["tipo"] = $row["tipo"];
            $sub_array["marca"] = $row["marca"];
            $sub_array["modelo"] = $row["modelo"];
            $sub_array["ubicacion"] = $row["ubicacion"];
            $sub_array["responsable"] = $row["responsable"];

            // 🔹 Datos para el modal (NO se mostrarán en DataTable)
            $sub_array["hostname"] = $row["hostname"];
            $sub_array["procesador"] = $row["procesador"];
            $sub_array["sisopera"] = $row["sisopera"];
            $sub_array["ram"] = $row["ram"];
            $sub_array["disco"] = $row["disco"];

            // 🔹 Botones de acción
            $sub_array["acciones"] = '
                    <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm" onClick="previsualizar(' . $row["id"] . ')">
                        <i class="bx bx-show-alt font-size-16 align-middle"></i>
                    </button>
                    <button type="button" class="btn btn-soft-warning waves-effect waves-light btn-sm" onClick="editar(' . $row["id"] . ')">
                        <i class="bx bx-edit-alt font-size-16 align-middle"></i>
                    </button>
                    <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="eliminar(' . $row["id"] . ')">
                        <i class="bx bx-trash-alt font-size-16 align-middle"></i>
                    </button>';

            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        header('Content-Type: application/json');
        echo json_encode($results);
        break;

    // Caso para insertar un nuevo vehículo
    case "insertar":
        // Capturar los datos enviados desde el formulario
        $sbn = isset($_POST["vehiculo_sbn"]) ? $_POST["vehiculo_sbn"] : null;
        $serie = isset($_POST["vehiculo_serie"]) ? $_POST["vehiculo_serie"] : null;
        $tipo = isset($_POST["vehiculo_tipo"]) ? $_POST["vehiculo_tipo"] : null;
        $marca = isset($_POST["vehiculo_marca"]) ? $_POST["vehiculo_marca"] : null;
        $modelo = isset($_POST["vehiculo_modelo"]) ? $_POST["vehiculo_modelo"] : null;
        $ubicacion = isset($_POST["vehiculo_ubicacion"]) ? $_POST["vehiculo_ubicacion"] : null;
        $responsable = isset($_POST["vehiculo_responsable"]) ? $_POST["vehiculo_responsable"] : null;
        $fecha_registro = isset($_POST["vehiculo_fecha_registro"]) ? $_POST["vehiculo_fecha_registro"] : null;
        $condicion = isset($_POST["vehiculo_condicion"]) ? $_POST["vehiculo_condicion"] : null;
        $estado = isset($_POST["vehiculo_estado"]) ? $_POST["vehiculo_estado"] : null;

        // Insertar el nuevo vehículo en la base de datos usando el tipo
        if ($activo->insertar_vehiculo($sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $tipo, $condicion, $estado)) {
            echo json_encode(["success" => "Vehículo registrado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al registrar el vehículo."]);
        }
        break;

    // Caso para editar un vehículo existente
    case "editar":
        // 🚀 REGISTRAR EN LOG LOS DATOS RECIBIDOS
        error_log("📌 Datos recibidos para editar: " . json_encode($_POST));

        // Capturar los datos enviados por el formulario
        $id = $_POST["vehiculo_id"] ?? null;
        $sbn = $_POST["vehiculo_sbn"] ?? null;
        $serie = $_POST["vehiculo_serie"] ?? null;
        $tipo = $_POST["vehiculo_tipo"] ?? null;
        $marca = $_POST["vehiculo_marca"] ?? null;
        $modelo = $_POST["vehiculo_modelo"] ?? null;
        $ubicacion = $_POST["vehiculo_ubicacion"] ?? null;
        $responsable_id = $_POST["vehiculo_responsable_id"] ?? null;
        $fecha_registro = $_POST["vehiculo_fecha_registro"] ?? null;
        $condicion = $_POST["vehiculo_condicion"] ?? null;
        $estado = $_POST["vehiculo_estado"] ?? null;
        $hostname = $_POST["vehiculo_hostname"] ?? null;
        $procesador = $_POST["vehiculo_procesador"] ?? null;
        $sisopera = $_POST["vehiculo_sisopera"] ?? null;
        $ram = $_POST["vehiculo_ram"] ?? null;
        $disco = $_POST["vehiculo_disco"] ?? null;

        // 🚨 Verificar si algún campo clave está vacío
        if (!$id || !$sbn || !$serie) {
            error_log("❌ Faltan datos obligatorios: ID: $id, SBN: $sbn, Serie: $serie");
            echo json_encode(["error" => "Faltan datos obligatorios."]);
            exit;
        }

        // 🔥 LLAMAMOS A LA FUNCIÓN DE ACTUALIZACIÓN
        $resultado = $activo->editar_vehiculo($id, $sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $hostname, $procesador, $sisopera, $ram, $disco);

        if ($resultado) {
            echo json_encode(["success" => "Vehículo actualizado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar el vehículo."]);
        }
        break;



    case "obtener_responsables":
        require_once("../models/Usuario.php");
        $usuario = new Usuario();

        $datos = $usuario->get_usuarios(); // Obtén los usuarios de tu modelo
        echo json_encode($datos);
        break;


    case "mostrar":
        if (isset($_POST["vehiculo_id"])) {
            $datos = $activo->get_vehiculo_por_id($_POST["vehiculo_id"]);

            if ($datos) {
                // 🔹 Asegurarnos de que la respuesta sea JSON sin errores
                header('Content-Type: application/json');
                echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            } else {
                echo json_encode(["error" => "No se encontraron datos para el ID del vehículo."]);
            }
        } else {
            echo json_encode(["error" => "No se proporcionó un ID de vehículo válido."]);
        }
        break;



    case "eliminar":
        if (!isset($_POST["vehiculo_id"]) || empty($_POST["vehiculo_id"])) {
            echo json_encode(["success" => false, "error" => "ID de activo no recibido"]);
            exit;
        }

        $vehiculo_id = $_POST["vehiculo_id"];
        $resultado = $activo->cambiar_estado($vehiculo_id, 0); // Cambia estado a inactivo

        if ($resultado) {
            echo json_encode(["success" => true, "message" => "Activo eliminado correctamente"]);
        } else {
            echo json_encode(["success" => false, "error" => "No se pudo eliminar el activo"]);
        }
        exit;


    default:
        echo json_encode(["error" => "Operación no válida."]);
        break;

    case "obtener_fotos":
        if (isset($_GET["vehiculo_id"])) {
            $vehiculo_id = $_GET["vehiculo_id"];
            $fotos = $activo->get_fotos_por_activo($vehiculo_id);

            if ($fotos) {
                echo json_encode($fotos);
            } else {
                echo json_encode(["error" => "No se encontraron fotos para este activo."]);
            }
        } else {
            echo json_encode(["error" => "ID del vehículo no proporcionado."]);
        }
        break;

    case "activos_estado":
        $datos = $activo->get_activos_por_estado();
        echo json_encode($datos);
        break;

    case "activos_tipo":
        $datos = $activo->get_activos_por_tipo();
        echo json_encode($datos);
        break;
}
