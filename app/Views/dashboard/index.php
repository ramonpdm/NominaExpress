<?php include APP_VIEWS_DIR . '/inc/header.php'; ?>

<div class="ne-page-header">
    <div>
        <h1>Dashboard</h1>
        <div class="subtitle">Visión general del sistema de nómina</div>
    </div>
    <div class="d-flex gap-2">
        <a href="/empleados/nuevo" class="ne-btn ne-btn--accent"><i class="bi bi-person-plus"></i> Nuevo empleado</a>
        <a href="/periodos" class="ne-btn ne-btn--primary"><i class="bi bi-calendar-plus"></i> Gestionar períodos</a>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-grid ne-grid--4 mb-3">
    <div class="ne-kpi">
        <div class="ne-kpi__label">Empleados Activos</div>
        <div class="ne-kpi__value"><?= $kpis['empleados'] ?></div>
        <div class="ne-kpi__extra">En TechSoft RD</div>
    </div>
    <div class="ne-kpi ne-kpi--accent">
        <div class="ne-kpi__label">Períodos Totales</div>
        <div class="ne-kpi__value"><?= $kpis['periodos'] ?></div>
        <div class="ne-kpi__extra">Creados históricamente</div>
    </div>
    <div class="ne-kpi ne-kpi--warning">
        <div class="ne-kpi__label">Períodos Abiertos</div>
        <div class="ne-kpi__value"><?= $kpis['abiertos'] ?></div>
        <div class="ne-kpi__extra">Pendientes de procesar</div>
    </div>
    <div class="ne-kpi ne-kpi--success">
        <div class="ne-kpi__label">Total Pagado</div>
        <div class="ne-kpi__value">RD$ <?= number_format($kpis['pagado'], 2) ?></div>
        <div class="ne-kpi__extra">Suma histórica de netos</div>
    </div>
</div>

<div class="ne-grid ne-grid--2">
    <div class="ne-card">
        <div class="ne-card__title">Últimos empleados registrados</div>
        <table class="ne-table">
            <thead>
                <tr><th>Nombre</th><th>Departamento</th><th>Ingreso</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosEmpleados as $e): ?>
                <tr>
                    <td><a href="/empleados/<?= $e->id ?>"><?= htmlspecialchars($e->getNombreCompleto()) ?></a></td>
                    <td><?= htmlspecialchars($e->departamento->nombre) ?></td>
                    <td><?= $e->fecha_ingreso->format('d/m/Y') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="ne-card">
        <div class="ne-card__title">Períodos recientes</div>
        <table class="ne-table">
            <thead>
                <tr><th>Período</th><th>Fechas</th><th>Estado</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosPeriodos as $p): ?>
                <tr>
                    <td><a href="/nomina/procesar/<?= $p->id ?>"><?= htmlspecialchars($p->nombre) ?></a></td>
                    <td><?= $p->fecha_inicio->format('d/m') ?> → <?= $p->fecha_fin->format('d/m/Y') ?></td>
                    <td><span class="ne-badge ne-badge--<?= $p->estado->badge() ?>"><?= $p->estado->label() ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
