<?php
require_once 'app/config/config.php';
$providers = db_fetch_all('SELECT id, name FROM providers LIMIT 5');
echo 'Providers:' . PHP_EOL;
foreach ($providers as $p) {
    echo $p['id'] . ': ' . $p['name'] . PHP_EOL;
}
?>