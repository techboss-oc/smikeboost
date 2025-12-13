<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($seo['description'] ?? SITE_DESCRIPTION); ?>">
    <meta name="robots" content="noindex, nofollow">
    
    <title><?php echo e($seo['title'] ?? seo_title('Auth')); ?></title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/auth.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">
</head>
<body class="auth-body">
    <!-- Navigation -->
    <?php include APP_PATH . '/views/components/navbar.php'; ?>

    <!-- Auth Content -->
    <main class="auth-container">
        <?php include $page_view; ?>
    </main>

    <!-- Scripts -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
</body>
</html>
