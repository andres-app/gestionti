var tabla;

function init(){
    $("#mnt_form").on("submit",function(e){
        guardaryeditar(e);
    });
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#mnt_form")[0]);
    $.ajax({
        url:"../../controller/sede.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            console.log(datos);
            if(datos == 1){
                $("#sede_id").val('');
                $("#mnt_form")[0].reset();
                $("#listado_table").DataTable().ajax.reload();
                $("#mnt_modal").modal('hide');
                Swal.fire({
                    title: "Sede",
                    html: "Se registró con éxito.",
                    icon: "success",
                    confirmButtonColor: "#5156be",
                });
            }else if(datos == 0){
                Swal.fire({
                    title: "Sede",
                    html: "Registro ya existe, por favor validar.",
                    icon: "error",
                    confirmButtonColor: "#5156be",
                });
            }else if(datos == 2){
                $("#sede_id").val('');
                $("#mnt_form")[0].reset();
                $("#listado_table").DataTable().ajax.reload();
                $("#mnt_modal").modal('hide');
                Swal.fire({
                    title: "Sede",
                    html: "Se actualizó con éxito.",
                    icon: "success",
                    confirmButtonColor: "#5156be",
                });
            }
        }
    });
}

$(document).ready(function(){ 

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
        "ajax":{
            url: '../../controller/sede.php?op=listar',
            type : "get",
            dataType : "json",
            error:function(e){
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo":true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    }).DataTable();

});

$(document).on("click","#btnnuevo",function(){
    $("#sede_id").val('');
    $("#mnt_form")[0].reset();
    $("#myModalLabel").html('Nuevo Registro');
    $("#mnt_modal").modal('show');
});

function editar(sede_id){
    $("#myModalLabel").html('Editar Registro');
    $.post("../../controller/sede.php?op=mostrar",{sede_id:sede_id},function(data){
        data=JSON.parse(data);
        $("#sede_id").val(data.sede_id);
        $("#sede_nom").val(data.sede_nom);
        // Si tienes más campos, agrégalos aquí
        $("#mnt_modal").modal('show');
    });
}

function eliminar(sede_id){
    Swal.fire({
        title: "¿Está seguro de eliminar el registro?",
        icon: "question",
        showDenyButton: true,
        confirmButtonText: "Sí",
        denyButtonText: `No`
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/sede.php?op=eliminar",{sede_id:sede_id},function(data){
                $("#listado_table").DataTable().ajax.reload();
                Swal.fire({
                    title: "Sede",
                    html: "Se eliminó con éxito.",
                    icon: "success",
                    confirmButtonColor: "#5156be",
                });
            });
        }
    });
}

init();
