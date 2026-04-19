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

<?php
// Extraer valores únicos para los filtros
$salariosUnicos = array_unique(array_map(fn($e) => $e->salario, $empleados));
rsort($salariosUnicos);

$departamentosUnicos = array_unique(array_map(fn($e) => $e->departamento->nombre, $empleados));
sort($departamentosUnicos);

$cargosUnicos = array_unique(array_map(fn($e) => $e->cargo->nombre, $empleados));
sort($cargosUnicos);
?>

<div class="ne-card mb-3">
    <div class="d-flex gap-2" style="flex-wrap: wrap;">
        <input type="text" class="ne-form-control" style="flex:1; min-width: 240px;"
               placeholder="Buscar empleado por cualquier dato..."
               id="filter-search">
        
        <select class="ne-form-control" style="width: auto;" id="filter-depto">
            <option value="">Todos los Departamentos</option>
            <?php foreach($departamentosUnicos as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>"><?= htmlspecialchars($d) ?></option>
            <?php endforeach; ?>
        </select>

        <select class="ne-form-control" style="width: auto;" id="filter-cargo">
            <option value="">Todos los Cargos</option>
            <?php foreach($cargosUnicos as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>

        <select class="ne-form-control" style="width: auto;" id="filter-salario">
            <option value="">Cualquier Salario</option>
            <?php foreach($salariosUnicos as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>">RD$ <?= number_format((float)$s, 2) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('filter-search');
    const deptoSelect = document.getElementById('filter-depto');
    const cargoSelect = document.getElementById('filter-cargo');
    const salarioSelect = document.getElementById('filter-salario');
    const tableRows = document.querySelectorAll('#tabla-empleados tbody tr');

    function filterTable() {
        const q = searchInput.value.toLowerCase();
        const depto = deptoSelect.value.toLowerCase();
        const cargo = cargoSelect.value.toLowerCase();
        // Format the selected salary to match the table's text format "RD$ X,XXX.XX" or just use the raw value if we match exactly.
        // It's safer to compare formatted text.
        const salarioOption = salarioSelect.options[salarioSelect.selectedIndex];
        const salarioText = salarioOption.value === "" ? "" : salarioOption.text.toLowerCase();

        tableRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const rowDepto = row.children[2].textContent.toLowerCase(); // Índice 2 = Departamento
            const rowCargo = row.children[3].textContent.toLowerCase(); // Índice 3 = Cargo
            const rowSalario = row.children[4].textContent.toLowerCase(); // Índice 4 = Salario

            const matchSearch = rowText.includes(q);
            const matchDepto = depto === "" || rowDepto === depto;
            const matchCargo = cargo === "" || rowCargo === cargo;
            const matchSalario = salarioText === "" || rowSalario.includes(salarioText);

            if (matchSearch && matchDepto && matchCargo && matchSalario) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    deptoSelect.addEventListener('change', filterTable);
    cargoSelect.addEventListener('change', filterTable);
    salarioSelect.addEventListener('change', filterTable);
});
</script>

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
