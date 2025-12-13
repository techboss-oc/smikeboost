<?php
require_once 'app/config/config.php';
require_once 'app/helpers/db.php';

// Check user balance
$user = db_fetch("SELECT wallet_balance FROM users WHERE id = 1 LIMIT 1");
echo "User balance: " . ($user['wallet_balance'] ?? 'NULL') . PHP_EOL;

// Check some transactions
$transactions = db_fetch_all("SELECT amount, type, gateway FROM transactions LIMIT 5");
echo "Sample transactions:" . PHP_EOL;
foreach ($transactions as $tx) {
    echo "Amount: {$tx['amount']}, Type: {$tx['type']}, Gateway: {$tx['gateway']}" . PHP_EOL;
}

// Check some orders
$orders = db_fetch_all("SELECT amount FROM orders LIMIT 5");
echo "Sample orders:" . PHP_EOL;
foreach ($orders as $order) {
    echo "Amount: {$order['amount']}" . PHP_EOL;
}
?>