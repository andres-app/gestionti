$(document).ready(function () {
    // Cargar lista de usuarios y bienes al iniciar
    cargarUsuarios();
    cargarBienes();

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
            { "data": "bien" },
            { "data": "fecha" },
            { "data": "acciones", "orderable": false }
        ]
    });
    
    // Corrección en exportación de PDF y Excel
    $("#btn_exportar_pdf").on("click", function () {
        window.location.href = "../../controller/reporte.php?op=exportar_pdf"; 
    });
    
    $("#btn_exportar_excel").on("click", function () {
        window.location.href = "../../controller/reporte.php?op=exportar_excel"; 
    });
    
    

    // Generar reporte con filtros
    $("#btn_generar_reporte").on("click", function () {
        var usuario = $("#reporte_usuario").val();
        var bien = $("#reporte_bien").val();
        var fecha = $("#reporte_fecha").val();

        tabla.ajax.url(`../../controller/reporte.php?op=listar&usuario_id=${usuario}&bien_id=${bien}&fecha=${fecha}`).load();
    });

    // Exportar a PDF
    $("#btn_exportar_pdf").on("click", function () {
        window.open(`../../controller/reporte.php?op=exportar_pdf&usuario_id=${$("#reporte_usuario").val()}&bien_id=${$("#reporte_bien").val()}&fecha=${$("#reporte_fecha").val()}`, "_blank");
    });

    // Exportar a Excel
    $("#btn_exportar_excel").on("click", function () {
        window.open(`../../controller/reporte.php?op=exportar_excel&usuario_id=${$("#reporte_usuario").val()}&bien_id=${$("#reporte_bien").val()}&fecha=${$("#reporte_fecha").val()}`, "_blank");
    });

    function cargarUsuarios() {
        $.ajax({
            url: '../../controller/reporte.php?op=listar_usuarios',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">Seleccione un usuario</option>';
                response.forEach(function (usuario) {
                    options += `<option value="${usuario.id}">${usuario.nombre}</option>`;
                });
                $('#reporte_usuario').html(options);
            }
        });
    }

    function cargarBienes() {
        $.ajax({
            url: '../../controller/reporte.php?op=listar_bienes',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                let options = '<option value="">Seleccione un bien</option>';
                response.forEach(function (bien) {
                    options += `<option value="${bien.id}">${bien.nombre}</option>`;
                });
                $('#reporte_bien').html(options);
            }
        });
    }
});
