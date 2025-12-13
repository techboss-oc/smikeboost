# Project Update Instructions

## 1. Database Updates

Import **`database/boost_full_schema.sql`** (only file required now). The script:

- Builds every table used by the app (users, services, orders, transactions, tickets, child_panels, referral_logs, settings, notifications, posts, etc.).
- Seeds the default admin account (`admin@smikeboost.com` / `admin123`).
- Inserts all default `settings` values plus the welcome ticker item.

> Run via cPanel’s MySQL import tool or CLI: `mysql -u <user> -p <db> < database/boost_full_schema.sql`

## 2. Configuration

1.  **Floating Widgets**:

    - Log in to the Admin Panel.
    - Go to **Settings**.
    - Scroll down to "Floating Widgets".
    - Enter your WhatsApp number (international format, e.g., `234800...`) and Tawk.to Property ID.
    - Toggle the switches to Enable/Disable them.
    - Click **Save Settings**.

2.  **News Ticker**:

    - The ticker displays messages from the `notifications` table where `type = 'ticker'`.
    - To add a ticker message, run this SQL in your database manager:
      ```sql
      INSERT INTO notifications (title, message, type, is_active)
      VALUES ('Update', 'Your ticker message here', 'ticker', 1);
      ```

3.  **Blog**:

    - Access the blog at `/blog`.
    - Manage posts in the Admin Panel under **Blog**.

4.  **Admin Login**:
    - The admin login page now authenticates against the `users` table. Ensure the admin row exists (seeded by the SQL file) and update its `password_hash` if you change the password.
    - The legacy config constants remain as a fallback so you can still log in with `admin` / `admin123` if the DB entry is missing.

## 3. Features Implemented

- **Mobile Menu**: Fixed CSS for better responsiveness.
- **Floating Widgets**: WhatsApp and Tawk.to chat buttons.
- **News Ticker**: Scrolling updates bar in the header.
- **How to Earn**: New page at `/how-to-earn`.
- **Email System**: `Mailer` class and templates created.
- **Blog System**: Complete backend and frontend implementation.
