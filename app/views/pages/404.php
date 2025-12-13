<?php
/**
 * 404 Not Found Page
 */
$seo = get_seo_tags('Page Not Found', 'The page you\'re looking for doesn\'t exist', '');
?>

<section class="not-found-page">
    <div class="section-container">
        <div class="not-found-content">
            <div class="not-found-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1>404</h1>
            <h2>Page Not Found</h2>
            <p>Sorry, the page you're looking for doesn't exist or has been moved.</p>
            <div class="not-found-actions">
                <a href="<?php echo url(); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-home"></i> Go Home
                </a>
                <a href="<?php echo url('contact'); ?>" class="btn btn-outline btn-lg">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.not-found-page {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-2xl) var(--spacing-lg);
    text-align: center;
}

.not-found-content {
    max-width: 600px;
}

.not-found-icon {
    font-size: 8rem;
    color: var(--color-danger);
    opacity: 0.3;
    margin-bottom: var(--spacing-lg);
}

.not-found-page h1 {
    font-size: 5rem;
    color: var(--color-primary);
    margin-bottom: var(--spacing-md);
}

.not-found-page h2 {
    font-size: 2rem;
    margin-bottom: var(--spacing-lg);
}

.not-found-page p {
    font-size: 1.125rem;
    margin-bottom: var(--spacing-2xl);
}

.not-found-actions {
    display: flex;
    gap: var(--spacing-lg);
    justify-content: center;
    flex-wrap: wrap;
}
</style>
