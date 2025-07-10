// Variable global para la tabla del DataTable
var tabla;

/**
 * Función de inicialización principal.
 * Se configura el evento 'submit' para el formulario de creación o edición de vehículos.
 */
function init() {
    // 🔁 Remueve cualquier posible event listener duplicado
    $("#mnt_form").off("submit");

    // ✅ Asegura que solo haya un listener
    $("#mnt_form").on("submit", function (e) {
        e.preventDefault(); // Esto es obligatorio
        guardaryeditar(e);
    });
}


/**
 * Función para guardar o editar un vehículo.
 * Se envían los datos del formulario mediante AJAX al controlador PHP.
 *
 * @param {Event} e - Evento de envío del formulario.
 */
function guardaryeditar(e) {
    e.preventDefault(); // Evitar el comportamiento por defecto del formulario

    var formData = new FormData($("#mnt_form")[0]); // Capturar datos del formulario

    console.log("🚀 Datos enviados al backend:", Object.fromEntries(formData)); // 🔥 Verificar datos antes del AJAX

    var url = $("#vehiculo_id").val() ? "../../controller/activo.php?op=editar" : "../../controller/activo.php?op=insertar";

    $.ajax({
        url: url,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log("✅ Respuesta del servidor:", response); // 🔥 Verificar respuesta del backend

            try {
                var jsonData = JSON.parse(response);
                if (jsonData.success) {
                    Swal.fire('Registro', 'Vehículo guardado correctamente', 'success');
                    tabla.ajax.reload();
                    $("#mnt_modal").modal("hide");
                } else {
                    Swal.fire('Error', jsonData.error || 'No se pudo actualizar.', 'error');
                }
            } catch (error) {
                console.error("❌ Error al parsear JSON:", error, response);
                Swal.fire('Error', 'Respuesta inesperada del servidor.', 'error');
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ Error en AJAX:", xhr.responseText);
            Swal.fire('Error', 'No se pudo guardar el vehículo', 'error');
        }
    });
}


function cargarResponsables(responsable_id = null, callback = null) {
    $.ajax({
        url: '../../controller/activo.php?op=obtener_responsables',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            let options = '<option value="">Seleccione un responsable</option>';

            response.forEach(function (usuario) {
                options += `<option value="${usuario.usu_id}">${usuario.usu_nomape}</option>`;
            });

            $('#vehiculo_responsable_id').html(options);

            // ✅ Asignar responsable solo si existe y es válido
            if (responsable_id !== null) {
                console.log("✅ Responsable seleccionado en el select:", responsable_id);
                $("#vehiculo_responsable_id").val(responsable_id);
            }

            // ✅ Forzar actualización visual del select
            $("#vehiculo_responsable_id").trigger("change");

            if (callback) callback();
        },
        error: function () {
            Swal.fire('Error', 'No se pudieron cargar los responsables', 'error');
        }
    });
}

/**
 * Función para eliminar un vehículo.
 * Solicita confirmación antes de proceder.
 *
 * @param {int} id - ID del vehículo que se va a eliminar.
 */
function eliminar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este elemento se eliminará permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../../controller/activo.php?op=eliminar",
                type: "POST",
                data: { vehiculo_id: id },
                dataType: "json",
                success: function (response) {
                    console.log("📌 Respuesta del servidor:", response);

                    if (response.success) {
                        Swal.fire('Eliminado', response.message, 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.error, 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.log("❌ Error en AJAX:", xhr.responseText);
                    Swal.fire('Error', 'No se pudo eliminar el activo', 'error');
                }
            });
        }
    });
}

/**
 * Función para previsualizar un vehículo.
 * Desactiva los campos del formulario para evitar su edición.
 *
 * @param {int} id - ID del vehículo a previsualizar.
 */
function previsualizar(id) {
    $.post("../../controller/activo.php?op=mostrar", { vehiculo_id: id }, function (data) {
        console.log("🔹 Datos recibidos en previsualizar:", data);

        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            $("#vehiculo_id").val(data.id);
            $("#vehiculo_sbn").val(data.sbn).prop("disabled", true);
            $("#vehiculo_serie").val(data.serie).prop("disabled", true);
            $("#vehiculo_tipo").val(data.tipo).prop("disabled", true);
            $("#vehiculo_marca").val(data.marca).prop("disabled", true);
            $("#vehiculo_modelo").val(data.modelo).prop("disabled", true);
            $("#vehiculo_ubicacion").val(data.ubicacion).prop("disabled", true);
            $("#vehiculo_fecha_registro").val(data.fecha_registro).prop("disabled", true);
            $("#vehiculo_condicion").val(data.condicion).prop("disabled", true);
            $("#vehiculo_estado").val(data.estado).prop("disabled", true);
            const ultimaFecha = data.ult_mant_real ? data.ult_mant_real : 'Sin mantenimiento registrado';
            $("#vehiculo_ult_mant").val(ultimaFecha).prop("disabled", true);
            $("#vehiculo_sede").val(data.sede).prop("disabled", true);
            $("#vehiculo_observaciones").val(data.observaciones).prop("disabled", true);
            $("#vehiculo_acompra").val(data.acompra).prop("disabled", true);
            // 🔹 Aplicar visibilidad a los campos vacíos
            manejarVisibilidadCampo("#vehiculo_hostname", data.hostname);
            manejarVisibilidadCampo("#vehiculo_procesador", data.procesador);
            manejarVisibilidadCampo("#vehiculo_sisopera", data.sisopera);
            manejarVisibilidadCampo("#vehiculo_ram", data.ram);
            manejarVisibilidadCampo("#vehiculo_disco", data.disco);
            $('#vehiculo_ubicacion').html(`<option selected>${data.ubicacion_nombre}</option>`).prop("disabled", true);




            // ✅ Agregamos el responsable sin que sea sobrescrito después
            // ✅ Aseguramos que el responsable se mantenga sin ser sobrescrito
            $("#vehiculo_responsable_id").html(`<option selected>${data.responsable}</option>`).prop("disabled", true);

            $("#myModalLabel").html("Previsualización del Activo");
            $(".modal-footer .btn-primary").hide();
            $("#mnt_modal").modal("show");

            // ✅ Cargar fotos correctamente
            cargarFotos(data.id); // 🔥 IMPORTANTE: Asegurar que se ejecuta aquí

            // Evitar que el modal recargue el select
            $("#mnt_modal").off("shown.bs.modal");
        }
    });
}


// Restaurar el formulario cuando se cierra el modal.
$("#mnt_modal").on("hidden.bs.modal", function () {
    $("#mnt_form input, #mnt_form select, #mnt_form textarea").prop("disabled", false);
    $(".modal-footer .btn-primary").show();
    $("#alerta-baja").remove(); // Eliminar alerta si quedó
    $("#mnt_form")[0].reset();
});



/**
 * Configuración del DataTable para listar los vehículos registrados.
 * Se configura la tabla con opciones de exportación y búsqueda.
 */
$(document).ready(function () {
    tabla = $("#listado_table").DataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
        "ajax": {
            url: '../../controller/activo.php?op=listar',
            type: "GET",
            dataType: "json",
            data: function (d) {
                d.condicion = $("#filtro_condicion").val(); // ✅ este es el filtro de condición
            },
            // error: function (e) {
            //     Swal.fire('Error', 'No se pudo cargar la lista de activos', 'error');
            // }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "columns": [
            { "data": "id", "visible": false },
            { "data": "sbn" },
            { "data": "serie" },
            { "data": "tipo" },
            { "data": "marca" },
            { "data": "modelo" },
            { "data": "ubicacion" },
            { "data": "responsable" },
            { "data": "acciones", "orderable": false, "searchable": false }
        ],
        "language": {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros)",
            "sSearch": "Buscar:",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });

    // ⏬ Esto hace que la tabla se actualice cuando cambias el filtro
    $("#filtro_condicion").on("change", function () {
        tabla.ajax.reload();
    });
});

/**
 * Función para cargar la galería de fotos de un activo en formato carrusel con 3 imágenes por slide.
 * @param {int} vehiculo_id - ID del activo
 */
function cargarFotos(vehiculo_id) {
    $.ajax({
        url: "../../controller/activo.php?op=obtener_fotos",
        type: "GET",
        data: { vehiculo_id: vehiculo_id },
        dataType: "json",
        success: function (response) {
            $('#galeria_fotos').empty(); // Limpiar el carrusel

            if (response.error || response.length === 0) {
                $('#galeria_fotos').html('<p class="text-muted text-center">No hay fotos disponibles.</p>');
            } else {
                let imagenesHTML = '';
                let totalFotos = response.length;

                for (let i = 0; i < totalFotos; i += 3) {
                    let activeClass = i === 0 ? 'active' : ''; // Solo la primera slide será activa

                    imagenesHTML += `<div class="carousel-item ${activeClass}">
                                        <div class="row">`;

                    // Agregar hasta 3 imágenes en cada slide
                    for (let j = i; j < i + 3 && j < totalFotos; j++) {
                        imagenesHTML += `
                            <div class="col-md-4">
                                <img src="${response[j].foto_url}" class="d-block w-100 rounded shadow-sm"
                                     style="max-height: 300px; object-fit: cover; cursor: pointer;"
                                     alt="Foto del activo" onclick="mostrarZoom('${response[j].foto_url}')">
                            </div>
                        `;
                    }

                    imagenesHTML += `</div></div>`;
                }

                $('#galeria_fotos').html(imagenesHTML); // Insertar imágenes en el carrusel
            }
        },
        error: function () {
            $('#galeria_fotos').html('<p class="text-danger text-center">Error al cargar las fotos.</p>');
        }
    });
}

/**
 * Función para mostrar la imagen en grande dentro del modal de zoom.
 * @param {string} url - URL de la imagen seleccionada
 */
function mostrarZoom(url) {
    $('#imagenZoom').attr('src', url); // Cambia la imagen en el modal
    $('#modalZoomImagen').modal('show'); // Muestra el modal
}

// Cargar fotos cuando se abre el modal de edición
$('#mnt_modal').on('shown.bs.modal', function () {
    var vehiculo_id = $('#vehiculo_id').val();
    if (vehiculo_id) {
        cargarFotos(vehiculo_id);
    }
});

function manejarVisibilidadCampo(selector, valor) {
    let parentCol = $(selector).closest(".col-md-6");

    if (!valor || valor === "N/A" || valor.trim() === "" || valor === null) {
        parentCol.hide();  // Oculta la columna si el valor es inválido
    } else {
        $(selector).val(valor).prop("disabled", true); // Asigna valor y deshabilita el campo
        parentCol.show();  // Muestra la columna si el campo tiene un valor válido
    }
}

/**
 * Modificación en la función editar para que también cargue las fotos
 */
function editar(id) {
    console.log("📌 Editando activo con ID:", id);

    $.ajax({
        url: "../../controller/activo.php?op=mostrar",
        type: "POST",
        data: { vehiculo_id: id },
        dataType: "json",
        success: function (data) {
            console.log("✅ Respuesta del servidor:", data);

            if (!data || data.error) {
                Swal.fire("Error", data.error || "No se encontró información", "error");
                return;
            }

            // ✅ Asignación de todos los campos
            $("#vehiculo_id").val(data.id);
            $("#vehiculo_sbn").val(data.sbn);
            $("#vehiculo_serie").val(data.serie);
            $("#vehiculo_tipo").val(data.tipo);
            $("#vehiculo_marca").val(data.marca);
            $("#vehiculo_modelo").val(data.modelo);
            $("#vehiculo_ubicacion").val(data.ubicacion);
            $("#vehiculo_fecha_registro").val(data.fecha_registro);
            $("#vehiculo_condicion").val(data.condicion);
            $("#vehiculo_estado").val(data.estado); // ⚠️ Asegúrate que sea 1 o 0
            $("#vehiculo_hostname").val(data.hostname);
            $("#vehiculo_procesador").val(data.procesador);
            $("#vehiculo_sisopera").val(data.sisopera);
            $("#vehiculo_ram").val(data.ram);
            $("#vehiculo_disco").val(data.disco);
            const ultimaFecha = data.ult_mant_real ? data.ult_mant_real : 'Sin mantenimiento registrado';
            $("#vehiculo_ult_mant").val(ultimaFecha);
            $("#vehiculo_sede").val(data.sede);
            $("#vehiculo_observaciones").val(data.observaciones).prop("disabled", false);
            $("#vehiculo_acompra").val(data.acompra);

            let responsableID = data.responsable_id && !isNaN(data.responsable_id) ? data.responsable_id : null;
            console.log("📌 Responsable ID recibido:", responsableID);

            // Verifica si está dado de baja
            const esDeBaja = data.condicion && data.condicion.toLowerCase() === 'de baja';

            if (esDeBaja) {
                // Mostrar alerta visual
                if ($("#alerta-baja").length === 0) {
                    $(".modal-body").prepend(`
            <div id="alerta-baja" class="alert alert-warning" role="alert">
                Este activo está registrado como <strong>De Baja</strong>. No se permiten modificaciones.
            </div>
        `);
                }

                // Deshabilitar todos los campos
                $("#mnt_form input, #mnt_form select, #mnt_form textarea").prop("disabled", true);

                // Ocultar botón guardar
                $(".modal-footer .btn-primary").hide();
            } else {
                $("#alerta-baja").remove(); // Quita la alerta si no aplica
                $("#mnt_form input, #mnt_form select, #mnt_form textarea").prop("disabled", false);
                $(".modal-footer .btn-primary").show();
            }

            $("#vehiculo_ubicacion").val(data.ubicacion); // Puedes omitir esta línea si usas cargarAreas con parámetro

            cargarAreas(data.ubicacion); // 🔹 Cargar áreas y seleccionar la correcta

            cargarResponsables(responsableID, function () {
                console.log("🔹 Responsable y demás campos cargados correctamente.");
                $("#myModalLabel").html("Editar Activo");
                $(".modal-footer .btn-primary").show();
                $("#mnt_modal").modal("show");
            });

        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("🔴 Error en la solicitud AJAX:", textStatus, errorThrown);
            Swal.fire("Error", "No se pudo obtener la información del activo", "error");
        }
    });
}

// Cargar fotos cuando se abre el modal
$('#mnt_modal').on('shown.bs.modal', function () {
    var vehiculo_id = $('#vehiculo_id').val();
    if (vehiculo_id) {
        cargarFotos(vehiculo_id);
    }
});


/**
 * Evento para mostrar el modal cuando se hace clic en el botón "Nuevo Registro".
 * Se limpia el formulario y se prepara el modal para la creación de un nuevo vehículo.
 */
$("#btnnuevo").on("click", function () {
    $("#vehiculo_id").val('');
    $("#mnt_form")[0].reset();
    $("#vehiculo_acompra").val('');

    // SOLO FECHA, no hora
    const fechaActual = new Date().toISOString().split('T')[0];
    $("#vehiculo_fecha_registro").val(fechaActual);

    // ✅ Cargar áreas
    cargarAreas(null);

    // ✅ Cargar responsables
    cargarResponsables(null, function () {
        $("#myModalLabel").html('Nuevo Registro');
        $(".modal-footer .btn-primary").show();
        $("#mnt_modal").modal('show');
    });
});



$(document).ready(function () {
    function toggleCamposCPU() {
        const tipo = $('#vehiculo_tipo').val();
        const esCPU = tipo === 'CPU';

        const camposCPU = [
            '#vehiculo_hostname',
            '#vehiculo_procesador',
            '#vehiculo_sisopera',
            '#vehiculo_ram',
            '#vehiculo_disco'
        ];

        camposCPU.forEach(selector => {
            const col = $(selector).closest('.col-md-6');
            if (esCPU) {
                col.show();
                $(selector).prop('required', true);
            } else {
                col.hide();
                $(selector).val('').prop('required', false);
            }
        });
    }

    // Inicial al cargar modal
    toggleCamposCPU();

    // Al cambiar tipo
    $('#vehiculo_tipo').on('change', toggleCamposCPU);
});

function abrirModalBaja(id) {
    $.ajax({
        url: '../../controller/activo.php?op=tiene_baja',
        type: 'POST',
        data: { activo_id: id },
        dataType: 'json',
        success: function (response) {
            if (response.tiene_baja) {
                Swal.fire({
                    icon: 'info',
                    title: 'Este activo ya tiene una baja registrada',
                    text: 'No puedes registrar otra baja para este activo.',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                $('#baja_activo_id').val(id);
                $('#form_baja_activo')[0].reset();
                $('#modalBajaActivo').modal('show');
            }
        },
        error: function () {
            Swal.fire('Error', 'No se pudo verificar si el activo ya tiene baja.', 'error');
        }
    });
}


$('#form_baja_activo').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    $.ajax({
        url: '../../controller/baja.php?op=registrar',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (resp) {
            console.log("RESPUESTA CRUDA:", resp);  // 👈 Revisa qué devuelve exactamente
            const res = JSON.parse(resp); // Esto es lo que lanza el error si no es JSON válido

            if (res.success) {
                Swal.fire('Listo', 'Baja registrada correctamente', 'success');
                $('#modalBajaActivo').modal('hide');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', res.error || 'No se pudo registrar la baja.', 'error');
            }
        }
    });
});

function verHistorial(id) {
    console.log("📌 ID enviado a historial:", id);

    $.ajax({
        url: '../../controller/activo.php?op=historial',
        type: 'POST',
        data: { activo_id: id },
        dataType: 'json',
        success: function (data) {
            console.log("📜 Historial recibido:", data);

            let html = "";
            if (data.length > 0) {
                data.forEach(item => {
                    html += `
                    <tr>
                    <td>${item.fecha}</td>
                    <td>${item.usuario}</td>
                    <td>${item.accion}</td>
                    <td>${item.campo_modificado || '-'}</td>
                    <td>${item.valor_anterior || '-'}</td>
                    <td>${item.valor_nuevo || '-'}</td>
                    <td>${item.descripcion}</td>
                    </tr>`;
                });

            } else {
                html = '<tr><td colspan="4" class="text-center">No hay historial registrado.</td></tr>';
            }

            $('#historial_body').html(html);
            $('#modalHistorial').modal('show');
        },
        error: function () {
            Swal.fire('Error', 'No se pudo cargar el historial', 'error');
        }
    });
}

// === FUNCIONES DE MANTENIMIENTO ===

function abrirModalMantenimiento(id) {
    $('#mantenimiento_activo_id').val(id);
    $('#form_mantenimiento')[0].reset();
    cargarHistorialMantenimientos(id);

    // 🟢 Establecer fecha actual por defecto
    document.getElementById("fecha").valueAsDate = new Date();

    $('#modalMantenimiento').modal('show');
}


$(document).ready(function () {
    $('#form_mantenimiento').off('submit').on('submit', function (e) {
        e.preventDefault();

        // Validar campos obligatorios antes de enviar (puedes agregar más validaciones)
        if (!$('#fecha').val()) {
            Swal.fire('Atención', 'Debe ingresar la fecha del mantenimiento', 'warning');
            return;
        }
        if (!$('#detalle').val().trim()) {
            Swal.fire('Atención', 'Debe ingresar el detalle del mantenimiento', 'warning');
            return;
        }

        // Validar archivo Orden de Servicio
        if ($('#orden_servicio')[0].files.length === 0) {
            Swal.fire('Atención', 'Debe cargar la Orden de Servicio', 'warning');
            return;
        }

        // Validar archivo Documento de Conformidad
        if ($('#documento_conformidad')[0].files.length === 0) {
            Swal.fire('Atención', 'Debe cargar el Documento de Conformidad', 'warning');
            return;
        }

        const $btnSubmit = $(this).find('button[type=submit]');
        $btnSubmit.prop('disabled', true); // deshabilitar botón

        const formData = new FormData(this);
        $.ajax({
            url: '../../controller/mantenimiento.php?op=registrar',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (resp) {
                console.log("🔴 RESPUESTA CRUDA:", resp);
                const res = JSON.parse(resp);

                if (res.success) {
                    Swal.fire('Listo', 'Mantenimiento registrado correctamente', 'success');
                    cargarHistorialMantenimientos($('#mantenimiento_activo_id').val());
                    $('#form_mantenimiento')[0].reset();
                } else {
                    Swal.fire('Error', 'No se pudo registrar el mantenimiento', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            },
            complete: function () {
                $btnSubmit.prop('disabled', false); // habilitar botón nuevamente
            }
        });
    });
});

function cargarHistorialMantenimientos(id) {
    $.ajax({
        url: '../../controller/mantenimiento.php?op=listar',
        type: 'POST',
        data: { activo_id: id },
        dataType: 'json',
        success: function (data) {
            let html = '';
            if (data.length === 0) {
                html = '<tr><td colspan="4" class="text-center">Sin mantenimientos registrados</td></tr>';
            } else {
                data.forEach(row => {
                    html += `
                        <tr>
                            <td>${row.fecha}</td>
                            <td>${row.usuario}</td>
                            <td>${row.proveedor}</td>
                            <td>${row.detalle}</td>
                            <td class="text-center">
    ${row.orden_servicio
                            ? `<a href="../../${row.orden_servicio}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf"></i></a>`
                            : '<span class="text-muted">-</span>'}
</td>
<td class="text-center">
    ${row.documento_conformidad
                            ? `<a href="../../${row.documento_conformidad}" target="_blank" class="btn btn-sm btn-outline-success"><i class="fas fa-file-pdf"></i></a>`
                            : '<span class="text-muted">-</span>'}
</td>

                        </tr>
                    `;
                });
            }
            $('#mantenimientos_body').html(html);
        },
        error: function () {
            $('#mantenimientos_body').html('<tr><td colspan="6" class="text-center text-danger">Error al cargar mantenimientos</td></tr>');
        }
    });
}

function cargarAreas(ubicacion = null) {
    console.log("🟡 Ejecutando cargarAreas()");

    $.ajax({
        url: '../../controller/activo.php?op=obtener_areas',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            let options = '<option value="">Seleccione un área</option>';
            response.forEach(area => {
                options += `<option value="${area.area_id}">${area.area_nom}</option>`;
            });
            $('#vehiculo_ubicacion').html(options);

            if (ubicacion) {
                $('#vehiculo_ubicacion').val(ubicacion);
            }

        },
        error: function () {
            Swal.fire('Error', 'No se pudieron cargar las áreas', 'error');
        }
    });
}

// Llamada a la función de inicialización
init();
