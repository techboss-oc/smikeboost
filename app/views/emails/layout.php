<?php
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($subject ?? ''); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #111827;
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
        }

        .container {
            max-width: 640px;
            margin: 24px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .header {
            background-color: #111827;
            color: #ffffff;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .content {
            padding: 24px;
            color: #111827;
        }

        .footer {
            background-color: #f9fafb;
            color: #6b7280;
            padding: 16px 24px;
            font-size: 12px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header"><?php echo htmlspecialchars($subject ?? ''); ?></div>
        <div class="content"><?php echo $content ?? ''; ?></div>
        <div class="footer">&copy; <?php echo date('Y'); ?> SmikeBoost. This email was sent automatically.</div>
    </div>
</body>

</html>