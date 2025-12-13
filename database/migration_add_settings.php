<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/helpers/db.php';

try {
    echo "Creating settings table...\n";
    db_execute("CREATE TABLE IF NOT EXISTS settings (
        `key` VARCHAR(50) PRIMARY KEY,
        `value` TEXT,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Settings table created.\n";

    echo "Adding proof_image to transactions...\n";
    // Check if column exists first to avoid error
    $cols = db_fetch_all("SHOW COLUMNS FROM transactions LIKE 'proof_image'");
    if (empty($cols)) {
        db_execute("ALTER TABLE transactions ADD COLUMN proof_image VARCHAR(255) NULL AFTER reference");
        echo "Column proof_image added.\n";
    } else {
        echo "Column proof_image already exists.\n";
    }

    // Seed default settings
    $defaults = [
        'flutterwave_public_key' => 'FLWPUBK_TEST-xxxxxxxxxxxxxxxxxxxxx-X',
        'flutterwave_secret_key' => 'FLWSECK_TEST-xxxxxxxxxxxxxxxxxxxxx-X',
        'flutterwave_env' => 'test',
        'bank_name' => 'Access Bank',
        'bank_account_name' => 'SmikeBoost Ltd',
        'bank_account_number' => '0123456789',
        'bank_instructions' => 'Please include your username in the transfer description.'
    ];

    foreach ($defaults as $key => $val) {
        db_execute("INSERT IGNORE INTO settings (`key`, `value`) VALUES (:key, :value)", ['key' => $key, 'value' => $val]);
    }
    echo "Default settings seeded.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
