<?php
require_once("../config/conexion.php");
require_once("../models/Activo.php");


// Crear instancia del tipo Vehículo
$activo = new Activo();
$total_activos = $activo->get_total_activos(); // Obtenemos el total de vehículos




// Evaluar el valor del parámetro "op" para determinar qué operación realizar
switch ($_GET["op"]) {

    // Caso para listar todos los vehículos
    case "listar":
        $condicion = $_GET["condicion"] ?? ""; // ← Aquí recibimos el filtro enviado desde el JS
        $datos = $activo->get_activos($condicion); // ← Y lo pasamos al modelo
        if ($datos === false) {
            echo json_encode(["error" => "Error al obtener los activos"]);
            exit;
        }

        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array["id"] = $row["id"];
            $sub_array["sbn"] = $row["sbn"];
            $sub_array["serie"] = $row["serie"];
            $sub_array["tipo"] = $row["tipo"];
            $sub_array["marca"] = $row["marca"];
            $sub_array["modelo"] = $row["modelo"];
            $sub_array["ubicacion"] = $row["ubicacion_nombre"];
            $sub_array["responsable"] = $row["responsable"];

            // 🔹 Datos para el modal (NO se mostrarán en DataTable)
            $sub_array["hostname"]   = $row["hostname"]   ?? null;
            $sub_array["procesador"] = $row["procesador"] ?? null;
            $sub_array["sisopera"]   = $row["sisopera"]   ?? null;
            $sub_array["ram"]        = $row["ram"]        ?? null;
            $sub_array["disco"]      = $row["disco"]      ?? null;



            if ($row["tiene_baja"]) {
                $sub_array["acciones"] = '
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-soft-danger btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="De Baja">
            <i class="fas fa-ban"></i> De Baja
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#" onclick="verHistorial(' . $row["id"] . ')">
                <i class="fas fa-history me-2"></i>Ver historial</a></li>
            <li><a class="dropdown-item" href="#" onclick="abrirModalMantenimiento(' . $row["id"] . ')">
                <i class="fas fa-tools me-2"></i>Ver mantenimientos</a></li>
        </ul>
    </div>';
            } else {
                $sub_array["acciones"] = '
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-soft-info btn-sm" title="Previsualizar" onClick="previsualizar(' . $row["id"] . ')">
                <i class="bx bx-show-alt"></i>
            </button>
            <button type="button" class="btn btn-soft-warning btn-sm" title="Editar" onClick="editar(' . $row["id"] . ')">
                <i class="bx bx-edit-alt"></i>
            </button>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-soft-dark btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Más acciones">
                    <i class="bx bx-dots-horizontal-rounded"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" onclick="abrirModalBaja(' . $row["id"] . ')"><i class="fas fa-file-upload me-2"></i>Registrar baja</a></li>
                    <li><a class="dropdown-item" href="#" onclick="verHistorial(' . $row["id"] . ')"><i class="fas fa-history me-2"></i>Ver historial</a></li>
                    <li><a class="dropdown-item" href="#" title="Mantenimientos" onclick="abrirModalMantenimiento(' . $row["id"] . ')"><i class="fas fa-tools me-2"></i>Ver Mantenimientos</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="eliminar(' . $row["id"] . ')"><i class="bx bx-trash-alt me-2"></i>Eliminar</a></li>
                </ul>
            </div>
        </div>';
            }
            $data[] = $sub_array;
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        header('Content-Type: application/json');
        echo json_encode($results);
        break;

    // Caso para insertar un nuevo vehículo
    case "insertar":
        $sbn = $_POST["vehiculo_sbn"] ?? null;
        $serie = $_POST["vehiculo_serie"] ?? null;
        $tipo = $_POST["vehiculo_tipo"] ?? null;
        $marca = $_POST["vehiculo_marca"] ?? null;
        $modelo = $_POST["vehiculo_modelo"] ?? null;
        $ubicacion = $_POST["vehiculo_ubicacion"] ?? null;
        $responsable_id = $_POST["vehiculo_responsable_id"] ?? null;
        $fecha_registro = date("Y-m-d H:i:s");
        $condicion = $_POST["vehiculo_condicion"] ?? null;
        $estado = 1;
        $sede = $_POST["vehiculo_sede"] ?? null;
        $observaciones = $_POST["vehiculo_observaciones"] ?? null;
        $acompra = $_POST["vehiculo_acompra"] ?? null;

        // Ejecutar y guardar resultado
        $resultado = $activo->insertar_vehiculo(
            $sbn,
            $serie,
            $tipo,
            $marca,
            $modelo,
            $ubicacion,
            $sede,
            $responsable_id,
            $fecha_registro,
            $condicion,
            $estado,
            $observaciones,
            $acompra
        );

        if ($resultado && is_numeric($resultado)) {
            require_once(__DIR__ . "/../models/Auditoria.php");
            $auditoria = new Auditoria();

            $usuario_id = $_SESSION["usu_id"] ?? 0;
            $accion = "Registro de nuevo activo";
            $detalle = "Se registró un nuevo activo con SBN: $sbn";

            $auditoria->registrar_accion($resultado, $usuario_id, $accion, $detalle);

            echo json_encode(["success" => "Vehículo registrado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al registrar el vehículo."]);
        }




        break;

    // Caso para editar un vehículo existente
    case "editar":
        // 🚀 REGISTRAR EN LOG LOS DATOS RECIBIDOS
        error_log("📌 Datos recibidos para editar: " . json_encode($_POST));

        // Capturar los datos enviados por el formulario
        $id = $_POST["vehiculo_id"] ?? null;
        $sbn = $_POST["vehiculo_sbn"] ?? null;
        $serie = $_POST["vehiculo_serie"] ?? null;
        $tipo = $_POST["vehiculo_tipo"] ?? null;
        $marca = $_POST["vehiculo_marca"] ?? null;
        $modelo = $_POST["vehiculo_modelo"] ?? null;
        $ubicacion = $_POST["vehiculo_ubicacion"] ?? null;
        $responsable_id = $_POST["vehiculo_responsable_id"] ?? null;
        $fecha_registro = $_POST["vehiculo_fecha_registro"] ?? null;
        $condicion = $_POST["vehiculo_condicion"] ?? null;
        $vehiculo_actual = $activo->get_vehiculo_por_id($id); // Obtener el registro actual
        $estado = $vehiculo_actual["estado"]; // Conservar el estado actual ya registrado
        $hostname = $_POST["vehiculo_hostname"] ?? null;
        $procesador = $_POST["vehiculo_procesador"] ?? null;
        $sisopera = $_POST["vehiculo_sisopera"] ?? null;
        $ram = $_POST["vehiculo_ram"] ?? null;
        $disco = $_POST["vehiculo_disco"] ?? null;
        $sede = $_POST["vehiculo_sede"] ?? null;
        $observaciones = $_POST["vehiculo_observaciones"] ?? null;
        $acompra = $_POST["vehiculo_acompra"] ?? null;



        // 🚨 Verificar si algún campo clave está vacío
        if (!$id || !$sbn || !$serie) {
            error_log("❌ Faltan datos obligatorios: ID: $id, SBN: $sbn, Serie: $serie");
            echo json_encode(["error" => "Faltan datos obligatorios."]);
            exit;
        }

        // 🔥 LLAMAMOS A LA FUNCIÓN DE ACTUALIZACIÓN
        $resultado = $activo->editar_vehiculo($id, $sbn, $serie, $tipo, $marca, $modelo, $ubicacion, $responsable_id, $fecha_registro, $condicion, $estado, $hostname, $procesador, $sisopera, $ram, $disco, $sede, $observaciones, $acompra);


        require_once(__DIR__ . "/../models/Auditoria.php");
        $auditoria = new Auditoria();

        if ($resultado) {
            require_once(__DIR__ . "/../models/Auditoria.php");
            $auditoria = new Auditoria();

            $usuario_id = $_SESSION["usu_id"] ?? 0;
            $accion = "Edición de activo";

            // Comparar campo por campo
            $campos = [
                "sbn",
                "serie",
                "tipo",
                "marca",
                "modelo",
                "ubicacion",
                "responsable_id",
                "fecha_registro",
                "condicion",
                "estado",
                "sede",
                "observaciones",
                "acompra",
                "hostname",
                "procesador",
                "sisopera",
                "ram",
                "disco"
            ];

            foreach ($campos as $campo) {
                $valor_antiguo = $vehiculo_actual[$campo] ?? null;
                $valor_nuevo = $$campo;

                if ($valor_antiguo != $valor_nuevo) {
                    if ($campo === "responsable_id") {
                        $valor_antiguo_nombre = $auditoria->obtener_nombre_usuario($valor_antiguo);
                        $valor_nuevo_nombre = $auditoria->obtener_nombre_usuario($valor_nuevo);

                        $detalle = "Se cambió el responsable: de '$valor_antiguo_nombre' a '$valor_nuevo_nombre'";
                    } else {
                        $detalle = "Se modificó el campo '$campo': '$valor_antiguo' → '$valor_nuevo'";
                    }

                    $auditoria->registrar_cambio($id, $usuario_id, $accion, $campo, $valor_antiguo, $valor_nuevo, $detalle);
                }
            }


            // ✅ ÚNICA respuesta JSON
            echo json_encode(["success" => "Vehículo actualizado correctamente."]);
        } else {
            echo json_encode(["error" => "Error al actualizar el vehículo."]);
        }

        break;



    case "obtener_responsables":
        require_once("../models/Usuario.php");
        $usuario = new Usuario();

        $datos = $usuario->get_usuarios(); // Obtén los usuarios de tu modelo
        echo json_encode($datos);
        break;


    case "mostrar":
        if (isset($_POST["vehiculo_id"])) {
            $datos = $activo->get_vehiculo_por_id($_POST["vehiculo_id"]);

            if ($datos) {
                header('Content-Type: application/json');
                echo json_encode($datos);
            } else {
                echo json_encode(["error" => "No se encontraron datos para el ID del vehículo."]);
            }
        }
        break; // ✅ ESTE break FALTABA




    case "eliminar":
        if (!isset($_POST["vehiculo_id"]) || empty($_POST["vehiculo_id"])) {
            echo json_encode(["success" => false, "error" => "ID de activo no recibido"]);
            exit;
        }

        $vehiculo_id = $_POST["vehiculo_id"];
        $resultado = $activo->cambiar_estado($vehiculo_id, 0); // Cambia estado a inactivo

        if ($resultado) {
            echo json_encode(["success" => true, "message" => "Activo eliminado correctamente"]);
        } else {
            echo json_encode(["success" => false, "error" => "No se pudo eliminar el activo"]);
        }
        exit;


    default:
        echo json_encode(["error" => "Operación no válida."]);
        break;

    case "obtener_fotos":
        if (isset($_GET["vehiculo_id"])) {
            $vehiculo_id = $_GET["vehiculo_id"];
            $fotos = $activo->get_fotos_por_activo($vehiculo_id);

            if ($fotos) {
                echo json_encode($fotos);
            } else {
                echo json_encode(["error" => "No se encontraron fotos para este activo."]);
            }
        } else {
            echo json_encode(["error" => "ID del vehículo no proporcionado."]);
        }
        break;

    case "activos_estado":
        $datos = $activo->get_activos_por_estado();
        echo json_encode($datos);
        break;

    case "activos_tipo":
        $datos = $activo->get_activos_por_tipo();
        echo json_encode($datos);
        break;

    case "activos_sede":
        $datos = $activo->get_activos_por_sede();
        echo json_encode($datos);
        break;

    case "activos_condicion":
        $datos = $activo->get_activos_por_condicion();
        echo json_encode($datos);
        break;

    case 'buscar_codigo':
        require_once("../config/conexion.php");

        $conectar = new Conectar();
        $pdo = $conectar->conexion();

        $codigo = $_GET["codigo"];
        $stmt = $pdo->prepare("SELECT * FROM activos WHERE sbn = ? OR serie = ?");
        $stmt->execute([$codigo, $codigo]);
        $activo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($activo) {
            // Obtener detalles técnicos si es tipo CPU
            $stmt_det = $pdo->prepare("SELECT * FROM detactivo WHERE activo_id = ?");
            $stmt_det->execute([$activo['id']]);
            $detalle = $stmt_det->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "success" => true,
                "activo" => $activo,
                "detalle" => $detalle ?: []
            ]);
        } else {
            echo json_encode(["success" => false]);
        }
        break;

    case "obsolescencia_garantia":
        $datos = $activo->get_obsolescencia_garantia();
        echo json_encode($datos);
        break;

    case "tiene_baja":
        if (!isset($_POST["activo_id"])) {
            echo json_encode(["error" => "ID no enviado"]);
            exit;
        }

        $existe = $activo->tiene_baja($_POST["activo_id"]);
        echo json_encode(["tiene_baja" => $existe]);
        break;

    case "historial":
        require_once("../models/Auditoria.php");
        $auditoria = new Auditoria();

        $activo_id = $_POST["activo_id"] ?? null;

        if (!$activo_id) {
            echo json_encode([]); // Si no se envía, devolver vacío
            exit;
        }

        $historial = $auditoria->obtener_historial($activo_id);
        echo json_encode($historial);
        break;

case 'obtener_areas':
    // Aquí va la lógica para consultar las áreas desde la base de datos
    // Ejemplo:
    require_once(__DIR__ . '/../models/Activo.php');

    $activo = new Activo();

    $rspta = $activo->listarAreas(); // función del modelo
    echo json_encode($rspta);
    break;

}
