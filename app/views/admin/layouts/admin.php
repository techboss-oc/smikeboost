<?php
require_once APP_PATH . '/helpers/admin_helpers.php';
$admin = admin_current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - SmikeBoost</title>
    <link rel="stylesheet" href="<?php echo asset('css/dashboard.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        /* Reset box-sizing for admin */
        *, *::before, *::after { box-sizing: border-box; }
        body { background: radial-gradient(circle at 20% 20%, rgba(168,85,247,0.08), transparent 25%), radial-gradient(circle at 80% 0%, rgba(236,72,153,0.08), transparent 25%), #0b0c10; margin: 0; padding: 0; }
        .admin-shell { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        .admin-sidebar { background: rgba(255,255,255,0.04); border-right: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(12px); padding: 24px; position: sticky; top: 0; height: 100vh; }
        .admin-sidebar .logo { font-weight: 700; font-size: 1.2rem; letter-spacing: 0.02em; color: #fff; }
        .admin-menu { margin-top: 24px; display: flex; flex-direction: column; gap: 10px; }
        .admin-menu a { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 10px; color: #e5e7eb; text-decoration: none; transition: all 0.2s; border: 1px solid transparent; }
        .admin-menu a.active, .admin-menu a:hover { background: rgba(168,85,247,0.12); border-color: rgba(168,85,247,0.3); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .admin-topbar { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.02); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 5; }
        .admin-content { padding: 24px; }
        .badge { padding: 4px 8px; border-radius: 999px; font-size: 12px; }
        .badge-success { background: rgba(34,197,94,0.15); color: #34d399; }
        .badge-warning { background: rgba(251,191,36,0.15); color: #fbbf24; }
        .badge-info { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .badge-danger { background: rgba(248,113,113,0.15); color: #f87171; }
        .card-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .glass { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); overflow: visible; }
        .glass form { overflow: visible; }
        .table { width: 100%; border-collapse: collapse; table-layout: auto; }
        .table thead { background: rgba(255,255,255,0.04); }
        .table th, .table td { padding: 12px 10px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); word-wrap: break-word; }
        .table tr:hover { background: rgba(255,255,255,0.02); }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 10px; text-decoration: none; border: 1px solid transparent; transition: all 0.2s; font-weight: 600; cursor: pointer; font-size: 14px; }
        .btn-primary { background: linear-gradient(135deg, #a855f7, #ec4899); color: #fff; border: none; }
        .btn-outline { border-color: rgba(255,255,255,0.1); color: #e5e7eb; background: transparent; }
        .btn-ghost { color: #e5e7eb; background: transparent; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 30px rgba(0,0,0,0.25); }
        .form-control, select, textarea { 
            width: 100%; 
            padding: 10px 12px; 
            border-radius: 10px; 
            border: 1px solid rgba(255,255,255,0.1); 
            background: rgba(255,255,255,0.05); 
            color: #e5e7eb; 
            box-sizing: border-box;
            font-size: 14px;
            line-height: 1.5;
            min-height: 42px;
        }
        input[type="number"] { -moz-appearance: textfield; }
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .form-control:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #a855f7;
            box-shadow: 0 0 0 2px rgba(168,85,247,0.2);
        }
        .form-control::placeholder { color: #6b7280; }
        select { cursor: pointer; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }
        select option { background: #1f2937; color: #e5e7eb; padding: 8px; }
        label { display: block; margin-bottom: 6px; color: #cbd5e1; font-size: 0.875rem; font-weight: 500; }
        /* Form grid layout fix */
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .form-grid > div { min-width: 0; overflow: hidden; }
        /* Grid form fields - prevent overlap */
        .glass > div[style*="display:grid"] { gap: 16px !important; }
        .glass > div[style*="display:grid"] > div { min-width: 0; overflow: visible; margin-bottom: 0; }
        .glass input, .glass select, .glass textarea { max-width: 100%; display: block; }
        .section-title { display: flex; align-items: center; gap: 8px; margin: 0 0 10px 0; font-size: 1rem; color: #e5e7eb; }
        .page h1, .page h2, .page h3, .page h4 { color: #fff; }
        .admin-content h1, .admin-content h2 { color: #fff; }
        /* Responsive admin layout */
        @media (max-width: 900px) {
            .admin-shell { grid-template-columns: 1fr; }
            .admin-sidebar { display: none; }
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="logo">⚡ SmikeBoost Admin</div>
        <nav class="admin-menu">
            <a href="<?php echo admin_url(); ?>" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-gauge"></i> Dashboard</a>
            
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 0;">
            
            <a href="<?php echo admin_url('orders'); ?>" class="<?php echo $page === 'orders' ? 'active' : ''; ?>"><i class="fas fa-list"></i> Orders</a>
            <a href="<?php echo admin_url('users'); ?>" class="<?php echo $page === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Users</a>
            
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 0;">
            
            <a href="<?php echo admin_url('services'); ?>" class="<?php echo $page === 'services' ? 'active' : ''; ?>"><i class="fas fa-cubes"></i> Services</a>
            <a href="<?php echo admin_url('providers'); ?>" class="<?php echo $page === 'providers' ? 'active' : ''; ?>"><i class="fas fa-plug"></i> Providers</a>
            
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 0;">
            
            <a href="<?php echo admin_url('payments'); ?>" class="<?php echo $page === 'payments' ? 'active' : ''; ?>"><i class="fas fa-credit-card"></i> Payments</a>
            <a href="<?php echo admin_url('transactions'); ?>" class="<?php echo $page === 'transactions' ? 'active' : ''; ?>"><i class="fas fa-wallet"></i> Transactions</a>
            
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 0;">
            
            <a href="<?php echo admin_url('tickets'); ?>" class="<?php echo $page === 'tickets' ? 'active' : ''; ?>"><i class="fas fa-headset"></i> Tickets</a>
            <a href="<?php echo admin_url('blog'); ?>" class="<?php echo $page === 'blog' ? 'active' : ''; ?>"><i class="fas fa-blog"></i> Blog</a>
            <a href="<?php echo admin_url('notifications'); ?>" class="<?php echo $page === 'notifications' ? 'active' : ''; ?>"><i class="fas fa-bell"></i> Push Notifications</a>
            <a href="<?php echo admin_url('newsletter'); ?>" class="<?php echo $page === 'newsletter' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Newsletter</a>
            <a href="<?php echo admin_url('announcements'); ?>" class="<?php echo $page === 'announcements' ? 'active' : ''; ?>"><i class="fas fa-bullhorn"></i> Announcements</a>
            <a href="<?php echo admin_url('emails'); ?>" class="<?php echo $page === 'emails' ? 'active' : ''; ?>"><i class="fas fa-mail-bulk"></i> Auto Emails</a>
            
            <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 8px 0;">
            
            <a href="<?php echo admin_url('settings'); ?>" class="<?php echo $page === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a>
        </nav>
    </aside>
    <main>
        <header class="admin-topbar">
            <div style="display:flex; align-items:center; gap:10px;">
                <form action="#" method="get" style="display:flex; align-items:center; gap:8px; background: rgba(255,255,255,0.04); border-radius: 10px; padding: 8px 10px; border: 1px solid rgba(255,255,255,0.06);">
                    <i class="fas fa-search" style="color:#9ca3af;"></i>
                    <input type="text" name="q" placeholder="Search..." style="background:transparent; border:none; color:#e5e7eb; outline:none; min-width:180px;">
                </form>
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="btn btn-ghost" style="border:1px solid rgba(255,255,255,0.06);"><i class="fas fa-bell"></i></button>
                <div style="display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:10px; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.03);">
                    <div style="width:34px; height:34px; border-radius:50%; background: linear-gradient(135deg, #a855f7, #ec4899); display:grid; place-items:center; font-weight:700; color:#fff;">
                        <?php echo strtoupper(substr($admin['username'] ?? 'A',0,1)); ?>
                    </div>
                    <div>
                        <div style="font-weight:600; color:#e5e7eb; font-size:0.95rem;">Admin</div>
                        <div style="color:#9ca3af; font-size:0.8rem;">Superadmin</div>
                    </div>
                    <a href="<?php echo admin_url('logout'); ?>" class="btn btn-outline" style="padding:8px 10px;"><i class="fas fa-right-from-bracket"></i></a>
                </div>
            </div>
        </header>
        <div class="admin-content">
            <?php 
            $successMsg = admin_flash('success');
            $errorMsg = admin_flash('error');
            if ($successMsg): ?>
                <div style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#34d399; padding:12px 16px; border-radius:10px; margin-bottom:16px; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-check-circle"></i> <?php echo e($successMsg); ?>
                </div>
            <?php endif; ?>
            <?php if ($errorMsg): ?>
                <div style="background:rgba(248,113,113,0.15); border:1px solid rgba(248,113,113,0.3); color:#f87171; padding:12px 16px; border-radius:10px; margin-bottom:16px; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo e($errorMsg); ?>
                </div>
            <?php endif; ?>
            <?php include VIEWS_PATH . '/admin/pages/' . $page . '.php'; ?>
        </div>
    </main>
</div>
</body>
</html>
