<?php
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Order.php';

class ApiController
{
    private function respond($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function authenticate()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? ($headers['X-API-KEY'] ?? $headers['x-api-key'] ?? '');

        if (!$apiKey) {
            $this->respond(['status' => 'error', 'message' => 'API key required. Use api_key query/body param or X-API-KEY header.'], 401);
        }

        $userModel = new User();
        $user = $userModel->findByApiToken($apiKey);
        if (!$user) {
            $this->respond(['status' => 'error', 'message' => 'Invalid API key'], 401);
        }
        return $user;
    }

    private function authenticateKey($apiKey)
    {
        $userModel = new User();
        $user = $userModel->findByApiToken($apiKey);
        if (!$user) {
            $this->respond(['error' => 'Invalid API key'], 401);
        }
        return $user;
    }

    public function handle($page)
    {
        // page looks like api/services or api/order
        $endpoint = trim(substr($page, 4), '/');

        // V2 unified endpoint: /api/v2 with POST action
        if ($endpoint === 'v2') {
            return $this->handleV2();
        }

        if ($endpoint === 'services') {
            return $this->services();
        }
        if ($endpoint === 'balance') {
            return $this->balance();
        }
        if ($endpoint === 'order') {
            return $this->createOrder();
        }
        if ($endpoint === 'status') {
            return $this->orderStatus();
        }

        $this->respond(['status' => 'error', 'message' => 'Not found'], 404);
    }

    private function handleV2()
    {
        $action = strtolower(trim($_POST['action'] ?? ''));
        $apiKey = $_POST['key'] ?? '';

        if (!$action || !$apiKey) {
            $this->respond(['error' => 'Missing action or key'], 400);
        }

        $user = $this->authenticateKey($apiKey);

        switch ($action) {
            case 'services':
                return $this->servicesV2();
            case 'balance':
                return $this->balanceV2($user);
            case 'add':
                return $this->addOrderV2($user);
            case 'status':
                return $this->statusV2($user);
            case 'refill':
            case 'refill_status':
            case 'cancel':
                // Not implemented; respond gracefully
                $this->respond(['error' => 'Action not supported'], 400);
            default:
                $this->respond(['error' => 'Unknown action'], 400);
        }
    }

    private function services()
    {
        $services = db_fetch_all("SELECT id, name, category, platform, rate_per_1000, min_qty, max_qty, status FROM services WHERE status = 'enabled' ORDER BY platform, name");
        $this->respond([
            'status' => 'success',
            'services' => $services,
        ]);
    }

    private function servicesV2()
    {
        $rows = db_fetch_all("SELECT id, name, category, platform, rate_per_1000, min_qty, max_qty FROM services WHERE status = 'enabled' ORDER BY platform, name");
        $services = [];
        foreach ($rows as $row) {
            $services[] = [
                'service' => (int)$row['id'],
                'name' => $row['name'],
                'type' => 'Default',
                'category' => $row['category'],
                'rate' => (string)$row['rate_per_1000'],
                'min' => (string)$row['min_qty'],
                'max' => (string)$row['max_qty'],
                'refill' => false,
                'cancel' => true,
            ];
        }
        $this->respond($services);
    }

    private function balance()
    {
        $user = $this->authenticate();
        $this->respond([
            'status' => 'success',
            'balance' => (float) $user['wallet_balance'],
            'currency' => CURRENCY_CODE,
        ]);
    }

    private function balanceV2($user)
    {
        $this->respond([
            'balance' => (string)$user['wallet_balance'],
            'currency' => CURRENCY_CODE,
        ]);
    }

    private function createOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['status' => 'error', 'message' => 'Method not allowed'], 405);
        }

        $user = $this->authenticate();

        $serviceId = (int) ($_POST['service_id'] ?? 0);
        $link = trim($_POST['link'] ?? '');
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if (!$serviceId || !$link || !$quantity) {
            $this->respond(['status' => 'error', 'message' => 'service_id, link and quantity are required'], 400);
        }

        $service = db_fetch("SELECT * FROM services WHERE id = :id AND status = 'enabled'", ['id' => $serviceId]);
        if (!$service) {
            $this->respond(['status' => 'error', 'message' => 'Service not found or disabled'], 404);
        }

        if ($quantity < $service['min_qty'] || $quantity > $service['max_qty']) {
            $this->respond([
                'status' => 'error',
                'message' => 'Quantity must be between ' . $service['min_qty'] . ' and ' . $service['max_qty'],
            ], 400);
        }

        $amount = ($quantity / 1000) * $service['rate_per_1000'];

        if ($user['wallet_balance'] < $amount) {
            $this->respond([
                'status' => 'error',
                'message' => 'Insufficient balance',
                'required' => $amount,
                'available' => (float) $user['wallet_balance'],
            ], 400);
        }

        // Create order
        $orderModel = new Order();
        $orderId = $orderModel->create([
            'user_id' => $user['id'],
            'service_id' => $serviceId,
            'link' => $link,
            'quantity' => $quantity,
            'amount' => $amount,
            'status' => 'pending',
            'provider_order_id' => null,
        ]);

        // Deduct balance
        db_execute("UPDATE users SET wallet_balance = wallet_balance - :amount WHERE id = :id", [
            'amount' => $amount,
            'id' => $user['id'],
        ]);

        $this->respond([
            'status' => 'success',
            'order_id' => $orderId,
            'amount' => $amount,
            'currency' => CURRENCY_CODE,
        ]);
    }

    private function addOrderV2($user)
    {
        $serviceId = (int) ($_POST['service'] ?? 0);
        $link = trim($_POST['link'] ?? '');
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if (!$serviceId || !$link || !$quantity) {
            $this->respond(['error' => 'service, link and quantity are required'], 400);
        }

        $service = db_fetch("SELECT * FROM services WHERE id = :id AND status = 'enabled'", ['id' => $serviceId]);
        if (!$service) {
            $this->respond(['error' => 'Service not found'], 404);
        }

        if ($quantity < $service['min_qty'] || $quantity > $service['max_qty']) {
            $this->respond(['error' => 'Quantity must be between ' . $service['min_qty'] . ' and ' . $service['max_qty']], 400);
        }

        $amount = ($quantity / 1000) * $service['rate_per_1000'];
        if ($user['wallet_balance'] < $amount) {
            $this->respond(['error' => 'Insufficient balance'], 400);
        }

        $orderModel = new Order();
        $orderId = $orderModel->create([
            'user_id' => $user['id'],
            'service_id' => $serviceId,
            'link' => $link,
            'quantity' => $quantity,
            'amount' => $amount,
            'status' => 'pending',
            'provider_order_id' => null,
        ]);

        db_execute("UPDATE users SET wallet_balance = wallet_balance - :amount WHERE id = :id", [
            'amount' => $amount,
            'id' => $user['id'],
        ]);

        $this->respond(['order' => (int)$orderId]);
    }

    private function orderStatus()
    {
        $user = $this->authenticate();
        // Bulk: ?order_ids=1,2,3 or single: ?order_id=1
        $orderIdsParam = $_GET['order_ids'] ?? '';
        if ($orderIdsParam) {
            $ids = array_filter(array_unique(array_map('intval', explode(',', $orderIdsParam))));
            if (empty($ids)) {
                $this->respond(['status' => 'error', 'message' => 'order_ids must contain at least one id'], 400);
            }
            // Enforce sane upper bound
            if (count($ids) > 50) {
                $this->respond(['status' => 'error', 'message' => 'Too many order_ids; max 50 per request'], 400);
            }
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT id, user_id, service_id, status, amount, created_at, provider_order_id FROM orders WHERE user_id = ? AND id IN ($placeholders)";
            $stmt = db()->prepare($sql);
            $stmt->execute(array_merge([$user['id']], $ids));
            $rows = $stmt->fetchAll();
            $orders = [];
            foreach ($rows as $row) {
                $orders[] = [
                    'id' => (int) $row['id'],
                    'service_id' => (int) $row['service_id'],
                    'state' => $row['status'],
                    'amount' => (float) $row['amount'],
                    'created_at' => $row['created_at'],
                    'provider_order_id' => $row['provider_order_id'],
                ];
            }
            $this->respond(['status' => 'success', 'orders' => $orders]);
        }

        $orderId = (int) ($_GET['order_id'] ?? 0);
        if (!$orderId) {
            $this->respond(['status' => 'error', 'message' => 'order_id is required'], 400);
        }

        $order = db_fetch("SELECT id, user_id, service_id, status, amount, created_at, provider_order_id FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order || (int)$order['user_id'] !== (int)$user['id']) {
            $this->respond(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $this->respond([
            'status' => 'success',
            'order' => [
                'id' => (int) $order['id'],
                'service_id' => (int) $order['service_id'],
                'state' => $order['status'],
                'amount' => (float) $order['amount'],
                'created_at' => $order['created_at'],
                'provider_order_id' => $order['provider_order_id'],
            ],
        ]);
    }

    private function statusV2($user)
    {
        $ordersParam = $_POST['orders'] ?? '';
        if ($ordersParam) {
            $ids = array_filter(array_unique(array_map('intval', explode(',', $ordersParam))));
            if (count($ids) > 100) {
                $this->respond(['error' => 'Too many order IDs; max 100'], 400);
            }
            $result = [];
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql = "SELECT id, user_id, service_id, status, amount, created_at, provider_order_id FROM orders WHERE user_id = ? AND id IN ($placeholders)";
                $stmt = db()->prepare($sql);
                $stmt->execute(array_merge([$user['id']], $ids));
                $rows = $stmt->fetchAll();
                $found = [];
                foreach ($rows as $row) {
                    $found[(int)$row['id']] = $row;
                }
                foreach ($ids as $id) {
                    if (!isset($found[$id])) {
                        $result[(string)$id] = ['error' => 'Incorrect order ID'];
                        continue;
                    }
                    $row = $found[$id];
                    $result[(string)$id] = [
                        'charge' => (string)$row['amount'],
                        'start_count' => '0',
                        'status' => $row['status'],
                        'remains' => '0',
                        'currency' => CURRENCY_CODE,
                    ];
                }
            }
            $this->respond($result ?: new stdClass());
        }

        $orderId = (int) ($_POST['order'] ?? 0);
        if (!$orderId) {
            $this->respond(['error' => 'order is required'], 400);
        }
        $order = db_fetch("SELECT id, user_id, service_id, status, amount, created_at, provider_order_id FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order || (int)$order['user_id'] !== (int)$user['id']) {
            $this->respond(['error' => 'Incorrect order ID'], 404);
        }

        $this->respond([
            'charge' => (string)$order['amount'],
            'start_count' => '0',
            'status' => $order['status'],
            'remains' => '0',
            'currency' => CURRENCY_CODE,
        ]);
    }
}
