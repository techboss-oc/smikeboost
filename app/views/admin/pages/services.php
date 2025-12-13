<?php
$providers = $providers ?? db_fetch_all("SELECT id, name FROM providers ORDER BY name ASC");
$categories = $categories ?? db_fetch_all("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
$csrf = admin_csrf_token();
$currentProvider = isset($_GET['provider_id']) ? (int)$_GET['provider_id'] : 0;
$currentStatus = $_GET['status'] ?? '';
$currentQ = trim($_GET['q'] ?? '');
?>

<section class="page">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:#fff; flex-wrap:wrap; gap:12px;">
        <h1>Services</h1>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button class="btn btn-primary" type="button" onclick="openServiceModal();"><i class="fas fa-plus"></i> Add Service</button>
            <button class="btn btn-success" type="button" onclick="openCategoryModal();"><i class="fas fa-folder-plus"></i> Create Category</button>
            <button class="btn btn-outline" type="button" style="color:#fff;" onclick="selectAllRows(true);"><i class="fas fa-check-double"></i> Select All</button>
            <button class="btn btn-outline-secondary" type="button" onclick="bulkAction('enable');">Bulk Enable</button>
            <button class="btn btn-outline-secondary" type="button" onclick="bulkAction('disable');">Bulk Disable</button>
            <button class="btn btn-outline-danger" type="button" onclick="bulkAction('delete');">Bulk Delete</button>
            <button class="btn btn-outline-info" type="button" onclick="openAdvancedImport();"><i class="fas fa-download"></i> Advanced Import</button>
            <button class="btn btn-info" type="button" onclick="openImportAllModal();"><i class="fas fa-download"></i> Import All Services</button>
        </div>
    </div>

    <div class="glass" style="margin-bottom:12px; padding:12px; color:#fff;">
        <form method="GET" action="" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="color:#e5e7eb;">Search</label>
                <input class="form-control" type="text" name="q" placeholder="Name, platform, category" value="<?php echo e($currentQ); ?>" style="min-width:200px;">
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="color:#e5e7eb;">Provider</label>
                <select name="provider_id" class="form-control" style="min-width:180px;">
                    <option value="">All providers</option>
                    <?php foreach ($providers as $p): ?>
                        <option value="<?php echo (int)$p['id']; ?>" <?php echo $currentProvider === (int)$p['id'] ? 'selected' : ''; ?>><?php echo e($p['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                <label style="color:#e5e7eb;">Status</label>
                <select name="status" class="form-control" style="min-width:140px;">
                    <option value="">All</option>
                    <option value="enabled" <?php echo $currentStatus === 'enabled' ? 'selected' : ''; ?>>Enabled</option>
                    <option value="disabled" <?php echo $currentStatus === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                </select>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
                <a class="btn btn-secondary" href="?" style="text-decoration:none;">Reset</a>
            </div>
        </form>
    </div>

    <div class="glass" style="color:#fff;">
        <?php $avgMapAdmin = function_exists('get_avg_time_map_for_services') ? get_avg_time_map_for_services($services) : []; ?>
        <?php
        $grouped = [];
        foreach ($services as $svc) {
            $cat = $svc['category'] ?? 'Uncategorized';
            if (!isset($grouped[$cat])) $grouped[$cat] = [];
            $grouped[$cat][] = $svc;
        }
        // Get category info from categories table
        $catInfo = [];
        foreach ($categories as $c) {
            $catInfo[$c['name']] = $c;
        }
        ?>

        <?php if (empty($services)): ?>
            <p style="text-align:center; color:#e5e7eb; padding:20px;">No services yet.</p>
        <?php else: ?>
            <?php foreach ($grouped as $catName => $rows):
                $catData = $catInfo[$catName] ?? null;
                $catId = $catData['id'] ?? 0;
                $catStatus = $catData['status'] ?? 'enabled';
            ?>
                <div class="category-block" style="margin-bottom:12px; border:1px solid rgba(255,255,255,0.1); border-radius:8px; overflow:hidden;">
                    <div class="category-header" style="background:rgba(255,255,255,0.06); padding:12px; display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="toggleCategoryBlock(this)">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" class="cat-check" data-category="<?php echo e($catName); ?>" data-cat-id="<?php echo $catId; ?>" onclick="event.stopPropagation(); toggleCategoryServices(this)">
                            <i class="fas fa-chevron-right category-icon" style="transition:transform 0.2s;"></i>
                            <span style="font-weight:bold; color:#9ae6b4; font-size:15px;">
                                <i class="fas fa-folder" style="margin-right:6px;"></i><?php echo e($catName); ?>
                            </span>
                            <span class="badge <?php echo $catStatus === 'enabled' ? 'badge-success' : 'badge-danger'; ?>" style="font-size:11px;"><?php echo ucfirst($catStatus); ?></span>
                            <span style="color:#94a3b8; font-size:13px;">(<?php echo count($rows); ?> services)</span>
                        </div>
                        <div style="display:flex; gap:6px;" onclick="event.stopPropagation();">
                            <?php if ($catId > 0): ?>
                                <button class="btn btn-sm btn-secondary" type="button" onclick="openEditCategoryModal(<?php echo htmlspecialchars(json_encode(['id' => $catId, 'name' => $catName, 'description' => $catData['description'] ?? '', 'sort_order' => $catData['sort_order'] ?? 0, 'status' => $catStatus])); ?>)"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="" style="margin:0; display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                                    <input type="hidden" name="action" value="toggle_category_status">
                                    <input type="hidden" name="category_id" value="<?php echo $catId; ?>">
                                    <input type="hidden" name="target_status" value="<?php echo $catStatus === 'enabled' ? 'disabled' : 'enabled'; ?>">
                                    <button class="btn btn-sm <?php echo $catStatus === 'enabled' ? 'btn-warning' : 'btn-success'; ?>" type="submit" title="<?php echo $catStatus === 'enabled' ? 'Disable' : 'Enable'; ?>"><i class="fas fa-<?php echo $catStatus === 'enabled' ? 'ban' : 'check'; ?>"></i></button>
                                </form>
                                <form method="POST" action="" style="margin:0; display:inline;" onsubmit="return confirm('Delete this category? Services will become uncategorized.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                                    <input type="hidden" name="action" value="delete_category">
                                    <input type="hidden" name="category_id" value="<?php echo $catId; ?>">
                                    <button class="btn btn-sm btn-danger" type="submit" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="category-services" style="display:none;">
                        <table class="table" style="color:#fff; margin:0;">
                            <thead>
                                <tr style="background:rgba(0,0,0,0.2);">
                                    <th style="width:32px;"></th>
                                    <th>ID</th>
                                    <th>Provider</th>
                                    <th>Platform</th>
                                    <th>Name</th>
                                    <th>Rate/1000</th>
                                    <th>Average time</th>
                                    <th>Min/Max</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rows as $s): ?>
                                    <tr class="service-row" data-category="<?php echo e($catName); ?>">
                                        <td><input type="checkbox" class="svc-check" value="<?php echo (int)$s['id']; ?>" data-category="<?php echo e($catName); ?>"></td>
                                        <td><?php echo e($s['id']); ?></td>
                                        <td><?php echo e($s['provider_name'] ?? '—'); ?></td>
                                        <td><?php echo e($s['platform']); ?></td>
                                        <td><?php echo e($s['name']); ?></td>
                                        <td><?php echo format_currency($s['rate_per_1000']); ?></td>
                                        <td><?php echo e($avgMapAdmin[(int)$s['id']] ?? '-'); ?></td>
                                        <td><?php echo number_format((int)$s['min_qty']); ?> / <?php echo number_format((int)$s['max_qty']); ?></td>
                                        <td><span class="badge <?php echo $s['status'] === 'enabled' ? 'badge-success' : 'badge-danger'; ?>"><?php echo ucfirst($s['status']); ?></span></td>
                                        <td style="display:flex; gap:6px; flex-wrap:wrap;">
                                            <button class="btn btn-sm btn-secondary" type="button" onclick='openServiceEditModal(<?php echo json_encode([
                                                                                                                                        'id' => (int)$s['id'],
                                                                                                                                        'provider_id' => (int)($s['provider_id'] ?? 0),
                                                                                                                                        'platform' => $s['platform'],
                                                                                                                                        'category' => $s['category'],
                                                                                                                                        'name' => $s['name'],
                                                                                                                                        'description' => $s['description'] ?? '',
                                                                                                                                        'rate_per_1000' => (float)$s['rate_per_1000'],
                                                                                                                                        'min_qty' => (int)$s['min_qty'],
                                                                                                                                        'max_qty' => (int)$s['max_qty'],
                                                                                                                                        'status' => $s['status'],
                                                                                                                                        'avg_time' => ($avgMapAdmin[(int)$s['id']] ?? '-')
                                                                                                                                    ]); ?>)'><i class="fas fa-edit"></i></button>
                                            <form method="POST" action="" style="margin:0;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="action" value="toggle_service_status">
                                                <input type="hidden" name="service_id" value="<?php echo (int)$s['id']; ?>">
                                                <input type="hidden" name="target_status" value="<?php echo $s['status'] === 'enabled' ? 'disabled' : 'enabled'; ?>">
                                                <button class="btn btn-sm <?php echo $s['status'] === 'enabled' ? 'btn-warning' : 'btn-success'; ?>" type="submit"><i class="fas fa-<?php echo $s['status'] === 'enabled' ? 'ban' : 'check'; ?>"></i></button>
                                            </form>
                                            <form method="POST" action="" style="margin:0;" onsubmit="return confirm('Delete this service?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="action" value="delete_service">
                                                <input type="hidden" name="service_id" value="<?php echo (int)$s['id']; ?>">
                                                <button class="btn btn-sm btn-danger" type="submit"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<div id="addServiceModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1000;">
    <div class="glass" style="padding:18px; color:#fff; width:min(720px, 96vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="margin:0;">Add Service</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeServiceModal();">Close</button>
        </div>
        <form method="POST" action="" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:10px; align-items:end;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="add_service">
            <select class="form-control" name="provider_id">
                <option value="">Provider (optional)</option>
                <?php foreach ($providers as $p): ?>
                    <option value="<?php echo (int)$p['id']; ?>"><?php echo e($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input class="form-control" name="platform" type="text" placeholder="Platform (e.g., Instagram)" required>
            <input class="form-control" name="category" type="text" placeholder="Category (e.g., Followers)" required>
            <input class="form-control" name="name" type="text" placeholder="Service name" required>
            <input class="form-control" name="description" type="text" placeholder="Short description">
            <input class="form-control" name="rate_per_1000" type="number" step="0.01" placeholder="Rate per 1000" required>
            <input class="form-control" name="min_qty" type="number" placeholder="Min qty" required>
            <input class="form-control" name="max_qty" type="number" placeholder="Max qty" required>
            <select class="form-control" name="status">
                <option value="enabled">Enabled</option>
                <option value="disabled">Disabled</option>
            </select>
            <div style="display:flex; gap:8px; grid-column:1 / -1;">
                <button class="btn btn-primary" type="submit">Save Service</button>
                <button class="btn btn-secondary" type="button" onclick="closeServiceModal();">Cancel</button>
            </div>
        </form>
    </div>
</div>

<form id="bulkForm" method="POST" action="" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
    <input type="hidden" name="action" value="bulk_action">
    <input type="hidden" name="bulk_action" id="bulk_action_field" value="">
</form>

<div id="categoryModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1000;">
    <div class="glass" style="padding:18px; color:#fff; width:min(480px, 92vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="margin:0;"><i class="fas fa-folder-plus"></i> Create Category</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeCategoryModal();">Close</button>
        </div>
        <form method="POST" action="" style="display:flex; flex-direction:column; gap:12px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="add_category">
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Category Name *</label>
                <input class="form-control" name="name" type="text" placeholder="e.g., Instagram Followers" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Description</label>
                <input class="form-control" name="description" type="text" placeholder="Optional description">
            </div>
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Sort Order</label>
                <input class="form-control" name="sort_order" type="number" value="0" placeholder="0">
            </div>
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Status</label>
                <select class="form-control" name="status">
                    <option value="enabled">Enabled</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save Category</button>
                <button class="btn btn-secondary" type="button" onclick="closeCategoryModal();">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editCategoryModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1000;">
    <div class="glass" style="padding:18px; color:#fff; width:min(480px, 92vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="margin:0;"><i class="fas fa-edit"></i> Edit Category</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeEditCategoryModal();">Close</button>
        </div>
        <form method="POST" action="" style="display:flex; flex-direction:column; gap:12px;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="update_category">
            <input type="hidden" name="category_id" id="edit_cat_id" value="">
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Category Name *</label>
                <input class="form-control" name="name" id="edit_cat_name" type="text" placeholder="e.g., Instagram Followers" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Description</label>
                <input class="form-control" name="description" id="edit_cat_description" type="text" placeholder="Optional description">
            </div>
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Sort Order</label>
                <input class="form-control" name="sort_order" id="edit_cat_sort_order" type="number" value="0" placeholder="0">
            </div>
            <div>
                <label style="display:block; margin-bottom:4px; color:#e5e7eb;">Status</label>
                <select class="form-control" name="status" id="edit_cat_status">
                    <option value="enabled">Enabled</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>
            <div style="display:flex; gap:8px;">
                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Update Category</button>
                <button class="btn btn-secondary" type="button" onclick="closeEditCategoryModal();">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="editServiceModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1000;">
    <div class="glass" style="padding:18px; color:#fff; width:min(720px, 96vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="margin:0;">Edit Service</h3>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeServiceEditModal();">Close</button>
        </div>
        <form id="editServiceForm" method="POST" action="" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:10px; align-items:end;">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="update_service">
            <input type="hidden" name="service_id" id="edit_service_id" value="">
            <select class="form-control" name="provider_id" id="edit_provider_id">
                <option value="">Provider (optional)</option>
                <?php foreach ($providers as $p): ?>
                    <option value="<?php echo (int)$p['id']; ?>"><?php echo e($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input class="form-control" name="platform" id="edit_platform" type="text" placeholder="Platform" required>
            <input class="form-control" name="category" id="edit_category" type="text" placeholder="Category" required>
            <input class="form-control" name="name" id="edit_name" type="text" placeholder="Service name" required>
            <input class="form-control" name="description" id="edit_description" type="text" placeholder="Short description">
            <input class="form-control" name="rate_per_1000" id="edit_rate" type="number" step="0.01" placeholder="Rate per 1000" required>
            <input class="form-control" name="min_qty" id="edit_min" type="number" placeholder="Min qty" required>
            <input class="form-control" name="max_qty" id="edit_max" type="number" placeholder="Max qty" required>
            <select class="form-control" name="status" id="edit_status">
                <option value="enabled">Enabled</option>
                <option value="disabled">Disabled</option>
            </select>
            <div class="glass" style="padding:10px; grid-column:1 / -1; background: rgba(255,255,255,0.05);">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:#94a3b8;">Average time</span>
                    <span id="edit_avg_time" style="font-weight:600;">-</span>
                </div>
            </div>
            <div style="display:flex; gap:8px; grid-column:1 / -1;">
                <button class="btn btn-primary" type="submit">Update Service</button>
                <button class="btn btn-secondary" type="button" onclick="closeServiceEditModal();">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="advImportModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1100;">
    <div class="glass" style="padding:18px; color:#fff; width:min(820px, 96vw); max-height:90vh; overflow:auto; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="margin:0;">Advanced Import - Select Services</h4>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeAdvancedImport();">Close</button>
        </div>
        <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:12px;">
            <div>
                <label>Provider</label>
                <select id="adv_provider" class="form-control" style="min-width:200px;">
                    <option value="">Select provider</option>
                    <?php foreach ($providers as $p): ?>
                        <option value="<?php echo (int)$p['id']; ?>"><?php echo e($p['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-primary" type="button" onclick="fetchProviderServices()">Fetch Services</button>
        </div>
        <div id="adv_services_list" style="background:rgba(255,255,255,0.05); padding:10px; border-radius:6px; max-height:55vh; overflow:auto;">
            <p style="color:#e5e7eb;">Select a provider and fetch services.</p>
        </div>
        <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center;">
            <div id="adv_count" style="color:#e5e7eb;">0 selected</div>
            <div style="display:flex; gap:8px;">
                <button class="btn btn-secondary" type="button" onclick="closeAdvancedImport()">Cancel</button>
                <button class="btn btn-primary" type="button" onclick="openMarkupModal()">Next: Set Markup</button>
            </div>
        </div>
    </div>
</div>

<div id="markupModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1200;">
    <div class="glass" style="padding:18px; color:#fff; width:min(520px, 92vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="margin:0;">Apply Markup</h4>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeMarkupModal();">Close</button>
        </div>
        <div class="form-group">
            <label><input type="radio" name="markup_type" value="percent" checked> Percentage</label>
            <label style="margin-left:12px;"><input type="radio" name="markup_type" value="fixed"> Fixed amount</label>
        </div>
        <div class="form-group">
            <label>Markup value</label>
            <input class="form-control" type="number" step="0.01" id="markup_value" value="10">
            <small style="color:#e5e7eb;">Percent example: 10 => +10%. Fixed adds currency units.</small>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-secondary" type="button" onclick="closeMarkupModal();">Back</button>
            <button class="btn btn-primary" type="button" onclick="runAdvancedImport()">Import Selected</button>
        </div>
    </div>
</div>

<div id="importAllModal" class="hidden" style="position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:1200;">
    <div class="glass" style="padding:18px; color:#fff; width:min(520px, 92vw); position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.4);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h4 style="margin:0;">Import All Services</h4>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeImportAllModal();">Close</button>
        </div>
        <div class="form-group">
            <label>Provider</label>
            <select id="import_all_provider" class="form-control">
                <option value="">Select provider</option>
                <?php foreach ($providers as $p): ?>
                    <option value="<?php echo (int)$p['id']; ?>"><?php echo e($p['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label><input type="radio" name="import_all_markup_type" value="percent" checked> Percentage Markup</label>
            <label style="margin-left:12px;"><input type="radio" name="import_all_markup_type" value="fixed"> Fixed Amount Markup</label>
        </div>
        <div class="form-group">
            <label>Markup value</label>
            <input class="form-control" type="number" step="0.01" id="import_all_markup_value" value="10">
            <small style="color:#e5e7eb;">Percent example: 10 => +10%. Fixed adds currency units.</small>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="btn btn-secondary" type="button" onclick="closeImportAllModal();">Cancel</button>
            <button class="btn btn-primary" type="button" onclick="runImportAll()">Import All Services</button>
        </div>
    </div>
</div>

<script>
    function selectAllRows(set = true) {
        document.querySelectorAll('.svc-check').forEach(cb => cb.checked = set);
        document.querySelectorAll('.cat-check').forEach(cb => cb.checked = set);
    }

    function toggleCategoryBlock(header) {
        const block = header.closest('.category-block');
        const services = block.querySelector('.category-services');
        const icon = header.querySelector('.category-icon');
        if (services.style.display === 'none') {
            services.style.display = 'block';
            icon.style.transform = 'rotate(90deg)';
        } else {
            services.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function toggleCategoryServices(el) {
        const catName = el.getAttribute('data-category');
        const checked = el.checked;
        document.querySelectorAll('.svc-check[data-category="' + catName + '"]').forEach(cb => cb.checked = checked);
    }

    function openCategoryModal() {
        var modal = document.getElementById('categoryModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }

    function closeCategoryModal() {
        var modal = document.getElementById('categoryModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    function openEditCategoryModal(data) {
        document.getElementById('edit_cat_id').value = data.id || '';
        document.getElementById('edit_cat_name').value = data.name || '';
        document.getElementById('edit_cat_description').value = data.description || '';
        document.getElementById('edit_cat_sort_order').value = data.sort_order || 0;
        document.getElementById('edit_cat_status').value = data.status || 'enabled';
        var modal = document.getElementById('editCategoryModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }

    function closeEditCategoryModal() {
        var modal = document.getElementById('editCategoryModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
    async function bulkAction(action) {
        const serviceIds = [...document.querySelectorAll('.svc-check:checked')].map(i => i.value);
        const categoryIds = [...document.querySelectorAll('.cat-check:checked')].map(i => i.getAttribute('data-cat-id')).filter(id => id && id !== '0');

        if (!serviceIds.length && !categoryIds.length) {
            alert('Select services or categories first');
            return;
        }
        if (action === 'delete' && !confirm('Delete selected items? This cannot be undone.')) return;
        if (action === 'enable' && !confirm('Enable all selected services and categories?')) return;
        if (action === 'disable' && !confirm('Disable all selected services and categories?')) return;

        const form = document.getElementById('bulkForm');
        const actionField = document.getElementById('bulk_action_field');
        actionField.value = action;

        // remove existing inputs
        form.querySelectorAll('input[name="ids[]"]').forEach(n => n.remove());
        form.querySelectorAll('input[name="cat_ids[]"]').forEach(n => n.remove());

        serviceIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = id;
            form.appendChild(inp);
        });
        categoryIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'cat_ids[]';
            inp.value = id;
            form.appendChild(inp);
        });
        form.submit();
    }

    let advServices = [];
    let advSelected = new Set();

    function openAdvancedImport() {
        advSelected.clear();
        document.getElementById('adv_services_list').innerHTML = '<p style="color:#e5e7eb;">Select a provider and fetch services.</p>';
        document.getElementById('adv_count').textContent = '0 selected';
        document.getElementById('advImportModal').style.display = 'flex';
        document.getElementById('advImportModal').classList.remove('hidden');
    }

    function closeAdvancedImport() {
        document.getElementById('advImportModal').style.display = 'none';
        document.getElementById('advImportModal').classList.add('hidden');
    }

    function closeMarkupModal() {
        document.getElementById('markupModal').style.display = 'none';
        document.getElementById('markupModal').classList.add('hidden');
    }

    function openImportAllModal() {
        document.getElementById('importAllModal').style.display = 'flex';
        document.getElementById('importAllModal').classList.remove('hidden');
    }

    function closeImportAllModal() {
        document.getElementById('importAllModal').style.display = 'none';
        document.getElementById('importAllModal').classList.add('hidden');
    }

    async function runImportAll() {
        const pid = document.getElementById('import_all_provider').value;
        if (!pid) {
            alert('Select provider');
            return;
        }
        const markupType = document.querySelector('input[name="import_all_markup_type"]:checked').value;
        const markupVal = document.getElementById('import_all_markup_value').value || '0';
        
        const fd = new FormData();
        fd.append('csrf_token', '<?php echo $csrf; ?>');
        fd.append('action', 'import_all_services');
        fd.append('provider_id', pid);
        fd.append('markup_type', markupType);
        fd.append('markup_value', markupVal);
        
        const res = await fetch('', {
            method: 'POST',
            body: fd
        });
        
        if (res.ok) {
            closeImportAllModal();
            location.reload();
        } else {
            alert('Import failed');
        }
    }

    function openMarkupModal() {
        if (advSelected.size === 0) {
            alert('Select at least one service.');
            return;
        }
        document.getElementById('markupModal').style.display = 'flex';
        document.getElementById('markupModal').classList.remove('hidden');
    }

    async function fetchProviderServices() {
        const pid = document.getElementById('adv_provider').value;
        if (!pid) {
            alert('Select provider');
            return;
        }
        const fd = new FormData();
        fd.append('csrf_token', '<?php echo $csrf; ?>');
        fd.append('action', 'preview_provider_services');
        fd.append('provider_id', pid);
        const res = await fetch('', {
            method: 'POST',
            body: fd
        });
        const data = await res.json();
        if (data.error) {
            alert(data.error);
            return;
        }
        advServices = data.services || [];
        advSelected.clear();
        renderAdvServices();
    }

    function renderAdvServices() {
        const wrap = document.getElementById('adv_services_list');
        if (!advServices.length) {
            wrap.innerHTML = '<p style="color:#e5e7eb;">No services from provider.</p>';
            updateAdvCount();
            return;
        }

        // Group services by category
        const grouped = {};
        advServices.forEach(s => {
            const cat = s.category || 'Uncategorized';
            if (!grouped[cat]) grouped[cat] = [];
            grouped[cat].push(s);
        });

        const categoryNames = Object.keys(grouped).sort();

        let html = '<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">';
        html += '<div><strong>' + advServices.length + '</strong> services in <strong>' + categoryNames.length + '</strong> categories</div>';
        html += '<div style="display:flex; gap:6px;">';
        html += '<button class="btn btn-sm btn-outline" type="button" style="color:#fff;" onclick="toggleAdvAll(true)">Select All</button>';
        html += '<button class="btn btn-sm btn-outline" type="button" style="color:#fff;" onclick="toggleAdvAll(false)">Deselect All</button>';
        html += '</div></div>';

        categoryNames.forEach(catName => {
            const services = grouped[catName];
            const catId = 'adv_cat_' + catName.replace(/[^a-zA-Z0-9]/g, '_');
            const allInCatSelected = services.every(s => advSelected.has(String(s.id)));

            html += '<div class="adv-category-block" style="margin-bottom:10px; border:1px solid rgba(255,255,255,0.1); border-radius:8px; overflow:hidden;">';
            html += '<div class="adv-cat-header" style="background:rgba(255,255,255,0.06); padding:10px; display:flex; justify-content:space-between; align-items:center; cursor:pointer;" onclick="toggleAdvCategoryBlock(\'' + catId + '\')">';
            html += '<div style="display:flex; align-items:center; gap:8px;">';
            html += '<input type="checkbox" class="adv-cat-check" data-category="' + escapeHtml(catName) + '" ' + (allInCatSelected ? 'checked' : '') + ' onclick="event.stopPropagation(); toggleAdvCategory(this)">';
            html += '<i class="fas fa-chevron-right adv-cat-icon" id="icon_' + catId + '" style="transition:transform 0.2s;"></i>';
            html += '<span style="font-weight:bold; color:#9ae6b4;"><i class="fas fa-folder" style="margin-right:6px;"></i>' + escapeHtml(catName) + '</span>';
            html += '<span style="color:#94a3b8; font-size:12px;">(' + services.length + ' services)</span>';
            html += '</div></div>';
            html += '<div class="adv-cat-services" id="' + catId + '" style="display:none; padding:8px; background:rgba(0,0,0,0.1);">';

            services.forEach(s => {
                const checked = advSelected.has(String(s.id)) ? 'checked' : '';
                html += '<label style="display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.04); padding:8px; border-radius:6px; margin-bottom:4px;">';
                html += '<input type="checkbox" class="adv-svc-check" data-sid="' + s.id + '" data-category="' + escapeHtml(catName) + '" onchange="toggleAdv(this)" ' + checked + '>';
                html += '<div style="flex:1; min-width:0;">';
                html += '<div style="font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + escapeHtml(s.name || '') + '</div>';
                html += '<div style="font-size:11px; color:#94a3b8;">' + escapeHtml(s.platform || '') + ' • Min: ' + (s.min || 0) + ' • Max: ' + (s.max || 0) + ' • Rate: ' + (s.rate || 0) + '</div>';
                html += '</div></label>';
            });

            html += '</div></div>';
        });

        wrap.innerHTML = html;
        updateAdvCount();
    }

    function toggleAdvCategoryBlock(catId) {
        const el = document.getElementById(catId);
        const icon = document.getElementById('icon_' + catId);
        if (el.style.display === 'none') {
            el.style.display = 'block';
            if (icon) icon.style.transform = 'rotate(90deg)';
        } else {
            el.style.display = 'none';
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    function toggleAdvCategory(el) {
        const catName = el.getAttribute('data-category');
        const checked = el.checked;
        advServices.filter(s => (s.category || 'Uncategorized') === catName).forEach(s => {
            if (checked) advSelected.add(String(s.id));
            else advSelected.delete(String(s.id));
        });
        // Update checkboxes in this category
        document.querySelectorAll('.adv-svc-check[data-category="' + catName + '"]').forEach(cb => cb.checked = checked);
        updateAdvCount();
    }

    function toggleAdvAll(set) {
        advSelected = new Set(set ? advServices.map(s => String(s.id)) : []);
        renderAdvServices();
    }

    function toggleAdv(el) {
        const id = el.getAttribute('data-sid');
        const catName = el.getAttribute('data-category');
        if (el.checked) advSelected.add(String(id));
        else advSelected.delete(String(id));

        // Update category checkbox based on whether all services in that category are selected
        const catServices = advServices.filter(s => (s.category || 'Uncategorized') === catName);
        const allSelected = catServices.every(s => advSelected.has(String(s.id)));
        const catCheckbox = document.querySelector('.adv-cat-check[data-category="' + catName + '"]');
        if (catCheckbox) catCheckbox.checked = allSelected;

        updateAdvCount();
    }

    function updateAdvCount() {
        document.getElementById('adv_count').textContent = advSelected.size + ' selected';
    }

    async function runAdvancedImport() {
        const pid = document.getElementById('adv_provider').value;
        if (!pid) {
            alert('Select provider');
            return;
        }
        if (advSelected.size === 0) {
            alert('Select services');
            return;
        }
        const markupType = document.querySelector('input[name="markup_type"]:checked').value;
        const markupVal = document.getElementById('markup_value').value || '0';
        const fd = new FormData();
        fd.append('csrf_token', '<?php echo $csrf; ?>');
        fd.append('action', 'import_selected_services');
        fd.append('provider_id', pid);
        fd.append('markup_type', markupType);
        fd.append('markup_value', markupVal);
        const selectedArray = [...advSelected];
        console.log('Sending service IDs:', selectedArray);
        fd.append('service_ids', JSON.stringify(selectedArray));
        const res = await fetch('', {
            method: 'POST',
            body: fd
        });
        if (res.ok) {
            closeMarkupModal();
            closeAdvancedImport();
            location.reload();
        } else {
            alert('Import failed');
        }
    }

    function escapeHtml(str) {
        if (str === undefined || str === null) return '';
        return String(str).replace(/[&<>'"]/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
    }

    function openServiceModal() {
        var modal = document.getElementById('addServiceModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
    }

    function closeServiceModal() {
        var modal = document.getElementById('addServiceModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }
    const avgTimeAdminMap = <?php echo json_encode($avgMapAdmin); ?>;

    function openServiceEditModal(data) {
        var modal = document.getElementById('editServiceModal');
        modal.style.display = 'flex';
        modal.classList.remove('hidden');
        document.getElementById('edit_service_id').value = data.id || '';
        document.getElementById('edit_provider_id').value = data.provider_id || '';
        document.getElementById('edit_platform').value = data.platform || '';
        document.getElementById('edit_category').value = data.category || '';
        document.getElementById('edit_name').value = data.name || '';
        document.getElementById('edit_description').value = data.description || '';
        document.getElementById('edit_rate').value = data.rate_per_1000 || '';
        document.getElementById('edit_min').value = data.min_qty || '';
        document.getElementById('edit_max').value = data.max_qty || '';
        document.getElementById('edit_status').value = data.status || 'enabled';
        var avg = data.avg_time || avgTimeAdminMap[(data.id || 0)] || '-';
        document.getElementById('edit_avg_time').textContent = avg || '-';
    }

    function closeServiceEditModal() {
        var modal = document.getElementById('editServiceModal');
        modal.style.display = 'none';
        modal.classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        var addModal = document.getElementById('addServiceModal');
        if (!addModal.classList.contains('hidden') && e.target === addModal) {
            closeServiceModal();
        }
        var editModal = document.getElementById('editServiceModal');
        if (!editModal.classList.contains('hidden') && e.target === editModal) {
            closeServiceEditModal();
        }
        var catModal = document.getElementById('categoryModal');
        if (!catModal.classList.contains('hidden') && e.target === catModal) {
            closeCategoryModal();
        }
        var editCatModal = document.getElementById('editCategoryModal');
        if (!editCatModal.classList.contains('hidden') && e.target === editCatModal) {
            closeEditCategoryModal();
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeServiceModal();
            closeServiceEditModal();
            closeAdvancedImport();
            closeMarkupModal();
            closeCategoryModal();
            closeEditCategoryModal();
        }
    });
</script>