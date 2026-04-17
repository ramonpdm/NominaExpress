<?php include APP_VIEWS_DIR . '/inc/header.php'; ?>

<div class="ne-page-header">
    <div>
        <h1>Reporte por Departamento</h1>
        <div class="subtitle">Totales calculados con algoritmo recursivo sobre el organigrama (unidad 2)</div>
    </div>
</div>

<div class="ne-alert ne-alert--info">
    <strong><i class="bi bi-info-circle"></i> Recursión aplicada:</strong>
    este reporte usa el algoritmo <code>Organigrama::costoNominaTotal()</code> y <code>Organigrama::contarEmpleados()</code>,
    que descienden recursivamente por el árbol de departamentos sumando los empleados de cada subdepartamento.
</div>

<table class="ne-table">
    <thead>
        <tr>
            <th>Departamento</th>
            <th class="text-end">Empleados (incl. subdeptos)</th>
            <th class="text-end">Costo total nómina</th>
            <th class="text-end">Promedio por empleado</th>
        </tr>
    </thead>
    <tbody>
        <?php $totalEmp = 0; $totalCosto = 0; foreach ($filas as $f):
            $totalEmp += $f['empleados']; $totalCosto += $f['costo_total'];
        ?>
        <tr>
            <td><strong><?= htmlspecialchars($f['depto']->nombre) ?></strong><br><small class="text-muted"><?= htmlspecialchars($f['depto']->descripcion ?? '') ?></small></td>
            <td class="text-end"><?= $f['empleados'] ?></td>
            <td class="text-end">RD$ <?= number_format($f['costo_total'], 2) ?></td>
            <td class="text-end">RD$ <?= $f['empleados'] > 0 ? number_format($f['costo_total'] / $f['empleados'], 2) : '0.00' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td>TOTAL EMPRESA</td>
            <td class="text-end"><?= $totalEmp ?></td>
            <td class="text-end">RD$ <?= number_format($totalCosto, 2) ?></td>
            <td class="text-end">RD$ <?= $totalEmp > 0 ? number_format($totalCosto / $totalEmp, 2) : '0.00' ?></td>
        </tr>
    </tfoot>
</table>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
