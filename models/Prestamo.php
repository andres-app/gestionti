<?php
class Prestamo extends Conectar
{
    // Registrar un nuevo préstamo
    public function registrar_prestamo($activo_id, $usuario_origen_id, $usuario_destino_id, $fecha_prestamo, $fecha_devolucion_estimada, $observaciones)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO prestamos (activo_id, usuario_origen_id, usuario_destino_id, fecha_prestamo, fecha_devolucion_estimada, observaciones, estado)
            VALUES (?, ?, ?, ?, ?, ?, 'Prestado')";

        $stmt = $conectar->prepare($sql);
        return $stmt->execute([
            $activo_id,
            $usuario_origen_id,
            $usuario_destino_id,
            $fecha_prestamo,
            $fecha_devolucion_estimada,
            $observaciones
        ]);
    }



    // Listar todos los préstamos
    public function listar_prestamos($estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
                p.id,
                a.sbn AS activo,
                uo.usu_nomape AS origen,
                ud.usu_nomape AS destino,
                p.fecha_prestamo,
                p.fecha_devolucion_estimada,
                p.fecha_devolucion_real,  -- ✅ Nuevo campo
                p.estado,
                p.observaciones
            FROM prestamos p
            INNER JOIN activos a ON p.activo_id = a.id
            INNER JOIN tm_usuario uo ON p.usuario_origen_id = uo.usu_id
            INNER JOIN tm_usuario ud ON p.usuario_destino_id = ud.usu_id
            WHERE a.ubicacion = 'OSIN'";

        if ($estado !== "todos") {
            $sql .= " AND p.estado = ? ORDER BY p.id DESC";
            $stmt = $conectar->prepare($sql);
            $stmt->execute([$estado]);
        } else {
            $sql .= " ORDER BY p.id DESC";
            $stmt = $conectar->prepare($sql);
            $stmt->execute();
        }

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Preparar acciones para la tabla
        foreach ($resultado as &$row) {
            $observacion = !empty($row["observaciones"]) ? $row["observaciones"] : 'Sin observaciones';
            $btnObs = '<button class="btn btn-outline-primary btn-sm" title="Ver observaciones" onclick="verObservaciones(`' . htmlspecialchars($observacion, ENT_QUOTES) . '`)">
                        <i class="fas fa-comment-alt"></i>
                   </button>';

            if (trim($row["estado"]) === "Prestado") {
                $row["acciones"] = '
                <div class="d-flex flex-row gap-1">
                    <button class="btn btn-success btn-sm" onclick="marcarDevuelto(' . $row["id"] . ')">
                        <i class="fas fa-undo-alt"></i> Devolver
                    </button>
                    ' . $btnObs . '
                </div>';
            } else {
                $row["acciones"] = '
                <div class="d-flex flex-row gap-1">
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-check-circle"></i> Devuelto
                    </button>
                    ' . $btnObs . '
                </div>';
            }
        }

        return $resultado;
    }



    public function marcar_como_devuelto($prestamo_id, $observaciones = '', $fecha_devolucion_real = null)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE prestamos 
            SET estado = 'Devuelto',
                fecha_devolucion_real = ?,
                observaciones = CONCAT(IFNULL(observaciones, ''), '\nDevolución: ', ?)
            WHERE id = ?";

        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$fecha_devolucion_real, $observaciones, $prestamo_id]);
    }
}
