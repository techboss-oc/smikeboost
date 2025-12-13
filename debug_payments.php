<?php
/**
 * Debug script for payment settings
 * Access via: http://localhost/boost/debug_payments.php
 */

require_once __DIR__ . '/app/config/config.php';
require_once APP_PATH . '/config/admin.php';

echo "<h2>Payment Settings Debug</h2>";
echo "<pre>";

// Test settings table
echo "1. Testing settings table...\n";
try {
    $result = db_fetch("SHOW TABLES LIKE 'settings'");
    echo "   Settings table exists: " . ($result ? '✅ Yes' : '❌ No') . "\n";
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test reading a setting
echo "\n2. Testing get_setting function...\n";
$testSettings = [
    'active_payment_gateway',
    'flutterwave_enabled',
    'paystack_enabled',
    'min_deposit',
    'max_deposit'
];

foreach ($testSettings as $key) {
    $value = get_setting($key);
    echo "   {$key}: " . ($value !== null ? "'{$value}'" : 'NULL') . "\n";
}

// Test writing a setting
echo "\n3. Testing update_setting function...\n";
$testKey = 'test_setting_' . time();
$testValue = 'test_value_' . time();

$writeResult = update_setting($testKey, $testValue);
echo "   Write result: " . ($writeResult ? '✅ Success' : '❌ Failed') . "\n";

$readBack = get_setting($testKey);
echo "   Read back: " . ($readBack === $testValue ? "✅ Match ('{$readBack}')" : "❌ Mismatch (expected '{$testValue}', got '{$readBack}')") . "\n";

// Clean up test setting
db_execute("DELETE FROM settings WHERE setting_key = :key", ['key' => $testKey]);

// Check current settings count
echo "\n4. All settings in database:\n";
$allSettings = db_fetch_all("SELECT setting_key, setting_value FROM settings ORDER BY setting_key");
if (empty($allSettings)) {
    echo "   No settings found in database.\n";
} else {
    foreach ($allSettings as $s) {
        $displayValue = strlen($s['setting_value']) > 50 ? substr($s['setting_value'], 0, 50) . '...' : $s['setting_value'];
        echo "   {$s['setting_key']}: {$displayValue}\n";
    }
}

// Check CSRF
echo "\n5. Session/CSRF check:\n";
echo "   Session ID: " . session_id() . "\n";
echo "   ADMIN_CSRF_KEY constant: " . ADMIN_CSRF_KEY . "\n";
echo "   CSRF in session: " . (isset($_SESSION[ADMIN_CSRF_KEY]) ? $_SESSION[ADMIN_CSRF_KEY] : 'Not set') . "\n";

echo "\n6. Form action URL test:\n";
echo "   SITE_URL: " . SITE_URL . "\n";
echo "   admin_url('payments'): " . admin_url('payments') . "\n";

echo "</pre>";

echo "<h3>Test Form</h3>";
echo "<p>Try submitting this form to test payment settings update:</p>";
$csrf = admin_csrf_token();
?>
<form method="POST" action="<?= admin_url('payments') ?>" style="background:#f5f5f5; padding:20px; max-width:400px;">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <p>
        <label>Min Deposit:</label>
        <input type="number" name="min_deposit" value="<?= htmlspecialchars(get_setting('min_deposit') ?: '100') ?>" style="width:100%; padding:8px;">
    </p>
    <p>
        <label>Test Value:</label>
        <input type="text" name="flutterwave_public_key" value="<?= htmlspecialchars(get_setting('flutterwave_public_key') ?: 'TEST_KEY') ?>" style="width:100%; padding:8px;">
    </p>
    <button type="submit" style="padding:10px 20px; background:#8b5cf6; color:white; border:none; cursor:pointer;">Save Test</button>
</form>

<p><a href="<?= admin_url('payments') ?>">Go to Admin Payments Page</a></p>
