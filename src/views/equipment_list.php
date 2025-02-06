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
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>IT Equipment List</h1>
            <div>
                <a href="?action=locations" class="btn btn-secondary">Manage Locations</a>
                <a href="?action=users" class="btn btn-secondary">Manage Users</a>
                <a href="?action=models_and_types" class="btn btn-secondary">Manage Models & Types</a>
                <a href="?action=shared_accounts" class="btn btn-secondary">Shared Accounts</a>
                <a href="?action=equipment_log" class="btn btn-secondary">Status Log</a>
                <a href="?action=create" class="btn btn-primary">Add New Equipment</a>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="sortable" data-sort="id">ID</th>
                    <th>
                        <div class="sortable" data-sort="model">Model</div>
                        <div class="mt-2">
                            <input type="text" class="form-control form-control-sm filter-input" 
                                   data-column="model" placeholder="Filter model...">
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
                            <input type="text" class="form-control form-control-sm filter-input" 
                                   data-column="location" placeholder="Filter location...">
                        </div>
                    </th>
                    <th>Actions</th>
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

            function filterTable() {
                const filters = {};
                filterInputs.forEach(input => {
                    filters[input.dataset.column] = input.value.toLowerCase();
                });

                tableRows.forEach(row => {
                    let show = true;
                    const cells = {
                        model: row.cells[1].textContent,
                        serial: row.cells[2].textContent,
                        status: row.cells[3].textContent,
                        assigned: row.cells[4].textContent,
                        location: row.cells[5].textContent
                    };

                    Object.keys(filters).forEach(key => {
                        if (filters[key] && !cells[key].toLowerCase().includes(filters[key])) {
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