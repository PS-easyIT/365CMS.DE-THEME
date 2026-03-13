<?php
/**
 * Customizer form partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<form method="POST" id="customizer-form"
      action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-form-hidden-fields.php'; ?>

    <div class="row g-3">
        <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-sidebar.php'; ?>
        <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-content-column.php'; ?>
    </div>
</form>
