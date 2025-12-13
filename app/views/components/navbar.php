<?php
/**
 * Main Navigation Bar Component
 */

$ticker_news = [];
try {
    if (function_exists('db_fetch_all')) {
        $ticker_news = db_fetch_all("SELECT * FROM notifications WHERE type = 'ticker' AND is_active = 1 ORDER BY created_at DESC");
        $ann = db_fetch_all("SELECT title, content FROM announcements WHERE is_active = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY created_at DESC");
        foreach ($ann as $a) {
            $msg = trim((string)($a['title'] ?? ''));
            if (!$msg) {
                $msg = trim((string)($a['content'] ?? ''));
            }
            if ($msg) {
                $ticker_news[] = ['message' => $msg];
            }
        }
    }
} catch (Exception $e) {
}
?>
<?php if (get_setting('enable_ticker', '1') === '1' && !empty($ticker_news)): ?>
<div class="news-ticker-wrap">
    <div class="ticker-label">UPDATES</div>
    <div class="ticker-container">
        <div class="ticker-move">
            <?php foreach ($ticker_news as $news): ?>
                <span class="ticker-item">
                    <i class="fas fa-bullhorn text-accent"></i> <?php echo htmlspecialchars($news['message']); ?>
                </span>
            <?php endforeach; ?>
</div>
</div>
</div>
<?php endif; ?>
<nav class="navbar glass-nav">
    <div class="navbar-container">
        <!-- Logo -->
        <div class="navbar-logo">
            <a href="<?php echo url(); ?>" class="logo-link">
                <div class="logo-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <span class="logo-text">Smike<span class="text-gradient">Boost</span></span>
            </a>
        </div>

        <!-- Hamburger Menu -->
        <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Navigation Links -->
        <div class="nav-wrapper" id="navMenu">
            <ul class="nav-menu">
                <li class="nav-item <?php echo is_active('home'); ?>">
                    <a href="<?php echo url(); ?>" class="nav-link">Home</a>
                </li>
                <li class="nav-item <?php echo is_active('services'); ?>">
                    <a href="<?php echo url('services'); ?>" class="nav-link">Services</a>
                </li>
                <li class="nav-item <?php echo is_active('how-it-works'); ?>">
                    <a href="<?php echo url('how-it-works'); ?>" class="nav-link">How it Works</a>
                </li>
                <li class="nav-item <?php echo is_active('blog'); ?>">
                    <a href="<?php echo url('blog'); ?>" class="nav-link">Blog</a>
                </li>
                <li class="nav-item <?php echo is_active('faq'); ?>">
                    <a href="<?php echo url('faq'); ?>" class="nav-link">FAQ</a>
                </li>
                <li class="nav-item <?php echo is_active('api-docs'); ?>">
                    <a href="<?php echo url('api-docs'); ?>" class="nav-link">API</a>
                </li>
                <li class="nav-item <?php echo is_active('contact'); ?>">
                    <a href="<?php echo url('contact'); ?>" class="nav-link">Contact</a>
                </li>
            </ul>

            <!-- Mobile Auth Buttons (Visible inside menu on mobile) -->
            <div class="nav-auth-mobile">
                <?php if (is_logged_in()): ?>
                    <a href="<?php echo url('dashboard'); ?>" class="btn btn-primary btn-block">
                        <i class="fas fa-user"></i> Dashboard
                    </a>
                <?php else: ?>
                    <a href="<?php echo url('login'); ?>" class="btn btn-outline btn-block">Login</a>
                    <a href="<?php echo url('register'); ?>" class="btn btn-primary btn-block">Get Started</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Desktop Auth Buttons -->
        <div class="nav-auth-desktop">
            <?php if (is_logged_in()): ?>
                <a href="<?php echo url('dashboard'); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-user"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="<?php echo url('login'); ?>" class="nav-link">Login</a>
                <a href="<?php echo url('register'); ?>" class="btn btn-sm btn-primary">
                    Get Started
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
