<!-- Modal de Historial de Activo -->
<div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-gradient bg-primary text-white">
        <h5 class="modal-title text-white" id="modalHistorialLabel">
          <i class="fas fa-history me-2"></i>Historial del Activo
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th><i class="fas fa-calendar-alt me-1 text-muted"></i>Fecha</th>
                <th><i class="fas fa-user me-1 text-muted"></i>Usuario</th>
                <th><i class="fas fa-tasks me-1 text-muted"></i>Acción</th>
                <th><i class="fas fa-info-circle me-1 text-muted"></i>Detalle</th>
              </tr>
            </thead>
            <tbody id="historial_body">
              <!-- Contenido dinámico vía JS -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
