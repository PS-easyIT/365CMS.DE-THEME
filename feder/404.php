<?php
declare(strict_types=1);

/**
 * Feder – Seite nicht gefunden (404)
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!headers_sent()) {
    http_response_code(404);
}

$recent = feder_get_posts(['limit' => 4]);
?>

<section class="fd-notfound fd-measure" aria-labelledby="fd-notfound-title">
    <p class="fd-notfound__code" aria-hidden="true">404</p>
    <p class="fd-kicker">Seite nicht gefunden</p>
    <h1 class="fd-archive-head__title" id="fd-notfound-title">Diese Seite ist wohl zwischen den Zeilen verloren gegangen.</h1>
    <p class="fd-archive-head__text">Vielleicht wurde der Text verschoben oder die Adresse hat sich geändert. Die Suche oder einer der neuesten Beiträge helfen weiter.</p>
    <?php feder_search_form('fd-search-404', '', 'fd-searchform--large'); ?>

    <?php if ($recent !== []) : ?>
        <h2 class="fd-section-title">Zuletzt erschienen</h2>
        <ul class="fd-simplelist">
            <?php foreach ($recent as $item) : ?>
                <li>
                    <a href="<?php echo feder_e(feder_post_link($item)); ?>"><?php echo feder_e((string) ($item->title ?? '')); ?></a>
                    <time datetime="<?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'iso')); ?>"><?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'short')); ?></time>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><a class="fd-button" href="<?php echo feder_e(feder_url('/')); ?>">Zur Startseite</a></p>
</section>
