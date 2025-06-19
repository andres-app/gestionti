<?php

class Reporte extends Conectar {

public function get_reportes($usuario_id = null, $tipo_activo = null, $fecha = null, $obsolescencia = null, $garantia = null, $condicion = null) {
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
                a.fecha_registro AS fecha,
                a.acompra,
                a.sede,
                a.condicion,
                a.observaciones
            FROM activos a
            LEFT JOIN tm_usuario u ON a.responsable_id = u.usu_id
            LEFT JOIN detactivo d ON a.id = d.activo_id
            WHERE a.estado = 1";

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
        $sql .= " AND YEAR(a.fecha_registro) = ?";
        $params[] = $fecha;
    }

    if ($obsolescencia === "obsoleto") {
        $sql .= " AND (YEAR(CURDATE()) - a.acompra) >= 5";
    } elseif ($obsolescencia === "vigente") {
        $sql .= " AND (YEAR(CURDATE()) - a.acompra) < 5";
    }

    if ($garantia === "sin") {
        $sql .= " AND (YEAR(CURDATE()) - a.acompra) >= 3";
    } elseif ($garantia === "con") {
        $sql .= " AND (YEAR(CURDATE()) - a.acompra) < 3";
    }

    if (!empty($condicion)) {
        $sql .= " AND a.condicion = ?";
        $params[] = $condicion;
    }

    $stmt = $conectar->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
