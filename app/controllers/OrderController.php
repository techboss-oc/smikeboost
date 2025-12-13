<?php
require_once APP_PATH . '/models/Order.php';
require_once APP_PATH . '/models/Service.php';

class OrderController
{
    public function handleCreateOrder()
    {
        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in first.');
            redirect('login');
        }

        $userId = $user['id'];
        $serviceId = sanitize($_POST['service_id'] ?? '');
        $link = sanitize($_POST['link'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (!$serviceId || !$link || !$quantity) {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                exit;
            }
            flash('error', 'All fields are required.');
            redirect('dashboard/new-order');
        }

        // Fetch service and user data
        $service = db_fetch("SELECT * FROM services WHERE id = :id", ['id' => $serviceId]);
        $userRow = db_fetch("SELECT wallet_balance FROM users WHERE id = :id", ['id' => $userId]);

        if (!$service) {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Service not found.']);
                exit;
            }
            flash('error', 'Service not found.');
            redirect('dashboard/new-order');
        }

        if (empty($service['provider_id'])) {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'This service is not available for ordering at the moment.']);
                exit;
            }
            flash('error', 'This service is not available for ordering at the moment.');
            redirect('dashboard/new-order');
        }

        // Calculate cost
        $amount = ($quantity / 1000) * $service['rate_per_1000'];
        $walletBalance = $userRow['wallet_balance'] ?? 0;

        if ($walletBalance < $amount) {
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Insufficient balance. Please add funds.']);
                exit;
            }
            flash('error', 'Insufficient balance. Please add funds.');
            redirect('dashboard/add-funds');
        }

        // Create order
        $orderModel = new Order();
        $orderId = $orderModel->create([
            'user_id' => $userId,
            'service_id' => $serviceId,
            'link' => $link,
            'quantity' => $quantity,
            'amount' => $amount,
            'status' => 'pending',
            'provider_order_id' => null,
        ]);

        // Try to fulfill the order
        try {
            $this->fulfillOrder($orderId);
        } catch (Exception $e) {
            // If fulfillment fails, refund and cancel
            $this->refundOrder($orderId, 'Fulfillment error: ' . $e->getMessage());
        }

        // Deduct from wallet
        db_execute(
            "UPDATE users SET wallet_balance = wallet_balance - :amount WHERE id = :id",
            ['amount' => $amount, 'id' => $userId]
        );

        // Create notification
        create_notification($userId, "Order #$orderId has been placed successfully!", 'shopping-cart', 'success');

        if (isset($_POST['ajax'])) {
            // Return JSON for AJAX requests
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'order_id' => $orderId,
                'message' => 'Order placed successfully!'
            ]);
            exit;
        }

        flash('success', 'Order #' . $orderId . ' placed successfully!');
        redirect('dashboard/orders');
    }

    private function fulfillOrder($orderId)
    {
        $order = db_fetch("SELECT o.*, s.provider_id, s.api_service_id, p.api_url, p.api_key FROM orders o JOIN services s ON s.id = o.service_id JOIN providers p ON p.id = s.provider_id WHERE o.id = :id", ['id' => $orderId]);
        if (!$order) return;

        $apiUrl = $order['api_url'];
        $apiKey = $order['api_key'];
        $serviceId = $order['api_service_id'];
        $link = $order['link'];
        $quantity = $order['quantity'];

        // Call provider API
        $postData = [
            'key' => $apiKey,
            'action' => 'add',
            'service' => $serviceId,
            'link' => $link,
            'quantity' => $quantity
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['order'])) {
                // Update order with provider_order_id and status to processing
                db_execute("UPDATE orders SET provider_order_id = :pid, status = 'processing' WHERE id = :id", ['pid' => $data['order'], 'id' => $orderId]);
            } else {
                // Failed, mark as failed and refund
                $this->refundOrder($orderId, 'API error: ' . ($data['error'] ?? 'Unknown'));
            }
        } else {
            // Failed, refund
            $this->refundOrder($orderId, 'Provider API unreachable');
        }
    }

    private function refundOrder($orderId, $reason)
    {
        $order = db_fetch("SELECT user_id, amount FROM orders WHERE id = :id", ['id' => $orderId]);
        if (!$order) return;

        // Refund to user
        db_execute("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :uid", ['amount' => $order['amount'], 'uid' => $order['user_id']]);

        // Update order status to canceled
        db_execute("UPDATE orders SET status = 'canceled' WHERE id = :id", ['id' => $orderId]);
    }

    public function handleMassOrder()
    {
        $user = current_user();
        if (!$user) redirect('login');

        $raw = $_POST['mass_orders'] ?? '';
        $lines = explode("\n", $raw);
        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = array_map('trim', explode('|', $line));
            if (count($parts) !== 3) {
                $failCount++;
                $errors[] = "Invalid format: $line";
                continue;
            }

            list($serviceId, $link, $quantity) = $parts;
            $quantity = (int)$quantity;

            // Validate Service
            $service = db_fetch("SELECT * FROM services WHERE id = :id", ['id' => $serviceId]);
            if (!$service) {
                $failCount++;
                $errors[] = "Service ID $serviceId not found";
                continue;
            }

            // Validate Quantity
            if ($quantity < $service['min_qty'] || $quantity > $service['max_qty']) {
                $failCount++;
                $errors[] = "Qty $quantity out of range for Service $serviceId";
                continue;
            }

            // Calculate Cost
            $amount = ($quantity / 1000) * $service['rate_per_1000'];
            
            // Check Balance (Re-fetch balance each time to be safe)
            $userRow = db_fetch("SELECT wallet_balance FROM users WHERE id = :id", ['id' => $user['id']]);
            if ($userRow['wallet_balance'] < $amount) {
                $failCount++;
                $errors[] = "Insufficient balance for line: $line";
                break; // Stop processing if out of money
            }

            // Deduct & Create
            db_execute("UPDATE users SET wallet_balance = wallet_balance - :amt WHERE id = :uid", ['amt' => $amount, 'uid' => $user['id']]);
            
            $orderModel = new Order();
            $orderId = $orderModel->create([
                'user_id' => $user['id'],
                'service_id' => $serviceId,
                'link' => $link,
                'quantity' => $quantity,
                'amount' => $amount,
                'status' => 'pending'
            ]);

            // Try to fulfill
            $this->fulfillOrder($orderId);
            
            $successCount++;
        }

        if ($failCount > 0) {
            flash('error', "$successCount orders placed. $failCount failed. " . implode(', ', array_slice($errors, 0, 3)));
        } else {
            flash('success', "All $successCount orders placed successfully!");
        }
        redirect('dashboard/mass-order');
    }

    public function handleChildPanel()
    {
        $user = current_user();
        if (!$user) redirect('login');

        $domain = sanitize($_POST['domain'] ?? '');
        $adminUser = sanitize($_POST['admin_username'] ?? '');
        $adminPass = $_POST['admin_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (!$domain || !$adminUser || !$adminPass) {
            flash('error', 'All fields are required.');
            redirect('dashboard/child-panel');
        }

        if ($adminPass !== $confirmPass) {
            flash('error', 'Passwords do not match.');
            redirect('dashboard/child-panel');
        }

        $price = get_setting('child_panel_price', 25000);
        
        // Check Balance
        $userRow = db_fetch("SELECT wallet_balance FROM users WHERE id = :id", ['id' => $user['id']]);
        if ($userRow['wallet_balance'] < $price) {
            flash('error', 'Insufficient balance. Please add funds.');
            redirect('dashboard/add-funds');
        }

        // Deduct Balance
        db_execute("UPDATE users SET wallet_balance = wallet_balance - :amt WHERE id = :uid", ['amt' => $price, 'uid' => $user['id']]);

        // Create Request
        db_execute(
            "INSERT INTO child_panels (user_id, domain, admin_username, admin_password, price_per_month, status) VALUES (:uid, :dom, :user, :pass, :price, 'pending')",
            [
                'uid' => $user['id'],
                'dom' => $domain,
                'user' => $adminUser,
                'pass' => password_hash($adminPass, PASSWORD_DEFAULT),
                'price' => $price
            ]
        );

        flash('success', 'Child panel order submitted successfully!');
        redirect('dashboard/child-panel');
    }
}
