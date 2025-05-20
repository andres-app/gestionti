<?php
require_once("../config/conexion.php");

class Mantenimiento extends Conectar
{
    public function registrar($activo_id, $usuario_id, $fecha, $proveedor, $detalle)
    {
        $conectar = parent::conexion();

        $orden_servicio_path = null;
        $documento_conformidad_path = null;

        // Ruta absoluta en el servidor
       $upload_dir = __DIR__ . "/../assets/document/mantenimientos/";
       
        // Crear carpeta si no existe
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Procesar orden de servicio
        if (isset($_FILES['orden_servicio']) && $_FILES['orden_servicio']['error'] == 0) {
            $ext = pathinfo($_FILES['orden_servicio']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid("orden_") . "." . $ext;
            $orden_servicio_path = "assets/document/mantenimientos/" . $nombre_archivo; // ruta relativa
            move_uploaded_file($_FILES['orden_servicio']['tmp_name'], $upload_dir . $nombre_archivo);
        }

        // Procesar documento de conformidad
        if (isset($_FILES['documento_conformidad']) && $_FILES['documento_conformidad']['error'] == 0) {
            $ext = pathinfo($_FILES['documento_conformidad']['name'], PATHINFO_EXTENSION);
            $nombre_archivo = uniqid("conformidad_") . "." . $ext;
            $documento_conformidad_path = "assets/document/mantenimientos/" . $nombre_archivo; // ruta relativa
            move_uploaded_file($_FILES['documento_conformidad']['tmp_name'], $upload_dir . $nombre_archivo);
        }

        // Guardar en la base de datos
        $sql = "INSERT INTO mantenimientos (activo_id, usuario_id, fecha, proveedor, detalle, orden_servicio, documento_conformidad) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        return $stmt->execute([
            $activo_id,
            $usuario_id,
            $fecha,
            $proveedor,
            $detalle,
            $orden_servicio_path,
            $documento_conformidad_path
        ]);
    }


    public function listar_por_activo($activo_id)
    {
        $conectar = parent::conexion();
        $sql = "SELECT m.*, u.usu_nomape AS usuario 
                FROM mantenimientos m 
                JOIN tm_usuario u ON m.usuario_id = u.usu_id 
                WHERE m.activo_id = ? ORDER BY m.fecha DESC";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
