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
        // Obtener los datos de los vehículos desde el tipo
        $datos = $activo->get_activos();

        // Verificar si hubo algún error al obtener los datos
        if ($datos === false) {
            echo json_encode(["error" => "Error al obtener los vehículos"]);
            exit;
        }

        // Array para almacenar los datos formateados
        $data = array();

        // Recorrer los datos obtenidos y formatearlos para el DataTable
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["id"]; // ID del vehículo
            $sub_array[] = $row["sbn"]; // sbn del vehículo
            $sub_array[] = $row["serie"]; // serie del vehículo
            $sub_array[] = $row["tipo"]; // tipo del vehículo
            $sub_array[] = $row["marca"]; // Año del vehículo
            $sub_array[] = $row["modelo"]; // Año del vehículo
            $sub_array[] = $row["ubicacion"]; // Año del vehículo
            $sub_array[] = $row["responsable"]; // Año del vehículo

            // Botones de acción (editar y eliminar) para cada fila
            $sub_array[] = '
            <button type="button" class="btn btn-soft-info waves-effect waves-light btn-sm" onClick="previsualizar(' . $row["id"] . ')">
                <i class="bx bx-show-alt font-size-16 align-middle"></i>
            </button>
            <button type="button" class="btn btn-soft-warning waves-effect waves-light btn-sm" onClick="editar(' . $row["id"] . ')">
                <i class="bx bx-edit-alt font-size-16 align-middle"></i>
            </button>
            <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="eliminar(' . $row["id"] . ')">
                <i class="bx bx-trash-alt font-size-16 align-middle"></i>
            </button>';

            // Agregar la fila formateada al array de datos
            $data[] = $sub_array;
        }

        // Preparar los resultados en el formato esperado por el DataTable
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        // Enviar los resultados como JSON
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
        $responsable_id = isset($_POST["vehiculo_responsable_id"]) ? $_POST["vehiculo_responsable_id"] : null;
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
        // Capturar los datos enviados por el formulario
        $id = $_POST["vehiculo_id"];
        $sbn = $_POST["vehiculo_sbn"];
        $serie = $_POST["vehiculo_serie"];
        $tipo = $_POST["vehiculo_tipo"];
        $marca = $_POST["vehiculo_marca"];
        $modelo = $_POST["vehiculo_modelo"];
        $ubicacion = $_POST["vehiculo_ubicacion"];
        $responsable_id = $_POST["vehiculo_responsable_id"];
        $fecha_registro = $_POST["vehiculo_fecha_registro"];
        $condicion = $_POST["vehiculo_condicion"];
        $estado = $_POST["vehiculo_estado"];

        // Llamar al método editar_vehiculo del tipo
        if ($activo->editar_vehiculo($id, $sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado)) {
            echo json_encode(["success" => "Vehículo actualizado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar el vehículo."]);
        }
        break;

    case "mostrar":
        if (isset($_POST["vehiculo_id"])) {
            $datos = $activo->get_vehiculo_por_id($_POST["vehiculo_id"]);
            if ($datos) {
                echo json_encode($datos);
            } else {
                echo json_encode(["error" => "No se encontraron datos para el ID del vehículo."]);
            }
        } else {
            echo json_encode(["error" => "No se proporcionó un ID de vehículo válido."]);
        }
        break;

    case "eliminar":
        if (isset($_POST["vehiculo_id"])) {
            $id = $_POST["vehiculo_id"];
            if ($activo->cambiar_estado($id, 0)) {
                echo json_encode(["success" => "Vehículo eliminado correctamente."]);
            } else {
                echo json_encode(["error" => "Error al eliminar el vehículo."]);
            }
        } else {
            echo json_encode(["error" => "No se proporcionó un ID de vehículo válido."]);
        }
        break;

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
}
