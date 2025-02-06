<!DOCTYPE html>
<html>
<head>
    <title>Log - ITEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
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
        .status-change {
            font-weight: bold;
        }
        .timestamp {
            color: #666;
            font-size: 0.9em;
        }
        .old-value {
            color: #dc3545;
            text-decoration: line-through;
            font-size: 0.9em;
            border-top: 1px solid #ddd;
            margin-top: 4px;
            padding-top: 4px;
        }
        .new-value {
            color: #28a745;
            font-weight: 500;
        }
        td {
            vertical-align: top !important;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Log</h1>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="sortable" data-sort="date">Date & Time</th>
                    <th class="sortable" data-sort="equipment">Model & Type</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Assigned To</th>
                    <th>TeamViewer ID</th>
                    <th>CERF ID</th>
                    <th class="sortable" data-sort="user">Changed By</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $entry): ?>
                <tr>
                    <td class="timestamp">
                        <?= date('Y-m-d H:i:s', strtotime($entry['changed_at'])) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($entry['type_name']) ?> - 
                        <?= htmlspecialchars($entry['model_name']) ?>
                        <br>
                        <small class="text-muted">SN: <?= htmlspecialchars($entry['serial_number']) ?></small>
                        <?php if (isset($entry['old_teamviewer_id']) && isset($entry['new_teamviewer_id']) &&
                                 ($entry['old_teamviewer_id'] !== null || $entry['new_teamviewer_id'] !== null)): ?>
                            <br>
                            <small class="text-muted">TeamViewer ID: 
                                <?= $entry['old_teamviewer_id'] !== null ? 
                                    htmlspecialchars($entry['old_teamviewer_id']) : 'Not Set' ?> → 
                                <?= $entry['new_teamviewer_id'] !== null ? 
                                    htmlspecialchars($entry['new_teamviewer_id']) : 'Not Set' ?>
                            </small>
                        <?php endif; ?>
                        <?php if (isset($entry['old_cerf_id']) && isset($entry['new_cerf_id']) &&
                                 ($entry['old_cerf_id'] !== null || $entry['new_cerf_id'] !== null)): ?>
                            <br>
                            <small class="text-muted">CERF ID: 
                                <?= $entry['old_cerf_id'] !== null ? 
                                    htmlspecialchars($entry['old_cerf_id']) : 'Not Set' ?> → 
                                <?= $entry['new_cerf_id'] !== null ? 
                                    htmlspecialchars($entry['new_cerf_id']) : 'Not Set' ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td class="status-cell">
                        <div class="new-value">
                            <?= htmlspecialchars($entry['new_status']) ?>
                        </div>
                        <?php if ($entry['old_status'] !== $entry['new_status']): ?>
                        <div class="old-value">
                            <?= htmlspecialchars($entry['old_status'] ?? 'none') ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="location-cell">
                        <div class="new-value">
                            <?= $entry['new_location_path'] ?: 'Not Set' ?>
                        </div>
                        <?php if ($entry['old_location_id'] !== $entry['new_location_id']): ?>
                        <div class="old-value">
                            <?= $entry['old_location_path'] ?: 'Not Set' ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="user-cell">
                        <div class="new-value">
                            <?= htmlspecialchars($entry['new_user_name'] ?? 'Not Assigned') ?>
                        </div>
                        <?php if ($entry['old_user_id'] !== $entry['new_user_id']): ?>
                        <div class="old-value">
                            <?= htmlspecialchars($entry['old_user_name'] ?? 'Not Assigned') ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="teamviewer-cell">
                        <div class="new-value">
                            <?= $entry['new_teamviewer_id'] ?: 'Not Set' ?>
                        </div>
                        <?php if ($entry['old_teamviewer_id'] != $entry['new_teamviewer_id']): ?>
                        <div class="old-value">
                            <?= $entry['old_teamviewer_id'] ?: 'Not Set' ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="cerf-cell">
                        <div class="new-value">
                            <?= $entry['new_cerf_id'] ?: 'Not Set' ?>
                        </div>
                        <?php if ($entry['old_cerf_id'] != $entry['new_cerf_id']): ?>
                        <div class="old-value">
                            <?= $entry['old_cerf_id'] ?: 'Not Set' ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($entry['changed_by_name'] ?? 'System') ?></td>
                    <td><?= htmlspecialchars($entry['comment'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortableHeaders = document.querySelectorAll('.sortable');
            let currentSort = { column: 'date', direction: 'desc' };

            sortableHeaders.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;
                    
                    sortableHeaders.forEach(h => {
                        h.classList.remove('asc', 'desc');
                    });

                    if (currentSort.column === column) {
                        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                    } else {
                        currentSort.column = column;
                        currentSort.direction = 'asc';
                    }

                    header.classList.add(currentSort.direction);
                    sortTable(column, currentSort.direction);
                });
            });

            function sortTable(column, direction) {
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));

                const sortedRows = rows.sort((a, b) => {
                    let aVal, bVal;

                    switch(column) {
                        case 'date':
                            aVal = new Date(a.cells[0].textContent.trim());
                            bVal = new Date(b.cells[0].textContent.trim());
                            break;
                        case 'equipment':
                            aVal = a.cells[1].textContent.trim();
                            bVal = b.cells[1].textContent.trim();
                            break;
                        case 'change':
                            aVal = a.cells[2].textContent.trim();
                            bVal = b.cells[2].textContent.trim();
                            break;
                        case 'user':
                            aVal = a.cells[3].textContent.trim();
                            bVal = b.cells[3].textContent.trim();
                            break;
                        default:
                            return 0;
                    }

                    if (aVal === bVal) return 0;
                    
                    const comparison = aVal > bVal ? 1 : -1;
                    return direction === 'asc' ? comparison : -comparison;
                });

                while (tbody.firstChild) {
                    tbody.removeChild(tbody.firstChild);
                }
                tbody.append(...sortedRows);
            }
        });
    </script>
</body>
</html> 