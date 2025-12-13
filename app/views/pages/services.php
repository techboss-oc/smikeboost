<?php
/**
 * Public services page (PerfectPanel/SmartPanel style)
 */
require_once APP_PATH . '/models/Service.php';
$serviceModel = new Service();
$services = $serviceModel->getActive();
$categories = $serviceModel->getCategories();
$totalServices = count($services);

$seo = get_seo_tags(
    'Services | ' . SITE_NAME,
    'Browse ' . $totalServices . ' ready-to-order SMM services with NGN pricing, accurate min/max values, and instant checkout.',
    'smm panel nigeria, smart panel alternative, perfect panel services'
);
?>

<section class="services-panel">
    <div class="container">
        <header class="panel-header glass-card">
            <div>
                <p class="eyebrow">All services, live pricing</p>
                <h1>Service Catalog</h1>
                <p class="lead">Review every active service with real-time NGN pricing and min/max limits.</p>
            </div>
            <div class="stats">
                <span><strong><?= number_format($totalServices) ?></strong> services</span>
                <span><strong><?= count($categories) ?></strong> categories</span>
            </div>
        </header>

        <div class="panel-filters glass-card">
            <div class="filter">
                <label for="serviceSearch">Search</label>
                <div class="input-icon">
                    <i class="fas fa-search"></i>
                    <input type="text" id="serviceSearch" placeholder="Service name, ID, platform...">
                </div>
            </div>
            <div class="filter">
                <label for="categoryFilter">Category</label>
                <select id="categoryFilter">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter">
                <label for="priceSort">Sort by price</label>
                <select id="priceSort">
                    <option value="">Default</option>
                    <option value="asc">Lowest first</option>
                    <option value="desc">Highest first</option>
                </select>
            </div>
            <button type="button" id="resetFilters" class="btn btn-outline"><i class="fas fa-undo"></i> Reset</button>
        </div>

        <div class="table-wrapper glass-card">
            <?php if (empty($services)): ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <p>No services have been published yet.</p>
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table class="services-table" id="servicesTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Service</th>
                                <th>Rate / 1K</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($services as $service): ?>
                                <tr data-category="<?= htmlspecialchars($service['category']) ?>" data-price="<?= $service['rate_per_1000'] ?>">
                                    <td class="text-muted">#<?= $service['id'] ?></td>
                                    <td><?= htmlspecialchars($service['category']) ?></td>
                                    <td>
                                        <div class="service-name"><?= htmlspecialchars($service['name']) ?></div>
                                        <small class="platform-tag"><?= htmlspecialchars($service['platform']) ?></small>
                                    </td>
                                    <td class="price"><?= format_currency($service['rate_per_1000']) ?></td>
                                    <td><?= number_format($service['min_qty']) ?></td>
                                    <td><?= number_format($service['max_qty']) ?></td>
                                    <td class="description"><?= htmlspecialchars($service['description'] ?? 'No description provided yet') ?></td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="<?= url('dashboard/new-order?service=' . $service['id']) ?>">Order</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.services-panel {
    padding: 60px 0 80px;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 32px;
}

.panel-header .eyebrow {
    font-size: 0.8rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.panel-header h1 {
    margin: 4px 0 8px;
}

.panel-header .lead {
    color: var(--text-muted);
}

.panel-header .stats {
    text-align: right;
    color: var(--text-muted);
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 6px;
}

.panel-filters {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
    align-items: end;
}

.panel-filters label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-bottom: 6px;
    display: block;
}

.panel-filters input,
.panel-filters select {
    width: 100%;
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    border: 1px solid rgba(255,255,255,0.15);
    background: rgba(15,10,31,0.85);
    color: var(--text-main);
    appearance: none;
}

.panel-filters select option {
    background: #120a24;
    color: var(--text-main);
}

.input-icon {
    position: relative;
}

.input-icon i {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.input-icon input {
    padding-left: 38px;
}

.table-wrapper {
    padding: 0;
}

.table-scroll {
    overflow-x: auto;
}

.services-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 900px;
}

.services-table thead {
    background: rgba(255,255,255,0.05);
}

.services-table th,
.services-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    text-align: left;
    font-size: 0.93rem;
}

.services-table th {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
}

.services-table tr:hover {
    background: rgba(168,85,247,0.08);
}

.service-name {
    font-weight: 600;
}

.platform-tag {
    color: var(--text-muted);
}

.price {
    color: var(--color-success);
    font-weight: 600;
}

.description {
    max-width: 320px;
    color: var(--text-muted);
    line-height: 1.4;
}

.text-muted {
    color: var(--text-dim);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 40px;
    margin-bottom: 12px;
}

@media (max-width: 900px) {
    .panel-header {
        flex-direction: column;
        text-align: left;
    }

    .panel-header .stats {
        text-align: left;
        flex-direction: row;
        gap: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = Array.from(document.querySelectorAll('#servicesTable tbody tr'));
    const searchInput = document.getElementById('serviceSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const priceSort = document.getElementById('priceSort');
    const resetBtn = document.getElementById('resetFilters');

    function applyFilters() {
        const term = searchInput.value.toLowerCase();
        const category = categoryFilter.value;

        rows.forEach(row => {
            const matchesSearch = !term || row.innerText.toLowerCase().includes(term);
            const matchesCategory = !category || row.dataset.category === category;
            row.style.display = matchesSearch && matchesCategory ? '' : 'none';
        });

        const tbody = document.querySelector('#servicesTable tbody');
        const visibleRows = rows.filter(row => row.style.display !== 'none');

        if (priceSort.value) {
            visibleRows.sort((a, b) => {
                const aPrice = parseFloat(a.dataset.price);
                const bPrice = parseFloat(b.dataset.price);
                return priceSort.value === 'asc' ? aPrice - bPrice : bPrice - aPrice;
            });
        }

        visibleRows.forEach(row => tbody.appendChild(row));
    }

    searchInput.addEventListener('input', applyFilters);
    categoryFilter.addEventListener('change', applyFilters);
    priceSort.addEventListener('change', applyFilters);

    resetBtn?.addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = '';
        priceSort.value = '';
        applyFilters();
    });
});
</script>

<?php
$serviceSchemas = array_slice(array_map(function ($service) {
    return [
        '@type' => 'Service',
        'name' => $service['name'],
        'serviceType' => $service['category'],
        'areaServed' => 'Nigeria',
        'provider' => [
            '@type' => 'Organization',
            'name' => SITE_NAME
        ],
        'offers' => [
            '@type' => 'Offer',
            'priceCurrency' => 'NGN',
            'price' => $service['rate_per_1000'],
            'url' => url('dashboard/new-order?service=' . $service['id']),
            'availability' => 'https://schema.org/InStock'
        ]
    ];
}, $services), 0, 50);
?>
<script type="application/ld+json">
<?= json_encode(['@context' => 'https://schema.org', '@graph' => $serviceSchemas], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
</script>
