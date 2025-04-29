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
                                </div>
                            </div>
                        </div>

                        <!-- Fila 1: Total de activos + Gráfico por estado -->
                        <div class="row g-4 align-items-stretch mb-4">
                            <!-- Total de Activos -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card text-center shadow h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="my-0">
                                            <i class="mdi mdi-car-multiple me-2"></i>Total de Activos
                                        </h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <h3 class="card-title mb-0"><?= $total_vehiculos; ?></h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Gráfico: Activos por Estado -->
                            <div class="col-xl-9 col-md-12">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="my-0">Activos por Estado</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="graficoEstado" style="min-height: 300px; height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fila 2: Gráfico por tipo -->
                        <div class="row g-4 align-items-stretch">
                            <!-- Gráfico: Activos por Tipo -->
                            <div class="col-xl-6 col-md-12">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="my-0">Activos por Tipo</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="graficoTipo" style="min-height: 300px; height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>

                            <!-- Espacio libre para próximo gráfico -->
                            <div class="col-xl-6 col-md-12">
                                <div class="card shadow h-100">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="my-0">Próximo gráfico</h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <p class="mb-0 text-muted">Aquí podrías mostrar activos por ubicación, condición o sede.</p>
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
        <script src="homecolaborador.js"></script>
    </body>

    </html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>