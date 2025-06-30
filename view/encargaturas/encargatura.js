var tabla;

// Ejecutar cuando el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function () {
    init();
});

// Función principal
function init() {
    listarEncargaturas();

    // Botón: abrir modal nuevo préstamo
    $("#btn_nuevo_encargatura").on("click", function () {
        limpiarFormulario();
        $("#tituloModal").text("Registrar Encargatura");
        $("#modalEncargatura").modal("show");
    });

    // Envío de formulario con validación
    $("#form_encargatura").on("submit", function (e) {
        e.preventDefault();

        const fechaInicio = new Date($("#fecha_inicio").val());
        const fechaFinalizacion = new Date($("#fecha_finalizacion").val());

        if (!fechaInicio || !fechaFinalizacion || isNaN(fechaInicio) || isNaN(fechaFinalizacion)) {
            Swal.fire("Error", "Debe ingresar fechas válidas.", "warning");
            return;
        }

        const formData = new FormData(this);

        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }


        $.ajax({
            url: "../../controller/encargatura.php?op=insertar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (resp) {
                var res = JSON.parse(resp);
                if (res.success) {
                    Swal.fire("¡Éxito!", "Encargatura registrada correctamente", "success");
                    $("#modalEncargatura").modal("hide");
                    tabla.ajax.reload();
                } else {
                    Swal.fire("Error", res.error || "Ocurrió un problema", "error");
                }
            },
            error: function () {
                Swal.fire("Error", "No se pudo conectar al servidor", "error");
            }
        });
    });
}

// Tabla de préstamos
function listarEncargaturas(estado = "En Curso") {
    tabla = $("#tabla_encargatura").DataTable({
        ajax: {
            url: "../../controller/encargatura.php?op=listar",
            type: "GET",
            data: { estado: estado },
            dataType: "json",
        },
        responsive: true,
        destroy: true,
        pageLength: 10,
        order: [[0, "desc"]],
        columns: [
            { data: "glpi" },
            { data: "titular" },
            { data: "encargado" },
            { data: "fecha_inicio" },
            { data: "fecha_fin" },
            {
                data: null,
                render: function (data, type, row) {
                    if (!row.fecha_fin) return '<span class="badge bg-secondary">Sin fecha</span>';

                    const hoy = new Date();
                    const partes = row.fecha_fin.split("-");
                    const fechaFin = new Date(partes[0], partes[1] - 1, partes[2]);
                    hoy.setHours(0, 0, 0, 0);
                    fechaFin.setHours(0, 0, 0, 0);

                    const diffDays = Math.round((fechaFin - hoy) / (1000 * 60 * 60 * 24)); // <-- AJUSTE CLAVE

                    let badgeClass = "";
                    let texto = "";

                    if (diffDays > 3) {
                        badgeClass = "bg-success";
                        texto = diffDays + " días";
                    } else if (diffDays >= 1) {
                        badgeClass = "bg-warning text-dark";
                        texto = diffDays + " días";
                    } else if (diffDays === 0) {
                        badgeClass = "bg-danger";
                        texto = "¡Hoy!";
                    } else {
                        badgeClass = "bg-danger";
                        texto = "Vencido";
                    }

                    return `<span class="badge ${badgeClass}">${texto}</span>`;
                }
            },
            { data: "registrado" },
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


// Filtro por estado
$("#filtro_estado").on("change", function () {
    const estadoSeleccionado = $(this).val();
    listarEncargaturas(estadoSeleccionado);
});

// Limpiar formulario
function limpiarFormulario() {
    $("#form_encargatura")[0].reset();
    $("#fecha_inicio").val(new Date().toISOString().split("T")[0]);
    $("#fecha_finalizacion").val(""); // Asegura limpiar la fecha de finalización si existe
}

// Marcar como devuelto con observación
function marcarFinalizado(id) {
    Swal.fire({
        title: '¿Confirmar Finalización?',
        html: `
            <label for="observacion_devolucion" class="form-label">Observaciones (opcional):</label>
            <textarea id="observacion_devolucion" class="form-control mb-2" rows="4" placeholder="Ingrese observaciones de la devolución (opcional)"></textarea>
            <label for="fecha_devolucion_real" class="form-label">Fecha de Finalizacion:</label>
            <input type="date" id="fecha_devolucion_real" class="form-control" value="${new Date().toISOString().split("T")[0]}" />
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
                url: '../../controller/encargatura.php?op=marcar_devuelto',
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
        title: '📝 Observaciones de la Encargatura',
        icon: 'info',
        html: `
            <div style="text-align: left; font-size: 14px;">
                <div style="margin-bottom: 1rem; background: #f0f8ff; padding: .75rem; border-radius: .25rem;">
                    <strong>📤 Observacion de cambio:</strong><br>${entrega}
                </div>
                ${devolucion ? `
                <div style="background: #e8f5e9; padding: .75rem; border-radius: .25rem;">
                    <strong>🔄 Finalizacion:</strong><br>${devolucion}
                </div>` : ''}
            </div>
        `,
        confirmButtonText: 'Cerrar'
    });
}
