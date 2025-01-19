<?php
// Clase Vehiculo que extiende de la clase Conectar, para manejar operaciones con la base de datos.
class Vehiculo extends Conectar
{
    /**
     * Método para obtener todos los vehículos registrados en la base de datos.
     * 
     * @return array Listado de vehículos con los siguientes datos:
     *               - id: Identificador del vehículo.
     *               - placa: Número de placa del vehículo.
     *               - marca: Marca del vehículo.
     *               - modelo: Modelo del vehículo.
     *               - anio: Año de fabricación del vehículo.
     *               - color: Color del vehículo.
     *               - motor: Tipo de motor del vehículo.
     *               - combustible: Tipo de combustible (Gasolina, Diesel, etc.).
     *               - tipo_vehiculo: Tipo de vehículo (Camioneta, Sedán, etc.).
     *               - poliza: Número de póliza de seguro del vehículo.
     *               - estado: Estado del vehículo (Activo o Inactivo).
     */
    public function get_vehiculos()
    {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Consulta para obtener los vehículos junto con sus mantenimientos
        $sql = "SELECT v.id, v.placa, v.marca, v.modelo, v.anio, 
                       m.fecha_mantenimiento, m.fecha_proximo_mantenimiento
                FROM vehiculos v
                LEFT JOIN mantenimiento m ON v.id = m.vehiculo_id
                WHERE v.estado = 1
                ORDER BY v.id DESC";
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_total_vehiculos() {
        $conectar = parent::conexion();
        $sql = "SELECT COUNT(*) as total FROM vehiculos"; // Cambia 'vehiculos' por el nombre correcto de tu tabla
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return $result['total']; // Retorna solo el valor total
    }    
    
    /**
     * Método para insertar un nuevo vehículo en la base de datos.
     * 
     * @param string $placa Placa del vehículo.
     * @param string $marca Marca del vehículo.
     * @param string $modelo Modelo del vehículo.
     * @param int $anio Año de fabricación del vehículo.
     * @param string $color Color del vehículo.
     * @param string $motor Tipo de motor del vehículo.
     * @param string $combustible Tipo de combustible (Gasolina, Diesel, etc.).
     * @param string $tipo_vehiculo Tipo de vehículo.
     * @param string $poliza Número de póliza del vehículo.
     * @param int $estado Estado del vehículo (Activo o Inactivo).
     * 
     * @return bool True si la inserción fue exitosa, false en caso de error.
     */
    public function insertar_vehiculo($placa, $marca, $modelo, $anio, $color, $motor, $combustible, $tipo_vehiculo, $poliza, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Consulta SQL para insertar un nuevo vehículo en la base de datos
        $sql = "INSERT INTO vehiculos (placa, marca, modelo, anio, color, motor, combustible, tipo_vehiculo, poliza, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conectar->prepare($sql);

        if ($stmt->execute([$placa, $marca, $modelo, $anio, $color, $motor, $combustible, $tipo_vehiculo, $poliza, $estado])) {
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
    public function editar_vehiculo($id, $placa, $marca, $modelo, $anio, $color, $motor, $combustible, $tipo_vehiculo, $poliza, $estado)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "UPDATE vehiculos 
                SET placa = ?, marca = ?, modelo = ?, anio = ?, color = ?, motor = ?, combustible = ?, tipo_vehiculo = ?, poliza = ?, estado = ?
                WHERE id = ?";

        $stmt = $conectar->prepare($sql);

        try {
            $stmt->execute([$placa, $marca, $modelo, $anio, $color, $motor, $combustible, $tipo_vehiculo, $poliza, $estado, $id]);
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

        $sql = "SELECT * FROM vehiculos WHERE id = ?";
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

        $sql = "UPDATE vehiculos SET estado = ? WHERE id = ?";
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
    
        $sql = "SELECT v.id, v.placa, v.marca, v.modelo, v.anio, m.fecha_proximo_mantenimiento,
                       CASE 
                           WHEN m.fecha_proximo_mantenimiento < CURDATE() THEN 'Vencido'
                           ELSE 'Próximo'
                       END AS estado_mantenimiento
                FROM vehiculos v
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
     * Método para marcar un mantenimiento como vencido si no se ha realizado.
     */
    public function marcar_mantenimiento_vencido($vehiculo_id)
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
            error_log("Error al marcar el mantenimiento como vencido: " . $e->getMessage());
            return false;
        }
    }
}
