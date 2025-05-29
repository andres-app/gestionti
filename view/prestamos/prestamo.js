var tabla;

// Ejecutar cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    init();
});

// Función principal
function init() {
    listarPrestamos();
    cargarActivosOSIN();
    cargarUsuariosDestino();

    // Botón: abrir modal nuevo préstamo
    $("#btn_nuevo_prestamo").on("click", function () {
        limpiarFormulario();
        cargarUsuariosDestino();
        cargarActivosOSIN();
        $("#modalPrestamo").modal("show");
    });

    // Envío de formulario con validación
    $("#form_prestamo").on("submit", function (e) {
        e.preventDefault();

        const fechaPrestamo = new Date($("#fecha_prestamo").val());
        const fechaDevolucion = new Date($("#fecha_devolucion_estimada").val());

        if (!fechaPrestamo || !fechaDevolucion || isNaN(fechaPrestamo) || isNaN(fechaDevolucion)) {
            Swal.fire("Error", "Debe ingresar fechas válidas.", "warning");
            return;
        }

        if (fechaDevolucion <= fechaPrestamo) {
            Swal.fire("Error", "La fecha de devolución debe ser posterior a la de préstamo.", "warning");
            return;
        }

        const formData = new FormData(this);

        $.ajax({
            url: "../../controller/prestamo.php?op=insertar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (resp) {
                var res = JSON.parse(resp);
                if (res.success) {
                    Swal.fire("¡Éxito!", "Préstamo registrado correctamente", "success");
                    $("#modalPrestamo").modal("hide");
                    tabla.ajax.reload();
                } else {
                    Swal.fire("Error", res.error || "Ocurrió un problema", "error");
                }
            }
        });
    });
}

// Tabla de préstamos
function listarPrestamos(estado = "Prestado") {
    tabla = $("#tabla_prestamos").DataTable({
        ajax: {
            url: "../../controller/prestamo.php?op=listar",
            type: "GET",
            data: { estado: estado },
            dataType: "json",
        },
        responsive: true,
        destroy: true,
        pageLength: 10,
        order: [[0, "desc"]],
        columns: [
            { data: "id" },
            { data: "activo" },
            { data: "origen" },
            { data: "destino" },
            { data: "fecha_prestamo" },
            { data: "fecha_devolucion_estimada" },
            { data: "fecha_devolucion_real" }, // NUEVO
            { data: "estado" },
            { data: "acciones" }
        ],

        language: {
            sSearch: "Buscar:",
            sZeroRecords: "No se encontraron resultados",
            sInfo: "Mostrando _TOTAL_ registros",
            sInfoEmpty: "Mostrando 0 registros",
            sInfoFiltered: "(filtrado de _MAX_ registros)",
            oPaginate: {
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        }
    });
}

$("#filtro_estado").on("change", function () {
    const estadoSeleccionado = $(this).val();
    listarPrestamos(estadoSeleccionado);
});


// Cargar activos OSIN en el select
function cargarActivosOSIN() {
    $.ajax({
        url: '../../controller/prestamo.php?op=activos_osin',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            if ($.fn.select2 && $('#activo_id').hasClass("select2-hidden-accessible")) {
                $('#activo_id').select2('destroy');
            }

            let html = '<option value=""></option>'; // opción vacía correcta
            data.forEach(activo => {
                html += `<option value="${activo.id}">[${activo.tipo}] ${activo.sbn} - Serie: ${activo.serie}</option>`;
            });
            $("#activo_id").html(html).select2({
                placeholder: "Seleccione un activo...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPrestamo')
            });

        }
    });
}


// Cargar usuarios destino en el select
function cargarUsuariosDestino() {
    $.ajax({
        url: "../../controller/prestamo.php?op=usuarios_destino",
        type: "GET",
        dataType: "json",
        success: function (data) {
            if ($.fn.select2 && $('#usuario_destino').hasClass("select2-hidden-accessible")) {
                $('#usuario_destino').select2('destroy');
            }

            let html = '<option value=""></option>'; // opción vacía correcta
            data.forEach(user => {
                html += `<option value="${user.usu_id}">${user.usu_nomape}</option>`;
            });
            $("#usuario_destino").html(html).select2({
                placeholder: "Seleccione...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPrestamo')
            });

        }
    });
}





// Limpiar formulario
function limpiarFormulario() {
    $("#form_prestamo")[0].reset();
    $("#activo_id").val('').trigger('change');
    $("#usuario_destino").val('').trigger('change');
    $("#fecha_prestamo").val(new Date().toISOString().slice(0, 16));
}

// Marcar como devuelto con observación
function marcarDevuelto(id) {
    Swal.fire({
        title: '¿Confirmar devolución?',
        html: `
            <label class="form-label text-start d-block mb-1">Observaciones (opcional):</label>
            <textarea id="observacion_devolucion" class="form-control mb-3" rows="3"
                placeholder="Ingrese observaciones de la devolución (opcional)"></textarea>

            <label class="form-label text-start d-block mb-1">Fecha de Devolución Real:</label>
            <input type="datetime-local" id="fecha_devolucion_real" class="form-control" value="${new Date().toISOString().slice(0, 16)}">
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, devolver',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const observacion = document.getElementById('observacion_devolucion').value.trim();
            const fechaReal = document.getElementById('fecha_devolucion_real').value;
            if (!fechaReal) {
                Swal.showValidationMessage('La fecha de devolución real es obligatoria.');
                return false;
            }
            return { observacion, fechaReal };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/prestamo.php?op=marcar_devuelto',
                type: 'POST',
                data: {
                    id: id,
                    observaciones: result.value.observacion,
                    fecha_devolucion_real: result.value.fechaReal
                },
                success: function (resp) {
                    const res = JSON.parse(resp);
                    if (res.success) {
                        Swal.fire('Listo', 'El activo fue marcado como devuelto.', 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', res.error || 'No se pudo devolver.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Error en la solicitud al servidor.', 'error');
                }
            });
        }
    });
}

// Mostrar observaciones en ventana modal
function verObservaciones(texto) {
    const partes = texto.split("Devolución:");
    const entrega = partes[0]?.trim() || 'Sin observaciones de entrega';
    const devolucion = partes[1]?.trim();

    Swal.fire({
        title: '📝 Observaciones del Préstamo',
        icon: 'info',
        html: `
            <div style="text-align: left; font-size: 14px;">
                <div style="margin-bottom: 1rem; background: #f0f8ff; padding: .75rem; border-radius: .25rem;">
                    <strong>📤 Entrega:</strong><br>${entrega}
                </div>
                ${devolucion ? `
                <div style="background: #e8f5e9; padding: .75rem; border-radius: .25rem;">
                    <strong>🔄 Devolución:</strong><br>${devolucion}
                </div>` : ''}
            </div>
        `,
        confirmButtonText: 'Cerrar'
    });
}
