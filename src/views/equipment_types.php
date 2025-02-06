<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - Equipment Types</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Equipment Types</h1>
            <a href="index.php" class="btn btn-secondary">Back to Equipment List</a>
        </div>

        <!-- Add New Type Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= $type ? 'Edit' : 'Add New' ?> Equipment Type</h5>
                <form method="POST" class="row g-3">
                    <?php if ($type): ?>
                        <input type="hidden" name="id" value="<?= $type['id'] ?>">
                    <?php endif; ?>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="name" 
                               value="<?= $type ? htmlspecialchars($type['name']) : '' ?>"
                               placeholder="Type Name" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <?= $type ? 'Update' : 'Add' ?> Type
                        </button>
                        <?php if ($type): ?>
                            <a href="?action=types" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Types List -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($types as $type): ?>
                <tr>
                    <td><?= htmlspecialchars($type['id']) ?></td>
                    <td><?= htmlspecialchars($type['name']) ?></td>
                    <td>
                        <a href="?action=types&edit_id=<?= $type['id'] ?>" 
                           class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $type['id'] ?>">
                            <input type="hidden" name="delete_type" value="1">
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this type?')">
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