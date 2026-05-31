<?php
/**
 * Customizer header menu editor note partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="card mt-3 border-primary">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="text-primary phinit-customizer__menu-icon">📋</div>
        <div>
            <strong><?php echo htmlspecialchars(phinit_t('theme_editor_menu_entries_title', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></strong>
            <?php echo htmlspecialchars(phinit_t('theme_editor_menu_entries_note', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
        </div>
        <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/menu-editor'); ?>"
           class="btn btn-sm btn-primary ms-auto"><?php echo htmlspecialchars(phinit_t('theme_editor_open_menu_editor', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?> →</a>
    </div>
</div>
