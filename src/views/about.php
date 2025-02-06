<!DOCTYPE html>
<html>
<head>
    <title>About - ITEM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>About</h1>
            <a href="index.php" class="btn btn-secondary">Back</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">ITEM - IT Equipment Management</h5>
                <p class="card-text">Version 1.0</p>
                
                <h6 class="mt-4">Uses:</h6>
                <ul>
                    <li>HTML</li>
                    <li>CSS</li>
                    <li>JavaScript</li>
                    <li>PHP</li>
                    <li>MySQL</li>
                    <li>Bootstrap</li>
                    <li>phpMyAdmin</li>
                    <li>Docker</li>
                    <li>Docker Compose</li>                    
                </ul>

                <h6 class="mt-4">Quick Links:</h6>
                <div class="list-group">
                    <a href="https://github.com/Gren-95/inv-managment" class="list-group-item list-group-item-action">Github</a>
                    <a href="https://github.com/Gren-95/inv-managment/issues" class="list-group-item list-group-item-action">Issue Tracker</a>
                    <a href="https://github.com/Gren-95" class="list-group-item list-group-item-action">Author: Gren-95</a>
                    <a href="<?php echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . ':8081'; ?>" class="list-group-item list-group-item-action">Database</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 