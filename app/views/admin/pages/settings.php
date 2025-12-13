<?php $csrf = admin_csrf_token(); ?>
<section class="page">
    <h1 style="margin-bottom:16px;">Settings</h1>

    <form method="POST" action="<?php echo admin_url('settings'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
        <!-- Payment Settings Link -->
        <div class="glass" style="margin-bottom:16px; background: linear-gradient(135deg, rgba(139,92,246,0.1), rgba(59,130,246,0.1));">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                <div>
                    <h3 style="margin:0 0 4px 0; color:#e5e7eb;">
                        <i class="fa fa-credit-card" style="margin-right:8px; color:#8b5cf6;"></i>Payment Settings
                    </h3>
                    <p style="color:#9ca3af; margin:0;">Configure payment gateways, bank transfers, and cryptocurrency options.</p>
                </div>
                <a href="<?php echo url('admin/payments'); ?>" class="btn btn-primary">
                    <i class="fa fa-cog" style="margin-right:6px;"></i>Manage Payments
                </a>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">Features & Pricing</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div>
                    <label>Child Panel Price (Monthly)</label>
                    <input name="child_panel_price" class="form-control" type="number" value="<?php echo e(get_setting('child_panel_price', 25000)); ?>" />
                </div>
                <div>
                    <label>Referral Commission (%)</label>
                    <input name="referral_commission_rate" class="form-control" type="number" step="0.1" value="<?php echo e(get_setting('referral_commission_rate', 5)); ?>" />
                </div>
                <div>
                    <label>Min Referral Payout</label>
                    <input name="referral_min_payout" class="form-control" type="number" value="<?php echo e(get_setting('referral_min_payout', 5000)); ?>" />
                </div>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">Contact Details</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div>
                    <label>Support Email</label>
                    <input name="contact_email" class="form-control" type="email" value="<?php echo e(get_setting('contact_email', 'support@smikeboost.com')); ?>" />
                </div>
                <div>
                    <label>Phone Number</label>
                    <input name="contact_phone" class="form-control" type="text" value="<?php echo e(get_setting('contact_phone')); ?>" />
                </div>
                <div>
                    <label>WhatsApp Number</label>
                    <input name="contact_whatsapp" class="form-control" type="text" value="<?php echo e(get_setting('contact_whatsapp')); ?>" />
                </div>
                <div>
                    <label>Telegram Username</label>
                    <input name="contact_telegram" class="form-control" type="text" value="<?php echo e(get_setting('contact_telegram')); ?>" />
                </div>
                <div style="grid-column: 1 / -1;">
                    <label>Address</label>
                    <input name="contact_address" class="form-control" type="text" value="<?php echo e(get_setting('contact_address')); ?>" />
                </div>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">Floating Widgets</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div>
                    <label style="display:flex; align-items:center; gap:8px; margin-bottom: 8px;">
                        <input type="checkbox" name="enable_whatsapp" value="1" <?php echo get_setting('enable_whatsapp') ? 'checked' : ''; ?>>
                        Enable WhatsApp Widget
                    </label>
                    <label>WhatsApp Number (International Format)</label>
                    <input name="whatsapp_number" class="form-control" type="text" value="<?php echo e(get_setting('whatsapp_number')); ?>" placeholder="2348000000000" />
                </div>
                <div>
                    <label style="display:flex; align-items:center; gap:8px; margin-bottom: 8px;">
                        <input type="checkbox" name="enable_tawk" value="1" <?php echo get_setting('enable_tawk') ? 'checked' : ''; ?>>
                        Enable Tawk.to Widget
                    </label>
                    <label>Tawk.to Property ID</label>
                    <input name="tawk_to_id" class="form-control" type="text" value="<?php echo e(get_setting('tawk_to_id')); ?>" placeholder="xxxxxxxxxxxxxxxx" />
                </div>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">News Ticker</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div style="grid-column: 1 / -1;">
                    <label style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="enable_ticker" value="1" <?php echo get_setting('enable_ticker', '1') === '1' ? 'checked' : ''; ?>>
                        Enable Updates Ticker at top of pages
                    </label>
                </div>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">Google Authentication</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div style="grid-column: 1 / -1;">
                    <label style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="google_auth_enabled" value="1" <?php echo get_setting('google_auth_enabled') ? 'checked' : ''; ?>>
                        Enable Login with Gmail
                    </label>
                </div>
                <div>
                    <label>Google Client ID</label>
                    <input name="google_client_id" class="form-control" type="text" value="<?php echo e(get_setting('google_client_id')); ?>" placeholder="xxxxxxxx.apps.googleusercontent.com" />
                </div>
                <div>
                    <label>Google Client Secret</label>
                    <input name="google_client_secret" class="form-control" type="password" value="<?php echo e(get_setting('google_client_secret')); ?>" placeholder="xxxxxxxx" />
                </div>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">SMTP Email Settings</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div>
                    <label>SMTP Host</label>
                    <input name="smtp_host" class="form-control" type="text" value="<?php echo e(get_setting('smtp_host', 'smtp.gmail.com')); ?>" />
                </div>
                <div>
                    <label>SMTP Port</label>
                    <input name="smtp_port" class="form-control" type="number" value="<?php echo e(get_setting('smtp_port', 587)); ?>" />
                </div>
                <div>
                    <label>SMTP Username</label>
                    <input name="smtp_username" class="form-control" type="text" value="<?php echo e(get_setting('smtp_username')); ?>" />
                </div>
                <div>
                    <label>SMTP Password</label>
                    <input name="smtp_password" class="form-control" type="password" value="<?php echo e(get_setting('smtp_password')); ?>" />
                </div>
                <div>
                    <label>From Email</label>
                    <input name="smtp_from_email" class="form-control" type="email" value="<?php echo e(get_setting('smtp_from_email', 'noreply@smikeboost.com')); ?>" />
                </div>
                <div>
                    <label>From Name</label>
                    <input name="smtp_from_name" class="form-control" type="text" value="<?php echo e(get_setting('smtp_from_name', 'SmikeBoost')); ?>" />
                </div>
                <div style="grid-column: 1 / -1;">
                    <label style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="smtp_secure" value="1" <?php echo get_setting('smtp_secure') ? 'checked' : ''; ?>>
                        Use TLS/SSL Encryption
                    </label>
                </div>
            </div>
        </div>

        <div class="glass" style="margin-bottom:16px;">
            <h3 style="margin:0 0 12px 0; color:#e5e7eb;">General</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap:12px;">
                <div>
                    <label>Site Name</label>
                    <input name="site_name" class="form-control" type="text" value="<?php echo e(get_setting('site_name', 'SmikeBoost')); ?>" />
                </div>
                <!-- ... other fields ... -->
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</section>
