<?php
/* controller/unidad.php */
require_once("../config/conexion.php");
require_once("../models/Unidad.php");

$unidad = new Unidad();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    case "combo":
        $datos = $unidad->get_unidad();
        $html = "<option value=''>Seleccionar</option>";
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['unidad_id'] . "'>" . $row['unidad_nom'] . "</option>";
            }
        }
        echo $html;
        break;

    case "guardaryeditar":
        $id  = isset($_POST["unidad_id"])  ? trim($_POST["unidad_id"])  : "";
        $nom = isset($_POST["unidad_nom"]) ? trim($_POST["unidad_nom"]) : "";

        if ($nom === "") {
            echo "0";
            exit;
        }

        if ($unidad->existe_unidad_por_nombre($nom, $id ?: null)) {
            echo "0";
            exit; // duplicado
        }

        if ($id === "") {
            $unidad->insert_unidad($nom);
            echo "1";
        } else {
            $unidad->update_unidad($id, $nom);
            echo "2";
        }
        break;

    case "mostrar":
        $datos = $unidad->get_unidad_x_id($_POST["unidad_id"]);
        $output = [];
        if (is_array($datos) && count($datos) > 0) {
            $row = $datos[0];
            $output["unidad_id"]  = $row["unidad_id"];
            $output["unidad_nom"] = $row["unidad_nom"];
        }
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($output);
        break;

    case "eliminar":
        $unidad->eliminar_unidad($_POST["unidad_id"]);
        echo "1";
        break;

    case "permiso":
        $datos = $unidad->get_unidad_usuario_permisos($_POST["usu_id"]);
        $data = [];
        foreach ($datos as $row) {
            $sub = [];
            $sub[] = $row["unidad_nom"];
            if (($row["unidd_permi"] ?? "No") === "Si") {
                $sub[] = '<button type="button" class="btn btn-soft-success waves-effect waves-light btn-sm" onClick="deshabilitar(' . $row["unidd_id"] . ')"><i class="bx bx-check-double font-size-16 align-middle"></i> Si</button>';
            } else {
                $sub[] = '<button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="habilitar(' . $row["unidd_id"] . ')"><i class="bx bx-window-close font-size-16 align-middle"></i> No</button>';
            }
            $data[] = $sub;
        }
        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($results);
        break;

    case "habilitar":
        $unidad->habilitar_unidad_usuario($_POST["unidd_id"]);
        echo "1";
        break;

    case "deshabilitar":
        $unidad->deshabilitar_unidad_usuario($_POST["unidd_id"]);
        echo "1";
        break;

    case "listar":
        $datos = $unidad->get_unidad();
        $data = [];

        foreach ($datos as $row) {
            // Preferir fecha de creación; si no hay, usar modific.
            $fechaRaw = $row["fech_crea"] ?? null;
            if (empty($fechaRaw)) $fechaRaw = $row["fech_modi"] ?? null;
            if (empty($fechaRaw)) $fechaRaw = $row["fech_elim"] ?? null;

            $fecha = "";
            if (!empty($fechaRaw)) {
                $ts = strtotime($fechaRaw);
                $fecha = $ts ? date("d/m/Y", $ts) : (string)$fechaRaw;
            }

            $sub = [];
            $sub[] = $row["unidad_nom"];
            $sub[] = $fecha;
            $sub[] = '
            <button type="button" class="btn btn-soft-warning waves-effect waves-light btn-sm" onClick="editar(' . $row["unidad_id"] . ')">
                <i class="bx bx-edit-alt font-size-16 align-middle"></i>
            </button>
            <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="eliminar(' . $row["unidad_id"] . ')">
                <i class="bx bx-trash-alt font-size-16 align-middle"></i>
            </button>';
            $data[] = $sub;
        }

        $results = [
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        ];
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($results);
        break;
}
