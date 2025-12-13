<?php
$users = db_fetch_all("SELECT id, username, email, wallet_balance, status, created_at FROM users ORDER BY created_at DESC LIMIT 50");
$csrf = admin_csrf_token();
?>

<section class="page">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:#fff; flex-wrap:wrap; gap:12px;">
        <h1>Users</h1>
        <button class="btn btn-primary" type="button" onclick="document.getElementById('addUserForm').classList.toggle('hidden');"><i class="fas fa-user-plus"></i> Add User</button>
    </div>

    <div id="addUserForm" class="glass hidden" style="padding:12px; color:#fff;">
        <form method="POST" action="" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="add_user">
            <input class="form-control" name="name" type="text" placeholder="Full name" style="width:200px;" required>
            <input class="form-control" name="username" type="text" placeholder="Username" style="width:160px;" required>
            <input class="form-control" name="email" type="email" placeholder="Email" style="width:200px;" required>
            <input class="form-control" name="password" type="text" placeholder="Password (default: password123)" style="width:200px;">
            <select class="form-control" name="status" style="width:140px;">
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-user-plus"></i> Save</button>
        </form>
    </div>

    <div class="glass" style="color:#fff;">
        <table class="table" style="color:#fff;">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Wallet</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="5" style="text-align:center; color:#e5e7eb;">No users yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo e($u['username']); ?></td>
                            <td><?php echo e($u['email']); ?></td>
                            <td><?php echo format_currency($u['wallet_balance']); ?></td>
                            <td><span class="badge <?php echo $u['status'] === 'active' ? 'badge-success' : 'badge-danger'; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                            <td><?php echo date('d M Y H:i', strtotime($u['created_at'])); ?></td>
                            <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a class="btn btn-outline" style="padding:6px 10px;" href="<?php echo url('admin/user-edit?id=' . (int)$u['id']); ?>">Edit</a>
                                <a class="btn btn-outline" style="padding:6px 10px;" href="<?php echo url('admin/impersonate?id=' . (int)$u['id']); ?>">Login as</a>
                                <?php if ($u['id'] != 1): ?>
                                <form method="POST" action="" onsubmit="return confirm('Delete this user?');" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                                    <button class="btn btn-outline" type="submit" style="padding:6px 10px; color:#f87171; border-color:rgba(248,113,113,0.4);">Delete</button>
                                </form>
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
document.addEventListener('DOMContentLoaded', function(){
    // Keep hidden class working if absent
    if (!document.getElementById('addUserForm').classList.contains('hidden')) {
        document.getElementById('addUserForm').classList.add('hidden');
    }
});
</script>
