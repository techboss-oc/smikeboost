<?php
/**
 * Register Page
 */
$seo = get_seo_tags('Sign Up', 'Create your SmikeBoost account and start growing', '');
?>

<div class="auth-container-inner">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Create Account</h1>
            <p>Join SmikeBoost today</p>
        </div>

        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-danger"><?php echo e($msg); ?></div>
        <?php elseif ($msg = flash('success')): ?>
            <div class="alert alert-success"><?php echo e($msg); ?></div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?php echo url('register'); ?>">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="fullname" name="name" placeholder="Your full name" required>
                </div>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <i class="fas fa-at"></i>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirm password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password_confirm', this)"></i>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="referral">Referral Username (Optional)</label>
                <div class="input-wrapper">
                    <i class="fas fa-user-plus"></i>
                    <input type="text" id="referral" name="referral" placeholder="Referrer username" value="<?php echo e($_GET['ref'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-agree">
                <label class="agree-label">
                    <input type="checkbox" name="agree" id="agree" required>
                    <span class="custom-checkbox">
                        <i class="fas fa-check"></i>
                    </span>
                    <span class="agree-text">I agree to the <a href="<?php echo url('terms'); ?>">Terms of Service</a> and <a href="<?php echo url('privacy-policy'); ?>">Privacy Policy</a></span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Create Account
            </button>

            <?php if (get_setting('google_auth_enabled')): ?>
            <div class="auth-divider">
                <span>OR</span>
            </div>

            <a href="<?php echo url('auth/google'); ?>" class="btn btn-outline btn-block google-btn">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" width="20">
                <span>Sign up with Gmail</span>
            </a>
            <?php endif; ?>

            <div class="auth-footer">
                <p>Already have an account? <a href="<?php echo url('login'); ?>">Sign in</a></p>
            </div>
        </form>
    </div>
</div>
