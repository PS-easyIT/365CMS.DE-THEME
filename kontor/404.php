<?php
declare(strict_types=1);

/**
 * Kontor – Seite nicht gefunden (404)
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!headers_sent()) {
    http_response_code(404);
}

$services = array_slice(kontor_services(), 0, 6);
?>

<section class="kt-notfound">
    <div class="kt-container kt-notfound__inner">
        <p class="kt-notfound__code" aria-hidden="true">404</p>
        <p class="kt-eyebrow kt-eyebrow--light">Seite nicht gefunden</p>
        <h1 class="kt-hero__title">Diese Seite gibt es leider nicht.</h1>
        <p class="kt-hero__text">Vielleicht wurde sie verschoben oder die Adresse ist nicht mehr aktuell. Nutzen Sie die Suche oder starten Sie auf der Startseite.</p>
        <div class="kt-notfound__search"><?php kontor_search_form('kt-search-404'); ?></div>
        <div class="kt-hero__actions">
            <?php echo kontor_button('Zur Startseite', '/', 'accent', true); ?>
            <?php echo kontor_button(kontor_text('header', 'header_cta_label', 'Beratung anfragen'), kontor_text('header', 'header_cta_url', '/kontakt'), 'outline-light'); ?>
        </div>
    </div>
</section>

<?php if ($services !== []) : ?>
    <section class="kt-section" aria-labelledby="kt-404-services">
        <div class="kt-container">
            <h2 class="kt-section__title" id="kt-404-services">Unsere Leistungen im Überblick</h2>
            <ul class="kt-services kt-services--compact">
                <?php foreach ($services as $service) : ?>
                    <li class="kt-service">
                        <span class="kt-service__icon"><?php echo kontor_icon($service['icon']); ?></span>
                        <h3 class="kt-service__title"><a href="<?php echo kontor_e(kontor_url($service['url'] !== '' ? $service['url'] : '/#leistungen')); ?>"><?php echo kontor_e($service['title']); ?></a></h3>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php endif; ?>
