<?php
if (!defined('ADMIN_SESSION_KEY')) {
    require_once APP_PATH . '/config/admin.php';
}
function admin_url($path = '')
{
    $base = SITE_URL . '/admin';
    return $path ? $base . '/' . trim($path, '/') : $base;
}
function admin_asset($path)
{
    return ASSETS_URL . '/' . trim($path, '/');
}
function public_url($path = '')
{
    $base = SITE_URL;
    $base = preg_replace('#/(?:public/)?admin$#', '', $base);
    $base = preg_replace('#/public$#', '', $base);
    return $path ? rtrim($base, '/') . '/' . trim($path, '/') : rtrim($base, '/');
}
function admin_is_logged_in()
{
    return isset($_SESSION[ADMIN_SESSION_KEY]);
}
function admin_require_login()
{
    if (!admin_is_logged_in()) {
        redirect('admin/login');
    }
}
function admin_login($user)
{
    $_SESSION[ADMIN_SESSION_KEY] = $user;
}
function admin_logout()
{
    unset($_SESSION[ADMIN_SESSION_KEY]);
    session_regenerate_id(true);
}
function admin_current_user()
{
    return $_SESSION[ADMIN_SESSION_KEY] ?? null;
}
function admin_csrf_token()
{
    if (empty($_SESSION[ADMIN_CSRF_KEY])) {
        $_SESSION[ADMIN_CSRF_KEY] = bin2hex(random_bytes(32));
    }
    return $_SESSION[ADMIN_CSRF_KEY];
}
function admin_verify_csrf($token)
{
    return hash_equals($_SESSION[ADMIN_CSRF_KEY] ?? '', $token ?? '');
}
function admin_flash($key, $message = null)
{
    if ($message !== null) {
        $_SESSION['admin_flash'][$key] = $message;
        return $message;
    }
    if (!isset($_SESSION['admin_flash'][$key])) {
        return null;
    }
    $msg = $_SESSION['admin_flash'][$key];
    unset($_SESSION['admin_flash'][$key]);
    return $msg;
}
function admin_rate_limit_ok()
{
    $key = 'admin_rate_limit';
    $now = time();
    $window = 60;
    $limit = ADMIN_RATE_LIMIT_PER_MINUTE;
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    $data = &$_SESSION[$key];
    if ($now - $data['start'] > $window) {
        $data = ['count' => 1, 'start' => $now];
        return true;
    }
    if ($data['count'] >= $limit) {
        return false;
    }
    $data['count']++;
    return true;
}
function admin_ip_allowed($ip = null)
{
    $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '');
    if (empty(ADMIN_ALLOWED_IPS)) return true;
    return in_array($ip, ADMIN_ALLOWED_IPS, true);
}
function admin_provider_request(array $provider, string $action, array $payload = [])
{
    $url = rtrim($provider['api_url'] ?? '', '/');
    if ($url && stripos($url, '/api') === false) {
        $url .= '/api/v2';
    }
    $key = $provider['api_key'] ?? '';
    if (!$url || !$key) {
        return ['error' => 'Invalid provider configuration'];
    }

    $params = array_merge($payload, ['key' => $key, 'action' => $action]);
    $body = json_encode($params);
    $ua = 'BoostPanel/1.0';

    $resp = null;
    $err = null;
    $code = null;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ]
        ]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false || $resp === '') {
            $qUrl = $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
            $ch2 = curl_init($qUrl);
            curl_setopt_array($ch2, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 12,
                CURLOPT_USERAGENT => $ua,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json'
                ]
            ]);
            $resp = curl_exec($ch2);
            $err = curl_error($ch2);
            $code = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
            curl_close($ch2);
        }
    } else {
        // Fallback to file_get_contents
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\nUser-Agent: $ua\r\n",
                'content' => $body,
                'timeout' => 15,
                'ignore_errors' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ];
        $ctx = stream_context_create($opts);
        $resp = @file_get_contents($url, false, $ctx);

        if ($resp === false || $resp === '') {
            $qUrl = $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
            $optsG = [
                'http' => [
                    'method' => 'GET',
                    'header' => "Accept: application/json\r\nUser-Agent: $ua\r\n",
                    'timeout' => 15,
                    'ignore_errors' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ];
            $ctxG = stream_context_create($optsG);
            $resp = @file_get_contents($qUrl, false, $ctxG);
        }

        if ($resp === false || $resp === '') {
            // Last resort: basic socket connection
            $host = parse_url($url, PHP_URL_HOST);
            $scheme = parse_url($url, PHP_URL_SCHEME);
            $path = parse_url($url, PHP_URL_PATH) ?: '/';
            $port = ($scheme === 'https' ? 443 : 80);
            $addr = ($scheme === 'https' ? 'ssl://' : '') . $host;
            $fp = @fsockopen($addr, $port, $errno, $errstr, 10);
            if ($fp) {
                $reqPath = $path . (strpos($path, '?') === false ? '?' : '&') . http_build_query($params);
                $req = "GET {$reqPath} HTTP/1.0\r\nHost: {$host}\r\nAccept: application/json\r\nUser-Agent: {$ua}\r\nConnection: close\r\n\r\n";
                fwrite($fp, $req);
                $raw = '';
                while (!feof($fp)) {
                    $raw .= fgets($fp, 2048);
                }
                fclose($fp);
                $pos = strpos($raw, "\r\n\r\n");
                if ($pos !== false) {
                    $resp = substr($raw, $pos + 4);
                }
            }
        }
    }

    if ($err) {
        return ['error' => $err];
    }

    $data = json_decode($resp, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        return $data;
    }

    return ['error' => 'Invalid response format'];
}

function admin_ensure_provider_balance_column()
{
    $col = db_fetch("SHOW COLUMNS FROM providers LIKE 'balance'");
    if ($col) {
        return true;
    }
    db_execute("ALTER TABLE providers ADD COLUMN balance DECIMAL(10,2) DEFAULT 0.00");
    return false;
}

function admin_normalize_provider_response($resp)
{
    if (isset($resp['data']) && is_array($resp['data'])) {
        $resp = $resp['data'];
    } elseif (isset($resp['services']) && is_array($resp['services'])) {
        $resp = $resp['services'];
    } elseif (isset($resp['list']) && is_array($resp['list'])) {
        $resp = $resp['list'];
    }
    if (!is_array($resp)) {
        return [];
    }

    $items = [];
    foreach ($resp as $item) {
        if (!is_array($item)) {
            continue;
        }
        $id = $item['service'] ?? $item['id'] ?? null;
        if ($id === null) {
            continue;
        }
        $name = $item['name'] ?? '';
        $category = $item['category'] ?? ($item['cat'] ?? ($item['group'] ?? 'Uncategorized'));
        $platform = $item['type'] ?? ($item['platform'] ?? '');
        $rate = isset($item['rate']) ? (float)$item['rate'] : (float)($item['price'] ?? ($item['price_per_1000'] ?? 0));
        $min = (int)($item['min'] ?? ($item['minimum'] ?? 1));
        $max = (int)($item['max'] ?? ($item['maximum'] ?? 0));
        $disabled = $item['disabled'] ?? false;
        $description = $item['description'] ?? '';

        $items[] = [
            'id' => $id,
            'name' => $name,
            'category' => $category,
            'platform' => $platform,
            'rate' => $rate,
            'min' => $min,
            'max' => $max,
            'disabled' => $disabled,
            'description' => $description
        ];
    }
    return $items;
}

function admin_parse_provider_services($text)
{
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $items = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (preg_match('/^\d+/', $line)) {
            $parts = explode('|', $line);
            if (count($parts) >= 5) {
                $id = (int)$parts[0];
                $name = (string)$parts[1];
                $rate = (float)$parts[2];
                $min = (int)$parts[3];
                $max = (int)$parts[4];
                $category = isset($parts[5]) ? (string)$parts[5] : 'Uncategorized';
            } else {
                $parts = preg_split('/\s+/', $line, 5);
                if (count($parts) >= 4) {
                    $id = (int)$parts[0];
                    $name = (string)$parts[1];
                    $rate = (float)$parts[2];
                    $min = (int)$parts[3];
                    $max = isset($parts[4]) ? (int)$parts[4] : 0;
                    $category = 'Uncategorized';
                } else {
                    continue;
                }
            }
            if ($id <= 0 || $name === '') continue;
            $items[] = [
                'id' => $id,
                'name' => $name,
                'category' => $category,
                'platform' => '',
                'rate' => $rate,
                'min' => $min,
                'max' => $max,
                'disabled' => false,
                'description' => ''
            ];
        }
    }
    return $items;
}