<?php
use App\Config\Auth;
use App\Enums\EstadoPeriodo;
use App\Enums\TipoPeriodo;
include APP_VIEWS_DIR . '/inc/header.php';

$ultimoPeriodo = count($periodos) > 0 ? $periodos[0] : null;
$ultimoFinStr = $ultimoPeriodo ? $ultimoPeriodo->fecha_fin->format('Y-m-d') : date('Y-m-d'); ?>

<div class="ne-page-header">
    <div>
        <h1>Períodos de Nómina</h1>
        <div class="subtitle"><?= count($periodos) ?> períodos registrados</div>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-grid ne-grid--2">
    <?php if (Auth::user()->puedeEditar()): ?>
    <div class="ne-card">
        <div class="ne-card__title">Crear período</div>
        <form method="post" action="/periodos">
            <div class="ne-form-group">
                <label class="ne-form-label">Nombre *</label>
                <input type="text" name="nombre" class="ne-form-control" placeholder="Ej: 1ra Quincena Mayo 2026" required>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Tipo *</label>
                <select name="tipo" class="ne-form-control">
                    <?php foreach (TipoPeriodo::cases() as $t): ?>
                        <option value="<?= $t->value ?>" <?= $t === TipoPeriodo::QUINCENAL ? 'selected' : '' ?>><?= $t->label() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ne-form-row">
                <div class="ne-form-group">
                    <label class="ne-form-label">Inicio *</label>
                    <input type="date" name="fecha_inicio" class="ne-form-control" required>
                </div>
                <div class="ne-form-group">
                    <label class="ne-form-label">Fin *</label>
                    <input type="date" name="fecha_fin" class="ne-form-control" required>
                </div>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Fecha de pago *</label>
                <input type="date" name="fecha_pago" class="ne-form-control" required>
            </div>
            <button class="ne-btn ne-btn--primary"><i class="bi bi-plus-lg"></i> Crear</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="ne-card">
        <div class="ne-card__title">Listado</div>
        <table class="ne-table">
            <thead><tr><th>Período</th><th>Tipo</th><th>Inicio</th><th>Fin</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($periodos as $p): ?>
                <tr style="vertical-align: middle;">
                    <td><strong><?= htmlspecialchars($p->nombre) ?></strong></td>
                    <td><?= $p->tipo->label() ?></td>
                    <td><?= $p->fecha_inicio->format('d/m/Y') ?></td>
                    <td><?= $p->fecha_fin->format('d/m/Y') ?></td>
                    <td><span class="ne-badge ne-badge--<?= $p->estado->badge() ?>"><?= $p->estado->label() ?></span></td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <?php if ($p->estado === EstadoPeriodo::ABIERTO): ?>
                                <a href="/nomina/procesar/<?= $p->id ?>" class="ne-btn ne-btn--sm ne-btn--primary"><i class="bi bi-gear"></i> Procesar</a>
                            <?php else: ?>
                                <a href="/nomina/procesar/<?= $p->id ?>" class="ne-btn ne-btn--sm"><i class="bi bi-eye"></i> Ver Detalles</a>
                            <?php endif; ?>
                            <?php if ($p->estado === EstadoPeriodo::ABIERTO && Auth::user()->puedeEditar()): ?>
                                <form method="post" action="/periodos/<?= $p->id ?>/cerrar" style="display:inline; margin: 0;">
                                    <button class="ne-btn ne-btn--sm ne-btn--secondary"
                                            data-confirm="¿Cerrar y marcar como PAGADO el período? Asegúrate de haber procesado a todos.">Cerrar y Pagar</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ultimoFinStr = <?= json_encode($ultimoFinStr) ?>;
    const tipoSelect = document.querySelector('select[name="tipo"]');
    const inputNombre = document.querySelector('input[name="nombre"]');
    const inputInicio = document.querySelector('input[name="fecha_inicio"]');
    const inputFin = document.querySelector('input[name="fecha_fin"]');
    const inputPago = document.querySelector('input[name="fecha_pago"]');
    
    if (!tipoSelect || !inputNombre) return;

    function formatLpad(num) {
        return num.toString().padStart(2, '0');
    }

    function calcularSiguientePeriodo() {
        const tipo = tipoSelect.value;
        // Parse date using string to avoid timezone issues:
        const [year, month, day] = ultimoFinStr.split('-');
        let fechaInicio = new Date(year, month - 1, day);
        fechaInicio.setDate(fechaInicio.getDate() + 1);
        
        let fechaFin = new Date(fechaInicio);
        let nombre = '';

        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        if (tipo === 'quincenal') {
            if (fechaInicio.getDate() <= 15) {
                fechaInicio.setDate(1);
                fechaFin = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth(), 15);
                nombre = `1ra Quincena ${meses[fechaInicio.getMonth()]} ${fechaInicio.getFullYear()}`;
            } else {
                fechaInicio.setDate(16);
                fechaFin = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth() + 1, 0);
                nombre = `2da Quincena ${meses[fechaInicio.getMonth()]} ${fechaInicio.getFullYear()}`;
            }
        } else if (tipo === 'mensual') {
            fechaInicio.setDate(1);
            fechaFin = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth() + 1, 0);
            nombre = `Mes de ${meses[fechaInicio.getMonth()]} ${fechaInicio.getFullYear()}`;
        } else if (tipo === 'semanal') {
            fechaFin.setDate(fechaInicio.getDate() + 6);
            nombre = `Semana del ${formatLpad(fechaInicio.getDate())} al ${formatLpad(fechaFin.getDate())} ${meses[fechaFin.getMonth()]} ${fechaFin.getFullYear()}`;
        }

        const inicioStr = `${fechaInicio.getFullYear()}-${formatLpad(fechaInicio.getMonth()+1)}-${formatLpad(fechaInicio.getDate())}`;
        const finStr = `${fechaFin.getFullYear()}-${formatLpad(fechaFin.getMonth()+1)}-${formatLpad(fechaFin.getDate())}`;
        
        inputInicio.value = inicioStr;
        inputFin.value = finStr;
        inputNombre.value = nombre;
        inputPago.value = finStr;
    }

    tipoSelect.addEventListener('change', calcularSiguientePeriodo);
    calcularSiguientePeriodo();
});
</script>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
