<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$customizerAdminCssUrl = '';
if (defined('CMS_PHINIT_THEME_DIR') && defined('CMS_PHINIT_THEME_URL')) {
    $customizerAdminCssFile = CMS_PHINIT_THEME_DIR . 'assets/css/customizer-admin.css';
    if (is_file($customizerAdminCssFile)) {
        $customizerAdminCssUrl = CMS_PHINIT_THEME_URL . 'assets/css/customizer-admin.css?v=' . rawurlencode((string) filemtime($customizerAdminCssFile));
    }
}
?>
<?php if ($customizerAdminCssUrl !== ''): ?>
<link rel="stylesheet" href="<?php echo htmlspecialchars($customizerAdminCssUrl, ENT_QUOTES, 'UTF-8'); ?>">
<?php endif; ?>
