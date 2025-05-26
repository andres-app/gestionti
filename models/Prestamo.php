<?php
class Prestamo extends Conectar
{
    // Registrar un nuevo préstamo
    public function registrar_prestamo($activo_id, $usuario_origen_id, $usuario_destino_id, $fecha_prestamo, $fecha_devolucion_estimada, $observaciones)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO prestamos (activo_id, usuario_origen_id, usuario_destino_id, fecha_prestamo, fecha_devolucion_estimada, observaciones)
                VALUES (?, ?, ?, ?, ?, ?)";

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
    public function listar_prestamos()
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
                    p.estado
                FROM prestamos p
                INNER JOIN activos a ON p.activo_id = a.id
                INNER JOIN tm_usuario uo ON p.usuario_origen_id = uo.usu_id
                INNER JOIN tm_usuario ud ON p.usuario_destino_id = ud.usu_id
                WHERE a.ubicacion = 'OSIN'
                ORDER BY p.id DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Añadir acciones
        foreach ($resultado as &$row) {
            if ($row["estado"] === "Prestado") {
                $row["acciones"] = '
                    <button class="btn btn-success btn-sm" onclick="marcarDevuelto(' . $row["id"] . ')">
                        <i class="fas fa-undo-alt"></i> Devolver
                    </button>';
            } else {
                $row["acciones"] = '<span class="badge bg-secondary">Devuelto</span>';
            }
        }

        return $resultado;
    }

    public function marcar_como_devuelto($prestamo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE prestamos 
            SET estado = 'Devuelto', fecha_devolucion_real = NOW() 
            WHERE id = ?";

        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$prestamo_id]);
    }
}
