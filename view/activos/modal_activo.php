<!-- Modal para el registro y edición de vehículos -->
<div id="mnt_modal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">

        <!-- Formulario para el registro o edición de vehículos -->
        <form method="post" id="mnt_form">
            <div class="modal-content">

                <!-- Encabezado del modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Registro de Activo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="modal-body">

                    <input type="hidden" id="vehiculo_id" name="vehiculo_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_sbn" class="form-label">SBN (*)</label>
                            <input class="form-control" type="text" name="vehiculo_sbn" id="vehiculo_sbn" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_serie" class="form-label">Serie (*)</label>
                            <input class="form-control" type="text" name="vehiculo_serie" id="vehiculo_serie" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_tipo" class="form-label">Tipo (*)</label>
                            <input class="form-control" type="text" name="vehiculo_tipo" id="vehiculo_tipo" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_marca" class="form-label">Año (*)</label>
                            <input class="form-control" type="text" name="vehiculo_marca" id="vehiculo_marca" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_modelo" class="form-label">Modelo</label>
                            <input class="form-control" type="text" name="vehiculo_modelo" id="vehiculo_modelo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_ubicacion" class="form-label">Ubicación</label>
                            <input class="form-control" type="text" name="vehiculo_ubicacion" id="vehiculo_ubicacion">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_responsable_id" class="form-label">Responsable</label>
                            <input class="form-control" type="text" name="vehiculo_responsable_id" id="vehiculo_responsable_id">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_fecha_registro" class="form-label">Fecha de registro</label>
                            <input class="form-control" type="text" name="vehiculo_fecha_registro" id="vehiculo_fecha_registro">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_condicion" class="form-label">Condición</label>
                            <input class="form-control" type="text" name="vehiculo_condicion" id="vehiculo_condicion">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_estado" class="form-label">Estado</label>
                            <select class="form-control" name="vehiculo_estado" id="vehiculo_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Galería de imágenes -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Galería de Fotos</h5>
                            <div id="galeria_fotos" class="row g-2"></div>
                        </div>
                    </div>

                    <!-- Modal para mostrar imagen en grande -->
                    <div class="modal fade" id="modalZoomImagen" tabindex="-1" aria-labelledby="modalZoomImagenLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalZoomImagenLabel">Vista de Imagen</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="imagenZoom" class="img-fluid rounded shadow-lg" style="max-height: 80vh;" alt="Imagen ampliada">
                                </div>
                            </div>
                        </div>
                    </div>


                </div> <!-- Fin del cuerpo del modal -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar</button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#mnt_modal').on('shown.bs.modal', function() {
            var vehiculo_id = $('#vehiculo_id').val();
            if (vehiculo_id) {
                cargarFotos(vehiculo_id);
            }
        });

        function cargarFotos(vehiculo_id) {
            $.ajax({
                url: 'backend/obtener_fotos.php', // Cambia esto según tu estructura backend
                type: 'GET',
                data: {
                    vehiculo_id: vehiculo_id
                },
                dataType: 'json',
                success: function(response) {
                    $('#galeria_fotos').empty();
                    if (response.length > 0) {
                        response.forEach(function(foto) {
                            $('#galeria_fotos').append(`
                            <div class="col-md-3 mb-3">
                                <img src="${foto.foto_url}" class="img-fluid rounded shadow" alt="Foto del activo">
                            </div>
                        `);
                        });
                    } else {
                        $('#galeria_fotos').html('<p class="text-muted">No hay fotos disponibles.</p>');
                    }
                }
            });
        }
    });
</script>