$(document).ready(function () {
    cargarUsuarios();
    cargarTiposActivos();
    cargarUbicaciones();

    var tabla = $("#listado_reportes").DataTable({
        "ajax": {
            url: "../../controller/reporte.php?op=listar",
            type: "GET",
            "data": function (d) {
                d.usuario = $("#reporte_usuario").val();
                d.tipo_activo = $("#reporte_tipo_activo").val();
                d.fecha = $("#reporte_fecha").val();
                d.obsolescencia = $("#filtro_obsolescencia").val(); // Nuevo
                d.garantia = $("#filtro_garantia").val(); // Nuevo
                d.condicion = $("#filtro_condicion").val();
                d.ubicacion = $("#filtro_ubicacion").val();
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
            { "data": "acompra" },
            {
                "data": "acompra",
                "render": function (acompra) {
                    if (!acompra || acompra < 1990) return "—";
                    const year = new Date().getFullYear();
                    const edad = year - acompra;
                    return edad >= 5 ? "Obsoleto" : "Vigente";
                }
            },
            {
                "data": "acompra",
                "render": function (acompra) {
                    if (!acompra || acompra < 1990) return "—";
                    const year = new Date().getFullYear();
                    const edad = year - acompra;
                    return edad >= 3 ? "Sin Garantía" : "Con Garantía";
                }
            },
            { "data": "sede" },
            { "data": "condicion" },
            { "data": "observaciones" },
            { "data": "fecha" }
        ]

    });

    $(document).ready(function() {
    // Toggle para mostrar/ocultar filtros adicionales
    $('#toggle-filtros').click(function() {
        $('#filtros-adicionales').slideToggle();
        $(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');
        $(this).html(function(i, html) {
            return html.includes("Más") ? 
                '<i class="fas fa-minus-circle me-1"></i> Menos filtros' : 
                '<i class="fas fa-plus-circle me-1"></i> Más filtros';
        });
    });
});

    $("#btn_exportar_pdf").on("click", function () {
        let usuario = $("#reporte_usuario").val() || "";
        let tipo_activo = $("#reporte_tipo_activo").val() || "";
        let fecha = $("#reporte_fecha").val() || "";
        let obsolescencia = $("#filtro_obsolescencia").val() || "";
        let garantia = $("#filtro_garantia").val() || "";
        let condicion = $("#filtro_condicion").val() || ""; // Añadido
        let ubicacion = $("#filtro_ubicacion").val() || "";

        let url = `../../controller/reporte.php?op=exportar_pdf&usuario=${usuario}&tipo_activo=${tipo_activo}&fecha=${fecha}&obsolescencia=${obsolescencia}&garantia=${garantia}&condicion=${condicion}&ubicacion=${ubicacion}`;
        window.open(url, '_blank');
    });

    $("#btn_exportar_excel").on("click", function () {
        let usuario = $("#reporte_usuario").val() || "";
        let tipo_activo = $("#reporte_tipo_activo").val() || "";
        let fecha = $("#reporte_fecha").val() || "";
        let obsolescencia = $("#filtro_obsolescencia").val() || "";
        let garantia = $("#filtro_garantia").val() || "";
        let condicion = $("#filtro_condicion").val() || ""; // Asegúrate de capturar este valor
        let ubicacion = $("#filtro_ubicacion").val() || "";

        let url = `../../controller/reporte.php?op=exportar_excel&usuario=${usuario}&tipo_activo=${tipo_activo}&fecha=${fecha}&obsolescencia=${obsolescencia}&garantia=${garantia}&condicion=${condicion}&ubicacion=${ubicacion}`;
        window.location.href = url;
    });




    $("#reporte_usuario, #reporte_tipo_activo, #reporte_fecha, #filtro_obsolescencia, #filtro_garantia, #filtro_condicion, #filtro_ubicacion").on("change", function () {
        console.log("Parámetros enviados:", {
            usuario: $("#reporte_usuario").val(),
            tipo_activo: $("#reporte_tipo_activo").val(),
            fecha: $("#reporte_fecha").val(),
            obsolescencia: $("#filtro_obsolescencia").val(),
            garantia: $("#filtro_garantia").val(),
            condicion: $("#filtro_condicion").val(),
            ubicacion: $("#filtro_ubicacion").val()
        });
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
                let options = '<option value="">Todos</option>';
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

    function cargarUbicaciones() {
        $.ajax({
            url: '../../controller/reporte.php?op=obtener_ubicaciones',
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                $('#filtro_ubicacion').html('<option value="">Cargando ubicaciones...</option>');
            },
            success: function (response) {
                console.log("Respuesta del servidor:", response); // Para depuración

                if (response.success && Array.isArray(response.data)) {
                    let options = '<option value="">Todas las ubicaciones</option>';

                    // Ordenar alfabéticamente
                    response.data.sort((a, b) => {
                        return a.ubicacion.localeCompare(b.ubicacion);
                    });

                    // Generar opciones
                    response.data.forEach(function (item) {
                        if (item.ubicacion && item.ubicacion.trim() !== '') {
                            options += `<option value="${item.ubicacion}">${item.ubicacion}</option>`;
                        }
                    });

                    $('#filtro_ubicacion').html(options);
                } else {
                    console.error("Estructura de respuesta inválida:", response);
                    $('#filtro_ubicacion').html('<option value="">Error en datos recibidos</option>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error en la solicitud:", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                $('#filtro_ubicacion').html('<option value="">Error al cargar ubicaciones</option>');
            }
        });
    }
});
