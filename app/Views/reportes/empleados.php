<?php
use App\Enums\EstadoEmpleado;
include APP_VIEWS_DIR . '/inc/header.php';
?>

<div class="ne-page-header">
    <div>
        <h1>Planilla de Empleados</h1>
        <div class="subtitle">Estadísticas calculadas con Media Aritmética / Min / Max (unidad 5)</div>
    </div>
</div>

<div class="ne-card mb-3">
    <form method="get" class="d-flex gap-2 align-center" style="flex-wrap: wrap;">
        <select name="estado" class="ne-form-control" style="max-width: 200px;">
            <option value="">— Todos los estados —</option>
            <?php foreach (EstadoEmpleado::cases() as $s): ?>
                <option value="<?= $s->value ?>" <?= ($_GET['estado'] ?? '') === $s->value ? 'selected' : '' ?>><?= $s->label() ?></option>
            <?php endforeach; ?>
        </select>
        <select name="departamento_id" class="ne-form-control" style="max-width: 260px;">
            <option value="">— Todos los departamentos —</option>
            <?php foreach ($departamentos as $d): ?>
                <option value="<?= $d->id ?>" <?= (int)($_GET['departamento_id'] ?? 0) === $d->id ? 'selected' : '' ?>><?= htmlspecialchars($d->nombre) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="orden" class="ne-form-control" style="max-width: 200px;">
            <option value="nombres">Orden: nombre</option>
            <option value="salario" <?= ($_GET['orden'] ?? '') === 'salario' ? 'selected' : '' ?>>Orden: salario</option>
            <option value="cedula" <?= ($_GET['orden'] ?? '') === 'cedula' ? 'selected' : '' ?>>Orden: cédula</option>
        </select>
        <button class="ne-btn ne-btn--primary">Filtrar</button>
    </form>
</div>

<div class="ne-grid ne-grid--4 mb-3">
    <div class="ne-kpi"><div class="ne-kpi__label">Total</div><div class="ne-kpi__value"><?= $estadisticas['total'] ?></div></div>
    <div class="ne-kpi ne-kpi--success"><div class="ne-kpi__label">Salario promedio</div><div class="ne-kpi__value">RD$ <?= number_format($estadisticas['promedio'], 0) ?></div></div>
    <div class="ne-kpi ne-kpi--warning"><div class="ne-kpi__label">Salario mínimo</div><div class="ne-kpi__value">RD$ <?= number_format($estadisticas['minimo'], 0) ?></div></div>
    <div class="ne-kpi ne-kpi--accent"><div class="ne-kpi__label">Salario máximo</div><div class="ne-kpi__value">RD$ <?= number_format($estadisticas['maximo'], 0) ?></div></div>
</div>

<table class="ne-table">
    <thead>
        <tr><th>Cédula</th><th>Nombre</th><th>Departamento</th><th>Cargo</th><th class="text-end">Salario</th><th>Estado</th></tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e->cedula) ?></td>
            <td><?= htmlspecialchars($e->getNombreCompleto()) ?></td>
            <td><?= htmlspecialchars($e->departamento->nombre) ?></td>
            <td><?= htmlspecialchars($e->cargo->nombre) ?></td>
            <td class="text-end">RD$ <?= number_format((float)$e->salario, 2) ?></td>
            <td><span class="ne-badge ne-badge--<?= $e->estado->badge() ?>"><?= $e->estado->label() ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
