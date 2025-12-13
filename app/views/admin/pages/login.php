<?php
$csrf = admin_csrf_token();
$error = admin_flash('error');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - SmikeBoost</title>
    <link rel="stylesheet" href="<?php echo asset('css/auth.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body { background: radial-gradient(circle at 20% 20%, rgba(168,85,247,0.08), transparent 25%), radial-gradient(circle at 80% 0%, rgba(236,72,153,0.08), transparent 25%), #0b0c10; }
        .card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 28px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 420px; width: 100%; }
        .input { width: 100%; padding: 12px 14px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); color: #e5e7eb; }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 12px; background: linear-gradient(135deg, #a855f7, #ec4899); color: #fff; font-weight: 700; cursor: pointer; }
        .error { background: rgba(248,113,113,0.12); color: #fca5a5; padding: 12px; border-radius: 10px; margin-bottom: 12px; border: 1px solid rgba(248,113,113,0.25); }
        label { color: #cbd5e1; font-size: 0.95rem; margin-bottom: 6px; display:block; }
    </style>
</head>
<body style="display:grid; place-items:center; min-height:100vh; padding:16px;">
    <div class="card">
        <div style="text-align:center; margin-bottom:16px;">
            <div style="font-weight:800; font-size:1.4rem; color:#fff;">⚡ SmikeBoost Admin</div>
            <p style="color:#cbd5e1; margin:6px 0 0 0;">Secure access only</p>
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo e($error); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <div style="margin-bottom:14px;">
                <label>Username or Email</label>
                <input class="input" type="text" name="username" placeholder="admin" required />
            </div>
            <div style="margin-bottom:16px;">
                <label>Password</label>
                <input class="input" type="password" name="password" placeholder="••••••••" required />
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
    </div>
</body>
</html>
