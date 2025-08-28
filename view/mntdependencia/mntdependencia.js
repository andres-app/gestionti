// views/mntdependencia.js
var tabla;

$(function () {
  init();
});

function init() {
  // Botón "Nuevo Registro"
  $("#btnnuevo").on("click", function () {
    limpiar();
    setModalTitle("Nuevo registro");
    $("#mnt_modal").modal("show");
  });

  // Submit del formulario
  $("#mnt_form").on("submit", function (e) {
    guardaryeditar(e);
  });

  // DataTable
  listar();
}

// Helper: setear título del modal (usa el id real del H5 en el modal)
function setModalTitle(txt){
  $("#myModalLabel").text(txt); // asegúrate que tu modal tenga este id en el <h5>
}

function listar() {
  tabla = $("#listado_table").DataTable({
    aProcessing: true,
    aServerSide: false,
    responsive: true,
    autoWidth: false,
    destroy: true,
    ajax: {
      url: "../../controller/dependencia.php?op=listar",
      type: "POST",
      dataSrc: function (json) {
        return json.aaData || [];
      },
      error: function (xhr) {
        console.error(xhr.responseText);
        Swal.fire("Error", "No se pudo cargar el listado.", "error");
      },
    },
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
    columnDefs: [
      { targets: -1, orderable: false, searchable: false },
    ],
  });
}

function guardaryeditar(e) {
  e.preventDefault();

  // Validación simple
  const nom = ($("#dependencia_nom").val() || "").trim();
  if (!nom) {
    $("#dependencia_nom").addClass("is-invalid").focus();
    return;
  } else {
    $("#dependencia_nom").removeClass("is-invalid");
  }

  const $submitBtn = $("#mnt_form button[type=submit]");
  let formData = new FormData($("#mnt_form")[0]);

  $submitBtn.prop("disabled", true);

  $.ajax({
    url: "../../controller/dependencia.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (r) {
      $submitBtn.prop("disabled", false);

      if (r == 1 || r === "1") {
        $("#mnt_modal").modal("hide");
        Swal.fire({ title: "Registro", html: "Se registró con éxito.", icon: "success", confirmButtonColor: "#5156be" });
        $("#listado_table").DataTable().ajax.reload();
      } else if (r == 2 || r === "2") {
        $("#mnt_modal").modal("hide");
        Swal.fire({ title: "Actualización", html: "Se actualizó con éxito.", icon: "success", confirmButtonColor: "#5156be" });
        $("#listado_table").DataTable().ajax.reload();
      } else if (r == 0 || r === "0") {
        Swal.fire({ title: "Duplicado", html: "La dependencia ya existe. Verifique.", icon: "warning", confirmButtonColor: "#5156be" });
      } else {
        console.warn("Respuesta no esperada:", r);
        Swal.fire({ title: "Error", html: "Ocurrió un problema al guardar.", icon: "error", confirmButtonColor: "#5156be" });
      }
    },
    error: function (xhr) {
      $submitBtn.prop("disabled", false);
      console.error(xhr.responseText);
      Swal.fire({ title: "Error", html: "No se pudo procesar la solicitud.", icon: "error", confirmButtonColor: "#5156be" });
    },
  });
}

function editar(dependencia_id) {
  $.post(
    "../../controller/dependencia.php?op=mostrar",
    { dependencia_id: dependencia_id },
    function (data) {
      try {
        if (typeof data === "string") data = JSON.parse(data);
      } catch (e) {
        console.error("JSON inválido en mostrar:", data);
        Swal.fire("Error", "No se pudo obtener el registro.", "error");
        return;
      }

      limpiar();
      $("#dependencia_id").val(data.dependencia_id);
      $("#dependencia_nom").val(data.dependencia_nom);

      setModalTitle("Editar registro");
      $("#mnt_modal").modal("show");
    }
  ).fail(function (xhr) {
    console.error(xhr.responseText);
    Swal.fire("Error", "No se pudo obtener el registro.", "error");
  });
}

function eliminar(dependencia_id) {
  Swal.fire({
    title: "¿Eliminar?",
    text: "Esta acción no se puede deshacer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "../../controller/dependencia.php?op=eliminar",
        { dependencia_id: dependencia_id },
        function (r) {
          if (r == 1 || r === "1") {
            Swal.fire("Eliminado", "El registro ha sido eliminado.", "success");
            $("#listado_table").DataTable().ajax.reload();
          } else {
            Swal.fire("Error", "No se pudo eliminar el registro.", "error");
          }
        }
      ).fail(function (xhr) {
        console.error(xhr.responseText);
        Swal.fire("Error", "No se pudo eliminar el registro.", "error");
      });
    }
  });
}

function limpiar() {
  $("#dependencia_id").val("");
  $("#dependencia_nom").val("").removeClass("is-invalid");
}
