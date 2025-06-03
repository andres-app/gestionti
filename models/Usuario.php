<?php
/* TODO: Definición de la clase Usuario que extiende la clase Conectar */
class Usuario extends Conectar
{

    private $key = "MesaDePartesAnderCode";
    private $cipher = "aes-256-cbc";

    public function login()
    {
        $conectar = parent::conexion();
        parent::set_names();
        if (isset($_POST["enviar"])) {
            $correo = $_POST["usu_correo"];
            $pass = $_POST["usu_pass"];
            if (empty($correo) and empty($pass)) {
                header("Location:" . conectar::ruta() . "index.php?m=2");
                exit();
            } else {
                $sql = "SELECT * FROM tm_usuario WHERE usu_correo = ? AND rol_id = 1";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $correo);
                $sql->execute();
                $resultado = $sql->fetch();
                if ($resultado) {
                    $textoCifrado = $resultado["usu_pass"];

                    $iv_dec = substr(base64_decode($textoCifrado), 0, openssl_cipher_iv_length($this->cipher));
                    $cifradoSinIV = substr(base64_decode($textoCifrado), openssl_cipher_iv_length($this->cipher));
                    $textoDecifrado = openssl_decrypt($cifradoSinIV, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv_dec);

                    if ($textoDecifrado == $pass) {
                        if (is_array($resultado) and count($resultado) > 0) {
                            $_SESSION["usu_id"] = $resultado["usu_id"];
                            $_SESSION["usu_nomape"] = $resultado["usu_nomape"];
                            $_SESSION["usu_correo"] = $resultado["usu_correo"];
                            $_SESSION["usu_img"] = $resultado["usu_img"];
                            $_SESSION["rol_id"] = $resultado["rol_id"];
                            header("Location:" . Conectar::ruta() . "view/Home/");
                            exit();
                        }
                    } else {
                        header("Location:" . Conectar::ruta() . "index.php?m=3");
                        exit();
                    }
                } else {
                    header("Location:" . Conectar::ruta() . "index.php?m=1");
                    exit();
                }
            }
        }
    }

    public function login_colaborador()
    {
        $conectar = parent::conexion();
        parent::set_names();
        if (isset($_POST["enviar"])) {
            $correo = $_POST["usu_correo"];
            $pass = $_POST["usu_pass"];

            if (empty($correo) || empty($pass)) {
                header("Location:" . conectar::ruta() . "view/accesopersonal/index.php?m=2");
                exit();
            } else {
                $sql = "SELECT * FROM tm_usuario WHERE usu_correo = ? AND rol_id IN (2,3,4,5)";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $correo);
                $sql->execute();
                $resultado = $sql->fetch();

                if ($resultado) {
                    // Usar password_verify para verificar la contraseña ingresada con la almacenada en la base de datos
                    if (password_verify($pass, $resultado["usu_pass"])) {
                        if (is_array($resultado) && count($resultado) > 0) {
                            $_SESSION["usu_id"] = $resultado["usu_id"];
                            $_SESSION["usu_nomape"] = $resultado["usu_nomape"];
                            $_SESSION["usu_correo"] = $resultado["usu_correo"];
                            $_SESSION["usu_img"] = $resultado["usu_img"];
                            $_SESSION["rol_id"] = $resultado["rol_id"];
                            header("Location:" . Conectar::ruta() . "view/homecolaborador/");
                            exit();
                        }
                    } else {
                        // Contraseña incorrecta
                        header("Location:" . Conectar::ruta() . "view/accesopersonal/index.php?m=3");
                        exit();
                    }
                } else {
                    // Usuario no encontrado
                    header("Location:" . Conectar::ruta() . "view/accesopersonal/index.php?m=1");
                    exit();
                }
            }
        }
    }


    /* TODO: Método para registrar un nuevo usuario en la base de datos */
    public function registrar_usuario($usu_nomape, $usu_correo, $usu_pass, $usu_img, $est)
    {

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $cifrado = openssl_encrypt($usu_pass, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        $textoCifrado = base64_encode($iv . $cifrado);

        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "INSERT INTO tm_usuario
                (usu_nomape,usu_correo,usu_pass,usu_img,rol_id,est)
                VALUES
                (?,?,?,?,1,?)";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $usu_nomape);
        $sql->bindValue(2, $usu_correo);
        $sql->bindValue(3, $textoCifrado);
        $sql->bindValue(4, $usu_img);
        $sql->bindValue(5, $est);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();

        $sql1 = "select last_insert_id() as 'usu_id'";
        $sql1 = $conectar->prepare($sql1);
        $sql1->execute();
        return $sql1->fetchAll();
    }

    public function get_usuarios()
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Consulta para obtener usuarios activos
        $sql = "SELECT usu_id, usu_nomape FROM tm_usuario WHERE est = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_usuario_correo($usu_correo)
    {
        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "SELECT * FROM tm_usuario
                WHERE usu_correo = ?";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $usu_correo);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();
        return $sql->fetchAll();
    }

    public function get_usuario_id($usu_id)
    {
        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "SELECT * FROM tm_usuario
                WHERE usu_id = ?";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $usu_id);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();
        return $sql->fetchAll();
    }

    public function activar_usuario($usu_id)
    {

        $iv_dec = substr(base64_decode($usu_id), 0, openssl_cipher_iv_length($this->cipher));
        $cifradoSinIV = substr(base64_decode($usu_id), openssl_cipher_iv_length($this->cipher));
        $textoDecifrado = openssl_decrypt($cifradoSinIV, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv_dec);

        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "UPDATE tm_usuario
                    SET
                        est=1,
                        fech_acti = NOW()
                    WHERE
                        usu_id = ?";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $textoDecifrado);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();
    }

    public function recuperar_usuario($usu_correo, $usu_pass)
    {

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $cifrado = openssl_encrypt($usu_pass, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        $textoCifrado = base64_encode($iv . $cifrado);

        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "UPDATE tm_usuario
                SET
                usu_pass = ?
                WHERE
                usu_correo = ?";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $textoCifrado);
        $sql->bindValue(2, $usu_correo);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();
    }

    public function insert_colaborador($usu_nomape, $usu_correo, $usu_pass, $area_id, $rol_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        if (!empty($usu_pass)) {
            $hashedPassword = password_hash($usu_pass, PASSWORD_BCRYPT);
        } else {
            return "La contraseña no puede estar vacía";
        }

        $sql = "INSERT INTO tm_usuario
            (usu_nomape, usu_correo, usu_pass, usu_img, area_id, rol_id, est)
            VALUES (?, ?, ?, '../../assets/picture/avatar.png', ?, ?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_nomape);
        $sql->bindValue(2, $usu_correo);
        $sql->bindValue(3, $hashedPassword);
        $sql->bindValue(4, $area_id);  // Nuevo parámetro
        $sql->bindValue(5, $rol_id);
        $sql->execute();

        $sql1 = "SELECT last_insert_id() as 'usu_id'";
        $sql1 = $conectar->prepare($sql1);
        $sql1->execute();
        return $sql1->fetchAll();
    }




    public function get_colaborador()
    {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
            u.usu_id,
            u.usu_nomape,
            u.usu_correo,
            u.rol_id,
            r.rol_nom,
            a.area_nom,
            u.fech_crea
            FROM tm_usuario u
            INNER JOIN tm_rol r ON u.rol_id = r.rol_id
            LEFT JOIN tm_area a ON u.area_id = a.area_id
            WHERE u.est = 1
            AND u.rol_id IN (2,3,4,5)
            ORDER BY u.usu_nomape";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_colaborador($usu_id, $usu_nomape, $usu_correo, $usu_pass, $area_id, $rol_id)
    {
        $conectar = parent::conexion();
        parent::set_names();

        if (!empty($usu_pass)) {
            $hashedPassword = password_hash($usu_pass, PASSWORD_BCRYPT);

            $sql = "UPDATE tm_usuario
              SET usu_nomape = ?, usu_correo = ?, usu_pass = ?, area_id = ?, rol_id = ?, fech_modi = NOW()
              WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nomape);
            $sql->bindValue(2, $usu_correo);
            $sql->bindValue(3, $hashedPassword);
            $sql->bindValue(4, $area_id);  // Nuevo parámetro
            $sql->bindValue(5, $rol_id);
            $sql->bindValue(6, $usu_id);
        } else {
            $sql = "UPDATE tm_usuario
              SET usu_nomape = ?, usu_correo = ?, area_id = ?, rol_id = ?, fech_modi = NOW()
              WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nomape);
            $sql->bindValue(2, $usu_correo);
            $sql->bindValue(3, $area_id);  // Nuevo parámetro
            $sql->bindValue(4, $rol_id);
            $sql->bindValue(5, $usu_id);
        }

        $sql->execute();
    }


    public function eliminar_colaborador($usu_id)
    {
        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "UPDATE tm_usuario
                    SET
                        est = 0,
                        fech_elim = NOW()
                    WHERE
                        usu_id = ?";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $usu_id);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();
    }

    public function get_usuario_permiso_area($usu_id)
    {
        /* TODO: Obtener la conexión a la base de datos utilizando el método de la clase padre */
        $conectar = parent::conexion();
        /* TODO: Establecer el juego de caracteres a UTF-8 utilizando el método de la clase padre */
        parent::set_names();
        /* TODO: Consulta SQL para insertar un nuevo usuario en la tabla tm_usuario */
        $sql = "SELECT 
                td_area_detalle.aread_id,
                td_area_detalle.area_id,
                td_area_detalle.aread_permi,
                tm_area.area_nom,
                tm_area.area_correo 
                FROM td_area_detalle
                INNER JOIN tm_area ON tm_area.area_id = td_area_detalle.area_id
                WHERE 
                td_area_detalle.usu_id = ?
                AND td_area_detalle.aread_permi = 'Si'
                AND tm_area.est=1";
        /* TODO:Preparar la consulta SQL */
        $sql = $conectar->prepare($sql);
        /* TODO: Vincular los valores a los parámetros de la consulta */
        $sql->bindValue(1, $usu_id);
        /* TODO: Ejecutar la consulta SQL */
        $sql->execute();
        return $sql->fetchAll(pdo::FETCH_ASSOC);
    }
    public function update_perfil($usu_id, $usu_nomape, $usu_img, $usu_pass = null)
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Si se proporciona una nueva contraseña, actualizarla
        if (!is_null($usu_pass)) {
            $sql = "UPDATE tm_usuario 
                        SET usu_nomape = ?, usu_img = ?, usu_pass = ?, fech_modi = NOW() 
                        WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nomape);
            $sql->bindValue(2, $usu_img);
            $sql->bindValue(3, $usu_pass);
            $sql->bindValue(4, $usu_id);
        } else {
            // Si no se proporciona una nueva contraseña, no actualizarla
            $sql = "UPDATE tm_usuario 
                        SET usu_nomape = ?, usu_img = ?, fech_modi = NOW() 
                        WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nomape);
            $sql->bindValue(2, $usu_img);
            $sql->bindValue(3, $usu_id);
        }

        $sql->execute();
    }

    public function get_areas()
    {
        $conectar = parent::conexion();
        parent::set_names();

        // Consulta para obtener áreas activas
        $sql = "SELECT area_id, area_nom FROM tm_area WHERE est = 1 ORDER BY area_nom";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
