<?php
/**
 * Dashboard Navigation Bar Component
 */
$user = current_user();
$userId = $user['id'] ?? 0;

// Fetch user notifications
$notifications = [];
$unreadCount = 0;
try {
    $notifications = db_fetch_all(
        "SELECT * FROM notifications WHERE (user_id = :user_id OR user_id IS NULL) AND type = 'user' AND is_active = 1 ORDER BY created_at DESC LIMIT 10",
        ['user_id' => $userId]
    );
    $unreadCount = count(array_filter($notifications, fn($n) => !($n['is_read'] ?? true)));
} catch (Exception $e) {
    // Notifications table may not be set up
    $notifications = [];
    $unreadCount = 0;
}
?>
<nav class="dashboard-navbar">
    <!-- Logo & Toggle -->
    <div class="d-flex align-center gap-md">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="dashboard-logo">
            <a href="<?php echo url('dashboard'); ?>" class="d-flex align-center gap-sm">
                <i class="fas fa-wand-magic-sparkles text-primary"></i>
                <span>SmikeBoost</span>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="dashboard-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search orders, services...">
    </div>

    <!-- Right Section -->
    <div class="dashboard-navbar-right">
        <!-- Notifications -->
        <div class="notification-icon">
            <i class="fas fa-bell"></i>
            <?php if ($unreadCount > 0): ?>
                <span class="notification-badge"><?php echo $unreadCount; ?></span>
            <?php endif; ?>
            <div class="notification-dropdown">
                <?php if (empty($notifications)): ?>
                    <div class="dropdown-item">
                        <p style="margin:0; font-size:0.9rem; color: var(--text-secondary);">No new notifications</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notif): ?>
                        <div class="dropdown-item <?php echo (($notif['is_read'] ?? true) ? '' : 'unread'); ?>">
                            <i class="fas fa-<?php echo $notif['icon'] ?? 'info-circle'; ?> text-<?php echo $notif['color'] ?? 'primary'; ?>"></i>
                            <div>
                                <p style="margin:0; font-size:0.9rem;"><?php echo e($notif['message']); ?></p>
                                <small class="text-tertiary" style="font-size:0.75rem;"><?php echo time_ago($notif['created_at']); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- User Profile -->
        <div class="profile-menu">
            <img src="<?php echo asset('images/avatar.jpg'); ?>" alt="Profile" class="profile-avatar">
            <div class="profile-dropdown">
                <a href="<?php echo url('dashboard/profile'); ?>" class="dropdown-item">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="<?php echo url('logout'); ?>" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</nav>
