<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sortable {
            cursor: pointer;
            position: relative;
            padding-right: 20px !important;
        }
        .sortable:after {
            content: '↕';
            position: absolute;
            right: 5px;
            color: #999;
        }
        .sortable.asc:after {
            content: '↑';
            color: #000;
        }
        .sortable.desc:after {
            content: '↓';
            color: #000;
        }
        .filter-input {
            width: 100%;
            min-width: 100px;
        }
        th {
            min-width: 120px;
            vertical-align: top;
        }
        .form-select {
            min-width: 120px;
            max-width: 200px;
        }
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-outline-secondary:focus {
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.25);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>IT Equipment List</h1>
            <div class="d-flex align-items-center gap-3">
                <select class="form-select" style="width: auto;" onchange="handlePageChange(this.value)">
                    <option value="?action=list">Equipment List</option>
                    <option value="?action=locations">Manage Locations</option>
                    <option value="?action=users">Manage Users</option>
                    <option value="?action=models_and_types">Manage Models & Types</option>
                    <option value="?action=shared_accounts">Shared Accounts</option>
                    <option value="?action=equipment_log">Status Log</option>
                    <option value="?action=audit">Equipment Audit</option>
                    <option value="phpmyadmin">Database Admin</option>
                    <option value="?action=about">About</option>
                </select>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <div class="sortable" data-sort="id">ID</div>
                        <div class="mt-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="resetFilters()">Reset Filters</button>
                        </div>
                    </th>
                    <th>
                        <div class="sortable" data-sort="model">Model</div>
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm filter-input" data-column="model" placeholder="Filter model...">
                        </div>
                    </th>
                    <th>
                        <div class="sortable" data-sort="serial">Serial Number</div>
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm filter-input" 
                                   data-column="serial" placeholder="Filter serial...">
                        </div>
                    </th>
                    <th>
                        <div class="sortable" data-sort="age">Age</div>
                        <div class="mt-2">
                            <input type="number" class="form-control form-control-sm filter-input" 
                                   data-column="age" placeholder="Filter age...">
                        </div>
                    </th>
                    <th>
                        <div class="sortable" data-sort="status">Status</div>
                        <div class="mt-2">
                            <select class="form-select form-select-sm filter-input" data-column="status">
                                <option value="">All</option>
                                <option value="available">Available</option>
                                <option value="assigned">Assigned</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="written_off">Written Off</option>
                            </select>
                        </div>
                    </th>
                    <th>
                        Assigned To
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm filter-input" 
                                   data-column="assigned" placeholder="Filter user...">
                        </div>
                    </th>
                    <th>
                        Location
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm filter-input" data-column="location" placeholder="Filter location..."></div>
                    </th>
                    <th>
                        Actions
                        <div class="mt-2">
                            <a href="?action=create" class="btn btn-primary btn-sm">Add Equipment</a>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td>
                        <?= htmlspecialchars($item['type_name']) ?> - 
                        <?= htmlspecialchars($item['model_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($item['serial_number']) ?></td>
                    <td <?= $item['age'] >= $item['lifespan_years'] ? 'class="table-warning"' : '' ?>>
                        <?= htmlspecialchars($item['age']) ?> years
                    </td>
                    <td><?= htmlspecialchars($item['status']) ?></td>
                    <td><?= htmlspecialchars($item['user_name'] ?? '-') ?></td>
                    <td>
                        <?php
                        $location_parts = array_filter([
                            $item['country_name'] ?? '',
                            $item['branch_name'] ?? '',
                            $item['department_name'] ?? '',
                            $item['area_name'] ? "({$item['area_name']})" : ''
                        ]);
                        echo $location_parts ? htmlspecialchars(implode(' - ', $location_parts)) : '-';
                        ?>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" 
                                onchange="handleAction(this, <?= $item['id'] ?>)" 
                                style="width: auto; display: inline-block;">
                            <option value="">Actions...</option>
                            <option value="update">Edit</option>
                            <option value="status_history">History</option>
                            <option value="print_label">Print Label</option>
                            <?php if ($item['status'] !== 'written_off'): ?>
                                <option value="write_off">Write Off</option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function handlePageChange(value) {
            if (value === 'phpmyadmin') {
                window.open('http://' + window.location.hostname + ':8081', '_blank');
            } else {
                window.location.href = value;
            }
        }

        // Set the current page in the select
        document.addEventListener('DOMContentLoaded', function() {
            const pageSelect = document.querySelector('select');
            const currentUrl = window.location.search || '?action=list';
            if (currentUrl !== 'phpmyadmin') {
                pageSelect.value = currentUrl;
            }
        });

        function handleAction(select, id) {
            if (!select.value) return;
            
            if (select.value === 'print_label') {
                window.open(`?action=${select.value}&id=${id}`, '_blank');
            } else {
                window.location.href = `?action=${select.value}&id=${id}`;
            }
            
            // Reset select to default option
            select.value = '';
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterInputs = document.querySelectorAll('.filter-input');
            const tableRows = document.querySelectorAll('tbody tr');

            filterInputs.forEach(input => {
                input.addEventListener('input', filterTable);
            });

            window.resetFilters = function() {
                filterInputs.forEach(input => {
                    input.value = '';
                });
                filterTable();
            };

            function filterTable() {
                const filters = {};
                filterInputs.forEach(input => {
                    filters[input.dataset.column] = input.value.toLowerCase();
                });

                tableRows.forEach(row => {
                    let show = true;
                    const cells = {
                        model: row.cells[1].textContent.toLowerCase(),
                        serial: row.cells[2].textContent.toLowerCase(),
                        age: row.cells[3].textContent.toLowerCase(),
                        status: row.cells[4].textContent.toLowerCase(),
                        assigned: row.cells[5].textContent.toLowerCase(),
                        location: row.cells[6].textContent.toLowerCase()
                    };

                    Object.keys(filters).forEach(key => {
                        if (filters[key] && !cells[key].includes(filters[key])) {
                            show = false;
                        }
                    });

                    row.style.display = show ? '' : 'none';
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortableHeaders = document.querySelectorAll('.sortable');
            let currentSort = { column: 'id', direction: 'asc' };

            sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;
                    
                    // Remove all sort classes
                    sortableHeaders.forEach(h => {
                        h.classList.remove('asc', 'desc');
                    });

                    // Update sort direction
                    if (currentSort.column === column) {
                        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.column = column;
                        currentSort.direction = 'asc';
                    }

                    // Add appropriate class
                    header.classList.add(currentSort.direction);

                    // Sort the table
                    sortTable(column, currentSort.direction);
                });
            });

            function sortTable(column, direction) {
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                const sortedRows = rows.sort((a, b) => {
                    let aVal, bVal;

                    switch(column) {
                        case 'id':
                            aVal = parseInt(a.cells[0].textContent);
                            bVal = parseInt(b.cells[0].textContent);
                            break;
                        case 'model':
                            aVal = a.cells[1].textContent.trim();
                            bVal = b.cells[1].textContent.trim();
                            break;
                        case 'serial':
                            aVal = a.cells[2].textContent.trim();
                            bVal = b.cells[2].textContent.trim();
                            break;
                        case 'age':
                            aVal = parseInt(a.cells[3].textContent);
                            bVal = parseInt(b.cells[3].textContent);
                            break;
                        case 'status':
                            aVal = a.cells[4].textContent.trim();
                            bVal = b.cells[4].textContent.trim();
                            break;
                        default:
                            return 0;
                    }

                    if (aVal === bVal) return 0;
                    
                    const comparison = aVal > bVal ? 1 : -1;
                    return direction === 'asc' ? comparison : -comparison;
                });

                // Clear and re-append rows
                while (tbody.firstChild) {
                    tbody.removeChild(tbody.firstChild);
                }
                tbody.append(...sortedRows);
            }
        });
    </script>
</body>
</html> 