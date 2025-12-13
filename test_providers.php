<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/helpers/db.php';

try {
    $providers = db_fetch_all('SELECT id, name, api_url, auto_sync, created_at FROM providers ORDER BY id DESC LIMIT 100');
    echo 'Query successful, found ' . count($providers) . ' providers' . PHP_EOL;
} catch (Exception $e) {
    echo 'DB Error: ' . $e->getMessage() . PHP_EOL;
}
?>