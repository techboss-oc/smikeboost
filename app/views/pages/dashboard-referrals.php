<?php
/**
 * Dashboard Referrals Page
 */
$seo = get_seo_tags('Referrals', 'Earn money by referring friends', '');
$user = current_user();
$userId = $user['id'];

// Stats
$referralCount = db_fetch("SELECT COUNT(*) as count FROM users WHERE referrer_id = :uid", ['uid' => $userId])['count'] ?? 0;
$totalEarnings = $user['affiliate_balance'] ?? 0;
$commissionRate = get_setting('referral_commission_rate', 5);
$minPayout = get_setting('referral_min_payout', 5000);

// Recent Referrals
$referrals = db_fetch_all("SELECT username, created_at FROM users WHERE referrer_id = :uid ORDER BY id DESC LIMIT 10", ['uid' => $userId]);

// Referral Link
$referralLink = SITE_URL . '/register?ref=' . $user['username'];
?>

<section class="referrals-page">
    <div class="page-header">
        <h1>Referrals</h1>
        <p>Invite friends and earn <?php echo $commissionRate; ?>% commission on their deposits!</p>
    </div>

    <div class="grid-2 mb-xl">
        <!-- Stats -->
        <div class="glass-card">
            <h3 class="mb-lg">Your Stats</h3>
            <div class="d-flex justify-between align-center mb-md">
                <span class="text-secondary">Total Referrals</span>
                <span class="text-primary" style="font-size: 1.5rem; font-weight: 700;"><?php echo number_format($referralCount); ?></span>
            </div>
            <div class="d-flex justify-between align-center mb-md">
                <span class="text-secondary">Available Earnings</span>
                <span class="text-success" style="font-size: 1.5rem; font-weight: 700;"><?php echo format_currency($totalEarnings); ?></span>
            </div>
            <div class="d-flex justify-between align-center">
                <span class="text-secondary">Commission Rate</span>
                <span class="text-info" style="font-weight: 600;"><?php echo $commissionRate; ?>%</span>
            </div>
        </div>

        <!-- Link -->
        <div class="glass-card">
            <h3 class="mb-lg">Your Referral Link</h3>
            <div class="form-group">
                <label>Share this link</label>
                <div class="d-flex gap-sm">
                    <input type="text" class="form-control" value="<?php echo $referralLink; ?>" readonly id="refLink">
                    <button class="btn btn-primary" onclick="copyToClipboard('<?php echo $referralLink; ?>')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <p class="text-tertiary" style="font-size: 0.9rem;">
                Minimum payout: <?php echo format_currency($minPayout); ?>. 
                Earnings are automatically credited to your wallet when your referrals deposit funds.
            </p>
        </div>
    </div>

    <!-- Recent Referrals Table -->
    <div class="glass-card">
        <h3 class="mb-lg">Recent Referrals</h3>
        <div class="table-responsive">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Date Joined</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($referrals): ?>
                        <?php foreach ($referrals as $ref): ?>
                            <tr>
                                <td><?php echo e($ref['username']); ?></td>
                                <td><?php echo format_date($ref['created_at']); ?></td>
                                <td><span class="badge badge-completed">Active</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center" style="padding: 2rem;">No referrals yet. Start sharing!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
