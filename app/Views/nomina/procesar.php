<?php
use App\Config\Auth;
use App\Enums\EstadoPeriodo;
include APP_VIEWS_DIR . '/inc/header.php';
?>

<div class="ne-page-header">
    <div>
        <h1>Procesar Nómina</h1>
        <div class="subtitle">
            <?= htmlspecialchars($periodo->nombre) ?> ·
            <?= $periodo->fecha_inicio->format('d/m/Y') ?> → <?= $periodo->fecha_fin->format('d/m/Y') ?> ·
            <span class="ne-badge ne-badge--<?= $periodo->estado->badge() ?>"><?= $periodo->estado->label() ?></span>
        </div>
    </div>
    <?php if ($periodo->estado === EstadoPeriodo::ABIERTO && Auth::user()->puedeEditar()): ?>
    <form method="post" action="/nomina/procesar/<?= $periodo->id ?>">
        <button class="ne-btn ne-btn--accent" data-confirm="¿Procesar la nómina de todos los empleados activos pendientes?">
            <i class="bi bi-lightning-fill"></i> Procesar todos
        </button>
    </form>
    <?php endif; ?>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-grid ne-grid--4 mb-3">
    <div class="ne-kpi">
        <div class="ne-kpi__label">Procesados</div>
        <div class="ne-kpi__value"><?= $totales['procesados'] ?> / <?= $totales['total'] ?></div>
    </div>
    <div class="ne-kpi ne-kpi--accent">
        <div class="ne-kpi__label">Salarios base</div>
        <div class="ne-kpi__value">RD$ <?= number_format($totales['base'], 2) ?></div>
    </div>
    <div class="ne-kpi ne-kpi--warning">
        <div class="ne-kpi__label">Deducciones</div>
        <div class="ne-kpi__value">RD$ <?= number_format($totales['deducciones'], 2) ?></div>
    </div>
    <div class="ne-kpi ne-kpi--success">
        <div class="ne-kpi__label">Total neto</div>
        <div class="ne-kpi__value">RD$ <?= number_format($totales['neto'], 2) ?></div>
    </div>
</div>

<table class="ne-table" id="tabla-procesar">
    <thead>
        <tr><th>Empleado</th><th>Departamento</th><th class="text-end">Salario</th><th class="text-end">Neto</th><th>Estado</th><th></th></tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $e):
            $nomina = $procesados[$e->id] ?? null;
        ?>
        <tr>
            <td><strong><?= htmlspecialchars($e->getNombreCompleto()) ?></strong><br><small class="text-muted"><?= htmlspecialchars($e->cargo->nombre) ?></small></td>
            <td><?= htmlspecialchars($e->departamento->nombre) ?></td>
            <td class="text-end">RD$ <?= number_format((float)$e->salario, 2) ?></td>
            <td class="text-end">
                <?= $nomina ? 'RD$ ' . number_format((float)$nomina->salario_neto, 2) : '—' ?>
            </td>
            <td>
                <?php if ($nomina): ?>
                    <span class="ne-badge ne-badge--success">Procesado</span>
                <?php else: ?>
                    <span class="ne-badge ne-badge--secondary">Pendiente</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($nomina): ?>
                    <a href="/nomina/comprobante/<?= $nomina->id ?>" class="ne-btn ne-btn--sm ne-btn--primary">
                        <i class="bi bi-receipt"></i> Comprobante
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
