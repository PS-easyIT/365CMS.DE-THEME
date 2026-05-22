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

get_header();
?>

<section class="ptc-page-hero">
    <div class="ptc-container">
        <p class="ptc-hero-overline">Fehler 404</p>
        <h1>Seite nicht gefunden</h1>
    </div>
</section>

<div class="ptc-page-content">
    <div class="ptc-container ptc-center-block">
        <div class="ptc-error-code" aria-hidden="true">404</div>
        <h2>Die angeforderte Seite existiert nicht.</h2>
        <p class="ptc-text-muted">Der Link ist veraltet oder die Adresse wurde falsch eingegeben.</p>
        <div class="ptc-error-actions">
            <a href="<?php echo ptc_href('/'); ?>" class="btn-ptc btn-ptc-primary btn-ptc-lg">Zur Startseite</a>
            <a href="<?php echo ptc_href('/#kontakt'); ?>" class="btn-ptc btn-ptc-ghost btn-ptc-lg">Kontakt</a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
