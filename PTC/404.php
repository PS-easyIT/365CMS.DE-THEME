<?php
/**
 * PTC Theme – 404 Fehlerseite
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

http_response_code(404);
?>

<section class="ptc-page-hero">
    <div class="ptc-container">
        <p class="ptc-hero-overline">Fehler 404</p>
        <h1>Seite nicht gefunden</h1>
        <p>Die angeforderte Seite existiert nicht oder wurde verschoben.</p>
    </div>
</section>

<div class="ptc-page-content">
    <div class="ptc-container" style="text-align:center;padding:5rem 0;">
        <div style="font-size:5rem;margin-bottom:1.5rem;">🔍</div>
        <h2 style="font-size:1.5rem;font-weight:700;color:var(--ptc-navy);margin-bottom:1rem;">Ups – hier gibt es nichts zu sehen.</h2>
        <p style="color:var(--ptc-slate);margin-bottom:2.5rem;max-width:440px;margin-left:auto;margin-right:auto;">
            Möglicherweise wurde die Seite umbenannt oder gelöscht. Nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.
        </p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?php echo ptc_site_url(); ?>/" class="btn-ptc btn-ptc-primary btn-ptc-lg">🏠 Zur Startseite</a>
            <a href="<?php echo ptc_site_url(); ?>/#kontakt" class="btn-ptc btn-ptc-ghost btn-ptc-lg">Kontakt aufnehmen</a>
        </div>
    </div>
</div>
