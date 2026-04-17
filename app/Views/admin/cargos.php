<?php
use App\Config\Auth;
include APP_VIEWS_DIR . '/inc/header.php';
?>

<div class="ne-page-header">
    <div>
        <h1>Cargos</h1>
        <div class="subtitle"><?= count($cargos) ?> cargos configurados</div>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-grid ne-grid--2">
    <?php if (Auth::user()->isAdmin()): ?>
    <div class="ne-card">
        <div class="ne-card__title">Crear cargo</div>
        <form method="post" action="/cargos">
            <div class="ne-form-group">
                <label class="ne-form-label">Nombre *</label>
                <input type="text" name="nombre" class="ne-form-control" required>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Departamento *</label>
                <select name="departamento_id" class="ne-form-control" required>
                    <?php foreach ($departamentos as $d): ?>
                        <option value="<?= $d->id ?>"><?= htmlspecialchars($d->nombre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Salario base sugerido (RD$)</label>
                <input type="number" step="0.01" min="0" name="salario_base_sugerido" class="ne-form-control" value="0.00">
            </div>
            <button class="ne-btn ne-btn--primary"><i class="bi bi-plus-lg"></i> Crear</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="ne-card">
        <div class="ne-card__title">Listado</div>
        <table class="ne-table">
            <thead><tr><th>Cargo</th><th>Departamento</th><th class="text-end">Salario sugerido</th></tr></thead>
            <tbody>
                <?php foreach ($cargos as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c->nombre) ?></td>
                    <td><?= htmlspecialchars($c->departamento->nombre) ?></td>
                    <td class="text-end">RD$ <?= number_format((float)$c->salario_base_sugerido, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
