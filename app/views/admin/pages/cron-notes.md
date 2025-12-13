# Admin Backend Cron Notes

- **Order status sync**: run every 5-10 minutes to hit provider `order_status_endpoint` and update local orders.
- **Services sync**: run daily to call provider `services_import_endpoint` and refresh catalog.
- **Balance check**: optional hourly to monitor provider balances.
- **Ticket reminders**: optional hourly to notify staff of waiting tickets.

Example (pseudo, Windows Task Scheduler):

- Command: `php c:\laragon\www\boost\artisan order:sync` (replace with actual script)
- Schedule: every 10 minutes
