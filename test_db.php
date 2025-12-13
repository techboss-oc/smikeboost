<?php
require_once __DIR__ . '/app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful.\n";

    // Check if tables exist
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nTesting User model...\n";

try {
    require_once __DIR__ . '/app/models/User.php';
    $user = new User();
    echo "User model instantiated successfully!\n";
} catch (Exception $e) {
    echo "User model error: " . $e->getMessage() . "\n";
}

echo "\nTesting AdminController...\n";

try {
    require_once __DIR__ . '/app/controllers/AdminController.php';
    $admin = new AdminController();
    echo "AdminController instantiated successfully!\n";
} catch (Exception $e) {
    echo "AdminController error: " . $e->getMessage() . "\n";
}
