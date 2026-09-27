<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Seite nicht gefunden (404)
 *
 * @package Showcase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!headers_sent()) {
    http_response_code(404);
}
?>

<section class="sc-notfound" aria-labelledby="sc-notfound-title">
    <div class="sc-hero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-notfound__inner">
        <p class="sc-notfound__code" aria-hidden="true">404</p>
        <p class="sc-eyebrow sc-eyebrow--light">Seite nicht gefunden</p>
        <h1 class="sc-notfound__title" id="sc-notfound-title">Diese Seite ist nicht Teil des <span class="sc-grad-text">Releases</span>.</h1>
        <p class="sc-notfound__text">Vielleicht wurde sie verschoben oder die Adresse hat sich geändert. Die Suche hilft weiter – oder Sie starten auf der Produktseite.</p>
        <?php showcase_search_form('sc-search-404', '', 'hero'); ?>
        <p class="sc-notfound__actions">
            <?php echo showcase_button('Zur Startseite', '/', 'primary', 'arrow'); ?>
            <?php echo showcase_button('Neuigkeiten lesen', '/blog', 'ghost-light', ''); ?>
        </p>
    </div>
</section>
