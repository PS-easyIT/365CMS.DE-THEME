<?php
/**
 * Customizer page body partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="page-body">
    <div class="container-xl">
        <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-alert.php'; ?>
        <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-form.php'; ?>
    </div>
</div>
