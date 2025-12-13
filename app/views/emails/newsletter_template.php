<?php 
// Newsletter email template
$site_name = $site_name ?? SITE_NAME ?? 'SmikeBoost';
$site_url = $site_url ?? SITE_URL ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $subject; ?></title>
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f3f4f6; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; }
        .email-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 24px; text-align: center; }
        .email-header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: bold; }
        .email-body { padding: 32px; color: #374151; line-height: 1.6; font-size: 16px; }
        .email-footer { background-color: #f9fafb; padding: 24px; text-align: center; color: #6b7280; font-size: 14px; border-top: 1px solid #e5e7eb; }
        .btn { display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 16px 0; }
        .btn:hover { background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%); }
        .highlight { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 16px; border-radius: 6px; margin: 16px 0; }
        .social-links { margin-top: 16px; }
        .social-links a { display: inline-block; margin: 0 8px; color: #6b7280; text-decoration: none; }
        @media (max-width: 600px) {
            .email-body { padding: 20px; }
            .email-header { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1><?php echo $site_name; ?></h1>
        </div>
        
        <div class="email-body">
            <?php echo $content; ?>
        </div>
        
        <div class="email-footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_name; ?>. All rights reserved.</p>
            <p>You are receiving this email because you are a registered user.</p>
            <div class="social-links">
                <a href="<?php echo $site_url; ?>">Visit Website</a>
            </div>
        </div>
    </div>
</body>
</html>