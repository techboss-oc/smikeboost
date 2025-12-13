<?php
require_once dirname(__DIR__) . '/../app/config/config.php';
require_once APP_PATH . '/controllers/AdminController.php';

$controller = new AdminController();

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
// Expecting paths like 'boost/public/admin/...'
$parts = explode('/', $path);
$last = end($parts);

$action = 'dashboard';
$subAction = null;

if ($last === 'login') $action = 'login';
elseif ($last === 'logout') $action = 'logout';
elseif ($last === 'orders') $action = 'orders';
elseif ($last === 'users') $action = 'users';
elseif ($last === 'user-edit') $action = 'userEdit';
elseif ($last === 'impersonate') $action = 'impersonate';
elseif ($last === 'services') $action = 'services';
elseif ($last === 'providers') $action = 'providers';
elseif ($last === 'payments') $action = 'payments';
elseif ($last === 'transactions') $action = 'transactions';
elseif ($last === 'tickets') $action = 'tickets';
elseif ($last === 'blog') $action = 'blog';
elseif ($last === 'settings') $action = 'settings';
elseif ($last === 'notifications') $action = 'notifications';
elseif ($last === 'newsletter') $action = 'newsletter';
elseif ($last === 'announcements') $action = 'announcements';
elseif ($last === 'emails') $action = 'emails';
elseif ($last === 'update') {
    // Handle sub-routes like admin/settings/update or admin/payments/update
    $prev = prev($parts);
    if ($prev === 'settings') {
        $action = 'updateSettings';
    } elseif ($prev === 'payments') {
        $action = 'payments';
        $subAction = 'update';
    }
} elseif ($last === 'cancel') {
    $prev = prev($parts);
    if ($prev === 'orders') {
        $action = 'cancelOrder';
    }
}

// Allow POST directly to payments or settings without /update suffix
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($last === 'payments') {
        $action = 'payments';
        $subAction = 'update';
    } elseif ($last === 'settings') {
        $action = 'updateSettings';
    }
}

if (!method_exists($controller, $action)) {
    http_response_code(404);
    exit('Not found');
}

if ($subAction !== null) {
    $controller->{$action}($subAction);
} else {
    $controller->{$action}();
}
