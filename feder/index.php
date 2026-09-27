<?php
declare(strict_types=1);

/**
 * Feder – Fallback-Template
 *
 * Greift für Templates ohne eigene Datei (z. B. „authors“, „sitemap“). Mit $posts
 * (Archive) wird das Archiv-Template genutzt, sonst eine Übersicht der neuesten Texte.
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($posts) && is_array($posts)) {
    require __DIR__ . '/blog.php';
    return;
}

$recent = feder_get_posts(['limit' => 10]);
$topics = feder_get_categories();
?>

<header class="fd-archive-head fd-measure">
    <p class="fd-kicker">Übersicht</p>
    <h1 class="fd-archive-head__title"><?php echo feder_e(feder_site_title()); ?></h1>
    <?php if (feder_site_description() !== '') : ?>
        <p class="fd-archive-head__text"><?php echo feder_e(feder_site_description()); ?></p>
    <?php endif; ?>
</header>

<section class="fd-archive fd-measure" aria-label="Inhalte">
    <?php if ($recent !== []) : ?>
        <h2 class="fd-section-title">Neueste Texte</h2>
        <ul class="fd-simplelist">
            <?php foreach ($recent as $item) : ?>
                <li>
                    <a href="<?php echo feder_e(feder_post_link($item)); ?>"><?php echo feder_e((string) ($item->title ?? '')); ?></a>
                    <time datetime="<?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'iso')); ?>"><?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'short')); ?></time>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($topics !== []) : ?>
        <h2 class="fd-section-title">Themen</h2>
        <ul class="fd-chips">
            <?php foreach ($topics as $topic) : ?>
                <li><a class="fd-chip" href="<?php echo feder_e($topic['url']); ?>"><?php echo feder_e($topic['name']); ?><span class="fd-chip__count"><?php echo (int) $topic['count']; ?></span></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($recent === [] && $topics === []) : ?>
        <p class="fd-empty">Hier gibt es noch keine Inhalte.</p>
    <?php endif; ?>
</section>
