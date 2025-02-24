<?php

class Reporte extends Conectar {

    public function get_reportes($usuario_id = null, $activo_id = null, $fecha = null) {
        $conectar = parent::conexion();
        
        // Consulta corregida SIN tabla reportes
        $sql = "SELECT a.id, u.usu_nomape AS usuario, a.sbn AS activo, a.fecha_registro AS fecha 
                FROM activos a
                LEFT JOIN tm_usuario u ON a.responsable_id = u.usu_id
                WHERE 1=1"; 

        if (!empty($usuario_id)) {
            $sql .= " AND a.responsable_id = ?";
        }
        if (!empty($activo_id)) {
            $sql .= " AND a.id = ?";
        }
        if (!empty($fecha)) {
            $sql .= " AND DATE(a.fecha_registro) = ?";
        }

        error_log("SQL Ejecutado: " . $sql); // 🛠 Para depuración

        $stmt = $conectar->prepare($sql);
        $params = [];

        if (!empty($usuario_id)) $params[] = $usuario_id;
        if (!empty($activo_id)) $params[] = $activo_id;
        if (!empty($fecha)) $params[] = $fecha;

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}   
