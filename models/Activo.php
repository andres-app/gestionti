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

        // Consulta para obtener todos los activos con datos de `detactivo`
        $sql = "SELECT v.id, v.sbn, v.serie, v.tipo, v.marca, v.modelo, v.ubicacion, 
                       u.usu_nomape AS responsable, d.hostname, d.procesador, d.sisopera, d.ram, d.disco
                FROM activos v
                LEFT JOIN tm_usuario u ON v.responsable_id = u.usu_id
                LEFT JOIN detactivo d ON v.id = d.activo_id
                WHERE v.estado = 1
                ORDER BY v.id DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_total_activos()
    {
        $conectar = parent::conexion();
        $sql = "SELECT COUNT(*) as total FROM activos WHERE estado = '1' "; // Cambia 'activos' por el nombre correcto de tu tabla
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
    public function editar_vehiculo($id, $sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $hostname, $procesador, $sisopera, $ram, $disco)
    {
        $conectar = parent::conexion();
        parent::set_names();
    
        try {
            // 🔹 Iniciar una transacción para asegurar que ambas tablas se actualizan correctamente
            $conectar->beginTransaction();
    
            // 🔹 Actualizar la tabla `activos`
            $sql1 = "UPDATE activos 
                    SET sbn = ?, serie = ?, tipo = ?, marca = ?, modelo = ?, ubicacion = ?, responsable_id = ?, fecha_registro = ?, 
                        condicion = ?, estado = ?
                    WHERE id = ?";
            $stmt1 = $conectar->prepare($sql1);
            $stmt1->execute([$sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $id]);
    
            // 🔹 Verificar si el `activo_id` ya está en `detactivo`
            $sql2 = "SELECT COUNT(*) FROM detactivo WHERE activo_id = ?";
            $stmt2 = $conectar->prepare($sql2);
            $stmt2->execute([$id]);
            $existe = $stmt2->fetchColumn();
    
            if ($existe) {
                // 🔹 Si existe, actualizar `detactivo`
                $sql3 = "UPDATE detactivo 
                        SET hostname = ?, procesador = ?, sisopera = ?, ram = ?, disco = ?
                        WHERE activo_id = ?";
                $stmt3 = $conectar->prepare($sql3);
                $stmt3->execute([$hostname, $procesador, $sisopera, $ram, $disco, $id]);
            } else {
                // 🔹 Si no existe, insertarlo
                $sql4 = "INSERT INTO detactivo (activo_id, hostname, procesador, sisopera, ram, disco) 
                         VALUES (?, ?, ?, ?, ?, ?)";
                $stmt4 = $conectar->prepare($sql4);
                $stmt4->execute([$id, $hostname, $procesador, $sisopera, $ram, $disco]);
            }
    
            // 🔹 Confirmar la transacción si todo salió bien
            $conectar->commit();
            return true;
        } catch (PDOException $e) {
            // 🔴 Si hay error, hacer rollback y registrar en logs
            $conectar->rollBack();
            error_log("❌ Error en la actualización: " . $e->getMessage());
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
    
        $sql = "SELECT v.id, v.sbn, v.serie, v.tipo, v.marca, v.modelo, v.ubicacion, 
                       v.responsable_id,  -- 🔹 Agrega el ID del responsable
                       u.usu_nomape AS responsable, 
                       v.fecha_registro, v.condicion, v.estado,
                       d.hostname, d.procesador, d.sisopera, d.ram, d.disco
                FROM activos v
                LEFT JOIN tm_usuario u ON v.responsable_id = u.usu_id  -- 🔹 Correcta relación con el usuario
                LEFT JOIN detactivo d ON v.id = d.activo_id
                WHERE v.id = ?";
    
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$id]);
    
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
       
    

    public function get_usuarios()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT usu_id, usu_nomape FROM tm_usuario WHERE estado = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function get_fotos_por_activo($activo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT foto_url FROM fotos_activos WHERE activo_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tipos_activos() {
        $conectar = parent::conexion();
        parent::set_names();
    
        $sql = "SELECT DISTINCT tipo FROM activos WHERE tipo IS NOT NULL AND tipo != ''";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
