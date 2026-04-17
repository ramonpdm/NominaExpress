<?php
use App\Config\Auth;
include APP_VIEWS_DIR . '/inc/header.php';
$e = $empleado;
?>

<div class="ne-page-header">
    <div>
        <h1><?= htmlspecialchars($e->getNombreCompleto()) ?></h1>
        <div class="subtitle">
            Cédula <?= htmlspecialchars($e->cedula) ?> ·
            <span class="ne-badge ne-badge--<?= $e->estado->badge() ?>"><?= $e->estado->label() ?></span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <?php if (Auth::user()->puedeEditar()): ?>
            <a href="/empleados/<?= $e->id ?>/editar" class="ne-btn ne-btn--primary"><i class="bi bi-pencil"></i> Editar</a>
        <?php endif; ?>
        <a href="/empleados" class="ne-btn ne-btn--secondary">Volver</a>
    </div>
</div>

<div class="ne-grid ne-grid--2">
    <div class="ne-card">
        <div class="ne-card__title">Datos personales</div>
        <table class="ne-table">
            <tr><th>Nombres</th><td><?= htmlspecialchars($e->nombres) ?></td></tr>
            <tr><th>Apellidos</th><td><?= htmlspecialchars($e->apellidos) ?></td></tr>
            <tr><th>Sexo</th><td><?= $e->sexo->label() ?></td></tr>
            <tr><th>Nacimiento</th><td><?= $e->fecha_nacimiento->format('d/m/Y') ?></td></tr>
            <tr><th>Teléfono</th><td><?= htmlspecialchars($e->telefono ?? '—') ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($e->email ?? '—') ?></td></tr>
            <tr><th>Dirección</th><td><?= htmlspecialchars($e->direccion ?? '—') ?></td></tr>
        </table>
    </div>

    <div class="ne-card">
        <div class="ne-card__title">Datos laborales</div>
        <table class="ne-table">
            <tr><th>Departamento</th><td><?= htmlspecialchars($e->departamento->nombre) ?></td></tr>
            <tr><th>Cargo</th><td><?= htmlspecialchars($e->cargo->nombre) ?></td></tr>
            <tr><th>Salario</th><td><strong>RD$ <?= number_format((float)$e->salario, 2) ?></strong></td></tr>
            <tr><th>Tipo contrato</th><td><?= $e->tipo_contrato->label() ?></td></tr>
            <tr><th>Fecha ingreso</th><td><?= $e->fecha_ingreso->format('d/m/Y') ?></td></tr>
            <tr><th>Antigüedad</th><td><?= $e->fecha_ingreso->diff(new DateTime())->y ?> años</td></tr>
        </table>
    </div>
</div>

<div class="ne-card mt-3">
    <div class="ne-card__title">Historial de nóminas (<?= count($e->nominas) ?>)</div>
    <table class="ne-table">
        <thead>
            <tr><th>Período</th><th class="text-end">Ingresos</th><th class="text-end">Deducciones</th><th class="text-end">Neto</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($e->nominas as $n): ?>
            <tr>
                <td><?= htmlspecialchars($n->periodo->nombre) ?></td>
                <td class="text-end">RD$ <?= number_format((float)$n->total_ingresos, 2) ?></td>
                <td class="text-end">RD$ <?= number_format((float)$n->total_deducciones, 2) ?></td>
                <td class="text-end"><strong>RD$ <?= number_format((float)$n->salario_neto, 2) ?></strong></td>
                <td><a href="/nomina/comprobante/<?= $n->id ?>" class="ne-btn ne-btn--sm ne-btn--primary">Comprobante</a></td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($e->nominas) === 0): ?>
                <tr><td colspan="5" class="text-center text-muted">Sin nóminas registradas</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
