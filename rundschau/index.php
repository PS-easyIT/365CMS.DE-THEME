<?php
declare(strict_types=1);

/**
 * Rundschau – Fallback-Template
 *
 * Greift für Templates ohne eigene Datei (z. B. „authors“, „sitemap“). Mit $posts
 * (Archive) wird das Archiv-Template genutzt, sonst eine kompakte Nachrichtenübersicht.
 *
 * @package Rundschau_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($posts) && is_array($posts)) {
    require __DIR__ . '/blog.php';
    return;
}

$latest = rundschau_get_posts(['limit' => 12]);
$ressorts = array_values(array_filter(rundschau_categories(), static fn(array $c): bool => $c['total'] > 0));
?>

<header class="rs-pagehead">
    <div class="rs-container">
        <p class="rs-pagehead__kicker">Übersicht</p>
        <h1 class="rs-pagehead__title"><?php echo rundschau_e(rundschau_site_title()); ?></h1>
        <?php if (rundschau_site_description() !== '') : ?>
            <p class="rs-pagehead__text"><?php echo rundschau_e(rundschau_site_description()); ?></p>
        <?php endif; ?>
    </div>
</header>

<div class="rs-container rs-layout rs-layout--sidebar">
    <div class="rs-layout__main">
        <h2 class="rs-section-title"><span>Neueste Meldungen</span></h2>
        <?php if ($latest === []) : ?>
            <p class="rs-empty">Noch keine Meldungen vorhanden.</p>
        <?php else : ?>
            <ol class="rs-timeline rs-timeline--wide">
                <?php foreach ($latest as $item) :
                    $date = (string) ($item->published_at ?? $item->created_at ?? '');
                    ?>
                    <li class="rs-timeline__item">
                        <time datetime="<?php echo rundschau_e(rundschau_format_date($date, 'iso')); ?>"><?php echo rundschau_e(rundschau_time_label($date)); ?></time>
                        <div>
                            <?php echo rundschau_kicker($item, 'rs-kicker rs-kicker--small'); ?>
                            <a class="rs-timeline__link" href="<?php echo rundschau_e(rundschau_post_link($item)); ?>"><?php echo rundschau_e((string) ($item->title ?? '')); ?></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
    <aside class="rs-layout__aside" aria-label="Ressorts">
        <?php if ($ressorts !== []) : ?>
            <section class="rs-box" aria-labelledby="rs-index-ressorts">
                <h2 class="rs-box__title" id="rs-index-ressorts">Ressorts</h2>
                <ul class="rs-ressortlist">
                    <?php foreach ($ressorts as $ressort) : ?>
                        <li class="<?php echo rundschau_e(rundschau_ressort_class($ressort['slug'])); ?>"><a href="<?php echo rundschau_e($ressort['url']); ?>"><span><?php echo rundschau_e($ressort['name']); ?></span><span class="rs-ressortlist__count"><?php echo (int) $ressort['total']; ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </aside>
</div>
