// Variable global para la tabla del DataTable
var tabla;

/**
 * Función de inicialización principal.
 * Se configura el evento 'submit' para el formulario de creación o edición de vehículos.
 */
function init() {
    // Configuración del evento submit para el formulario de creación o edición
    $("#mnt_form").on("submit", function (e) {
        guardaryeditar(e); // Llamar a la función para guardar o editar un registro
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
            $("#vehiculo_ult_mant").val(data.ult_mant).prop("disabled", true);
            $("#vehiculo_sede").val(data.sede).prop("disabled", true);
            $("#vehiculo_observaciones").val(data.observaciones).prop("disabled", true);
            $("#vehiculo_acompra").val(data.acompra).prop("disabled", true);
            // 🔹 Aplicar visibilidad a los campos vacíos
            manejarVisibilidadCampo("#vehiculo_hostname", data.hostname);
            manejarVisibilidadCampo("#vehiculo_procesador", data.procesador);
            manejarVisibilidadCampo("#vehiculo_sisopera", data.sisopera);
            manejarVisibilidadCampo("#vehiculo_ram", data.ram);
            manejarVisibilidadCampo("#vehiculo_disco", data.disco);



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
    // Habilitar todos los campos del formulario, incluyendo textarea
    $("#mnt_form input, #mnt_form select, #mnt_form textarea").prop("disabled", false);

    // Mostrar el botón de guardar nuevamente
    $(".modal-footer .btn-primary").show();

    // Cambiar el título del modal a "Nuevo Registro"
    $("#myModalLabel").html("Nuevo Registro");
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
            error: function (e) {
                Swal.fire('Error', 'No se pudo cargar la lista de activos', 'error');
            }
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
            $("#vehiculo_ult_mant").val(data.ult_mant);
            $("#vehiculo_sede").val(data.sede);
            $("#vehiculo_observaciones").val(data.observaciones).prop("disabled", false);
            $("#vehiculo_acompra").val(data.acompra);



            let responsableID = data.responsable_id && !isNaN(data.responsable_id) ? data.responsable_id : null;
            console.log("📌 Responsable ID recibido:", responsableID);

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
    $("#vehiculo_id").val('');   // Limpiar el campo de ID del vehículo
    $("#mnt_form")[0].reset();   // Resetea el formulario
    $("#vehiculo_acompra").val(''); // 🔹 Limpiar campo acompra

    // 🕒 Asignar fecha actual
    const fechaActual = new Date();
    const formato = fechaActual.toISOString().slice(0, 19).replace("T", " ");
    $("#vehiculo_fecha_registro").val(formato);

    // ✅ Cargar opciones del select de responsables
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


// Llamada a la función de inicialización
init();
