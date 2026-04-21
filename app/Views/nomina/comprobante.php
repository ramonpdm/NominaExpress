<?php
use App\Enums\TipoConcepto;
include APP_VIEWS_DIR . '/inc/header.php';

$n = $nomina;
$emp = $n->empleado;

$ingresos = [];
$deducciones = [];
foreach ($n->detalles as $d) {
    if ($d->tipo === TipoConcepto::INGRESO) $ingresos[] = $d;
    else $deducciones[] = $d;
}
?>

<div class="ne-page-header no-print">
    <div>
        <h1>Comprobante de Pago</h1>
        <div class="subtitle"><?= htmlspecialchars($emp->getNombreCompleto()) ?> · <?= htmlspecialchars($n->periodo->nombre) ?></div>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="ne-btn ne-btn--primary"><i class="bi bi-printer"></i> Imprimir</button>
        <a href="/empleados/<?= $emp->id ?>" class="ne-btn ne-btn--secondary">Volver</a>
    </div>
</div>

<div class="ne-comprobante">
    <div class="ne-comprobante__header">
        <div class="ne-comprobante__empresa">TechSoft RD, S.R.L.</div>
        <div class="ne-comprobante__subtitle">RNC 1-23-45678-9 · Av. Principal #100, Santo Domingo, R.D.</div>
        <h3 style="margin-top:16px; color: var(--ne-primary);">COMPROBANTE DE PAGO</h3>
        <div class="text-muted"><?= htmlspecialchars($n->periodo->nombre) ?></div>
    </div>

    <table class="ne-table" style="box-shadow:none; margin-bottom: 20px;">
        <tr>
            <th style="width: 25%;">Empleado</th>
            <td><?= htmlspecialchars($emp->getNombreCompleto()) ?></td>
            <th style="width: 15%;">Cédula</th>
            <td><?= htmlspecialchars($emp->cedula) ?></td>
        </tr>
        <tr>
            <th>Departamento</th>
            <td><?= htmlspecialchars($emp->departamento->nombre) ?></td>
            <th>Cargo</th>
            <td><?= htmlspecialchars($emp->cargo->nombre) ?></td>
        </tr>
        <tr>
            <th>Período</th>
            <td><?= $n->periodo->fecha_inicio->format('d/m/Y') ?> → <?= $n->periodo->fecha_fin->format('d/m/Y') ?></td>
            <th>Fecha de pago</th>
            <td><?= $n->periodo->fecha_pago->format('d/m/Y') ?></td>
        </tr>
    </table>

    <div class="ne-grid ne-grid--2">
        <div>
            <h4 style="color: var(--ne-success); border-bottom: 2px solid var(--ne-success); padding-bottom: 6px;">Ingresos</h4>
            <table class="ne-table" style="box-shadow: none;">
                <tr>
                    <td><strong>Salario Base Mensual</strong></td>
                    <td class="text-end"><strong>RD$ <?= number_format((float)$emp->salario, 2) ?></strong></td>
                </tr>
                <tr style="background: #F8FAFC;">
                    <td>Salario Base del Período (<?= htmlspecialchars($n->periodo->tipo->label()) ?>)</td>
                    <td class="text-end">RD$ <?= number_format((float)$n->salario_base, 2) ?></td>
                </tr>
                <?php $totalIng = (float)$n->salario_base; foreach ($ingresos as $d): ?>
                <?php if ($d->concepto && $d->concepto->codigo !== 'SAL_BASE'): ?>
                <tr>
                    <td><?= htmlspecialchars($d->concepto->nombre) ?></td>
                    <td class="text-end">RD$ <?= number_format((float)$d->monto, 2) ?></td>
                </tr>
                <?php $totalIng += (float) $d->monto; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <tr style="background: #F0FDF4;">
                    <td><strong>Total Ingresos</strong></td>
                    <td class="text-end"><strong>RD$ <?= number_format($totalIng, 2) ?></strong></td>
                </tr>
            </table>
        </div>

        <div>
            <h4 style="color: var(--ne-danger); border-bottom: 2px solid var(--ne-danger); padding-bottom: 6px;">Deducciones</h4>
            <table class="ne-table" style="box-shadow: none;">
                <?php $totalDed = 0; foreach ($deducciones as $d): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($d->concepto->nombre) ?>
                        <?php if ($d->porcentaje_aplicado): ?>
                            <small class="text-muted">(<?= number_format((float)$d->porcentaje_aplicado, 2) ?>%)</small>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">RD$ <?= number_format((float)$d->monto, 2) ?></td>
                </tr>
                <?php $totalDed += (float) $d->monto; ?>
                <?php endforeach; ?>
                <?php if (empty($deducciones)): ?>
                    <tr><td colspan="2" class="text-muted text-center">Sin deducciones</td></tr>
                <?php endif; ?>
                <tr style="background: #FEF2F2;">
                    <td><strong>Total Deducciones</strong></td>
                    <td class="text-end"><strong>RD$ <?= number_format($totalDed, 2) ?></strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="ne-comprobante__neto">
        <div class="ne-comprobante__neto-label">Salario Neto a Recibir</div>
        <div class="ne-comprobante__neto-value">RD$ <?= number_format((float)$n->salario_neto, 2) ?></div>
    </div>

    <div class="ne-grid ne-grid--2" style="margin-top: 48px;">
        <div style="text-align: center; border-top: 1px solid var(--ne-text); padding-top: 8px;">
            <strong>Firma del Empleado</strong>
        </div>
        <div style="text-align: center; border-top: 1px solid var(--ne-text); padding-top: 8px;">
            <strong>Autorización RRHH</strong>
        </div>
    </div>
</div>

<?php include APP_VIEWS_DIR . '/inc/footer.php'; ?>
