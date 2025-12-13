<?php
$csrfToken = admin_csrf_token();
$successFlash = admin_flash('success');
$errorFlash = admin_flash('error');
if ($successFlash) {
    unset($_SESSION['admin_flash']['success']);
}
if ($errorFlash) {
    unset($_SESSION['admin_flash']['error']);
}
?>

<style>
* {
    color: white !important;
}
</style>

<section class="page">
    <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:16px; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;">Manual Deposit Verification</h1>
            <p style="color:#9ca3af; margin:6px 0 0 0;">Approve or reject bank transfer and crypto deposits before crediting wallets.</p>
        </div>
        <div style="text-align:right;">
            <div style="color:#cbd5e1; font-size:0.85rem;">Pending queue</div>
            <div style="font-size:1.8rem; font-weight:700; color:#fff;"><?php echo count($manualDeposits ?? []); ?></div>
        </div>
    </div>

    <?php if ($successFlash || $errorFlash): ?>
        <div class="glass" style="margin-bottom:18px; border-left:4px solid <?php echo $successFlash ? '#34d399' : '#f87171'; ?>;">
            <div style="color:#e5e7eb; font-weight:600;">
                <?php echo e($successFlash ?: $errorFlash); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="glass" style="margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <h2 class="section-title" style="margin:0;">
                <i class="fa fa-clipboard-check"></i>Pending Manual Deposits
            </h2>
            <span style="color:#cbd5e1; font-size:0.9rem;">Newest first</span>
        </div>

        <?php if (empty($manualDeposits)): ?>
            <div style="text-align:center; padding:30px 10px; color:#94a3b8;">
                <i class="fa fa-circle-check" style="font-size:32px; color:#22c55e; margin-bottom:10px;"></i>
                <p style="margin:0;">No manual deposits are awaiting review right now.</p>
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:14px;">
                <?php foreach ($manualDeposits as $tx): ?>
                    <div style="border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:16px; background:rgba(255,255,255,0.02);">
                        <div style="display:flex; flex-wrap:wrap; gap:16px; justify-content:space-between;">
                            <div style="flex:1 1 280px; min-width:220px;">
                                <div style="color:#9ca3af; font-size:0.85rem;">User</div>
                                <div style="font-weight:600; color:#fff;"><?php echo e($tx['username']); ?> <span style="color:#94a3b8; font-weight:400;">(<?php echo e($tx['email']); ?>)</span></div>
                                <div style="margin-top:6px; color:#cbd5e1;">Reference: <?php echo e($tx['reference']); ?></div>
                                <div style="margin-top:4px; color:#94a3b8; font-size:0.9rem;">Submitted <?php echo date('d M Y, H:i', strtotime($tx['created_at'] ?? 'now')); ?></div>
                            </div>
                            <div style="min-width:200px; text-align:right;">
                                <div style="color:#9ca3af; font-size:0.85rem;">Amount</div>
                                <div style="font-size:1.7rem; font-weight:700; color:#fff;">
                                    <?php echo format_currency((float)$tx['amount']); ?>
                                </div>
                                <?php
                                    $gatewayLabel = ucwords(str_replace('_', ' ', $tx['gateway']));
                                    $gatewayBadge = $tx['gateway'] === 'crypto' ? 'badge-info' : 'badge-warning';
                                ?>
                                <span class="badge <?php echo $gatewayBadge; ?>" style="margin-top:8px; display:inline-block;"><?php echo e($gatewayLabel); ?></span>
                            </div>
                        </div>

                        <div style="margin-top:14px; display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between;">
                            <div>
                                <?php if (!empty($tx['proof_image'])): ?>
                                    <a class="btn btn-outline" style="padding:8px 14px;" href="<?php echo url($tx['proof_image']); ?>" target="_blank">
                                        <i class="fa fa-image"></i> View Proof
                                    </a>
                                <?php else: ?>
                                    <span style="color:#fbbf24; font-size:0.9rem;">No proof uploaded</span>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                <form method="POST" action="<?php echo admin_url('transactions'); ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="transaction_id" value="<?php echo (int)$tx['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-outline" style="border-color:rgba(248,113,113,0.4); color:#f87171;">
                                        <i class="fa fa-times"></i> Reject
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo admin_url('transactions'); ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    <input type="hidden" name="transaction_id" value="<?php echo (int)$tx['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check"></i> Approve &amp; Credit
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="glass">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
            <h2 class="section-title" style="margin:0;">
                <i class="fa fa-clock-rotate-left"></i>Recent Manual Activity
            </h2>
            <span style="color:#cbd5e1; font-size:0.9rem;">Latest bank &amp; crypto submissions</span>
        </div>

        <?php if (empty($recentManualDeposits)): ?>
            <p style="color:#94a3b8; margin:0;">No manual transactions recorded yet.</p>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Reference</th>
                            <th>Gateway</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentManualDeposits as $tx): ?>
                            <?php
                                $gatewayLabel = ucwords(str_replace('_', ' ', $tx['gateway']));
                                $statusClass = 'badge-warning';
                                if ($tx['status'] === 'completed') {
                                    $statusClass = 'badge-success';
                                } elseif ($tx['status'] === 'failed') {
                                    $statusClass = 'badge-danger';
                                }
                            ?>
                            <tr>
                                <td style="white-space:nowrap;">
                                    <strong><?php echo e($tx['username']); ?></strong><br>
                                    <span style="color:#94a3b8; font-size:0.85rem;">ID #<?php echo (int)$tx['user_id']; ?></span>
                                </td>
                                <td><?php echo e($tx['reference']); ?></td>
                                <td><?php echo e($gatewayLabel); ?></td>
                                <td><?php echo format_currency((float)$tx['amount']); ?></td>
                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo ucwords($tx['status']); ?></span></td>
                                <td><?php echo date('d M Y, H:i', strtotime($tx['created_at'] ?? 'now')); ?></td>
                                <td>
                                    <?php if (!empty($tx['proof_image'])): ?>
                                        <a href="<?php echo url($tx['proof_image']); ?>" target="_blank" style="color:#60a5fa;">View</a>
                                    <?php else: ?>
                                        <span style="color:#6b7280;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
