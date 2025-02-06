<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - Models</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Models</h1>
            <div>
                <a href="?action=types" class="btn btn-secondary">Manage Types</a>
                <a href="index.php" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <!-- Add New Model Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= $model ? 'Edit' : 'Add New' ?> Model</h5>
                <form method="POST" class="row g-3">
                    <?php if ($model): ?>
                        <input type="hidden" name="id" value="<?= $model['id'] ?>">
                    <?php endif; ?>
                    <div class="col-md-4">
                        <select name="type_id" class="form-control" required>
                            <option value="">Select Type</option>
                            <?php foreach ($types as $type): 
                                $selected = ($model && $model['type_id'] == $type['id']) ? 'selected' : '';
                            ?>
                                <option value="<?= $type['id'] ?>" <?= $selected ?>><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="name" 
                               value="<?= $model ? htmlspecialchars($model['name']) : '' ?>"
                               placeholder="Model Name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="release_year" 
                               value="<?= $model ? htmlspecialchars($model['release_year']) : '' ?>"
                               placeholder="Release Year" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <?= $model ? 'Update' : 'Add' ?> Model
                        </button>
                        <?php if ($model): ?>
                            <a href="?action=models" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Models List -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Model Name</th>
                    <th>Release Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model): ?>
                <tr>
                    <td><?= htmlspecialchars($model['type_name']) ?></td>
                    <td><?= htmlspecialchars($model['name']) ?></td>
                    <td><?= htmlspecialchars($model['release_year']) ?></td>
                    <td>
                        <a href="?action=models&edit_id=<?= $model['id'] ?>" 
                           class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?= $model['id'] ?>">
                            <input type="hidden" name="delete_model" value="1">
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this model?')">
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