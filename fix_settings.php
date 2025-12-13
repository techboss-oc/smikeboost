<?php
require_once __DIR__ . '/app/config/config.php';
require_once APP_PATH . '/helpers/db.php';

echo "<pre>";
echo "Checking settings table...\n";

try {
    // Check for duplicates
    $duplicates = db_fetch_all("
        SELECT setting_key, COUNT(*) as c 
        FROM settings 
        GROUP BY setting_key 
        HAVING c > 1
    ");

    if ($duplicates) {
        echo "Found duplicates for keys:\n";
        foreach ($duplicates as $d) {
            echo "- " . $d['setting_key'] . " (" . $d['c'] . " times)\n";
        }
        
        echo "Cleaning up duplicates...\n";
        // Keep the one with the highest ID (latest)
        db_execute("
            DELETE s1 FROM settings s1
            INNER JOIN settings s2 
            WHERE s1.id < s2.id AND s1.setting_key = s2.setting_key
        ");
        echo "Duplicates removed.\n";
    } else {
        echo "No duplicates found.\n";
    }

    // Check if unique index exists by trying to add it
    echo "Ensuring UNIQUE constraint on setting_key...\n";
    try {
        db_execute("ALTER TABLE settings ADD UNIQUE INDEX idx_setting_key (setting_key)");
        echo "Unique index added.\n";
    } catch (PDOException $e) {
        // Check if error is "Duplicate key name" (1061)
        if (strpos($e->getMessage(), 'Duplicate key name') !== false || $e->getCode() == '42000') {
             echo "Unique index already exists.\n";
        } else {
             echo "Error adding index: " . $e->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done.\n";
echo "</pre>";
