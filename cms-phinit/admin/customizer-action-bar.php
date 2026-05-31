<?php
/**
 * Customizer action bar partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$uiLocale = strtolower((string) ($editorUiLocale ?? 'de'));
$isEnglish = $uiLocale === 'en';
?>
<div class="card mb-3 phinit-customizer__action-card">
    <div class="card-body py-2 d-flex align-items-center gap-2 flex-wrap" role="toolbar" aria-label="<?php echo htmlspecialchars($isEnglish ? 'Theme editor actions' : 'Theme-Editor Aktionen', ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" form="customizer-form" name="action" value="save_theme_options" class="btn btn-primary" aria-label="<?php echo htmlspecialchars($isEnglish ? 'Save current section' : 'Aktiven Bereich speichern', ENT_QUOTES, 'UTF-8'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                 stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                <circle cx="12" cy="15" r="2"/><polyline points="14 4 14 8 8 8 8 4"/>
            </svg>
            <?php echo htmlspecialchars(phinit_t('theme_editor_save', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="preview-toggle-btn-secondary">
            👁️ <?php echo htmlspecialchars(phinit_t('theme_editor_live_preview', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
        </button>
        <button
            type="button"
            class="btn btn-outline-secondary"
            data-confirm-message="<?php echo htmlspecialchars(phinit_t('theme_editor_reset_tab', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>?"
            data-confirm-submit-target="phinit-customizer-reset-submit"
            aria-label="<?php echo htmlspecialchars($isEnglish ? 'Reset active section' : 'Aktiven Bereich zurücksetzen', ENT_QUOTES, 'UTF-8'); ?>"
        >
            ↩️ <?php echo htmlspecialchars(phinit_t('theme_editor_reset_tab', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
        </button>
        <button
            type="button"
            class="btn btn-outline-secondary"
            data-collapse-toggle
            data-collapse-target="phinit-tools-panel"
            aria-controls="phinit-tools-panel"
            aria-expanded="false"
            aria-label="<?php echo htmlspecialchars($isEnglish ? 'Open import and export panel' : 'Import-/Export-Panel öffnen', ENT_QUOTES, 'UTF-8'); ?>"
        >
            ⋯ <?php echo htmlspecialchars($isEnglish ? 'More' : 'Mehr', ENT_QUOTES, 'UTF-8'); ?>
        </button>
        <button type="submit" form="customizer-form" name="action" value="reset_theme_tab"
                id="phinit-customizer-reset-submit" class="d-none" aria-hidden="true" tabindex="-1">
            <?php echo htmlspecialchars(phinit_t('theme_editor_reset_tab', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
        </button>
        <span id="unsaved-hint" class="ms-auto text-warning phinit-customizer__unsaved-hint" aria-live="polite">
            ⚠️ <?php echo htmlspecialchars(phinit_t('theme_editor_unsaved_changes', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>
        </span>
        <span class="text-muted ms-auto phinit-customizer__shortcut-hint"><?php echo htmlspecialchars(phinit_t('theme_editor_save_shortcut', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></span>
    </div>
</div>

<div class="phinit-customizer__confirm-backdrop" id="phinit-customizer-confirm" hidden aria-hidden="true">
    <div class="phinit-customizer__confirm-modal" role="dialog" aria-modal="true" aria-labelledby="phinit-customizer-confirm-title">
        <h2 id="phinit-customizer-confirm-title" class="phinit-customizer__confirm-title"><?php echo htmlspecialchars(phinit_t('theme_editor_confirm_change_title', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></h2>
        <p id="phinit-customizer-confirm-message" class="phinit-customizer__confirm-message"><?php echo htmlspecialchars(phinit_t('theme_editor_confirm_continue', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></p>
        <div class="phinit-customizer__confirm-actions">
            <button type="button" class="btn btn-outline-secondary" data-confirm-cancel><?php echo htmlspecialchars(phinit_t('theme_editor_cancel', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></button>
            <button type="button" class="btn btn-primary" data-confirm-accept><?php echo htmlspecialchars(phinit_t('theme_editor_continue', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?></button>
        </div>
    </div>
</div>
