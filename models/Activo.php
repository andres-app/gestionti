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
    public function get_activos($condicion = '')
    {
        $conectar = parent::conexion();

        $sql = "SELECT a.*, 
               u.usu_nomape AS responsable, 
               ta.area_nom AS ubicacion_nombre, 
               s.sede_nom AS sede_nombre, 
               EXISTS (SELECT 1 FROM bajas b WHERE b.activo_id = a.id) AS tiene_baja 
        FROM activos a 
        LEFT JOIN tm_usuario u ON a.responsable_id = u.usu_id 
        LEFT JOIN tm_area ta ON a.ubicacion = ta.area_id 
        LEFT JOIN tm_sede s ON a.sede_id = s.sede_id
        WHERE a.estado = 1";


        // Filtro adicional
        if ($condicion === "activo") {
            $sql .= " AND NOT EXISTS (SELECT 1 FROM bajas b WHERE b.activo_id = a.id)";
        } elseif ($condicion === "baja") {
            $sql .= " AND EXISTS (SELECT 1 FROM bajas b WHERE b.activo_id = a.id)";
        }

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
    // Insertar (cambia $sede por $sede_id)
    public function insertar_vehiculo($sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $sede_id, $responsable_id, $fecha_registro, $condicion, $estado, $observaciones, $acompra)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO activos (sbn, serie, tipo, marca, modelo, ubicacion, sede_id, responsable_id, fecha_registro, condicion, estado, observaciones, acompra)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conectar->prepare($sql);

        if ($stmt->execute([$sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $sede_id, $responsable_id, $fecha_registro, $condicion, $estado, $observaciones, $acompra])) {
            return $conectar->lastInsertId(); // Retornar ID insertado
        } else {
            $error = $stmt->errorInfo();
            error_log("Error en la consulta: " . $error[2]);
            return false;
        }
    }




    /**
     * Método para actualizar los datos de un vehículo.
     */
    public function editar_vehiculo($id, $sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $hostname, $procesador, $sisopera, $ram, $disco, $sede, $observaciones, $acompra)
    {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            // 🔹 Iniciar una transacción para asegurar que ambas tablas se actualizan correctamente
            $conectar->beginTransaction();

            // 🔹 Actualizar la tabla `activos`
            $sql1 = "UPDATE activos 
    SET sbn = ?, serie = ?, tipo = ?, marca = ?, modelo = ?, ubicacion = ?, responsable_id = ?, fecha_registro = ?, 
        condicion = ?, estado = ?, sede_id = ?, observaciones = ?, acompra = ?
    WHERE id = ?";
            $stmt1 = $conectar->prepare($sql1);
            $stmt1->execute([$sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $sede, $observaciones, $acompra, $id]);

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
            v.responsable_id, u.usu_nomape AS responsable, 
            v.fecha_registro, v.condicion, v.estado,
            v.sede_id, s.sede_nom AS sede, -- <-- OJO: sede_id y el nombre con JOIN
            v.observaciones, v.acompra,
            d.hostname, d.procesador, d.sisopera, d.ram, d.disco,
            a.area_nom AS ubicacion_nombre
                FROM activos v
                LEFT JOIN tm_usuario u ON v.responsable_id = u.usu_id
                LEFT JOIN detactivo d ON v.id = d.activo_id
                LEFT JOIN tm_area a ON v.ubicacion = a.area_id
                LEFT JOIN tm_sede s ON v.sede_id = s.sede_id
                WHERE v.id = ?";


        $stmt = $conectar->prepare($sql);
        $stmt->execute([$id]);

        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        // 🔹 Aquí agregas la última fecha real de mantenimiento
        $ultimo_mantenimiento = $this->get_ultimo_mantenimiento($id);
        $datos['ult_mant_real'] = $ultimo_mantenimiento;

        return $datos;
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

    public function get_tipos_activos()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT DISTINCT tipo FROM activos WHERE tipo IS NOT NULL AND tipo != ''";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_activos_por_estado()
    {
        $conectar = parent::conexion();
        $sql = "SELECT 
                    CASE estado
                        WHEN 1 THEN 'Activo'
                        WHEN 0 THEN 'Inactivo'
                        ELSE 'Otro'
                    END AS estado_nombre,
                    COUNT(*) AS total
                FROM activos
                GROUP BY estado";

        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_activos_por_tipo()
    {
        $conectar = parent::conexion();
        $sql = "SELECT tipo, COUNT(*) AS total
                FROM activos
                WHERE tipo IS NOT NULL AND tipo != ''
                GROUP BY tipo";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_activos_por_sede()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT s.sede_nom AS sede, COUNT(*) AS total 
            FROM activos a
            LEFT JOIN tm_sede s ON a.sede_id = s.sede_id
            WHERE a.sede_id IS NOT NULL 
            GROUP BY a.sede_id
            ORDER BY s.sede_nom";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function get_activos_por_condicion()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
            CASE 
                WHEN EXISTS (SELECT 1 FROM bajas b WHERE b.activo_id = a.id) THEN 'De Baja'
                ELSE a.condicion
            END AS condicion,
            COUNT(*) AS total
        FROM activos a
        WHERE a.estado = 1
        GROUP BY condicion";


        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_obsolescencia_garantia()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN YEAR(CURDATE()) - acompra >= 5 THEN 1 ELSE 0 END) AS obsoletos,
                    SUM(CASE WHEN YEAR(CURDATE()) - acompra < 5 THEN 1 ELSE 0 END) AS vigentes_tecnologicamente,
                    SUM(CASE WHEN YEAR(CURDATE()) - acompra >= 3 THEN 1 ELSE 0 END) AS fuera_garantia,
                    SUM(CASE WHEN YEAR(CURDATE()) - acompra < 3 THEN 1 ELSE 0 END) AS con_garantia
                FROM activos
                WHERE estado = 1 AND acompra IS NOT NULL AND acompra > 1990";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function tiene_baja($activo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT COUNT(*) FROM bajas WHERE activo_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function registrar_auditoria($activo_id, $usuario_id, $accion, $descripcion)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "INSERT INTO auditoria (activo_id, usuario_id, accion, descripcion)
            VALUES (?, ?, ?, ?)";
        $stmt = $conectar->prepare($sql);
        return $stmt->execute([$activo_id, $usuario_id, $accion, $descripcion]);
    }

    public function get_ultimo_mantenimiento($activo_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT fecha 
            FROM mantenimientos 
            WHERE activo_id = ? 
            ORDER BY fecha DESC 
            LIMIT 1";

        $stmt = $conectar->prepare($sql);
        $stmt->execute([$activo_id]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['fecha'] : null;
    }

    public function get_activos_prox_mantenimiento()
    {
        $conectar = parent::conexion();

        $sql = "
        SELECT COUNT(*) as total FROM activos a
        LEFT JOIN (
            SELECT activo_id, MAX(fecha) as ultima_fecha
            FROM mantenimientos
            GROUP BY activo_id
        ) m ON a.id = m.activo_id
        WHERE (
            (m.ultima_fecha IS NULL AND YEAR(NOW()) - acompra >= 1)
            OR
            (m.ultima_fecha IS NOT NULL AND DATEDIFF(NOW(), m.ultima_fecha) >= 365)
        ) AND a.estado = 1
    ";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_activos_disponibles_osin()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT a.id, a.sbn, a.serie, a.tipo
            FROM activos a
            WHERE a.estado = 1
              AND a.ubicacion = 'OSIN'
              AND NOT EXISTS (
                  SELECT 1 FROM prestamos p
                  WHERE p.activo_id = a.id
                  AND p.estado = 'Prestado'
              )
            ORDER BY a.tipo, a.sbn";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarAreas()
    {
        $conectar = parent::conexion();
        $sql = "SELECT area_id, area_nom FROM tm_area";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_sedes()
    {
        $conectar = parent::conexion();
        $sql = "SELECT sede_id, sede_nom FROM tm_sede WHERE est = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
