<?php $n = htmlspecialchars($name ?? ''); ?>
<p>Hi <?php echo $n; ?>,</p>
<p>Your wallet has been funded successfully.</p>
<p>Amount credited: <strong><?php echo htmlspecialchars($amount_fmt ?? ''); ?></strong></p>
<p>Reference: <strong><?php echo htmlspecialchars($reference ?? ''); ?></strong></p>
<p>Thank you for your payment.</p>