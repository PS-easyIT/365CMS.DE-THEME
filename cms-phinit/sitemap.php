<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$sitemap = phinit_build_html_sitemap_view_model();
$sections = [
    [
        'title' => 'Direktzugriff',
        'items' => $sitemap['quickLinks'] ?? [],
    ],
    [
        'title' => 'Seiten',
        'items' => $sitemap['pages'] ?? [],
    ],
    [
        'title' => 'Kategorien',
        'items' => $sitemap['categories'] ?? [],
    ],
    [
        'title' => 'Tags',
        'items' => $sitemap['tags'] ?? [],
    ],
    [
        'title' => 'Autoren',
        'items' => $sitemap['authors'] ?? [],
    ],
    [
        'title' => 'Neueste Beiträge',
        'items' => $sitemap['recentPosts'] ?? [],
    ],
];
?>

<div class="container phinit-special-page">
    <section class="phinit-special-hero" data-anim>
        <div class="phinit-special-hero__content">
            <span class="phinit-special-hero__eyebrow">Reader Tools</span>
            <h1>HTML-Sitemap</h1>
            <p class="phinit-special-hero__lead">Eine leserfreundliche Übersicht über wichtige Seiten, Kategorien, Tags, Autoren und aktuelle Artikel. Ideal für schnelle Orientierung – ganz ohne XML-Brille.</p>
        </div>
        <div class="phinit-special-hero__stats" aria-label="Sitemap Statistik">
            <?php foreach ($sections as $section): ?>
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo count(is_array($section['items']) ? $section['items'] : []); ?></span>
                <span class="phinit-special-stat__label"><?php echo htmlspecialchars((string) ($section['title'] ?? 'Bereich'), ENT_QUOTES); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="phinit-sitemap-grid" data-anim data-anim-delay="1">
        <?php foreach ($sections as $section): ?>
        <?php $items = is_array($section['items'] ?? null) ? $section['items'] : []; ?>
        <article class="phinit-sitemap-card">
            <header class="phinit-sitemap-card__header">
                <h2><?php echo htmlspecialchars((string) ($section['title'] ?? 'Bereich'), ENT_QUOTES); ?></h2>
                <span class="phinit-sitemap-card__count"><?php echo count($items); ?></span>
            </header>

            <?php if ($items !== []): ?>
            <ul class="phinit-sitemap-list">
                <?php foreach ($items as $item): ?>
                <?php
                    $item = is_array($item) ? $item : [];
                    $itemTitle = trim((string) ($item['title'] ?? 'Eintrag'));
                    $itemUrl = trim((string) ($item['url'] ?? '#'));
                    $itemMeta = trim((string) ($item['meta'] ?? ''));
                    $itemCount = isset($item['count']) ? (int) $item['count'] : null;
                    $itemAuthor = trim((string) ($item['author'] ?? ''));
                ?>
                <li class="phinit-sitemap-list__item">
                    <a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($itemTitle, ENT_QUOTES); ?></a>
                    <?php if ($itemCount !== null): ?>
                    <span class="phinit-sitemap-list__meta"><?php echo $itemCount; ?> Einträge</span>
                    <?php elseif ($itemMeta !== ''): ?>
                    <span class="phinit-sitemap-list__meta"><?php echo htmlspecialchars(phinit_format_date($itemMeta, 'numeric', $currentLocale), ENT_QUOTES); ?><?php echo $itemAuthor !== '' ? ' • ' . htmlspecialchars($itemAuthor, ENT_QUOTES) : ''; ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="phinit-sitemap-card__empty">Keine Einträge vorhanden.</p>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </section>
</div>
