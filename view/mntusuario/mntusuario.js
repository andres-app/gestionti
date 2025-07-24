var tabla;
var tabla_permiso;

function init() {
    $("#mnt_form").on("submit", function (e) {
        guardaryeditar(e);
    });

    // Cargar combo de áreas
    $.post("../../controller/area.php?op=combo", function (data) {
        $('#area_id').html(data);
    });
}

function guardaryeditar(e) {
    e.preventDefault();

    // Validar contraseña para nuevo usuario
    if ($("#usu_id").val() == "" && $("#usu_pass").val() == "") {
        Swal.fire({
            title: "Error",
            text: "Debe ingresar una contraseña para nuevo usuario",
            icon: "error"
        });
        return false;
    }

    // Crear FormData y agregar campos manualmente
    var formData = new FormData();
    formData.append('usu_id', $("#usu_id").val());
    formData.append('usu_nomape', $("#usu_nomape").val());
    formData.append('usu_correo', $("#usu_correo").val());
    formData.append('area_id', $("#area_id").val());
    formData.append('rol_id', $("#rol_id").val());
    
    // Solo agregar la contraseña si no está vacía o es un nuevo usuario
    if ($("#usu_pass").val() != "" || $("#usu_id").val() == "") {
        formData.append('usu_pass', $("#usu_pass").val());
    }

    // Depuración: Mostrar lo que se enviará
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    $.ajax({
        url: "../../controller/usuario.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            console.log("Respuesta del servidor:", datos);
            if (datos == 1) {
                $("#mnt_form")[0].reset();
                $("#mnt_modal").modal('hide');
                tabla.ajax.reload();
                Swal.fire({
                    title: "Éxito",
                    text: "Usuario registrado con éxito",
                    icon: "success"
                });
            } else if (datos == 2) {
                $("#mnt_form")[0].reset();
                $("#mnt_modal").modal('hide');
                tabla.ajax.reload();
                Swal.fire({
                    title: "Éxito",
                    text: "Usuario actualizado con éxito",
                    icon: "success"
                });
            } else if (datos == 0) {
                Swal.fire({
                    title: "Error",
                    text: "El usuario ya existe",
                    icon: "error"
                });
            } else if (datos == 5) {
                Swal.fire({
                    title: "Error",
                    text: "Error al actualizar el usuario",
                    icon: "error"
                });
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la solicitud AJAX:", status, error);
            Swal.fire({
                title: "Error",
                text: "Ocurrió un error al procesar la solicitud",
                icon: "error"
            });
        },
        beforeSend: function() {
            $('#btnguardar').prop("disabled", true);
        },
        complete: function() {
            $('#btnguardar').prop("disabled", false);
        }
    });
}


$(document).ready(function () {

    // Cargar combo de roles
    $.post("../../controller/rol.php?op=combo", function (data) {
        $('#rol_id').html(data);
    });

    // Cargar combo de áreas
    $.post("../../controller/area.php?op=combo", function (data) {
        $('#area_id').html(data);
    });

    tabla = $("#listado_table").dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '../../controller/usuario.php?op=listar',
            type: "get",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    }).DataTable();

});

$(document).on("click", "#btnnuevo", function () {
    $("#usu_id").val('');
    $("#mnt_form")[0].reset();
    $("#myModalLabel").html('Nuevo Registro');
    $("#pass_required").show(); // Muestra que es obligatorio
    $("#usu_pass").prop('required', true); // Hace el campo requerido
    $("#pass_help").hide(); // Oculta la ayuda para edición
    $("#mnt_modal").modal('show');
});

function editar(usu_id) {
    $("#myModalLabel").html('Editar Registro');
    $.post("../../controller/usuario.php?op=mostrar", { usu_id: usu_id }, function (data) {
        data = JSON.parse(data);
        $("#usu_id").val(data.usu_id);
        $("#usu_nomape").val(data.usu_nomape);
        $("#usu_correo").val(data.usu_correo);
        $("#area_id").val(data.area_id);
        $("#rol_id").val(data.rol_id);
        $("#usu_pass").val('').prop('required', false); // Quita el required
        $("#pass_required").hide(); // Oculta el asterisco
        $("#pass_help").show(); // Muestra la ayuda
        $("#mnt_modal").modal('show');
    });
}

function eliminar(usu_id) {
    Swal.fire({
        title: "Esta seguro de eliminar el registro?",
        icon: "question",
        showDenyButton: true,
        confirmButtonText: "Si",
        denyButtonText: `No`
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/usuario.php?op=eliminar", { usu_id: usu_id }, function (data) {
                $("#listado_table").DataTable().ajax.reload();
                Swal.fire({
                    title: "Eliminar",
                    html: "Se elimino con exito.",
                    icon: "success",
                    confirmButtonColor: "#5156be",
                });
            });
        }
    });
}

function permiso(usu_id) {

    tabla_permiso = $("#listado_table_permiso").dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        "ajax": {
            url: '../../controller/area.php?op=permiso',
            type: "post",
            data: { usu_id: usu_id },
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 15,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    }).DataTable();

    $("#mnt_modal_permiso").modal('show');
}

function habilitar(aread_id) {
    $.post("../../controller/area.php?op=habilitar", { aread_id: aread_id }, function (data) {
        $("#listado_table_permiso").DataTable().ajax.reload();
    });
}

function deshabilitar(aread_id) {
    $.post("../../controller/area.php?op=deshabilitar", { aread_id: aread_id }, function (data) {
        $("#listado_table_permiso").DataTable().ajax.reload();
    });
}

init();