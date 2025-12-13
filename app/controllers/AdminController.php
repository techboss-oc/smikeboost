<?php
require_once APP_PATH . '/config/admin.php';
require_once APP_PATH . '/helpers/admin_helpers.php';
require_once APP_PATH . '/models/Transaction.php';
require_once APP_PATH . '/models/User.php';

class AdminController
{
    private $transactionModel;
    private $userModel;

    public function __construct()
    {
        if (!admin_ip_allowed()) {
            http_response_code(403);
            exit('Access denied');
        }

        $this->transactionModel = new Transaction();
        $this->userModel = new User();
    }

    public function login()
    {
        if (admin_is_logged_in()) {
            redirect('admin');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!admin_rate_limit_ok()) {
                admin_flash('error', 'Too many attempts, please wait a minute.');
                redirect('admin/login');
            }

            $identity = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $csrf = $_POST['csrf_token'] ?? '';

            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid session. Please try again.');
                redirect('admin/login');
            }

            $adminUser = null;
            if ($identity !== '') {
                try {
                    $adminUser = $this->userModel->findAdminByIdentity($identity);
                } catch (Exception $e) {
                    error_log('Admin login lookup failed: ' . $e->getMessage());
                }
            }

            if ($adminUser && !empty($adminUser['password_hash']) && verify_password($password, $adminUser['password_hash'])) {
                admin_login([
                    'id' => $adminUser['id'],
                    'username' => $adminUser['username'],
                    'email' => $adminUser['email'],
                    'role' => $adminUser['role'] ?? 'admin'
                ]);
                redirect('admin');
            }

            // Fallback to config-defined credentials to prevent lockout if DB user is missing
            if (($identity === ADMIN_USERNAME || $identity === ADMIN_EMAIL) && verify_password($password, ADMIN_PASSWORD_HASH)) {
                admin_login([
                    'username' => ADMIN_USERNAME,
                    'email' => ADMIN_EMAIL,
                    'role' => 'superadmin'
                ]);
                redirect('admin');
            }

            admin_flash('error', 'Invalid credentials');
        }

        $page = 'login';
        require VIEWS_PATH . '/admin/pages/login.php';
    }

    public function logout()
    {
        admin_logout();
        redirect('admin/login');
    }

    public function dashboard()
    {
        admin_require_login();
        $page = 'dashboard';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function orders()
    {
        admin_require_login();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'sync_order') {
                $this->syncOrder();
                exit;
            }
        }

        $page = 'orders';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function users()
    {
        admin_require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid session token.');
                redirect('admin/users');
            }

            $action = $_POST['action'] ?? '';

            if ($action === 'bulk_action') {
                $bulk = $_POST['bulk_action'] ?? '';
                $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
                $catIds = array_filter(array_map('intval', $_POST['cat_ids'] ?? []));

                if (empty($ids) && empty($catIds)) {
                    admin_flash('error', 'No services or categories selected.');
                    redirect('admin/services');
                }

                // Handle services
                if (!empty($ids)) {
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    if ($bulk === 'enable' || $bulk === 'disable') {
                        $status = $bulk === 'enable' ? 'enabled' : 'disabled';
                        db_execute("UPDATE services SET status = '{$status}' WHERE id IN ({$in})", $ids);
                    } elseif ($bulk === 'delete') {
                        db_execute("UPDATE services SET deleted_at = NOW() WHERE id IN ({$in})", $ids);
                    }
                }

                // Handle categories
                if (!empty($catIds)) {
                    $catIn = implode(',', array_fill(0, count($catIds), '?'));
                    if ($bulk === 'enable' || $bulk === 'disable') {
                        $status = $bulk === 'enable' ? 'enabled' : 'disabled';
                        db_execute("UPDATE categories SET status = '{$status}' WHERE id IN ({$catIn})", $catIds);
                        // Also update all services in these categories
                        $catNames = db_fetch_all("SELECT name FROM categories WHERE id IN ({$catIn})", $catIds);
                        foreach ($catNames as $cat) {
                            db_execute("UPDATE services SET status = :status WHERE category = :cat AND deleted_at IS NULL", ['status' => $status, 'cat' => $cat['name']]);
                        }
                    } elseif ($bulk === 'delete') {
                        // Delete categories and set services to uncategorized
                        $catNames = db_fetch_all("SELECT name FROM categories WHERE id IN ({$catIn})", $catIds);
                        foreach ($catNames as $cat) {
                            db_execute("UPDATE services SET category = 'Uncategorized' WHERE category = :cat", ['cat' => $cat['name']]);
                        }
                        db_execute("DELETE FROM categories WHERE id IN ({$catIn})", $catIds);
                    }
                }

                admin_flash('success', 'Bulk action applied successfully.');
                redirect('admin/services');
            }

            if ($action === 'update_category') {
                $catId = (int)($_POST['category_id'] ?? 0);
                $name = sanitize($_POST['name'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $status = ($_POST['status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$catId || !$name) {
                    admin_flash('error', 'Category ID and name are required.');
                    redirect('admin/services');
                }

                // Get old category name to update services
                $oldCat = db_fetch("SELECT name FROM categories WHERE id = :id LIMIT 1", ['id' => $catId]);
                if ($oldCat && $oldCat['name'] !== $name) {
                    db_execute("UPDATE services SET category = :new WHERE category = :old", ['new' => $name, 'old' => $oldCat['name']]);
                }

                db_execute(
                    "UPDATE categories SET name = :name, description = :description, sort_order = :sort_order, status = :status WHERE id = :id",
                    [
                        'name' => $name,
                        'description' => $description,
                        'sort_order' => $sort_order,
                        'status' => $status,
                        'id' => $catId,
                    ]
                );
                admin_flash('success', 'Category updated successfully.');
                redirect('admin/services');
            }

            if ($action === 'toggle_category_status') {
                $catId = (int)($_POST['category_id'] ?? 0);
                $targetStatus = ($_POST['target_status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$catId) {
                    admin_flash('error', 'Invalid category.');
                    redirect('admin/services');
                }

                db_execute("UPDATE categories SET status = :status WHERE id = :id", ['status' => $targetStatus, 'id' => $catId]);
                admin_flash('success', 'Category status updated.');
                redirect('admin/services');
            }

            if ($action === 'delete_category') {
                $catId = (int)($_POST['category_id'] ?? 0);

                if (!$catId) {
                    admin_flash('error', 'Invalid category.');
                    redirect('admin/services');
                }

                $cat = db_fetch("SELECT name FROM categories WHERE id = :id LIMIT 1", ['id' => $catId]);
                if ($cat) {
                    db_execute("UPDATE services SET category = 'Uncategorized' WHERE category = :name", ['name' => $cat['name']]);
                    db_execute("DELETE FROM categories WHERE id = :id", ['id' => $catId]);
                }
                admin_flash('success', 'Category deleted. Services moved to Uncategorized.');
                redirect('admin/services');
            }

            if ($action === 'add_category') {
                $name = sanitize($_POST['name'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $status = ($_POST['status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$name) {
                    admin_flash('error', 'Category name is required.');
                    redirect('admin/services');
                }

                $existing = db_fetch("SELECT id FROM categories WHERE name = :name LIMIT 1", ['name' => $name]);
                if ($existing) {
                    admin_flash('error', 'Category already exists.');
                    redirect('admin/services');
                }

                db_execute(
                    "INSERT INTO categories (name, description, sort_order, status) VALUES (:name, :description, :sort_order, :status)",
                    [
                        'name' => $name,
                        'description' => $description,
                        'sort_order' => $sort_order,
                        'status' => $status,
                    ]
                );
                admin_flash('success', 'Category created successfully.');
                redirect('admin/services');
            }

            if ($action === 'add_user') {
                $name = sanitize($_POST['name'] ?? '');
                $email = sanitize($_POST['email'] ?? '');
                $username = sanitize($_POST['username'] ?? '');
                $password = $_POST['password'] ?? 'password123';
                $status = ($_POST['status'] ?? 'active') === 'suspended' ? 'suspended' : 'active';

                if (!$name || !$email || !$username || !validate_email($email) || !validate_username($username)) {
                    admin_flash('error', 'Please provide valid name, email, and username.');
                    redirect('admin/users');
                }

                try {
                    db_execute(
                        "INSERT INTO users (name, username, email, password_hash, role, status, wallet_balance) VALUES (:name, :username, :email, :password_hash, 'user', :status, 0)",
                        [
                            'name' => $name,
                            'username' => $username,
                            'email' => $email,
                            'password_hash' => hash_password($password),
                            'status' => $status,
                        ]
                    );
                    admin_flash('success', 'User added.');
                } catch (Exception $e) {
                    admin_flash('error', 'Failed to add user: ' . $e->getMessage());
                }
                redirect('admin/users');
            }

            if ($action === 'edit_user') {
                $id = (int)($_POST['id'] ?? 0);
                $status = ($_POST['status'] ?? 'active') === 'suspended' ? 'suspended' : 'active';
                if ($id > 0) {
                    db_execute("UPDATE users SET status = :status WHERE id = :id", ['status' => $status, 'id' => $id]);
                    admin_flash('success', 'User updated.');
                }
                redirect('admin/users');
            }

            if ($action === 'delete_user') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    db_execute("DELETE FROM users WHERE id = :id AND role != 'admin'", ['id' => $id]);
                    admin_flash('success', 'User deleted.');
                }
                redirect('admin/users');
            }
        }

        $page = 'users';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function userEdit()
    {
        admin_require_login();
        $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
        if ($id <= 0) {
            admin_flash('error', 'User not found.');
            redirect('admin/users');
        }

        $user = db_fetch("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);
        if (!$user) {
            admin_flash('error', 'User not found.');
            redirect('admin/users');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid session token.');
                redirect('admin/user-edit?id=' . $id);
            }

            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $username = sanitize($_POST['username'] ?? '');
            $status = ($_POST['status'] ?? 'active') === 'suspended' ? 'suspended' : 'active';
            $role = ($_POST['role'] ?? 'user') === 'admin' ? 'admin' : 'user';
            $wallet = (float)($_POST['wallet_balance'] ?? $user['wallet_balance']);
            $password = $_POST['password'] ?? '';

            if (!$name || !$email || !$username || !validate_email($email) || !validate_username($username)) {
                admin_flash('error', 'Please provide valid name, email, and username.');
                redirect('admin/user-edit?id=' . $id);
            }

            try {
                db_execute(
                    "UPDATE users SET name = :name, email = :email, username = :username, status = :status, role = :role, wallet_balance = :wallet WHERE id = :id",
                    [
                        'name' => $name,
                        'email' => $email,
                        'username' => $username,
                        'status' => $status,
                        'role' => $role,
                        'wallet' => $wallet,
                        'id' => $id,
                    ]
                );

                if (!empty($password)) {
                    db_execute("UPDATE users SET password_hash = :ph WHERE id = :id", ['ph' => hash_password($password), 'id' => $id]);
                }

                admin_flash('success', 'User updated.');
            } catch (Exception $e) {
                admin_flash('error', 'Update failed: ' . $e->getMessage());
            }

            redirect('admin/user-edit?id=' . $id);
        }

        $page = 'user-edit';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function impersonate()
    {
        admin_require_login();
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            admin_flash('error', 'User not found.');
            redirect('admin/users');
        }
        $user = db_fetch("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $id]);
        if (!$user) {
            admin_flash('error', 'User not found.');
            redirect('admin/users');
        }
        // Start user session without dropping admin session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'] ?? '',
            'email' => $user['email'],
            'role' => $user['role'] ?? 'user',
        ];
        flash('success', 'You are now logged in as ' . $user['username'] . '.');
        header('Location: ' . public_url('dashboard'));
        exit;
    }

    public function services()
    {
        admin_require_login();
        $providerIdFilter = isset($_GET['provider_id']) ? (int)$_GET['provider_id'] : null;
        $statusFilter = $_GET['status'] ?? '';
        $qFilter = trim($_GET['q'] ?? '');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid session token.');
                redirect('admin/services');
            }

            $action = $_POST['action'] ?? '';

            // Category actions
            if ($action === 'delete_category') {
                $catId = (int)($_POST['category_id'] ?? 0);

                if (!$catId) {
                    admin_flash('error', 'Invalid category.');
                    redirect('admin/services');
                }

                $cat = db_fetch("SELECT name FROM categories WHERE id = :id LIMIT 1", ['id' => $catId]);
                if ($cat) {
                    db_execute("UPDATE services SET category = 'Uncategorized' WHERE category = :name", ['name' => $cat['name']]);
                    db_execute("DELETE FROM categories WHERE id = :id", ['id' => $catId]);
                    admin_flash('success', 'Category deleted. Services moved to Uncategorized.');
                } else {
                    admin_flash('error', 'Category not found.');
                }
                redirect('admin/services');
            }

            if ($action === 'toggle_category_status') {
                $catId = (int)($_POST['category_id'] ?? 0);
                $targetStatus = ($_POST['target_status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$catId) {
                    admin_flash('error', 'Invalid category.');
                    redirect('admin/services');
                }

                db_execute("UPDATE categories SET status = :status WHERE id = :id", ['status' => $targetStatus, 'id' => $catId]);
                admin_flash('success', 'Category status updated.');
                redirect('admin/services');
            }

            if ($action === 'update_category') {
                $catId = (int)($_POST['category_id'] ?? 0);
                $name = sanitize($_POST['name'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $status = ($_POST['status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$catId || !$name) {
                    admin_flash('error', 'Category ID and name are required.');
                    redirect('admin/services');
                }

                $oldCat = db_fetch("SELECT name FROM categories WHERE id = :id LIMIT 1", ['id' => $catId]);
                if ($oldCat && $oldCat['name'] !== $name) {
                    db_execute("UPDATE services SET category = :new WHERE category = :old", ['new' => $name, 'old' => $oldCat['name']]);
                }

                db_execute(
                    "UPDATE categories SET name = :name, description = :description, sort_order = :sort_order, status = :status WHERE id = :id",
                    ['name' => $name, 'description' => $description, 'sort_order' => $sort_order, 'status' => $status, 'id' => $catId]
                );
                admin_flash('success', 'Category updated successfully.');
                redirect('admin/services');
            }

            if ($action === 'add_category') {
                $name = sanitize($_POST['name'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $status = ($_POST['status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$name) {
                    admin_flash('error', 'Category name is required.');
                    redirect('admin/services');
                }

                $existing = db_fetch("SELECT id FROM categories WHERE name = :name LIMIT 1", ['name' => $name]);
                if ($existing) {
                    admin_flash('error', 'Category already exists.');
                    redirect('admin/services');
                }

                db_execute(
                    "INSERT INTO categories (name, description, sort_order, status) VALUES (:name, :description, :sort_order, :status)",
                    ['name' => $name, 'description' => $description, 'sort_order' => $sort_order, 'status' => $status]
                );
                admin_flash('success', 'Category created successfully.');
                redirect('admin/services');
            }

            if ($action === 'bulk_action') {
                $bulk = $_POST['bulk_action'] ?? '';
                $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));
                $catIds = array_filter(array_map('intval', $_POST['cat_ids'] ?? []));

                if (empty($ids) && empty($catIds)) {
                    admin_flash('error', 'No services or categories selected.');
                    redirect('admin/services');
                }

                if (!empty($ids)) {
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    if ($bulk === 'enable' || $bulk === 'disable') {
                        $status = $bulk === 'enable' ? 'enabled' : 'disabled';
                        db_execute("UPDATE services SET status = '{$status}' WHERE id IN ({$in})", $ids);
                    } elseif ($bulk === 'delete') {
                        db_execute("UPDATE services SET deleted_at = NOW() WHERE id IN ({$in})", $ids);
                    }
                }

                if (!empty($catIds)) {
                    $catIn = implode(',', array_fill(0, count($catIds), '?'));
                    if ($bulk === 'enable' || $bulk === 'disable') {
                        $status = $bulk === 'enable' ? 'enabled' : 'disabled';
                        db_execute("UPDATE categories SET status = '{$status}' WHERE id IN ({$catIn})", $catIds);
                        $catNames = db_fetch_all("SELECT name FROM categories WHERE id IN ({$catIn})", $catIds);
                        foreach ($catNames as $cat) {
                            db_execute("UPDATE services SET status = :status WHERE category = :cat AND deleted_at IS NULL", ['status' => $status, 'cat' => $cat['name']]);
                        }
                    } elseif ($bulk === 'delete') {
                        $catNames = db_fetch_all("SELECT name FROM categories WHERE id IN ({$catIn})", $catIds);
                        foreach ($catNames as $cat) {
                            db_execute("UPDATE services SET category = 'Uncategorized' WHERE category = :cat", ['cat' => $cat['name']]);
                        }
                        db_execute("DELETE FROM categories WHERE id IN ({$catIn})", $catIds);
                    }
                }

                admin_flash('success', 'Bulk action applied successfully.');
                redirect('admin/services');
            }

            if ($action === 'preview_provider_services') {
                $providerId = (int)($_POST['provider_id'] ?? 0);
                header('Content-Type: application/json');
                if ($providerId <= 0) {
                    echo json_encode(['error' => 'Select a provider.']);
                    exit;
                }
                $provider = db_fetch("SELECT * FROM providers WHERE id = :id LIMIT 1", ['id' => $providerId]);
                if (!$provider) {
                    echo json_encode(['error' => 'Provider not found.']);
                    exit;
                }
                $resp = admin_provider_request($provider, 'services', ['limit' => 1000]);
                if (isset($resp['error'])) {
                    echo json_encode(['error' => $resp['error']]);
                    exit;
                }
                if (!is_array($resp)) {
                    echo json_encode(['error' => 'Unexpected response']);
                    exit;
                }
                $list = [];
                foreach ($resp as $item) {
                    if (!is_array($item)) continue;
                    $category = $item['category'] ?? ($item['cat'] ?? ($item['type'] ?? 'Uncategorized'));
                    $list[] = [
                        'id' => $item['service'] ?? ($item['id'] ?? null),
                        'name' => $item['name'] ?? 'Unknown',
                        'category' => $category ?: 'Uncategorized',
                        'platform' => $item['type'] ?? ($item['platform'] ?? ''),
                        'rate' => isset($item['rate']) ? (float)$item['rate'] : (float)($item['price'] ?? 0),
                        'min' => (int)($item['min'] ?? 0),
                        'max' => (int)($item['max'] ?? 0),
                        'disabled' => !empty($item['disabled']),
                    ];
                }
                echo json_encode(['services' => $list]);
                exit;
            }

            if ($action === 'add_service') {
                $provider_id = (int)($_POST['provider_id'] ?? 0) ?: null;
                $platform = sanitize($_POST['platform'] ?? '');
                $category = sanitize($_POST['category'] ?? '');
                $name = sanitize($_POST['name'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $rate = (float)($_POST['rate_per_1000'] ?? 0);
                $min = (int)($_POST['min_qty'] ?? 0);
                $max = (int)($_POST['max_qty'] ?? 0);
                $status = ($_POST['status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if (!$platform || !$category || !$name || $rate <= 0 || $min <= 0 || $max <= 0) {
                    admin_flash('error', 'Please fill all required fields.');
                    redirect('admin/services');
                }

                db_execute(
                    "INSERT INTO services (provider_id, platform, category, name, description, rate_per_1000, min_qty, max_qty, status) VALUES (:provider_id, :platform, :category, :name, :description, :rate, :min_qty, :max_qty, :status)",
                    [
                        'provider_id' => $provider_id,
                        'platform' => $platform,
                        'category' => $category,
                        'name' => $name,
                        'description' => $description,
                        'rate' => $rate,
                        'min_qty' => $min,
                        'max_qty' => $max,
                        'status' => $status,
                    ]
                );
                admin_flash('success', 'Service added.');
                redirect('admin/services');
            }

            if ($action === 'import_selected_services') {
                $providerId = (int)($_POST['provider_id'] ?? 0);
                $markupType = $_POST['markup_type'] === 'fixed' ? 'fixed' : 'percent';
                $markupVal = (float)($_POST['markup_value'] ?? 0);
                $selectedRaw = $_POST['service_ids'] ?? '[]';
                $selected = json_decode($selectedRaw, true) ?? [];
                $selected = array_filter(array_map('trim', (array)$selected));

                error_log("Import selected services - Provider: $providerId, Selected count: " . count($selected));
                error_log("Selected service IDs: " . implode(', ', $selected));
                error_log("Selected types: " . implode(', ', array_map('gettype', $selected)));

                if ($providerId <= 0 || empty($selected)) {
                    admin_flash('error', 'Select provider and services.');
                    redirect('admin/services');
                }
                $provider = db_fetch("SELECT * FROM providers WHERE id = :id LIMIT 1", ['id' => $providerId]);
                if (!$provider) {
                    admin_flash('error', 'Provider not found.');
                    redirect('admin/services');
                }
                $resp = admin_provider_request($provider, 'services', ['limit' => 1000]);
                if (isset($resp['error']) || !is_array($resp)) {
                    admin_flash('error', 'Import failed from provider.');
                    redirect('admin/services');
                }

                error_log("Import API Response count: " . count($resp));
                if (!empty($resp)) {
                    error_log("Import First API item: " . json_encode($resp[0]));
                }

                $selected = array_map(function ($id) {
                    return is_scalar($id) ? trim((string)$id) : '';
                }, $selected);
                $selected = array_filter($selected);
                $selectedMap = array_flip($selected);

                // Also create normalized versions for better matching
                $selectedNormalized = [];
                foreach ($selected as $id) {
                    $selectedNormalized[] = $id;
                    $selectedNormalized[] = (string)(int)$id; // Try as integer string
                    $selectedNormalized[] = (string)$id; // Ensure string
                }
                $selectedNormalized = array_unique($selectedNormalized);
                $added = 0;
                $updated = 0;
                $categoriesCreated = 0;

                foreach ($resp as $item) {
                    if (!is_array($item)) continue;
                    $apiId = $item['service'] ?? ($item['id'] ?? null);
                    if ($apiId === null || !is_scalar($apiId)) {
                        continue;
                    }
                    $apiId = (string)$apiId;
                    error_log("Checking API ID: '$apiId' (type: " . gettype($apiId) . ", original: " . json_encode($item['service'] ?? $item['id']) . ")");

                    // Check multiple variations of the ID
                    $idMatches = false;
                    if (isset($selectedMap[$apiId])) {
                        $idMatches = true;
                    } elseif (isset($selectedMap[(string)(int)$apiId])) {
                        $idMatches = true;
                    } elseif (in_array($apiId, $selectedNormalized, true)) {
                        $idMatches = true;
                    }

                    if (!$idMatches) {
                        error_log("Service ID '$apiId' not in selected list. Selected: " . implode(', ', $selected));
                        continue;
                    }

                    error_log("Processing service ID $apiId: " . ($item['name'] ?? 'Unknown'));

                    $platform = $item['type'] ?? ($item['platform'] ?? 'generic');
                    $category = $item['category'] ?? ($item['cat'] ?? ($item['type'] ?? 'Uncategorized'));
                    if (empty($category)) $category = 'Uncategorized';

                    $name = $item['name'] ?? '';
                    if (!$name) continue;
                    $rate = isset($item['rate']) ? (float)$item['rate'] : (float)($item['price'] ?? 0);
                    if ($markupType === 'percent') {
                        $rate = $rate * (1 + ($markupVal / 100));
                    } else {
                        $rate = $rate + $markupVal;
                    }
                    $min = (int)($item['min'] ?? 0);
                    $max = (int)($item['max'] ?? 0);
                    $status = (!empty($item['disabled']) && $item['disabled']) ? 'disabled' : 'enabled';
                    if ($rate <= 0 || $min <= 0 || $max <= 0) {
                        continue;
                    }

                    // Ensure category exists in categories table
                    $existingCat = db_fetch("SELECT id FROM categories WHERE name = :name LIMIT 1", ['name' => $category]);
                    if (!$existingCat) {
                        db_execute(
                            "INSERT INTO categories (name, description, sort_order, status) VALUES (:name, '', 0, 'enabled')",
                            ['name' => $category]
                        );
                        $categoriesCreated++;
                    }

                    $existing = db_fetch(
                        "SELECT id FROM services WHERE provider_id = :pid AND api_service_id = :asid AND deleted_at IS NULL LIMIT 1",
                        ['pid' => $providerId, 'asid' => $apiId]
                    );

                    if ($existing) {
                        try {
                            db_execute(
                                "UPDATE services SET platform=:platform, category=:category, name=:name, description=:description, rate_per_1000=:rate, min_qty=:min, max_qty=:max, status=:status WHERE id=:id",
                                [
                                    'platform' => $platform,
                                    'category' => $category,
                                    'name' => $name,
                                    'description' => $item['description'] ?? '',
                                    'rate' => $rate,
                                    'min' => $min,
                                    'max' => $max,
                                    'status' => $status,
                                    'id' => $existing['id'],
                                ]
                            );
                            $updated++;
                        } catch (Exception $e) {
                            error_log("Error updating service $apiId: " . $e->getMessage());
                        }
                    } else {
                        try {
                            db_execute(
                                "INSERT INTO services (provider_id, api_service_id, platform, category, name, description, rate_per_1000, min_qty, max_qty, status) VALUES (:provider_id, :api_service_id, :platform, :category, :name, :description, :rate, :min, :max, :status)",
                                [
                                    'provider_id' => $providerId,
                                    'api_service_id' => $apiId,
                                    'platform' => $platform,
                                    'category' => $category,
                                    'name' => $name,
                                    'description' => $item['description'] ?? '',
                                    'rate' => $rate,
                                    'min' => $min,
                                    'max' => $max,
                                    'status' => $status,
                                ]
                            );
                            $added++;
                        } catch (Exception $e) {
                            error_log("Error inserting service $apiId: " . $e->getMessage());
                        }
                    }
                }
                $msg = "Imported {$added} new, updated {$updated} services.";
                if ($categoriesCreated > 0) {
                    $msg .= " Created {$categoriesCreated} new categories.";
                }
                admin_flash('success', $msg);
                redirect('admin/services');
            }

            if ($action === 'import_all_services') {
                $providerId = (int)($_POST['provider_id'] ?? 0);
                $markupType = $_POST['markup_type'] === 'fixed' ? 'fixed' : 'percent';
                $markupVal = (float)($_POST['markup_value'] ?? 0);

                if ($providerId <= 0) {
                    admin_flash('error', 'Select provider.');
                    redirect('admin/services');
                }
                $provider = db_fetch("SELECT * FROM providers WHERE id = :id LIMIT 1", ['id' => $providerId]);
                if (!$provider) {
                    admin_flash('error', 'Provider not found.');
                    redirect('admin/services');
                }
                $resp = admin_provider_request($provider, 'services', ['limit' => 1000]);
                if (isset($resp['error']) || !is_array($resp)) {
                    admin_flash('error', 'Import failed from provider.');
                    redirect('admin/services');
                }
                $added = 0;
                $updated = 0;
                $categoriesCreated = 0;

                foreach ($resp as $item) {
                    if (!is_array($item)) continue;
                    $apiId = $item['service'] ?? ($item['id'] ?? null);
                    if ($apiId === null || !is_scalar($apiId)) {
                        continue;
                    }
                    $apiId = (string)$apiId;

                    $platform = $item['type'] ?? ($item['platform'] ?? 'generic');
                    $category = $item['category'] ?? ($item['cat'] ?? ($item['type'] ?? 'Uncategorized'));
                    if (empty($category)) $category = 'Uncategorized';

                    $name = $item['name'] ?? '';
                    if (!$name) continue;
                    $rate = isset($item['rate']) ? (float)$item['rate'] : (float)($item['price'] ?? 0);
                    if ($markupType === 'percent') {
                        $rate = $rate * (1 + ($markupVal / 100));
                    } else {
                        $rate = $rate + $markupVal;
                    }
                    $min = (int)($item['min'] ?? 0);
                    $max = (int)($item['max'] ?? 0);
                    $status = (!empty($item['disabled']) && $item['disabled']) ? 'disabled' : 'enabled';
                    if ($rate <= 0 || $min <= 0 || $max <= 0) continue;

                    // Ensure category exists in categories table
                    $existingCat = db_fetch("SELECT id FROM categories WHERE name = :name LIMIT 1", ['name' => $category]);
                    if (!$existingCat) {
                        try {
                            db_execute(
                                "INSERT INTO categories (name, description, sort_order, status) VALUES (:name, '', 0, 'enabled')",
                                ['name' => $category]
                            );
                            $categoriesCreated++;
                        } catch (Exception $e) {
                            error_log("Error creating category $category: " . $e->getMessage());
                        }
                    }

                    $existing = db_fetch(
                        "SELECT id FROM services WHERE provider_id = :pid AND api_service_id = :asid AND deleted_at IS NULL LIMIT 1",
                        ['pid' => $providerId, 'asid' => $apiId]
                    );

                    if ($existing) {
                        try {
                            db_execute(
                                "UPDATE services SET platform=:platform, category=:category, name=:name, description=:description, rate_per_1000=:rate, min_qty=:min, max_qty=:max, status=:status WHERE id=:id",
                                [
                                    'platform' => $platform,
                                    'category' => $category,
                                    'name' => $name,
                                    'description' => $item['description'] ?? '',
                                    'rate' => $rate,
                                    'min' => $min,
                                    'max' => $max,
                                    'status' => $status,
                                    'id' => $existing['id'],
                                ]
                            );
                            $updated++;
                        } catch (Exception $e) {
                            error_log("Error updating service $apiId: " . $e->getMessage());
                        }
                    } else {
                        try {
                            db_execute(
                                "INSERT INTO services (provider_id, api_service_id, platform, category, name, description, rate_per_1000, min_qty, max_qty, status) VALUES (:provider_id, :api_service_id, :platform, :category, :name, :description, :rate, :min, :max, :status)",
                                [
                                    'provider_id' => $providerId,
                                    'api_service_id' => $apiId,
                                    'platform' => $platform,
                                    'category' => $category,
                                    'name' => $name,
                                    'description' => $item['description'] ?? '',
                                    'rate' => $rate,
                                    'min' => $min,
                                    'max' => $max,
                                    'status' => $status,
                                ]
                            );
                            $added++;
                        } catch (Exception $e) {
                            error_log("Error inserting service $apiId: " . $e->getMessage());
                        }
                    }
                }
                $msg = "Imported {$added} new, updated {$updated} services.";
                if ($categoriesCreated > 0) {
                    $msg .= " Created {$categoriesCreated} new categories.";
                }
                admin_flash('success', $msg);
                redirect('admin/services');
            }

            if ($action === 'update_service') {
                $serviceId = (int)($_POST['service_id'] ?? 0);
                $provider_id = (int)($_POST['provider_id'] ?? 0) ?: null;
                $platform = sanitize($_POST['platform'] ?? '');
                $category = sanitize($_POST['category'] ?? '');
                $name = sanitize($_POST['name'] ?? '');
                $description = sanitize($_POST['description'] ?? '');
                $rate = (float)($_POST['rate_per_1000'] ?? 0);
                $min = (int)($_POST['min_qty'] ?? 0);
                $max = (int)($_POST['max_qty'] ?? 0);
                $status = ($_POST['status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';

                if ($serviceId <= 0 || !$platform || !$category || !$name || $rate <= 0 || $min <= 0 || $max <= 0) {
                    admin_flash('error', 'Please fill all required fields.');
                    redirect('admin/services');
                }

                db_execute(
                    "UPDATE services SET provider_id = :provider_id, platform = :platform, category = :category, name = :name, description = :description, rate_per_1000 = :rate, min_qty = :min_qty, max_qty = :max_qty, status = :status WHERE id = :id",
                    [
                        'provider_id' => $provider_id,
                        'platform' => $platform,
                        'category' => $category,
                        'name' => $name,
                        'description' => $description,
                        'rate' => $rate,
                        'min_qty' => $min,
                        'max_qty' => $max,
                        'status' => $status,
                        'id' => $serviceId,
                    ]
                );
                admin_flash('success', 'Service updated.');
                redirect('admin/services');
            }

            if ($action === 'toggle_service_status') {
                $serviceId = (int)($_POST['service_id'] ?? 0);
                $target = ($_POST['target_status'] ?? 'enabled') === 'disabled' ? 'disabled' : 'enabled';
                if ($serviceId <= 0) {
                    admin_flash('error', 'Service not found.');
                    redirect('admin/services');
                }
                db_execute("UPDATE services SET status = :status WHERE id = :id", ['status' => $target, 'id' => $serviceId]);
                admin_flash('success', 'Service ' . ($target === 'enabled' ? 'enabled' : 'disabled') . '.');
                redirect('admin/services');
            }

            if ($action === 'delete_service') {
                $serviceId = (int)($_POST['service_id'] ?? 0);
                if ($serviceId <= 0) {
                    admin_flash('error', 'Service not found.');
                    redirect('admin/services');
                }
                db_execute("DELETE FROM services WHERE id = :id", ['id' => $serviceId]);
                admin_flash('success', 'Service deleted.');
                redirect('admin/services');
            }

            if ($action === 'bulk_import_services') {
                $lines = array_filter(array_map('trim', explode("\n", $_POST['bulk_payload'] ?? '')));
                $added = 0;
                foreach ($lines as $line) {
                    $parts = array_map('trim', str_getcsv($line));
                    if (count($parts) < 7) continue;
                    [$platform, $category, $name, $rate, $min, $max, $status] = $parts;
                    $rate = (float)$rate;
                    $min = (int)$min;
                    $max = (int)$max;
                    $status = strtolower($status) === 'disabled' ? 'disabled' : 'enabled';
                    if (!$platform || !$category || !$name || $rate <= 0 || $min <= 0 || $max <= 0) continue;
                    db_execute(
                        "INSERT INTO services (platform, category, name, rate_per_1000, min_qty, max_qty, status) VALUES (:platform, :category, :name, :rate, :min, :max, :status)",
                        [
                            'platform' => $platform,
                            'category' => $category,
                            'name' => $name,
                            'rate' => $rate,
                            'min' => $min,
                            'max' => $max,
                            'status' => $status,
                        ]
                    );
                    $added++;
                }
                admin_flash('success', "Bulk import completed. Added {$added} services.");
                redirect('admin/services');
            }
        }

        $where = ['s.deleted_at IS NULL'];
        $params = [];
        if ($providerIdFilter) {
            $where[] = 's.provider_id = :filter_pid';
            $params['filter_pid'] = $providerIdFilter;
        }
        if ($statusFilter === 'enabled' || $statusFilter === 'disabled') {
            $where[] = 's.status = :filter_status';
            $params['filter_status'] = $statusFilter;
        }
        if ($qFilter !== '') {
            $where[] = '(s.name LIKE :q OR s.category LIKE :q OR s.platform LIKE :q)';
            $params['q'] = '%' . $qFilter . '%';
        }
        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $services = db_fetch_all(
            "SELECT s.id, s.provider_id, p.name AS provider_name, s.platform, s.category, s.name, s.description, s.rate_per_1000, s.min_qty, s.max_qty, s.status
             FROM services s
             LEFT JOIN providers p ON p.id = s.provider_id
             {$whereSql} AND s.deleted_at IS NULL
             ORDER BY s.id DESC
             LIMIT 300",
            $params
        );

        $providers = db_fetch_all("SELECT id, name FROM providers ORDER BY name ASC");
        $categories = db_fetch_all("SELECT * FROM categories ORDER BY sort_order ASC, name ASC");
        $csrf = admin_csrf_token();

        $page = 'services';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function providers()
    {
        admin_require_login();
        admin_ensure_provider_balance_column();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid session token.');
                redirect('admin/providers');
            }

            $action = $_POST['action'] ?? '';

            if ($action === 'add_provider') {
                $name = sanitize($_POST['name'] ?? '');
                $api_url = sanitize($_POST['api_url'] ?? '');
                $api_key = sanitize($_POST['api_key'] ?? '');
                $auto_sync = isset($_POST['auto_sync']) ? 1 : 0;

                if (!$name || !$api_url || !$api_key) {
                    admin_flash('error', 'Please provide name, API URL, and API key.');
                    redirect('admin/providers');
                }

                db_execute(
                    "INSERT INTO providers (name, api_key, api_url, auto_sync) VALUES (:name, :api_key, :api_url, :auto_sync)",
                    [
                        'name' => $name,
                        'api_key' => $api_key,
                        'api_url' => $api_url,
                        'auto_sync' => $auto_sync,
                    ]
                );
                admin_flash('success', 'Provider added.');
                redirect('admin/providers');
            } elseif ($action === 'sync_provider_balance') {
                $providerId = (int)($_POST['provider_id'] ?? 0);
                if (!$providerId) {
                    admin_flash('error', 'Missing provider.');
                    redirect('admin/providers');
                }

                if (!admin_ensure_provider_balance_column()) {
                    admin_flash('error', 'Could not prepare balance storage.');
                    redirect('admin/providers');
                }

                $provider = db_fetch("SELECT * FROM providers WHERE id = :id LIMIT 1", ['id' => $providerId]);
                if (!$provider) {
                    admin_flash('error', 'Provider not found.');
                    redirect('admin/providers');
                }

                $resp = admin_provider_request($provider, 'balance');
                if (isset($resp['error'])) {
                    admin_flash('error', 'Sync failed: ' . $resp['error']);
                    redirect('admin/providers');
                }

                $balance = $resp['balance'] ?? ($resp['balance_float'] ?? null);
                if ($balance === null && isset($resp['raw'])) {
                    if (preg_match('/([0-9]+(?:\.[0-9]+)?)/', $resp['raw'], $m)) {
                        $balance = (float)$m[1];
                    }
                }

                if ($balance === null || !is_numeric($balance)) {
                    admin_flash('error', 'Could not parse provider balance.');
                    redirect('admin/providers');
                }

                db_execute("UPDATE providers SET balance = :balance WHERE id = :id", [
                    'balance' => (float)$balance,
                    'id' => $providerId,
                ]);

                admin_flash('success', 'Balance synced: ' . format_currency((float)$balance));
                redirect('admin/providers');
            }
        }

        $page = 'providers';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function payments($action = null)
    {
        admin_require_login();

        // Handle POST requests for saving settings
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid security token. Please try again.');
                redirect('admin/payments');
                return;
            }

            $section = $_POST['section'] ?? '';

            // Define keys for each section
            $sectionKeys = [
                'general' => ['active_payment_gateway', 'min_deposit', 'max_deposit'],
                'flutterwave' => [
                    'flutterwave_enabled',
                    'flutterwave_env',
                    'flutterwave_public_key',
                    'flutterwave_secret_key',
                    'flutterwave_encryption_key',
                    'flutterwave_webhook_secret'
                ],
                'paystack' => ['paystack_enabled', 'paystack_env', 'paystack_public_key', 'paystack_secret_key'],
                'bank' => [
                    'bank_transfer_enabled',
                    'bank_name',
                    'bank_account_name',
                    'bank_account_number',
                    'bank_instructions'
                ],
                'crypto' => ['crypto_enabled', 'crypto_btc_address', 'crypto_usdt_address', 'crypto_eth_address']
            ];

            // Get keys for the submitted section
            $keys = $sectionKeys[$section] ?? [];

            if (empty($keys)) {
                admin_flash('error', 'Invalid form section.');
                redirect('admin/payments');
                return;
            }

            $saved = 0;
            $errors = [];

            foreach ($keys as $key) {
                // For checkbox fields, use '0' if not present, otherwise use the posted value
                $value = $_POST[$key] ?? '0';

                $result = update_setting($key, $value);
                if ($result) {
                    $saved++;
                } else {
                    $errors[] = $key;
                }
            }

            $sectionNames = [
                'general' => 'General',
                'flutterwave' => 'Flutterwave',
                'paystack' => 'Paystack',
                'bank' => 'Bank Transfer',
                'crypto' => 'Cryptocurrency'
            ];

            $sectionName = $sectionNames[$section] ?? ucfirst($section);

            if (empty($errors)) {
                admin_flash('success', "{$sectionName} settings saved successfully!");
            } else {
                admin_flash('error', "Some {$sectionName} settings failed to save: " . implode(', ', $errors));
            }
            redirect('admin/payments');
            return;
        }

        $page = 'payments';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function transactions()
    {
        admin_require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid security token.');
                redirect('admin/transactions');
                return;
            }

            $txId = (int)($_POST['transaction_id'] ?? 0);
            $action = $_POST['action'] ?? '';

            if ($txId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
                admin_flash('error', 'Invalid transaction request.');
                redirect('admin/transactions');
                return;
            }

            if ($action === 'approve') {
                if ($this->transactionModel->approve($txId)) {
                    admin_flash('success', 'Deposit approved and wallet credited.');
                } else {
                    admin_flash('error', 'Unable to approve this transaction. It may have been processed already.');
                }
            } else {
                if ($this->transactionModel->reject($txId)) {
                    admin_flash('success', 'Deposit rejected.');
                } else {
                    admin_flash('error', 'Unable to reject this transaction.');
                }
            }

            redirect('admin/transactions');
            return;
        }

        $manualDeposits = db_fetch_all(
            "SELECT t.*, u.username, u.email, u.name
             FROM transactions t
             JOIN users u ON u.id = t.user_id
             WHERE t.type = 'deposit'
               AND t.gateway IN ('bank_transfer', 'crypto')
               AND t.status = 'pending'
             ORDER BY t.created_at DESC"
        );

        $recentManualDeposits = db_fetch_all(
            "SELECT t.*, u.username, u.email
             FROM transactions t
             JOIN users u ON u.id = t.user_id
             WHERE t.gateway IN ('bank_transfer', 'crypto')
             ORDER BY t.created_at DESC
             LIMIT 25"
        );

        $page = 'transactions';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function tickets()
    {
        admin_require_login();

        $tickets = db_fetch_all(
            "SELECT t.*, u.username, u.email, u.name
             FROM tickets t
             JOIN users u ON u.id = t.user_id
             ORDER BY t.created_at DESC"
        );

        $page = 'tickets';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function settings()
    {
        admin_require_login();
        $page = 'settings';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function updateSettings()
    {
        admin_require_login();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid security token.');
                redirect('admin/settings');
                return;
            }

            $keys = [
                // General settings (payment settings are now in admin/payments)
                'site_name',
                'child_panel_price',
                'referral_commission_rate',
                'referral_min_payout',
                'contact_email',
                'contact_phone',
                'contact_whatsapp',
                'contact_telegram',
                'contact_address',
                // Widget settings
                'enable_whatsapp',
                'whatsapp_number',
                'enable_tawk',
                'tawk_to_id',
                // Ticker
                'enable_ticker',
                // Auth settings
                'google_auth_enabled',
                'google_client_id',
                'google_client_secret'
            ];

            // Handle checkboxes (if unchecked, they won't be in $_POST)
            $checkboxes = ['enable_whatsapp', 'enable_tawk', 'google_auth_enabled', 'enable_ticker'];
            foreach ($checkboxes as $chk) {
                if (!isset($_POST[$chk])) {
                    update_setting($chk, '0');
                }
            }

            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    update_setting($key, $_POST[$key]);
                }
            }

            admin_flash('success', 'Settings updated successfully.');
            redirect('admin/settings');
        }
    }

    public function blog()
    {
        admin_require_login();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrf = $_POST['csrf_token'] ?? '';
            if (!admin_verify_csrf($csrf)) {
                admin_flash('error', 'Invalid session token.');
                redirect('admin/blog');
            }

            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $title = $_POST['title'];
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
                $content = $_POST['content'];
                $excerpt = $_POST['excerpt'];
                $status = $_POST['status'];

                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['image'];
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (in_array($file['type'], $allowedTypes)) {
                        $uploadDir = APP_PATH . '/uploads/blog/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $filename = 'blog_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                        $filepath = $uploadDir . $filename;
                        if (move_uploaded_file($file['tmp_name'], $filepath)) {
                            $imagePath = $filename;
                        }
                    }
                }

                try {
                    db_execute(
                        "INSERT INTO posts (title, slug, content, excerpt, status, author_id, image) VALUES (:title, :slug, :content, :excerpt, :status, :author_id, :image)",
                        [
                            'title' => $title,
                            'slug' => $slug,
                            'content' => $content,
                            'excerpt' => $excerpt,
                            'status' => $status,
                            'author_id' => 1,
                            'image' => $imagePath
                        ]
                    );
                } catch (Exception $e) {
                    // Fallback without image
                    db_execute(
                        "INSERT INTO posts (title, slug, content, excerpt, status, author_id) VALUES (:title, :slug, :content, :excerpt, :status, :author_id)",
                        [
                            'title' => $title,
                            'slug' => $slug,
                            'content' => $content,
                            'excerpt' => $excerpt,
                            'status' => $status,
                            'author_id' => 1
                        ]
                    );
                }
                admin_flash('success', 'Post created.');
            }
            redirect('admin/blog');
        }

        $posts = db_fetch_all("SELECT * FROM posts ORDER BY created_at DESC");
        $page = 'blog';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function cancelOrder()
    {
        admin_require_login();

        $input = json_decode(file_get_contents('php://input'), true);
        $orderId = (int)($input['order_id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
            exit;
        }

        try {
            $order = db_fetch("SELECT user_id, amount, status FROM orders WHERE id = :id", ['id' => $orderId]);
            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order not found.']);
                exit;
            }

            if (!in_array($order['status'], ['pending', 'processing'])) {
                echo json_encode(['success' => false, 'message' => 'Order cannot be canceled.']);
                exit;
            }

            // Refund
            db_execute("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :uid", ['amount' => $order['amount'], 'uid' => $order['user_id']]);
            db_execute("UPDATE orders SET status = 'canceled' WHERE id = :id", ['id' => $orderId]);

            // Notification
            create_notification($order['user_id'], "Order #$orderId has been canceled by admin and refunded.", 'times-circle', 'danger');

            echo json_encode(['success' => true, 'message' => 'Order canceled and refunded.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function syncOrder()
    {
        admin_require_login();

        $input = json_decode(file_get_contents('php://input'), true);
        $orderId = (int)($input['order_id'] ?? 0);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
            exit;
        }

        try {
            $order = db_fetch("SELECT o.id, o.user_id, o.provider_order_id, o.status, o.amount, p.api_url, p.api_key FROM orders o JOIN services s ON s.id = o.service_id JOIN providers p ON p.id = s.provider_id WHERE o.id = :id", ['id' => $orderId]);
            if (!$order) {
                echo json_encode(['success' => false, 'message' => 'Order not found.']);
                exit;
            }

            if (empty($order['provider_order_id'])) {
                echo json_encode(['success' => false, 'message' => 'Order has no provider order ID.']);
                exit;
            }

            $provider = [
                'api_url' => $order['api_url'],
                'api_key' => $order['api_key']
            ];

            $response = admin_provider_request($provider, 'status', ['order' => $order['provider_order_id']]);

            if (isset($response['error'])) {
                echo json_encode(['success' => false, 'message' => 'API Error: ' . $response['error']]);
                exit;
            }

            $status = strtolower($response['status'] ?? 'unknown');

            if ($status === 'completed') {
                db_execute("UPDATE orders SET status = 'completed' WHERE id = :id", ['id' => $orderId]);
                create_notification($order['user_id'], "Order #$orderId has been completed successfully!", 'check-circle', 'success');
                echo json_encode(['success' => true, 'message' => 'Order status updated to completed.', 'new_status' => 'completed']);
            } elseif (in_array($status, ['canceled', 'cancelled', 'failed'])) {
                // Refund
                db_execute("UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :uid", ['amount' => $order['amount'], 'uid' => $order['user_id']]);
                db_execute("UPDATE orders SET status = 'canceled' WHERE id = :id", ['id' => $orderId]);
                create_notification($order['user_id'], "Order #$orderId has been canceled and refunded.", 'times-circle', 'danger');
                echo json_encode(['success' => true, 'message' => 'Order status updated to canceled and refunded.', 'new_status' => 'canceled']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Order status is still ' . ucfirst($status) . '.', 'new_status' => $status]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function notifications()
    {
        admin_require_login();
        $page = 'notifications';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function newsletter()
    {
        admin_require_login();
        $page = 'newsletter';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function announcements()
    {
        admin_require_login();
        $page = 'announcements';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }

    public function emails()
    {
        admin_require_login();
        $page = 'emails';
        require VIEWS_PATH . '/admin/layouts/admin.php';
    }
}
