<?php
use App\Enums\EstadoEmpleado;
use App\Enums\Sexo;
use App\Enums\TipoContrato;
include APP_VIEWS_DIR . '/inc/header.php';

$e = $empleado;
$isEdit = $e !== null;
$action = $isEdit ? "/empleados/{$e->id}/editar" : '/empleados/nuevo';
?>

<div class="ne-page-header">
    <div>
        <h1><?= $isEdit ? 'Editar Empleado' : 'Nuevo Empleado' ?></h1>
        <div class="subtitle"><?= $isEdit ? htmlspecialchars($e->getNombreCompleto()) : 'Registro de expediente laboral' ?></div>
    </div>
    <a href="/empleados" class="ne-btn ne-btn--secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<?php if (!empty($error)): ?>
    <div class="ne-alert ne-alert--danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="<?= $action ?>">
    <div class="ne-card">
        <h3 class="ne-form-section-title">Datos Personales</h3>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Cédula *</label>
                <input type="text" name="cedula" class="ne-form-control" required
                       value="<?= htmlspecialchars($e->cedula ?? $_POST['cedula'] ?? '') ?>"
                       <?= $isEdit ? 'readonly' : '' ?>>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Sexo *</label>
                <select name="sexo" class="ne-form-control" required>
                    <?php foreach (Sexo::cases() as $s): ?>
                        <option value="<?= $s->value ?>" <?= ($e->sexo ?? null) === $s ? 'selected' : '' ?>><?= $s->label() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Nombres *</label>
                <input type="text" name="nombres" class="ne-form-control" required
                       value="<?= htmlspecialchars($e->nombres ?? $_POST['nombres'] ?? '') ?>">
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Apellidos *</label>
                <input type="text" name="apellidos" class="ne-form-control" required
                       value="<?= htmlspecialchars($e->apellidos ?? $_POST['apellidos'] ?? '') ?>">
            </div>
        </div>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Fecha de nacimiento *</label>
                <input type="date" name="fecha_nacimiento" class="ne-form-control" required
                       value="<?= $e?->fecha_nacimiento->format('Y-m-d') ?? $_POST['fecha_nacimiento'] ?? '' ?>">
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Teléfono</label>
                <input type="text" name="telefono" class="ne-form-control"
                       value="<?= htmlspecialchars($e->telefono ?? $_POST['telefono'] ?? '') ?>">
            </div>
        </div>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Correo</label>
                <input type="email" name="email" class="ne-form-control"
                       value="<?= htmlspecialchars($e->email ?? $_POST['email'] ?? '') ?>">
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Dirección</label>
                <input type="text" name="direccion" class="ne-form-control"
                       value="<?= htmlspecialchars($e->direccion ?? $_POST['direccion'] ?? '') ?>">
            </div>
        </div>

        <h3 class="ne-form-section-title">Datos Laborales</h3>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Departamento *</label>
                <select name="departamento_id" class="ne-form-control" required>
                    <option value="">— Seleccione —</option>
                    <?php foreach ($departamentos as $d): ?>
                        <option value="<?= $d->id ?>" <?= ($e->departamento->id ?? null) === $d->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Cargo *</label>
                <select name="cargo_id" class="ne-form-control" required data-cargo-select>
                    <option value="">— Seleccione —</option>
                    <?php foreach ($cargos as $c): ?>
                        <option value="<?= $c->id ?>" data-salario="<?= $c->salario_base_sugerido ?>"
                                <?= ($e->cargo->id ?? null) === $c->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c->nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Salario mensual (RD$) *</label>
                <input type="number" step="0.01" min="0" name="salario" class="ne-form-control" required data-salario-input
                       value="<?= htmlspecialchars($e->salario ?? $_POST['salario'] ?? '') ?>">
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Fecha de ingreso *</label>
                <input type="date" name="fecha_ingreso" class="ne-form-control" required
                       value="<?= $e?->fecha_ingreso->format('Y-m-d') ?? $_POST['fecha_ingreso'] ?? '' ?>">
            </div>
        </div>
        <div class="ne-form-row">
            <div class="ne-form-group">
                <label class="ne-form-label">Tipo de contrato *</label>
                <select name="tipo_contrato" class="ne-form-control" required>
                    <?php foreach (TipoContrato::cases() as $t): ?>
                        <option value="<?= $t->value ?>" <?= ($e->tipo_contrato ?? null) === $t ? 'selected' : '' ?>><?= $t->label() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="ne-form-group">
                <label class="ne-form-label">Estado *</label>
                <select name="estado" class="ne-form-control" required>
                    <?php foreach (EstadoEmpleado::cases() as $s): ?>
                        <option value="<?= $s->value ?>" <?= ($e->estado ?? EstadoEmpleado::ACTIVO) === $s ? 'selected' : '' ?>><?= $s->label() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="ne-btn ne-btn--primary"><i class="bi bi-check-lg"></i> Guardar</button>
            <a href="/empleados" class="ne-btn ne-btn--secondary">Cancelar</a>
        </div>
    </div>
</form>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
