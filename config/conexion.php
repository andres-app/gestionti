<?php
    /* TODO: Inicia la sesión (si no está iniciada) */
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



    /* TODO: Definición de la clase Conectar */
    class Conectar{
        /* TODO: Propiedad protegida para almacenar la conexión a la base de datos */
        protected $dbh;

        /* TODO: Método para establecer la conexión a la base de datos */
        public function conexion() {
            try{
                /* TODO: Detectar entorno */
                if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
                    // Configuración para el entorno de desarrollo
                    $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=gestionti", "root", "Dev2804751$$$");
                } else {
                    // Configuración para el entorno de producción
                    $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=gestionti", "root", "Dev2804751$$$");
                }
                
                
                return $conectar;
            } catch(Exception $e) {
                /* TODO: En caso de error, imprime un mensaje y termina el script */
                print "Error BD: " . $e->getMessage() . "<br>";
                die();
            }
        }

        /* TODO: Método para establecer el juego de caracteres a UTF-8 */
        public function set_names(){
            return $this->dbh->query("SET NAMES 'utf8'");
        }

        /* TODO: Método estático que devuelve la ruta base del proyecto */
        public static function ruta(){
            if ($_SERVER['HTTP_HOST'] == 'localhost') {
                /* Ruta para el entorno de desarrollo */
                return "http://localhost:8080/gestionti/";
            } else {
                /* Ruta para el entorno de producción */
                return "http://10.4.16.83:8080/gestionti/";
            }
        }
    }
?>
