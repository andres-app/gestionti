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
                            <select class="form-control" name="vehiculo_tipo" id="vehiculo_tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="CPU">CPU</option>
                                <option value="TECLADO">TECLADO</option>
                                <option value="MONITOR">MONITOR</option>
                                <option value="IMPRESORA">IMPRESORA</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_marca" class="form-label">Marca (*)</label>
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
                            <select class="form-control" id="vehiculo_responsable_id" name="vehiculo_responsable_id" required>
                                <option value="">Seleccione un responsable</option>
                            </select>
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
                    <!-- 🔹 Hostname y Procesador en una fila -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_hostname" class="form-label">Hostname</label>
                            <input class="form-control" type="text" name="vehiculo_hostname" id="vehiculo_hostname">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_procesador" class="form-label">Procesador</label>
                            <input class="form-control" type="text" name="vehiculo_procesador" id="vehiculo_procesador">
                        </div>
                    </div>


                    <!-- 🔹 Sistema Operativo y RAM en otra fila -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_sisopera" class="form-label">Sistema Operativo</label>
                            <input class="form-control" type="text" name="vehiculo_sisopera" id="vehiculo_sisopera">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_ram" class="form-label">RAM</label>
                            <input class="form-control" type="text" name="vehiculo_ram" id="vehiculo_ram">
                        </div>
                    </div>

                    <!-- 🔹 Disco en una fila separada -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_disco" class="form-label">Discoss</label>
                            <input class="form-control" type="text" name="vehiculo_disco" id="vehiculo_disco">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_ult_mant" class="form-label">Último Mantenimiento</label>
                            <input class="form-control" type="date" name="vehiculo_ult_mant" id="vehiculo_ult_mant">
                        </div>
                    </div>



                    <!-- Galería de imágenes en Carrusel -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Galería de Fotos</h5>
                            <div id="carouselGaleria" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner" id="galeria_fotos"></div>

                                <!-- Botones de navegación del carrusel -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleria" data-bs-slide="prev" style="filter: invert(0%);">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Anterior</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleria" data-bs-slide="next" style="filter: invert(0%);">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Siguiente</span>
                                </button>
                            </div>
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