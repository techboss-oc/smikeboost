<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Dashboard - SmikeBoost">
    <meta name="robots" content="noindex, nofollow">

    <title><?php echo e($seo['title'] ?? seo_title('Dashboard')); ?></title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/dashboard.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/svg+xml" href="<?php echo asset('images/favicon.svg'); ?>">
</head>

<body class="dashboard-body">
    <?php include VIEWS_PATH . '/components/ticker.php'; ?>
    <?php include VIEWS_PATH . '/components/dashboard-navbar.php'; ?>

    <style>
        .dashboard-body .dashboard-navbar {
            top: 40px;
        }

        .dashboard-body .dashboard-sidebar {
            top: 110px;
        }

        .dashboard-body .dashboard-content {
            margin-top: 110px;
        }

        /* Collapsible sidebar (desktop) */
        .dashboard-body .dashboard-sidebar.collapsed {
            width: 72px;
        }

        .dashboard-body .dashboard-content.collapsed {
            margin-left: 72px;
        }

        .dashboard-body .dashboard-sidebar.collapsed .sidebar-link {
            justify-content: center;
        }

        .dashboard-body .dashboard-sidebar.collapsed .sidebar-link span {
            display: none;
        }

        .dashboard-body .dashboard-sidebar.collapsed .sidebar-link i {
            width: auto;
        }
    </style>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include VIEWS_PATH . '/components/dashboard-sidebar.php'; ?>

        <!-- Sidebar Backdrop for mobile -->
        <div class="sidebar-backdrop"></div>

        <!-- Main Content -->
        <main class="dashboard-content">
            <?php include $page_view; ?>
        </main>
    </div>

    <!-- Scripts -->
    <script src="<?php echo asset('js/main.js'); ?>"></script>
    <script src="<?php echo asset('js/animations.js'); ?>"></script>
    <script>
        (function() {
            var sidebar = document.getElementById('dashboardSidebar');
            var content = document.querySelector('.dashboard-content');
            var toggleBtn = document.getElementById('sidebarToggle');
            var backdrop = document.querySelector('.sidebar-backdrop');
            if (!sidebar || !content || !toggleBtn || !backdrop) return;

            // Restore desktop collapse state
            try {
                var saved = localStorage.getItem('dashboard_sidebar_collapsed');
                if (saved === '1' && window.innerWidth > 1024) {
                    sidebar.classList.add('collapsed');
                    content.classList.add('collapsed');
                }
            } catch (e) {}

            function openMobileSidebar() {
                sidebar.classList.add('active');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeMobileSidebar() {
                sidebar.classList.remove('active');
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    if (sidebar.classList.contains('active')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                } else {
                    var isCollapsed = sidebar.classList.toggle('collapsed');
                    content.classList.toggle('collapsed', isCollapsed);
                    try {
                        localStorage.setItem('dashboard_sidebar_collapsed', isCollapsed ? '1' : '0');
                    } catch (e) {}
                }
            });

            // Backdrop click closes on mobile
            backdrop.addEventListener('click', function() {
                closeMobileSidebar();
            });

            // Escape key closes on mobile
            document.addEventListener('keydown', function(ev) {
                if (ev.key === 'Escape' && window.innerWidth <= 1024) {
                    closeMobileSidebar();
                }
            });

            // On resize, clean up mobile state and reapply desktop collapse
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    closeMobileSidebar();
                    try {
                        var saved = localStorage.getItem('dashboard_sidebar_collapsed');
                        var shouldCollapse = saved === '1';
                        sidebar.classList.toggle('collapsed', shouldCollapse);
                        content.classList.toggle('collapsed', shouldCollapse);
                    } catch (e) {}
                } else {
                    // Remove desktop collapse classes for mobile layout
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('collapsed');
                }
            });
        })();
    </script>
</body>

</html>