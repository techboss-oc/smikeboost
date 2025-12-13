<?php
/**
 * SmikeBoost - Main Router
 * This file handles all page routing and displays appropriate content
 */


// Load configuration
require_once dirname(__DIR__) . '/app/config/config.php';
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/OrderController.php';
require_once APP_PATH . '/controllers/ApiController.php';
require_once APP_PATH . '/controllers/PaymentController.php';
require_once APP_PATH . '/controllers/ProfileController.php';

// Profile controller instance
$profileController = new ProfileController();

$authController = new AuthController();
$orderController = new OrderController();
$apiController = new ApiController();
$paymentController = new PaymentController();

// Seed demo user automatically for testing
if (defined('ENABLE_DEMO_USER') && ENABLE_DEMO_USER) {
    ensure_demo_user();
}

// Map pages to their respective files and layouts
$pageMap = [
    // Public pages
    'home' => ['view' => 'home.php', 'layout' => 'main.php', 'seo' => true],
    'services' => ['view' => 'services.php', 'layout' => 'main.php', 'seo' => true],
    'about' => ['view' => 'about.php', 'layout' => 'main.php', 'seo' => true],
    'contact' => ['view' => 'contact.php', 'layout' => 'main.php', 'seo' => true],
    'how-it-works' => ['view' => 'how-it-works.php', 'layout' => 'main.php', 'seo' => true],
    'how-to-earn' => ['view' => 'how-to-earn.php', 'layout' => 'main.php', 'seo' => true],
    'blog' => ['view' => 'blog.php', 'layout' => 'main.php', 'seo' => true],
    'faq' => ['view' => 'faq.php', 'layout' => 'main.php', 'seo' => true],
    'help-center' => ['view' => 'help-center.php', 'layout' => 'main.php', 'seo' => true],
    'status' => ['view' => 'status.php', 'layout' => 'main.php', 'seo' => true],
    'api-docs' => ['view' => 'api-docs.php', 'layout' => 'main.php', 'seo' => true],
    'privacy-policy' => ['view' => 'privacy-policy.php', 'layout' => 'main.php', 'seo' => true],
    'terms' => ['view' => 'terms.php', 'layout' => 'main.php', 'seo' => true],
    'cookie-policy' => ['view' => 'cookie-policy.php', 'layout' => 'main.php', 'seo' => true],
    'disclaimer' => ['view' => 'disclaimer.php', 'layout' => 'main.php', 'seo' => true],

    // Auth pages
    'login' => ['view' => 'login.php', 'layout' => 'auth.php', 'seo' => false],
    'register' => ['view' => 'register.php', 'layout' => 'auth.php', 'seo' => false],
    'forgot-password' => ['view' => 'forgot-password.php', 'layout' => 'auth.php', 'seo' => false],

    // Dashboard pages
    'dashboard' => ['view' => 'dashboard-home.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/new-order' => ['view' => 'dashboard-new-order.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/orders' => ['view' => 'dashboard-orders.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/add-funds' => ['view' => 'dashboard-add-funds.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/profile' => ['view' => 'dashboard-profile.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/support' => ['view' => 'dashboard-support.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/mass-order' => ['view' => 'dashboard-mass-order.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/child-panel' => ['view' => 'dashboard-child-panel.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
    'dashboard/referrals' => ['view' => 'dashboard-referrals.php', 'layout' => 'dashboard.php', 'protected' => true, 'seo' => false],
];

/**
 * Build XML sitemap markup from the page map
 */
function build_sitemap_xml(array $pageMap) {
    $urls = [];
    $now = date('c');


    foreach ($pageMap as $slug => $config) {
        $loc = ($slug === 'home') ? url() : url($slug);
        $viewPath = VIEWS_PATH . '/pages/' . ($config['view'] ?? '');
        $lastmod = file_exists($viewPath) ? date('c', filemtime($viewPath)) : $now;
        $priority = !empty($config['protected']) ? '0.35' : (($slug === 'home') ? '1.0' : '0.80');
        $changefreq = !empty($config['protected']) ? 'weekly' : 'daily';

        $urls[$loc] = [
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($urls as $loc => $meta) {
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
        $xml .= "    <lastmod>{$meta['lastmod']}</lastmod>\n";
        $xml .= "    <changefreq>{$meta['changefreq']}</changefreq>\n";
        $xml .= "    <priority>{$meta['priority']}</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= "</urlset>";

    return $xml;
}

/**
 * Load blog posts from shared content file
 */
function get_blog_posts() {
    static $posts = null;
    if ($posts === null) {
        $posts = require APP_PATH . '/content/blog_posts.php';
        usort($posts, function ($a, $b) {
            return strtotime($b['published_at']) <=> strtotime($a['published_at']);
        });
    }
    return $posts;
}

/**
 * Build RSS 2.0 feed for blog content
 */
function build_rss_feed() {
    $posts = array_slice(get_blog_posts(), 0, 15);
    $siteUrl = url('blog');
    $rss = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $rss .= "<rss version=\"2.0\">\n";
    $rss .= "  <channel>\n";
    $rss .= "    <title>SmikeBoost Blog</title>\n";
    $rss .= "    <link>" . htmlspecialchars($siteUrl, ENT_XML1, 'UTF-8') . "</link>\n";
    $rss .= "    <description>Nigeria-focused SMM growth playbooks from SmikeBoost.</description>\n";
    $rss .= "    <language>en-NG</language>\n";
    $rss .= "    <lastBuildDate>" . date(DATE_RSS) . "</lastBuildDate>\n";

    foreach ($posts as $post) {
        $postUrl = url('blog') . '#post-' . $post['slug'];
        $rss .= "    <item>\n";
        $rss .= "      <title>" . htmlspecialchars($post['title'], ENT_XML1, 'UTF-8') . "</title>\n";
        $rss .= "      <link>" . htmlspecialchars($postUrl, ENT_XML1, 'UTF-8') . "</link>\n";
        $rss .= "      <guid isPermaLink=\"true\">" . htmlspecialchars($postUrl, ENT_XML1, 'UTF-8') . "</guid>\n";
        $rss .= "      <pubDate>" . date(DATE_RSS, strtotime($post['published_at'])) . "</pubDate>\n";
        $rss .= "      <description>" . htmlspecialchars($post['excerpt'], ENT_XML1, 'UTF-8') . "</description>\n";
        $rss .= "    </item>\n";
    }

    $rss .= "  </channel>\n";
    $rss .= "</rss>";

    return $rss;
}

// Derive page from path or query (?page=...)
$basePath = trim(parse_url(SITE_URL, PHP_URL_PATH), '/'); // e.g., boost/public
$uriPath  = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Strip base path from URI (PHP 7 compatible, no str_starts_with)
if ($basePath && strpos($uriPath, $basePath) === 0) {
    $uriPath = ltrim(substr($uriPath, strlen($basePath)), '/');
}

// Serve sitemap
if ($uriPath === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo build_sitemap_xml($pageMap);
    exit;
}

// Serve RSS feed
if ($uriPath === 'feed.xml' || $uriPath === 'rss.xml') {
    header('Content-Type: application/rss+xml; charset=utf-8');
    echo build_rss_feed();
    exit;
}

// Prefer explicit ?page= query
$page = isset($_GET['page']) ? sanitize($_GET['page']) : ($uriPath ?: 'home');

// Validate page name (allow slashes for nested dashboard paths)
$page = preg_replace('/[^a-z0-9\-\/]/', '', $page);

// Normalize: remove trailing slash
$page = trim($page, '/');

// Make current page available for helpers (active states)
$_GET['page'] = $page;

// API endpoints (JSON only)
if (strpos($page, 'api/') === 0) {
    $apiController->handle($page);
    exit;
}

// Special actions / POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($page === 'login') {
        $authController->handleLogin();
    } elseif ($page === 'register') {
        $authController->handleRegister();
    } elseif ($page === 'dashboard/new-order') {
        $orderController->handleCreateOrder();
    } elseif ($page === 'dashboard/mass-order') {
        $orderController->handleMassOrder();
    } elseif ($page === 'dashboard/child-panel') {
        $orderController->handleChildPanel();
    } elseif ($page === 'dashboard/add-funds/bank') {
        $paymentController->handleBankTransfer();
    } elseif ($page === 'dashboard/add-funds/crypto') {
        $paymentController->handleCryptoTransfer();
    }
    elseif ($page === 'dashboard/profile/update') {
        $profileController->updateInfo();
    }
    elseif ($page === 'dashboard/profile/password') {
        $profileController->changePassword();
    }
    elseif ($page === 'dashboard/profile/regenerate-key') {
        $profileController->regenerateApiKey();
    }
    elseif ($page === 'dashboard/profile/avatar') {
        $profileController->updateAvatar();
    }
}

// Payment verification (handles both Flutterwave and Paystack)
if ($page === 'dashboard/add-funds/verify') {
    $paymentController->verifyPayment();
}

if ($page === 'logout') {
    session_destroy();
    redirect('home');
}

// Handle Blog Single Post
if (strpos($page, 'blog/') === 0) {
    $slug = substr($page, 5);
    require_once APP_PATH . '/controllers/BlogController.php';
    $blogController = new BlogController();
    $postData = $blogController->show($slug);
    
    if ($postData) {
        $pageConfig = ['view' => 'blog-single.php', 'layout' => 'main.php', 'seo' => true];
        $post = $postData['post'];
        // Override SEO for single post
        $seo = [
            'title' => $post['title'] . ' - ' . SITE_NAME,
            'description' => $post['excerpt'] ?? substr(strip_tags($post['content']), 0, 160),
            'image' => !empty($post['image']) ? asset($post['image']) : asset('images/og-image.png'),
            'url' => url('blog/' . $post['slug'])
        ];
    } else {
        header('HTTP/1.0 404 Not Found');
        $page = '404';
        $pageConfig = ['view' => '404.php', 'layout' => 'main.php', 'seo' => false];
    }
}
// Check if page exists
elseif (!isset($pageMap[$page])) {
    header('HTTP/1.0 404 Not Found');
    $page = '404';
    $pageConfig = ['view' => '404.php', 'layout' => 'main.php', 'seo' => false];
} else {
    $pageConfig = $pageMap[$page];
}

// Check if page is protected (requires login)
if (!empty($pageConfig['protected']) && !is_logged_in()) {
    redirect('login');
}

// Prepare page view path
$page_view = VIEWS_PATH . '/pages/' . $pageConfig['view'];

// Check if page file exists
if (!file_exists($page_view)) {
    header('HTTP/1.0 404 Not Found');
    $page_view = VIEWS_PATH . '/pages/404.php';
}

// Prepare SEO tags if applicable
$seo = [];
if (!empty($pageConfig['seo'])) {
    // SEO is handled within each page view file
} else {
    $seo = ['title' => 'SmikeBoost', 'description' => SITE_DESCRIPTION, 'keywords' => SITE_KEYWORDS];
}

// Load the layout
$layout_file = VIEWS_PATH . '/layouts/' . $pageConfig['layout'];
if (file_exists($layout_file)) {
    include $layout_file;
} else {
    // Fallback - just include the page view
    include $page_view;
}
