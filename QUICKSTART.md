# SmikeBoost - Quick Start Guide

## Installation Steps

### 1. Initial Setup

```bash
# Navigate to project directory
cd c:\laragon\www\boost

# The project should already be in place with all files
```

### 2. Configure Environment

Edit `app/config/config.php` and update the following constants:

```php
// Site Configuration
define('SITE_URL', 'http://localhost/boost/public'); // Change to your domain
define('SITE_NAME', 'SmikeBoost');
define('SITE_DESCRIPTION', 'The Ultimate SMM Panel');

// Database (when ready to implement)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'smikeboost');

// Payment Gateway
define('PAYMENT_GATEWAY', 'flutterwave');

// JWT Secret (for API authentication)
define('JWT_SECRET', 'generate_a_random_secret_key_here');

// Email Settings (for future use)
define('MAIL_FROM', 'noreply@smikeboost.com');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', 'your_email@gmail.com');
define('MAIL_PASSWORD', 'your_app_password');
```

### 3. Enable Apache mod_rewrite

**For Laragon:**

1. Click on "Apache" in Laragon menu
2. Go to "Apache" → "Edit Config"
3. Search for `LoadModule rewrite_module`
4. Uncomment the line (remove #)
5. Restart Apache

**For XAMPP:**

1. Open `httpd.conf` in `apache/conf/`
2. Find `LoadModule rewrite_module`
3. Uncomment if needed
4. Restart Apache

### 4. Access the Website

Open your browser and navigate to:

```
http://localhost/boost/public/
```

### 5. Test All Pages

- **Home**: http://localhost/boost/public/?page=home
- **Services**: http://localhost/boost/public/?page=services
- **About**: http://localhost/boost/public/?page=about
- **Contact**: http://localhost/boost/public/?page=contact
- **Login**: http://localhost/boost/public/?page=login
- **Register**: http://localhost/boost/public/?page=register
- **FAQ**: http://localhost/boost/public/?page=faq
- **Blog**: http://localhost/boost/public/?page=blog
- **How It Works**: http://localhost/boost/public/?page=how-it-works
- **Dashboard**: http://localhost/boost/public/?page=dashboard (requires login)

## File Structure Overview

```
public/
├── index.php           ← Main entry point (router)
├── .htaccess          ← URL rewriting configuration
└── assets/
    ├── css/           ← All stylesheets
    ├── js/            ← JavaScript files
    └── images/        ← Images (to be added)

app/
├── config/
│   └── config.php     ← Site configuration
├── helpers/
│   └── helpers.php    ← Utility functions
└── views/
    ├── layouts/       ← Page templates
    ├── pages/         ← Page content
    └── components/    ← Reusable components
```

## Customization Guide

### Change Colors

Edit `public/assets/css/style.css` - look for `:root` section at the top

### Change Logo/Branding

1. Edit `app/views/components/navbar.php` - change logo text
2. Edit `app/config/config.php` - change SITE_NAME

### Add New Pages

1. Create a new file in `app/views/pages/yourpage.php`
2. Add to `$pageMap` in `public/index.php`
3. Link to it from navbar or other pages using `url('yourpage')`

### Modify Navigation

Edit `app/views/components/navbar.php` to add/remove menu items

### Change Styling

- **Global styles**: `public/assets/css/style.css`
- **Auth pages**: `public/assets/css/auth.css`
- **Dashboard**: `public/assets/css/dashboard.css`

## Key Features Explained

### Responsive Design

- Automatically adjusts for mobile, tablet, and desktop
- Media queries handle all breakpoints
- Touch-friendly buttons and spacing

### Dark Theme

- Built with accessibility in mind
- Easy on the eyes
- Professional mystical fantasy design

### Glassmorphism

- Modern frosted glass effect
- Uses CSS backdrop-filter
- Fallback for older browsers

### Animation Effects

- Scroll-triggered animations
- Floating card effects
- Smooth transitions
- Counter animations for statistics

### SEO Optimization

- Proper meta tags
- Open Graph for social sharing
- Semantic HTML structure
- Fast loading
- Mobile-friendly

## Testing Checklist

- [ ] Home page loads correctly
- [ ] Navigation menu works
- [ ] All links navigate properly
- [ ] Forms display correctly
- [ ] Responsive design works on mobile
- [ ] Images load (once added)
- [ ] FAQ accordion opens/closes
- [ ] Contact form looks good
- [ ] Dashboard pages require login
- [ ] Footer displays correctly

## Common Issues & Solutions

### Issue: Pages not loading

**Solution**: Check that mod_rewrite is enabled and Apache is restarted

### Issue: Styling looks broken

**Solution**: Hard refresh browser (Ctrl+Shift+R on Windows/Linux, Cmd+Shift+R on Mac)

### Issue: JavaScript not working

**Solution**: Check browser console for errors (F12), ensure all JS files load

### Issue: Forms not submitting

**Solution**: These are frontend mockups - backend API routes need to be created

## Next Steps for Development

1. **Create Backend APIs**

   - User authentication
   - Order management
   - Payment processing
   - Admin functionality

2. **Setup Database**

   - Create database schema
   - Create models in `app/models/`
   - Create API controllers in `app/controllers/`

3. **Integrate Payment Gateway**

   - Flutterwave integration
   - Bank transfer handling
   - Wallet management

4. **Add Real Data**

   - Replace mock data with database queries
   - Setup admin panel
   - Implement order system

5. **Deploy**
   - Choose hosting provider
   - Configure domain
   - Setup SSL certificate
   - Deploy code

## Getting Help

For issues or questions:

1. Check the README.md for detailed information
2. Review the code comments in files
3. Check `app/config/config.php` for configuration
4. Review `app/helpers/helpers.php` for utility functions

## Tips for Success

✅ Keep code organized in the `app/` directory
✅ Use the helper functions provided
✅ Follow the existing code structure and style
✅ Test pages after making changes
✅ Use browser developer tools for debugging
✅ Always sanitize user input
✅ Add comments to your code
✅ Version control with Git

---

**You're all set! Start customizing and building your SMM panel! 🚀**
