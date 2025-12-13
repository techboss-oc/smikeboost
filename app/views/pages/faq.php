<?php
/**
 * FAQ Page
 */
$seo = get_seo_tags(
    'FAQ',
    'Frequently asked questions about SmikeBoost SMM panel services',
    'FAQ, Questions, Help, Support, SmikeBoost'
);

$faqItems = require APP_PATH . '/content/faqs.php';
?>

<section class="faq-page section" style="padding-top: 1rem;">
    <div class="container">
        <div class="page-header" style="padding-bottom: 1rem;">
            <p class="eyebrow">Support Center</p>
            <h1 class="text-gradient">Frequently Asked Questions</h1>
            <p>Everything you need to know about our services, billing, and support.</p>
        </div>

        <div class="faq-grid" style="max-width: 800px; margin: 0 auto; display: grid; gap: 1.5rem;">
            <?php foreach ($faqItems as $index => $faq): ?>
                <div class="glass-card faq-card" style="cursor: pointer; transition: all 0.3s ease;">
                    <div class="faq-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="font-size: 1.1rem; margin: 0;"><?php echo e($faq['question']); ?></h3>
                        <i class="fas fa-chevron-down" style="color: var(--color-primary); transition: transform 0.3s ease;"></i>
                    </div>
                    <div class="faq-body" style="display: none; margin-top: 1rem; color: var(--text-muted); line-height: 1.6; border-top: 1px solid var(--glass-border); padding-top: 1rem;">
                        <?php echo e($faq['answer']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cta-banner glass-card" style="margin-top: 4rem; text-align: center; padding: 3rem;">
            <h2>Still have questions?</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">Can't find the answer you're looking for? Please chat to our friendly team.</p>
            <a href="<?php echo url('contact'); ?>" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Contact Support
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqCards = document.querySelectorAll('.faq-card');
    
    faqCards.forEach(card => {
        card.addEventListener('click', function() {
            const body = this.querySelector('.faq-body');
            const icon = this.querySelector('.fa-chevron-down');
            
            // Close other open FAQs
            faqCards.forEach(otherCard => {
                if (otherCard !== card) {
                    otherCard.querySelector('.faq-body').style.display = 'none';
                    otherCard.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
                    otherCard.style.background = 'var(--glass-bg)';
                }
            });
            
            // Toggle current
            if (body.style.display === 'none' || !body.style.display) {
                body.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
                this.style.background = 'var(--glass-bg-hover)';
            } else {
                body.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
                this.style.background = 'var(--glass-bg)';
            }
        });
    });
});
</script>

<?php
$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(function ($item) {
        return [
            '@type' => 'Question',
            'name' => $item['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $item['answer'],
            ],
        ];
    }, $faqItems),
];
?>
<script type="application/ld+json">
    <?php echo json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
