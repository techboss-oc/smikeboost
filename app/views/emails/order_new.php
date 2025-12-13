<?php $n = htmlspecialchars($name ?? ''); ?>
<p>Hi <?php echo $n; ?>,</p>
<p>Your order has been placed successfully.</p>
<p>Order ID: <strong>#<?php echo htmlspecialchars((string)($order_id ?? '')); ?></strong></p>
<p>Service: <strong><?php echo htmlspecialchars($service_name ?? ''); ?></strong></p>
<p>Quantity: <strong><?php echo htmlspecialchars((string)($quantity ?? '')); ?></strong></p>
<p>Amount: <strong><?php echo htmlspecialchars($amount_fmt ?? ''); ?></strong></p>
<p>We will start processing shortly.</p>