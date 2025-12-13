<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($seo['description'] ?? SITE_DESCRIPTION); ?>">
    <meta name="keywords" content="<?php echo e($seo['keywords'] ?? SITE_KEYWORDS); ?>">
    <meta name="author" content="SmikeBoost">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo e($seo['url'] ?? url()); ?>">
    <meta property="og:title" content="<?php echo e($seo['title'] ?? seo_title('Home')); ?>">
    <meta property="og:description" content="<?php echo e($seo['description'] ?? SITE_DESCRIPTION); ?>">
    <meta property="og:image" content="<?php echo e($seo['image'] ?? asset('images/og-image.png')); ?>">
    <meta property="og:url" content="<?php echo e($seo['url'] ?? url()); ?>">
    <meta property="og:site_name" content="<?php echo e(SITE_NAME); ?>">
    <meta property="og:locale" content="en_NG">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($seo['title'] ?? seo_title('Home')); ?>">
    <meta name="twitter:description" content="<?php echo e($seo['description'] ?? SITE_DESCRIPTION); ?>">
    <meta name="twitter:image" content="<?php echo e($seo['image'] ?? asset('images/og-image.png')); ?>">
    
        <title><?php echo e($seo['title'] ?? seo_title('Home')); ?></title>

        <?php
        $structuredData = [
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => 'SmikeBoost SMM Panel',
                'description' => $seo['description'] ?? SITE_DESCRIPTION,
                'areaServed' => [
                        '@type' => 'Country',
                        'name' => 'Nigeria',
                ],
                'provider' => [
                        '@type' => 'Organization',
                        'name' => 'SmikeBoost',
                        'url' => $seo['url'] ?? url(),
                        'logo' => $seo['image'] ?? asset('images/og-image.png'),
                ],
                'offers' => [
                        '@type' => 'Offer',
                        'priceCurrency' => 'NGN',
                        'availability' => 'https://schema.org/InStock',
                ],
                'url' => $seo['url'] ?? url(),
        ];
        ?>
        <script type="application/ld+json">
                <?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
        </script>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">
        <link rel="alternate" type="application/rss+xml" title="SmikeBoost Blog RSS" href="<?php echo url('feed.xml'); ?>">
</head>
<body>
    <!-- Navigation -->
    <?php include VIEWS_PATH . '/components/navbar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php include $page_view; ?>
    </main>

    <!-- Footer -->
    <?php include VIEWS_PATH . '/components/footer.php'; ?>

    <!-- Scripts -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/animations.js'); ?>"></script>

    <?php
    // Fetch widget settings
    $widgets = [];
    try {
        if (function_exists('db_fetch_all')) {
            $settings_rows = db_fetch_all("SELECT * FROM settings WHERE setting_key IN ('whatsapp_number', 'tawk_to_id', 'enable_whatsapp', 'enable_tawk')");
            foreach ($settings_rows as $row) {
                $widgets[$row['setting_key']] = $row['setting_value'];
            }
        }
    } catch (Exception $e) {
        // Silent fail
    }
    ?>
    <!-- Floating Widgets -->
    <div class="floating-widgets">
        <?php if (!empty($widgets['enable_whatsapp']) && !empty($widgets['whatsapp_number'])): ?>
        <a href="https://wa.me/<?php echo htmlspecialchars($widgets['whatsapp_number']); ?>" target="_blank" class="floating-btn btn-whatsapp">
            <i class="fab fa-whatsapp"></i>
            <span class="widget-tooltip">Chat on WhatsApp</span>
        </a>
        <?php endif; ?>

        <?php if (!empty($widgets['enable_tawk']) && !empty($widgets['tawk_to_id'])): ?>
        <div class="floating-btn btn-tawk" onclick="if(typeof Tawk_API !== 'undefined'){ Tawk_API.toggle(); }">
            <i class="fas fa-comments"></i>
            <span class="widget-tooltip">Live Chat</span>
        </div>
        <!-- Tawk.to Script -->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        Tawk_API.onLoad = function(){
            Tawk_API.hideWidget();
        };
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/<?php echo htmlspecialchars($widgets['tawk_to_id']); ?>/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
