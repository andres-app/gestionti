<?php
// Clase Activo que extiende de la clase Conectar, para manejar operaciones con la base de datos.
class Activo extends Conectar
{
    /**
     * Método para obtener todos los vehículos registrados en la base de datos.
     * 
     * @return array Listado de vehículos con los siguientes datos:
     *               - id: Identificador del vehículo.
     *               - sbn: Número de sbn del vehículo.
     *               - serie: serie del vehículo.
     *               - tipo: tipo del vehículo.
     *               - marca: Año de fabricación del vehículo.
     *               - modelo: modelo del vehículo.
     *               - ubicacion: Tipo de ubicacion del vehículo.
     *               - responsable_id: Tipo de responsable_id (Gasolina, Diesel, etc.).
     *               - fecha_registro: Tipo de vehículo (Camioneta, Sedán, etc.).
     *               - condicion: Número de póliza de seguro del vehículo.
     *               - estado: Estado del vehículo (Activo o Inactivo).
     */
    public function get_activos()
    {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Consulta para obtener los vehículos junto con sus mantenimientos
        $sql = "SELECT v.id, v.sbn, v.serie, v.tipo, v.marca, 
                       m.fecha_mantenimiento, m.fecha_proximo_mantenimiento, v.modelo, v.ubicacion, v.responsable_id
                FROM activos v
                LEFT JOIN mantenimiento m ON v.id = m.vehiculo_id
                WHERE v.estado = 1
                ORDER BY v.id DESC";
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_total_activos() {
        $conectar = parent::conexion();
        $sql = "SELECT COUNT(*) as total FROM activos"; // Cambia 'activos' por el nombre correcto de tu tabla
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['total']; // Retorna solo el valor total
    }    
    
    /**
     * Método para insertar un nuevo vehículo en la base de datos.
     * 
     * @param string $sbn sbn del vehículo.
     * @param string $serie serie del vehículo.
     * @param string $tipo tipo del vehículo.
     * @param int $marca Año de fabricación del vehículo.
     * @param string $modelo modelo del vehículo.
     * @param string $ubicacion Tipo de ubicacion del vehículo.
     * @param string $responsable_id Tipo de responsable_id (Gasolina, Diesel, etc.).
     * @param string $fecha_registro Tipo de vehículo.
     * @param string $condicion Número de póliza del vehículo.
     * @param int $estado Estado del vehículo (Activo o Inactivo).
     * 
     * @return bool True si la inserción fue exitosa, false en caso de error.
     */
    public function insertar_vehiculo($sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Consulta SQL para insertar un nuevo vehículo en la base de datos
        $sql = "INSERT INTO activos (sbn, serie, tipo, marca, modelo, ubicacion, responsable_id, fecha_registro, condicion, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conectar->prepare($sql);

        if ($stmt->execute([$sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado])) {
            return true;
        } else {
            $error = $stmt->errorInfo();
            error_log("Error en la consulta: " . $error[2]);
            return false;
        }
    }

    /**
     * Método para actualizar los datos de un vehículo.
     */
    public function editar_vehiculo($id, $sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE activos 
                SET sbn = ?, serie = ?, tipo = ?, marca = ?, modelo = ?, ubicacion = ?, responsable_id = ?, fecha_registro = ?, condicion = ?, estado = ?
                WHERE id = ?";

        $stmt = $conectar->prepare($sql);

        try {
            $stmt->execute([$sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error en la consulta de actualización: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Método para obtener los detalles de un vehículo por su ID.
     */
    public function get_vehiculo_por_id($id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM activos WHERE id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Método para cambiar el estado de un vehículo.
     */
    public function cambiar_estado($id, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE activos SET estado = ? WHERE id = ?";
        $stmt = $conectar->prepare($sql);

        try {
            $stmt->execute([$estado, $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al cambiar el estado del vehículo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Método para obtener los próximos mantenimientos de la tabla mantenimiento.
     */
    public function get_proximos_mantenimientos()
    {
        $conectar = parent::conexion();
        parent::set_names();
    
        $sql = "SELECT v.id, v.sbn, v.serie, v.tipo, v.marca, m.fecha_proximo_mantenimiento,
                       CASE 
                           WHEN m.fecha_proximo_mantenimiento < CURDATE() THEN 'Vencido'
                           ELSE 'Próximo'
                       END AS estado_mantenimiento
                FROM activos v
                JOIN mantenimiento m ON v.id = m.vehiculo_id
                WHERE v.estado = 1 
                AND m.realizado = 0
                ORDER BY m.fecha_proximo_mantenimiento ASC";
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    /**
     * Método para insertar un nuevo registro de mantenimiento.
     */
    public function insertar_mantenimiento($vehiculo_id, $fecha_mantenimiento, $kilometraje, $precio, $detalle, $repuestos)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO mantenimiento (id_vehiculo, fecha_mantenimiento, kilometraje, precio, observaciones, repuestos, estado) 
                VALUES (?, ?, ?, ?, ?, ?, 'realizado')";

        $stmt = $conectar->prepare($sql);

        if ($stmt->execute([$vehiculo_id, $fecha_mantenimiento, $kilometraje, $precio, $detalle, $repuestos])) {
            return true;
        } else {
            $error = $stmt->errorInfo();
            error_log("Error en la consulta de mantenimiento: " . $error[2]);
            return false;
        }
    }

    /**
     * Método para serier un mantenimiento como vencido si no se ha realizado.
     */
    public function serier_mantenimiento_vencido($vehiculo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE mantenimiento 
                SET realizado = 0
                WHERE id_vehiculo = ? 
                AND fecha_mantenimiento < CURDATE()";

        $stmt = $conectar->prepare($sql);

        try {
            $stmt->execute([$vehiculo_id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al serier el mantenimiento como vencido: " . $e->getMessage());
            return false;
        }
    }

    public function get_fotos_por_activo($activo_id) {
        $conectar = parent::conexion();
        parent::set_names();
    
        $sql = "SELECT foto_url FROM fotos_activos WHERE activo_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
