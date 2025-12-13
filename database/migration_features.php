<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/helpers/db.php';

try {
    echo "Starting feature migration...\n";

    // 1. Update Users Table for Referrals
    $cols = db_fetch_all("SHOW COLUMNS FROM users LIKE 'referrer_id'");
    if (empty($cols)) {
        db_execute("ALTER TABLE users ADD COLUMN referrer_id INT NULL AFTER id");
        db_execute("ALTER TABLE users ADD COLUMN affiliate_balance DECIMAL(12,2) DEFAULT 0 AFTER wallet_balance");
        echo "Added referrer_id and affiliate_balance to users.\n";
    }

    // 2. Create Child Panels Table
    db_execute("CREATE TABLE IF NOT EXISTS child_panels (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        domain VARCHAR(150) NOT NULL,
        admin_username VARCHAR(80) NOT NULL,
        admin_password VARCHAR(255) NOT NULL,
        price_per_month DECIMAL(10,2) NOT NULL,
        status ENUM('pending','active','suspended','canceled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Created child_panels table.\n";

    // 3. Create Referral Visits/Logs (Optional but good for tracking)
    db_execute("CREATE TABLE IF NOT EXISTS referral_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        referrer_id INT NOT NULL,
        referred_user_id INT NOT NULL,
        commission_amount DECIMAL(10,2) NOT NULL,
        order_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Created referral_logs table.\n";

    // 4. Seed Settings
    $settings = [
        'referral_commission_rate' => '5', // 5%
        'referral_min_payout' => '5000',
        'child_panel_price' => '25000', // Monthly price
        'child_panel_currency' => 'NGN'
    ];

    foreach ($settings as $key => $val) {
        db_execute("INSERT IGNORE INTO settings (`key`, `value`) VALUES (:key, :value)", ['key' => $key, 'value' => $val]);
    }
    echo "Seeded feature settings.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
