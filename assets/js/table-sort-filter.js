// table-sort-filter.js
// Globally injects search and sort functionality into tables

document.addEventListener('DOMContentLoaded', () => {
    // Find all tables that have a tbody and thead (ignore pure layout tables ideally)
    const tables = document.querySelectorAll('table');

    tables.forEach(table => {
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');

        // Only process tables with proper structure and at least one row
        if (!thead || !tbody || tbody.querySelectorAll('tr').length === 0) return;

        // Ensure parent has relative positioning if we inject custom components
        table.parentElement.style.position = 'relative';

        // --- 1. Filter Implementation ---
        // Create wrapper and search input
        const filterContainer = document.createElement('div');
        filterContainer.className = 'table-filter-container';

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Search records...';
        searchInput.className = 'table-filter-input';

        filterContainer.appendChild(searchInput);

        // Insert right before the table (or the table's container if it's wrapped in a specific wrapper)
        const parent = table.parentElement;
        let targetElement = table;

        // If the table is wrapped for responsiveness, insert before the wrapper instead of inside it.
        // We only check for table-responsive so it doesn't break CSS grids if table-container is a grid item.
        if (parent.classList.contains('table-responsive')) {
            targetElement = parent;
        }

        targetElement.parentNode.insertBefore(filterContainer, targetElement);

        // Filter Logic
        searchInput.addEventListener('input', function (e) {
            const term = e.target.value.toLowerCase();
            const rows = tbody.querySelectorAll('tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });

        // --- 2. Sorting Implementation ---
        const headers = thead.querySelectorAll('th');

        headers.forEach((header, index) => {
            // Ignore headers that don't have text or shouldn't be sorted (e.g. Action columns)
            if (header.textContent.trim() === '' || header.classList.contains('no-sort')) return;

            header.classList.add('sortable');

            header.addEventListener('click', () => {
                const currentIsAsc = header.classList.contains('sort-asc');
                const direction = currentIsAsc ? -1 : 1; // 1 for Asc, -1 for Desc
                const isNumericCol = isColumnNumeric(tbody, index);

                // Reset all headers
                headers.forEach(h => {
                    h.classList.remove('sort-asc');
                    h.classList.remove('sort-desc');
                });

                // Set new direction class
                header.classList.add(currentIsAsc ? 'sort-desc' : 'sort-asc');

                // Get array of rows
                const rowsArray = Array.from(tbody.querySelectorAll('tr'));

                // Sort
                rowsArray.sort((a, b) => {
                    // Handle missing cells just in case
                    const aCol = a.children[index] ? a.children[index].textContent.trim() : '';
                    const bCol = b.children[index] ? b.children[index].textContent.trim() : '';

                    if (isNumericCol) {
                        const aNum = parseFloat(aCol.replace(/[^0-9.-]+/g, ""));
                        const bNum = parseFloat(bCol.replace(/[^0-9.-]+/g, ""));

                        const aVal = isNaN(aNum) ? 0 : aNum;
                        const bVal = isNaN(bNum) ? 0 : bNum;

                        return (aVal > bVal ? 1 : (aVal < bVal ? -1 : 0)) * direction;
                    } else {
                        return aCol.localeCompare(bCol) * direction;
                    }
                });

                // Re-append rows in sorted order (this automatically moves them, doesn't duplicate)
                rowsArray.forEach(row => tbody.appendChild(row));
            });
        });
    });

    // Helper function to detect if a column is mostly numbers (for proper numeric sorting)
    function isColumnNumeric(tbody, colIndex) {
        const rows = tbody.querySelectorAll('tr');
        // Sample up to 5 rows to guess the type
        const sampleSize = Math.min(5, rows.length);
        let numberCount = 0;

        for (let i = 0; i < sampleSize; i++) {
            const cell = rows[i].children[colIndex];
            if (cell) {
                const text = cell.textContent.trim();
                // Check if it consists mostly of digits, commas, dots, and a few possible symbols (currency, %, etc)
                if (/^[\d,.\s%$€£+-]+$/.test(text) && text !== "") {
                    numberCount++;
                }
            }
        }

        // If more than half the sample is numeric, treat the column as numeric
        return numberCount > (sampleSize / 2);
    }
});
