$(document).ready(function () {
    cargarUsuarios();
    cargarTiposActivos();

    var tabla = $("#listado_reportes").DataTable({
        "ajax": {
            url: "../../controller/reporte.php?op=listar",
            type: "GET",
            data: function (d) {
                d.usuario = $("#reporte_usuario").val();
                d.tipo_activo = $("#reporte_tipo_activo").val();
                d.fecha = $("#reporte_fecha").val();
            },
            dataType: "json",
            error: function (xhr, status, error) {
                console.log("❌ Error en AJAX:", xhr.responseText);
                Swal.fire('Error', 'No se pudo cargar los reportes', 'error');
            }
        },
        "processing": true,
        "serverSide": false,
        "responsive": true,
        "autoWidth": false,
        "order": [[0, "asc"]],
        "columns": [
            { "data": "id" },
            { "data": "usuario" },
            { "data": "sbn" },
            { "data": "serie" },
            { "data": "tipo_activo" },
            { "data": "marca" },
            { "data": "modelo" },
            { "data": "ubicacion" },
            { "data": "hostname" },
            { "data": "procesador" },
            { "data": "sisopera" },
            { "data": "ram" },
            { "data": "disco" },
            { "data": "fecha" }
        ]
    });

    $("#reporte_usuario, #reporte_tipo_activo, #reporte_fecha").on("change", function () {
        let usuario = $("#reporte_usuario").val() || "";
        let tipo_activo = $("#reporte_tipo_activo").val() || "";
        let fecha = $("#reporte_fecha").val() || "";

        console.log(`📌 Filtrando - Usuario: ${usuario}, Tipo de Activo: ${tipo_activo}, Fecha: ${fecha}`);

        tabla.ajax.reload();
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

    function cargarTiposActivos() {
        $.ajax({
            url: '../../controller/reporte.php?op=obtener_tipos_activos',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log("📌 Tipos de activos recibidos:", response);
                let options = '<option value="">Seleccione un tipo de activo</option>';
                response.forEach(function (tipo) {
                    if (tipo.tipo) {
                        options += `<option value="${tipo.tipo}">${tipo.tipo}</option>`;
                    }
                });
                $('#reporte_tipo_activo').html(options);
            },
            error: function (xhr, status, error) {
                console.log("❌ Error al cargar los tipos de activos:", xhr.responseText);
            }
        });
    }
});
