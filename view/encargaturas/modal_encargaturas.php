<div class="modal fade" id="modalEncargatura" tabindex="-1" role="dialog" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form_encargatura" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Registrar Encargatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <!-- Responsable del registro -->
                    <div class="mb-3">
                        <label class="form-label">Responsable del registro</label>
                        <input type="text" class="form-control" value="<?= $_SESSION['usu_nomape']; ?>" readonly>
                    </div>

                    <!-- GLPI -->
                    <div class="col-md-6 mb-3">
                        <label for="activo_id" class="form-label">GLPI</label>
                        <input type="text" id="glpi" name="glpi" class="form-control" required>
                    </div>

                    <!-- Titular y Encargado en una fila -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="titular" class="form-label">Titular</label>
                            <input type="text" id="titular" name="titular" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="encargado" class="form-label">Encargado (*)</label>
                            <input type="text" id="encargado" name="encargado" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha inicio -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                        </div>

                        <!-- Fecha fin estimada -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha_finalizacion" class="form-label">Fecha de finalización</label>
                            <input type="date" id="fecha_finalizacion" name="fecha_finalizacion" class="form-control">
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="3" placeholder="Ej. Nignuno"></textarea>
                    </div>

                    <!-- Campo oculto para edición -->
                    <input type="hidden" id="encargatura_id" name="encargatura_id">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Encargatura</button>
                </div>
            </div>
        </form>
    </div>
</div>