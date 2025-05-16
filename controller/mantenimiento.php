<?php
ob_start(); // ✅ Asegura que no se envíe salida antes del JSON
session_start();
require_once("../config/conexion.php");
require_once("../models/Mantenimiento.php");

$mantenimiento = new Mantenimiento();

switch ($_GET["op"]) {
    
    case "registrar":
        $mantenimiento = new Mantenimiento();

        $activo_id = $_POST["activo_id"];
        $fecha = $_POST["fecha"];
        $proveedor = $_POST["proveedor"];
        $detalle = $_POST["detalle"];
        $usuario_id = $_SESSION["usu_id"] ?? 0;

        $resultado = $mantenimiento->registrar($activo_id, $usuario_id, $fecha, $proveedor, $detalle);

        echo json_encode(["success" => $resultado]);
        break;


    case "listar":
        $activo_id = $_POST["activo_id"];
        $data = $mantenimiento->listar_por_activo($activo_id);
        echo json_encode($data);
        break;
}
