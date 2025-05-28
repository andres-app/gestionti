<div class="modal fade" id="modalPrestamo" tabindex="-1" role="dialog" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form_prestamo">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Registrar Préstamo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Responsable del préstamo</label>
                        <input type="text" class="form-control" value="<?php echo $_SESSION['usu_nomape']; ?>" readonly>
                    </div>

                    <!-- Activo OSIN -->
                    <div class="mb-3">
                        <label for="activo_id" class="form-label">Activo OSIN</label>
                        <select id="activo_id" name="activo_id" class="form-select" required>
                            <!-- sin option por defecto aquí -->
                        </select>
                    </div>

                    <!-- Usuario Destino -->
                    <div class="mb-3">
                        <label for="usuario_destino" class="form-label">Usuario Destino</label>
                        <select id="usuario_destino" name="usuario_destino" class="form-select" required>
                            <!-- sin option por defecto aquí -->
                        </select>
                    </div>

                    <!-- Fecha de Préstamo -->
                    <div class="mb-3">
                        <label for="fecha_prestamo" class="form-label">Fecha de Préstamo</label>
                        <input type="datetime-local" id="fecha_prestamo" name="fecha_prestamo" class="form-control" required>
                    </div>

                    <!-- Fecha Devolución Estimada -->
                    <div class="mb-3">
                        <label for="fecha_devolucion_estimada" class="form-label">Fecha Devolución Estimada</label>
                        <input type="date" id="fecha_devolucion_estimada" name="fecha_devolucion_estimada" class="form-control">
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Préstamo</button>
                </div>
            </div>
        </form>
    </div>
</div>