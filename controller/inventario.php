<?php
require_once '../config/conexion.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($_GET['op'] == 'registrar') {
    $conectar = new Conectar();
    $conn = $conectar->conexion();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener valores del formulario
    $codigo = $_POST['codigo_activo'];
    $estado = $_POST['estado'] ?? '';
    $anio = date('Y');
    $usuario_id = $_SESSION['usuario_id'] ?? 1; // fallback a 1 si no hay sesión

    // Buscar activo
    $stmt = $conn->prepare("SELECT * FROM activos a 
                            LEFT JOIN detactivo d ON a.id = d.activo_id 
                            WHERE a.sbn = ? OR a.serie = ? LIMIT 1");
    $stmt->execute([$codigo, $codigo]);
    $activo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activo) {
        echo json_encode(['success' => false, 'message' => 'Activo no encontrado']);
        exit;
    }

    // Insertar en inventario
    $sql = "INSERT INTO inventario (
                activo_id, sbn, serie, tipo, marca, modelo,
                hostname, procesador, sisopera, ram, disco,
                responsable, ubicacion, sede,
                estado_encontrado, condicion, observaciones,
                acompra, ult_mant, anio_inventario, registrado_por
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $activo['id'], $activo['sbn'], $activo['serie'], $activo['tipo'], $activo['marca'],
        $activo['modelo'], $activo['hostname'], $activo['procesador'], $activo['sisopera'], $activo['ram'],
        $activo['disco'], $activo['responsable_id'], $activo['ubicacion'], $activo['sede'],
        $estado, $activo['condicion'], $activo['observaciones'],
        $activo['acompra'], $activo['ult_mant'], $anio, $usuario_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Inventario registrado correctamente']);
    exit;
}

if ($_GET['op'] == 'historial') {
    $conectar = new Conectar();
    $conn = $conectar->conexion();

    $sql = "SELECT i.sbn AS codigo, i.estado_encontrado, i.responsable, i.ubicacion,
                   i.observaciones, i.fecha_registro AS fecha
            FROM inventario i
            ORDER BY i.fecha_registro DESC
            LIMIT 50";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
    exit;
}
