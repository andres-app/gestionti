<div class="modal fade" id="modalMantenimiento" tabindex="-1" aria-labelledby="modalMantenimientoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg rounded-4">
      <div class="modal-header bg-gradient bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-semibold text-white" id="modalMantenimientoLabel">
          <i class="fas fa-tools me-2"></i> Registro de Mantenimientos
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body px-4 pb-4">

        <form id="form_mantenimiento" class="needs-validation" novalidate enctype="multipart/form-data">
          <input type="hidden" id="mantenimiento_activo_id" name="activo_id">

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label for="fecha" class="form-label fw-medium">Fecha del mantenimiento <span class="text-danger">*</span></label>
              <input type="date" name="fecha" id="fecha" class="form-control rounded-pill" required>
            </div>
            <div class="col-md-8">
              <label for="proveedor" class="form-label fw-medium">Proveedor</label>
              <input type="text" name="proveedor" id="proveedor" class="form-control rounded-pill" placeholder="Ej. SERVITEC, INFORMAT">
            </div>
          </div>

          <div class="mb-4">
            <label for="detalle" class="form-label fw-medium">Detalle del mantenimiento <span class="text-danger">*</span></label>
            <textarea name="detalle" id="detalle" rows="3" class="form-control rounded-3" placeholder="Descripción técnica, repuestos, diagnóstico..." required></textarea>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label for="orden_servicio" class="form-label fw-medium">📄 Orden de Servicio</label>
              <input type="file" name="orden_servicio" id="orden_servicio" class="form-control rounded-pill" accept=".pdf,.jpg,.png,.doc,.docx">
            </div>
            <div class="col-md-6">
              <label for="documento_conformidad" class="form-label fw-medium">📄 Documento de Conformidad</label>
              <input type="file" name="documento_conformidad" id="documento_conformidad" class="form-control rounded-pill" accept=".pdf,.jpg,.png,.doc,.docx">
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4">
              <i class="fas fa-save me-2"></i>Registrar Mantenimiento
            </button>
          </div>

        </form>

        <hr class="my-4">

        <h6 class="text-secondary fw-semibold mb-3">🕒 Historial de Mantenimientos</h6>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle table-bordered rounded shadow-sm">
            <thead class="table-light text-center">
              <tr class="align-middle">
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Proveedor</th>
                <th>Detalle</th>
                <th>Orden Servicio</th>
                <th>Conformidad</th>
              </tr>
            </thead>
            <tbody id="mantenimientos_body">
              <tr>
                <td colspan="4" class="text-center text-muted">Sin registros</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>