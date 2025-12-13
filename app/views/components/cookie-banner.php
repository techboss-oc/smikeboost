<div class="cookie-consent" id="cookieConsent" role="dialog" aria-live="polite" aria-label="Cookie consent banner" hidden>
    <div class="cookie-consent__content">
        <p class="cookie-consent__eyebrow">NDPR compliant</p>
        <h3>We use cookies to keep SmikeBoost secure</h3>
        <p>
            Essential cookies keep you signed in and protect payments. Analytics and marketing cookies help us understand what works. Read the <a href="<?php echo url('cookie-policy'); ?>">Cookie Policy</a> for details.
        </p>
        <button type="button" class="cookie-consent__manage" id="cookieConsentManage" aria-expanded="false">
            Manage preferences
        </button>
        <div class="cookie-consent__options" id="cookiePreferenceGroup" hidden>
            <label class="cookie-pref">
                <input type="checkbox" data-cookie-pref="analytics" checked>
                <span>
                    <strong>Analytics</strong>
                    <small>Shows what visitors engage with so we can improve pages.</small>
                </span>
            </label>
            <label class="cookie-pref">
                <input type="checkbox" data-cookie-pref="marketing" checked>
                <span>
                    <strong>Marketing</strong>
                    <small>Lets us run focused offers on WhatsApp and social channels.</small>
                </span>
            </label>
        </div>
    </div>
    <div class="cookie-consent__actions">
        <button type="button" class="btn btn-outline" data-consent="essential">Allow essentials only</button>
        <button type="button" class="btn btn-primary" data-consent="selected">Save preferences</button>
        <button type="button" class="cookie-consent__link" data-consent="all">Accept all cookies</button>
    </div>
</div>
