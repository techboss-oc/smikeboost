<?php
/**
 * Footer Component
 */
?>
<footer class="footer glass-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="<?php echo url(); ?>" class="footer-logo">
                    <i class="fas fa-bolt text-gradient"></i>
                    <span>SmikeBoost</span>
                </a>
                <p class="footer-desc">
                    Nigeria's #1 SMM Panel. We help creators, brands, and businesses grow their social presence with instant, automated services.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <div class="footer-links-group">
                <h4>Platform</h4>
                <ul>
                    <li><a href="<?php echo url('services'); ?>">Services</a></li>
                    <li><a href="<?php echo url('api-docs'); ?>">API</a></li>
                    <li><a href="<?php echo url('status'); ?>">System Status</a></li>
                    <li><a href="<?php echo url('how-it-works'); ?>">How it Works</a></li>
                </ul>
            </div>

            <div class="footer-links-group">
                <h4>Company</h4>
                <ul>
                    <li><a href="<?php echo url('about'); ?>">About Us</a></li>
                    <li><a href="<?php echo url('blog'); ?>">Blog</a></li>
                    <li><a href="<?php echo url('contact'); ?>">Contact</a></li>
                    <li><a href="<?php echo url('careers'); ?>">Careers</a></li>
                </ul>
            </div>

            <div class="footer-links-group">
                <h4>Legal</h4>
                <ul>
                    <li><a href="<?php echo url('terms'); ?>">Terms of Service</a></li>
                    <li><a href="<?php echo url('privacy-policy'); ?>">Privacy Policy</a></li>
                    <li><a href="<?php echo url('cookie-policy'); ?>">Cookie Policy</a></li>
                    <li><a href="<?php echo url('disclaimer'); ?>">Disclaimer</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> SmikeBoost. All rights reserved.
            </div>
            <div class="made-in">
                <span>Made By <a href="https://smikedigital.com" target="_blank" style="color: var(--color-primary); text-decoration: none;">Smikedigital</a></span>
            </div>
        </div>
    </div>
</footer>
