<?php
/**
 * Customizer alert partial.
 *
 * @package CMS_Phinit_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$alertMsg = $alertMsg !== null ? trim((string) $alertMsg) : null;
$alertType = trim((string) $alertType);
?>
<?php if ($alertMsg !== null && $alertMsg !== ''): ?>
<div class="alert alert-<?php echo htmlspecialchars($alertType, ENT_QUOTES); ?> alert-dismissible" role="alert">
    <?php echo htmlspecialchars($alertMsg, ENT_QUOTES); ?>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo htmlspecialchars(phinit_t('close', [], $editorUiLocale ?? 'de'), ENT_QUOTES); ?>"></a>
</div>
<?php endif; ?>
