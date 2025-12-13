<?php
/**
 * Home Page - Modern Redesign
 */
$seo = get_seo_tags(
    "SmikeBoost | Nigeria's Fastest SMM Panel",
    'Launch viral Nigerian campaigns with instant SMM services, NGN wallet deposits, and 24/7 Lagos support.',
    'SMM Panel Nigeria, Buy Instagram Followers Lagos, TikTok boost Nigeria'
);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text fade-in-up">
                <span class="eyebrow">#1 SMM Panel in Nigeria</span>
                <h1>Supercharge Your <br><span class="text-gradient">Social Growth</span></h1>
                <p>The secret weapon for Nigerian creators and brands. Instant delivery, NGN payments, and 24/7 support from Lagos.</p>
                <div class="hero-buttons">
                    <a href="<?php echo url('register'); ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i> Get Started Free
                    </a>
                    <a href="<?php echo url('services'); ?>" class="btn btn-outline btn-lg">
                        <i class="fas fa-list"></i> View Services
                    </a>
                </div>
            </div>
            <div class="hero-visual fade-in-up delay-200">
                <!-- Login Card -->
                <div class="glass-card login-card-hero" style="padding: 2rem; max-width: 400px; margin: 0 auto;">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <h3 style="margin-bottom: 0.5rem;">Welcome Back</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Login to manage your campaigns</p>
                    </div>
                    
                    <form action="<?php echo url('login'); ?>" method="POST">
                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-user"></i>
                                <input type="text" name="username" placeholder="Username or Email" required class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" placeholder="Password" required class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block" style="width: 100%; margin-bottom: 1rem;">
                            Login
                        </button>
                    </form>

                    <?php if (get_setting('google_auth_enabled')): ?>
                    <div class="auth-divider" style="text-align: center; margin: 1rem 0; position: relative;">
                        <span style="background: var(--glass-bg); padding: 0 10px; color: var(--text-muted); font-size: 0.8rem; position: relative; z-index: 1;">OR</span>
                        <div style="position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: var(--glass-border);"></div>
                    </div>
                    <a href="<?php echo url('auth/google'); ?>" class="btn btn-outline btn-block" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="width: 20px;">
                        Login with Gmail
                    </a>
                    <?php endif; ?>

                    <div style="text-align: center; margin-top: 1rem; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);">New here?</span> 
                        <a href="<?php echo url('register'); ?>" class="text-gradient" style="font-weight: 600;">Create Account</a>
                    </div>
                </div>

                <!-- Floating Elements (Behind) -->
                <div class="glass-card hero-card" style="top: -20px; right: -40px; padding: 1rem; z-index: -1; animation-delay: 1s;">
                    <i class="fab fa-instagram" style="font-size: 2rem; color: #E1306C;"></i>
                </div>
                <div class="glass-card hero-card" style="bottom: -30px; left: -40px; padding: 1rem; z-index: -1; animation-delay: 2s;">
                    <i class="fab fa-tiktok" style="font-size: 2rem; color: #00f2ea;"></i>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Features Grid -->
<section class="section">
    <div class="container">
        <div class="text-center" style="text-align: center; margin-bottom: 4rem;">
            <span class="eyebrow">Why Choose Us</span>
            <h2>Built for <span class="text-gradient">Performance</span></h2>
        </div>
        <div class="grid grid-3">
            <div class="glass-card fade-in-up">
                <div class="service-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Instant Delivery</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">Automated systems ensure your orders start within seconds of payment.</p>
            </div>
            <div class="glass-card fade-in-up delay-100">
                <div class="service-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>NGN Payments</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">Pay seamlessly with Flutterwave, Paystack, or Bank Transfer in Naira.</p>
            </div>
            <div class="glass-card fade-in-up delay-200">
                <div class="service-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p style="color: var(--text-muted); margin-top: 1rem;">Our Lagos-based team is always online to help you succeed.</p>
            </div>
        </div>
    </div>
</section>

<!-- Platforms -->
<section class="section" style="background: rgba(0,0,0,0.2);">
    <div class="container">
        <div class="grid grid-2" style="align-items: center;">
            <div>
                <span class="eyebrow">Supported Platforms</span>
                <h2>Grow on Every <br><span class="text-gradient">Major Network</span></h2>
                <p style="color: var(--text-muted); margin: 1.5rem 0 2rem;">From Instagram Reels to YouTube Shorts, we have the services you need to go viral.</p>
                <a href="<?php echo url('services'); ?>" class="btn btn-primary">Explore All Services</a>
            </div>
            <div class="grid grid-2" style="gap: 1rem;">
                <div class="glass-card" style="text-align: center; padding: 1.5rem;">
                    <i class="fab fa-instagram" style="font-size: 2.5rem; color: #E1306C; margin-bottom: 1rem;"></i>
                    <h4>Instagram</h4>
                </div>
                <div class="glass-card" style="text-align: center; padding: 1.5rem;">
                    <i class="fab fa-tiktok" style="font-size: 2.5rem; color: #00f2ea; margin-bottom: 1rem;"></i>
                    <h4>TikTok</h4>
                </div>
                <div class="glass-card" style="text-align: center; padding: 1.5rem;">
                    <i class="fab fa-youtube" style="font-size: 2.5rem; color: #FF0000; margin-bottom: 1rem;"></i>
                    <h4>YouTube</h4>
                </div>
                <div class="glass-card" style="text-align: center; padding: 1.5rem;">
                    <i class="fab fa-twitter" style="font-size: 2.5rem; color: #1DA1F2; margin-bottom: 1rem;"></i>
                    <h4>Twitter</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section">
    <div class="container">
        <div class="glass-card" style="text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, rgba(168, 85, 247, 0.1), rgba(236, 72, 153, 0.1));">
            <h2>Ready to <span class="text-gradient">Blow Up?</span></h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 1.5rem auto;">Join thousands of Nigerian creators who trust SmikeBoost for their social growth.</p>
            <a href="<?php echo url('register'); ?>" class="btn btn-primary btn-lg">Create Free Account</a>
        </div>
    </div>
</section>
