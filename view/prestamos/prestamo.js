var tabla;

// Inicialización
function init() {
    listarPrestamos();

    // Evento: botón nuevo préstamo
    $("#btn_nuevo_prestamo").on("click", function () {
        limpiarFormulario();
        cargarUsuariosDestino();
        $("#modalPrestamo").modal("show");
    });

    // Evento: envío del formulario
    $("#form_prestamo").on("submit", function (e) {
        e.preventDefault();

        var formData = new FormData(this);

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

// Listado de préstamos
function listarPrestamos() {
    tabla = $("#tabla_prestamos").DataTable({
        ajax: {
            url: "../../controller/prestamo.php?op=listar",
            type: "GET",
            dataType: "json",
            error: function () {
                Swal.fire("Error", "No se pudo cargar la lista", "error");
            }
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

// Cargar usuarios destino al select
function cargarUsuariosDestino() {
    $.ajax({
        url: "../../controller/prestamo.php?op=usuarios_destino",
        type: "GET",
        dataType: "json",
        success: function (data) {
            let html = '<option value="">Seleccione...</option>';
            data.forEach(user => {
                html += `<option value="${user.usu_id}">${user.usu_nomape}</option>`;
            });
            $("#usuario_destino").html(html);
        },
        error: function () {
            Swal.fire("Error", "No se pudieron cargar los usuarios destino", "error");
        }
    });
}

// Limpiar modal
function limpiarFormulario() {
    $("#form_prestamo")[0].reset();
    $("#prestamo_id").val('');
    $("#activo_id").val(''); // deberías establecerlo según qué activo se está prestando
    $("#fecha_prestamo").val(new Date().toISOString().slice(0, 16)); // set default fecha/hora
}

// Función para marcar préstamo como devuelto
function marcarDevuelto(id) {
    Swal.fire({
        title: '¿Confirmar devolución?',
        html: `
            <textarea id="observacion_devolucion" class="form-control" rows="4"
                placeholder="Ingrese observaciones de la devolución (opcional)"></textarea>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, devolver',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            return document.getElementById('observacion_devolucion').value.trim();
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const observacion = result.value;

            $.ajax({
                url: '../../controller/prestamo.php?op=marcar_devuelto',
                type: 'POST',
                data: {
                    id: id,
                    observaciones: observacion
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


// Cargar activos disponibles en OSIN
function cargarActivosOSIN() {
    $.ajax({
        url: '../../controller/prestamo.php?op=activos_osin',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            let html = '<option value="">Seleccione un activo...</option>';
            data.forEach(activo => {
                html += `<option value="${activo.id}">
                    [${activo.tipo}] ${activo.sbn} - Serie: ${activo.serie}
                </option>`;
            });
            $("#activo_id").html(html);

            // Inicializar Select2 DESPUÉS de llenar el select
            $('#activo_id').select2({
                placeholder: "Seleccione un activo",
                width: '100%',
                dropdownParent: $('#modalPrestamo') // ⚠️ Asegúrate de que el ID del modal es correcto
            });
        },
        error: function () {
            Swal.fire('Error', 'No se pudieron cargar los activos disponibles.', 'error');
        }
    });
}




$("#btn_nuevo_prestamo").on("click", function () {
    limpiarFormulario();
    cargarUsuariosDestino();
    cargarActivosOSIN();
    $("#modalPrestamo").modal("show");
});

$('#activo_id').select2({
    placeholder: "Seleccione un activo",
    width: '100%',
    dropdownParent: $('#modalPrestamo')  // IMPORTANTE: esto hace que el dropdown aparezca dentro del modal
});

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





init();
