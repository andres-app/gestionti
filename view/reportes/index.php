<?php
// Importar conexión y modelo de Roles
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");

// Validar permisos del usuario
$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "reportes");

// Verificar si el usuario tiene acceso al módulo
if (isset($_SESSION["usu_id"]) && count($datos) > 0) {
?>

<!doctype html>
<html lang="es">
<head>
    <title>Reportes - Sistema</title>
    <?php require_once("../html/head.php") ?>
</head>

<body>

    <div id="layout-wrapper">
        <?php require_once("../html/header.php") ?>
        <?php require_once("../html/menu.php") ?>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- Título y breadcrumbs -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Reportes</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Módulos</a></li>
                                        <li class="breadcrumb-item active">Reportes</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros para generar reportes -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Generar Reporte</h4>
                                    <p class="card-title-desc">Filtra y genera reportes personalizados.</p>
                                </div>

                                <div class="card-body">
                                    <form id="reporte_form">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Usuario</label>
                                                <select class="form-control" id="reporte_usuario">
                                                    <option value="">Seleccione un usuario</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Bien</label>
                                                <select class="form-control" id="reporte_bien">
                                                    <option value="">Seleccione un bien</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Fecha</label>
                                                <input type="date" class="form-control" id="reporte_fecha">
                                            </div>
                                        </div>
                                        <br>

                                        <!-- Botón para generar reporte -->
                                        <button type="button" id="btn_generar_reporte" class="btn btn-primary">
                                            Generar Reporte
                                        </button>

                                        <!-- Botones para exportar -->
                                        <button type="button" id="btn_exportar_pdf" class="btn btn-danger">
                                            Exportar a PDF
                                        </button>

                                        <button type="button" id="btn_exportar_excel" class="btn btn-success">
                                            Exportar a Excel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla para mostrar los reportes generados -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <table id="listado_reportes" class="table table-bordered dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Usuario</th>
                                                <th>Bien</th>
                                                <th>Fecha</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> <!-- Container-fluid -->
            </div> <!-- Page-content -->
            <?php require_once("../html/footer.php") ?>
        </div> <!-- Main-content -->

    </div> <!-- Layout-wrapper -->

    <?php require_once("../html/sidebar.php") ?>
    <div class="rightbar-overlay"></div>
    <?php require_once("../html/js.php") ?>
    <script type="text/javascript" src="reporte.js"></script>

</body>
</html>

<?php
} else {
    // Redirigir si el usuario no tiene permisos
    header("Location:" . Conectar::ruta() . "index.php");
}
?>
