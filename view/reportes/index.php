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
                        <!-- Reemplaza la sección de filtros con este código -->
<!-- Reemplaza la sección de filtros con este código -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Generar Reporte</h4>
                <p class="card-title-desc">Filtra y genera reportes personalizados.</p>
            </div>
            <div class="card-body">
                <form id="reporte_form">
                    <!-- Filtros principales siempre visibles -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tipo de Activo</label>
                            <select class="form-control" id="reporte_tipo_activo">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Año de Registro</label>
                            <select class="form-control" id="reporte_fecha">
                                <option value="">Todos</option>
                                <?php
                                $año_actual = date("Y");
                                for ($i = $año_actual; $i >= 2013; $i--) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Condición</label>
                            <select class="form-control" id="filtro_condicion">
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="baja">De Baja</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Filtros adicionales (ocultos inicialmente) -->
                    <div class="row mb-3" id="filtros-adicionales" style="display: none;">
                        <div class="col-md-4">
                            <label class="form-label">Usuario</label>
                            <select class="form-control" id="reporte_usuario">
                                <option value="">Seleccione un usuario</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Obsolescencia</label>
                            <select class="form-control" id="filtro_obsolescencia">
                                <option value="">Todos</option>
                                <option value="vigente">Vigente (&lt; 5 años)</option>
                                <option value="obsoleto">Obsoleto (≥ 5 años)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Garantía</label>
                            <select class="form-control" id="filtro_garantia">
                                <option value="">Todos</option>
                                <option value="con">Con Garantía (&lt; 3 años)</option>
                                <option value="sin">Sin Garantía (≥ 3 años)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mt-3">
                            <label class="form-label">Ubicación</label>
                            <select class="form-control" id="filtro_ubicacion">
                                <option value="">Cargando ubicaciones...</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-link p-0" id="toggle-filtros">
                            <i class="fas fa-plus-circle me-1"></i> Más filtros
                        </button>
                        
                        <div>
                            <button type="button" id="btn_exportar_pdf" class="btn btn-danger me-2">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            
                            <button type="button" id="btn_exportar_excel" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
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
                                                    <th>SBN</th>
                                                    <th>Serie</th>
                                                    <th>Tipo</th>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Ubicación</th>
                                                    <th>Hostname</th>
                                                    <th>Procesador</th>
                                                    <th>Sis. Ope</th>
                                                    <th>RAM</th>
                                                    <th>Disco</th>
                                                    <th>Año Compra</th>
                                                    <th>Obsolescencia</th>
                                                    <th>Garantía</th>
                                                    <th>Sede</th>
                                                    <th>Condición</th>
                                                    <th>Observaciones</th>
                                                    <th>Fecha Reg.</th>
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