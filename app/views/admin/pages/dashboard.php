<?php
// Pull live stats from database
$totalUsers = (int) (db_fetch('SELECT COUNT(*) AS c FROM users')['c'] ?? 0);
$totalOrders = (int) (db_fetch('SELECT COUNT(*) AS c FROM orders')['c'] ?? 0);
$pendingOrders = (int) (db_fetch("SELECT COUNT(*) AS c FROM orders WHERE status = 'pending'")['c'] ?? 0);
$completedOrders = (int) (db_fetch("SELECT COUNT(*) AS c FROM orders WHERE status = 'completed'")['c'] ?? 0);
$cancelledOrders = (int) (db_fetch("SELECT COUNT(*) AS c FROM orders WHERE status = 'canceled'")['c'] ?? 0);

$todayRevenue = (float) (db_fetch("SELECT COALESCE(SUM(amount),0) AS amt FROM orders WHERE status = 'completed' AND DATE(created_at) = CURDATE()")['amt'] ?? 0);
$walletLoadToday = (float) (db_fetch("SELECT COALESCE(SUM(amount),0) AS amt FROM transactions WHERE type = 'credit' AND status = 'completed' AND DATE(created_at) = CURDATE()")['amt'] ?? 0);

$widgets = [
    ['label' => 'Total Users', 'value' => number_format($totalUsers), 'icon' => 'fa-users'],
    ['label' => 'Total Orders', 'value' => number_format($totalOrders), 'icon' => 'fa-list-check'],
    ['label' => 'Pending Orders', 'value' => number_format($pendingOrders), 'icon' => 'fa-clock'],
    ['label' => 'Completed Orders', 'value' => number_format($completedOrders), 'icon' => 'fa-circle-check'],
    ['label' => 'Cancelled Orders', 'value' => number_format($cancelledOrders), 'icon' => 'fa-times-circle'],
    ['label' => 'Today Revenue', 'value' => format_currency($todayRevenue), 'icon' => 'fa-sack-dollar'],
    ['label' => 'Wallet Load Today', 'value' => format_currency($walletLoadToday), 'icon' => 'fa-wallet'],
];
?>

<section class="page">
    <h1 style="margin-bottom:16px; color:#fff;">Dashboard</h1>
    <div class="card-grid">
        <?php foreach ($widgets as $w): ?>
            <div class="glass" style="display:flex; align-items:center; justify-content:space-between; color:#fff;">
                <div>
                    <p style="margin:0; color:#e5e7eb; font-size:0.9rem;"><?php echo e($w['label']); ?></p>
                    <h2 style="margin:6px 0 0 0; color:#fff; font-size:1.4rem;"><?php echo e($w['value']); ?></h2>
                </div>
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(168,85,247,0.18); display:grid; place-items:center; color:#fff;">
                    <i class="fas <?php echo e($w['icon']); ?>"></i>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:20px; display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
        <div class="glass" style="min-height:260px;">
            <div class="section-title"><i class="fas fa-chart-line"></i> User Growth</div>
            <p style="color:#9ca3af;">Chart placeholder</p>
        </div>
        <div class="glass" style="min-height:260px;">
            <div class="section-title"><i class="fas fa-chart-column"></i> Orders Statistics</div>
            <p style="color:#9ca3af;">Chart placeholder</p>
        </div>
        <div class="glass" style="min-height:260px;">
            <div class="section-title"><i class="fas fa-chart-bar"></i> Revenue</div>
            <p style="color:#9ca3af;">Chart placeholder</p>
        </div>
    </div>
</section>
