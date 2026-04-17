<?php
use App\Config\Auth;
include APP_VIEWS_DIR . '/inc/header.php';
?>

<div class="ne-page-header">
    <div>
        <h1>Empleados</h1>
        <div class="subtitle"><?= count($empleados) ?> registrados · ordenado por <?= htmlspecialchars($ordenActual) ?> vía QuickSort</div>
    </div>
    <?php if (Auth::user()->puedeEditar()): ?>
        <a href="/empleados/nuevo" class="ne-btn ne-btn--accent"><i class="bi bi-person-plus"></i> Nuevo empleado</a>
    <?php endif; ?>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-card mb-3">
    <div class="d-flex gap-2" style="flex-wrap: wrap;">
        <input type="text" class="ne-form-control" style="flex:1; min-width: 240px;"
               placeholder="Filtrar por nombre, cédula, departamento..."
               data-filter-table="tabla-empleados">
        <a href="?orden=nombres"       class="ne-btn ne-btn--<?= $ordenActual === 'nombres' ? 'primary' : 'secondary' ?>">Nombre</a>
        <a href="?orden=salario"       class="ne-btn ne-btn--<?= $ordenActual === 'salario' ? 'primary' : 'secondary' ?>">Salario</a>
        <a href="?orden=fecha_ingreso" class="ne-btn ne-btn--<?= $ordenActual === 'fecha_ingreso' ? 'primary' : 'secondary' ?>">Fecha ingreso</a>
    </div>
</div>

<table class="ne-table" id="tabla-empleados">
    <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Departamento</th>
            <th>Cargo</th>
            <th class="text-end">Salario</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e->cedula) ?></td>
            <td><strong><?= htmlspecialchars($e->getNombreCompleto()) ?></strong></td>
            <td><?= htmlspecialchars($e->departamento->nombre) ?></td>
            <td><?= htmlspecialchars($e->cargo->nombre) ?></td>
            <td class="text-end">RD$ <?= number_format((float)$e->salario, 2) ?></td>
            <td><span class="ne-badge ne-badge--<?= $e->estado->badge() ?>"><?= $e->estado->label() ?></span></td>
            <td>
                <a href="/empleados/<?= $e->id ?>" class="ne-btn ne-btn--sm ne-btn--secondary"><i class="bi bi-eye"></i></a>
                <?php if (Auth::user()->puedeEditar()): ?>
                    <a href="/empleados/<?= $e->id ?>/editar" class="ne-btn ne-btn--sm ne-btn--primary"><i class="bi bi-pencil"></i></a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
