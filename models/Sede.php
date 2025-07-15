<?php
class Sede extends Conectar
{
    public function get_sede() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_sede WHERE est = 1 ORDER BY sede_id DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sede_x_id($sede_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_sede WHERE sede_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sede_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sede_nombre($sede_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_sede WHERE sede_nom = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sede_nom);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_sede($sede_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_sede (sede_nom) VALUES (?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sede_nom);
        $sql->execute();
    }

    public function update_sede($sede_id, $sede_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_sede SET sede_nom = ?, fech_modi = NOW() WHERE sede_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sede_nom);
        $sql->bindValue(2, $sede_id);
        $sql->execute();
    }

    public function deshabilitar_sede($sede_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_sede SET est = 0, fech_elim = NOW() WHERE sede_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $sede_id);
        $sql->execute();
    }
}
