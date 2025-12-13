<?php
/**
 * Dashboard Support Page
 */
$seo = get_seo_tags('Support', 'Contact our support team for help', '');
?>

<section class="support-page">
    <div class="page-header">
        <h1>Support & Help</h1>
        <p>Get help from our support team</p>
    </div>

    <div class="support-container grid-2" style="max-width: 1200px; margin: 0 auto;">
        <!-- Contact Methods -->
        <div>
            <h2 class="mb-lg" style="font-size: 1.25rem;">Contact Us</h2>
            
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h3 class="mb-sm" style="margin-top: 0;">Email Support</h3>
                    <p class="mb-sm"><a href="mailto:<?php echo get_setting('contact_email', 'support@smikeboost.com'); ?>" class="text-primary"><?php echo get_setting('contact_email', 'support@smikeboost.com'); ?></a></p>
                    <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Response time: 1-2 hours</p>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-icon" style="color: #25D366;">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h3 class="mb-sm" style="margin-top: 0;">WhatsApp</h3>
                    <p class="mb-sm"><a href="https://wa.me/<?php echo get_setting('contact_whatsapp'); ?>" class="text-primary"><?php echo get_setting('contact_whatsapp', '+234 (0) XXX XXX XXXX'); ?></a></p>
                    <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Available 24/7</p>
                </div>
            </div>

            <div class="contact-card">
                <div class="contact-icon" style="color: #0088cc;">
                    <i class="fab fa-telegram"></i>
                </div>
                <div>
                    <h3 class="mb-sm" style="margin-top: 0;">Telegram</h3>
                    <p class="mb-sm"><a href="https://t.me/<?php echo get_setting('contact_telegram'); ?>" class="text-primary">@<?php echo get_setting('contact_telegram', 'SmikeBoost'); ?></a></p>
                    <p class="text-tertiary" style="margin: 0; font-size: 0.875rem;">Available 24/7</p>
                </div>
            </div>
        </div>

        <!-- Support Tickets -->
        <div>
            <h2 class="mb-lg" style="font-size: 1.25rem;">Create Support Ticket</h2>
            
            <form class="glass-card">
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" class="form-control" placeholder="Briefly describe your issue" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" required>
                        <option value="">Select category</option>
                        <option value="technical">Technical Support</option>
                        <option value="order">Order Issue</option>
                        <option value="billing">Billing & Payment</option>
                        <option value="account">Account Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Priority</label>
                    <select class="form-control" required>
                        <option value="">Select priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" placeholder="Describe your issue in detail" rows="6" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Submit Ticket
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div style="margin-top: var(--spacing-2xl);">
        <h2 class="mb-lg" style="font-size: 1.25rem;">Your Support Tickets</h2>
        <div class="table-responsive">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#TKT-001</td>
                        <td>Order not delivered</td>
                        <td>Order Issue</td>
                        <td><span class="badge badge-completed">Resolved</span></td>
                        <td>Dec 3, 2024</td>
                    </tr>
                    <tr>
                        <td>#TKT-002</td>
                        <td>Payment failed</td>
                        <td>Billing & Payment</td>
                        <td><span class="badge badge-completed">Resolved</span></td>
                        <td>Dec 1, 2024</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
