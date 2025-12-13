<?php

/**
 * Dashboard Home Page
 */
require_once APP_PATH . '/models/Order.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Service.php';

$seo = get_seo_tags('Dashboard', 'Your SMM Panel Dashboard', '');

$user = current_user();
$userId = $user['id'] ?? 0;

$totals = db_fetch(
    "SELECT 
        COUNT(*) AS total,
        SUM(status='completed') AS completed,
        SUM(status='processing') AS processing,
        SUM(status='pending') AS pending
     FROM orders WHERE user_id = :uid",
    ['uid' => $userId]
) ?? ['total' => 0, 'completed' => 0, 'processing' => 0, 'pending' => 0];

$balanceRow = db_fetch("SELECT wallet_balance FROM users WHERE id = :uid", ['uid' => $userId]);
$walletBalance = $balanceRow['wallet_balance'] ?? 0;

$services = db_fetch_all("SELECT * FROM services WHERE status = 'enabled' ORDER BY platform, category, name");

$recentOrders = db_fetch_all(
    "SELECT o.*, s.name AS service_name
     FROM orders o
     LEFT JOIN services s ON s.id = o.service_id
     WHERE o.user_id = :uid
     ORDER BY o.id DESC
     LIMIT 5",
    ['uid' => $userId]
);

$announcements = [];
try {
    $announcements = db_fetch_all("SELECT title, content, type FROM announcements WHERE is_active = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY created_at DESC LIMIT 4");
} catch (Exception $e) {
    $announcements = [];
}

$error = flash('error');
$success = flash('success');

$strowalletEnabled = get_setting('strowallet_enabled', '0') === '1';
$strowalletDetails = $strowalletEnabled ? strowallet_ensure_virtual_account($userId, $user['name'] ?? '', $user['email'] ?? '') : null;
// Provider metrics for recent orders
$recentMetrics = [];
try {
    if (!empty($recentOrders) && function_exists('get_order_metrics_for_user_orders')) {
        $recentMetrics = get_order_metrics_for_user_orders($recentOrders);
    }
} catch (Throwable $e) {
}

// Precompute average time map for services for selection UI
$avgMap = [];
try {
    if (!empty($services) && function_exists('get_avg_time_map_for_services')) {
        $avgMap = get_avg_time_map_for_services($services);
    }
} catch (Throwable $e) {
}
?>

<section class="dashboard-home">
    <div class="dashboard-header d-flex justify-between align-center mb-xl" style="flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="margin-bottom: 0.5rem;">Welcome, <?php echo e($user['name'] ?? 'User'); ?>!</h1>
            <p class="text-secondary">Create a new order and monitor your key stats.</p>
        </div>
        <a href="<?php echo url('dashboard/add-funds'); ?>" class="btn btn-primary">
            <i class="fas fa-coins"></i> Add Funds
        </a>
    </div>

    <?php if ($strowalletEnabled): ?>
        <div class="glass-card mb-lg" style="background: rgba(16,185,129,0.08); border: 1px solid rgba(16,185,129,0.2);">
            <div class="d-flex align-center justify-between" style="gap:12px; flex-wrap: wrap;">
                <div class="d-flex align-center gap-md">
                    <i class="fas fa-building text-success" style="font-size: 1.25rem;"></i>
                    <h3 style="margin:0; font-size:1rem;">Your Deposit Account (Strowallet)</h3>
                </div>
                <a href="<?php echo url('dashboard/add-funds'); ?>" class="btn btn-outline"><i class="fas fa-arrow-right"></i> Deposit</a>
            </div>
            <div class="d-flex" style="margin-top:10px; gap: 16px; flex-wrap: wrap;">
                <?php if ($strowalletDetails && !empty($strowalletDetails['strowallet_account_number'])): ?>
                    <div style="min-width:220px;">
                        <div class="text-secondary" style="font-size:0.85rem;">Bank</div>
                        <div style="font-weight:600; color:#fff;"><?php echo e($strowalletDetails['strowallet_bank_name'] ?? ''); ?></div>
                    </div>
                    <div style="min-width:220px;">
                        <div class="text-secondary" style="font-size:0.85rem;">Account Name</div>
                        <div style="font-weight:600; color:#fff;"><?php echo e($strowalletDetails['strowallet_account_name'] ?? ($user['name'] ?? '')); ?></div>
                    </div>
                    <div style="min-width:220px;">
                        <div class="text-secondary" style="font-size:0.85rem;">Account Number</div>
                        <div style="font-weight:600; color:#fff; font-family: monospace; cursor:pointer;" onclick="navigator.clipboard.writeText('<?php echo e($strowalletDetails['strowallet_account_number']); ?>')">
                            <?php echo e($strowalletDetails['strowallet_account_number']); ?> <i class="fas fa-copy" style="font-size: 0.9em;"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-secondary" style="font-size:0.9rem;">Strowallet is enabled but your account is not ready. Please check back later or contact support.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($announcements)): ?>
        <div id="dashboardAnnouncements" class="glass-card mb-lg" style="background: rgba(255,255,255,0.03);">
            <div class="d-flex align-center justify-between mb-md" style="gap:12px;">
                <div class="d-flex align-center gap-md">
                    <i class="fas fa-bullhorn text-primary"></i>
                    <h3 style="margin:0; font-size:1rem;">Announcements</h3>
                </div>
                <button id="hideAnnouncementsBtn" class="btn btn-outline" style="padding:6px 12px;">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
            <div class="d-flex" style="flex-direction: column; gap: 10px;">
                <?php foreach ($announcements as $a): ?>
                    <div class="d-flex gap-md align-start" style="border-left: 3px solid <?php
                                                                                            $t = strtolower($a['type'] ?? 'info');
                                                                                            echo $t === 'success' ? '#34d399' : ($t === 'danger' ? '#f87171' : ($t === 'warning' ? '#fbbf24' : '#60a5fa')); ?>; padding-left: 10px;">
                        <div style="font-weight:600; color:#fff;"><?php echo htmlspecialchars($a['title'] ?? ''); ?></div>
                        <div class="text-secondary" style="flex:1;"><?php echo $a['content'] ?? ''; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <script>
            (function() {
                var wrap = document.getElementById('dashboardAnnouncements');
                if (!wrap) return;
                try {
                    if (localStorage.getItem('hideDashboardAnnouncements') === '1') {
                        wrap.style.display = 'none';
                    }
                } catch (e) {}
                var btn = document.getElementById('hideAnnouncementsBtn');
                if (btn) {
                    btn.addEventListener('click', function() {
                        wrap.style.display = 'none';
                        try {
                            localStorage.setItem('hideDashboardAnnouncements', '1');
                        } catch (e) {}
                    });
                }
            })();
        </script>
    <?php endif; ?>

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-content">
                <h3>Balance</h3>
                <p class="stat-value"><?php echo format_currency($walletBalance); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-content">
                <h3>Total Orders</h3>
                <p class="stat-value"><?php echo number_format($totals['total'] ?? 0); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>Completed</h3>
                <p class="stat-value"><?php echo number_format($totals['completed'] ?? 0); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <h3>In Progress</h3>
                <p class="stat-value"><?php echo number_format(($totals['processing'] ?? 0) + ($totals['pending'] ?? 0)); ?></p>
            </div>
        </div>
    </div>

    <div class="grid-2" style="grid-template-columns: 2fr 1fr;">
        <div class="order-panel">
            <?php if ($error): ?>
                <div class="alert alert-danger mb-lg" style="padding: 1rem; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md); color: var(--color-danger);"><?php echo e($error); ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success mb-lg" style="padding: 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md); color: var(--color-success);"><?php echo e($success); ?></div>
            <?php endif; ?>

            <form class="glass-card" method="POST" action="<?php echo url('dashboard/new-order'); ?>">
                <div class="grid-2">
                    <div class="form-column">
                        <h2 class="mb-lg" style="font-size: 1.25rem;">Order New Service</h2>

                        <!-- Platform selection removed -->

                        <div class="form-group">
                            <label for="service_category">Category</label>
                            <select id="service_category" name="category" class="form-control" required onchange="onCategoryChange()">
                                <option value="">Select category</option>
                                <?php
                                $categories = [];
                                foreach ($services as $svc) {
                                    $categories[$svc['category']] = true;
                                }
                                foreach (array_keys($categories) as $category): ?>
                                    <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="service_id">Service *</label>
                            <select id="service_id" name="service_id" class="form-control" required onchange="updateOrderPrice()">
                                <option value="">Select service</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="link">Link/Username *</label>
                            <input type="text" id="link" name="link" class="form-control" placeholder="Post link, username, or channel URL" required>
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity *</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" min="1" placeholder="Enter quantity" required onchange="updateOrderPrice()">
                        </div>

                        <div class="d-flex gap-md align-center" style="background: rgba(59, 130, 246, 0.1); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(59, 130, 246, 0.2);">
                            <i class="fas fa-info-circle text-info" style="font-size: 1.25rem;"></i>
                            <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">Ensure your account is public and has no restrictions.</p>
                        </div>
                    </div>

                    <div class="form-column">
                        <h2 class="mb-lg" style="font-size: 1.25rem;">Price Calculation</h2>

                        <div class="glass-card mb-lg" style="background: rgba(255, 255, 255, 0.02);">
                            <div class="d-flex justify-between mb-sm" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                                <span class="text-secondary">Service:</span>
                                <span id="service-name" class="text-primary" style="text-align: right; max-width: 60%;">-</span>
                            </div>
                            <div class="d-flex justify-between mb-sm">
                                <span class="text-secondary">Price per 1000:</span>
                                <span id="price-per-1000"><?php echo format_currency(0); ?></span>
                            </div>
                            <div class="d-flex justify-between mb-sm">
                                <span class="text-secondary">Average time:</span>
                                <span id="avg-time">-</span>
                            </div>
                            <div class="mb-sm">
                                <span class="text-secondary">Description:</span>
                                <div id="service-desc" class="text-tertiary" style="margin-top:4px; font-size:0.9rem;">-</div>
                            </div>
                            <div class="d-flex justify-between mb-lg">
                                <span class="text-secondary">Quantity:</span>
                                <span id="qty-display">0</span>
                            </div>
                            <div class="d-flex justify-between align-center" style="border-top: 1px solid var(--glass-border); padding-top: 1rem;">
                                <span style="font-weight: 600;">Total Amount:</span>
                                <span id="total-amount" class="text-primary" style="font-size: 1.5rem; font-weight: 700;"><?php echo format_currency(0); ?></span>
                            </div>
                        </div>

                        <div class="glass-card mb-lg" style="background: rgba(255, 255, 255, 0.02);">
                            <div class="d-flex justify-between align-center">
                                <span class="text-secondary">Available Balance:</span>
                                <span class="text-success" style="font-weight: 700; font-size: 1.1rem;"><?php echo format_currency($walletBalance); ?></span>
                            </div>
                        </div>

                        <div class="d-flex" style="flex-direction: column; gap: 1rem;">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">
                                <i class="fas fa-shopping-cart"></i> Place Order
                            </button>
                            <a href="<?php echo url('dashboard/orders'); ?>" class="btn btn-outline btn-block">View Orders</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="order-sidebar">
            <div class="glass-card mb-lg">
                <div class="d-flex justify-between align-center mb-lg">
                    <h3 style="margin: 0; font-size: 1.1rem;">Recent Orders</h3>
                    <a href="<?php echo url('dashboard/orders'); ?>" class="text-primary" style="font-size: 0.9rem;">View all</a>
                </div>
                <div class="d-flex" style="flex-direction: column; gap: 0.75rem;">
                    <?php if ($recentOrders): ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <?php $m = $recentMetrics[(int)$order['id']] ?? null;
                            $st = strtolower($m['status'] ?? $order['status']);
                            $amt = isset($m['charge']) ? (float)$m['charge'] : (float)$order['amount']; ?>
                            <div class="recent-order-card">
                                <div>
                                    <p class="text-secondary mb-xs" style="margin: 0 0 0.25rem 0; font-size: 0.85rem;">#<?php echo e($order['id']); ?></p>
                                    <small class="badge badge-<?php echo $st === 'completed' ? 'completed' : ($st === 'processing' ? 'processing' : ($st === 'canceled' ? 'danger' : 'pending')); ?>" style="font-size: 0.7rem; padding: 0.15rem 0.5rem;"><?php echo ucfirst($st); ?></small>
                                </div>
                                <div style="font-weight: 600; font-size: 0.9rem;"><?php echo format_currency($amt); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-tertiary" style="text-align: center; padding: 1rem;">No orders yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const serviceData = <?php echo json_encode($services); ?>;
    const avgTimeMap = <?php echo json_encode($avgMap); ?>;

    // Group services by category only
    function groupedServicesByCategory() {
        return serviceData.reduce((acc, svc) => {
            const category = svc.category;
            if (!acc[category]) acc[category] = [];
            acc[category].push(svc);
            return acc;
        }, {});
    }

    const serviceMap = groupedServicesByCategory();

    function onCategoryChange() {
        const category = document.getElementById('service_category').value;
        const serviceSelect = document.getElementById('service_id');

        serviceSelect.innerHTML = '<option value="">Select service</option>';
        serviceSelect.disabled = true;

        if (!category || !serviceMap[category]) return;

        serviceMap[category].forEach(svc => {
            const opt = document.createElement('option');
            opt.value = svc.id;
            opt.textContent = svc.name;
            opt.dataset.price = svc.rate_per_1000;
            opt.dataset.name = svc.name;
            opt.dataset.avg = avgTimeMap[svc.id] || '-';
            opt.dataset.desc = svc.description || '';
            serviceSelect.appendChild(opt);
        });

        serviceSelect.disabled = false;
    }

    function updateOrderPrice() {
        const serviceSelect = document.getElementById('service_id');
        const selected = serviceSelect.options[serviceSelect.selectedIndex] || {};
        const price = parseFloat(selected.dataset ? selected.dataset.price : 0) || 0;
        const name = (selected.dataset ? selected.dataset.name : '') || '-';
        const qty = parseFloat(document.getElementById('quantity').value || 0);

        document.getElementById('service-name').textContent = name;
        document.getElementById('price-per-1000').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + price.toLocaleString();
        document.getElementById('avg-time').textContent = (selected.dataset ? selected.dataset.avg : '-') || '-';
        document.getElementById('service-desc').textContent = (selected.dataset ? selected.dataset.desc : '') || '-';
        document.getElementById('qty-display').textContent = qty.toLocaleString();
        document.getElementById('total-amount').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + ((qty / 1000) * price).toLocaleString('en-US', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }
</script>
<script>
    (function() {
        var cards = document.querySelectorAll('.recent-order-card');
        // Replace badge and amount using provider metrics when available
        <?php foreach ($recentOrders as $order): $mid = (int)$order['id'];
            $m = $recentMetrics[$mid] ?? null;
            if ($m): ?>
                    (function() {
                        var card = document.querySelector('.recent-order-card:nth-child(<?php echo array_search($order, $recentOrders, true) + 1; ?>)');
                        if (!card) return;
                        var badge = card.querySelector('.badge');
                        var amount = card.querySelector('div[style*="font-weight: 600"]');
                        if (badge) badge.textContent = (<?php echo json_encode(ucfirst(strtolower($m['status']))); ?>);
                        if (badge) {
                            var s = <?php echo json_encode(strtolower($m['status'])); ?>;
                            badge.className = 'badge badge-' + (s === 'completed' ? 'completed' : (s === 'processing' ? 'processing' : (s === 'canceled' ? 'danger' : 'pending')));
                        }
                        if (amount) amount.textContent = '<?php echo CURRENCY_SYMBOL; ?>' + (<?php echo json_encode(number_format((float)$m['charge'], 2)); ?>);
                    })();
        <?php endif;
        endforeach; ?>
    })();
</script>