<?php
declare(strict_types=1);

/**
 * Rundschau – Seite nicht gefunden (404)
 *
 * @package Rundschau_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!headers_sent()) {
    http_response_code(404);
}

$latest = rundschau_get_posts(['limit' => 4, 'with_content' => true]);
?>

<header class="rs-pagehead rs-pagehead--error">
    <div class="rs-container">
        <p class="rs-pagehead__kicker">Fehler 404</p>
        <h1 class="rs-pagehead__title">Diese Meldung gibt es nicht (mehr).</h1>
        <p class="rs-pagehead__text">Der Link ist veraltet oder die Seite wurde verschoben. Suchen Sie nach einem Stichwort oder lesen Sie die neuesten Nachrichten.</p>
        <?php rundschau_search_form('rs-search-404', '', 'rs-searchform--large'); ?>
    </div>
</header>

<div class="rs-container">
    <?php if ($latest !== []) : ?>
        <h2 class="rs-section-title"><span>Neueste Meldungen</span></h2>
        <div class="rs-grid rs-grid--4">
            <?php foreach ($latest as $item) : ?>
                <?php rundschau_teaser($item, 'card', 'h3'); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p class="rs-center"><a class="rs-button" href="<?php echo rundschau_e(rundschau_url('/')); ?>">Zur Startseite</a></p>
</div>
