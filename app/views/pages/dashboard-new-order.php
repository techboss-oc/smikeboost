<?php
/**
 * New Order Page
 */
require_once APP_PATH . '/models/Service.php';

$seo = get_seo_tags('New Order', 'Create a new SMM order', '');

$user = current_user();
$userId = $user['id'] ?? 0;
$userRow = db_fetch("SELECT wallet_balance FROM users WHERE id = :id", ['id' => $userId]);
$walletBalance = $userRow['wallet_balance'] ?? 0;

// Fetch all enabled services grouped by platform
$services = db_fetch_all(
    "SELECT * FROM services WHERE status = 'enabled' ORDER BY platform, category, name"
);

// Flash messages
$error = flash('error');
$success = flash('success');
?>

<section class="new-order-page">
    <div class="page-header">
        <h1>Create New Order</h1>
        <p>Select a service and provide details</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger mb-lg" style="padding: 1rem; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md); color: var(--color-danger);"><?php echo e($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success mb-lg" style="padding: 1rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md); color: var(--color-success);"><?php echo e($success); ?></div>
    <?php endif; ?>

    <form class="glass-card" id="orderForm" method="POST" action="<?php echo url('dashboard/new-order'); ?>">
        <input type="hidden" name="ajax" value="1">
        <div class="grid-2">
            <!-- Left Column -->
            <div class="form-column">
                <h2 class="mb-lg" style="font-size: 1.25rem;">Order Details</h2>

                <div class="form-group">
                    <label for="service_category">Category</label>
                    <select id="service_category" name="category" class="form-control" required onchange="onCategoryChange()">
                        <option value="">Select category</option>
                        <?php
                        $categories = [];
                        foreach ($services as $svc) {
                            $categories[$svc['category']] = true;
                        }
                        foreach (array_keys($categories) as $category): ?>
                            <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="service_id">Service *</label>
                    <select id="service_id" name="service_id" class="form-control" required onchange="updateOrderPrice()" disabled>
                        <option value="">Select service</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="link">Link/Username *</label>
                    <input type="text" id="link" name="link" class="form-control" placeholder="Post link, username, or channel URL" required>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity *</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="1" placeholder="Enter quantity" required onchange="updateOrderPrice()">
                </div>

                <div class="d-flex gap-md align-center" style="background: rgba(59, 130, 246, 0.1); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(59, 130, 246, 0.2);">
                    <i class="fas fa-info-circle text-info" style="font-size: 1.25rem;"></i>
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">Ensure your account is public and has no restrictions.</p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="form-column">
                <h2 class="mb-lg" style="font-size: 1.25rem;">Price Calculation</h2>

                <div class="glass-card mb-lg" style="background: rgba(255, 255, 255, 0.02);">
                    <div class="d-flex justify-between mb-sm" style="border-bottom: 1px solid var(--glass-border); padding-bottom: 0.5rem;">
                        <span class="text-secondary">Service:</span>
                        <span id="service-name" class="text-primary" style="text-align: right; max-width: 60%;">-</span>
                    </div>
                    <div class="d-flex justify-between mb-sm">
                        <span class="text-secondary">Price per 1000:</span>
                        <span id="price-per-1000"><?php echo format_currency(0); ?></span>
                    </div>
                    <div class="d-flex justify-between mb-lg">
                        <span class="text-secondary">Quantity:</span>
                        <span id="qty-display">0</span>
                    </div>
                    <div class="d-flex justify-between align-center" style="border-top: 1px solid var(--glass-border); padding-top: 1rem;">
                        <span style="font-weight: 600;">Total Amount:</span>
                        <span id="total-amount" class="text-primary" style="font-size: 1.5rem; font-weight: 700;"><?php echo format_currency(0); ?></span>
                    </div>
                </div>

                <div class="glass-card mb-lg" style="background: rgba(255, 255, 255, 0.02);">
                    <div class="d-flex justify-between align-center">
                        <span class="text-secondary">Available Balance:</span>
                        <span class="text-success" style="font-weight: 700; font-size: 1.1rem;"><?php echo format_currency($walletBalance); ?></span>
                    </div>
                </div>

                <div class="d-flex" style="flex-direction: column; gap: 1rem;">
                    <button type="submit" class="btn btn-primary btn-block btn-lg" id="placeOrderBtn">
                        <i class="fas fa-shopping-cart"></i> Place Order
                    </button>
                    <a href="<?php echo url('dashboard'); ?>" class="btn btn-outline btn-block">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</section>

<!-- Order Result Modal -->
<div id="orderModal" class="modal" style="display: none;">
    <div class="modal-content glass-card" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="modalTitle">Order Status</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalMessage"></div>
            <div class="d-flex justify-end mt-lg">
                <button class="btn btn-primary" onclick="closeModal()">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
const serviceData = <?php echo json_encode($services); ?>;

// Group services by category only
function groupedServicesByCategory() {
    return serviceData.reduce((acc, svc) => {
        const category = svc.category;
        if (!acc[category]) acc[category] = [];
        acc[category].push(svc);
        return acc;
    }, {});
}

const serviceMap = groupedServicesByCategory();

function onCategoryChange() {
    const category = document.getElementById('service_category').value;
    const serviceSelect = document.getElementById('service_id');

    serviceSelect.innerHTML = '<option value="">Select service</option>';
    serviceSelect.disabled = true;

    if (!category || !serviceMap[category]) return;

    serviceMap[category].forEach(svc => {
        const opt = document.createElement('option');
        opt.value = svc.id;
        opt.textContent = svc.name;
        opt.dataset.price = svc.rate_per_1000;
        opt.dataset.name = svc.name;
        serviceSelect.appendChild(opt);
    });

    serviceSelect.disabled = false;
}

function updateOrderPrice() {
    const serviceSelect = document.getElementById('service_id');
    const selected = serviceSelect.options[serviceSelect.selectedIndex] || {};
    const price = parseFloat(selected.dataset ? selected.dataset.price : 0) || 0;
    const name = (selected.dataset ? selected.dataset.name : '') || '-';
    const qty = parseFloat(document.getElementById('quantity').value || 0);

    document.getElementById('service-name').textContent = name;
    document.getElementById('price-per-1000').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + price.toLocaleString();
    document.getElementById('qty-display').textContent = qty.toLocaleString();
    document.getElementById('total-amount').textContent = '<?php echo CURRENCY_SYMBOL; ?>' + ((qty / 1000) * price).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function showModal(title, message, isSuccess = true) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').innerHTML = message;
    document.getElementById('orderModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
    if (document.getElementById('modalTitle').textContent === 'Order Placed Successfully!') {
        window.location.href = '<?php echo url('dashboard/orders'); ?>';
    }
}

// Handle form submission
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('placeOrderBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    btn.disabled = true;

    const formData = new FormData(this);

    fetch('<?php echo url('dashboard/new-order'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('Order Placed Successfully!', `Order #${data.order_id} has been placed successfully!`, true);
        } else {
            showModal('Order Failed', data.message || 'An error occurred while placing the order.', false);
        }
    })
    .catch(error => {
        showModal('Error', 'Network error occurred. Please try again.', false);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
});
</script>
