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


    // Filtrar automáticamente cuando se cambien los filtros
    $("#reporte_usuario, #reporte_activo, #reporte_fecha").on("change", function () {
        console.log("📌 Filtrando - Usuario:", $("#reporte_usuario").val(), "Activo:", $("#reporte_activo").val(), "Fecha:", $("#reporte_fecha").val());
        tabla.ajax.reload(); // Recarga los datos aplicando los filtros
    });

    function cargarUsuarios() {
        $.ajax({
            url: '../../controller/reporte.php?op=obtener_usuarios',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log("📌 Respuesta de usuarios:", response); // Verificar datos en consola

                if (!Array.isArray(response)) {
                    console.error("❌ Error: Respuesta inválida en usuarios", response);
                    return;
                }

                let options = '<option value="">Seleccione un usuario</option>';
                response.forEach(function (usuario) {
                    // Asegurémonos de que 'usuario.id' y 'usuario.nombre' existen
                    if (usuario.usu_id && usuario.usu_nomape) {
                        options += `<option value="${usuario.usu_id}">${usuario.usu_nomape}</option>`;
                    } else {
                        console.warn("⚠️ Usuario inválido:", usuario);
                    }
                });

                $('#reporte_usuario').html(options);
            },
            error: function (xhr, status, error) {
                console.log("❌ Error en AJAX:", xhr.responseText);
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
