<?php
require_once APP_PATH . '/models/Transaction.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/libraries/Mailer.php';

class PaymentController
{
    private $transactionModel;
    private $debugLog;

    public function __construct()
    {
        $this->transactionModel = new Transaction();
        $this->debugLog = APP_PATH . '/bank_deposit_debug.log';
    }

    public function handleBankTransfer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('dashboard/add-funds');
        }

        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in again.');
            redirect('login');
        }

        // CSRF token already validated via dashboard form (dashboard layout includes CSRF middleware)
        $this->logManualDeposit('Bank transfer POST payload: ' . json_encode($_POST));

        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $minDeposit = (float) get_setting('min_deposit', 100);
        $maxDeposit = (float) get_setting('max_deposit', 1000000);

        if (!$amount || $amount < $minDeposit) {
            flash('error', "Invalid amount. Minimum deposit is " . format_currency($minDeposit));
            redirect('dashboard/add-funds');
        }

        if ($amount > $maxDeposit) {
            flash('error', "Invalid amount. Maximum deposit is " . format_currency($maxDeposit));
            redirect('dashboard/add-funds');
        }

        // Handle File Upload
        $proofPath = null;
        if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
            $fileError = $_FILES['proof']['error'] ?? 'missing';
            $this->logManualDeposit('Incoming proof upload error code: ' . $fileError);

            $uploadDir = PUBLIC_PATH . '/uploads/proofs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
                $this->logManualDeposit('Created upload dir: ' . $uploadDir);
            }

            // Validate file size (5MB max)
            if ($_FILES['proof']['size'] > 5 * 1024 * 1024) {
                $this->logManualDeposit('File too large: ' . $_FILES['proof']['size']);
                flash('error', 'File too large. Maximum size is 5MB.');
                redirect('dashboard/add-funds');
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            $mimeType = null;
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                if ($finfo) {
                    $mimeType = finfo_file($finfo, $_FILES['proof']['tmp_name']);
                    finfo_close($finfo);
                }
            }

            if ($mimeType === null && function_exists('mime_content_type')) {
                $mimeType = mime_content_type($_FILES['proof']['tmp_name']);
            }

            $this->logManualDeposit('Detected mime type: ' . ($mimeType ?? 'unknown'));

            if ($mimeType !== null && !in_array($mimeType, $allowedTypes)) {
                $this->logManualDeposit('Rejected mime type: ' . $mimeType);
                flash('error', 'Invalid file type. Allowed: JPG, PNG, GIF, PDF');
                redirect('dashboard/add-funds');
            }

            $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
            $filename = 'proof_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['proof']['tmp_name'], $dest)) {
                $this->logManualDeposit('Proof uploaded to ' . $dest);
                $proofPath = 'uploads/proofs/' . $filename;
            } else {
                $this->logManualDeposit('move_uploaded_file failed from ' . $_FILES['proof']['tmp_name'] . ' to ' . $dest);
                flash('error', 'Failed to upload proof.');
                redirect('dashboard/add-funds');
            }
        } else {
            $this->logManualDeposit('Proof upload missing or error code not OK.');
            flash('error', 'Payment proof is required.');
            redirect('dashboard/add-funds');
        }

        $this->ensureTransactionsSchema();

        try {
            $hasProofColumn = $this->columnExists('transactions', 'proof_image');
        } catch (Throwable $e) {
            $this->logManualDeposit('Column check failed: ' . $e->getMessage());
            $hasProofColumn = false;
        }

        if (!$hasProofColumn) {
            $this->logManualDeposit('proof_image column missing; omitting proof path.');
            $proofPath = null;
        } else {
            $this->logManualDeposit('proof_image column present; keeping proof path.');
        }

        try {
            $reference = 'BANK-' . time() . '-' . $user['id'];
            $this->transactionModel->create([
                'user_id' => $user['id'],
                'amount' => $amount,
                'type' => 'deposit',
                'gateway' => 'bank_transfer',
                'status' => 'pending',
                'reference' => $reference,
                'proof_image' => $proofPath
            ]);
            $this->logManualDeposit('Transaction created for user ' . $user['id']);
        } catch (Throwable $e) {
            $this->logManualDeposit('Transaction create failed: ' . $e->getMessage());
            flash('error', 'Unable to record your deposit right now. Please try again.');
            redirect('dashboard/add-funds');
        }

        try {
            $mailer = new Mailer();
            $mailer->send($user['email'], 'Deposit Request Received', 'deposit_pending', [
                'name' => $user['name'],
                'amount_fmt' => format_currency($amount),
                'gateway' => 'Bank Transfer',
                'reference' => $reference
            ]);
        } catch (Throwable $e) {
        }

        $this->logManualDeposit('Redirecting with success message.');
        flash('success', 'Deposit request submitted successfully! Admin will review and credit your account shortly.');
        redirect('dashboard/add-funds');
    }

    public function handleCryptoTransfer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('dashboard/add-funds');
        }

        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in again.');
            redirect('login');
        }

        // CSRF token already validated via dashboard form
        $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
        $cryptoType = $_POST['crypto_type'] ?? 'btc';
        $txHash = trim($_POST['tx_hash'] ?? '');

        if (!$amount || $amount < 10) {
            flash('error', 'Invalid amount. Minimum is $10 USD equivalent.');
            redirect('dashboard/add-funds');
        }

        // Handle File Upload (optional for crypto)
        $proofPath = null;
        if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = PUBLIC_PATH . '/uploads/proofs/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION);
            $filename = 'crypto_' . $user['id'] . '_' . time() . '.' . $ext;
            $dest = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['proof']['tmp_name'], $dest)) {
                $proofPath = 'uploads/proofs/' . $filename;
            }
        }

        // Create Transaction
        $reference = 'CRYPTO-' . strtoupper($cryptoType) . '-' . time() . '-' . $user['id'];
        if ($txHash) {
            $reference .= '-' . substr($txHash, 0, 20);
        }

        if (!$this->columnExists('transactions', 'proof_image')) {
            $proofPath = null;
        }

        $this->transactionModel->create([
            'user_id' => $user['id'],
            'amount' => $amount,
            'type' => 'deposit',
            'gateway' => 'crypto',
            'status' => 'pending',
            'reference' => $reference,
            'proof_image' => $proofPath
        ]);
        try {
            $mailer = new Mailer();
            $mailer->send($user['email'], 'Deposit Request Received', 'deposit_pending', [
                'name' => $user['name'],
                'amount_fmt' => format_currency($amount),
                'gateway' => 'Crypto (' . strtoupper($cryptoType) . ')',
                'reference' => $reference
            ]);
        } catch (Throwable $e) {
        }

        flash('success', 'Crypto deposit request submitted! Admin will verify and credit your account.');
        redirect('dashboard/add-funds');
    }

    public function verifyPayment()
    {
        $gateway = $_GET['gateway'] ?? 'flutterwave';

        if ($gateway === 'paystack') {
            $this->verifyPaystack();
        } else {
            $this->verifyFlutterwave();
        }
    }

    public function verifyFlutterwave()
    {
        $txRef = $_GET['tx_ref'] ?? '';
        $transactionId = $_GET['transaction_id'] ?? '';
        $status = $_GET['status'] ?? '';

        if ($status === 'cancelled') {
            flash('error', 'Transaction cancelled.');
            redirect('dashboard/add-funds');
        }

        if (!$transactionId) {
            flash('error', 'No transaction ID provided.');
            redirect('dashboard/add-funds');
        }

        // Check if transaction already processed (use tx_ref from response or query)
        $existing = db_fetch("SELECT id FROM transactions WHERE reference = :ref", ['ref' => $txRef]);
        if ($existing) {
            flash('info', 'Transaction already processed.');
            redirect('dashboard/add-funds');
        }

        // Verify with Flutterwave API
        $secretKey = get_setting('flutterwave_secret_key');

        if (!$secretKey) {
            flash('error', 'Payment gateway not configured properly.');
            redirect('dashboard/add-funds');
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transactionId}/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $secretKey
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $res = json_decode($response, true);

        $dataStatus = strtolower($res['data']['status'] ?? '');
        if ($httpCode === 200 && isset($res['status']) && $res['status'] === 'success' && in_array($dataStatus, ['successful', 'success', 'completed'], true)) {
            $amount = (float)($res['data']['amount'] ?? 0);
            $ref = $res['data']['tx_ref'] ?? $txRef;
            $currency = strtoupper($res['data']['currency'] ?? CURRENCY);
            if ($currency !== CURRENCY) {
                flash('error', 'Currency mismatch for payment.');
                redirect('dashboard/add-funds');
            }
            $uid = 0;
            if (is_string($ref) && preg_match('/^FLW-\d+-(\d+)$/', $ref, $m)) {
                $uid = (int)$m[1];
            }
            $userRow = null;
            if ($uid > 0) {
                $userRow = db_fetch("SELECT id, email, name FROM users WHERE id = :id", ['id' => $uid]);
            }
            if (!$userRow) {
                $email = $res['data']['customer']['email'] ?? '';
                if ($email) {
                    $userRow = db_fetch("SELECT id, email, name FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
                }
            }
            if (!$userRow) {
                flash('error', 'Unable to locate user for payment.');
                redirect('dashboard/add-funds');
            }

            // Prevent double-credit if ref already exists
            $already = db_fetch("SELECT id FROM transactions WHERE reference = :ref", ['ref' => $ref]);
            if ($already) {
                flash('info', 'Transaction already processed.');
                redirect('dashboard/add-funds');
            }

            db_execute("UPDATE users SET wallet_balance = wallet_balance + :amt WHERE id = :uid", ['amt' => $amount, 'uid' => $userRow['id']]);

            $this->transactionModel->create([
                'user_id' => $userRow['id'],
                'amount' => $amount,
                'type' => 'deposit',
                'gateway' => 'flutterwave',
                'status' => 'completed',
                'reference' => $ref
            ]);

            try {
                $mailer = new Mailer();
                $mailer->send($userRow['email'], 'Deposit Completed', 'deposit_completed', [
                    'name' => $userRow['name'],
                    'amount_fmt' => format_currency($amount),
                    'reference' => $ref
                ]);
            } catch (Throwable $e) {
            }

            flash('success', "Wallet funded with " . format_currency($amount) . " successfully!");
        } else {
            flash('error', 'Transaction verification failed. Please contact support if money was deducted.');
        }

        redirect('dashboard/add-funds');
    }

    public function verifyPaystack()
    {
        $reference = $_GET['reference'] ?? '';

        if (!$reference) {
            flash('error', 'No reference provided.');
            redirect('dashboard/add-funds');
        }

        // Check if transaction already processed
        $existing = db_fetch("SELECT id FROM transactions WHERE reference = :ref", ['ref' => $reference]);
        if ($existing) {
            flash('info', 'Transaction already processed.');
            redirect('dashboard/add-funds');
        }

        // Verify with Paystack API
        $secretKey = get_setting('paystack_secret_key');

        if (!$secretKey) {
            flash('error', 'Payment gateway not configured properly.');
            redirect('dashboard/add-funds');
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $secretKey,
                "Cache-Control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $res = json_decode($response, true);

        if ($httpCode === 200 && isset($res['status']) && $res['status'] === true && $res['data']['status'] === 'success') {
            // Paystack returns amount in kobo (divide by 100)
            $amount = $res['data']['amount'] / 100;
            $user = current_user();

            // Credit User
            db_execute("UPDATE users SET wallet_balance = wallet_balance + :amt WHERE id = :uid", ['amt' => $amount, 'uid' => $user['id']]);

            // Record Transaction
            $this->transactionModel->create([
                'user_id' => $user['id'],
                'amount' => $amount,
                'type' => 'deposit',
                'gateway' => 'paystack',
                'status' => 'completed',
                'reference' => $reference
            ]);

            try {
                $mailer = new Mailer();
                $mailer->send($user['email'], 'Deposit Completed', 'deposit_completed', [
                    'name' => $user['name'],
                    'amount_fmt' => format_currency($amount),
                    'reference' => $reference
                ]);
            } catch (Throwable $e) {
            }

            flash('success', "Wallet funded with " . format_currency($amount) . " successfully!");
        } else {
            flash('error', 'Transaction verification failed. Please contact support if money was deducted.');
        }

        redirect('dashboard/add-funds');
    }

    private function ensureTransactionsSchema()
    {
        try {
            $columns = db_fetch_all("SHOW COLUMNS FROM transactions");
        } catch (Throwable $e) {
            $this->logManualDeposit('Schema introspection failed: ' . $e->getMessage());
            return;
        }

        $hasProofColumn = false;
        $typeDefinition = null;

        foreach ($columns as $column) {
            if (($column['Field'] ?? '') === 'proof_image') {
                $hasProofColumn = true;
            }

            if (($column['Field'] ?? '') === 'type') {
                $typeDefinition = $column['Type'] ?? null;
            }
        }

        if (!$hasProofColumn) {
            try {
                db_execute("ALTER TABLE transactions ADD COLUMN proof_image VARCHAR(255) NULL AFTER reference");
                $this->logManualDeposit('Added proof_image column to transactions table.');
            } catch (Throwable $e) {
                $this->logManualDeposit('Failed to add proof_image column: ' . $e->getMessage());
            }
        }

        if ($typeDefinition) {
            $enumValues = [];
            if (stripos($typeDefinition, 'enum(') === 0) {
                $trimmed = substr($typeDefinition, 5, -1);
                $enumValues = array_map(function ($value) {
                    return trim($value, "'\" ");
                }, explode(',', $trimmed));
            }

            if (!in_array('deposit', $enumValues, true)) {
                try {
                    db_execute("ALTER TABLE transactions MODIFY COLUMN type ENUM('credit','debit','deposit','withdrawal') NOT NULL");
                    $this->logManualDeposit('Updated transactions.type enum to include deposit.');
                } catch (Throwable $e) {
                    $this->logManualDeposit('Failed to update transactions.type enum: ' . $e->getMessage());
                }
            }
        }
    }

    private function columnExists($table, $column)
    {
        $pdo = db();
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);

        if (!$table || !$column) {
            return false;
        }

        $sql = "SHOW COLUMNS FROM `$table` LIKE " . $pdo->quote($column);
        $stmt = $pdo->query($sql);

        return (bool)$stmt->fetch();
    }

    private function logManualDeposit($message)
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents($this->debugLog, $line, FILE_APPEND);
    }

    public function webhookStrowallet()
    {
        $apiKey = get_setting('strowallet_api_key', '');
        if (!$apiKey) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['ok' => false]);
            exit;
        }
        $raw = file_get_contents('php://input');
        $sig = $_SERVER['HTTP_X_STROWALLET_SIGNATURE'] ?? '';
        $calc = hash_hmac('sha256', $raw, $apiKey);
        if (!$sig || !hash_equals($calc, $sig)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['ok' => false]);
            exit;
        }
        $data = json_decode($raw, true);
        $status = strtolower($data['status'] ?? ($data['data']['status'] ?? ''));
        if (!in_array($status, ['success', 'successful'], true)) {
            echo json_encode(['ok' => true]);
            exit;
        }
        $accountNumber = $data['account_number'] ?? ($data['data']['account_number'] ?? null);
        $amount = (float)($data['amount'] ?? ($data['data']['amount'] ?? 0));
        $reference = $data['reference'] ?? ($data['data']['reference'] ?? ('STROWALLET-' . time()));
        if (!$accountNumber || $amount <= 0) {
            echo json_encode(['ok' => false]);
            exit;
        }
        $user = db_fetch("SELECT id, email, name FROM users WHERE strowallet_account_number = :n LIMIT 1", ['n' => $accountNumber]);
        if (!$user) {
            echo json_encode(['ok' => false]);
            exit;
        }
        $existing = db_fetch("SELECT id FROM transactions WHERE reference = :ref", ['ref' => $reference]);
        if ($existing) {
            echo json_encode(['ok' => true]);
            exit;
        }
        db_execute("UPDATE users SET wallet_balance = wallet_balance + :amt WHERE id = :uid", ['amt' => $amount, 'uid' => $user['id']]);
        $this->transactionModel->create([
            'user_id' => $user['id'],
            'amount' => $amount,
            'type' => 'deposit',
            'gateway' => 'strowallet',
            'status' => 'completed',
            'reference' => $reference
        ]);
        try {
            $mailer = new Mailer();
            $mailer->send($user['email'], 'Deposit Completed', 'deposit_completed', [
                'name' => $user['name'],
                'amount_fmt' => format_currency($amount),
                'reference' => $reference
            ]);
        } catch (Throwable $e) {
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }

    public function webhookFlutterwave()
    {
        $secret = get_setting('flutterwave_webhook_secret', '');
        if (!$secret) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['ok' => false]);
            exit;
        }
        $hash = $_SERVER['HTTP_VERIF_HASH'] ?? ($_SERVER['HTTP_X_VERIF_HASH'] ?? '');
        if (!$hash || !hash_equals($secret, $hash)) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['ok' => false]);
            exit;
        }
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        $status = strtolower($payload['status'] ?? ($payload['data']['status'] ?? ''));
        if (!in_array($status, ['successful', 'success', 'completed'], true)) {
            echo json_encode(['ok' => true]);
            exit;
        }
        $amount = (float)($payload['data']['amount'] ?? 0);
        $currency = strtoupper($payload['data']['currency'] ?? CURRENCY);
        if ($currency !== CURRENCY || $amount <= 0) {
            echo json_encode(['ok' => false]);
            exit;
        }
        $txRef = $payload['data']['tx_ref'] ?? '';
        $uid = 0;
        if (is_string($txRef) && preg_match('/^FLW-\d+-(\d+)$/', $txRef, $m)) {
            $uid = (int)$m[1];
        }
        $userRow = null;
        if ($uid > 0) {
            $userRow = db_fetch("SELECT id, email, name FROM users WHERE id = :id", ['id' => $uid]);
        }
        if (!$userRow) {
            $email = $payload['data']['customer']['email'] ?? '';
            if ($email) {
                $userRow = db_fetch("SELECT id, email, name FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
            }
        }
        if (!$userRow) {
            echo json_encode(['ok' => false]);
            exit;
        }
        $ref = $txRef ?: ('FLW-' . time() . '-' . $userRow['id']);
        $exists = db_fetch("SELECT id FROM transactions WHERE reference = :r", ['r' => $ref]);
        if ($exists) {
            echo json_encode(['ok' => true]);
            exit;
        }
        db_execute("UPDATE users SET wallet_balance = wallet_balance + :amt WHERE id = :uid", ['amt' => $amount, 'uid' => $userRow['id']]);
        $this->transactionModel->create([
            'user_id' => $userRow['id'],
            'amount' => $amount,
            'type' => 'deposit',
            'gateway' => 'flutterwave',
            'status' => 'completed',
            'reference' => $ref
        ]);
        try {
            $mailer = new Mailer();
            $mailer->send($userRow['email'], 'Deposit Completed', 'deposit_completed', [
                'name' => $userRow['name'],
                'amount_fmt' => format_currency($amount),
                'reference' => $ref
            ]);
        } catch (Throwable $e) {
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }
}
