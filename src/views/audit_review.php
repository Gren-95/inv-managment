<!DOCTYPE html>
<html>
<head>
    <title>Review Equipment Audits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .changes-cell {
            min-width: 300px;
            white-space: normal;
        }
        .text-danger {
            text-decoration: line-through;
            opacity: 0.7;
        }
        .text-success {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Review Equipment Audits</h1>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <input type="hidden" name="action" value="audit_review">
                    <div class="col-md-4">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="from_date" 
                               value="<?= $_GET['from_date'] ?? date('Y-m-d', strtotime('-7 days')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="to_date" 
                               value="<?= $_GET['to_date'] ?? date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="mb-3">
            <button class="btn btn-success" onclick="approveSelected()">Approve Selected</button>
            <button class="btn btn-danger" onclick="rejectSelected()">Reject Selected</button>
        </div>

        <!-- Audits Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 40px">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                        </th>
                        <th style="width: 150px">Date</th>
                        <th>Equipment</th>
                        <th>Serial</th>
                        <th class="changes-cell">Changes Made</th>
                        <th>Audited By</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($audits as $audit): ?>
                    <tr>
                        <td>
                            <?php if ($audit['status'] === 'pending'): ?>
                            <input type="checkbox" class="audit-select" value="<?= $audit['id'] ?>">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($audit['audit_date']))) ?></td>
                        <td><?= htmlspecialchars($audit['type_name'] . ' - ' . $audit['model_name']) ?></td>
                        <td><?= htmlspecialchars($audit['serial_number']) ?></td>
                        <td>
                            <div class="small">
                                <?php if ($audit['current_status'] !== $audit['new_status']): ?>
                                    <div class="mb-2">
                                        <strong>Status:</strong><br>
                                        <span class="text-danger"><?= htmlspecialchars($audit['current_status']) ?></span> →
                                        <span class="text-success"><?= htmlspecialchars($audit['new_status']) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($audit['current_location_id'] !== $audit['new_location_id']): ?>
                                    <div class="mb-2">
                                        <strong>Location:</strong><br>
                                        <span class="text-danger"><?= htmlspecialchars($audit['current_location'] ?? 'Not Set') ?></span><br>→<br>
                                        <span class="text-success"><?= htmlspecialchars($audit['new_location'] ?? 'Not Set') ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($audit['current_assigned_to_id'] !== $audit['new_assigned_to_id']): ?>
                                    <div class="mb-2">
                                        <strong>Assigned To:</strong><br>
                                        <span class="text-danger"><?= htmlspecialchars($audit['current_assigned_to_name'] ?? 'Not Assigned') ?></span> →
                                        <span class="text-success"><?= htmlspecialchars($audit['new_assigned_to_name'] ?? 'Not Assigned') ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($audit['audited_by_name']) ?></td>
                        <td><?= htmlspecialchars($audit['audit_notes']) ?></td>
                        <td>
                            <span class="badge bg-<?= $audit['status'] === 'pending' ? 'warning' : 
                                                   ($audit['status'] === 'approved' ? 'success' : 'danger') ?>">
                                <?= ucfirst($audit['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($audit['status'] === 'pending'): ?>
                            <button class="btn btn-sm btn-success" onclick="approveAudit(<?= $audit['id'] ?>)">
                                Approve
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="rejectAudit(<?= $audit['id'] ?>)">
                                Reject
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleSelectAll(checkbox) {
            document.querySelectorAll('.audit-select').forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        async function approveSelected() {
            const selected = Array.from(document.querySelectorAll('.audit-select:checked')).map(cb => cb.value);
            if (!selected.length) {
                alert('Please select audits to approve');
                return;
            }
            
            if (confirm('Are you sure you want to approve the selected audits?')) {
                await updateAudits(selected, 'approve');
            }
        }

        async function rejectSelected() {
            const selected = Array.from(document.querySelectorAll('.audit-select:checked')).map(cb => cb.value);
            if (!selected.length) {
                alert('Please select audits to reject');
                return;
            }
            
            if (confirm('Are you sure you want to reject the selected audits?')) {
                await updateAudits(selected, 'reject');
            }
        }

        async function approveAudit(id) {
            if (confirm('Are you sure you want to approve this audit?')) {
                await updateAudits([id], 'approve');
            }
        }

        async function rejectAudit(id) {
            if (confirm('Are you sure you want to reject this audit?')) {
                await updateAudits([id], 'reject');
            }
        }

        async function updateAudits(ids, action) {
            try {
                const response = await fetch('index.php?action=update_audits', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        ids: ids,
                        action: action
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.error || 'Failed to update audits');
                }
            } catch (error) {
                alert('Failed to update audits');
            }
        }
    </script>
</body>
</html> 