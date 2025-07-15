<?php
require_once("../config/conexion.php");
require_once("../models/Sede.php");

$sede = new Sede();

switch ($_GET["op"]) {

    case "guardaryeditar":
        $datos = $sede->get_sede_nombre($_POST["sede_nom"]);
        if (is_array($datos) == true and count($datos) == 0) {
            if (empty($_POST["sede_id"])) {
                $sede->insert_sede($_POST["sede_nom"]);
                echo "1"; // Nuevo registro
            } else {
                $sede->update_sede($_POST["sede_id"], $_POST["sede_nom"]);
                echo "2"; // Edición
            }
        } else {
            echo "0"; // Ya existe
        }
        break;

    case "mostrar":
        $datos = $sede->get_sede_x_id($_POST["sede_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
            foreach ($datos as $row) {
                $output["sede_id"] = $row["sede_id"];
                $output["sede_nom"] = $row["sede_nom"];
            }
            echo json_encode($output);
        }
        break;

    case "eliminar":
        $sede->deshabilitar_sede($_POST["sede_id"]);
        echo "1";
        break;

    case "listar":
        $datos = $sede->get_sede();
        $data = array();

        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["sede_nom"];
            $sub_array[] = date("d/m/Y", strtotime($row["fech_crea"]));
            $sub_array[] = '
                <button type="button" class="btn btn-soft-warning waves-effect waves-light btn-sm" onClick="editar(' . $row["sede_id"] . ')">
                    <i class="bx bx-edit-alt font-size-16 align-middle"></i>
                </button>
                <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-sm" onClick="eliminar(' . $row["sede_id"] . ')">
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
