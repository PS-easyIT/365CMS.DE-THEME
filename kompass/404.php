<?php
declare(strict_types=1);

/**
 * Kompass – Seite nicht gefunden (404)
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!headers_sent()) {
    http_response_code(404);
}

$quickLinks = kompass_quick_links();
?>

<header class="kp-pagehead kp-pagehead--center">
    <div class="kp-container kp-notfound">
        <span class="kp-notfound__icon"><?php echo kompass_icon('compass'); ?></span>
        <p class="kp-eyebrow">Fehler 404</p>
        <h1 class="kp-pagehead__title">Diese Seite wurde nicht gefunden.</h1>
        <p class="kp-pagehead__lead">Die Adresse ist möglicherweise veraltet oder falsch geschrieben. Nutzen Sie die Suche oder einen der Einstiege unten.</p>
        <?php kompass_search_form('kp-search-404', '', 'page'); ?>
        <p class="kp-notfound__actions">
            <a class="kp-button kp-button--primary" href="<?php echo kompass_e(kompass_url('/')); ?>"><?php echo kompass_icon('home'); ?><span>Zur Startseite</span></a>
            <a class="kp-button kp-button--ghost" href="<?php echo kompass_e(kompass_archive_url('category')); ?>"><span>Alle Themen</span></a>
        </p>
    </div>
</header>

<?php if ($quickLinks !== []) : ?>
    <section class="kp-section" aria-labelledby="kp-404-quick">
        <div class="kp-container">
            <h2 class="kp-section__title" id="kp-404-quick">Häufig genutzt</h2>
            <ul class="kp-quick">
                <?php foreach ($quickLinks as $link) : ?>
                    <li>
                        <a class="kp-quick__link" href="<?php echo kompass_e($link['url']); ?>">
                            <span class="kp-quick__icon"><?php echo kompass_icon($link['icon']); ?></span>
                            <span class="kp-quick__label"><?php echo kompass_e($link['label']); ?></span>
                            <?php echo kompass_icon('chevron', 'kp-icon kp-quick__chevron'); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php endif; ?>

<div class="kp-section kp-section--service">
    <div class="kp-container">
        <?php kompass_service_box('h2'); ?>
    </div>
</div>
