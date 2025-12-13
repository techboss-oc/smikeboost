<?php
$csrf = admin_csrf_token();
?>
<section class="page" style="color:#fff;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
        <h1>Edit User</h1>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?php echo url('admin/users'); ?>" class="btn btn-outline" style="color:#fff;">Back</a>
            <a href="<?php echo url('admin/impersonate?id=' . (int)$user['id']); ?>" class="btn btn-primary">Login as this user</a>
        </div>
    </div>

    <div class="glass" style="padding:16px; color:#fff;">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div>
                    <label>Name</label>
                    <input class="form-control" type="text" name="name" value="<?php echo e($user['name']); ?>" required>
                </div>
                <div>
                    <label>Username</label>
                    <input class="form-control" type="text" name="username" value="<?php echo e($user['username']); ?>" required>
                </div>
                <div>
                    <label>Email</label>
                    <input class="form-control" type="email" name="email" value="<?php echo e($user['email']); ?>" required>
                </div>
                <div>
                    <label>Status</label>
                    <select class="form-control" name="status">
                        <option value="active" <?php echo $user['status']==='active'?'selected':''; ?>>Active</option>
                        <option value="suspended" <?php echo $user['status']==='suspended'?'selected':''; ?>>Suspended</option>
                    </select>
                </div>
                <div>
                    <label>Role</label>
                    <select class="form-control" name="role">
                        <option value="user" <?php echo $user['role']==='user'?'selected':''; ?>>User</option>
                        <option value="admin" <?php echo $user['role']==='admin'?'selected':''; ?>>Admin</option>
                    </select>
                </div>
                <div>
                    <label>Wallet Balance</label>
                    <input class="form-control" type="number" step="0.01" name="wallet_balance" value="<?php echo number_format((float)$user['wallet_balance'],2,'.',''); ?>" required>
                </div>
                <div>
                    <label>New Password (leave blank to keep)</label>
                    <input class="form-control" type="text" name="password" placeholder="Leave blank to keep current">
                </div>
            </div>
            <div style="margin-top:16px; display:flex; gap:10px;">
                <button class="btn btn-primary" type="submit">Update User</button>
                <a href="<?php echo url('admin/impersonate?id=' . (int)$user['id']); ?>" class="btn btn-outline" style="color:#fff;">Login as user</a>
            </div>
        </form>
    </div>
</section>
