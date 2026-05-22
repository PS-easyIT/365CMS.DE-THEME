<?php
declare(strict_types=1);

/**
 * Business Theme – Fehlerseite (generisch)
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$errorCode    = (int) ($GLOBALS['error_code']    ?? 500);
$errorMessage = (string) ($GLOBALS['error_message'] ?? 'Ein unerwarteter Fehler ist aufgetreten.');
http_response_code($errorCode);
?>

<section class="biz-page-hero">
    <div class="biz-container">
        <p class="biz-page-eyebrow">Fehler <?php echo $errorCode; ?></p>
        <h1>Etwas ist schiefgelaufen.</h1>
    </div>
</section>

<div class="biz-page-content">
    <div class="biz-container">
        <div class="biz-error-wrap">
            <p class="biz-error-text">
                <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <div class="biz-error-actions">
                <a href="<?php echo htmlspecialchars(biz_href('/'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-primary btn-biz-lg">Zur Startseite</a>
            </div>
        </div>
    </div>
</div>
