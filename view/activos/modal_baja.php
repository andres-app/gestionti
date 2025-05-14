<div class="modal fade" id="modalBajaActivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_baja_activo" enctype="multipart/form-data">
            <input type="hidden" name="activo_id" id="baja_activo_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Registrar Baja del Activo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Motivo de baja</label>
                    <textarea class="form-control" name="motivo" required></textarea>
                    <label class="mt-3">Documento de respaldo</label>
                    <input type="file" class="form-control" name="archivo" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Registrar Baja</button>
                </div>
            </div>
        </form>
    </div>
</div>