<?php
/**
 * Cron script for order status checks and auto refunds
 * Run this periodically, e.g., every 5 minutes
 */

// Load config
require_once dirname(__DIR__) . '/app/config/config.php';

$pendingOrders = db_fetch_all("SELECT o.id, o.user_id, o.provider_order_id, p.api_url, p.api_key FROM orders o JOIN services s ON s.id = o.service_id JOIN providers p ON p.id = s.provider_id WHERE o.status = 'processing' AND o.provider_order_id IS NOT NULL");

foreach ($pendingOrders as $order) {
    $apiUrl = $order['api_url'];
    $apiKey = $order['api_key'];
    $providerOrderId = $order['provider_order_id'];

    $postData = [
        'key' => $apiKey,
        'action' => 'status',
        'order' => $providerOrderId
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
        $status = $data['status'] ?? 'unknown';

        if ($status === 'Completed' || $status === 'completed') {
            db_execute("UPDATE orders SET status = 'completed' WHERE id = :id", ['id' => $order['id']]);
            create_notification($order['user_id'], "Order #{$order['id']} has been completed successfully!", 'check-circle', 'success');
        } elseif ($status === 'Canceled' || $status === 'canceled' || $status === 'Failed' || $status === 'failed') {
            // Refund
            refundOrder($order['id'], 'Order ' . $status . ' by provider');
            create_notification($order['user_id'], "Order #{$order['id']} has been canceled and refunded.", 'times-circle', 'danger');
        }
        // If still processing, leave as is
    }
}

// Check for old processing orders (e.g., 24 hours)
$oldOrders = db_fetch_all("SELECT id, user_id FROM orders WHERE status = 'processing' AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
foreach ($oldOrders as $old) {
    refundOrder($old['id'], 'Order timeout after 24 hours');
    create_notification($old['user_id'], "Order #{$old['id']} has timed out and been refunded.", 'clock', 'warning');
}

function refundOrder($orderId, $reason) {
    $order = db_fetch("SELECT user_id, amount FROM orders WHERE id = :id", ['id' => $orderId]);
    if (!$order) return;

    db_execute("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :uid", ['amount' => $order['amount'], 'uid' => $order['user_id']]);
    db_execute("UPDATE orders SET status = 'canceled' WHERE id = :id", ['id' => $orderId]);
}

echo "Cron completed\n";
?>