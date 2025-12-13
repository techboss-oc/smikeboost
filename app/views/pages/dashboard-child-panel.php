<?php
/**
 * Dashboard Child Panel Page
 */
$seo = get_seo_tags('Child Panel', 'Start your own SMM business', '');
$price = get_setting('child_panel_price', 25000);
$currency = get_setting('child_panel_currency', 'NGN');

$user = current_user();
$requests = db_fetch_all("SELECT * FROM child_panels WHERE user_id = :uid ORDER BY id DESC", ['uid' => $user['id']]);
?>

<section class="child-panel-page">
    <div class="page-header">
        <h1>Child Panel</h1>
        <p>Launch your own SMM Panel for just <?php echo format_currency($price); ?>/month</p>
    </div>

    <div class="grid-2 mb-xl" style="grid-template-columns: 1fr 1fr;">
        <!-- Order Form -->
        <div class="glass-card">
            <h3 class="mb-lg">Order New Panel</h3>
            
            <form method="POST" action="<?php echo url('dashboard/child-panel'); ?>">
                <div class="form-group">
                    <label>Domain Name</label>
                    <input type="text" name="domain" class="form-control" placeholder="example.com" required>
                    <small class="text-tertiary">Please point your domain's nameservers to our NS1 and NS2 first.</small>
                </div>

                <div class="form-group">
                    <label>Admin Username</label>
                    <input type="text" name="admin_username" class="form-control" placeholder="admin" required>
                </div>

                <div class="form-group">
                    <label>Admin Password</label>
                    <input type="password" name="admin_password" class="form-control" placeholder="Secure password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <div class="d-flex justify-between align-center mb-lg p-md" style="background: rgba(255,255,255,0.05); border-radius: var(--radius-md);">
                    <span>Price per month:</span>
                    <span class="text-primary" style="font-size: 1.25rem; font-weight: 700;"><?php echo format_currency($price); ?></span>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-rocket"></i> Submit Order
                </button>
            </form>
        </div>

        <!-- Info / History -->
        <div>
            <div class="glass-card mb-lg">
                <h3 class="mb-md">How it works</h3>
                <ul style="list-style: disc; padding-left: 1.5rem; color: var(--text-secondary); line-height: 1.6;">
                    <li>Buy a domain name from any registrar (Namecheap, GoDaddy, etc).</li>
                    <li>Update nameservers to <strong>ns1.smikeboost.com</strong> and <strong>ns2.smikeboost.com</strong>.</li>
                    <li>Fill the form on the left.</li>
                    <li>We will install your panel within 24 hours.</li>
                    <li>You get a fully functional panel connected to our API.</li>
                </ul>
            </div>

            <div class="glass-card">
                <h3 class="mb-md">Your Panels</h3>
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($requests): ?>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><?php echo e($req['domain']); ?></td>
                                        <td>
                                            <?php 
                                            $status = $req['status'];
                                            $badge = $status === 'active' ? 'completed' : ($status === 'pending' ? 'pending' : 'canceled');
                                            ?>
                                            <span class="badge badge-<?php echo $badge; ?>"><?php echo ucfirst($status); ?></span>
                                        </td>
                                        <td><?php echo format_date($req['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No panels yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
