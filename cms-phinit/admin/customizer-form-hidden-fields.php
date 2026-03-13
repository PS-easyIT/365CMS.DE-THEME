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
<input type="hidden" name="active_section" id="active_section_input" value="<?php echo htmlspecialchars($activeTab); ?>">
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
