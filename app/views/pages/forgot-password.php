<?php
/**
 * Forgot Password Page
 */
$seo = get_seo_tags('Forgot Password', 'Reset your SmikeBoost password', '');
?>

<div class="auth-container-inner">
    <div class="auth-card glass-card">
        <div class="auth-header">
            <h1>Forgot Password?</h1>
            <p>We'll help you recover your account</p>
        </div>

        <form class="auth-form" method="POST" action="<?php echo url('api/auth/forgot-password'); ?>">
            <div class="form-info">
                <p>Enter your email address and we'll send you instructions to reset your password.</p>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>

        <div class="auth-footer">
            <p>Remember your password? <a href="<?php echo url('login'); ?>">Sign in</a></p>
            <p>Don't have an account? <a href="<?php echo url('register'); ?>">Create one</a></p>
        </div>
    </div>

    <div class="auth-side">
        <div class="auth-illustration">
            <i class="fas fa-key"></i>
        </div>
        <h2>Secure Access</h2>
        <p>Your account security is our priority. We'll help you regain access quickly and safely.</p>
    </div>
</div>
