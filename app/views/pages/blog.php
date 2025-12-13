<?php

/**
 * Blog Page
 */
$seo = get_seo_tags(
    'SmikeBoost Blog | Nigeria Social Media Growth Playbooks & Tactics',
    'Data-backed Lagos campaign insights, StoryBrand templates, TikTok/Instagram/YouTube growth tactics, and WhatsApp commerce funnels. 8-minute briefs for Nigerian creators, agencies, and brands.',
    'Nigeria social media marketing, Lagos campaign data, TikTok Nigeria trends, Instagram growth Nigeria, YouTube strategy Nigeria, WhatsApp commerce Nigeria, SMM playbooks, agency automation NG'
);

// Fetch posts from DB
require_once APP_PATH . '/controllers/BlogController.php';
$blogController = new BlogController();
$data = $blogController->index();
$blogPosts = $data['posts'];


$featuredPosts = array_slice($blogPosts, 0, 9);
$platformFilters = ['All Platforms', 'Instagram', 'TikTok', 'YouTube', 'Twitter/X', 'WhatsApp', 'Agency'];
$audienceFilters = ['All Audiences', 'Creators', 'Agencies', 'Brands', 'Labels'];
$goalFilters = ['All Goals', 'Awareness', 'Sales', 'Community', 'Retention'];
?>

<section class="blog-page">
    <div class="glass-card" style="padding: 32px; display: grid; gap: 16px;">
        <div>
            <p class="eyebrow">SmikeBoost Intelligence</p>
            <h1>Nigeria Social Media Growth Playbooks</h1>
            <p>Actionable briefs for Nigerian creators, agencies, and brands — built on Lagos campaign data with StoryBrand templates and platform‑specific tactics for TikTok, Instagram, YouTube, and WhatsApp commerce.</p>
        </div>
        <div style="display:flex; flex-wrap: wrap; align-items:center; gap:8px;">
            <span class="badge">Instagram</span>
            <span class="badge">TikTok</span>
            <span class="badge">YouTube</span>
            <span class="badge">WhatsApp</span>
            <span class="badge">Agency</span>
        </div>
        <div class="page-links" style="display:flex; flex-wrap: wrap; gap:12px; align-items:center;">
            <a href="<?php echo url('services'); ?>" class="btn btn-primary">Explore SMM Services</a>
            <a href="<?php echo url('pricing'); ?>" class="btn btn-outline">View Pricing & Plans</a>
            <a href="<?php echo url('faq'); ?>" class="btn btn-outline">Read FAQs</a>
        </div>
    </div>

    <div class="section-container">
        <form class="blog-filters glass-card" aria-label="Filter blog posts" style="display:grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px;">
            <div class="filter-group">
                <label for="filter-platform">Platform</label>
                <div class="select-wrapper">
                    <select id="filter-platform" name="platform">
                        <?php foreach ($platformFilters as $platform): ?>
                            <option value="<?php echo strtolower(str_replace(['/', ' '], '-', $platform)); ?>"><?php echo e($platform); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-group">
                <label for="filter-audience">Audience</label>
                <div class="select-wrapper">
                    <select id="filter-audience" name="audience">
                        <?php foreach ($audienceFilters as $audience): ?>
                            <option value="<?php echo strtolower(str_replace(' ', '-', $audience)); ?>"><?php echo e($audience); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-group">
                <label for="filter-goal">Goal</label>
                <div class="select-wrapper">
                    <select id="filter-goal" name="goal">
                        <?php foreach ($goalFilters as $goal): ?>
                            <option value="<?php echo strtolower(str_replace(' ', '-', $goal)); ?>"><?php echo e($goal); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-group search">
                <label for="filter-keyword">Keyword</label>
                <div class="input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="search" id="filter-keyword" placeholder="Search Nigeria social media playbooks…">
                </div>
            </div>
            <div style="display:flex; align-items:end;">
                <button type="button" class="btn btn-primary" style="width:100%;">Apply filters</button>
            </div>
        </form>



        <!-- Blog Grid -->
        <div class="blog-grid" aria-label="Latest playbooks">
            <?php if (empty($featuredPosts)): ?>
                <div class="glass-card" style="padding:24px; text-align:center;">
                    <p>No playbooks published yet. Check back soon.</p>
                </div>
            <?php endif; ?>
            <?php foreach ($featuredPosts as $post):
                $date = isset($post['published_at']) ? $post['published_at'] : ($post['created_at'] ?? date('Y-m-d'));
                $category = $post['category'] ?? 'General';
                $readingTime = $post['reading_time'] ?? '5 min read';
                $link = url('blog/' . $post['slug']);
            ?>
                <article id="post-<?php echo e($post['slug']); ?>" class="blog-card glass-card">
                    <div class="blog-image">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo asset($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width:100%; height: 200px; object-fit: cover; border-radius: var(--radius-sm);">
                        <?php else: ?>
                            <div class="blog-placeholder" style="height: 200px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; border-radius: var(--radius-sm);">
                                <span class="badge"><?php echo e($category); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="blog-content">
                        <span class="blog-date"><?php echo date('M j, Y', strtotime($date)); ?> • <?php echo e($readingTime); ?> • WAT</span>
                        <h3><?php echo e($post['title']); ?></h3>
                        <p><?php echo e($post['excerpt']); ?></p>
                        <p class="blog-author">By SmikeBoost Editorial · Lagos/Abuja/PH signals</p>
                        <a href="<?php echo $link; ?>" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination" aria-label="Pagination">
            <a href="#" class="btn btn-outline">← Previous</a>
            <span class="page-info">Page 1</span>
            <a href="#" class="btn btn-outline">Next →</a>
        </div>
    </div>
</section>


<?php
$blogSchema = [
    '@context' => 'https://schema.org',
    '@graph' => array_map(function ($post) {
        return [
            '@type' => 'BlogPosting',
            'headline' => $post['title'],
            'description' => $post['excerpt'],
            'datePublished' => date(DATE_ATOM, strtotime($post['published_at'])),
            'author' => [
                '@type' => 'Organization',
                'name' => 'SmikeBoost Editorial'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'SmikeBoost',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('assets/images/logo.png')
                ]
            ],
            'mainEntityOfPage' => url('blog') . '#post-' . $post['slug']
        ];
    }, $featuredPosts)
];
?>
<script type="application/ld+json">
    <?php echo json_encode($blogSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>