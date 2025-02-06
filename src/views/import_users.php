<!DOCTYPE html>
<html>
<head>
    <title>Import Users - ITEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Import Users</h1>
            <a href="?action=users" class="btn btn-secondary">Back</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Upload CSV File</h5>
                <p class="text-muted">File should contain columns: name, email, ppid</p>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import Users</button>
                </form>
            </div>
        </div>

        <?php if (isset($results)): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Import Results</h5>
                <div class="alert alert-success">
                    Successfully imported: <?= $results['success'] ?> users
                </div>
                <?php if (!empty($results['errors'])): ?>
                <div class="alert alert-danger">
                    <h6>Errors:</h6>
                    <ul>
                        <?php foreach ($results['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html> 