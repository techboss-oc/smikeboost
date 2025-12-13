-- SmikeBoost Unified Database Schema
-- Generated to include every feature currently implemented (users, orders, payments, referrals, widgets, blog, ticker, etc.)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    api_token VARCHAR(64) UNIQUE,
    avatar VARCHAR(255) NULL,
    role ENUM('user','admin','superadmin') DEFAULT 'user',
    wallet_balance DECIMAL(12,2) DEFAULT 0,
    affiliate_balance DECIMAL(12,2) DEFAULT 0,
    referrer_id INT NULL,
    status ENUM('active','suspended') DEFAULT 'active',
    strowallet_account_number VARCHAR(32) NULL,
    strowallet_bank_name VARCHAR(64) NULL,
    strowallet_account_name VARCHAR(128) NULL,
    strowallet_va_reference VARCHAR(64) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_referrer FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Service Providers
CREATE TABLE IF NOT EXISTS providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    api_key VARCHAR(255) NOT NULL,
    api_url VARCHAR(255) NOT NULL,
    auto_sync TINYINT(1) DEFAULT 0,
    balance DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Service Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT '',
    sort_order INT DEFAULT 0,
    status ENUM('enabled','disabled') DEFAULT 'enabled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NULL,
    api_service_id VARCHAR(100) DEFAULT NULL,
    platform VARCHAR(50) NOT NULL,
    category VARCHAR(80) NOT NULL,
    name VARCHAR(180) NOT NULL,
    description TEXT,
    rate_per_1000 DECIMAL(10,2) NOT NULL,
    min_qty INT NOT NULL,
    max_qty INT NOT NULL,
    status ENUM('enabled','disabled') DEFAULT 'enabled',
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    link VARCHAR(500) NOT NULL,
    quantity INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending','processing','completed','canceled') DEFAULT 'pending',
    provider_order_id VARCHAR(120) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Transactions (supports proof uploads)
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    type ENUM('credit','debit','deposit','withdrawal') NOT NULL,
    gateway VARCHAR(80) NOT NULL,
    status ENUM('pending','completed','failed') DEFAULT 'pending',
    reference VARCHAR(120) NOT NULL,
    proof_image VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Support Tickets
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    priority ENUM('low','normal','high') DEFAULT 'normal',
    message TEXT NOT NULL,
    status ENUM('open','closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Child Panel requests
CREATE TABLE IF NOT EXISTS child_panels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    domain VARCHAR(150) NOT NULL,
    admin_username VARCHAR(80) NOT NULL,
    admin_password VARCHAR(255) NOT NULL,
    price_per_month DECIMAL(10,2) NOT NULL,
    status ENUM('pending','active','suspended','canceled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Referral earnings log
CREATE TABLE IF NOT EXISTS referral_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referred_user_id INT NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    order_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (referrer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (referred_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Key/Value application settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Public notifications + ticker items
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(255) NULL,
    message TEXT NOT NULL,
    type VARCHAR(32) DEFAULT 'info',
    icon VARCHAR(32) NULL,
    color VARCHAR(32) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type VARCHAR(32) DEFAULT 'info',
    start_date DATE NULL,
    end_date DATE NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS auto_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trigger_event VARCHAR(64) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    sent_count INT DEFAULT 0,
    failed_count INT DEFAULT 0,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog posts
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    excerpt TEXT,
    image VARCHAR(255),
    author_id INT,
    status ENUM('draft','published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default admin (password: admin123) - change after import
INSERT INTO users (name, username, email, password_hash, role, wallet_balance, referrer_id, affiliate_balance)
VALUES ('Admin', 'admin', 'admin@smikeboost.com', '$2y$10$h2Yp7DpCGN/KrPN8zC1lEOfYwqE1VU./uZrhCnlqSSaC8DZUHTvHG', 'admin', 0, NULL, 0)
ON DUPLICATE KEY UPDATE email = VALUES(email);

-- Default application settings (idempotent)
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'SmikeBoost'),
('enable_whatsapp', '1'),
('whatsapp_number', '2348000000000'),
('enable_tawk', '1'),
('tawk_to_id', 'YOUR_TAWKTO_ID'),
('google_auth_enabled', '0'),
('google_client_id', ''),
('google_client_secret', ''),
('child_panel_price', '25000'),
('child_panel_currency', 'NGN'),
('referral_commission_rate', '5'),
('referral_min_payout', '5000'),
('contact_email', 'support@smikeboost.com'),
('contact_phone', '+2340000000000'),
('contact_whatsapp', '2348000000000'),
('contact_telegram', 'SmikeBoost'),
('contact_address', 'Lagos, Nigeria'),
('active_payment_gateway', 'flutterwave'),
('min_deposit', '100'),
('max_deposit', '1000000'),
('flutterwave_enabled', '1'),
('flutterwave_env', 'sandbox'),
('flutterwave_public_key', ''),
('flutterwave_secret_key', ''),
('flutterwave_encryption_key', ''),
('flutterwave_webhook_secret', ''),
('paystack_enabled', '0'),
('paystack_env', 'test'),
('paystack_public_key', ''),
('paystack_secret_key', ''),
('strowallet_enabled', '0'),
('strowallet_api_base', ''),
('strowallet_api_key', ''),
('bank_transfer_enabled', '0'),
('bank_name', ''),
('bank_account_name', ''),
('bank_account_number', ''),
('bank_instructions', 'Please transfer the exact amount and upload your proof of payment.'),
('crypto_enabled', '0'),
('crypto_btc_address', ''),
('crypto_usdt_address', ''),
('crypto_eth_address', '')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Sample ticker notification (idempotent)
INSERT INTO notifications (id, title, message, type, is_active)
VALUES (1, 'Welcome', 'Welcome to SmikeBoost! Get 50% bonus on your first deposit.', 'ticker', 1)
ON DUPLICATE KEY UPDATE message = VALUES(message), type = VALUES(type), is_active = VALUES(is_active);

SET FOREIGN_KEY_CHECKS = 1;
