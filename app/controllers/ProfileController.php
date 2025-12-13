<?php
require_once APP_PATH . '/models/User.php';

class ProfileController
{
    public function updateInfo()
    {
        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in first.');
            redirect('dashboard/profile');
        }
        $userId = $user['id'];
        $name = sanitize($_POST['name'] ?? '');
        $username = sanitize($_POST['username'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        if (!$name || !$username || !$email) {
            flash('error', 'Name, username, and email are required.');
            redirect('dashboard/profile');
        }
        db_execute("UPDATE users SET name = :name, username = :username, email = :email WHERE id = :id", [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'id' => $userId
        ]);
        flash('success', 'Profile updated successfully.');
        redirect('dashboard/profile');
    }

    public function changePassword()
    {
        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in first.');
            redirect('dashboard/profile');
        }
        $userId = $user['id'];
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $userRow = db_fetch("SELECT password_hash FROM users WHERE id = :id", ['id' => $userId]);
        if (!$userRow || !verify_password($current, $userRow['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            redirect('dashboard/profile');
        }
        if (!$new || strlen($new) < 6) {
            flash('error', 'New password must be at least 6 characters.');
            redirect('dashboard/profile');
        }
        if ($new !== $confirm) {
            flash('error', 'Passwords do not match.');
            redirect('dashboard/profile');
        }
        db_execute("UPDATE users SET password_hash = :ph WHERE id = :id", [
            'ph' => hash_password($new),
            'id' => $userId
        ]);
        flash('success', 'Password changed successfully.');
        redirect('dashboard/profile');
    }

    public function regenerateApiKey()
    {
        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in first.');
            redirect('dashboard/profile');
        }
        $userId = $user['id'];
        $newToken = generate_token(40);
        db_execute("UPDATE users SET api_token = :t WHERE id = :id", ['t' => $newToken, 'id' => $userId]);
        flash('success', 'API key regenerated.');
        redirect('dashboard/profile');
    }

    public function updateAvatar()
    {
        $user = current_user();
        if (!$user) {
            flash('error', 'Please log in first.');
            redirect('dashboard/profile');
        }
        $userId = $user['id'];

        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'No file uploaded or upload error.');
            redirect('dashboard/profile');
        }

        $file = $_FILES['avatar'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            flash('error', 'Invalid file type. Only JPG, PNG, GIF allowed.');
            redirect('dashboard/profile');
        }

        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            flash('error', 'File too large. Max 2MB.');
            redirect('dashboard/profile');
        }

        $uploadDir = PUBLIC_PATH . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update user avatar path
            try {
                db_execute("UPDATE users SET avatar = :avatar WHERE id = :id", ['avatar' => $filename, 'id' => $userId]);
                flash('success', 'Avatar updated successfully.');
            } catch (Exception $e) {
                flash('error', 'Avatar uploaded but database update failed. Please contact support.');
            }
        } else {
            flash('error', 'Failed to save avatar.');
        }
        redirect('dashboard/profile');
    }
}
