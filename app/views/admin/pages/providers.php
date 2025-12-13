<?php
$hasBalance = false;
try {
    $col = db_fetch("SHOW COLUMNS FROM providers LIKE 'balance'");
    $hasBalance = (bool)$col;
} catch (Exception $e) {
    $hasBalance = false;
}
$balanceSelect = $hasBalance ? ', COALESCE(balance,0) AS balance' : '';
$providers = db_fetch_all("SELECT id, name, api_url, auto_sync, created_at{$balanceSelect} FROM providers ORDER BY id DESC LIMIT 100");
$csrf = admin_csrf_token();
?>

<section class="page">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:#fff; flex-wrap:wrap; gap:12px;">
        <h1>Providers</h1>
        <button class="btn btn-primary" type="button" onclick="openProviderModal();"><i class="fas fa-plus"></i> Add Provider</button>
    </div>

    <div id="addProviderModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1000;">
        <div class="glass" style="padding:18px; color:#fff; width: min(520px, 92vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <h3 style="margin:0;">Add Provider</h3>
                <button type="button" class="btn btn-sm btn-secondary" onclick="closeProviderModal();">Close</button>
            </div>
            <form method="POST" action="" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="action" value="add_provider">
                <input class="form-control" name="name" type="text" placeholder="Name" style="width:180px;" required>
                <input class="form-control" name="api_url" type="text" placeholder="API URL" style="width:220px;" required>
                <input class="form-control" name="api_key" type="text" placeholder="API Key" style="width:220px;" required>
                <label style="color:#e5e7eb; display:flex; align-items:center; gap:6px;">
                    <input type="checkbox" name="auto_sync" value="1"> Auto Sync
                </label>
                <div style="display:flex; gap:8px;">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
                    <button class="btn btn-secondary" type="button" onclick="closeProviderModal();">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="glass" style="color:#fff;">
        <table class="table" style="color:#fff;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>API URL</th>
                    <th>Auto Sync</th>
                    <?php if ($hasBalance): ?><th>Balance</th><?php endif; ?>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($providers)): ?>
                    <tr><td colspan="<?php echo $hasBalance?6:5; ?>" style="text-align:center; color:#e5e7eb;">No providers yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($providers as $p): ?>
                        <tr>
                            <td><?php echo e($p['name']); ?></td>
                            <td><?php echo e($p['api_url']); ?></td>
                            <td><span class="badge <?php echo $p['auto_sync'] ? 'badge-info' : 'badge-warning'; ?>"><?php echo $p['auto_sync'] ? 'Enabled' : 'Disabled'; ?></span></td>
                            <?php if ($hasBalance): ?>
                                <?php $balDisplay = isset($p['balance']) ? format_currency((float)$p['balance']) : '—'; ?>
                                <td><?php echo $balDisplay; ?></td>
                            <?php endif; ?>
                            <td><?php echo date('d M Y H:i', strtotime($p['created_at'])); ?></td>
                            <td>
                                <form method="POST" action="" style="display:inline-block; margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                                    <input type="hidden" name="action" value="sync_provider_balance">
                                    <input type="hidden" name="provider_id" value="<?php echo (int)$p['id']; ?>">
                                    <button class="btn btn-sm btn-secondary" type="submit">Sync balance</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
function openProviderModal() {
    var modal = document.getElementById('addProviderModal');
    modal.style.display = 'flex';
    modal.classList.remove('hidden');
}
function closeProviderModal() {
    var modal = document.getElementById('addProviderModal');
    modal.style.display = 'none';
    modal.classList.add('hidden');
}
document.addEventListener('click', function(e){
    var modal = document.getElementById('addProviderModal');
    if (!modal.classList.contains('hidden') && e.target === modal) {
        closeProviderModal();
    }
});
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        closeProviderModal();
    }
});
</script>
