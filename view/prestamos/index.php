<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "prestamos");

if (isset($_SESSION["usu_id"]) && count($datos) > 0) {
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

                        <!-- Título y breadcrumb -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Lista de Préstamos - Área OSIN</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Página</a></li>
                                            <li class="breadcrumb-item active">Lista de Préstamos</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Préstamos de Activos</h4>
                                        <p class="card-title-desc">Registra, devuelve y consulta el historial de préstamos temporales.</p>
                                    </div>

                                    <div class="card-body">
                                        <div class="d-flex justify-content-end mb-3">
                                            <select id="filtro_estado" class="form-select w-auto">
                                                <option value="Prestado">Prestados</option>
                                                <option value="Devuelto">Devueltos</option>
                                            </select>
                                        </div>


                                        <!-- Botón -->
                                        <button type="button" id="btn_nuevo_prestamo" class="btn btn-primary waves-effect waves-light mb-3">
                                            <i class="fas fa-plus"></i> Nuevo Préstamo
                                        </button>

                                        <!-- Tabla -->
                                        <div class="table-responsive">
                                            <table id="tabla_prestamos" class="table table-bordered dt-responsive nowrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Activo</th>
                                                        <th>Responsable</th>
                                                        <th>Destino</th>
                                                        <th>Fecha Préstamo</th>
                                                        <th>Dev. Estimada</th>
                                                        <th>Estado</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>

                                    </div> <!-- end card-body -->
                                </div> <!-- end card -->
                            </div>
                        </div>

                    </div> <!-- .container-fluid -->
                </div> <!-- .page-content -->

                <?php require_once("../html/footer.php") ?>
            </div> <!-- .main-content -->

        </div> <!-- #layout-wrapper -->

        <?php require_once("../html/sidebar.php") ?>
        <div class="rightbar-overlay"></div>

        <?php require_once("modal_prestamo.php"); ?> <!-- ✅ MOVIDO AQUÍ -->

        <?php require_once("../html/js.php") ?>
        <script src="prestamo.js" type="text/javascript"></script>

    </body>

    </html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>