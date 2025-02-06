<!DOCTYPE html>
<html>
<head>
    <title>Equipment Status Log</title>
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
        .status-change {
            font-weight: bold;
        }
        .timestamp {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Equipment Status Log</h1>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="sortable" data-sort="date">Date & Time</th>
                    <th class="sortable" data-sort="equipment">Equipment</th>
                    <th class="sortable" data-sort="change">Status Change</th>
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
                    </td>
                    <td class="status-change">
                        <?= htmlspecialchars($entry['old_status'] ?? 'none') ?> → 
                        <?= htmlspecialchars($entry['new_status']) ?>
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