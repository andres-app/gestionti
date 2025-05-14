<?php
class Baja extends Conectar {
    public function registrar_baja($activo_id, $motivo, $archivo) {
        $conectar = parent::conexion();
        parent::set_names();

        $stmt = $conectar->prepare("INSERT INTO bajas (activo_id, motivo, documento) VALUES (?, ?, ?)");
        return $stmt->execute([$activo_id, $motivo, $archivo]);
    }
}
