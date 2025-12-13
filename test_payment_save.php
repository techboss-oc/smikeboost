<?php
/**
 * Test script for payment settings save
 */
require_once __DIR__ . '/app/config/config.php';
require_once APP_PATH . '/config/admin.php';
require_once APP_PATH . '/helpers/admin_helpers.php';

echo "<h2>Payment Settings Save Test</h2>";
echo "<pre>";

// Test 1: Check if settings table exists
echo "1. Checking settings table...\n";
try {
    $result = db_fetch("SHOW TABLES LIKE 'settings'");
    echo "   Settings table exists: " . ($result ? '✅ Yes' : '❌ No') . "\n";
    
    if (!$result) {
        echo "   Creating settings table...\n";
        ensure_settings_table_exists();
        echo "   Table created.\n";
    }
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test 2: Direct database insert
echo "\n2. Testing direct setting save...\n";
$testKey = 'test_payment_' . time();
$testValue = 'test_value';

try {
    $result = update_setting($testKey, $testValue);
    echo "   update_setting result: " . ($result ? '✅ Success' : '❌ Failed') . "\n";
    
    $readBack = get_setting($testKey);
    echo "   Read back: " . ($readBack === $testValue ? "✅ Match" : "❌ Mismatch (got: $readBack)") . "\n";
    
    // Clean up
    db_execute("DELETE FROM settings WHERE setting_key = :key", ['key' => $testKey]);
} catch (Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

// Test 3: Show all current payment settings
echo "\n3. Current payment settings in database:\n";
$paymentKeys = [
    'active_payment_gateway', 'min_deposit', 'max_deposit',
    'flutterwave_enabled', 'paystack_enabled', 'bank_transfer_enabled', 'crypto_enabled'
];
foreach ($paymentKeys as $key) {
    $val = get_setting($key);
    echo "   $key: " . ($val !== null ? "'$val'" : 'NULL') . "\n";
}

// Test 4: Check CSRF
echo "\n4. CSRF Token check:\n";
$token = admin_csrf_token();
echo "   Generated token: " . substr($token, 0, 20) . "...\n";
echo "   Session token: " . (isset($_SESSION[ADMIN_CSRF_KEY]) ? substr($_SESSION[ADMIN_CSRF_KEY], 0, 20) . "..." : 'NOT SET') . "\n";
echo "   Verify test: " . (admin_verify_csrf($token) ? '✅ Valid' : '❌ Invalid') . "\n";

echo "</pre>";

// Test form
echo "<h3>Test Save Form</h3>";
echo "<p>Submit this form to test saving:</p>";
?>
<form method="POST" action="" style="background:#f5f5f5; padding:20px; max-width:500px;">
    <input type="hidden" name="csrf_token" value="<?= admin_csrf_token() ?>">
    <input type="hidden" name="test_save" value="1">
    <p>
        <label>Test Setting Key:</label><br>
        <input type="text" name="test_key" value="test_min_deposit" style="width:100%; padding:8px;">
    </p>
    <p>
        <label>Test Setting Value:</label><br>
        <input type="text" name="test_value" value="500" style="width:100%; padding:8px;">
    </p>
    <button type="submit" style="padding:10px 20px; background:#8b5cf6; color:white; border:none; cursor:pointer;">Save Test Setting</button>
</form>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_save'])) {
    echo "<h3>Form Submission Result:</h3><pre>";
    
    $csrf = $_POST['csrf_token'] ?? '';
    echo "CSRF Token received: " . substr($csrf, 0, 20) . "...\n";
    echo "CSRF Valid: " . (admin_verify_csrf($csrf) ? '✅ Yes' : '❌ No') . "\n";
    
    if (admin_verify_csrf($csrf)) {
        $key = $_POST['test_key'] ?? '';
        $value = $_POST['test_value'] ?? '';
        
        echo "Saving: $key = $value\n";
        $result = update_setting($key, $value);
        echo "Save result: " . ($result ? '✅ Success' : '❌ Failed') . "\n";
        
        $readBack = get_setting($key);
        echo "Verify read: " . ($readBack === $value ? "✅ Match ($readBack)" : "❌ Mismatch (got: $readBack)") . "\n";
    }
    
    echo "</pre>";
}
?>

<p><a href="<?= admin_url('payments') ?>">Go to Admin Payments Page</a></p>
