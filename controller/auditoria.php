<?php
require_once("../models/Auditoria.php");
$auditoria = new Auditoria();

switch ($_GET["op"]) {
    case "listar":
        $filtro_tabla = isset($_POST["tabla_afectada"]) ? $_POST["tabla_afectada"] : null;
        $datos = $auditoria->obtener_todo_historial($filtro_tabla);
        echo json_encode($datos);
        break;
}
?>
