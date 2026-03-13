<?php
/**
 * Customizer content column partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$currentGroups = $tabGroups[$activeTab] ?? [];
$tabSections = $config[$activeTab]['sections'] ?? [];

if ($activeTab === 'advanced') {
    foreach (['custom_css', 'custom_head_code', 'custom_footer_code'] as $_fk) {
        if (isset($tabSections[$_fk])) {
            $tabSections[$_fk]['rows'] = 8;
        }
    }
}
?>
<div class="col-12 col-md-9 col-lg-10">

    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-action-bar.php'; ?>

    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-tab-extras.php'; ?>

    <?php require CMS_PHINIT_THEME_DIR . 'admin/customizer-tab-groups.php'; ?>

</div>
