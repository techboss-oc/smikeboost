<?php $n = htmlspecialchars($name ?? ''); ?>
<p>Hi <?php echo $n; ?>,</p>
<p>Your order is now processing.</p>
<p>Order ID: <strong>#<?php echo htmlspecialchars((string)($order_id ?? '')); ?></strong></p>
<p>We will notify you when it is completed.</p>