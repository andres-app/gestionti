<?php

class Reporte extends Conectar {
    
    /**
     * Obtener lista de reportes filtrados por usuario, bien y fecha
     * @param int $usuario_id ID del usuario
     * @param int $bien_id ID del bien
     * @param string $fecha Fecha en formato YYYY-MM-DD
     * @return array Lista de reportes
     */
    public function get_reportes($usuario_id = null, $bien_id = null, $fecha = null) {
        $conectar = parent::conexion();
        $sql = "SELECT r.id, u.usu_nomape AS usuario, b.nombre AS bien, r.fecha 
                FROM reportes r
                LEFT JOIN tm_usuario u ON r.usuario_id = u.usu_id
                LEFT JOIN bienes b ON r.bien_id = b.id
                WHERE 1=1";
    
        if (!empty($usuario_id)) {
            $sql .= " AND r.usuario_id = ?";
        }
        if (!empty($bien_id)) {
            $sql .= " AND r.bien_id = ?";
        }
        if (!empty($fecha)) {
            $sql .= " AND DATE(r.fecha) = ?";
        }
    
        var_dump($sql); die(); // 🚨 Esto imprimirá la consulta en pantalla
    
        $stmt = $conectar->prepare($sql);
        $params = [];
    
        if (!empty($usuario_id)) $params[] = $usuario_id;
        if (!empty($bien_id)) $params[] = $bien_id;
        if (!empty($fecha)) $params[] = $fecha;
    
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>
