# SmikeBoost Installation Guide (cPanel)

Complete installation guide for deploying SmikeBoost on a cPanel-hosted server.

## Prerequisites

- cPanel/WHM access
- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+
- Apache with mod_rewrite enabled
- SSH/Terminal access (recommended)

## Step 1: Upload Project Files

### Option A: Via cPanel File Manager

1. Login to cPanel
2. Navigate to **File Manager** → **public_html**
3. Create a new folder: `smikeboost` (or your desired folder name)
4. Upload all project files into this folder (or use Git)

### Option B: Via Git (Recommended)

```bash
cd public_html
git clone <your-repo-url> smikeboost
cd smikeboost
```

### Option C: Via FTP

Use FileZilla or similar to upload all files to `public_html/smikeboost/`

---

## Step 2: Create MySQL Database

### Via cPanel

1. Login to cPanel
2. Go to **MySQL Databases**
3. Create a new database:

   - Database Name: `smikeboost_db` (or your choice)
   - Click **Create Database**

4. Create a new MySQL User:

   - Username: `smikeboost_user` (or your choice)
   - Password: `Generate a strong password` (e.g., `P@ssw0rd123!xYz`)
   - Click **Create User**

5. Assign user to database:
   - Select both user and database
   - Click **Add User to Database**
   - Grant **All Privileges**

### Via SSH (Alternative)

```bash
mysql -u root -p
CREATE DATABASE smikeboost_db;
CREATE USER 'smikeboost_user'@'localhost' IDENTIFIED BY 'P@ssw0rd123!xYz';
GRANT ALL PRIVILEGES ON smikeboost_db.* TO 'smikeboost_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## Step 3: Update Configuration File

Edit `app/config/config.php` with your database credentials:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'smikeboost_user');      // Your MySQL username
define('DB_PASS', 'P@ssw0rd123!xYz');       // Your MySQL password
define('DB_NAME', 'smikeboost_db');         // Your database name

// Site Configuration
define('SITE_URL', 'https://yourdomain.com/smikeboost');  // Update domain

// Mail Configuration (Gmail SMTP example)
define('MAIL_FROM', 'support@yourdomain.com');
define('MAIL_FROM_NAME', 'SmikeBoost');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');  // Use Gmail App Password, not regular password
```

---

## Step 4: Set File Permissions

Via cPanel Terminal or SSH:

```bash
cd public_html/smikeboost

# Make web-accessible directories writable
chmod 755 public/
chmod 755 app/
chmod 755 app/views/

# Optional: Make storage/logs writable if needed
mkdir -p storage/logs
chmod 777 storage/logs
```

---

## Step 5: Import Database Schema

### Via cPanel phpMyAdmin

1. Login to cPanel
2. Open **phpMyAdmin**
3. Select your database (`smikeboost_db`)
4. Click **Import** tab
5. Choose file: `database/schema.sql`
6. Click **Import**

### Via SSH Terminal

```bash
cd public_html/smikeboost
mysql -u smikeboost_user -p smikeboost_db < database/schema.sql
# Enter password: P@ssw0rd123!xYz
```

---

## Step 6: Enable mod_rewrite (if needed)

Create/verify `.htaccess` in `public_html/smikeboost/`:

```apache
Options -Indexes
RewriteEngine On
RewriteBase /smikeboost/

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

If mod_rewrite is disabled, contact your hosting provider or enable via cPanel's **EasyApache**.

---

## Step 7: Enable HTTPS (SSL/TLS)

1. In cPanel, go to **SSL/TLS Status**
2. Check if your domain has Auto SSL enabled
3. If not, use **AutoSSL** or purchase a certificate
4. After enabling SSL, update `SITE_URL` in `app/config/config.php` to use `https://`

---

## Step 8: Access Your Installation

Open your browser and visit:

```
https://yourdomain.com/smikeboost/
```

You should see the SmikeBoost homepage.

---

## Default Admin Account

**Admin Panel URL**: `https://yourdomain.com/smikeboost/public/admin/login`

| Field        | Value                  |
| ------------ | ---------------------- |
| **Username** | `admin`                |
| **Email**    | `admin@smikeboost.com` |
| **Password** | `admin123`             |

⚠️ **IMPORTANT**: Change the admin password immediately after first login!

### Change Admin Password

In cPanel Terminal/SSH:

```bash
php -r "echo password_hash('your-new-password', PASSWORD_BCRYPT);"
```

Copy the hash, then update `app/config/admin.php`:

```php
define('ADMIN_PASSWORD_HASH', 'paste-the-hash-here');
```

---

## Demo User Account

**Login Page**: `https://yourdomain.com/smikeboost/public/login`

| Field               | Value                 |
| ------------------- | --------------------- |
| **Username**        | `demouser`            |
| **Email**           | `demo@smikeboost.com` |
| **Password**        | `demo123456`          |
| **Initial Balance** | ₦50,000               |

To add this demo user, run in cPanel Terminal:

```bash
cd public_html/smikeboost
mysql -u smikeboost_user -p smikeboost_db << 'EOF'
INSERT INTO users (name, username, email, password_hash, role, wallet_balance, status)
VALUES ('Demo User', 'demouser', 'demo@smikeboost.com', '$2y$10$Kd1.h8fJ5Q6z9L2x1M3w4e7K9p2q5r8t1v4w7z0a3b6c9d2e5f8g1h4', 'user', 50000, 'active');
EOF
```

Or via phpMyAdmin directly insert the row with these values.

---

## Post-Installation Checklist

- [ ] Database created and schema imported
- [ ] Configuration file updated with DB credentials
- [ ] SITE_URL updated to match domain
- [ ] File permissions set correctly (755)
- [ ] mod_rewrite enabled
- [ ] HTTPS/SSL enabled
- [ ] Admin password changed
- [ ] Can login to frontend as demo user
- [ ] Can login to admin panel as admin
- [ ] Email configuration tested (optional but recommended)
- [ ] Database backups configured

---

## Verify Installation

### Test Frontend

1. Visit `https://yourdomain.com/smikeboost/`
2. Navigate through pages (Home, Services, About, etc.)
3. Click "Sign up" and create a test account
4. Login with new account
5. View dashboard

### Test Admin Panel

1. Visit `https://yourdomain.com/smikeboost/public/admin/login`
2. Login with admin credentials
3. View Dashboard, Orders, Users, Services pages
4. Verify data displays correctly

### Test Database Connection

In cPanel Terminal:

```bash
cd public_html/smikeboost
php -r "
require 'app/config/config.php';
try {
    \$pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    echo 'Database connection successful!';
} catch (Exception \$e) {
    echo 'Connection failed: ' . \$e->getMessage();
}
"
```

---

## Common Issues & Solutions

### Issue: 404 Page Not Found

**Solution**: Verify mod_rewrite is enabled. Check `.htaccess` exists in `public_html/smikeboost/`. Try accessing direct URL: `https://yourdomain.com/smikeboost/public/?page=home`

### Issue: Database Connection Error

**Solution**: Verify credentials in `app/config/config.php` match cPanel MySQL settings. Check database user has all privileges.

### Issue: Blank Page

**Solution**: Check cPanel error logs. Enable PHP error reporting in `app/config/config.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Issue: File Upload Fails

**Solution**: Ensure folder permissions are 755 or 777. Check folder ownership in cPanel File Manager.

### Issue: Email Not Sending

**Solution**: Verify SMTP credentials in `app/config/config.php`. For Gmail, use an [App Password](https://support.google.com/accounts/answer/185833), not your regular password.

---

## Security Recommendations

1. **Change default passwords** immediately
2. **Enable firewall** via cPanel Security Center
3. **Set up backups** via cPanel Backups
4. **Use strong passwords** (min 16 chars, mixed case, numbers, symbols)
5. **Restrict admin panel access** via IP whitelist (in `app/config/admin.php`)
6. **Enable 2FA** for cPanel account
7. **Keep PHP updated** to latest stable version
8. **Regular database backups** (automate via cPanel)

---

## Next Steps

1. **Customize Content**: Update homepage, services, and branding in admin panel
2. **Configure Payment Gateway**: Add Flutterwave keys in Admin → Payments
3. **Add Service Providers**: Configure provider APIs in Admin → Providers
4. **Set Up Email Notifications**: Test SMTP configuration
5. **Deploy to Production**: After thorough testing, enable maintenance mode off

---

## Support

For issues or questions:

- Check `DOCUMENTATION.md` for technical details
- Review error logs in cPanel
- Test database connection via SSH
- Verify all credentials match cPanel MySQL settings

---

## File Structure

```
public_html/
└── smikeboost/
    ├── app/
    │   ├── config/
    │   │   ├── config.php          (← Update with DB credentials)
    │   │   ├── admin.php
    │   │   └── database.php
    │   ├── controllers/
    │   ├── models/
    │   ├── helpers/
    │   └── views/
    ├── public/
    │   ├── index.php               (← Main entry point)
    │   ├── admin/
    │   ├── assets/
    │   └── .htaccess               (← Ensure mod_rewrite rules)
    ├── database/
    │   └── schema.sql              (← Run this in MySQL)
    ├── .htaccess                   (← Root redirect)
    └── README.md
```

---

**Installation Date**: December 6, 2025  
**Version**: 1.0  
**Last Updated**: December 6, 2025
