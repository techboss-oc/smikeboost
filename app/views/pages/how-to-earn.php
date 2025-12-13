<div class="page-header">
    <div class="container">
        <h1 class="text-gradient">How to Earn Money</h1>
        <p class="lead text-muted">Join our affiliate program and start earning today!</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid grid-2">
            <div class="glass-card">
                <div class="service-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Refer Friends</h3>
                <p class="text-muted">Share your unique referral link with friends, family, and your audience. The more you share, the more you can earn.</p>
            </div>
            <div class="glass-card">
                <div class="service-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>Earn Commission</h3>
                <p class="text-muted">Get paid for every deposit your referrals make. We offer competitive commission rates to help you maximize your earnings.</p>
            </div>
        </div>

        <div class="glass-card mt-5" style="margin-top: 3rem;">
            <div class="grid grid-2" style="align-items: center;">
                <div>
                    <h2 class="mb-3">Affiliate Program Details</h2>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--color-success); margin-right: 0.5rem;"></i> <strong>5% Commission</strong> on all referral deposits</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--color-success); margin-right: 0.5rem;"></i> Minimum payout: <strong>$10.00</strong></li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--color-success); margin-right: 0.5rem;"></i> Instant transfer to your panel balance</li>
                        <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: var(--color-success); margin-right: 0.5rem;"></i> 24/7 Affiliate Support</li>
                    </ul>
                    <?php if (is_logged_in()): ?>
                        <a href="<?php echo url('dashboard/affiliates'); ?>" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-link"></i> Get Your Link
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('register'); ?>" class="btn btn-primary" style="margin-top: 1rem;">
                            <i class="fas fa-user-plus"></i> Join Now
                        </a>
                    <?php endif; ?>
                </div>
                <div style="text-center: center;">
                    <!-- Placeholder for illustration -->
                    <div style="background: rgba(255,255,255,0.05); border-radius: 20px; padding: 2rem; text-align: center;">
                        <i class="fas fa-hand-holding-usd" style="font-size: 5rem; color: var(--color-primary);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
