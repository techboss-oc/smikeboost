<?php
/**
 * Dashboard Sidebar Component
 */
?>
<aside class="dashboard-sidebar" id="dashboardSidebar">
    <div class="sidebar-menu">
        <div class="sidebar-item <?php echo is_active('dashboard') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard'); ?>" class="sidebar-link">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('new-order') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/new-order'); ?>" class="sidebar-link">
                <i class="fas fa-plus-circle"></i>
                <span>New Order</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('orders') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/orders'); ?>" class="sidebar-link">
                <i class="fas fa-list"></i>
                <span>Order History</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('mass-order') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/mass-order'); ?>" class="sidebar-link">
                <i class="fas fa-layer-group"></i>
                <span>Mass Order</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('add-funds') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/add-funds'); ?>" class="sidebar-link">
                <i class="fas fa-wallet"></i>
                <span>Add Funds</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('child-panel') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/child-panel'); ?>" class="sidebar-link">
                <i class="fas fa-store"></i>
                <span>Child Panel</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('referrals') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/referrals'); ?>" class="sidebar-link">
                <i class="fas fa-users"></i>
                <span>Referrals</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('profile') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/profile'); ?>" class="sidebar-link">
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
        </div>
        <div class="sidebar-item <?php echo is_active('support') ? 'active' : ''; ?>">
            <a href="<?php echo url('dashboard/support'); ?>" class="sidebar-link">
                <i class="fas fa-headset"></i>
                <span>Support</span>
            </a>
        </div>
    </div>

    <div class="sidebar-footer">
        <a href="<?php echo url('logout'); ?>" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
