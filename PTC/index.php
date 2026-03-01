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
    <div class="ptc-container" style="text-align:center;padding:4rem 0;">
        <p style="color:var(--ptc-slate);margin-bottom:2rem;">Bitte nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.</p>
        <a href="<?php echo ptc_site_url(); ?>/" class="btn-ptc btn-ptc-primary">Zur Startseite</a>
    </div>
</div>
