<?php
use App\Config\Auth;
use App\Enums\MetodoCalculo;
use App\Enums\TipoConcepto;
include APP_VIEWS_DIR . '/inc/header.php';
?>

<div class="ne-page-header">
    <div>
        <h1>Conceptos de Nómina</h1>
        <div class="subtitle">Catálogo de ingresos y deducciones configurables</div>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/flash.php'; ?>

<div class="ne-grid ne-grid--2">
    <?php if (Auth::user()->isAdmin()): ?>
    <div class="ne-card">
        <div class="ne-card__title">Crear concepto</div>
        <form method="post" action="/conceptos">
            <div class="ne-form-row">
                <div class="ne-form-group">
                    <label class="ne-form-label">Código *</label>
                    <input type="text" name="codigo" class="ne-form-control" maxlength="20" required>
                </div>
                <div class="ne-form-group">
                    <label class="ne-form-label">Tipo *</label>
                    <select name="tipo" class="ne-form-control" required>
                        <?php foreach (TipoConcepto::cases() as $t): ?>
                            <option value="<?= $t->value ?>"><?= $t->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Nombre *</label>
                <input type="text" name="nombre" class="ne-form-control" required>
            </div>
            <div class="ne-form-row">
                <div class="ne-form-group">
                    <label class="ne-form-label">Método de cálculo *</label>
                    <select name="metodo_calculo" class="ne-form-control" required>
                        <?php foreach (MetodoCalculo::cases() as $m): ?>
                            <option value="<?= $m->value ?>"><?= $m->label() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ne-form-group">
                    <label class="ne-form-label">Valor</label>
                    <input type="number" step="0.0001" name="valor" class="ne-form-control" value="0">
                </div>
            </div>
            <div class="ne-form-group">
                <label class="d-flex align-center gap-2">
                    <input type="checkbox" name="obligatorio"> Aplicar a todos los empleados
                </label>
            </div>
            <button class="ne-btn ne-btn--primary"><i class="bi bi-plus-lg"></i> Crear</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="ne-card">
        <div class="ne-card__title">Listado</div>
        <table class="ne-table">
            <thead>
                <tr><th>Código</th><th>Nombre</th><th>Tipo</th><th>Cálculo</th><th class="text-end">Valor</th><th>Obl.</th></tr>
            </thead>
            <tbody>
                <?php foreach ($conceptos as $c): ?>
                <tr>
                    <td><code><?= htmlspecialchars($c->codigo) ?></code></td>
                    <td><?= htmlspecialchars($c->nombre) ?></td>
                    <td>
                        <span class="ne-badge ne-badge--<?= $c->tipo === TipoConcepto::INGRESO ? 'success' : 'danger' ?>">
                            <?= $c->tipo->label() ?>
                        </span>
                    </td>
                    <td><?= $c->metodo_calculo->label() ?></td>
                    <td class="text-end">
                        <?= $c->metodo_calculo === MetodoCalculo::PORCENTAJE
                            ? number_format((float)$c->valor, 2) . '%'
                            : 'RD$ ' . number_format((float)$c->valor, 2) ?>
                    </td>
                    <td class="text-center"><?= $c->obligatorio ? '<i class="bi bi-check-lg text-success"></i>' : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
