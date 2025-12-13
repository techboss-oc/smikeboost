<?php $n = htmlspecialchars($name ?? ''); ?>
<p>Hi <?php echo $n; ?>,</p>
<p>Your order has been canceled and refunded.</p>
<p>Order ID: <strong>#<?php echo htmlspecialchars((string)($order_id ?? '')); ?></strong></p>
<p>Refunded amount: <strong><?php echo htmlspecialchars($amount_fmt ?? ''); ?></strong></p>
<p>Reason: <strong><?php echo htmlspecialchars($reason ?? ''); ?></strong></p>
<p>If you have questions, please contact support.</p>