<?php
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/libraries/Mailer.php';

class AuthController
{
    public function handleLogin()
    {
        $emailOrUsername = sanitize($_POST['email'] ?? ($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (!$emailOrUsername || !$password) {
            flash('error', 'Email/Username and password are required.');
            redirect('login');
        }

        $userModel = new User();
        $user = db_fetch(
            "SELECT * FROM users WHERE email = :email OR username = :username LIMIT 1",
            ['email' => $emailOrUsername, 'username' => $emailOrUsername]
        );
        if (!$user || !verify_password($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials.');
            redirect('login');
        }

        if (($user['status'] ?? 'active') !== 'active') {
            flash('error', 'Your account is suspended. Contact support.');
            redirect('login');
        }

        // Ensure user has an API token
        if (empty($user['api_token'])) {
            $newToken = generate_token(40);
            db_execute("UPDATE users SET api_token = :t WHERE id = :id", ['t' => $newToken, 'id' => $user['id']]);
            $user['api_token'] = $newToken;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'] ?? '',
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
        ];

        flash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('dashboard');
    }

    public function handleRegister()
    {
        $name = sanitize($_POST['name'] ?? ($_POST['fullname'] ?? ''));
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? ($_POST['confirm_password'] ?? '');

        if (!$name || !$username || !$email || !$password || !$confirm) {
            flash('error', 'All fields are required.');
            redirect('register');
        }
        if (!validate_email($email)) {
            flash('error', 'Invalid email address.');
            redirect('register');
        }
        if (!validate_username($username)) {
            flash('error', 'Username must be 3-32 characters (letters, numbers, dot, dash, underscore).');
            redirect('register');
        }
        if ($password !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirect('register');
        }
        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            redirect('register');
        }

        $userModel = new User();
        if ($userModel->findByUsername($username)) {
            flash('error', 'Username already in use.');
            redirect('register');
        }
        if ($userModel->findByEmail($email)) {
            flash('error', 'Email already in use.');
            redirect('register');
        }

        // Handle Referral
        $referrerId = null;
        $referralUser = sanitize($_POST['referral'] ?? '');
        if ($referralUser) {
            $refUser = $userModel->findByUsername($referralUser);
            if ($refUser) {
                $referrerId = $refUser['id'];
            }
        }

        $id = $userModel->create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password_hash' => hash_password($password),
            'role' => 'user',
            'api_token' => generate_token(40),
            'referrer_id' => $referrerId
        ]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user'] = [
            'id' => $id,
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'role' => 'user',
        ];

        try {
            $mailer = new Mailer();
            $mailer->send($email, 'Welcome to SmikeBoost', 'welcome_registration', [
                'name' => $name,
            ]);
        } catch (Throwable $e) {
            // swallow
        }

        flash('success', 'Account created. Welcome, ' . $name . '!');
        redirect('dashboard');
    }
}
