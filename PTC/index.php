<?php
/**
 * PTC Theme – Index / Fallback
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="ptc-page-hero">
    <div class="ptc-container">
        <h1>Seite nicht gefunden</h1>
        <p>Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
    </div>
</section>

<div class="ptc-page-content">
    <div class="ptc-container ptc-center-block">
        <p class="ptc-text-muted">Bitte nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.</p>
        <a href="<?php echo ptc_href('/'); ?>" class="btn-ptc btn-ptc-primary">Zur Startseite</a>
    </div>
</div>
