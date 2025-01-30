<!-- Modal para el registro y edición de vehículos -->
<div id="mnt_modal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- modal-xl para hacer el modal más grande -->
        
        <!-- Formulario para el registro o edición de vehículos -->
        <form method="post" id="mnt_form">
            <div class="modal-content">

                <!-- Encabezado del modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Registro de Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Cuerpo del modal -->
                <div class="modal-body">

                    <!-- Campo oculto para el ID del vehículo (se usa en caso de edición) -->
                    <input type="hidden" id="vehiculo_id" name="vehiculo_id">

                    <!-- Fila 1: sbn y serie del vehículo -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_sbn" class="form-label">SBN (*)</label>
                            <input class="form-control" type="text" name="vehiculo_sbn" id="vehiculo_sbn" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_serie" class="form-label">serie (*)</label>
                            <input class="form-control" type="text" name="vehiculo_serie" id="vehiculo_serie" required>
                        </div>
                    </div>

                    <!-- Fila 2: tipo y Año del vehículo -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_tipo" class="form-label">tipo (*)</label>
                            <input class="form-control" type="text" name="vehiculo_tipo" id="vehiculo_tipo" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_marca" class="form-label">Año (*)</label>
                            <input class="form-control" type="number" name="vehiculo_marca" id="vehiculo_marca" required>
                        </div>
                    </div>

                    <!-- Fila 3: modelo y ubicacion del vehículo -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_modelo" class="form-label">modelo</label>
                            <input class="form-control" type="text" name="vehiculo_modelo" id="vehiculo_modelo">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_ubicacion" class="form-label">Ubicacion</label>
                            <input class="form-control" type="text" name="vehiculo_ubicacion" id="vehiculo_ubicacion">
                        </div>
                    </div>

                    <!-- Fila 4: Tipo de responsable_id y Tipo de vehículo -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_responsable_id" class="form-label">responsable_id</label>
                            <select class="form-control" name="vehiculo_responsable_id" id="vehiculo_responsable_id">
                                <option value="Gasolina">Gasolina</option>
                                <option value="Diesel">Diesel</option>
                                <option value="Gas">Gas</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_tipo" class="form-label">Tipo de Vehículo</label>
                            <select class="form-control" name="vehiculo_tipo" id="vehiculo_tipo">
                            <option value="Camioneta">Camioneta</option>
                                <option value="Sedán">Sedán</option>
                                <option value="Hatchback">Hatchback</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 5: Póliza del vehículo y estado (activo/inactivo) -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vehiculo_condicion" class="form-label">Póliza</label>
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

                </div> <!-- Fin del cuerpo del modal -->

                <!-- Pie del modal (botones de acción) -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>
