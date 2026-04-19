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

    // Mejoras globales a todas las tablas (.ne-table)
    document.querySelectorAll('.ne-table').forEach(table => {
        // 1. Wrapper para Scroll Y
        const wrapper = document.createElement('div');
        wrapper.style.maxHeight = '600px';
        wrapper.style.overflowY = 'auto';
        wrapper.style.border = '1px solid var(--ne-border)';
        wrapper.style.borderRadius = 'var(--ne-radius)';
        
        table.parentNode.insertBefore(wrapper, table);
        wrapper.appendChild(table);

        // Remover el border-radius de la tabla para que encaje bien en el wrapper
        table.style.borderRadius = '0';
        table.style.border = 'none';
        table.style.overflow = 'visible'; // Permite que los TH sticky funcionen

        // 2. Cabeceras sticky y ordenamiento
        table.querySelectorAll('thead th').forEach((th, index) => {
            th.style.position = 'sticky';
            th.style.top = '0';
            th.style.zIndex = '2';
            th.style.cursor = 'pointer';
            th.style.userSelect = 'none';
            th.title = 'Clic para ordenar';

            // Añadir icono de orden
            const icon = document.createElement('i');
            icon.className = 'bi bi-arrow-down-up text-muted';
            icon.style.opacity = '0.4';
            icon.style.fontSize = '0.85em';
            icon.style.marginLeft = '6px';
            icon.style.display = 'inline-block';
            th.appendChild(icon);

            let asc = true;

            th.addEventListener('click', () => {
                const tbody = table.querySelector('tbody');
                if (!tbody) return;

                // Resetear todos los iconos de la tabla
                table.querySelectorAll('thead th i').forEach(i => {
                    i.className = 'bi bi-arrow-down-up text-muted';
                    i.style.opacity = '0.4';
                });

                // Activar el icono de la columna clicada
                icon.className = asc ? 'bi bi-arrow-down' : 'bi bi-arrow-up';
                icon.style.opacity = '1';

                const rows = Array.from(tbody.querySelectorAll('tr'));
                
                rows.sort((a, b) => {
                    const aCol = a.children[index];
                    const bCol = b.children[index];
                    if (!aCol || !bCol) return 0;
                    
                    const aText = aCol.textContent.trim();
                    const bText = bCol.textContent.trim();

                    // Limpiar para detectar números o moneda
                    const numA = parseFloat(aText.replace(/[^0-9.-]+/g,""));
                    const numB = parseFloat(bText.replace(/[^0-9.-]+/g,""));

                    const isNumA = !isNaN(numA) && aText.match(/\d/);
                    const isNumB = !isNaN(numB) && bText.match(/\d/);

                    if (isNumA && isNumB) {
                        return asc ? numA - numB : numB - numA;
                    }

                    return asc ? aText.localeCompare(bText) : bText.localeCompare(aText);
                });

                // Reinsertar las filas ordenadas
                rows.forEach(row => tbody.appendChild(row));
                asc = !asc;
            });
        });
    });
});
