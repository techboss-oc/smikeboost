<?php
$hash = '$2y$10$h2Yp7DpCGN/KrPN8zC1lEOfYwqE1VU./uZrhCnlqSSaC8DZUHTvHG';
$password = 'admin123';

if (password_verify($password, $hash)) {
    echo "Hash matches admin123\n";
} else {
    echo "Hash DOES NOT match admin123\n";
}

echo "New hash for admin123: " . password_hash('admin123', PASSWORD_DEFAULT) . "\n";
