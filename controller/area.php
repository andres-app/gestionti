<?php
/* TODO: Incluye el archivo de configuración de la conexión a la base de datos y la clase Usuario */
require_once("../config/conexion.php");
require_once("../models/Area.php");

/* TODO:Crea una instancia de la clase Area */
$area = new Area();

/* TODO: Utiliza una estructura switch para determinar la operación a realizar según el valor de $_GET["op"] */
switch ($_GET["op"]) {

    /* TODO: Si la operación es "combo" */
    case "combo":
        $datos = $area->get_area();
        $html = "";
        $html .= "<option value=''>Seleccionar</option>";
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['area_id'] . "'>" . $row['area_nom'] . "</option>";
            }
            echo $html;
        }
        break;

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


    case "guardaryeditar":
        $datos = $area->get_area_nombre($_POST["area_nom"]);
        if (is_array($datos) == true and count($datos) == 0) {
            if (empty($_POST["area_id"])) {
                $area->insert_area($_POST["area_nom"]);
                echo "1";
            } else {
                $area->update_area($_POST["area_id"], $_POST["area_nom"]);
                echo "2";
            }
        } else {
            echo "0";
        }
        break;

    case "mostrar":
        $datos = $area->get_area_x_id($_POST["area_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["area_id"] = $row["area_id"];
                $output["area_nom"] = $row["area_nom"];
                // $output["area_correo"] = $row["area_correo"];  // <-- Eliminado
            }
            echo json_encode($output);
        }
        break;

    case "eliminar":
        $area->eliminar_area($_POST["area_id"]);
        echo "1";
        break;

    case "permiso":
        $datos = $area->get_area_usuario_permisos($_POST["usu_id"]);
        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["area_nom"];
            if ($row["aread_permi"] == "Si") {
                $sub_array[] = '<button type="button" class="btn btn-soft-success waves-effect waves-light btn-sm" onClick="deshabilitar(' . $row["aread_id"] . ')"><i class="bx bx-check-double font-size-16 align-middle"></i> Si</button>';
            } else {
                $sub_array[] = '<button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="habilitar(' . $row["aread_id"] . ')"><i class="bx bx-window-close font-size-16 align-middle"></i> No</button>';
            }
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;

    case "habilitar":
        $area->habilitar_area_usuario($_POST["aread_id"]);
        echo "1";
        break;

    case "deshabilitar":
        $area->deshabilitar_area_usuario($_POST["aread_id"]);
        echo "1";
        break;

    case "listar":
        $datos = $area->get_area();
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["area_nom"];
            // $sub_array[] = $row["area_correo"]; // ELIMINADO
            $sub_array[] = date("d/m/Y", strtotime($row["fech_crea"]));
            $sub_array[] = '
        <button type="button" class="btn btn-soft-warning waves-effect waves-light btn-sm" onClick="editar(' . $row["area_id"] . ')">
            <i class="bx bx-edit-alt font-size-16 align-middle"></i>
        </button>
        <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="eliminar(' . $row["area_id"] . ')">
            <i class="bx bx-trash-alt font-size-16 align-middle"></i>
        </button>
    ';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        echo json_encode($results);
        break;
}
