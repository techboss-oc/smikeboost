<?php $n = htmlspecialchars($name ?? ''); ?>
<p>Hi <?php echo $n; ?>,</p>
<p>We received your deposit request.</p>
<p>Amount: <strong><?php echo htmlspecialchars($amount_fmt ?? ''); ?></strong></p>
<p>Method: <strong><?php echo htmlspecialchars($gateway ?? ''); ?></strong></p>
<p>Reference: <strong><?php echo htmlspecialchars($reference ?? ''); ?></strong></p>
<p>Our team will review and credit your wallet shortly.</p>