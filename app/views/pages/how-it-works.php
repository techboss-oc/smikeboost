<?php
/**
 * How It Works Page
 */
$seo = get_seo_tags(
    'How SmikeBoost Works | 4-Step Playbook for Nigerian Growth',
    'Follow the SmikeBoost StoryBrand plan: create your account, fund via NGN wallets, launch SMM orders, and analyze results with Lagos support.',
    'How SmikeBoost works, SMM panel Nigeria guide, Flutterwave SMM deposit, Launch Nigerian campaigns'
);
?>

<section class="how-it-works-page">
    <!-- Hero Section -->
    <div class="hiw-hero">
        <div class="hero-bg-effects">
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>
            <div class="hero-grid-overlay"></div>
            <div class="hero-glow"></div>
        </div>
        
        <div class="hero-content">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                <span>The Playbook</span>
                <i class="fas fa-arrow-right"></i>
            </div>
            
            <h1 class="hero-title">
                <span class="title-line">From Zero to</span>
                <span class="title-highlight">Viral</span>
                <span class="title-line">in 4 Steps</span>
            </h1>
            
            <p class="hero-subtitle">Stop guessing. Start growing. We've simplified the process of getting reliable social proof for Nigerian creators and businesses.</p>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-value">10K+</span>
                    <span class="stat-label">Active Users</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-value">₦1,500</span>
                    <span class="stat-label">Min. Deposit</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <span class="stat-value">24/7</span>
                    <span class="stat-label">Lagos Support</span>
                </div>
            </div>
            
            <div class="hero-actions">
                <a href="<?php echo url('register'); ?>" class="btn-hero-primary">
                    <span>Start Your Journey</span>
                    <i class="fas fa-rocket"></i>
                </a>
                <a href="#video-section" class="btn-hero-secondary">
                    <span class="play-icon"><i class="fas fa-play"></i></span>
                    <span>Watch Demo</span>
                </a>
            </div>
        </div>
        
        <div class="hero-visual">
            <div class="floating-cards">
                <div class="float-card float-card-1">
                    <i class="fas fa-user-plus"></i>
                    <span>Sign Up</span>
                </div>
                <div class="float-card float-card-2">
                    <i class="fas fa-wallet"></i>
                    <span>Fund</span>
                </div>
                <div class="float-card float-card-3">
                    <i class="fas fa-rocket"></i>
                    <span>Launch</span>
                </div>
                <div class="float-card float-card-4">
                    <i class="fas fa-chart-line"></i>
                    <span>Grow</span>
                </div>
            </div>
            <div class="center-circle">
                <div class="circle-ring circle-ring-1"></div>
                <div class="circle-ring circle-ring-2"></div>
                <div class="circle-ring circle-ring-3"></div>
                <div class="circle-core">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="section-container">
        <!-- Video Section -->
        <div class="video-section-modern" id="video-section">
            <div class="video-wrapper">
                <div class="video-glow"></div>
                <div class="video-frame">
                    <div class="video-thumbnail">
                        <div class="video-overlay">
                            <a href="https://www.youtube.com/watch?v=smikeboostdemo" target="_blank" class="video-play-btn">
                                <span class="play-ring"></span>
                                <i class="fas fa-play"></i>
                            </a>
                        </div>
                        <div class="video-info">
                            <span class="video-badge"><i class="fas fa-clock"></i> 90 sec</span>
                            <h3>See it in action</h3>
                            <p>Learn how to fund your wallet and launch your first campaign.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Steps Timeline -->
        <div class="timeline-container">
            <!-- Step 1 -->
            <div class="timeline-row">
                <div class="timeline-content glass-card">
                    <div class="step-number">01</div>
                    <h3>Create Account</h3>
                    <p>Sign up in seconds. No credit card required. Get instant access to our dashboard and browse our full catalog of services.</p>
                    <a href="<?php echo url('register'); ?>" class="btn-link">Get Started <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="timeline-visual">
                    <div class="visual-circle">
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="timeline-spacer"></div>
            </div>

            <!-- Step 2 -->
            <div class="timeline-row reverse">
                <div class="timeline-spacer"></div>
                <div class="timeline-visual">
                    <div class="visual-circle">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="timeline-content glass-card">
                    <div class="step-number">02</div>
                    <h3>Fund Wallet</h3>
                    <p>Deposit Naira instantly using Flutterwave, Paystack, or Bank Transfer. We lock your rate so you don't worry about FX fluctuations.</p>
                    <a href="<?php echo url('dashboard/add-funds'); ?>" class="btn-link">View Payment Methods <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="timeline-row">
                <div class="timeline-content glass-card">
                    <div class="step-number">03</div>
                    <h3>Launch Campaign</h3>
                    <p>Select your platform (Instagram, TikTok, etc.), paste your link, and choose your quantity. Our system handles the rest automatically.</p>
                    <a href="<?php echo url('services'); ?>" class="btn-link">Browse Services <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="timeline-visual">
                    <div class="visual-circle">
                        <i class="fas fa-rocket"></i>
                    </div>
                </div>
                <div class="timeline-spacer"></div>
            </div>

            <!-- Step 4 -->
            <div class="timeline-row reverse">
                <div class="timeline-spacer"></div>
                <div class="timeline-visual">
                    <div class="visual-circle">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="timeline-content glass-card">
                    <div class="step-number">04</div>
                    <h3>Track & Grow</h3>
                    <p>Monitor your order status in real-time. Watch your engagement grow and use our analytics to plan your next move.</p>
                    <a href="<?php echo url('dashboard'); ?>" class="btn-link">Go to Dashboard <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="cta-banner">
            <h2>Ready to boost your presence?</h2>
            <p>Join thousands of Nigerian creators and businesses today.</p>
            <div class="cta-buttons">
                <a href="<?php echo url('register'); ?>" class="btn btn-primary btn-lg">Create Free Account</a>
                <a href="<?php echo url('contact'); ?>" class="btn btn-outline btn-lg">Contact Sales</a>
            </div>
        </div>
    </div>
</section>

<section class="trust-proof-section">
    <div class="trust-wrapper">
        <div class="trust-header">
            <p class="eyebrow">Proof It Works</p>
            <h2>Why creators trust the SmikeBoost plan</h2>
            <p>Every workflow is engineered for Nigerian creators: secure banking rails, honest SLAs, and a Lagos support desk that actually picks up.</p>
        </div>

        <div class="trust-grid">
            <article class="trust-card">
                <div class="trust-icon"><i class="fas fa-receipt"></i></div>
                <h3>Instant Wallet Receipts</h3>
                <p>Flutterwave and Paystack top-ups clear in under 60 seconds and auto-generate signed invoices for your team.</p>
            </article>

            <article class="trust-card">
                <div class="trust-icon"><i class="fas fa-balance-scale"></i></div>
                <h3>Transparent SLAs</h3>
                <p>Every service spells out speed, refill rules, and geography before you press pay so expectations stay aligned.</p>
            </article>

            <article class="trust-card">
                <div class="trust-icon"><i class="fas fa-user-shield"></i></div>
                <h3>NDPR-Grade Privacy</h3>
                <p>Minimal data capture, encrypted NVMe storage, and internal access logs keep your campaigns compliant.</p>
            </article>

            <article class="trust-card">
                <div class="trust-icon"><i class="fas fa-satellite-dish"></i></div>
                <h3>Real Signals Only</h3>
                <p>We audit suppliers weekly to guarantee authentic engagement—no grey-market bots or mismatched regions.</p>
            </article>

            <article class="trust-card">
                <div class="trust-icon"><i class="fas fa-headset"></i></div>
                <h3>Founder-Led Support</h3>
                <p>WhatsApp and Telegram chats go straight to core operators who can fix issues, not outsourced responders.</p>
            </article>

            <article class="trust-card">
                <div class="trust-icon"><i class="fas fa-sync"></i></div>
                <h3>Refill & Refund Safety</h3>
                <p>Any drops trigger automatic refills or instant refunds so your spend never hangs in limbo.</p>
            </article>
        </div>

        <div class="trust-cta glass-card">
            <h3>Ready to follow the playbook?</h3>
            <p>Let SmikeBoost guide your next launch with predictable reach, NDPR-compliant workflows, and a dashboard that feels custom-built for Lagos operators.</p>
            <div class="trust-cta-meta">
                <span class="cta-badge">Launch from ₦1,500</span>
                <ul class="trust-cta-list">
                    <li><i class="fas fa-check"></i> Wallet funding in seconds</li>
                    <li><i class="fas fa-check"></i> Automated order tracking</li>
                    <li><i class="fas fa-check"></i> Creator-friendly invoices</li>
                </ul>
            </div>
            <a href="<?php echo url('register'); ?>" class="btn btn-primary btn-lg">
                Launch your first order for ₦1,500 <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<?php
$howToSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'HowTo',
    'name' => 'How SmikeBoost Works',
    'description' => 'Four-step plan for Nigerian creators to launch SMM campaigns with SmikeBoost.',
    'totalTime' => 'PT10M',
    'supply' => [
        ['@type' => 'HowToSupply', 'name' => 'SmikeBoost account'],
        ['@type' => 'HowToSupply', 'name' => 'NGN wallet deposit via Flutterwave/Paystack']
    ],
    'tool' => [
        ['@type' => 'HowToTool', 'name' => 'SmikeBoost dashboard']
    ],
    'step' => [
        [
            '@type' => 'HowToStep',
            'name' => 'Create account',
            'text' => 'Sign up for a SmikeBoost account to unlock the dashboard and Lagos support team.',
            'url' => url('register')
        ],
        [
            '@type' => 'HowToStep',
            'name' => 'Fund wallet',
            'text' => 'Deposit NGN via Flutterwave, Paystack, or bank transfer with instant receipts.',
            'url' => url('dashboard/add-funds')
        ],
        [
            '@type' => 'HowToStep',
            'name' => 'Launch orders',
            'text' => 'Choose services across Instagram, TikTok, YouTube, or Twitter with transparent SLAs.',
            'url' => url('services')
        ],
        [
            '@type' => 'HowToStep',
            'name' => 'Track & optimize',
            'text' => 'Monitor dashboards, trigger refills, and coordinate with Lagos strategists.',
            'url' => url('dashboard')
        ],
    ]
];
?>
<script type="application/ld+json">
    <?php echo json_encode($howToSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>

<style>
/* ============================================
   HOW IT WORKS PAGE - ORGANIZED STYLES
   1. Base & Container
   2. Hero Section
   3. Video Section  
   4. Timeline/Steps Section
   5. Trust/Proof Section
   6. Responsive Breakpoints
============================================ */

/* ============================================
   1. BASE & CONTAINER
============================================ */
.how-it-works-page {
    padding: 0;
    overflow-x: hidden;
}

.section-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ============================================
   2. HERO SECTION
============================================ */
.hiw-hero {
    position: relative;
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 3rem;
    padding: 100px 2rem 3rem;
    max-width: 1400px;
    margin: 0 auto;
}

/* Background Effects */
.hero-bg-effects {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.4;
}

.hero-orb-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(168, 85, 247, 0.4) 0%, transparent 70%);
    top: -150px;
    left: -150px;
    animation: orbFloat1 20s ease-in-out infinite;
}

.hero-orb-2 {
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(236, 72, 153, 0.3) 0%, transparent 70%);
    bottom: -100px;
    right: -100px;
    animation: orbFloat2 25s ease-in-out infinite;
}

.hero-orb-3 {
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
    top: 40%;
    left: 50%;
    animation: orbFloat3 15s ease-in-out infinite;
}

@keyframes orbFloat1 {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, 20px); }
}

@keyframes orbFloat2 {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(-40px, -30px); }
}

@keyframes orbFloat3 {
    0%, 100% { transform: translate(-50%, 0); }
    50% { transform: translate(-50%, 20px); }
}

.hero-grid-overlay {
    position: absolute;
    inset: 0;
    background-image: 
        linear-gradient(rgba(168, 85, 247, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(168, 85, 247, 0.02) 1px, transparent 1px);
    background-size: 50px 50px;
    mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%);
}

.hero-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    background: radial-gradient(ellipse at center, rgba(168, 85, 247, 0.06) 0%, transparent 50%);
}

/* Hero Content */
.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.9rem 0.4rem 0.5rem;
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.15) 0%, rgba(236, 72, 153, 0.1) 100%);
    border: 1px solid rgba(168, 85, 247, 0.3);
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--color-primary);
    margin-bottom: 1.25rem;
    animation: fadeInUp 0.6s ease-out;
}

.badge-dot {
    width: 6px;
    height: 6px;
    background: var(--color-primary);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.hero-badge i {
    font-size: 0.65rem;
    opacity: 0.6;
}

.hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 1.25rem;
    animation: fadeInUp 0.6s ease-out 0.1s backwards;
}

.title-line {
    display: block;
    color: #fff;
}

.title-highlight {
    display: inline-block;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
}

.title-highlight::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
    border-radius: 2px;
    opacity: 0.4;
}

.hero-subtitle {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.65);
    line-height: 1.7;
    max-width: 420px;
    margin-bottom: 1.5rem;
    animation: fadeInUp 0.6s ease-out 0.2s backwards;
}

/* Hero Stats */
.hero-stats {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
    animation: fadeInUp 0.6s ease-out 0.3s backwards;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 800;
    color: #fff;
}

.stat-label {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.5);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.stat-divider {
    width: 1px;
    height: 32px;
    background: linear-gradient(to bottom, transparent, rgba(168, 85, 247, 0.4), transparent);
}

/* Hero Actions */
.hero-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    animation: fadeInUp 0.6s ease-out 0.4s backwards;
}

.btn-hero-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
    border-radius: 10px;
    color: #fff;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(168, 85, 247, 0.35);
    transition: all 0.3s ease;
}

.btn-hero-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(168, 85, 247, 0.45);
}

.btn-hero-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.875rem 1.25rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-hero-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(168, 85, 247, 0.4);
}

.play-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    border-radius: 50%;
    font-size: 0.65rem;
}

/* Hero Visual */
.hero-visual {
    position: relative;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 1s ease-out 0.5s backwards;
}

.floating-cards {
    position: absolute;
    inset: 0;
}

.float-card {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    padding: 1rem 1.25rem;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    backdrop-filter: blur(16px);
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
}

.float-card i {
    font-size: 1.25rem;
    color: var(--color-primary);
}

.float-card-1 { top: 8%; left: 8%; animation: floatCard 6s ease-in-out infinite; }
.float-card-2 { top: 5%; right: 12%; animation: floatCard 6s ease-in-out 1.5s infinite; }
.float-card-3 { bottom: 12%; left: 5%; animation: floatCard 6s ease-in-out 3s infinite; }
.float-card-4 { bottom: 8%; right: 8%; animation: floatCard 6s ease-in-out 4.5s infinite; }

@keyframes floatCard {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Center Circle */
.center-circle {
    position: relative;
    width: 160px;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.circle-ring {
    position: absolute;
    border-radius: 50%;
    border: 1px solid rgba(168, 85, 247, 0.25);
}

.circle-ring-1 { width: 100%; height: 100%; animation: ringPulse 3s ease-out infinite; }
.circle-ring-2 { width: 140%; height: 140%; animation: ringPulse 3s ease-out 1s infinite; }
.circle-ring-3 { width: 180%; height: 180%; animation: ringPulse 3s ease-out 2s infinite; }

@keyframes ringPulse {
    0% { transform: scale(0.8); opacity: 0.8; }
    100% { transform: scale(1.2); opacity: 0; }
}

.circle-core {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fff;
    box-shadow: 0 0 50px rgba(168, 85, 247, 0.45);
    animation: corePulse 2s ease-in-out infinite;
}

@keyframes corePulse {
    0%, 100% { box-shadow: 0 0 50px rgba(168, 85, 247, 0.45); }
    50% { box-shadow: 0 0 70px rgba(168, 85, 247, 0.6); }
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* ============================================
   3. VIDEO SECTION
============================================ */
.video-section-modern {
    padding: 3rem 1rem;
    max-width: 800px;
    margin: 0 auto;
}

.video-wrapper {
    position: relative;
}

.video-glow {
    position: absolute;
    inset: -15px;
    background: radial-gradient(ellipse at center, rgba(168, 85, 247, 0.12) 0%, transparent 70%);
    filter: blur(30px);
    pointer-events: none;
}

.video-frame {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.35);
}

.video-frame::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.4), transparent);
}

.video-thumbnail {
    position: relative;
    min-height: 300px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.08) 0%, rgba(236, 72, 153, 0.04) 100%);
}

.video-overlay {
    margin-bottom: 1.5rem;
}

.video-play-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
    border-radius: 50%;
    color: #fff;
    font-size: 1.5rem;
    text-decoration: none;
    box-shadow: 0 6px 24px rgba(168, 85, 247, 0.45);
    transition: all 0.3s ease;
}

.video-play-btn i {
    margin-left: 3px;
}

.play-ring {
    position: absolute;
    inset: -12px;
    border: 2px solid rgba(168, 85, 247, 0.35);
    border-radius: 50%;
    animation: playRingPulse 2s ease-out infinite;
}

@keyframes playRingPulse {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(1.4); opacity: 0; }
}

.video-play-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 32px rgba(168, 85, 247, 0.55);
}

.video-info {
    text-align: center;
}

.video-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 999px;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 0.5rem;
}

.video-info h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.35rem;
}

.video-info p {
    color: rgba(255, 255, 255, 0.55);
    font-size: 0.875rem;
    max-width: 350px;
    margin: 0 auto;
}

/* ============================================
   4. TIMELINE/STEPS SECTION
============================================ */

.timeline-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 3rem 1rem;
}

.timeline-row {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
    align-items: center;
}

.timeline-row.reverse {
    direction: rtl;
}

.timeline-row.reverse > * {
    direction: ltr;
}

.timeline-content {
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px;
    backdrop-filter: blur(10px);
}

.step-number {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.75rem;
}

.timeline-content h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
}

.timeline-content p {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.btn-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--color-primary);
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: gap 0.3s ease;
}

.btn-link:hover {
    gap: 0.6rem;
}

.timeline-visual {
    display: flex;
    align-items: center;
    justify-content: center;
}

.visual-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.15) 0%, rgba(236, 72, 153, 0.1) 100%);
    border: 2px solid rgba(168, 85, 247, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    font-size: 1.25rem;
}

.timeline-spacer {
    min-width: 50px;
}

/* CTA Banner */
.cta-banner {
    text-align: center;
    padding: 3rem 1.5rem;
    margin: 2rem 0;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 20px;
}

.cta-banner h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
}

.cta-banner p {
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 1.5rem;
}

.cta-buttons {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* ============================================
   5. TRUST/PROOF SECTION
============================================ */
.trust-proof-section {
    position: relative;
    padding: 4rem 0;
    overflow: hidden;
    background: radial-gradient(circle at 50% 0%, rgba(168, 85, 247, 0.08), transparent 60%);
}

.trust-proof-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: 
        radial-gradient(circle at 20% 30%, rgba(168, 85, 247, 0.1), transparent 35%),
        radial-gradient(circle at 80% 70%, rgba(236, 72, 153, 0.08), transparent 35%);
    filter: blur(50px);
    pointer-events: none;
}

.trust-wrapper {
    position: relative;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1rem;
    z-index: 1;
}

.trust-header {
    text-align: center;
    margin-bottom: 3rem;
}

.trust-header .section-eyebrow,
.trust-header .eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 1rem;
    background: rgba(168, 85, 247, 0.1);
    border: 1px solid rgba(168, 85, 247, 0.2);
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #d8b4fe;
    margin-bottom: 1rem;
}

.trust-header h2 {
    margin: 0 0 1rem;
    font-size: clamp(1.5rem, 4vw, 2.5rem);
    font-weight: 800;
    line-height: 1.15;
    color: #fff;
}

.trust-header p {
    color: rgba(255, 255, 255, 0.6);
    font-size: clamp(0.9rem, 2.5vw, 1.1rem);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Trust Cards Grid */
.trust-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    margin-bottom: 3rem;
}

.trust-card {
    padding: 1.5rem;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.trust-card:hover {
    transform: translateY(-4px);
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(168, 85, 247, 0.25);
}

.trust-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(168, 85, 247, 0.1);
    color: var(--color-primary);
    font-size: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.trust-card:hover .trust-icon {
    background: rgba(168, 85, 247, 0.15);
    transform: scale(1.05);
}

.trust-card h3 {
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
}

.trust-card p {
    color: rgba(255, 255, 255, 0.55);
    line-height: 1.6;
    font-size: 0.875rem;
    margin: 0;
}

/* Trust CTA */
.trust-cta {
    padding: 2.5rem 1.5rem;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(236, 72, 153, 0.08) 100%);
    border: 1px solid rgba(168, 85, 247, 0.15);
    text-align: center;
}

.trust-cta::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(168, 85, 247, 0.4), transparent);
}

.trust-cta h3 {
    margin-bottom: 0.75rem;
    font-size: clamp(1.25rem, 3vw, 1.75rem);
    font-weight: 800;
    color: #fff;
}

.trust-cta > p {
    margin: 0 auto 1.5rem;
    max-width: 600px;
    color: rgba(255, 255, 255, 0.65);
    font-size: clamp(0.875rem, 2vw, 1rem);
    line-height: 1.6;
}

.trust-cta-meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.25rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.cta-badge {
    padding: 0.5rem 1rem;
    border-radius: 999px;
    background: rgba(168, 85, 247, 0.12);
    border: 1px solid rgba(168, 85, 247, 0.25);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-size: 0.8rem;
    font-weight: 700;
    color: #e9d5ff;
}

.trust-cta-list {
    display: flex;
    gap: 1rem;
    padding: 0;
    margin: 0;
    list-style: none;
    flex-wrap: wrap;
    justify-content: center;
}

.trust-cta-list li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    font-weight: 500;
}

.trust-cta-list li i {
    color: var(--color-primary);
    font-size: 0.7rem;
}

.trust-cta .btn-primary {
    padding: 0.875rem 1.75rem;
    font-size: 0.95rem;
    font-weight: 700;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
    color: white;
    border: none;
    box-shadow: 0 6px 20px rgba(168, 85, 247, 0.35);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.trust-cta .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(168, 85, 247, 0.45);
}

/* ============================================
   6. RESPONSIVE BREAKPOINTS
============================================ */

/* Tablet - 1024px */
@media (max-width: 1024px) {
    .hiw-hero {
        grid-template-columns: 1fr;
        min-height: auto;
        padding: 90px 1.5rem 2rem;
        gap: 2rem;
    }
    
    .hero-content {
        text-align: center;
        order: 1;
    }
    
    .hero-subtitle {
        margin-left: auto;
        margin-right: auto;
    }
    
    .hero-stats {
        justify-content: center;
    }
    
    .hero-actions {
        justify-content: center;
    }
    
    .hero-visual {
        height: 300px;
        order: 2;
    }
    
    .float-card-1 { left: 5%; top: 5%; }
    .float-card-2 { right: 5%; top: 0; }
    .float-card-3 { left: 0; bottom: 5%; }
    .float-card-4 { right: 0; bottom: 10%; }
    
    .trust-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .timeline-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .timeline-row.reverse {
        direction: ltr;
    }
    
    .timeline-visual {
        display: none;
    }
    
    .timeline-spacer {
        display: none;
    }
}

/* Mobile - 768px */
@media (max-width: 768px) {
    .hiw-hero {
        padding: 85px 1rem 1.5rem;
    }
    
    .hero-title {
        font-size: 1.75rem;
    }
    
    .hero-subtitle {
        font-size: 0.9rem;
    }
    
    .hero-stats {
        gap: 1rem;
    }
    
    .stat-value {
        font-size: 1.1rem;
    }
    
    .stat-label {
        font-size: 0.65rem;
    }
    
    .stat-divider {
        height: 28px;
    }
    
    .hero-visual {
        height: 250px;
    }
    
    .float-card {
        padding: 0.6rem 0.9rem;
        font-size: 0.65rem;
        border-radius: 10px;
    }
    
    .float-card i {
        font-size: 1rem;
    }
    
    .center-circle {
        width: 120px;
        height: 120px;
    }
    
    .circle-core {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .video-section-modern {
        padding: 2rem 1rem;
    }
    
    .video-thumbnail {
        min-height: 220px;
        padding: 1.5rem 1rem;
    }
    
    .video-play-btn {
        width: 64px;
        height: 64px;
        font-size: 1.25rem;
    }
    
    .video-info h3 {
        font-size: 1.1rem;
    }
    
    .video-info p {
        font-size: 0.8rem;
    }
    
    .trust-proof-section {
        padding: 3rem 0;
    }
    
    .trust-wrapper {
        padding: 0 1rem;
    }
    
    .trust-header {
        margin-bottom: 2rem;
    }
    
    .trust-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .trust-card {
        padding: 1.25rem;
    }
    
    .trust-icon {
        width: 42px;
        height: 42px;
        font-size: 1.1rem;
    }
    
    .trust-card h3 {
        font-size: 1rem;
    }
    
    .trust-card p {
        font-size: 0.8rem;
    }
    
    .trust-cta {
        padding: 2rem 1rem;
    }
    
    .trust-cta-meta {
        flex-direction: column;
        gap: 1rem;
    }
    
    .trust-cta-list {
        flex-direction: column;
        gap: 0.6rem;
        align-items: center;
    }
    
    .cta-banner {
        padding: 2rem 1rem;
    }
    
    .cta-banner h2 {
        font-size: 1.25rem;
    }
    
    .timeline-content {
        padding: 1.25rem;
    }
    
    .step-number {
        font-size: 1.5rem;
    }
    
    .timeline-content h3 {
        font-size: 1.1rem;
    }
    
    .timeline-content p {
        font-size: 0.85rem;
    }
}

/* Small Mobile - 480px */
@media (max-width: 480px) {
    .hiw-hero {
        padding: 80px 0.75rem 1rem;
    }
    
    .hero-badge {
        font-size: 0.7rem;
        padding: 0.35rem 0.7rem 0.35rem 0.4rem;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
    
    .hero-subtitle {
        font-size: 0.85rem;
        line-height: 1.6;
    }
    
    .hero-stats {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .stat-divider {
        display: none;
    }
    
    .hero-actions {
        flex-direction: column;
        width: 100%;
        gap: 0.6rem;
    }
    
    .btn-hero-primary,
    .btn-hero-secondary {
        width: 100%;
        justify-content: center;
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }
    
    .hero-visual {
        height: 200px;
    }
    
    .float-card {
        padding: 0.5rem 0.7rem;
        font-size: 0.6rem;
    }
    
    .float-card i {
        font-size: 0.9rem;
    }
    
    .float-card-1 { left: 2%; top: 8%; }
    .float-card-2 { right: 2%; top: 3%; }
    .float-card-3 { left: 0; bottom: 8%; }
    .float-card-4 { right: 0; bottom: 15%; }
    
    .center-circle {
        width: 90px;
        height: 90px;
    }
    
    .circle-core {
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
    }
    
    .video-frame {
        border-radius: 16px;
    }
    
    .video-thumbnail {
        min-height: 180px;
    }
    
    .video-play-btn {
        width: 56px;
        height: 56px;
        font-size: 1.1rem;
    }
    
    .play-ring {
        inset: -8px;
    }
    
    .trust-header h2 {
        font-size: 1.35rem;
    }
    
    .trust-header p {
        font-size: 0.85rem;
    }
    
    .trust-cta h3 {
        font-size: 1.15rem;
    }
    
    .trust-cta > p {
        font-size: 0.85rem;
    }
    
    .trust-cta .btn-primary {
        width: 100%;
        justify-content: center;
        padding: 0.75rem 1.25rem;
        font-size: 0.875rem;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
    
    .cta-buttons .btn {
        width: 100%;
    }
}
</style>

