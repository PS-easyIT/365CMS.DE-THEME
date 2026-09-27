<?php
declare(strict_types=1);

/**
 * Kompass – Fallback-Template
 *
 * Greift für Templates ohne eigene Datei (z. B. „authors“, „sitemap“). Mit $posts
 * (Archive) wird das Archiv-Template genutzt, sonst eine Seitenübersicht aus Themen,
 * Schnellzugriffen und aktuellen Informationen.
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($posts) && is_array($posts)) {
    require __DIR__ . '/blog.php';
    return;
}

$topics = kompass_categories();
$quickLinks = kompass_quick_links();
$recent = kompass_get_posts(['limit' => 8]);
$path = strtolower(trim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/'));
$title = str_ends_with($path, 'sitemap') ? 'Seitenübersicht' : 'Übersicht';
?>

<header class="kp-pagehead">
    <div class="kp-container">
        <nav class="kp-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kompass_e(kompass_url('/')); ?>">Start</a></li>
                <li><span aria-current="page"><?php echo kompass_e($title); ?></span></li>
            </ol>
        </nav>
        <h1 class="kp-pagehead__title"><?php echo kompass_e($title); ?></h1>
        <?php if (kompass_site_description() !== '') : ?>
            <p class="kp-pagehead__lead"><?php echo kompass_e(kompass_site_description()); ?></p>
        <?php endif; ?>
    </div>
</header>

<div class="kp-container kp-sitemap">
    <?php if ($topics !== []) : ?>
        <section aria-labelledby="kp-sitemap-topics">
            <h2 class="kp-section__title" id="kp-sitemap-topics">Themenbereiche</h2>
            <ul class="kp-sitemap__list">
                <?php foreach ($topics as $topic) : ?>
                    <li>
                        <a href="<?php echo kompass_e($topic['url']); ?>"><?php echo kompass_e($topic['name']); ?></a>
                        <?php if ($topic['children'] !== []) : ?>
                            <ul>
                                <?php foreach ($topic['children'] as $child) : ?>
                                    <li><a href="<?php echo kompass_e($child['url']); ?>"><?php echo kompass_e($child['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <li><a href="<?php echo kompass_e(kompass_archive_url('tag')); ?>">Themen A–Z</a></li>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($quickLinks !== []) : ?>
        <section aria-labelledby="kp-sitemap-quick">
            <h2 class="kp-section__title" id="kp-sitemap-quick">Schnellzugriff</h2>
            <ul class="kp-sitemap__list">
                <?php foreach ($quickLinks as $link) : ?>
                    <li><a href="<?php echo kompass_e($link['url']); ?>"><?php echo kompass_e($link['label']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($recent !== []) : ?>
        <section aria-labelledby="kp-sitemap-recent">
            <h2 class="kp-section__title" id="kp-sitemap-recent">Aktuelle Informationen</h2>
            <ul class="kp-sitemap__list">
                <?php foreach ($recent as $item) : ?>
                    <li><a href="<?php echo kompass_e(kompass_post_link($item)); ?>"><?php echo kompass_e((string) ($item->title ?? '')); ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?php echo kompass_e(kompass_url('/blog')); ?>">Alle Meldungen</a></li>
            </ul>
        </section>
    <?php endif; ?>
</div>
