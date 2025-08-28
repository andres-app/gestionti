<?php
/* controller/dependencia.php */
require_once("../config/conexion.php");
require_once("../models/Dependencia.php");

$dependencia = new Dependencia();
$op = isset($_GET["op"]) ? $_GET["op"] : "";

switch ($op) {

    case "combo":
        $datos = $dependencia->get_dependencia();
        $html = "<option value=''>Seleccionar</option>";
        if (is_array($datos) && count($datos) > 0) {
            foreach ($datos as $row) {
                $html .= "<option value='" . $row['dependencia_id'] . "'>" . $row['dependencia_nom'] . "</option>";
            }
        }
        echo $html;
        break;

    case "guardaryeditar":
        $id  = isset($_POST["dependencia_id"])  ? trim($_POST["dependencia_id"])  : "";
        $nom = isset($_POST["dependencia_nom"]) ? trim($_POST["dependencia_nom"]) : "";

        if ($nom === "") { echo "0"; exit; }

        if ($dependencia->existe_dependencia_por_nombre($nom, $id ?: null)) {
            echo "0"; exit; // duplicado
        }

        if ($id === "") {
            $dependencia->insert_dependencia($nom);
            echo "1";
        } else {
            $dependencia->update_dependencia($id, $nom);
            echo "2";
        }
        break;

    case "mostrar":
        $datos = $dependencia->get_dependencia_x_id($_POST["dependencia_id"]);
        $output = [];
        if (is_array($datos) && count($datos) > 0) {
            $row = $datos[0];
            $output["dependencia_id"]  = $row["dependencia_id"];
            $output["dependencia_nom"] = $row["dependencia_nom"];
        }
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($output);
        break;

    case "eliminar":
        $dependencia->eliminar_dependencia($_POST["dependencia_id"]);
        echo "1";
        break;

    /* ==== Opcional: gestión de permisos si usas el módulo ==== */
    case "permiso":
        $datos = $dependencia->get_dependencia_usuario_permisos($_POST["usu_id"]);
        $data = [];
        foreach ($datos as $row) {
            $sub = [];
            $sub[] = $row["dependencia_nom"];
            if (($row["depdd_permi"] ?? "No") === "Si") {
                $sub[] = '<button type="button" class="btn btn-soft-success waves-effect waves-light btn-sm" onClick="deshabilitar(' . $row["depdd_id"] . ')"><i class="bx bx-check-double font-size-16 align-middle"></i> Si</button>';
            } else {
                $sub[] = '<button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="habilitar(' . $row["depdd_id"] . ')"><i class="bx bx-window-close font-size-16 align-middle"></i> No</button>';
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
        $dependencia->habilitar_dependencia_usuario($_POST["depdd_id"]);
        echo "1";
        break;

    case "deshabilitar":
        $dependencia->deshabilitar_dependencia_usuario($_POST["depdd_id"]);
        echo "1";
        break;

    case "listar":
        $datos = $dependencia->get_dependencia();
        $data = [];

        foreach ($datos as $row) {
            // Preferir fecha de creación; si no hay, usar modific/eliminación.
            $fechaRaw = $row["fech_crea"] ?? null;
            if (empty($fechaRaw)) $fechaRaw = $row["fech_modi"] ?? null;
            if (empty($fechaRaw)) $fechaRaw = $row["fech_elim"] ?? null;

            $fecha = "";
            if (!empty($fechaRaw)) {
                $ts = strtotime($fechaRaw);
                $fecha = $ts ? date("d/m/Y", $ts) : (string)$fechaRaw;
            }

            $sub = [];
            $sub[] = $row["dependencia_nom"];
            $sub[] = $fecha;
            $sub[] = '
            <button type="button" class="btn btn-soft-warning waves-effect waves-light btn-sm" onClick="editar(' . $row["dependencia_id"] . ')">
                <i class="bx bx-edit-alt font-size-16 align-middle"></i>
            </button>
            <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="eliminar(' . $row["dependencia_id"] . ')">
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
