<?php
require_once 'app/config/config.php';
require_once 'app/helpers/db.php';

// Get demo user
$user = db_fetch("SELECT id FROM users WHERE username = 'demo' LIMIT 1");
if (!$user) {
    echo "Demo user not found. Run seed_demo_user.php first.\n";
    exit(1);
}

$userId = $user['id'];

// Add sample balance
db_execute("UPDATE users SET wallet_balance = 50000.00 WHERE id = :id", ['id' => $userId]);
echo "Updated demo user balance to ₦50,000\n";

// Add sample transactions
$sampleTransactions = [
    ['amount' => 10000.00, 'type' => 'deposit', 'gateway' => 'flutterwave', 'reference' => 'FW_DEMO_001', 'status' => 'completed'],
    ['amount' => 25000.00, 'type' => 'deposit', 'gateway' => 'paystack', 'reference' => 'PS_DEMO_002', 'status' => 'completed'],
    ['amount' => 15000.00, 'type' => 'deposit', 'gateway' => 'bank_transfer', 'reference' => 'BT_DEMO_003', 'status' => 'pending'],
];

foreach ($sampleTransactions as $tx) {
    db_execute(
        "INSERT INTO transactions (user_id, amount, type, gateway, reference, status, created_at) VALUES (:user_id, :amount, :type, :gateway, :reference, :status, NOW())",
        array_merge(['user_id' => $userId], $tx)
    );
}

echo "Added " . count($sampleTransactions) . " sample transactions\n";

// Add a sample order if services exist
$service = db_fetch("SELECT id FROM services WHERE status = 'enabled' LIMIT 1");
if ($service) {
    db_execute(
        "INSERT INTO orders (user_id, service_id, link, quantity, amount, status, created_at) VALUES (:user_id, :service_id, 'https://instagram.com/demo', 1000, 5000.00, 'completed', NOW())",
        ['user_id' => $userId, 'service_id' => $service['id']]
    );
    echo "Added sample order\n";
}

echo "Demo data seeded successfully!\n";
echo "Login as demo/demo1234 to see the Naira formatting.\n";
?>