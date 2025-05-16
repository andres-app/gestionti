<?php
require_once("../config/conexion.php");

class Activo extends Conectar {
    
    public function get_total_vehiculos() {
        $conexion = parent::conexion();
        $sql = "SELECT COUNT(*) as total FROM vehiculos";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'];
    }
    
    public function get_vehiculos() {
        $conexion = parent::conexion();
        $sql = "SELECT * FROM vehiculos";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

}

// Instancia de Activo
$activo = new Activo();

// Verificar si se está ejecutando en localhost y realizar pruebas directas
if ($_SERVER['SERVER_NAME'] === 'localhost') {
    echo "<h3>Pruebas en Localhost</h3>";
    
    // Obtener el total de vehículos
    echo "<h4>Total de Vehículos:</h4>";
    echo $activo->get_total_vehiculos();
    
    // Obtener la lista completa de vehículos
    echo "<h4>Lista de Vehículos:</h4>";
    echo "<pre>";
    print_r($activo->get_vehiculos());
    echo "</pre>";
    

} else {
    // Switch para operaciones basadas en el valor 'op'
    $op = isset($_GET['op']) ? $_GET['op'] : '';

    switch ($op) {
        case 'total':
            $total = $activo->get_total_vehiculos();
            echo json_encode(['total' => $total]);
            break;

        case 'listar':
            $vehiculos = $activo->get_vehiculos();
            echo json_encode($vehiculos);
            break;

        default:
            echo json_encode(['error' => 'Operación no válida']);
            break;
    }
}
?>
