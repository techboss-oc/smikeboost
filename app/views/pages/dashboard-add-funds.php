<?php
/**
 * Dashboard Add Funds Page - Fully Functional
 * Links to admin payment settings from database
 */
$seo = get_seo_tags('Add Funds', 'Deposit money to your SMM panel wallet', '');

$user = current_user();
$userId = $user['id'] ?? 0;

// Fetch user's current balance
$userRow = db_fetch("SELECT wallet_balance FROM users WHERE id = :id", ['id' => $userId]);
$walletBalance = $userRow['wallet_balance'] ?? 0;

// Fetch Payment Settings from Admin
$activeGateway = get_setting('active_payment_gateway', 'flutterwave');
$minDeposit = (float) get_setting('min_deposit', 100);
$maxDeposit = (float) get_setting('max_deposit', 1000000);

// Flutterwave Settings
$flwEnabled = get_setting('flutterwave_enabled', '0') === '1';
$flwPublicKey = get_setting('flutterwave_public_key', '');
$flwEnv = get_setting('flutterwave_env', 'sandbox');

// Paystack Settings
$paystackEnabled = get_setting('paystack_enabled', '0') === '1';
$paystackPublicKey = get_setting('paystack_public_key', '');
$paystackEnv = get_setting('paystack_env', 'test');

// Bank Transfer Settings
$bankEnabled = get_setting('bank_transfer_enabled', '0') === '1';
$bankName = get_setting('bank_name', '');
$accountName = get_setting('bank_account_name', '');
$accountNumber = get_setting('bank_account_number', '');
$bankInstructions = get_setting('bank_instructions', 'Please transfer the exact amount and upload your proof of payment.');

// Crypto Settings
$cryptoEnabled = get_setting('crypto_enabled', '0') === '1';
$btcAddress = get_setting('crypto_btc_address', '');
$usdtAddress = get_setting('crypto_usdt_address', '');
$ethAddress = get_setting('crypto_eth_address', '');

// Strowallet Settings
$strowalletEnabled = get_setting('strowallet_enabled', '0') === '1';
$strowalletDetails = $strowalletEnabled ? strowallet_ensure_virtual_account($userId, $user['name'] ?? '', $user['email'] ?? '') : null;

// Determine which methods to show based on active gateway setting
$showFlutterwave = $flwEnabled && ($activeGateway === 'flutterwave' || $activeGateway === 'both');
$showPaystack = $paystackEnabled && ($activeGateway === 'paystack' || $activeGateway === 'both');

// Fetch Transaction History
$transactions = db_fetch_all(
    "SELECT * FROM transactions WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20",
    ['uid' => $userId]
);

// CSRF Token
$csrfToken = csrf_token();

// Flash Messages
$flashSuccess = flash('success');
$flashError = flash('error');
$flashInfo = flash('info');
?>

<section class="add-funds-page">
    <div class="page-header">
        <h1>Add Funds</h1>
        <p>Deposit money to your wallet</p>
    </div>

    <?php if ($flashSuccess): ?>
    <div class="alert alert-success" style="margin-bottom: 1rem; padding: 1rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
        <i class="fas fa-check-circle"></i> <?php echo e($flashSuccess); ?>
    </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
    <div class="alert alert-danger" style="margin-bottom: 1rem; padding: 1rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">
        <i class="fas fa-exclamation-circle"></i> <?php echo e($flashError); ?>
    </div>
    <?php endif; ?>

    <?php if ($flashInfo): ?>
    <div class="alert alert-info" style="margin-bottom: 1rem; padding: 1rem; border-radius: var(--radius-md); background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.3); color: #3b82f6;">
        <i class="fas fa-info-circle"></i> <?php echo e($flashInfo); ?>
    </div>
    <?php endif; ?>

    <!-- Current Balance Card -->
    <div class="glass-card" style="margin-bottom: var(--spacing-xl); padding: var(--spacing-lg); background: linear-gradient(135deg, rgba(168,85,247,0.15), rgba(236,72,153,0.15));">
        <div class="d-flex align-center justify-between" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">Current Balance</p>
                <h2 style="margin: 0.5rem 0 0 0; color: var(--color-success); font-size: 2rem;"><?php echo format_currency($walletBalance); ?></h2>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0; color: var(--text-secondary); font-size: 0.85rem;">Min Deposit: <?php echo format_currency($minDeposit); ?></p>
                <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.85rem;">Max Deposit: <?php echo format_currency($maxDeposit); ?></p>
            </div>
        </div>
    </div>

    <div class="funds-container grid-2" style="max-width: 1200px; margin: 0 auto;">
        <!-- Payment Methods -->
        <div>
            <h2 class="mb-lg" style="font-size: 1.25rem;">Select Payment Method</h2>
            
            <?php if ($showFlutterwave): ?>
            <div class="payment-method-card mb-lg active" id="method-flutterwave" onclick="selectMethod('flutterwave')">
                <div class="d-flex align-center gap-md">
                    <i class="fas fa-credit-card" style="font-size: 2rem; color: var(--color-primary);"></i>
                    <div>
                        <h3 class="mb-sm" style="margin-top: 0;">Flutterwave</h3>
                        <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Pay with Card, Bank Transfer, USSD</p>
                    </div>
                </div>
                <span class="badge badge-success" style="position: absolute; top: 10px; right: 10px;">Instant</span>
            </div>
            <?php endif; ?>

            <?php if ($showPaystack): ?>
            <div class="payment-method-card mb-lg <?php echo !$showFlutterwave ? 'active' : ''; ?>" id="method-paystack" onclick="selectMethod('paystack')">
                <div class="d-flex align-center gap-md">
                    <i class="fas fa-bolt" style="font-size: 2rem; color: #00c3f7;"></i>
                    <div>
                        <h3 class="mb-sm" style="margin-top: 0;">Paystack</h3>
                        <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Pay with Card, Bank, USSD, Mobile Money</p>
                    </div>
                </div>
                <span class="badge badge-success" style="position: absolute; top: 10px; right: 10px;">Instant</span>
            </div>
            <?php endif; ?>

            <?php if ($bankEnabled): ?>
            <div class="payment-method-card mb-lg <?php echo (!$showFlutterwave && !$showPaystack) ? 'active' : ''; ?>" id="method-bank" onclick="selectMethod('bank')">
                <div class="d-flex align-center gap-md">
                    <i class="fas fa-university" style="font-size: 2rem; color: var(--text-secondary);"></i>
                    <div>
                        <h3 class="mb-sm" style="margin-top: 0;">Bank Transfer</h3>
                        <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Direct bank transfer (Manual verification)</p>
                    </div>
                </div>
                <span class="badge badge-warning" style="position: absolute; top: 10px; right: 10px;">Manual</span>
            </div>
            <?php endif; ?>

            <?php if ($strowalletEnabled): ?>
            <div class="payment-method-card mb-lg <?php echo (!$showFlutterwave && !$showPaystack && !$bankEnabled) ? 'active' : ''; ?>" id="method-strowallet" onclick="selectMethod('strowallet')">
                <div class="d-flex align-center gap-md">
                    <i class="fas fa-building" style="font-size: 2rem; color: var(--color-primary);"></i>
                    <div>
                        <h3 class="mb-sm" style="margin-top: 0;">Strowallet (Virtual Account)</h3>
                        <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Transfer to your unique account number</p>
                    </div>
                </div>
                <span class="badge badge-success" style="position: absolute; top: 10px; right: 10px;">Instant</span>
            </div>
            <?php endif; ?>

            <?php if ($cryptoEnabled): ?>
            <div class="payment-method-card" id="method-crypto" onclick="selectMethod('crypto')">
                <div class="d-flex align-center gap-md">
                    <i class="fab fa-bitcoin" style="font-size: 2rem; color: #f7931a;"></i>
                    <div>
                        <h3 class="mb-sm" style="margin-top: 0;">Cryptocurrency</h3>
                        <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">BTC, ETH, USDT</p>
                    </div>
                </div>
                <span class="badge badge-warning" style="position: absolute; top: 10px; right: 10px;">Manual</span>
            </div>
            <?php endif; ?>

            <?php if (!$showFlutterwave && !$showPaystack && !$bankEnabled && !$cryptoEnabled): ?>
            <div class="alert alert-warning" style="padding: 1rem; border-radius: var(--radius-md); background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.3);">
                <i class="fas fa-exclamation-triangle"></i> No payment methods are currently enabled. Please contact support.
            </div>
            <?php endif; ?>
        </div>

        <!-- Deposit Forms -->
        <div>
            <h2 class="mb-lg" style="font-size: 1.25rem;">Deposit Amount</h2>
            
            <?php if ($showFlutterwave): ?>
            <!-- Flutterwave Form -->
            <form id="form-flutterwave" class="glass-card d-flex" style="flex-direction: column; gap: var(--spacing-lg);">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Amount (<?php echo CURRENCY_SYMBOL; ?>)</label>
                    <input type="number" name="amount" id="flw-amount" class="form-control amount-input" 
                           placeholder="Enter amount" min="<?php echo $minDeposit; ?>" max="<?php echo $maxDeposit; ?>" step="100" required>
                    <small class="text-tertiary">Min: <?php echo format_currency($minDeposit); ?> | Max: <?php echo format_currency($maxDeposit); ?></small>
                </div>

                <div class="preset-amounts">
                    <button type="button" class="preset-btn" onclick="setAmount(1000, 'flw')"><strong><?php echo format_currency(1000); ?></strong></button>
                    <button type="button" class="preset-btn" onclick="setAmount(5000, 'flw')"><strong><?php echo format_currency(5000); ?></strong></button>
                    <button type="button" class="preset-btn" onclick="setAmount(10000, 'flw')"><strong><?php echo format_currency(10000); ?></strong></button>
                    <button type="button" class="preset-btn" onclick="setAmount(25000, 'flw')"><strong><?php echo format_currency(25000); ?></strong></button>
                </div>

                <button type="button" class="btn btn-primary btn-block btn-lg" onclick="payWithFlutterwave()">
                    <i class="fas fa-lock"></i> Pay with Flutterwave
                </button>
            </form>
            <?php endif; ?>

            <?php if ($showPaystack): ?>
            <!-- Paystack Form -->
            <form id="form-paystack" class="glass-card d-flex" style="<?php echo $showFlutterwave ? 'display: none !important;' : ''; ?> flex-direction: column; gap: var(--spacing-lg);">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Amount (<?php echo CURRENCY_SYMBOL; ?>)</label>
                    <input type="number" name="amount" id="ps-amount" class="form-control amount-input" 
                           placeholder="Enter amount" min="<?php echo $minDeposit; ?>" max="<?php echo $maxDeposit; ?>" step="100" required>
                    <small class="text-tertiary">Min: <?php echo format_currency($minDeposit); ?> | Max: <?php echo format_currency($maxDeposit); ?></small>
                </div>

                <div class="preset-amounts">
                    <button type="button" class="preset-btn" onclick="setAmount(1000, 'ps')"><strong><?php echo format_currency(1000); ?></strong></button>
                    <button type="button" class="preset-btn" onclick="setAmount(5000, 'ps')"><strong><?php echo format_currency(5000); ?></strong></button>
                    <button type="button" class="preset-btn" onclick="setAmount(10000, 'ps')"><strong><?php echo format_currency(10000); ?></strong></button>
                    <button type="button" class="preset-btn" onclick="setAmount(25000, 'ps')"><strong><?php echo format_currency(25000); ?></strong></button>
                </div>

                <button type="button" class="btn btn-primary btn-block btn-lg" onclick="payWithPaystack()" style="background: linear-gradient(135deg, #00c3f7, #0070ba);">
                    <i class="fas fa-lock"></i> Pay with Paystack
                </button>
            </form>
            <?php endif; ?>

            <?php if ($bankEnabled): ?>
            <!-- Bank Transfer Form -->
            <form id="form-bank" class="glass-card d-flex" style="<?php echo ($showFlutterwave || $showPaystack) ? 'display: none !important;' : ''; ?> flex-direction: column; gap: var(--spacing-lg);" method="POST" action="<?php echo url('dashboard/add-funds/bank'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="alert alert-info" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); padding: 1rem; border-radius: var(--radius-md);">
                    <h4 style="margin-top: 0; color: var(--color-info);">Bank Details</h4>
                    <p style="margin-bottom: 0.5rem;"><strong>Bank:</strong> <?php echo e($bankName); ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Account Name:</strong> <?php echo e($accountName); ?></p>
                    <p style="margin-bottom: 0.5rem;"><strong>Account Number:</strong> 
                        <span class="text-primary" style="font-family: monospace; font-size: 1.1em; cursor: pointer;" onclick="copyToClipboard('<?php echo e($accountNumber); ?>')" title="Click to copy">
                            <?php echo e($accountNumber); ?> <i class="fas fa-copy" style="font-size: 0.8em;"></i>
                        </span>
                    </p>
                    <p style="margin-bottom: 0; font-size: 0.9rem; color: var(--text-secondary);"><?php echo e($bankInstructions); ?></p>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Amount Sent (<?php echo CURRENCY_SYMBOL; ?>)</label>
                    <input type="number" name="amount" class="form-control amount-input" 
                           placeholder="Enter amount sent" min="<?php echo $minDeposit; ?>" max="<?php echo $maxDeposit; ?>" step="100" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Payment Proof (Screenshot/Receipt)</label>
                    <input type="file" name="proof" class="form-control" accept="image/*,.pdf" required>
                    <small class="text-tertiary">Accepted: JPG, PNG, PDF (Max 5MB)</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-upload"></i> Submit for Verification
                </button>
            </form>
            <?php endif; ?>

            <?php if ($strowalletEnabled): ?>
            <!-- Strowallet Virtual Account -->
            <div id="form-strowallet" class="glass-card d-flex" style="<?php echo ($showFlutterwave || $showPaystack || $bankEnabled) ? 'display: none !important;' : ''; ?> flex-direction: column; gap: var(--spacing-lg);">
                <div class="alert alert-info" style="background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); padding: 1rem; border-radius: var(--radius-md);">
                    <h4 style="margin-top: 0; color: var(--color-success);">Your Unique Deposit Account</h4>
                    <?php if ($strowalletDetails && !empty($strowalletDetails['strowallet_account_number'])): ?>
                        <p style="margin-bottom: 0.5rem;"><strong>Bank:</strong> <?php echo e($strowalletDetails['strowallet_bank_name'] ?? ''); ?></p>
                        <p style="margin-bottom: 0.5rem;"><strong>Account Name:</strong> <?php echo e($strowalletDetails['strowallet_account_name'] ?? ($user['name'] ?? '')); ?></p>
                        <p style="margin-bottom: 0.5rem;"><strong>Account Number:</strong>
                            <span class="text-primary" style="font-family: monospace; font-size: 1.1em; cursor: pointer;" onclick="copyToClipboard('<?php echo e($strowalletDetails['strowallet_account_number']); ?>')" title="Click to copy">
                                <?php echo e($strowalletDetails['strowallet_account_number']); ?> <i class="fas fa-copy" style="font-size: 0.8em;"></i>
                            </span>
                        </p>
                        <p style="margin-bottom: 0; font-size: 0.9rem; color: var(--text-secondary);">Transfer any amount within limits. Funds are auto‑credited once received.</p>
                    <?php else: ?>
                        <p style="margin-bottom: 0; font-size: 0.9rem; color: var(--text-secondary);">Strowallet is enabled but not configured. Please contact support or an admin to set API credentials.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($cryptoEnabled): ?>
            <!-- Crypto Form -->
            <form id="form-crypto" class="glass-card d-flex" style="display: none !important; flex-direction: column; gap: var(--spacing-lg);" method="POST" action="<?php echo url('dashboard/add-funds/crypto'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Select Cryptocurrency</label>
                    <select name="crypto_type" class="form-control" id="crypto-select" onchange="showCryptoAddress()">
                        <?php if ($btcAddress): ?><option value="btc">Bitcoin (BTC)</option><?php endif; ?>
                        <?php if ($usdtAddress): ?><option value="usdt">USDT (TRC20)</option><?php endif; ?>
                        <?php if ($ethAddress): ?><option value="eth">Ethereum (ETH)</option><?php endif; ?>
                    </select>
                </div>

                <div class="alert alert-info" id="crypto-address-box" style="background: rgba(247, 147, 26, 0.1); border: 1px solid rgba(247, 147, 26, 0.2); padding: 1rem; border-radius: var(--radius-md);">
                    <h4 style="margin-top: 0; color: #f7931a;">Send to Address</h4>
                    <p style="margin-bottom: 0; word-break: break-all; font-family: monospace; cursor: pointer;" id="crypto-address" onclick="copyToClipboard(this.innerText)">
                        <?php echo e($btcAddress ?: $usdtAddress ?: $ethAddress); ?> <i class="fas fa-copy" style="font-size: 0.8em;"></i>
                    </p>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Amount Sent (USD equivalent)</label>
                    <input type="number" name="amount" class="form-control" placeholder="Enter USD value" min="10" step="0.01" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Transaction Hash / Proof</label>
                    <input type="text" name="tx_hash" class="form-control" placeholder="Enter transaction hash">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label>Screenshot (Optional)</label>
                    <input type="file" name="proof" class="form-control" accept="image/*,.pdf">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="background: linear-gradient(135deg, #f7931a, #ff9500);">
                    <i class="fas fa-upload"></i> Submit for Verification
                </button>
            </form>
            <?php endif; ?>

            <div class="d-flex gap-md align-center mt-lg" style="background: rgba(16, 185, 129, 0.1); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(16, 185, 129, 0.2);">
                <i class="fas fa-shield-alt text-success" style="font-size: 1.5rem;"></i>
                <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">
                    All transactions are encrypted and secure. Automatic payments are credited instantly.
                </p>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div style="margin-top: var(--spacing-2xl);">
        <h2 class="mb-lg" style="font-size: 1.25rem;">Transaction History</h2>
        <div class="table-responsive glass-card" style="padding: 0; overflow: hidden;">
            <table class="orders-table" style="margin: 0;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-tertiary);">
                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            No transactions yet
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?></td>
                        <td style="font-family: monospace; font-size: 0.85rem;"><?php echo e($tx['reference']); ?></td>
                        <td>
                            <span class="<?php echo in_array($tx['type'], ['credit', 'deposit']) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo in_array($tx['type'], ['credit', 'deposit']) ? '+' : '-'; ?>
                                <?php echo format_currency($tx['amount']); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            $gatewayIcons = [
                                'flutterwave' => '<i class="fas fa-credit-card"></i> Flutterwave',
                                'paystack' => '<i class="fas fa-bolt"></i> Paystack',
                                'bank_transfer' => '<i class="fas fa-university"></i> Bank',
                                'crypto' => '<i class="fab fa-bitcoin"></i> Crypto',
                                'strowallet' => '<i class="fas fa-building"></i> Strowallet',
                            ];
                            echo $gatewayIcons[$tx['gateway']] ?? ucfirst($tx['gateway']);
                            ?>
                        </td>
                        <td>
                            <?php
                            $statusClasses = [
                                'completed' => 'badge-completed',
                                'pending' => 'badge-pending',
                                'failed' => 'badge-cancelled'
                            ];
                            $statusClass = $statusClasses[$tx['status']] ?? 'badge-pending';
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($tx['status']); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Flutterwave Script -->
    <?php if ($showFlutterwave): ?>
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <?php endif; ?>

    <!-- Paystack Script -->
    <?php if ($showPaystack): ?>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <?php endif; ?>

    <script>
        const minDeposit = <?php echo $minDeposit; ?>;
        const maxDeposit = <?php echo $maxDeposit; ?>;

        function selectMethod(method) {
            // Update Cards
            document.querySelectorAll('.payment-method-card').forEach(el => el.classList.remove('active'));
            const methodCard = document.getElementById('method-' + method);
            if (methodCard) methodCard.classList.add('active');

            // Hide all forms
            const forms = ['form-flutterwave', 'form-paystack', 'form-bank', 'form-crypto', 'form-strowallet'];
            forms.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.setProperty('display', 'none', 'important');
            });

            // Show selected form
            const selectedForm = document.getElementById('form-' + method);
            if (selectedForm) selectedForm.style.setProperty('display', 'flex', 'important');
        }

        function setAmount(val, prefix) {
            const input = document.getElementById(prefix + '-amount');
            if (input) input.value = val;
        }

        function validateAmount(amount) {
            if (!amount || amount < minDeposit) {
                alert('Minimum deposit is <?php echo format_currency($minDeposit); ?>');
                return false;
            }
            if (amount > maxDeposit) {
                alert('Maximum deposit is <?php echo format_currency($maxDeposit); ?>');
                return false;
            }
            return true;
        }

        <?php if ($showFlutterwave): ?>
        function payWithFlutterwave() {
            const amount = parseFloat(document.getElementById('flw-amount').value);
            if (!validateAmount(amount)) return;

            FlutterwaveCheckout({
                public_key: "<?php echo e($flwPublicKey); ?>",
                tx_ref: "FLW-" + Date.now() + "-<?php echo $userId; ?>",
                amount: amount,
                currency: "<?php echo CURRENCY; ?>",
                payment_options: "card, banktransfer, ussd",
                customer: {
                    email: "<?php echo e($user['email']); ?>",
                    name: "<?php echo e($user['name']); ?>",
                },
                customizations: {
                    title: "<?php echo e(SITE_NAME); ?> Wallet",
                    description: "Fund your wallet",
                    logo: "<?php echo asset('img/logo.png'); ?>",
                },
                redirect_url: "<?php echo url('dashboard/add-funds/verify'); ?>?gateway=flutterwave",
                callback: function (data) {
                    if (data.status === 'successful') {
                        window.location.href = "<?php echo url('dashboard/add-funds/verify'); ?>?gateway=flutterwave&tx_ref=" + data.tx_ref + "&transaction_id=" + data.transaction_id;
                    } else {
                        alert('Payment was not successful. Please try again.');
                    }
                },
                onclose: function() {
                    // User closed modal
                }
            });
        }
        <?php endif; ?>

        <?php if ($showPaystack): ?>
        function payWithPaystack() {
            const amount = parseFloat(document.getElementById('ps-amount').value);
            if (!validateAmount(amount)) return;

            const handler = PaystackPop.setup({
                key: "<?php echo e($paystackPublicKey); ?>",
                email: "<?php echo e($user['email']); ?>",
                amount: amount * 100, // Paystack uses kobo
                currency: "<?php echo CURRENCY; ?>",
                ref: "PS-" + Date.now() + "-<?php echo $userId; ?>",
                metadata: {
                    user_id: <?php echo $userId; ?>,
                    custom_fields: [
                        {
                            display_name: "User",
                            variable_name: "user_name",
                            value: "<?php echo e($user['name']); ?>"
                        }
                    ]
                },
                callback: function(response) {
                    window.location.href = "<?php echo url('dashboard/add-funds/verify'); ?>?gateway=paystack&reference=" + response.reference;
                },
                onClose: function() {
                    // User closed popup
                }
            });
            handler.openIframe();
        }
        <?php endif; ?>

        <?php if ($cryptoEnabled): ?>
        const cryptoAddresses = {
            btc: "<?php echo e($btcAddress); ?>",
            usdt: "<?php echo e($usdtAddress); ?>",
            eth: "<?php echo e($ethAddress); ?>"
        };

        function showCryptoAddress() {
            const select = document.getElementById('crypto-select');
            const addressEl = document.getElementById('crypto-address');
            if (select && addressEl && cryptoAddresses[select.value]) {
                addressEl.innerHTML = cryptoAddresses[select.value] + ' <i class="fas fa-copy" style="font-size: 0.8em;"></i>';
            }
        }
        <?php endif; ?>

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied to clipboard!');
            }).catch(() => {
                // Fallback
                const temp = document.createElement('textarea');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                document.execCommand('copy');
                document.body.removeChild(temp);
                alert('Copied to clipboard!');
            });
        }
    </script>

    <style>
        .payment-method-card {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method-card:hover {
            transform: translateY(-2px);
            border-color: var(--color-primary);
        }
        .payment-method-card.active {
            border-color: var(--color-primary);
            background: rgba(168, 85, 247, 0.1);
        }
        .preset-amounts {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }
        .preset-btn {
            padding: 0.75rem;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.03);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-primary);
        }
        .preset-btn:hover {
            background: rgba(168, 85, 247, 0.2);
            border-color: var(--color-primary);
        }
        @media (max-width: 600px) {
            .preset-amounts {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</section>
