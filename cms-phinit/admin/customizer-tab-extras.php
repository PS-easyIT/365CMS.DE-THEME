<?php
/**
 * Customizer tab-specific extras partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<?php if ($activeTab === 'colors'): ?>
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-color-presets.php'; ?>
<?php endif; ?>

<?php if ($activeTab === 'header'): ?>
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-header-menu-note.php'; ?>
<?php endif; ?>
