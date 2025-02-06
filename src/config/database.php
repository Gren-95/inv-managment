<?php

$db_config = [
    'host' => 'mysql',
    'dbname' => $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE'),
    'username' => $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME'),
    'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD')
];

try {
    $pdo = new PDO(
        "mysql:host={$db_config['host']};dbname={$db_config['dbname']}",
        $db_config['username'],
        $db_config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
} 