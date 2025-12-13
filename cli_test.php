<?php
require __DIR__ . '/app/config/config.php';
$key = 'cli_test_' . time();
update_setting($key, 'ok');
$value = get_setting($key);
echo $key . '=' . $value . PHP_EOL;
