<?php
/**
 * Customizer page header partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle"><?php echo htmlspecialchars(phinit_t('theme_editor_pretitle', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></div>
                <h2 class="page-title">🎨 <?php echo htmlspecialchars(phinit_t('theme_customizer_title', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-group me-2" role="group" aria-label="<?php echo htmlspecialchars(phinit_t('editor_language', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>">
                    <a class="btn btn-outline-secondary<?php echo ($editorUiLocale ?? 'de') === 'de' ? ' active' : ''; ?>" href="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . rawurlencode((string) $activeTab) . '&editor_lang=de', ENT_QUOTES); ?>">DE</a>
                    <a class="btn btn-outline-secondary<?php echo ($editorUiLocale ?? 'de') === 'en' ? ' active' : ''; ?>" href="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . rawurlencode((string) $activeTab) . '&editor_lang=en', ENT_QUOTES); ?>">EN</a>
                </div>
                <button type="button" id="preview-toggle-btn" class="btn btn-outline-secondary me-2" aria-label="<?php echo htmlspecialchars(phinit_t('theme_editor_live_preview', [], $editorUiLocale ?? 'de'), ENT_QUOTES, 'UTF-8'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 7a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z"/>
                        <path d="M7 20l10 0"/><path d="M9 16l0 4"/><path d="M15 16l0 4"/>
                    </svg>
                    <?php echo htmlspecialchars(phinit_t('theme_editor_live_preview', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
                </button>
            </div>
        </div>
    </div>
</div>
