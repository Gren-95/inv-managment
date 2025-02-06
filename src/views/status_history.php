<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - Status History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Status History</h1>
            <a href="index.php" class="btn btn-secondary">Back to Equipment List</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Equipment Details</h5>
                <p>
                    <strong>Type-Model:</strong> <?= htmlspecialchars($item['type_name']) ?> - <?= htmlspecialchars($item['model_name']) ?><br>
                    <strong>Current Status:</strong> <?= htmlspecialchars($item['status']) ?>
                </p>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Old Status</th>
                    <th>New Status</th>
                    <th>Changed By</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $record): ?>
                <tr>
                    <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($record['changed_at']))) ?></td>
                    <td>
                        <span class="badge bg-secondary">
                            <?= htmlspecialchars($record['old_status'] ?? 'Initial') ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-<?= getStatusColor($record['new_status']) ?>">
                            <?= htmlspecialchars($record['new_status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($record['changed_by_name'] ?? 'System') ?></td>
                    <td><?= htmlspecialchars($record['comment']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'available':
            return 'success';
        case 'assigned':
            return 'primary';
        case 'maintenance':
            return 'warning';
        case 'written_off':
            return 'danger';
        case 'pending_write_off':
            return 'info';
        default:
            return 'secondary';
    }
}
?> 