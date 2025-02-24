<?php

class Reporte extends Conectar {
    
    public function get_reportes($usuario_id = null, $bien_id = null, $fecha = null) {
        $conectar = parent::conexion();
        $sql = "SELECT a.id, u.usu_nomape AS usuario, a.sbn AS bien, a.fecha_registro AS fecha 
                FROM activos a
                LEFT JOIN tm_usuario u ON a.responsable_id = u.usu_id
                WHERE 1=1";
    
        $params = [];
        if (!empty($usuario_id)) { $sql .= " AND a.responsable_id = ?"; $params[] = $usuario_id; }
        if (!empty($bien_id)) { $sql .= " AND a.id = ?"; $params[] = $bien_id; }
        if (!empty($fecha)) { $sql .= " AND DATE(a.fecha_registro) = ?"; $params[] = $fecha; }
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
    
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Ver qué devuelve la función
        if (empty($datos)) {
            error_log("🔍 No se encontraron datos en get_reportes()");
        } else {
            error_log("✅ Datos obtenidos: " . json_encode($datos));
        }
    
        return $datos;
    }
    
    
}
?>
