<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Users</h1>
            <div>
                <a href="?action=users&import=1" class="btn btn-primary">Import Users</a>
                <a href="index.php" class="btn btn-secondary">Back to Equipment List</a>
            </div>
        </div>

        <!-- Add New User Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= isset($edit_user) ? 'Edit' : 'Add New' ?> User</h5>
                <form method="POST" class="row g-3">
                    <?php if (isset($edit_user)): ?>
                        <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
                    <?php endif; ?>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="name" 
                               value="<?= isset($edit_user) ? htmlspecialchars($edit_user['name']) : '' ?>"
                               placeholder="Full Name" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= $edit_user ? htmlspecialchars($edit_user['email']) : '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users List -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <a href="?action=users&edit_id=<?= $user['id'] ?>" 
                           class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="delete_user" value="1">
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this user?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 