<?php
require_once("../config/conexion.php");

class Auditoria extends Conectar
{
    public function registrar_accion($activo_id, $usuario_id, $accion, $detalle)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO auditoria (activo_id, usuario_id, accion, detalle, fecha) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$activo_id, $usuario_id, $accion, $detalle]);
    }

    public function obtener_historial($activo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT a.fecha, a.accion, a.detalle,
                   a.campo_modificado, a.valor_anterior, a.valor_nuevo, a.descripcion,
                   u.usu_nomape AS usuario
            FROM auditoria a
            INNER JOIN tm_usuario u ON a.usuario_id = u.usu_id
            WHERE a.activo_id = ?
            ORDER BY a.fecha DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 🔁 Reemplazar IDs con nombres en ciertos campos
        foreach ($historial as &$item) {
            if ($item["campo_modificado"] === "responsable_id") {
                $item["valor_anterior"] = $this->obtener_nombre_usuario($item["valor_anterior"]);
                $item["valor_nuevo"] = $this->obtener_nombre_usuario($item["valor_nuevo"]);
            }
        }

        return $historial;
    }


    public function registrar_cambio($activo_id, $usuario_id, $accion, $campo, $valor_antiguo, $valor_nuevo, $detalle)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO auditoria (activo_id, usuario_id, accion, campo_modificado, valor_anterior, valor_nuevo, descripcion)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$activo_id, $usuario_id, $accion, $campo, $valor_antiguo, $valor_nuevo, $detalle]);
    }

    public function obtener_nombre_usuario($id)
    {
        if (!$id) return 'Sin asignar';

        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT usu_nomape FROM tm_usuario WHERE usu_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        return $usuario ? $usuario['usu_nomape'] : "ID $id";
    }

public function obtener_todo_historial($filtro_tabla = null)
{
    $conectar = parent::conexion();
    parent::set_names();

    $sql = "SELECT a.*, 
                   u.usu_nomape AS usuario
            FROM auditoria a
            LEFT JOIN tm_usuario u ON a.usuario_id = u.usu_id";
    $params = [];
    if ($filtro_tabla) {
        $sql .= " WHERE a.tabla_afectada = ?";
        $params[] = $filtro_tabla;
    }
    $sql .= " ORDER BY a.fecha DESC";

    $stmt = $conectar->prepare($sql);
    $stmt->execute($params);
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cambiar IDs por nombres de usuario
    foreach ($historial as &$item) {
        if ($item["campo_modificado"] === "responsable_id") {
            $item["valor_anterior"] = $this->obtener_nombre_usuario($item["valor_anterior"]);
            $item["valor_nuevo"]    = $this->obtener_nombre_usuario($item["valor_nuevo"]);
        }
    }
    return $historial;
}

public function obtener_nombre_sede($id)
{
    if (!$id) return 'Sin sede';
    $conectar = parent::conexion();
    parent::set_names();
    $sql = "SELECT sede_nom FROM tm_sede WHERE sede_id = ?";
    $stmt = $conectar->prepare($sql);
    $stmt->execute([$id]);
    $sede = $stmt->fetch(PDO::FETCH_ASSOC);
    return $sede ? $sede['sede_nom'] : "ID $id";
}


    
}
