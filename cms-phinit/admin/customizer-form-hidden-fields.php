<?php
/**
 * Customizer form hidden fields partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>
<input type="hidden" name="action" value="save_theme_options">
<input type="hidden" name="active_section" id="active_section_input" value="<?php echo htmlspecialchars((string) $activeTab, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="storage_section" value="<?php echo htmlspecialchars((string) ($config[$activeTab]['storageTab'] ?? $activeTab), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="editor_lang" value="<?php echo htmlspecialchars((string) ($editorUiLocale ?? 'de'), ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string) $csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
