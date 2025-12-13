<?php
/**
 * Debug admin login - delete after troubleshooting!
 */

require_once __DIR__ . '/app/config/config.php';

echo "<h2>Admin Login Debug</h2>";

// Check DB connection
echo "<h3>1. Database Connection</h3>";
try {
    $pdo = db();
    echo "✅ Database connected successfully<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

// Check admin user
echo "<h3>2. Admin User in Database</h3>";
$user = db_fetch("SELECT * FROM users WHERE id = 1");
if ($user) {
    echo "✅ Admin user found:<br>";
    echo "- ID: " . $user['id'] . "<br>";
    echo "- Username: " . $user['username'] . "<br>";
    echo "- Email: " . $user['email'] . "<br>";
    echo "- Role: " . $user['role'] . "<br>";
    echo "- Hash length: " . strlen($user['password_hash']) . "<br>";
    echo "- Hash starts with: " . substr($user['password_hash'], 0, 10) . "...<br>";
} else {
    echo "❌ No user with id=1 found<br>";
    exit;
}

// Check findAdminByIdentity
echo "<h3>3. Testing findAdminByIdentity()</h3>";
require_once APP_PATH . '/models/User.php';
$userModel = new User();

$testIdentities = ['admin', 'admin@smikeboost.com'];
foreach ($testIdentities as $identity) {
    $result = $userModel->findAdminByIdentity($identity);
    if ($result) {
        echo "✅ findAdminByIdentity('$identity') returned user id=" . $result['id'] . "<br>";
    } else {
        echo "❌ findAdminByIdentity('$identity') returned NULL<br>";
    }
}

// Test password verification
echo "<h3>4. Password Verification Test</h3>";
$testPassword = 'admin123';
$storedHash = $user['password_hash'];

echo "Testing password: '$testPassword'<br>";
echo "Against hash: $storedHash<br>";

// Test with password_verify directly
$result1 = password_verify($testPassword, $storedHash);
echo "password_verify() result: " . ($result1 ? '✅ TRUE' : '❌ FALSE') . "<br>";

// Test with verify_password helper
$result2 = verify_password($testPassword, $storedHash);
echo "verify_password() result: " . ($result2 ? '✅ TRUE' : '❌ FALSE') . "<br>";

// Generate a new hash and test
echo "<h3>5. Generate Fresh Hash</h3>";
$freshHash = password_hash($testPassword, PASSWORD_BCRYPT);
echo "Fresh hash for '$testPassword': $freshHash<br>";
$result3 = password_verify($testPassword, $freshHash);
echo "Verify fresh hash: " . ($result3 ? '✅ TRUE' : '❌ FALSE') . "<br>";

// Check session
echo "<h3>6. Session Status</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . " (2 = active)<br>";

// Check CSRF
require_once APP_PATH . '/config/admin.php';
require_once APP_PATH . '/helpers/admin_helpers.php';
echo "CSRF token exists: " . (isset($_SESSION[ADMIN_CSRF_KEY]) ? '✅ Yes' : '❌ No') . "<br>";

// Fix the password now
echo "<h3>7. Fixing Password Now...</h3>";
$newHash = password_hash('admin123', PASSWORD_BCRYPT);
db_execute("UPDATE users SET password_hash = :hash WHERE id = 1", ['hash' => $newHash]);
echo "✅ Password updated to 'admin123'<br>";
echo "New hash: $newHash<br>";

// Verify fix
$updatedUser = db_fetch("SELECT password_hash FROM users WHERE id = 1");
$verifyFix = password_verify('admin123', $updatedUser['password_hash']);
echo "Verification after fix: " . ($verifyFix ? '✅ SUCCESS' : '❌ FAILED') . "<br>";

echo "<br><strong>Now try logging in with username 'admin' and password 'admin123'</strong><br>";
echo "<strong style='color:red'>DELETE THIS FILE AFTER USE!</strong>";
