// Variable global para la tabla del DataTable
var tabla;

/**
 * Función de inicialización principal.
 * Se configura el evento 'submit' para el formulario de creación o edición de vehículos.
 */
function init() {
    // Configuración del evento submit para el formulario de creación o edición
    $("#mnt_form").on("submit", function(e) {
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

    var formData = new FormData($("#mnt_form")[0]); // Crear un objeto FormData con los datos del formulario
    
    // Verificar si es una edición (si vehiculo_id tiene un valor)
    var url = $("#vehiculo_id").val() ? "../../controller/activo.php?op=editar" : "../../controller/activo.php?op=insertar";

    // Enviar los datos mediante AJAX
    $.ajax({
        url: url, // Cambia la URL según si es insertar o editar
        type: "POST",
        data: formData, // Datos del formulario
        contentType: false, // No establecer ningún tipo de contenido para los datos
        processData: false, // No procesar los datos automáticamente (para permitir el uso de FormData)
        success: function(datos) {
            // Mostrar un mensaje de éxito usando SweetAlert
            Swal.fire('Registro', 'Vehículo guardado correctamente', 'success');
            // Recargar el DataTable para mostrar el nuevo registro
            tabla.ajax.reload();
            // Cerrar el modal de registro
            $("#mnt_modal").modal("hide");
        },
        error: function(e) {
            // Mostrar un mensaje de error en caso de que falle la inserción
            Swal.fire('Error', 'No se pudo guardar el vehículo', 'error');
        }
    });
}

function cargarResponsables() {
    $.ajax({
        url: '../../controller/activo.php?op=obtener_responsables', // Ruta al backend
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            let options = '<option value="">Seleccione un responsable</option>';
            response.forEach(function(usuario) {
                options += `<option value="${usuario.usu_id}">${usuario.usu_nomape}</option>`;
            });
            $('#vehiculo_responsable_id').html(options);
        },
        error: function() {
            Swal.fire('Error', 'No se pudieron cargar los responsables', 'error');
        }
    });
}

// Llama esta función cada vez que abras el modal
$('#mnt_modal').on('shown.bs.modal', function () {
    cargarResponsables();
});

/**
 * Función para editar un vehículo.
 * Se cargan los datos en el formulario para su edición.
 *
 * @param {int} id - ID del vehículo que se va a editar.
 */
function editar(id) {
    // Cargar los datos del vehículo
    $.post("../../controller/activo.php?op=mostrar", { vehiculo_id: id }, function(data) {
        data = JSON.parse(data);

        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            // Llenar los campos del formulario con los datos recibidos
            $("#vehiculo_id").val(data.id);
            $("#vehiculo_sbn").val(data.sbn);
            $("#vehiculo_serie").val(data.serie);
            $("#vehiculo_tipo").val(data.tipo);
            $("#vehiculo_marca").val(data.marca);
            $("#vehiculo_modelo").val(data.modelo);
            $("#vehiculo_ubicacion").val(data.ubicacion);
            $("#vehiculo_fecha_registro").val(data.fecha_registro);
            $("#vehiculo_condicion").val(data.condicion);
            $("#vehiculo_estado").val(data.estado);

            // Cargar los responsables antes de establecer el valor seleccionado
            cargarResponsables();

            // Esperar un momento para asegurar que los responsables están cargados
            setTimeout(function() {
                $("#vehiculo_responsable_id").val(data.responsable_id);
            }, 200);

            $("#myModalLabel").html("Editar Activo");
            $("#mnt_modal").modal("show");
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
    // Confirmación de eliminación con SweetAlert
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este elemento se eliminará permanentemente",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonmodelo: '#3085d6',
        cancelButtonmodelo: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Hacer la petición AJAX para eliminar el vehículo
            $.post("../../controller/activo.php?op=eliminar", { vehiculo_id: id }, function(data) {
                data = JSON.parse(data); // Convertir los datos a formato JSON
                
                // Verificar si la operación fue exitosa
                if (data.success) {
                    Swal.fire('Eliminado', data.success, 'success');
                    tabla.ajax.reload(); // Recargar el DataTable para reflejar los cambios
                } else {
                    Swal.fire('Error', data.error, 'error');
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
    // Hacer la petición AJAX para obtener los datos del vehículo
    $.post("../../controller/activo.php?op=mostrar", { vehiculo_id: id }, function(data) {
        data = JSON.parse(data); // Convertir los datos recibidos a formato JSON
        
        // Verificar si se recibieron los datos correctamente
        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            // Llenar los campos del formulario con los datos recibidos
            $("#vehiculo_id").val(data.id);
            $("#vehiculo_sbn").val(data.sbn).prop("disabled", true);
            $("#vehiculo_serie").val(data.serie).prop("disabled", true);
            $("#vehiculo_tipo").val(data.tipo).prop("disabled", true);
            $("#vehiculo_marca").val(data.marca).prop("disabled", true);
            $("#vehiculo_modelo").val(data.modelo).prop("disabled", true);
            $("#vehiculo_ubicacion").val(data.ubicacion).prop("disabled", true);
            $("#vehiculo_responsable").val(data.responsable).prop("disabled", true);
            $("#vehiculo_fecha_registro").val(data.fecha_registro).prop("disabled", true);
            $("#vehiculo_ultimo_mantenimiento").val(data.ultimo_mantenimiento).prop("disabled", true);
            $("#vehiculo_proximo_mantenimiento").val(data.fecha_proximo_mantenimiento).prop("disabled", true);  // Cambiado a `fecha_proximo_mantenimiento`
            $("#vehiculo_condicion").val(data.condicion).prop("disabled", true);
            $("#vehiculo_estado").val(data.estado).prop("disabled", true);

            // Cambiar el título del modal a "Previsualización"
            $("#myModalLabel").html("Previsualización del Activo");

            // Deshabilitar el botón de guardar
            $(".modal-footer .btn-primary").hide();  // Ocultar el botón de guardar

            // Mostrar el modal con los datos cargados
            $("#mnt_modal").modal("show");
        }
    });
}

/**
 * Restaurar el formulario cuando se cierra el modal.
 * Se habilitan todos los campos y se muestra el botón de guardar.
 */
$("#mnt_modal").on("hidden.bs.modal", function () {
    // Habilitar todos los campos del formulario
    $("#mnt_form input, #mnt_form select").prop("disabled", false);

    // Mostrar el botón de guardar nuevamente
    $(".modal-footer .btn-primary").show();

    // Cambiar el título del modal a "Nuevo Registro"
    $("#myModalLabel").html("Nuevo Registro");
});



/**
 * Configuración del DataTable para listar los vehículos registrados.
 * Se configura la tabla con opciones de exportación y búsqueda.
 */
$(document).ready(function() {
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
            error: function(e) {
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
        success: function(response) {
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
        error: function() {
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


/**
 * Modificación en la función editar para que también cargue las fotos
 */
function editar(id) {
    $.post("../../controller/activo.php?op=mostrar", { vehiculo_id: id }, function(data) {
        data = JSON.parse(data);

        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            $("#vehiculo_id").val(data.id);
            $("#vehiculo_sbn").val(data.sbn);
            $("#vehiculo_serie").val(data.serie);
            $("#vehiculo_tipo").val(data.tipo);
            $("#vehiculo_marca").val(data.marca);
            $("#vehiculo_modelo").val(data.modelo);
            $("#vehiculo_ubicacion").val(data.ubicacion);
            $("#vehiculo_responsable_id").val(data.responsable_id);
            $("#vehiculo_fecha_registro").val(data.fecha_registro);
            $("#vehiculo_condicion").val(data.condicion);
            $("#vehiculo_estado").val(data.estado);

            // Llamar a la función para cargar fotos
            cargarFotos(data.id);

            $("#myModalLabel").html("Editar Activo");
            $("#mnt_modal").modal("show");
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
$("#btnnuevo").on("click", function() {
    $("#vehiculo_id").val('');   // Limpiar el campo de ID del vehículo
    $("#mnt_form")[0].reset();   // Resetea el formulario
    $("#myModalLabel").html('Nuevo Registro'); // Cambia el título del modal
    $("#mnt_modal").modal('show'); // Muestra el modal de registro
});

// Llamada a la función de inicialización
init();
