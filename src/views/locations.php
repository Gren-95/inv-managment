<!DOCTYPE html>
<html>
<head>
    <title>IT Equipment Management - Location Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Location Management</h1>
            <a href="index.php" class="btn btn-secondary">Back to Equipment List</a>
        </div>

        <div class="row">
            <!-- Countries -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Countries</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="country">
                            <div class="input-group">
                                <input type="text" name="name" class="form-control" placeholder="Country Name" required>
                                <button type="submit" class="btn btn-primary">Add Country</button>
                            </div>
                        </form>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($countries as $country): ?>
                                <tr>
                                    <td><?= htmlspecialchars($country['name']) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="type" value="country">
                                            <input type="hidden" name="id" value="<?= $country['id'] ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure? This will delete all related branches.')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Branches -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Branches</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="branch">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="country_id" class="form-control" required>
                                        <option value="">Select Country</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="name" class="form-control" placeholder="Branch Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Branch</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($branches as $branch): ?>
                                <tr>
                                    <td><?= htmlspecialchars($branch['country_name']) ?></td>
                                    <td><?= htmlspecialchars($branch['name']) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="type" value="branch">
                                            <input type="hidden" name="id" value="<?= $branch['id'] ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure? This will delete all related departments.')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Departments -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Departments</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="department">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="branch_id" class="form-control" required>
                                        <option value="">Select Branch</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?= $branch['id'] ?>">
                                                <?= htmlspecialchars($branch['country_name'] . ' - ' . $branch['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="name" class="form-control" placeholder="Department Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Department</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <td><?= htmlspecialchars($dept['country_name'] . ' - ' . $dept['branch_name']) ?></td>
                                    <td><?= htmlspecialchars($dept['name']) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="type" value="department">
                                            <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure? This will delete all related areas.')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Areas -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Areas</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="type" value="area">
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <select name="department_id" class="form-control" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>">
                                                <?= htmlspecialchars($dept['country_name'] . ' - ' . $dept['branch_name'] . ' - ' . $dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="name" class="form-control" placeholder="Area Name" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Add</button>
                                </div>
                            </div>
                        </form>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Area</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($areas as $area): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($area['country_name'] . ' - ' . 
                                            $area['branch_name'] . ' - ' . 
                                            $area['department_name']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($area['name']) ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="type" value="area">
                                            <input type="hidden" name="id" value="<?= $area['id'] ?>">
                                            <input type="hidden" name="delete" value="1">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                Delete
                                            </button>
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
</body>
</html> 