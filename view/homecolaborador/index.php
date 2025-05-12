<?php
require_once("../../config/conexion.php");
require_once("../../models/Activo.php");
require_once("../../models/Rol.php");

$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "iniciocolaborador");

if (isset($_SESSION["usu_id"]) && count($datos) > 0) {
    $activo = new Activo();
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

                        <!-- Título -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">DASHBOARD</h4>
                                    <a href="../../controller/descargar_dashboard_excel.php"
                                        class="btn btn-outline-primary mb-3">
                                        <i class="mdi mdi-download me-1"></i> Descargar Excel
                                    </a>
                                </div>

                            </div>
                        </div>




                        <!-- Total de Activos -->
                        <div class="row mb-4">
                            <div class="col-xl-3 col-md-6">
                                <div class="card shadow text-center h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="my-0 text-white">
                                            <i class="mdi mdi-car-multiple me-2"></i>Total de Activos
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <h3 class="card-title mb-0"><?= $total_vehiculos; ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fila de 3 gráficos -->
                        <div class="row g-4 align-items-stretch mb-4">
                            <!-- Estado -->
                            <div class="col-xl-4 col-md-6">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="my-0 text-white">Distribución por Condición</h5>
                                    </div>
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <canvas id="graficoCondicion" style="height: 220px; max-height: 220px;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo -->
                            <div class="col-xl-4 col-md-12">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="my-0 text-white">Distribución por Tipo</h5>
                                    </div>
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <canvas id="graficoTipo" style="height: 220px; max-height: 220px;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-xl-4 col-md-6">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="my-0 text-white">Distribución por Ubicación</h5>
                                    </div>
                                    <div class="card-body d-flex justify-content-center align-items-center">
                                        <canvas id="graficoUbicacion" style="height: 220px; max-height: 220px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

<!-- Fila 2: Estado + Obsolescencia en paralelo -->
<div class="row g-4 align-items-stretch">
    <!-- Estado -->
    <div class="col-xl-6 col-md-6">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="my-0 text-white">Distribución por Estado</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="graficoEstado" style="height: 220px; max-height: 220px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Obsolescencia -->
    <div class="col-xl-6 col-md-6">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="my-0 text-white">Obsolescencia y Garantía</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="graficoResumen" style="height: 220px; max-height: 220px;"></canvas>
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
            <script src="homecolaborador.js"></script>
    </body>

    </html>
    <?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>