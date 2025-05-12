<?php require_once("../html/head.php"); ?>

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">Registro de Inventario 2025</h5>
            <div class="d-flex align-items-center gap-2 flex-nowrap">
                <button id="btn_escanear"
                    class="btn btn-light border-primary text-primary fw-semibold px-3 py-2 d-flex align-items-center rounded-pill shadow-sm">
                    <i class="fas fa-qrcode me-2"></i> Escanear
                </button>
                <button id="btn_detener"
                    class="btn btn-outline-danger fw-semibold px-3 py-2 d-flex align-items-center rounded-pill shadow-sm d-none">
                    <i class="fas fa-stop-circle me-2"></i> Detener
                </button>
                <button id="btn_toggle_historial"
                    class="btn btn-light text-primary fw-semibold px-3 py-2 d-flex align-items-center rounded-pill shadow-sm">
                    <i class="fas fa-clock-rotate-left me-2"></i> Historial
                </button>

            </div>

        </div>

        <div class="card-body">
            <!-- Cámara -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="ratio ratio-16x9 border border-secondary rounded shadow-sm d-none" id="preview">
                        <!-- QuaggaJS insertará el video aquí -->
                    </div>
                    <small class="text-muted d-block text-center mt-2">Escanea el código QR o de barras del
                        activo</small>
                </div>
            </div>

            <!-- Formulario escaneado -->
            <form id="form_inventario" class="d-none">
                <input type="hidden" id="codigo_activo" name="codigo_activo">

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Datos del Activo Escaneado</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php
                            $campos = [
                                'sbn',
                                'serie',
                                'tipo',
                                'marca',
                                'modelo',
                                'hostname',
                                'procesador',
                                'ram',
                                'disco',
                                'sisopera',
                                'responsable',
                                'ubicacion',
                                'sede',
                                'condicion',
                                'acompra',
                                'ult_mant',
                                'fecha_registro'
                            ];
                            foreach ($campos as $campo) {
                                $label = ucwords(str_replace('_', ' ', $campo));
                                $readonly = $campo !== 'serie' ? 'readonly' : '';
                                echo "<div class='col-md-6'>
                                        <label for='{$campo}'>{$label}</label>
                                        <input type='" . ($campo === 'ult_mant' ? 'date' : 'text') . "' class='form-control bg-light' id='{$campo}' {$readonly}>
                                      </div>";
                            }
                            ?>
                            <div class="col-md-12">
                                <label for="observaciones">Observaciones</label>
                                <textarea id="observaciones" class="form-control bg-light" rows="2" readonly></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-1"></i> Registrar Inventario
                    </button>
                </div>
            </form>

            <!-- Historial toggleado -->
            <div id="historial_wrapper" class="card mt-4 d-none">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Historial de Escaneos</h6>
                    <small class="text-muted">Últimos registros</small>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-hover table-bordered align-middle" id="tabla_historial">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Estado</th>
                                <th>Responsable</th>
                                <th>Ubicación</th>
                                <th>Observación</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Llenado dinámico -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("../html/js.php"); ?>
<script src="inventario.js"></script>