# SmikeBoost Admin Backend (Vanilla PHP)

Lightweight admin dashboard scaffold powered by vanilla PHP (no framework) with glassmorphism-inspired UI, aligned to the provided requirements.

## Features Implemented

- Auth: simple session-based admin login with CSRF + rate limit + IP allow-list hook
- Layout: dark glass dashboard shell (sidebar, topbar, content area)
- Pages: Dashboard, Orders, Users, Services, Providers, Payments, Transactions, Tickets, Settings
- Styling: reuses existing `public/assets/css/dashboard.css` and FontAwesome
- Routing: single entry `public/admin/index.php` dispatching to `AdminController`

## Structure

- `public/admin/index.php` — router/entry
- `app/config/admin.php` — admin-specific config (credentials, limits)
- `app/helpers/admin_helpers.php` — admin helpers (auth, csrf, rate limit, IP allow)
- `app/controllers/AdminController.php` — actions per page
- `app/views/admin/layouts/admin.php` — layout shell
- `app/views/admin/pages/*.php` — page stubs
- `app/views/admin/pages/providers-api-stub.php` — stub functions for provider HTTP calls
- `app/views/admin/pages/cron-notes.md` — cron guidance

## Quick Start (Laragon)

1. Copy to `c:\laragon\www\boost` (already present).
2. Update credentials in `app/config/admin.php` (ADMIN_USERNAME/EMAIL/PASSWORD_HASH).
   - To change password: `php -r "echo password_hash('newpass', PASSWORD_BCRYPT);"`
3. Start Apache in Laragon.
4. Visit `http://localhost/boost/public/admin/login`.
5. Login with default: `admin` / `admin123` (hash stored in config).

## Notes / Next Steps

- Wire database models for users, orders, services, providers, payments, tickets.
- Replace page tables with real DB data + pagination (ADMIN_ITEMS_PER_PAGE).
- Implement provider HTTP calls in `providers-api-stub.php` (cURL) and cron scripts for sync.
- Add JWT if exposing admin APIs; keep session for panel.
- Enforce IP allow-list by filling `ADMIN_ALLOWED_IPS` in config.
- Add CSRF fields to all forms (already on login).
- Add input validation + flash errors/success on form submissions.
- Add roles/permissions checks per action.
- Secure .htaccess in `public/admin/.htaccess` if desired for extra layer.
