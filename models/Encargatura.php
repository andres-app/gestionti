<?php
class Encargatura extends Conectar
{
    // Registrar una nueva encargatura
    public function registrar_encargatura($glpi, $titular, $encargado, $fecha_inicio, $fecha_fin, $registrado, $estado, $observaciones)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO encargaturas 
        (glpi, titular, encargado, fecha_inicio, fecha_fin, registrado, estado, observaciones)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conectar->prepare($sql);
        return $stmt->execute([
            $glpi,
            $titular,
            $encargado,
            $fecha_inicio,
            $fecha_fin,
            $registrado,
            $estado,
            $observaciones
        ]);
    }


    // Listar todas las encargaturas por estado
    public function listar_encargaturas($estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
        id,
        glpi,
        titular,
        encargado,
        fecha_inicio,
        fecha_fin,
        registrado,
        estado,
        observaciones
    FROM encargaturas
    WHERE 1 ";

        // Solo filtra si no es 'todos'
        if ($estado !== "todos") {
            $sql .= "AND estado = ? ";
            $stmt = $conectar->prepare($sql . "ORDER BY id DESC");
            $stmt->execute([$estado]);
        } else {
            $stmt = $conectar->prepare($sql . "ORDER BY id DESC");
            $stmt->execute();
        }

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Acciones y formato para la tabla
        foreach ($resultado as &$row) {
            $observacion = !empty($row["observaciones"]) ? $row["observaciones"] : 'Sin observaciones';
            $btnObs = '<button class="btn btn-outline-primary btn-sm" title="Ver observaciones" onclick="verObservaciones(`' . htmlspecialchars($observacion, ENT_QUOTES) . '`)">
                <i class="fas fa-comment-alt"></i>
           </button>';

            if (trim($row["estado"]) === "En Curso") {
                $row["acciones"] = '
        <div class="d-flex flex-row gap-1">
            <button class="btn btn-success btn-sm" onclick="marcarFinalizado(' . $row["id"] . ')">
                <i class="fas fa-undo-alt"></i> Finalizar
            </button>
            ' . $btnObs . '
        </div>';
            } else {
                $row["acciones"] = '
        <div class="d-flex flex-row gap-1">
            <button class="btn btn-secondary btn-sm" disabled>
                <i class="fas fa-check-circle"></i> Finalizado
            </button>
            ' . $btnObs . '
        </div>';
            }
        }

        return $resultado;
    }



    // Marcar encargatura como finalizada
    public function marcar_como_finalizado($encargatura_id, $observaciones = '', $fecha_devolucion_real = null)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE encargaturas 
            SET estado = 'Finalizado',
                fecha_fin = ?,
                observaciones = CONCAT(IFNULL(observaciones, ''), '\nDevolución: ', ?)
            WHERE id = ?";

        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$fecha_devolucion_real, $observaciones, $encargatura_id]);
    }
}
