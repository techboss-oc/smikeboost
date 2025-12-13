# SmikeBoost - SMM Panel Frontend

A modern, responsive, and feature-rich SMM (Social Media Marketing) panel frontend built with vanilla PHP and advanced CSS with glassmorphism design.

## 🎨 Design Features

- **Dark Mystical Fantasy Theme**: Beautiful dark purple gradient background with neon glowing effects
- **Glassmorphism UI**: Modern frosted glass effect cards with backdrop blur
- **Fully Responsive**: Works perfectly on desktop, tablet, and mobile devices
- **Smooth Animations**: Scroll reveal effects, floating animations, and smooth transitions
- **SEO Optimized**: Meta tags, structured data, and SEO best practices implemented

## 📁 Project Structure

```
boost/
├── public/
│   ├── index.php              # Main router
│   ├── .htaccess              # URL rewriting rules
│   └── assets/
│       ├── css/
│       │   ├── style.css      # Main stylesheet (glassmorphism, responsive)
│       │   ├── auth.css       # Authentication pages styles
│       │   └── dashboard.css  # Dashboard styles
│       ├── js/
│       │   ├── main.js        # Main JavaScript (forms, navigation)
│       │   ├── animations.js  # Scroll reveals and animations
│       │   └── dashboard.js   # Dashboard functionality
│       └── images/            # Placeholder for images
├── app/
│   ├── config/
│   │   └── config.php         # Configuration file
│   ├── helpers/
│   │   └── helpers.php        # Helper functions
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── main.php       # Main layout
│   │   │   ├── auth.php       # Auth layout
│   │   │   └── dashboard.php  # Dashboard layout
│   │   ├── pages/
│   │   │   ├── home.php
│   │   │   ├── services.php
│   │   │   ├── about.php
│   │   │   ├── contact.php
│   │   │   ├── how-it-works.php
│   │   │   ├── blog.php
│   │   │   ├── faq.php
│   │   │   ├── login.php
│   │   │   ├── register.php
│   │   │   ├── forgot-password.php
│   │   │   ├── dashboard-home.php
│   │   │   ├── dashboard-new-order.php
│   │   │   ├── dashboard-orders.php
│   │   │   ├── dashboard-add-funds.php
│   │   │   ├── dashboard-profile.php
│   │   │   ├── dashboard-support.php
│   │   │   └── 404.php
│   │   └── components/
│   │       ├── navbar.php
│   │       ├── footer.php
│   │       ├── dashboard-navbar.php
│   │       └── dashboard-sidebar.php
│   ├── controllers/           # For future API controllers
│   └── models/               # For future database models
└── README.md
```

## 🚀 Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ (for database functionality)
- A web server with mod_rewrite enabled (Apache/Nginx)
- cURL extension (for API calls)
- Composer (optional, for dependencies)

### Installation

1. **Clone or extract to your web server directory**

   ```bash
   # The project should be at: c:\laragon\www\boost
   ```

2. **Configure the config file**
   Edit `app/config/config.php` and update:

   - `SITE_URL` - Your site URL
   - `SITE_NAME` - Your site name
   - Database credentials (for future use)
   - Email settings (for future use)

3. **Access the website**

   ```
   http://localhost/boost/public/
   ```

4. **Enable mod_rewrite** (if not enabled)
   - Uncomment `LoadModule rewrite_module modules/mod_rewrite.so` in Apache config
   - Restart Apache

### Cron Job Setup

SmikeBoost requires automated cron jobs for order fulfillment, status checking, and auto refunds. Set up the following:

#### For Linux/Unix Servers:

1. Open crontab:

   ```bash
   crontab -e
   ```

2. Add this line to run every 5 minutes:

   ```bash
   */5 * * * * curl -s https://yourdomain.com/cron.php > /dev/null 2>&1
   ```

   Replace `yourdomain.com` with your actual domain.

#### For Windows Servers (using Task Scheduler):

1. Create a batch file `cron.bat` in your project root:

   ```batch
   @echo off
   curl -s https://yourdomain.com/cron.php > nul
   ```

2. Open Task Scheduler and create a new task:
   - Program: `C:\path\to\cron.bat`
   - Schedule: Every 5 minutes

#### What the Cron Does:

- **Order Fulfillment**: Automatically submits new orders to provider APIs
- **Status Checking**: Updates order status from providers (pending → processing → completed/failed)
- **Auto Refunds**: Refunds users for failed orders or orders that timeout (24 hours)
- **Provider Sync**: Keeps order statuses in sync with external providers

#### Testing the Cron:

You can manually test by visiting `https://yourdomain.com/cron.php` in your browser. It should output "Cron completed" if successful.

#### Cron Troubleshooting:

- Ensure `cron.php` is accessible (not blocked by .htaccess)
- Check server error logs for cURL issues
- Verify provider API credentials are correct
- Test with a small order to ensure fulfillment works

### Database Setup

- Create a MySQL database named `smikeboost`
- Import the SQL schema (contact support for schema file)
- Update database credentials in `app/config/config.php`

### Provider Setup

To enable order fulfillment:

1. Add providers in the admin panel (`/admin`)
2. Configure `api_url` and `api_key` for each provider
3. Map services to providers with correct `api_service_id`

Supported provider APIs: Standard SMM panel format (PerfectPanel, SmartPanel, etc.)

## 📄 Pages Available

### Public Pages

- **Home** - Hero section with features and CTAs
- **Services** - Browse and filter SMM services
- **About** - Company story and values
- **Contact** - Contact form and support channels
- **How It Works** - Step-by-step guide
- **Blog** - Blog posts and articles
- **FAQ** - Frequently asked questions

### Authentication Pages

- **Login** - User login
- **Register** - User registration
- **Forgot Password** - Password recovery

### Dashboard Pages (Protected)

- **Dashboard Home** - Overview and stats
- **New Order** - Create new orders
- **Order History** - View all orders
- **Add Funds** - Deposit money
- **Profile** - Manage account
- **Support** - Contact support

## 🎯 Features

### Frontend

✅ Responsive design (mobile-first)
✅ Modern glassmorphism UI
✅ Smooth animations and transitions
✅ Form validation
✅ FAQ accordion
✅ Service filtering
✅ Blog grid layout
✅ Dark theme with gradient backgrounds
✅ Glowing neon effects
✅ Floating card animations

### SEO Features

✅ Meta tags (title, description, keywords)
✅ Open Graph tags (social sharing)
✅ Twitter Card tags
✅ Structured semantic HTML
✅ Mobile-friendly viewport
✅ Fast load times
✅ SEO-friendly URL structure
✅ Accessible navigation

### JavaScript Features

✅ Navigation hamburger menu toggle
✅ FAQ accordion functionality
✅ Form validation
✅ Scroll reveal animations
✅ Parallax effects
✅ Counter animations for statistics
✅ Currency formatting
✅ Notification/toast system
✅ Lazy image loading
✅ Smooth scrolling

## 🛠️ Customization

### Colors

Edit `:root` variables in `public/assets/css/style.css`:

```css
:root {
  --color-primary: #a855f7; /* Purple */
  --color-accent: #ec4899; /* Pink */
  /* ... more colors ... */
}
```

### Fonts

Change font-family in `body` selector in `style.css`

### Content

Edit PHP files in `app/views/pages/` directory

### Configuration

Update settings in `app/config/config.php`

## 🔄 Routing

The router in `public/index.php` maps URLs to pages:

```php
// URL -> Page mapping
/boost/public/?page=home      → home.php
/boost/public/?page=services  → services.php
/boost/public/?page=login     → login.php
/boost/public/?page=dashboard → dashboard-home.php (protected)
```

## 💾 Database Integration (Future)

The project is structured to add database functionality:

1. Create database models in `app/models/`
2. Create API controllers in `app/controllers/`
3. Update form handling in pages to use models

## 🔐 Security Considerations

For production use:

1. Set `ENVIRONMENT` to 'production' in config
2. Update JWT_SECRET in config
3. Configure HTTPS
4. Implement database with prepared statements
5. Add CSRF token protection
6. Rate limit API endpoints
7. Sanitize all user inputs (helpers included)

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## 📊 Performance

- Optimized CSS (no unused styles)
- Minified JavaScript ready
- Lazy loading images (built-in)
- Efficient animations (CSS-based)
- Fast load time (<2s typical)

## 🎓 Learning Resources

- Vanilla PHP with clean architecture
- CSS Grid and Flexbox
- Modern JavaScript (ES6+)
- SEO best practices
- Responsive design patterns
- Glassmorphism design trend

## 📝 License

This project is provided as-is for educational and commercial use.

## 🤝 Support

For questions or issues:

- Email: support@smikeboost.com
- WhatsApp: [Contact]
- Telegram: @SmikeBoost

---

**Made with ❤️ for SMM Marketers Worldwide**
