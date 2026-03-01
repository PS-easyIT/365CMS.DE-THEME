<?php
/**
 * PTC Theme – Fehlerseite (generisch)
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$errorCode    = $GLOBALS['error_code']    ?? 500;
$errorMessage = $GLOBALS['error_message'] ?? 'Ein unerwarteter Fehler ist aufgetreten.';
http_response_code((int)$errorCode);
?>

<section class="ptc-page-hero">
    <div class="ptc-container">
        <p class="ptc-hero-overline">Fehler <?php echo (int)$errorCode; ?></p>
        <h1>Etwas ist schiefgelaufen.</h1>
    </div>
</section>

<div class="ptc-page-content">
    <div class="ptc-container" style="text-align:center;padding:5rem 0;">
        <p style="color:var(--ptc-slate);margin-bottom:2.5rem;">
            <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <a href="<?php echo ptc_site_url(); ?>/" class="btn-ptc btn-ptc-primary btn-ptc-lg">🏠 Zur Startseite</a>
    </div>
</div>
