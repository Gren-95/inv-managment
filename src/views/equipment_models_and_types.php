<!DOCTYPE html>
<html>
<head>
    <title>Manage Equipment Types & Models</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Equipment Types & Models</h1>
            <a href="index.php" class="btn btn-secondary">Back to List</a>
        </div>

        <div class="row">
            <!-- Equipment Types Section -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Equipment Types</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                            Add Type
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($types as $type): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($type['name']) ?>
                                    <div>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="editType(<?= $type['id'] ?>, '<?= htmlspecialchars($type['name']) ?>', <?= $type['lifespan_years'] ?>)">
                                            Edit
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            <input type="hidden" name="delete_type" value="1">
                                            <input type="hidden" name="id" value="<?= $type['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Models Section -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Equipment Models</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModelModal">
                            Add Model
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table">
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
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="editModel(<?= htmlspecialchars(json_encode($model)) ?>)">
                                                Edit
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                <input type="hidden" name="delete_model" value="1">
                                                <input type="hidden" name="id" value="<?= $model['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Type Modals -->
    <div class="modal fade" id="addTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Equipment Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Type Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lifespan (Years)</label>
                            <input type="number" name="lifespan_years" class="form-control" 
                                   min="1" max="10" value="5" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="id" id="editTypeId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Equipment Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Type Name</label>
                            <input type="text" name="name" id="editTypeName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lifespan (Years)</label>
                            <input type="number" name="lifespan_years" id="editTypeLifespan" 
                                   class="form-control" min="1" max="10" required>
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

    <!-- Model Modals -->
    <div class="modal fade" id="addModelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Equipment Model</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Equipment Type</label>
                            <select name="type_id" class="form-select" required>
                                <option value="">Select Type</option>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Release Year</label>
                            <input type="number" name="release_year" class="form-control" 
                                   min="1900" max="<?= date('Y') + 1 ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Model</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="id" id="editModelId">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Equipment Model</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Equipment Type</label>
                            <select name="type_id" id="editModelType" class="form-select" required>
                                <?php foreach ($types as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Model Name</label>
                            <input type="text" name="name" id="editModelName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Release Year</label>
                            <input type="number" name="release_year" id="editModelYear" class="form-control" 
                                   min="1900" max="<?= date('Y') + 1 ?>" required>
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
        function editType(id, name, lifespan) {
            document.getElementById('editTypeId').value = id;
            document.getElementById('editTypeName').value = name;
            document.getElementById('editTypeLifespan').value = lifespan;
            new bootstrap.Modal(document.getElementById('editTypeModal')).show();
        }

        function editModel(model) {
            document.getElementById('editModelId').value = model.id;
            document.getElementById('editModelType').value = model.type_id;
            document.getElementById('editModelName').value = model.name;
            document.getElementById('editModelYear').value = model.release_year;
            new bootstrap.Modal(document.getElementById('editModelModal')).show();
        }
    </script>
</body>
</html> 