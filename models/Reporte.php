<?php

class Reporte extends Conectar {

    public function get_reportes($usuario_id = null, $activo_id = null, $fecha = null) {
        $conectar = parent::conexion();
        $sql = "SELECT r.id, u.usu_nomape AS usuario, a.sbn AS activo, r.fecha_registro AS fecha 
                FROM activos r
                LEFT JOIN tm_usuario u ON r.responsable_id = u.usu_id
                LEFT JOIN activos a ON r.id = a.id
                WHERE 1=1";
    
        $params = [];
    
        if (!empty($usuario_id)) {
            $sql .= " AND r.responsable_id = ?";
            $params[] = $usuario_id;
        }
        if (!empty($activo_id)) {
            $sql .= " AND r.id = ?";
            $params[] = $activo_id;
        }
        if (!empty($fecha)) {
            $sql .= " AND DATE(r.fecha_registro) = ?";
            $params[] = $fecha;
        }
    
        error_log("📌 SQL Ejecutado: " . $sql);
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$result) {
            error_log("⚠️ No se encontraron reportes en la BD con los filtros aplicados.");
        }
    
        return $result;
    }
    
}   
