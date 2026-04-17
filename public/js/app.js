// Nomina Express — scripts del cliente

document.addEventListener('DOMContentLoaded', () => {
    // Marca el link activo del navbar según la URL
    const path = window.location.pathname;
    document.querySelectorAll('.ne-nav__link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && (path === href || (href !== '/' && path.startsWith(href)))) {
            link.classList.add('active');
        }
    });

    // Autocompletado de salario al elegir cargo en el formulario de empleados
    const cargoSelect = document.querySelector('[data-cargo-select]');
    const salarioInput = document.querySelector('[data-salario-input]');
    if (cargoSelect && salarioInput) {
        cargoSelect.addEventListener('change', () => {
            const salario = cargoSelect.selectedOptions[0]?.dataset.salario;
            if (salario) salarioInput.value = salario;
        });
    }

    // Filtro en cliente de tablas (búsqueda en tiempo real)
    document.querySelectorAll('[data-filter-table]').forEach(input => {
        const tableId = input.dataset.filterTable;
        const table = document.getElementById(tableId);
        if (!table) return;
        input.addEventListener('input', () => {
            const q = input.value.toLowerCase();
            table.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    });

    // Confirmaciones de acciones sensibles
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });
});
