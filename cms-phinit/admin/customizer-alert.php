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
?>
<?php if ($alertMsg !== null): ?>
<div class="alert alert-<?php echo htmlspecialchars($alertType); ?> alert-dismissible" role="alert">
    <?php echo $alertMsg; ?>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>
<?php endif; ?>
