<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Fallback-Template
 *
 * Greift für Templates ohne eigene Datei (z. B. „authors“, „sitemap“). Mit $posts
 * (Archive) wird das Blog-Template genutzt, sonst eine Seitenübersicht.
 *
 * @package Showcase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($posts) && is_array($posts)) {
    require __DIR__ . '/blog.php';
    return;
}

$recent = showcase_get_posts(['limit' => 8]);
$categories = showcase_get_categories();
$path = strtolower(trim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/'));
$title = str_ends_with($path, 'sitemap') ? 'Seitenübersicht' : 'Übersicht';
$sections = [
    ['label' => 'Funktionen', 'url' => '/#funktionen'],
    ['label' => 'Rollen & Perspektiven', 'url' => '/#rollen'],
    ['label' => 'Sicherheit', 'url' => '/#sicherheit'],
    ['label' => 'Für Entwickler', 'url' => '/#entwickler'],
    ['label' => 'Themes', 'url' => '/#themes'],
    ['label' => 'Release Notes', 'url' => '/#neuigkeiten'],
    ['label' => 'Installation', 'url' => '/#installation'],
    ['label' => 'Häufige Fragen', 'url' => '/#faq'],
];
?>

<header class="sc-pagehero">
    <div class="sc-pagehero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-pagehero__inner">
        <nav class="sc-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo showcase_e(showcase_url('/')); ?>">Start</a></li>
                <li><span aria-current="page"><?php echo showcase_e($title); ?></span></li>
            </ol>
        </nav>
        <h1 class="sc-pagehero__title"><?php echo showcase_e($title); ?></h1>
        <?php if (showcase_site_description() !== '') : ?>
            <p class="sc-pagehero__lead"><?php echo showcase_e(showcase_site_description()); ?></p>
        <?php endif; ?>
    </div>
</header>

<div class="sc-container sc-sitemap">
    <section aria-labelledby="sc-sitemap-product">
        <h2 class="sc-subheading" id="sc-sitemap-product">Produkt</h2>
        <ul class="sc-sitemap__list">
            <?php foreach ($sections as $section) : ?>
                <li><a href="<?php echo showcase_e(showcase_url($section['url'])); ?>"><?php echo showcase_e($section['label']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php if ($categories !== []) : ?>
        <section aria-labelledby="sc-sitemap-categories">
            <h2 class="sc-subheading" id="sc-sitemap-categories">Kategorien</h2>
            <ul class="sc-sitemap__list">
                <?php foreach ($categories as $item) : ?>
                    <li><a href="<?php echo showcase_e($item['url']); ?>"><?php echo showcase_e($item['name']); ?></a> <span class="sc-filter__count"><?php echo (int) $item['count']; ?></span></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if ($recent !== []) : ?>
        <section aria-labelledby="sc-sitemap-recent">
            <h2 class="sc-subheading" id="sc-sitemap-recent">Neueste Beiträge</h2>
            <ul class="sc-sitemap__list">
                <?php foreach ($recent as $item) : ?>
                    <li><a href="<?php echo showcase_e(showcase_post_link($item)); ?>"><?php echo showcase_e((string) ($item->title ?? '')); ?></a></li>
                <?php endforeach; ?>
                <li><a href="<?php echo showcase_e(showcase_url('/blog')); ?>">Alle Neuigkeiten</a></li>
            </ul>
        </section>
    <?php endif; ?>
</div>
