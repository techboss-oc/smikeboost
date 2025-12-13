<?php

/**
 * Global Helper Functions
 */

/**
 * Get the URL for a page
 */
function url($page = '')
{
    $base = SITE_URL;
    return $page ? $base . '/' . trim($page, '/') : $base;
}

/**
 * CSRF token helper for public pages
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Asset URL helper
 */
function asset($path)
{
    return ASSETS_URL . '/' . trim($path, '/');
}

/**
 * Escape HTML
 */
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Get current page for navigation
 */
function is_active($page)
{
    $current = $_GET['page'] ?? 'home';
    return $current === $page ? 'active' : '';
}

/**
 * Format currency
 */
function format_currency($amount, $decimals = 2)
{
    return CURRENCY_SYMBOL . number_format($amount, $decimals);
}

/**
 * Get SEO title
 */
function seo_title($page_title)
{
    return $page_title . SEO_TITLE_SEPARATOR . SITE_NAME;
}

/**
 * Get SEO meta tags
 */
function get_seo_tags($page_title, $description = '', $keywords = '', $image = '')
{
    $title = seo_title($page_title);
    $description = $description ?: SITE_DESCRIPTION;
    $keywords = $keywords ?: SITE_KEYWORDS;
    $image = $image ?: asset('images/og-image.png');
    $url = url();

    return [
        'title' => $title,
        'description' => $description,
        'keywords' => $keywords,
        'image' => $image,
        'url' => $url
    ];
}

/**
 * Is user logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 */
function current_user()
{
    return $_SESSION['user'] ?? null;
}

/**
 * Redirect to page
 */
function redirect($page)
{
    header('Location: ' . url($page));
    exit;
}

/**
 * Time ago helper
 */
function time_ago($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', $time);
}

/**
 * Create user notification
 */
function create_notification($userId, $message, $icon = 'info-circle', $color = 'primary')
{
    try {
        db_execute(
            "INSERT INTO notifications (user_id, type, message, icon, color, is_active, created_at) VALUES (:user_id, 'user', :message, :icon, :color, 1, NOW())",
            ['user_id' => $userId, 'message' => $message, 'icon' => $icon, 'color' => $color]
        );
    } catch (Exception $e) {
        // Notifications table may not be set up
    }
}

/**
 * Flash message helper
 */
function flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return $message;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $msg = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $msg;
}

/**
 * Format date
 */
function format_date($date, $format = 'd M Y')
{
    return date($format, strtotime($date));
}

/**
 * Generate random string
 */
function generate_token($length = 32)
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Hash password
 */
function hash_password($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verify_password($password, $hash)
{
    if ($hash === null || $hash === '') {
        return false;
    }

    // Native password_hash formats (bcrypt/argon)
    if (strpos($hash, '$2y$') === 0 || strpos($hash, '$argon2') === 0) {
        return password_verify($password, $hash);
    }

    // Allow legacy hex digests (sha256/sha1/md5) for manually updated records
    if (ctype_xdigit($hash)) {
        $lowerHash = strtolower($hash);
        $length = strlen($hash);

        if ($length === 64) {
            return hash_equals($lowerHash, hash('sha256', $password));
        }

        if ($length === 40) {
            return hash_equals($lowerHash, hash('sha1', $password));
        }

        if ($length === 32) {
            return hash_equals($lowerHash, md5($password));
        }
    }

    // Fallback attempt for any other algorithm supported by password_verify
    return password_verify($password, $hash);
}

/**
 * Sanitize input
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validate_email($email)
{
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate username (3-32 chars, alphanumeric, underscore, dot, dash)
 */
function validate_username($username)
{
    return (bool) preg_match('/^[A-Za-z0-9_.-]{3,32}$/', $username);
}

/**
 * Get form value from POST/GET
 */
function form_value($key, $default = '')
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Ensure a demo user exists for testing without failing the request if DB is unavailable
 */
function ensure_demo_user()
{
    try {
        $demoEmail = 'demo@smikeboost.com';
        $demoUsername = 'demo';

        $existing = db_fetch(
            "SELECT id, api_token FROM users WHERE email = :email OR username = :username LIMIT 1",
            ['email' => $demoEmail, 'username' => $demoUsername]
        );

        if ($existing) {
            if (empty($existing['api_token'])) {
                db_execute(
                    "UPDATE users SET api_token = :token WHERE id = :id",
                    ['token' => generate_token(40), 'id' => $existing['id']]
                );
            }
            return;
        }

        db_execute(
            "INSERT INTO users (name, username, email, password_hash, role, api_token, status) VALUES (:name, :username, :email, :password_hash, :role, :api_token, :status)",
            [
                'name' => 'Demo User',
                'username' => $demoUsername,
                'email' => $demoEmail,
                'password_hash' => hash_password('demo1234'),
                'role' => 'user',
                'api_token' => generate_token(40),
                'status' => 'active',
            ]
        );
    } catch (Throwable $e) {
        // Silently ignore to keep bootstrap resilient if DB is offline
    }
}

function log_settings_error($message)
{
    $file = APP_PATH . '/settings_debug.log';
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    file_put_contents($file, $line, FILE_APPEND);
}

function ensure_settings_table_exists()
{
    static $settingsReady = false;
    if ($settingsReady) {
        return true;
    }

    try {
        // First, check if settings table exists at all
        $tableCheck = db_fetch("SHOW TABLES LIKE 'settings'");

        if (!$tableCheck) {
            // Table doesn't exist, create it fresh
            db_execute("CREATE TABLE settings (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `setting_key` VARCHAR(100) NOT NULL UNIQUE,
                `setting_value` TEXT,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        } else {
            // Table exists - check if it has the right columns
            $columns = db_fetch_all("SHOW COLUMNS FROM settings");
            $columnNames = array_column($columns, 'Field');

            // Check if we have old column names (key/value) vs new (setting_key/setting_value)
            if (in_array('key', $columnNames) && !in_array('setting_key', $columnNames)) {
                // Old schema - rename columns
                db_execute("ALTER TABLE settings CHANGE COLUMN `key` `setting_key` VARCHAR(100) NOT NULL UNIQUE");
                log_settings_error("Migrated column 'key' to 'setting_key'");
            }
            if (in_array('value', $columnNames) && !in_array('setting_value', $columnNames)) {
                db_execute("ALTER TABLE settings CHANGE COLUMN `value` `setting_value` TEXT");
                log_settings_error("Migrated column 'value' to 'setting_value'");
            }

            // If neither old nor new columns exist, add them
            if (!in_array('setting_key', $columnNames) && !in_array('key', $columnNames)) {
                db_execute("ALTER TABLE settings ADD COLUMN `setting_key` VARCHAR(100) NOT NULL UNIQUE");
            }
            if (!in_array('setting_value', $columnNames) && !in_array('value', $columnNames)) {
                db_execute("ALTER TABLE settings ADD COLUMN `setting_value` TEXT");
            }
        }

        $settingsReady = true;
        return true;
    } catch (Exception $e) {
        log_settings_error('ensure_settings_table_exists failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get a system setting
 */
function get_setting($key, $default = null)
{
    if (!ensure_settings_table_exists()) {
        return $default;
    }

    try {
        $row = db_fetch("SELECT setting_value FROM settings WHERE setting_key = :key", ['key' => $key]);
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        log_settings_error('get_setting failed: ' . $e->getMessage());
        return $default;
    }
}

/**
 * Update a system setting
 */
function update_setting($key, $value)
{
    if (!ensure_settings_table_exists()) {
        log_settings_error("update_setting: settings table does not exist");
        return false;
    }

    try {
        // Check if key exists using setting_key column (not id)
        $existing = db_fetch("SELECT setting_key FROM settings WHERE setting_key = :key", ['key' => $key]);

        if ($existing) {
            db_execute(
                "UPDATE settings SET setting_value = :value WHERE setting_key = :key",
                ['key' => $key, 'value' => $value]
            );
        } else {
            db_execute(
                "INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)",
                ['key' => $key, 'value' => $value]
            );
        }
        return true;
    } catch (Exception $e) {
        log_settings_error('update_setting failed for key "' . $key . '": ' . $e->getMessage());
        return false;
    }
}

function normalize_provider_status_text($text)
{
    $t = strtolower(trim((string)$text));
    if ($t === '') return 'pending';
    $map = [
        'completed' => 'completed',
        'complete' => 'completed',
        'success' => 'completed',
        'finished' => 'completed',
        'processing' => 'processing',
        'in progress' => 'processing',
        'pending' => 'pending',
        'partial' => 'processing',
        'on hold' => 'processing',
        'hold' => 'processing',
        'paused' => 'processing',
        'failed' => 'canceled',
        'canceled' => 'canceled',
        'cancelled' => 'canceled',
        'refunded' => 'canceled',
    ];
    if (isset($map[$t])) return $map[$t];
    if (strpos($t, 'progress') !== false) return 'processing';
    if (strpos($t, 'cancel') !== false) return 'canceled';
    if (strpos($t, 'complete') !== false || strpos($t, 'success') !== false || strpos($t, 'finish') !== false) return 'completed';
    return 'processing';
}

function get_order_metrics_for_user_orders($orders)
{
    $metrics = [];
    if (empty($orders)) return $metrics;
    $providers = [];
    foreach ($orders as $o) {
        $pid = isset($o['provider_id']) ? $o['provider_id'] : null;
        if ($pid && !isset($providers[$pid])) {
            $row = db_fetch('SELECT id, api_url, api_key FROM providers WHERE id = :id', ['id' => $pid]);
            if ($row) $providers[$pid] = $row;
        }
    }
    foreach ($orders as $o) {
        $id = isset($o['id']) ? (int)$o['id'] : 0;
        if ($id <= 0) continue;
        $metrics[$id] = [
            'charge' => isset($o['amount']) ? (float)$o['amount'] : 0,
            'start_count' => '0',
            'remains' => '0',
            'avg_time' => '-',
            'status' => isset($o['status']) ? strtolower((string)$o['status']) : 'pending',
        ];
        $provId = isset($o['provider_id']) ? $o['provider_id'] : null;
        $provOrderId = isset($o['provider_order_id']) ? $o['provider_order_id'] : null;
        if (!$provId || !$provOrderId) continue;
        $provider = isset($providers[$provId]) ? $providers[$provId] : null;
        if (!$provider) continue;
        $status = $metrics[$id]['status'];
        if ($status === 'pending' || $status === 'processing') {
            $resp = null;
            if (function_exists('safe_admin_provider_request')) {
                $resp = safe_admin_provider_request($provider, 'status', ['order' => $provOrderId]);
            } else {
                // Minimal fallback request
                $url = rtrim(isset($provider['api_url']) ? $provider['api_url'] : '', '/');
                if ($url && stripos($url, '/api') === false) $url .= '/api/v2';
                $key = isset($provider['api_key']) ? $provider['api_key'] : '';
                if ($url && $key) {
                    $body = http_build_query(['key' => $key, 'action' => 'status', 'order' => $provOrderId]);
                    $ctx = stream_context_create([
                        'http' => [
                            'method' => 'POST',
                            'header' => "Content-type: application/x-www-form-urlencoded\r\nUser-Agent: BoostPanel/1.0\r\nAccept: application/json, text/plain",
                            'content' => $body,
                            'timeout' => 15,
                        ],
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);
                    $raw = @file_get_contents($url, false, $ctx);
                    if ($raw === false || $raw === '') {
                        $qUrl = $url . (strpos($url, '?') === false ? '?' : '&') . $body;
                        $raw = @file_get_contents($qUrl);
                    }
                    if ($raw) {
                        $resp = json_decode($raw, true);
                        if (!is_array($resp)) $resp = ['raw' => trim((string)$raw)];
                    }
                }
            }
            if (is_array($resp)) {
                $statusText = isset($resp['status']) ? (string)$resp['status'] : '';
                $metrics[$id]['status'] = normalize_provider_status_text($statusText);
                $metrics[$id]['status_text'] = $statusText !== '' ? $statusText : $metrics[$id]['status'];
                if (isset($resp['start_count'])) $metrics[$id]['start_count'] = (string)$resp['start_count'];
                elseif (isset($resp['start'])) $metrics[$id]['start_count'] = (string)$resp['start'];
                if (isset($resp['remains'])) $metrics[$id]['remains'] = (string)$resp['remains'];
                elseif (isset($resp['remain'])) $metrics[$id]['remains'] = (string)$resp['remain'];
            }
        }
    }
    return $metrics;
}
