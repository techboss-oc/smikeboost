<?php
$items = [];
try {
    if (function_exists('db_fetch_all')) {
        $tickers = db_fetch_all("SELECT message FROM notifications WHERE type = 'ticker' AND is_active = 1 ORDER BY created_at DESC");
        foreach ($tickers as $t) {
            if (!empty($t['message'])) {
                $items[] = ['message' => $t['message']];
            }
        }
        $ann = db_fetch_all("SELECT title, content FROM announcements WHERE is_active = 1 AND (start_date IS NULL OR start_date <= CURDATE()) AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY created_at DESC");
        foreach ($ann as $a) {
            $msg = trim((string)($a['title'] ?? ''));
            if (!$msg) {
                $msg = trim((string)($a['content'] ?? ''));
            }
            if ($msg) {
                $items[] = ['message' => $msg];
            }
        }
    }
} catch (Throwable $e) {
}
?>
<?php if (!empty($items)): ?>
    <div class="news-ticker-wrap">
        <div class="ticker-label">UPDATES</div>
        <div class="ticker-container">
            <div class="ticker-move">
                <?php foreach ($items as $news): ?>
                    <span class="ticker-item">
                        <i class="fas fa-bullhorn text-accent"></i> <?php echo htmlspecialchars($news['message']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>