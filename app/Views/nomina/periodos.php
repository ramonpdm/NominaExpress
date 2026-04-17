<?php
use App\Config\Auth;
use App\Enums\EstadoPeriodo;
use App\Enums\TipoPeriodo;
include APP_VIEWS_DIR . '/inc/header.php';
?>

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
                <tr>
                    <td><strong><?= htmlspecialchars($p->nombre) ?></strong></td>
                    <td><?= $p->tipo->label() ?></td>
                    <td><?= $p->fecha_inicio->format('d/m/Y') ?></td>
                    <td><?= $p->fecha_fin->format('d/m/Y') ?></td>
                    <td><span class="ne-badge ne-badge--<?= $p->estado->badge() ?>"><?= $p->estado->label() ?></span></td>
                    <td class="d-flex gap-2">
                        <a href="/nomina/procesar/<?= $p->id ?>" class="ne-btn ne-btn--sm ne-btn--primary"><i class="bi bi-gear"></i> Procesar</a>
                        <?php if ($p->estado === EstadoPeriodo::ABIERTO && Auth::user()->puedeEditar()): ?>
                            <form method="post" action="/periodos/<?= $p->id ?>/cerrar" style="display:inline;">
                                <button class="ne-btn ne-btn--sm ne-btn--secondary"
                                        data-confirm="¿Cerrar el período?">Cerrar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
