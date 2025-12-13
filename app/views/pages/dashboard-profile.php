<?php
/**
 * Dashboard Profile Page
 */
$seo = get_seo_tags('Profile', 'Manage your SmikeBoost account profile', '');
$userId = $_SESSION['user_id'] ?? null;
$userData = null;
if ($userId) {
    try {
        $userData = db_fetch("SELECT name, username, email, api_token, wallet_balance, avatar FROM users WHERE id = :id", ['id' => $userId]);
    } catch (Exception $e) {
        $userData = db_fetch("SELECT name, username, email, api_token, wallet_balance FROM users WHERE id = :id", ['id' => $userId]);
        $userData['avatar'] = null;
    }
}
$apiToken = $userData['api_token'] ?? 'Generate from account';

// Calculate total spent
$totalSpent = 0;
if ($userId) {
    try {
        $spent = db_fetch("SELECT SUM(amount) as total FROM orders WHERE user_id = :user_id AND status = 'completed'", ['user_id' => $userId]);
        $totalSpent = (float)($spent['total'] ?? 0);
    } catch (Exception $e) {
        $totalSpent = 0;
    }
}
?>

<section class="profile-page">
    <div class="page-header">
        <h1>Your Profile</h1>
        <p>Manage your account settings</p>
    </div>

    <div class="profile-container grid-2" style="grid-template-columns: 1fr 2fr; max-width: 1200px; margin: 0 auto;">
        <!-- Profile Sidebar -->
        <div class="profile-sidebar">
            <div class="glass-card" style="text-align: center;">
                <div class="profile-avatar-large">
                    <?php if (!empty($userData['avatar'])): ?>
                        <img src="<?php echo SITE_URL . '/uploads/avatars/' . $userData['avatar']; ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <h2 class="mb-sm"><?php echo e($userData['name'] ?? 'User'); ?></h2>
                <p class="text-tertiary mb-lg">Member</p>
                
                <div class="mb-lg" style="background: rgba(168, 85, 247, 0.1); padding: var(--spacing-md); border-radius: var(--radius-md);">
                    <p class="text-tertiary mb-sm" style="font-size: 0.875rem;">Total Spent</p>
                    <p class="text-primary" style="margin: 0; font-size: 1.5rem; font-weight: 700;"><?php echo format_currency($totalSpent); ?></p>
                </div>
                
                <!-- Avatar change not implemented in backend yet -->
                <form method="POST" action="<?php echo url('dashboard/profile/avatar'); ?>" enctype="multipart/form-data">
                    <input type="file" name="avatar" accept="image/*" style="display:none" id="avatarInput" onchange="this.form.submit()">
                    <a href="#" class="btn btn-outline btn-block" onclick="document.getElementById('avatarInput').click(); return false;">Change Avatar</a>
                </form>
            </div>
        </div>

        <!-- Profile Forms -->
        <div class="profile-forms d-flex" style="flex-direction: column; gap: var(--spacing-lg);">
            <!-- Personal Information -->
            <div class="glass-card">
                <h3 class="mb-lg">Personal Information</h3>
                <form method="POST" action="<?php echo url('dashboard/profile/update'); ?>">
                    <div class="grid-2 mb-lg">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Full Name</label>
                            <input type="text" class="form-control" name="name" value="<?php echo e($userData['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" value="<?php echo e($userData['username'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?php echo e($userData['email'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Information</button>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="glass-card">
                <h3 class="mb-lg">Security Settings</h3>
                <form method="POST" action="<?php echo url('dashboard/profile/password'); ?>">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="grid-2 mb-lg">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="new_password">
                        </div>
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>

            <!-- API Key -->
            <div class="glass-card">
                <h3 class="mb-lg">API Key</h3>
                <p class="text-secondary mb-lg">Use your API key to integrate SmikeBoost services into your platform.</p>
                
                <div class="d-flex align-center gap-md mb-lg" style="background: rgba(0, 0, 0, 0.3); padding: var(--spacing-md); border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                    <input type="password" id="apiTokenInput" value="<?php echo e($apiToken); ?>" readonly style="background: transparent; border: none; color: var(--text-primary); font-family: monospace; width: 100%; outline: none;">
                    <button type="button" class="btn btn-outline" style="padding: 0.5rem;" onclick="toggleApiKey()">
                        <i class="fas fa-eye" id="apiEyeIcon"></i>
                    </button>
                </div>

                <div class="d-flex gap-md">
                    <button type="button" class="btn btn-primary" onclick="copyApiKey()">
                        <i class="fas fa-copy"></i> Copy API Key
                    </button>
                    <form method="POST" action="<?php echo url('dashboard/profile/regenerate-key'); ?>" style="display:inline;">
                        <button type="submit" class="btn btn-outline">
                            <i class="fas fa-refresh"></i> Regenerate
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function copyApiKey() {
    const token = document.getElementById('apiTokenInput').value;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(token).then(() => {
            alert('API Key copied to clipboard!');
        }).catch(() => {
            fallbackCopy(token);
        });
    } else {
        fallbackCopy(token);
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        alert('API Key copied to clipboard!');
    } catch (err) {
        alert('Failed to copy API Key. Please copy manually.');
    }
    document.body.removeChild(textArea);
}

function regenerateApiKey() {
    if(confirm('Are you sure? This will invalidate your current key.')) {
        // Call backend to regenerate
        alert('Feature coming soon');
    }
}
</script>
