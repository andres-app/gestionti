<?php
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/../models/Baja.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$baja = new Baja();

switch ($_GET["op"]) {
    case "registrar":
        $activo_id = $_POST["activo_id"];
        $motivo = $_POST["motivo"];
        $archivo = "";

        $destino_fisico = __DIR__ . '/../assets/document/bajas/';
        if (!file_exists($destino_fisico)) {
            mkdir($destino_fisico, 0777, true);
        }

        if (!empty($_FILES["archivo"]["tmp_name"])) {
            $nombre = "baja_" . time() . "_" . basename($_FILES["archivo"]["name"]);
            $ruta = $destino_fisico . $nombre;

            if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta)) {
                $archivo = "assets/document/bajas/" . $nombre;
            }
        }

        $res = $baja->registrar_baja($activo_id, $motivo, $archivo);

        if ($res) {
            $pdo = (new Conectar())->conexion();
            $stmt = $pdo->prepare("UPDATE activos SET condicion = 'De Baja' WHERE id = ?");
            $stmt->execute([$activo_id]);
        }

        echo json_encode(["success" => $res]);
        break;
}
