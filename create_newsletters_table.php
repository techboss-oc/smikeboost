<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/db.php';

// Create newsletters table if it doesn't exist
try {
    db_execute("
        CREATE TABLE IF NOT EXISTS newsletters (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subject VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            sent_count INT DEFAULT 0,
            failed_count INT DEFAULT 0,
            sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Newsletters table created successfully!";
} catch (Exception $e) {
    echo "Error creating newsletters table: " . $e->getMessage();
}
