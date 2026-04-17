<?php
use App\Config\Auth;
include APP_VIEWS_DIR . '/inc/header.php';
?>

<div class="ne-page-header">
    <div>
        <h1>Departamentos</h1>
        <div class="subtitle"><?= count($departamentos) ?> registrados</div>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-grid ne-grid--2">
    <?php if (Auth::user()->isAdmin()): ?>
    <div class="ne-card">
        <div class="ne-card__title">Crear departamento</div>
        <form method="post" action="/departamentos">
            <div class="ne-form-group">
                <label class="ne-form-label">Nombre *</label>
                <input type="text" name="nombre" class="ne-form-control" required>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Descripción</label>
                <input type="text" name="descripcion" class="ne-form-control">
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Departamento padre</label>
                <select name="padre_id" class="ne-form-control">
                    <option value="">— Raíz —</option>
                    <?php foreach ($departamentos as $d): ?>
                        <option value="<?= $d->id ?>"><?= htmlspecialchars($d->nombre) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="ne-btn ne-btn--primary"><i class="bi bi-plus-lg"></i> Crear</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="ne-card">
        <div class="ne-card__title">Listado</div>
        <table class="ne-table">
            <thead><tr><th>Nombre</th><th>Descripción</th><th>Empleados</th><th>Padre</th></tr></thead>
            <tbody>
                <?php foreach ($departamentos as $d): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d->nombre) ?></strong></td>
                    <td><?= htmlspecialchars($d->descripcion ?? '—') ?></td>
                    <td class="text-center"><?= count($d->empleados) ?></td>
                    <td><?= $d->padre ? htmlspecialchars($d->padre->nombre) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
