$(document).ready(function () {
    // Cargar lista de usuarios y activos al iniciar
    cargarUsuarios();
    cargarActivos();

    // Inicializar DataTable
    var tabla = $("#listado_reportes").DataTable({
        "ajax": {
            url: "../../controller/reporte.php?op=listar",
            type: "GET",
            data: function (d) {
                d.usuario = $("#reporte_usuario").val();
                d.activo = $("#reporte_activo").val();
                d.fecha = $("#reporte_fecha").val();
            },
            dataType: "json",
            error: function (xhr, status, error) {
                console.log("❌ Error en AJAX:", xhr.responseText);
                Swal.fire('Error', 'No se pudo cargar los reportes', 'error');
            }
        },
        "processing": true,
        "serverSide": true,
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

    // Aplicar filtros automáticamente cuando el usuario cambia algún select o fecha
    $("#reporte_usuario, #reporte_activo, #reporte_fecha").on("change", function () {
        let usuario = $("#reporte_usuario").val() || "";
        let activo = $("#reporte_activo").val() || "";
        let fecha = $("#reporte_fecha").val() || "";

        console.log(`📌 Filtrando - Usuario: ${usuario}, Activo: ${activo}, Fecha: ${fecha}`);

        tabla.ajax.reload(); // Recarga los datos aplicando los filtros
    });

    function cargarUsuarios() {
        $.ajax({
            url: '../../controller/reporte.php?op=obtener_usuarios',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">Seleccione un usuario</option>';
                response.forEach(function (usuario) {
                    if (usuario.usu_id && usuario.usu_nomape) {
                        options += `<option value="${usuario.usu_id}">${usuario.usu_nomape}</option>`;
                    }
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
                let options = '<option value="">Seleccione un activo</option>';
                response.forEach(function (activo) {
                    if (activo.id && activo.sbn) {
                        options += `<option value="${activo.id}">${activo.sbn}</option>`;
                    }
                });
                $('#reporte_activo').html(options);
            }
        });
    }
});
