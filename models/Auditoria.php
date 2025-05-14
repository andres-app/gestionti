<?php
require_once("../config/conexion.php");

class Auditoria extends Conectar
{
    public function registrar_accion($activo_id, $usuario_id, $accion, $detalle)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO auditoria (activo_id, usuario_id, accion, detalle, fecha) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$activo_id, $usuario_id, $accion, $detalle]);
    }

    public function obtener_historial($activo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT a.fecha, a.accion, a.detalle, u.usu_nomape AS usuario
            FROM auditoria a
            INNER JOIN tm_usuario u ON a.usuario_id = u.usu_id
            WHERE a.activo_id = ?
            ORDER BY a.fecha DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
