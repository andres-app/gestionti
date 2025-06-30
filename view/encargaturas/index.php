<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "encargaturas");

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
                                    <h4 class="mb-sm-0 font-size-18">Encargaturas</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Página</a></li>
                                            <li class="breadcrumb-item active">Lista de Encargaturas</li>
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
                                        <!-- Botón a la izquierda y filtro a la derecha -->
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                            <button type="button" id="btn_nuevo_encargatura" class="btn btn-primary waves-effect waves-light mb-2">
                                                <i class="fas fa-plus"></i> Nueva Encargatura
                                            </button>
                                            <select id="filtro_estado" class="form-select w-auto mb-2">
                                                <option value="En Curso">En Curso</option>
                                                <option value="Finalizado">Finalizados</option>
                                                <option value="todos">Todos</option>
                                            </select>
                                        </div>
                                        <!-- Tabla -->
                                        <div class="table-responsive">
                                            <table id="tabla_encargatura" class="table table-bordered dt-responsive nowrap w-100">
                                                <thead>
                                                    <tr>
                                                        <th>GLPI</th>
                                                        <th>Titular</th>
                                                        <th>Encargado</th>
                                                        <th>Fecha inicio</th>
                                                        <th>Fecha fin</th>
                                                        <th>Dias</th>
                                                        <th>Registrado</th>
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

        <?php require_once("modal_encargaturas.php"); ?> <!-- ✅ MOVIDO AQUÍ -->

        <?php require_once("../html/js.php") ?>
        <script src="encargatura.js" type="text/javascript"></script>

    </body>

    </html>

<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>