<?php
/**
 * Dashboard Orders Page
 */
$seo = get_seo_tags('Order History', 'View all your SMM orders', '');

$user = current_user();
$userId = $user['id'] ?? 0;

$statusFilter = sanitize($_GET['status'] ?? '');
$searchFilter = sanitize($_GET['search'] ?? '');

$query = "SELECT o.*, s.name AS service_name
          FROM orders o
          LEFT JOIN services s ON s.id = o.service_id
          WHERE o.user_id = :uid";
$params = ['uid' => $userId];

if ($statusFilter) {
    $query .= " AND o.status = :status";
    $params['status'] = $statusFilter;
}
if ($searchFilter) {
    $query .= " AND s.name LIKE :search";
    $params['search'] = "%{$searchFilter}%";
}
$query .= " ORDER BY o.id DESC LIMIT 100";

$orders = db_fetch_all($query, $params);
?>

<section class="orders-page">
    <div class="page-header">
        <h1>Order History</h1>
        <p>View and manage all your orders</p>
    </div>

    <!-- Filters -->
    <form method="get" class="glass-card mb-lg d-flex gap-md" style="flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Search by service..." value="<?php echo e($searchFilter); ?>" class="form-control" style="flex: 1; min-width: 200px;">
        <select name="status" class="form-control" style="width: auto; min-width: 150px;">
            <option value="">All Status</option>
            <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="canceled" <?php echo $statusFilter === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <?php
        $metrics = [];
        try {
            if (!empty($orders)) {
                $metrics = get_order_metrics_for_user_orders($orders);
            }
        } catch (Throwable $e) {}
    ?>
    <div class="table-responsive">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Link</th>
                    <th>Charge</th>
                    <th>Start count</th>
                    <th>Quantity</th>
                    <th>Service</th>
                    <th>Average time</th>
                    <th>Status</th>
                    <th>Remains</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php $mid = (int)$order['id']; $m = $metrics[$mid] ?? []; ?>
                        <tr>
                            <td>#<?php echo e($order['id']); ?></td>
                            <td><?php echo format_date($order['created_at'] ?? 'now', 'M d, Y'); ?></td>
                            <td><a href="<?php echo e($order['link']); ?>" target="_blank" class="text-primary"><?php echo e(substr($order['link'], 0, 30) . (strlen($order['link']) > 30 ? '...' : '')); ?></a></td>
                            <td><?php echo format_currency(isset($m['charge']) ? (float)$m['charge'] : (float)$order['amount']); ?></td>
                            <td><?php echo e($m['start_count'] ?? '0'); ?></td>
                            <td><?php echo number_format($order['quantity']); ?></td>
                            <td><?php echo e($order['service_name'] ?? 'Service'); ?></td>
                            <td><?php echo e($m['avg_time'] ?? '-'); ?></td>
                            <td>
                                <?php $status = strtolower($m['status'] ?? $order['status']); $statusText = $m['status_text'] ?? ucfirst($status); ?>
                                <span class="badge badge-<?php echo $status === 'completed' ? 'completed' : ($status === 'processing' ? 'processing' : ($status === 'canceled' ? 'danger' : 'pending')); ?>">
                                    <?php echo e($statusText); ?>
                                </span>
                            </td>
                            <td><?php echo e($m['remains'] ?? '0'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                        <td colspan="10" style="text-align:center; padding: 2rem;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                                <i class="fas fa-box-open" style="font-size: 3rem; color: var(--text-tertiary);"></i>
                                <p>No orders found matching your criteria.</p>
                                <a href="<?php echo url('dashboard/new-order'); ?>" class="btn btn-primary">Place New Order</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
