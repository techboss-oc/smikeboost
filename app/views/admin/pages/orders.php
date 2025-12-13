<?php
$orders = db_fetch_all("SELECT o.id, u.username, s.name AS service_name, s.platform, o.quantity, o.amount, o.status, o.created_at, o.provider_order_id, o.provider_id
    FROM orders o
    JOIN users u ON u.id = o.user_id
    JOIN services s ON s.id = o.service_id
    ORDER BY o.created_at DESC
    LIMIT 50");

function admin_status_badge($status) {
    $map = [
        'pending' => 'badge-warning',
        'processing' => 'badge-info',
        'completed' => 'badge-success',
        'canceled' => 'badge-danger'
    ];
    return $map[$status] ?? 'badge-info';
}
?>

<section class="page">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; color:#fff;">
        <h1>Orders</h1>
        <div style="display:flex; gap:10px;">
            <input class="form-control" type="text" placeholder="Search user or service" style="width:220px;" />
            <button class="btn btn-warning" id="autoSyncToggle" onclick="toggleAutoSync()"><i class="fas fa-pause"></i> Pause Auto-Sync</button>
            <button class="btn btn-primary"><i class="fas fa-arrows-rotate"></i> Refresh</button>
        </div>
    </div>

    <div class="glass" style="color:#fff;">
        <table class="table" style="color:#fff;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Service</th>
                    <th>Platform</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Provider Order ID</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <?php $metrics = []; try { if (!empty($orders) && function_exists('get_order_metrics_for_user_orders')) { $metrics = get_order_metrics_for_user_orders($orders); } } catch (Throwable $e) {} ?>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="10" style="text-align:center; color:#e5e7eb;">No orders yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <?php $mid = (int)$o['id']; $m = isset($metrics[$mid]) ? $metrics[$mid] : []; $status = strtolower(isset($m['status']) ? $m['status'] : $o['status']); ?>
                        <tr data-order-id="<?php echo $o['id']; ?>">
                            <td>#<?php echo e($o['id']); ?></td>
                            <td><?php echo e($o['username']); ?></td>
                            <td><?php echo e($o['service_name']); ?></td>
                            <td><?php echo e($o['platform']); ?></td>
                            <td><?php echo number_format((int)$o['quantity']); ?></td>
                            <td><?php echo format_currency($o['amount']); ?></td>
                            <td><span class="badge status-badge <?php echo admin_status_badge($status); ?>"><?php echo ucfirst($status); ?></span></td>
                            <td><?php echo e($o['provider_order_id'] ?: '-'); ?></td>
                            <td><?php echo date('d M Y H:i', strtotime($o['created_at'])); ?></td>
                            <td>
                                <?php if (!empty($o['provider_order_id']) && in_array($status, ['pending', 'processing'])): ?>
                                    <button class="btn btn-sm btn-info" onclick="syncOrder(<?php echo $o['id']; ?>)">
                                        <i class="fas fa-sync"></i> Sync
                                    </button>
                                <?php endif; ?>
                                <?php if (in_array($status, ['pending', 'processing'])): ?>
                                    <button class="btn btn-sm btn-danger" onclick="cancelOrder(<?php echo $o['id']; ?>)">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
let autoSyncInterval = null;
let isAutoSyncEnabled = true;

function syncOrder(orderId) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Syncing...';
    button.disabled = true;

    fetch('<?php echo admin_url('orders'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ action: 'sync_order', order_id: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.new_status && data.new_status !== 'processing') {
                location.reload();
            } else {
                // Update status badge without reload
                updateOrderStatus(orderId, data.new_status || 'processing');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        } else {
            console.error('Sync error:', data.message);
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function updateOrderStatus(orderId, newStatus) {
    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
    if (row) {
        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            statusCell.className = `badge ${newStatus === 'completed' ? 'badge-success' : newStatus === 'canceled' ? 'badge-danger' : 'badge-info'}`;
        }
    }
}

function syncAllOrders() {
    if (!isAutoSyncEnabled) return;
    
    const syncButtons = document.querySelectorAll('button[onclick*="syncOrder("]');
    if (syncButtons.length === 0) return;
    
    console.log('Auto-syncing orders...');
    
    syncButtons.forEach(button => {
        const onclick = button.getAttribute('onclick');
        const match = onclick.match(/syncOrder\((\d+)\)/);
        if (match) {
            const orderId = parseInt(match[1]);
            // Only sync if not already syncing
            if (!button.disabled) {
                syncOrder(orderId);
            }
        }
    });
}

function toggleAutoSync() {
    isAutoSyncEnabled = !isAutoSyncEnabled;
    const button = document.getElementById('autoSyncToggle');
    if (button) {
        button.innerHTML = isAutoSyncEnabled ? '<i class="fas fa-pause"></i> Pause Auto-Sync' : '<i class="fas fa-play"></i> Resume Auto-Sync';
        button.className = isAutoSyncEnabled ? 'btn btn-warning' : 'btn btn-success';
    }
    
    if (isAutoSyncEnabled) {
        startAutoSync();
    } else {
        stopAutoSync();
    }
}

function startAutoSync() {
    if (autoSyncInterval) clearInterval(autoSyncInterval);
    autoSyncInterval = setInterval(syncAllOrders, 30000); // Sync every 30 seconds
    console.log('Auto-sync started');
}

function stopAutoSync() {
    if (autoSyncInterval) {
        clearInterval(autoSyncInterval);
        autoSyncInterval = null;
    }
    console.log('Auto-sync stopped');
}

// Start auto-sync when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoSync();
});

// Stop auto-sync when page unloads
window.addEventListener('beforeunload', stopAutoSync);

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this order? This will refund the user.')) {
        fetch('<?php echo admin_url('orders/cancel'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order canceled and refunded successfully.');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Network error occurred.');
        });
    }
}
</script>
