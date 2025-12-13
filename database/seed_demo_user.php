<?php
/**
 * Seed a demo user for testing purposes.
 * Usage: php database/seed_demo_user.php
 */

require_once __DIR__ . '/../app/config/config.php';

try {
    $demoEmail = 'demo@smikeboost.com';
    $demoUsername = 'demo';
    $password = 'demo1234';

    $existing = db_fetch(
        "SELECT id, email FROM users WHERE email = :email OR username = :username LIMIT 1",
        ['email' => $demoEmail, 'username' => $demoUsername]
    );

    if ($existing) {
        echo "Demo user already exists (ID: {$existing['id']})." . PHP_EOL;
        exit(0);
    }

    db_execute(
        "INSERT INTO users (name, username, email, password_hash, role, api_token, status) VALUES (:name, :username, :email, :password_hash, :role, :api_token, :status)",
        [
            'name' => 'Demo User',
            'username' => $demoUsername,
            'email' => $demoEmail,
            'password_hash' => hash_password($password),
            'role' => 'user',
            'api_token' => generate_token(40),
            'status' => 'active',
        ]
    );

    echo "Demo user created.\nUsername: {$demoUsername}\nEmail: {$demoEmail}\nPassword: {$password}" . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    echo 'Error seeding demo user: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
