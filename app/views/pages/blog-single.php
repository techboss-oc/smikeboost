<div class="page-header">
    <div class="container">
        <h1 class="text-gradient"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="text-muted">
            <i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
        </p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="glass-card">
            <?php if (!empty($post['image'])): ?>
                <img src="<?php echo asset($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid mb-4" style="width: 100%; border-radius: var(--radius-md);">
            <?php endif; ?>
            
            <div class="blog-content" style="font-size: 1.1rem; line-height: 1.8;">
                <?php echo $post['content']; // Assuming content is stored as HTML from a rich text editor ?>
            </div>
            
            <div class="mt-5 pt-4" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--glass-border);">
                <a href="<?php echo url('blog'); ?>" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Blog
                </a>
            </div>
        </div>
    </div>
</section>
