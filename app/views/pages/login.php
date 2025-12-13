<?php
/**
 * Login Page
 */
$seo = get_seo_tags('Login', 'Sign in to your SmikeBoost account', '');
?>

<div class="auth-container-inner">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Welcome Back</h1>
            <p>Sign in to continue to your dashboard</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-danger"><?php echo e($msg); ?></div>
        <?php elseif ($msg = flash('success')): ?>
            <div class="alert alert-success"><?php echo e($msg); ?></div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?php echo url('login'); ?>">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter your username or email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                </div>
            </div>

            <div class="form-remember">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="<?php echo url('forgot-password'); ?>" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>

            <?php if (get_setting('google_auth_enabled')): ?>
            <div class="auth-divider">
                <span>OR</span>
            </div>

            <a href="<?php echo url('auth/google'); ?>" class="btn btn-outline btn-block google-btn">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20">
                <span>Login with Gmail</span>
            </a>
            <?php endif; ?>

            <div class="auth-footer">
                <p>Don't have an account? <a href="<?php echo url('register'); ?>">Sign up</a></p>
            </div>
        </form>
    </div>
    
    <!-- Mobile Stats - Only visible on mobile -->
    <div class="auth-mobile-stats">
        <div class="mobile-stat">
            <span class="mobile-stat-value">120k+</span>
            <span class="mobile-stat-label">Orders</span>
        </div>
        <div class="mobile-stat">
            <span class="mobile-stat-value">50k+</span>
            <span class="mobile-stat-label">Users</span>
        </div>
        <div class="mobile-stat">
            <span class="mobile-stat-value">4.9/5</span>
            <span class="mobile-stat-label">Rating</span>
        </div>
    </div>
</div>
