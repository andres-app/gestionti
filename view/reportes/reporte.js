$(document).ready(function () {
    // Cargar lista de usuarios y activos al iniciar
    cargarUsuarios();
    cargarActivos();

    // Configuración de DataTable
    var tabla = $("#listado_reportes").DataTable({
        "ajax": {
            url: "../../controller/reporte.php?op=listar",
            type: "GET",
            dataType: "json",
            error: function (xhr, status, error) {
                console.log("❌ Error en AJAX:", xhr.responseText);
                Swal.fire('Error', 'No se pudo cargar los reportes', 'error');
            }
        },
        "responsive": true,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "columns": [
            { "data": "id" },
            { "data": "usuario" },
            { "data": "activo" },
            { "data": "fecha" },
            { 
                "data": "acciones",
                "orderable": false,
                "searchable": false,
                "defaultContent": "" // Evita errores si "acciones" está vacío
            }
        ]
    });
    

    // Generar reporte con filtros
    $("#btn_generar_reporte").on("click", function () {
        var usuario = $("#reporte_usuario").val();
        var activo = $("#reporte_activo").val();
        var fecha = $("#reporte_fecha").val();

        tabla.ajax.url(`../../controller/reporte.php?op=listar&usuario=${usuario}&activo=${activo}&fecha=${fecha}`).load();
    });

    function cargarUsuarios() {
        $.ajax({
            url: '../../controller/reporte.php?op=obtener_usuarios',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (!Array.isArray(response)) {
                    console.error("❌ Error: Respuesta inválida en usuarios", response);
                    return;
                }
                let options = '<option value="">Seleccione un usuario</option>';
                response.forEach(function (usuario) {
                    options += `<option value="${usuario.id}">${usuario.nombre}</option>`;
                });
                $('#reporte_usuario').html(options);
            }
        });
    }

    function cargarActivos() {
        $.ajax({
            url: '../../controller/reporte.php?op=obtener_activos',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (!Array.isArray(response)) {
                    console.error("❌ Error: Respuesta inválida en activos", response);
                    return;
                }
                let options = '<option value="">Seleccione un activo</option>';
                response.forEach(function (activo) {
                    options += `<option value="${activo.id}">${activo.sbn}</option>`;
                });
                $('#reporte_activo').html(options);
            }
        });
    }
});
