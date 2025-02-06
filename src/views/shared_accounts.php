<!DOCTYPE html>
<html>
<head>
    <title>Shared Accounts - ITEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Shared Accounts</h1>
            <div>
                <a href="index.php" class="btn btn-secondary">Back</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAccountModal">
                    Add Account
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Passcode</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><?= htmlspecialchars($account['username']) ?></td>
                        <td><?= htmlspecialchars($account['email']) ?></td>
                        <td><?= htmlspecialchars($account['passcode']) ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-warning me-2" 
                                    onclick="editAccount(<?= htmlspecialchars(json_encode($account)) ?>)">
                                Edit
                            </button>
                            <a href="?action=print_account_label&id=<?= $account['id'] ?>" 
                               class="btn btn-sm btn-info me-2" target="_blank">
                                Print Label
                            </a>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                <input type="hidden" name="delete_account" value="1">
                                <input type="hidden" name="id" value="<?= $account['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Account Modal -->
    <div class="modal fade" id="addAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Shared Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Passcode</label>
                            <input type="text" name="passcode" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Account Modal -->
    <div class="modal fade" id="editAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="id" id="editAccountId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Shared Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" id="editAccountUsername" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editAccountEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Passcode</label>
                            <input type="text" name="passcode" id="editAccountPasscode" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editAccount(account) {
            document.getElementById('editAccountId').value = account.id;
            document.getElementById('editAccountUsername').value = account.username;
            document.getElementById('editAccountEmail').value = account.email;
            document.getElementById('editAccountPasscode').value = account.passcode;
            new bootstrap.Modal(document.getElementById('editAccountModal')).show();
        }
    </script>
</body>
</html> 