<?php
require_once("../../config/conexion.php");
require_once("../../models/Activo.php");
require_once("../../models/Rol.php");

$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "iniciocolaborador");

if (isset($_SESSION["usu_id"]) && count($datos) > 0) {
    // Crear instancia del modelo Vehículo
    $activo = new Activo();
    
    // Obtener vehículos con próximos mantenimientos
    $total_vehiculos = $activo->get_total_activos();
    $proximos_mantenimientos = $activo->get_proximos_mantenimientos();
    ?>
    <!doctype html>
    <html lang="es">

    <head>
        <title>Gestión TI - INPE</title>
        <?php require_once("../html/head.php") ?>
    </head>

    <body>

        <div id="layout-wrapper">

            <?php require_once("../html/header.php") ?>

            <?php require_once("../html/menu.php") ?>

            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- Título de la página -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">DASHBOARD</h4>
                                </div>
                            </div>
                        </div>
                
                        <div class="row">
                    <!-- Card para el total de vehículos -->
                    <div class="col-xl-3">
                        <div class="card text-center">
                            <div class="card-header bg-primary text-white">
                                <h5 class="my-0 text-white">
                                    <i class="mdi mdi-car-multiple me-2 text-white"></i>Total de Activos
                                </h5>
                            </div>
                            <div class="card-body">
                                <h3 class="card-title"><?= $total_vehiculos; ?></h3>
                            </div>
                        </div>
                    </div>
                    </div>


                    </div>
                </div>

                <?php require_once("../html/footer.php") ?>

            </div>

        </div>

        <?php require_once("../html/sidebar.php") ?>

        <div class="rightbar-overlay"></div>

        <?php require_once("../html/js.php") ?>

    </body>

    </html>
    <?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>
