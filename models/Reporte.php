<?php

class Reporte extends Conectar
{

    public function get_reportes($usuario_id = null, $tipo_activo = null, $fecha = null, $obsolescencia = null, $garantia = null, $condicion = null, $ubicacion = null)
    {
        $conectar = parent::conexion();

        $sql = "SELECT 
    a.id, 
    u.usu_nomape AS usuario, 
    a.sbn, 
    a.serie, 
    a.tipo AS tipo_activo, 
    a.marca, 
    a.modelo, 
    ar.area_nom AS ubicacion,
    a.ubicacion AS ubicacion_id,  
    d.hostname, 
    d.procesador, 
    d.sisopera, 
    d.ram, 
    d.disco, 
    a.fecha_registro AS fecha,
    a.acompra,
    s.sede_nom as sede,          -- <-- Agrega esto
    a.sede_id,                  -- (opcional, solo si necesitas el id)
    a.condicion,
    a.observaciones
FROM activos a
LEFT JOIN tm_usuario u ON a.responsable_id = u.usu_id
LEFT JOIN detactivo d ON a.id = d.activo_id
LEFT JOIN tm_area ar ON a.ubicacion = ar.area_id
LEFT JOIN tm_sede s ON a.sede_id = s.sede_id    -- <-- Nuevo JOIN
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

        if (!empty($ubicacion)) {
            $sql .= " AND a.ubicacion = ?";
            $params[] = $ubicacion;
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

    public function get_ubicaciones()
    {
        $conectar = parent::conexion();

        $sql = "SELECT area_id, area_nom 
            FROM tm_area 
            WHERE area_nom IS NOT NULL AND area_nom != '' 
            ORDER BY area_nom";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
