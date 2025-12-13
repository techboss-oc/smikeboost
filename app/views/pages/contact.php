<?php
/**
 * Contact Page
 */
$seo = get_seo_tags(
    "Contact SmikeBoost | WhatsApp, Telegram & Lagos Office",
    "Talk to a human in under five minutes. Lagos strategists, WhatsApp war rooms, and NDPR support standing by 24/7.",
    "Contact SmikeBoost Nigeria, WhatsApp SMM support, Lagos SMM panel help, NDPR support"
);
?>

<section class="contact-page section">
    <div class="container">
        <div class="contact-body">
            <div class="glass-card contact-form-card">
                <div class="contact-form-card__header">
                    <p class="contact-form-card__eyebrow">Send a ticket</p>
                    <h2>Tell us how we can help</h2>
                    <p>Share a few details about your request. Your ticket drops straight into our first-response playbook.</p>
                </div>

                <form class="contact-form" method="POST" action="<?php echo url("api/contact"); ?>">
                    <div class="contact-form-grid">
                        <div class="contact-field">
                            <label class="contact-field__label" for="name">Full Name</label>
                            <div class="contact-field__control">
                                <i class="fas fa-user"></i>
                                <input type="text" id="name" name="name" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="contact-field">
                            <label class="contact-field__label" for="email">Email Address</label>
                            <div class="contact-field__control">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" placeholder="john@example.com" required>
                            </div>
                        </div>

                        <div class="contact-field">
                            <label class="contact-field__label" for="phone">Phone (optional)</label>
                            <div class="contact-field__control">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" placeholder="+234...">
                            </div>
                        </div>

                        <div class="contact-field">
                            <label class="contact-field__label" for="subject">Subject</label>
                            <div class="contact-field__control">
                                <i class="fas fa-tag"></i>
                                <select id="subject" name="subject" required>
                                    <option value="" disabled selected>How can we help?</option>
                                    <option value="support">Technical Support</option>
                                    <option value="billing">Billing &amp; Payments</option>
                                    <option value="order">Order Inquiry</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="general">General Question</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="contact-field">
                        <label class="contact-field__label" for="message">Your Message</label>
                        <div class="contact-field__control contact-field__control--textarea">
                            <i class="fas fa-comment-alt"></i>
                            <textarea id="message" name="message" rows="6" placeholder="Tell us more about your campaign, issue, or request..." required></textarea>
                        </div>
                    </div>

                    <div class="contact-form-footer">
                        <div class="contact-form-note">
                            <i class="fas fa-shield-alt"></i>
                            <span>We route every ticket through NDPR-compliant workflows. Expect a personalised response — never a bot.</span>
                        </div>
                        <button type="submit" class="btn btn-primary contact-submit">
                            <span>Send Message</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="contact-sidebar">
                <div class="support-grid">
                    <a href="mailto:<?php echo get_setting("contact_email", "support@smikeboost.com"); ?>" class="contact-channel">
                        <div class="contact-channel__icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-channel__content">
                            <h3>Email Support</h3>
                            <p><?php echo e(get_setting("contact_email", "support@smikeboost.com")); ?></p>
                            <span class="contact-channel__cta">Send an email <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>

                    <a href="https://wa.me/<?php echo get_setting("contact_whatsapp"); ?>" target="_blank" class="contact-channel">
                        <div class="contact-channel__icon contact-channel__icon--whatsapp"><i class="fab fa-whatsapp"></i></div>
                        <div class="contact-channel__content">
                            <h3>WhatsApp</h3>
                            <p>Live updates, order escalations, and launch checklists.</p>
                            <span class="contact-channel__cta">Start chatting <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>

                    <a href="https://t.me/<?php echo get_setting("contact_telegram"); ?>" target="_blank" class="contact-channel">
                        <div class="contact-channel__icon contact-channel__icon--telegram"><i class="fab fa-telegram-plane"></i></div>
                        <div class="contact-channel__content">
                            <h3>Telegram Channel</h3>
                            <p>Get deployment alerts and best-practice drops in real time.</p>
                            <span class="contact-channel__cta">Join channel <i class="fas fa-arrow-right"></i></span>
                        </div>
                    </a>

                    <div class="contact-channel">
                        <div class="contact-channel__icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-channel__content">
                            <h3>Lagos HQ</h3>
                            <p>Victoria Island, Lagos, Nigeria</p>
                            <span class="contact-channel__cta">View on map (by appointment)</span>
                        </div>
                    </div>
                </div>

                <div class="contact-faq-card">
                    <div class="contact-faq-card__content">
                        <h3>Need a quick answer?</h3>
                        <p>Browse FAQs for instant help on orders, payments, refunds, and API access.</p>
                    </div>
                    <a href="<?php echo url("faq"); ?>" class="btn btn-outline btn-sm">Visit FAQ</a>
                </div>

                <div class="contact-policy-card">
                    <h3>NDPR &amp; Data Requests</h3>
                    <p>For subject access requests, deletion requests, or compliance documentation, contact our Data Protection Officer.</p>
                    <p class="contact-policy-note">Email: <a href="mailto:dpo@smikeboost.com">dpo@smikeboost.com</a></p>
                    <p class="contact-policy-note">We respond to NDPR submissions within 72 hours.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$contactSchema = [
    "@context" => "https://schema.org",
    "@type" => "Organization",
    "name" => "SmikeBoost Support",
    "url" => url("contact"),
    "contactPoint" => [
        [
            "@type" => "ContactPoint",
            "contactType" => "customer support",
            "telephone" => get_setting("contact_phone", "+2348072703028"),
            "email" => get_setting("contact_email", "support@smikeboost.com"),
            "availableLanguage" => ["English"],
            "areaServed" => "NG"
        ],
        [
            "@type" => "ContactPoint",
            "contactType" => "data protection",
            "email" => "dpo@smikeboost.com",
            "availableLanguage" => ["English"],
            "areaServed" => "NG"
        ]
    ]
];
?>
<script type="application/ld+json">
    <?php echo json_encode($contactSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
