<?php
/**
 * Dashboard Mass Order Page
 */
$seo = get_seo_tags('Mass Order', 'Place multiple orders at once', '');
?>

<section class="mass-order-page">
    <div class="page-header">
        <h1>Mass Order</h1>
        <p>Place multiple orders efficiently</p>
    </div>

    <div class="glass-card">
        <div class="alert alert-info mb-lg" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); padding: 1rem; border-radius: var(--radius-md);">
            <h4 style="margin-top: 0; color: var(--color-info);">Format</h4>
            <p style="font-family: monospace; background: rgba(0,0,0,0.2); padding: 0.5rem; border-radius: 4px;">Service ID | Link | Quantity</p>
            <p style="margin-bottom: 0; font-size: 0.9rem; margin-top: 0.5rem;">One order per line. Example:</p>
            <pre style="background: rgba(0,0,0,0.2); padding: 0.5rem; border-radius: 4px; margin-top: 0.25rem; color: var(--text-secondary);">102 | https://instagram.com/user | 1000
105 | https://tiktok.com/@user | 5000</pre>
        </div>

        <form method="POST" action="<?php echo url('dashboard/mass-order'); ?>">
            <div class="form-group">
                <label for="mass_orders">Orders</label>
                <textarea name="mass_orders" id="mass_orders" class="form-control" rows="15" placeholder="102 | https://link.com | 1000" required style="font-family: monospace;"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-layer-group"></i> Submit Orders
            </button>
        </form>
    </div>
</section>
