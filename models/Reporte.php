<?php

class Reporte extends Conectar {

    public function get_reportes($usuario_id = null, $tipo_activo = null, $fecha = null) {
        $conectar = parent::conexion();
        $sql = "SELECT 
                    a.id, 
                    u.usu_nomape AS usuario, 
                    a.sbn, 
                    a.serie, 
                    a.tipo AS tipo_activo, 
                    a.marca, 
                    a.modelo, 
                    a.ubicacion, 
                    d.hostname, 
                    d.procesador, 
                    d.sisopera, 
                    d.ram, 
                    d.disco, 
                    a.fecha_registro AS fecha 
                FROM activos a
                LEFT JOIN tm_usuario u ON a.responsable_id = u.usu_id
                LEFT JOIN detactivo d ON a.id = d.activo_id
                WHERE 1=1";
    
        $params = [];
    
        if (!empty($usuario_id)) {
            $sql .= " AND a.responsable_id = ?";
            $params[] = $usuario_id;
        }
        if (!empty($tipo_activo)) {
            $sql .= " AND a.tipo = ?";
            $params[] = $tipo_activo;
        }
        if (!empty($fecha)) {
            $sql .= " AND DATE(a.fecha_registro) = ?";
            $params[] = $fecha;
        }
    
        error_log("📌 SQL Ejecutado: " . $sql);
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
}   
