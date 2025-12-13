<?php
/**
 * Admin-specific configuration
 */

// Admin auth (change in production)
define('ADMIN_EMAIL', 'admin@smikeboost.com');
define('ADMIN_USERNAME', 'admin');
// Password: admin123 (match seeded DB user)
define('ADMIN_PASSWORD_HASH', '$2y$10$h2Yp7DpCGN/KrPN8zC1lEOfYwqE1VU./uZrhCnlqSSaC8DZUHTvHG');

define('ADMIN_SESSION_KEY', 'smikeboost_admin');
define('ADMIN_CSRF_KEY', 'smikeboost_admin_csrf');

define('ADMIN_ITEMS_PER_PAGE', 25);

define('ADMIN_THEME', 'dark-glass');

define('ADMIN_ALLOWED_IPS', []); // Add IP strings to restrict admin access

define('ADMIN_RATE_LIMIT_PER_MINUTE', 60);
