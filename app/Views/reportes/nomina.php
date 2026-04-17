<?php include APP_VIEWS_DIR . '/inc/header.php'; ?>

<div class="ne-page-header">
    <div>
        <h1>Reporte de Nómina por Período</h1>
        <div class="subtitle">Totales calculados con Suma Iterativa · Orden vía QuickSort</div>
    </div>
</div>

<div class="ne-card mb-3">
    <form method="get">
        <div class="d-flex gap-2 align-center">
            <label class="ne-form-label mb-0">Período:</label>
            <select name="periodo_id" class="ne-form-control" style="max-width: 400px;" onchange="this.form.submit()">
                <?php foreach ($periodos as $p): ?>
                    <option value="<?= $p->id ?>" <?= $periodo && $periodo->id === $p->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p->nombre) ?> — <?= $p->estado->label() ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if ($periodo && count($nominas) > 0): ?>
<table class="ne-table">
    <thead>
        <tr>
            <th>Empleado</th>
            <th>Departamento</th>
            <th class="text-end">Salario Base</th>
            <th class="text-end">Ingresos</th>
            <th class="text-end">Deducciones</th>
            <th class="text-end">Neto</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nominas as $n): ?>
        <tr>
            <td><?= htmlspecialchars($n->empleado->getNombreCompleto()) ?></td>
            <td><?= htmlspecialchars($n->empleado->departamento->nombre) ?></td>
            <td class="text-end">RD$ <?= number_format((float)$n->salario_base, 2) ?></td>
            <td class="text-end">RD$ <?= number_format((float)$n->total_ingresos, 2) ?></td>
            <td class="text-end">RD$ <?= number_format((float)$n->total_deducciones, 2) ?></td>
            <td class="text-end"><strong>RD$ <?= number_format((float)$n->salario_neto, 2) ?></strong></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">TOTALES</td>
            <td class="text-end">RD$ <?= number_format($totales['base'], 2) ?></td>
            <td class="text-end">RD$ <?= number_format($totales['ingresos'], 2) ?></td>
            <td class="text-end">RD$ <?= number_format($totales['deducciones'], 2) ?></td>
            <td class="text-end">RD$ <?= number_format($totales['neto'], 2) ?></td>
        </tr>
    </tfoot>
</table>
<?php else: ?>
    <div class="ne-alert ne-alert--info">No hay nóminas procesadas para este período. Procéselo primero desde el módulo de nómina.</div>
<?php endif; ?>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
