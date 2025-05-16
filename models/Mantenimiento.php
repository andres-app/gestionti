<?php
require_once("../config/conexion.php");

class Mantenimiento extends Conectar {
    public function registrar($activo_id, $usuario_id, $fecha, $proveedor, $detalle) {
        $conectar = parent::conexion();
        $sql = "INSERT INTO mantenimientos (activo_id, usuario_id, fecha, proveedor, detalle) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$activo_id, $usuario_id, $fecha, $proveedor, $detalle]);
    }

    public function listar_por_activo($activo_id) {
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
