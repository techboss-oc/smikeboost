<?php $csrf = admin_csrf_token(); ?>
<section class="page">
    <h1 style="margin-bottom:16px; color:#fff;">Payment Settings</h1>

    <!-- Payment Gateway Selection & Deposit Limits -->
    <form method="POST" action="<?= admin_url('payments') ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="section" value="general">
        <div class="glass" style="margin-bottom:16px; overflow:visible;">
            <h3 style="margin:0 0 16px 0; color:#e5e7eb;">
                <i class="fa fa-credit-card" style="margin-right:8px; color:#8b5cf6;"></i>Active Payment Gateway & Limits
            </h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:16px;">
                <div style="min-width:0;">
                    <label>Select Gateway</label>
                    <select name="active_payment_gateway" class="form-control">
                        <option value="flutterwave" <?= get_setting('active_payment_gateway') == 'flutterwave' ? 'selected' : '' ?>>Flutterwave</option>
                        <option value="paystack" <?= get_setting('active_payment_gateway') == 'paystack' ? 'selected' : '' ?>>Paystack</option>
                        <option value="both" <?= get_setting('active_payment_gateway') == 'both' ? 'selected' : '' ?>>Both (User Choice)</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Minimum Deposit (₦)</label>
                    <input class="form-control" type="number" name="min_deposit" step="0.01" 
                           value="<?= htmlspecialchars(get_setting('min_deposit') ?: '100') ?>" placeholder="100" />
                </div>
                <div style="min-width:0;">
                    <label>Maximum Deposit (₦)</label>
                    <input class="form-control" type="number" name="max_deposit" step="0.01" 
                           value="<?= htmlspecialchars(get_setting('max_deposit') ?: '1000000') ?>" placeholder="1000000" />
                </div>
            </div>
            <p style="color:#9ca3af; margin-top:12px; font-size:0.875rem;">Choose which payment gateway(s) to show on the deposit page.</p>
            <button type="submit" class="btn btn-primary" style="margin-top:12px;">
                <i class="fa fa-save" style="margin-right:8px;"></i>Save General Settings
            </button>
        </div>
    </form>

    <!-- Flutterwave -->
    <form method="POST" action="<?= admin_url('payments') ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="section" value="flutterwave">
        <div class="glass" style="margin-bottom:16px; overflow:visible;">
            <h3 style="margin:0 0 16px 0; color:#e5e7eb;">
                <i class="fa fa-bolt" style="margin-right:8px; color:#8b5cf6;"></i>Flutterwave
                <span style="font-size:12px; background:<?= get_setting('flutterwave_enabled') == '1' ? '#22c55e' : '#ef4444' ?>; padding:2px 8px; border-radius:4px; margin-left:8px;">
                    <?= get_setting('flutterwave_enabled') == '1' ? 'Enabled' : 'Disabled' ?>
                </span>
            </h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:16px;">
                <div style="min-width:0;">
                    <label>Status</label>
                    <select name="flutterwave_enabled" class="form-control">
                        <option value="1" <?= get_setting('flutterwave_enabled') == '1' ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= get_setting('flutterwave_enabled') != '1' ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Environment</label>
                    <select name="flutterwave_env" class="form-control">
                        <option value="sandbox" <?= get_setting('flutterwave_env') == 'sandbox' ? 'selected' : '' ?>>Sandbox (Test)</option>
                        <option value="live" <?= get_setting('flutterwave_env') == 'live' ? 'selected' : '' ?>>Live</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Public Key</label>
                    <input class="form-control" type="text" name="flutterwave_public_key" 
                           value="<?= htmlspecialchars(get_setting('flutterwave_public_key') ?? '') ?>" placeholder="FLWPUBK-..." />
                </div>
                <div style="min-width:0;">
                    <label>Secret Key</label>
                    <input class="form-control" type="password" name="flutterwave_secret_key" 
                           value="<?= htmlspecialchars(get_setting('flutterwave_secret_key') ?? '') ?>" placeholder="FLWSECK-..." />
                </div>
                <div style="min-width:0;">
                    <label>Encryption Key</label>
                    <input class="form-control" type="password" name="flutterwave_encryption_key" 
                           value="<?= htmlspecialchars(get_setting('flutterwave_encryption_key') ?? '') ?>" placeholder="Encryption Key" />
                </div>
                <div style="min-width:0;">
                    <label>Webhook Secret Hash</label>
                    <input class="form-control" type="password" name="flutterwave_webhook_secret" 
                           value="<?= htmlspecialchars(get_setting('flutterwave_webhook_secret') ?? '') ?>" placeholder="whsec_..." />
                </div>
            </div>
            <p style="color:#9ca3af; margin-top:10px;">
                Webhook URL: <code style="background:#374151; padding:2px 6px; border-radius:4px;"><?= url('webhook/flutterwave') ?></code>
            </p>
            <button type="submit" class="btn btn-primary" style="margin-top:12px;">
                <i class="fa fa-save" style="margin-right:8px;"></i>Save Flutterwave Settings
            </button>
        </div>
    </form>

    <!-- Paystack -->
    <form method="POST" action="<?= admin_url('payments') ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="section" value="paystack">
        <div class="glass" style="margin-bottom:16px; overflow:visible;">
            <h3 style="margin:0 0 16px 0; color:#e5e7eb;">
                <i class="fa fa-university" style="margin-right:8px; color:#8b5cf6;"></i>Paystack
                <span style="font-size:12px; background:<?= get_setting('paystack_enabled') == '1' ? '#22c55e' : '#ef4444' ?>; padding:2px 8px; border-radius:4px; margin-left:8px;">
                    <?= get_setting('paystack_enabled') == '1' ? 'Enabled' : 'Disabled' ?>
                </span>
            </h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:16px;">
                <div style="min-width:0;">
                    <label>Status</label>
                    <select name="paystack_enabled" class="form-control">
                        <option value="1" <?= get_setting('paystack_enabled') == '1' ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= get_setting('paystack_enabled') != '1' ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Environment</label>
                    <select name="paystack_env" class="form-control">
                        <option value="test" <?= get_setting('paystack_env') == 'test' ? 'selected' : '' ?>>Test</option>
                        <option value="live" <?= get_setting('paystack_env') == 'live' ? 'selected' : '' ?>>Live</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Public Key</label>
                    <input class="form-control" type="text" name="paystack_public_key" 
                           value="<?= htmlspecialchars(get_setting('paystack_public_key') ?? '') ?>" placeholder="pk_test_..." />
                </div>
                <div style="min-width:0;">
                    <label>Secret Key</label>
                    <input class="form-control" type="password" name="paystack_secret_key" 
                           value="<?= htmlspecialchars(get_setting('paystack_secret_key') ?? '') ?>" placeholder="sk_test_..." />
                </div>
            </div>
            <p style="color:#9ca3af; margin-top:10px;">
                Webhook URL: <code style="background:#374151; padding:2px 6px; border-radius:4px;"><?= url('webhook/paystack') ?></code>
            </p>
            <button type="submit" class="btn btn-primary" style="margin-top:12px;">
                <i class="fa fa-save" style="margin-right:8px;"></i>Save Paystack Settings
            </button>
        </div>
    </form>

    <!-- Strowallet Virtual Accounts -->
    <form method="POST" action="<?= admin_url('payments') ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="section" value="strowallet">
        <div class="glass" style="margin-bottom:16px; overflow:visible;">
            <h3 style="margin:0 0 16px 0; color:#e5e7eb;">
                <i class="fa fa-bank" style="margin-right:8px; color:#8b5cf6;"></i>Strowallet Virtual Accounts
                <span style="font-size:12px; background:<?= get_setting('strowallet_enabled') == '1' ? '#22c55e' : '#ef4444' ?>; padding:2px 8px; border-radius:4px; margin-left:8px;">
                    <?= get_setting('strowallet_enabled') == '1' ? 'Enabled' : 'Disabled' ?>
                </span>
            </h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:16px;">
                <div style="min-width:0;">
                    <label>Status</label>
                    <select name="strowallet_enabled" class="form-control">
                        <option value="1" <?= get_setting('strowallet_enabled') == '1' ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= get_setting('strowallet_enabled') != '1' ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>API Base URL</label>
                    <input class="form-control" type="text" name="strowallet_api_base"
                           value="<?= htmlspecialchars(get_setting('strowallet_api_base') ?? '') ?>" placeholder="https://api.strowallet.com" />
                </div>
                <div style="min-width:0;">
                    <label>Secret API Key</label>
                    <input class="form-control" type="password" name="strowallet_api_key"
                           value="<?= htmlspecialchars(get_setting('strowallet_api_key') ?? '') ?>" placeholder="sk_live_..." />
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:12px;">
                <i class="fa fa-save" style="margin-right:8px;"></i>Save Strowallet Settings
            </button>
        </div>
    </form>

    <!-- Bank Transfer -->
    <form method="POST" action="<?= admin_url('payments') ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="section" value="bank">
        <div class="glass" style="margin-bottom:16px; overflow:visible;">
            <h3 style="margin:0 0 16px 0; color:#e5e7eb;">
                <i class="fa fa-building" style="margin-right:8px; color:#8b5cf6;"></i>Bank Transfer (Manual)
                <span style="font-size:12px; background:<?= get_setting('bank_transfer_enabled') == '1' ? '#22c55e' : '#ef4444' ?>; padding:2px 8px; border-radius:4px; margin-left:8px;">
                    <?= get_setting('bank_transfer_enabled') == '1' ? 'Enabled' : 'Disabled' ?>
                </span>
            </h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:16px;">
                <div style="min-width:0;">
                    <label>Status</label>
                    <select name="bank_transfer_enabled" class="form-control">
                        <option value="1" <?= get_setting('bank_transfer_enabled') == '1' ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= get_setting('bank_transfer_enabled') != '1' ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Bank Name</label>
                    <input class="form-control" type="text" name="bank_name" 
                           value="<?= htmlspecialchars(get_setting('bank_name') ?? '') ?>" placeholder="e.g. GTBank, Access Bank" />
                </div>
                <div style="min-width:0;">
                    <label>Account Name</label>
                    <input class="form-control" type="text" name="bank_account_name" 
                           value="<?= htmlspecialchars(get_setting('bank_account_name') ?? '') ?>" placeholder="Account holder name" />
                </div>
                <div style="min-width:0;">
                    <label>Account Number</label>
                    <input class="form-control" type="text" name="bank_account_number" 
                           value="<?= htmlspecialchars(get_setting('bank_account_number') ?? '') ?>" placeholder="0000000000" />
                </div>
            </div>
            <div style="margin-top:12px;">
                <label>Payment Instructions</label>
                <textarea class="form-control" name="bank_instructions" rows="3" 
                          placeholder="Enter instructions for users making bank transfers..."><?= htmlspecialchars(get_setting('bank_instructions') ?? '') ?></textarea>
            </div>
            <p style="color:#9ca3af; margin-top:10px;">Manual verification required. Users will need to upload proof of payment.</p>
            <button type="submit" class="btn btn-primary" style="margin-top:12px;">
                <i class="fa fa-save" style="margin-right:8px;"></i>Save Bank Transfer Settings
            </button>
        </div>
    </form>

    <!-- Cryptocurrency -->
    <form method="POST" action="<?= admin_url('payments') ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="section" value="crypto">
        <div class="glass" style="margin-bottom:16px; overflow:visible;">
            <h3 style="margin:0 0 16px 0; color:#e5e7eb;">
                <i class="fab fa-bitcoin" style="margin-right:8px; color:#8b5cf6;"></i>Cryptocurrency
                <span style="font-size:12px; background:<?= get_setting('crypto_enabled') == '1' ? '#22c55e' : '#ef4444' ?>; padding:2px 8px; border-radius:4px; margin-left:8px;">
                    <?= get_setting('crypto_enabled') == '1' ? 'Enabled' : 'Disabled' ?>
                </span>
            </h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap:16px;">
                <div style="min-width:0;">
                    <label>Status</label>
                    <select name="crypto_enabled" class="form-control">
                        <option value="1" <?= get_setting('crypto_enabled') == '1' ? 'selected' : '' ?>>Enabled</option>
                        <option value="0" <?= get_setting('crypto_enabled') != '1' ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>
                <div style="min-width:0;">
                    <label>Bitcoin (BTC) Address</label>
                    <input class="form-control" type="text" name="crypto_btc_address" 
                           value="<?= htmlspecialchars(get_setting('crypto_btc_address') ?? '') ?>" placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa" />
                </div>
                <div style="min-width:0;">
                    <label>USDT (TRC20) Address</label>
                    <input class="form-control" type="text" name="crypto_usdt_address" 
                           value="<?= htmlspecialchars(get_setting('crypto_usdt_address') ?? '') ?>" placeholder="TRC20 Address" />
                </div>
                <div style="min-width:0;">
                    <label>Ethereum (ETH) Address</label>
                    <input class="form-control" type="text" name="crypto_eth_address" 
                           value="<?= htmlspecialchars(get_setting('crypto_eth_address') ?? '') ?>" placeholder="0x..." />
                </div>
            </div>
            <p style="color:#9ca3af; margin-top:10px;">Manual verification required for crypto payments.</p>
            <button type="submit" class="btn btn-primary" style="margin-top:12px;">
                <i class="fa fa-save" style="margin-right:8px;"></i>Save Crypto Settings
            </button>
        </div>
    </form>
</section>

<style>
    .glass h3 i { color: #8b5cf6; }
    .glass code { font-size: 12px; }
    .form-control:focus { border-color: #8b5cf6; box-shadow: 0 0 0 2px rgba(139,92,246,0.2); }
</style>
