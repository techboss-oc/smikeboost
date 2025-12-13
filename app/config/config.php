<?php

/**
 * SmikeBoost Configuration File
 * Central configuration for the entire application
 */

// Site Configuration
define('SITE_NAME', 'SmikeBoost');

// Auto-detect scheme/host for local IP or hostname access (Laragon/LAN)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Determine base path without exposing the /public directory in URLs
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$scriptDir = ($scriptDir === '/' || $scriptDir === '\\') ? '' : rtrim($scriptDir, '/');
$basePath = preg_replace('#/public$#', '', $scriptDir ?? '');
$basePath = trim($basePath, '/');
$basePath = $basePath ? '/' . $basePath : '';
define('BASE_URI', $basePath);
define('SITE_URL', rtrim($scheme . '://' . $host . $basePath, '/'));
define('SITE_DESCRIPTION', 'Nigeria’s trusted SMM panel for Instagram, TikTok, YouTube, Facebook and more. Grow authentic engagement, automate reseller orders and accept Naira payments.');
define('SITE_KEYWORDS', 'SMM panel Nigeria, buy Instagram followers Nigeria, TikTok growth NG, social media marketing Lagos, cheap SMM services Africa');

// Database Configuration (for future use)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smikeboost');

// Payment Configuration
define('PAYMENT_GATEWAY', 'flutterwave');
define('CURRENCY', 'NGN');
define('CURRENCY_SYMBOL', '&#8358;');
define('CURRENCY_CODE', 'NGN');

// API Configuration
define('API_BASE_URL', SITE_URL . '/api');

// Paths
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');
define('ASSETS_URL', SITE_URL . '/assets');

// Security
define('SESSION_TIMEOUT', 3600); // 1 hour
define('JWT_SECRET', 'your_jwt_secret_key_here');
define('HASH_ALGO', 'bcrypt');
define('ENABLE_DEMO_USER', true);

// Mail Configuration
define('MAIL_FROM', 'noreply@smikeboost.com');
define('MAIL_FROM_NAME', 'SmikeBoost');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your_email@gmail.com');
define('MAIL_PASSWORD', 'your_app_password');

// SEO Configuration
define('SEO_TITLE_SEPARATOR', ' | ');

// Timezone
date_default_timezone_set('Africa/Lagos');

// Error Reporting (enable on localhost by default for debugging)
$env = getenv('ENVIRONMENT') ?: ((($_SERVER['HTTP_HOST'] ?? '') === 'localhost') ? 'development' : 'production');
if ($env === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoload helpers
require_once APP_PATH . '/helpers/helpers.php';
require_once APP_PATH . '/helpers/db.php';
