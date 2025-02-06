<!DOCTYPE html>
<html>
<head>
    <title>Users - ITEM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Users</h1>
            <div>
                <a href="?action=users&import=1" class="btn btn-secondary">Import Users</a>
                <a href="index.php" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>PPID</th>
                        <th>Status</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <?php $isCurrentUser = ($u['id'] == $_SESSION['user_id']); ?>
                    <tr>
                        <td <?= $isCurrentUser ? 'class="text-muted"' : '' ?>>
                            <?= htmlspecialchars($u['name']) ?>
                            <?= $isCurrentUser ? ' (You)' : '' ?>
                        </td>
                        <td <?= $isCurrentUser ? 'class="text-muted"' : '' ?>><?= htmlspecialchars($u['email']) ?></td>
                        <td <?= $isCurrentUser ? 'class="text-muted"' : '' ?>><?= htmlspecialchars($u['ppid']) ?></td>
                        <td>
                            <?php if ($u['active']): ?>
                                <span class="badge <?= $isCurrentUser ? 'bg-success' : 'bg-success' ?>">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info" 
                                    onclick="showPermissions(<?= $u['id'] ?>)"
                                    <?= $isCurrentUser ? 'disabled title="Cannot modify your own permissions"' : '' ?>>
                                Manage Permissions
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" 
                                    onclick="resetPassword(<?= $u['id'] ?>)">
                                Reset Password
                            </button>
                            <button type="button" class="btn btn-sm btn-<?= $u['active'] ? 'danger' : 'success' ?>" 
                                    onclick="toggleActive(<?= $u['id'] ?>, <?= $u['active'] ? 'false' : 'true' ?>)"
                                    <?= $isCurrentUser ? 'disabled title="Cannot deactivate your own account"' : '' ?>>
                                <?= $u['active'] ? 'Deactivate' : 'Activate' ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Permissions Modal -->
    <div class="modal fade" id="permissionsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Permissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="permissionsForm">
                        <input type="hidden" name="user_id" id="permissionUserId">
                        <?php foreach ($permissions as $perm): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" 
                                   name="permissions[]" value="<?= $perm['id'] ?>" 
                                   id="perm<?= $perm['id'] ?>">
                            <label class="form-check-label" for="perm<?= $perm['id'] ?>">
                                <?= htmlspecialchars($perm['name']) ?>
                                <small class="text-muted d-block">
                                    <?= htmlspecialchars($perm['description']) ?>
                                </small>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="savePermissions()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Reset Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        <input type="hidden" name="user_id" id="passwordUserId">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="savePassword()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const permissionsModal = new bootstrap.Modal(document.getElementById('permissionsModal'));
        const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));

        async function showPermissions(userId) {
            if (userId === <?= $_SESSION['user_id'] ?>) {
                alert('Cannot modify your own permissions');
                return;
            }
            const response = await fetch(`?action=get_user_permissions&user_id=${userId}`);
            const permissions = await response.json();
            
            document.getElementById('permissionUserId').value = userId;
            document.querySelectorAll('#permissionsForm input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = permissions.includes(parseInt(checkbox.value));
            });
            
            permissionsModal.show();
        }

        async function savePermissions() {
            const form = document.getElementById('permissionsForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('?action=update_user_permissions', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    permissionsModal.hide();
                    location.reload();
                } else {
                    alert('Failed to update permissions');
                }
            } catch (error) {
                alert('Failed to update permissions');
            }
        }

        function resetPassword(userId) {
            document.getElementById('passwordUserId').value = userId;
            passwordModal.show();
        }

        async function savePassword() {
            const form = document.getElementById('passwordForm');
            const formData = new FormData(form);
            
            if (formData.get('password') !== formData.get('confirm_password')) {
                alert('Passwords do not match');
                return;
            }
            
            try {
                const response = await fetch('?action=reset_user_password', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    passwordModal.hide();
                    form.reset();
                    alert('Password updated successfully');
                } else {
                    alert('Failed to update password');
                }
            } catch (error) {
                alert('Failed to update password');
            }
        }

        async function toggleActive(userId, active) {
            if (userId === <?= $_SESSION['user_id'] ?>) {
                alert('Cannot modify your own account status');
                return;
            }
            if (!confirm(`Are you sure you want to ${active ? 'activate' : 'deactivate'} this user?`)) {
                return;
            }
            
            try {
                const response = await fetch('?action=toggle_user_active', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ user_id: userId, active: active })
                });
                
                const result = await response.json();
                if (response.ok && result.success) {
                    location.reload();
                } else {
                    alert(result.error || 'Failed to update user status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to update user status');
            }
        }
    </script>
</body>
</html> 