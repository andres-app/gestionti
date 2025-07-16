<?php
require_once("../../config/conexion.php");
require_once("../../models/Rol.php");
$rol = new Rol();
$datos = $rol->validar_menu_x_rol($_SESSION["rol_id"], "auditoria");

if (isset($_SESSION["usu_id"]) && count($datos) > 0) {
?>
<!doctype html>
<html lang="es">
<head>
    <title>Auditoría - Gestión TI</title>
    <?php require_once("../html/head.php") ?>
</head>
<body>
<div id="layout-wrapper">
    <?php require_once("../html/header.php") ?>
    <?php require_once("../html/menu.php") ?>
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">Auditoría del Sistema</h4>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="filtro_tabla" class="form-select">
                            <option value="">Todas las tablas</option>
                            <option value="activos">Activos</option>
                            <option value="usuarios">Usuarios</option>
                            <!-- Agrega aquí otras tablas si las usas en auditoría -->
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Historial de Auditoría</h4>
                                <p class="card-title-desc">Revisa los cambios y acciones registradas en el sistema.</p>
                            </div>
                            <div class="card-body">
                                <table id="tabla_auditoria" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Acción</th>
                                            <th>Fecha</th>
                                            <th>Usuario ID</th>
                                            <th>Activo ID</th>
                                            <th>Tabla</th>
                                            <th>Registro ID</th>
                                            <th>Campo</th>
                                            <th>Valor Anterior</th>
                                            <th>Valor Nuevo</th>
                                            <th>Detalle</th>
                                            <th>Descripción</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
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
<script type="text/javascript">
$(document).ready(function() {
    function cargarAuditoria(tabla = "") {
        $.ajax({
            url: '../../controller/auditoria.php?op=listar',
            type: 'POST',
            data: { tabla_afectada: tabla },
            dataType: 'json',
            success: function(data) {
                var rows = '';
                $.each(data, function(i, item) {
                    rows += '<tr>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.accion + '</td>' +
                        '<td>' + item.fecha + '</td>' +
                        '<td>' + item.usuario_id + '</td>' +
                        '<td>' + item.activo_id + '</td>' +
                        '<td>' + item.tabla_afectada + '</td>' +
                        '<td>' + item.registro_id + '</td>' +
                        '<td>' + (item.campo_modificado || '') + '</td>' +
                        '<td>' + (item.valor_anterior || '') + '</td>' +
                        '<td>' + (item.valor_nuevo || '') + '</td>' +
                        '<td>' + (item.detalle || '') + '</td>' +
                        '<td>' + (item.descripcion || '') + '</td>' +
                        '</tr>';
                });
                $("#tabla_auditoria tbody").html(rows);
            }
        });
    }

    // Inicial
    cargarAuditoria();

    // Filtrado
    $("#filtro_tabla").on('change', function() {
        cargarAuditoria($(this).val());
    });
});
</script>
</body>
</html>
<?php
} else {
    header("Location:" . Conectar::ruta() . "index.php");
}
?>
