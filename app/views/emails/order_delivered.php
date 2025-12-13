<?php $n = htmlspecialchars($name ?? ''); ?>
<p>Hi <?php echo $n; ?>,</p>
<p>Your order has been completed successfully.</p>
<p>Order ID: <strong>#<?php echo htmlspecialchars((string)($order_id ?? '')); ?></strong></p>
<p>Thanks for choosing SmikeBoost.</p>