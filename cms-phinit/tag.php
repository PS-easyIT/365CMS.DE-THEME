<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = function_exists('home_url') ? home_url() : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$tag = isset($tag) ? (array) $tag : [];
$isOverview = !empty($isOverview);
$overviewItems = isset($overviewItems) && is_array($overviewItems) ? $overviewItems : [];
$posts = isset($posts) && is_array($posts) ? $posts : [];
$total = isset($total) ? (int) $total : 0;
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$posts = array_map(
    static fn($archivePost): array => is_object($archivePost) ? get_object_vars($archivePost) : (array) $archivePost,
    $posts
);
$posts = function_exists('phinit_prepare_homepage_posts') ? phinit_prepare_homepage_posts($posts, $currentLocale) : $posts;
$blogQuery = isset($query) ? trim((string) $query) : phinit_input_string($_GET, 'q', '', 200);
$tagName = trim((string) ($tag['name'] ?? ($isOverview ? 'Schlagwörter' : 'Tag')));
$tagSlug = trim((string) ($tag['slug'] ?? ''));
$tagDescription = trim((string) ($tag['description'] ?? ''));
$tagBaseUrl = function_exists('cms_get_archive_url')
    ? cms_get_archive_url('tag', $isOverview ? '' : $tagSlug, $currentLocale)
    : (function_exists('phinit_localized_href')
        ? phinit_localized_href('/tag/' . rawurlencode($tagSlug), $currentLocale, $siteUrl)
        : rtrim($siteUrl, '/') . '/tag/' . rawurlencode($tagSlug));
$blogUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog';
$archiveSummary = $isOverview
    ? ($tagDescription !== ''
        ? $tagDescription
        : 'Alle Schlagwörter im Überblick – kompakt sortiert nach Relevanz und direkt verlinkt.')
    : ($tagDescription !== ''
        ? $tagDescription
        : 'Alle Beiträge, die mit diesem Schlagwort versehen wurden – praktisch für Serienthemen, Tools, Releases und How-to-Cluster.');
?>

<?php if ($isOverview): ?>
<div class="container phinit-special-page">
    <section class="phinit-special-hero" data-anim>
        <div class="phinit-special-hero__content">
            <span class="phinit-special-hero__eyebrow">Archiv-Navigation</span>
            <h1>Tag-Sitemap</h1>
            <p class="phinit-special-hero__lead"><?php echo htmlspecialchars($archiveSummary, ENT_QUOTES); ?></p>
        </div>
        <div class="phinit-special-hero__stats" aria-label="Tag Sitemap Statistik">
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo $total; ?></span>
                <span class="phinit-special-stat__label">Tags</span>
            </div>
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo $currentPage; ?>/<?php echo max(1, $totalPages); ?></span>
                <span class="phinit-special-stat__label">Seite</span>
            </div>
        </div>
    </section>

    <?php
    get_theme_part('partials/blog-archive-toolbar', [
        'siteUrl' => $siteUrl,
        'blogQuery' => $blogQuery,
        'blogTotal' => $total,
        'blogBaseUrl' => $tagBaseUrl,
        'backUrl' => $blogUrl,
        'backLabel' => 'Alle Artikel',
    ]);
    ?>

    <section class="phinit-sitemap-grid" data-anim data-anim-delay="1">
        <article class="phinit-sitemap-card">
            <header class="phinit-sitemap-card__header">
                <h2>Alle Tags</h2>
                <span class="phinit-sitemap-card__count"><?php echo $total; ?></span>
            </header>

            <?php if (!empty($overviewItems)): ?>
            <ul class="phinit-sitemap-list">
                <?php foreach ($overviewItems as $overviewItem): ?>
                    <?php
                    $itemTitle = trim((string) ($overviewItem['title'] ?? 'Tag'));
                    $itemCount = (int) ($overviewItem['count'] ?? 0);
                    $itemUrl = trim((string) ($overviewItem['url'] ?? ''));
                    ?>
                <li class="phinit-sitemap-list__item">
                    <a href="<?php echo htmlspecialchars($itemUrl !== '' ? $itemUrl : '#', ENT_QUOTES); ?>">#<?php echo htmlspecialchars($itemTitle, ENT_QUOTES); ?></a>
                    <span class="phinit-sitemap-list__meta"><?php echo $itemCount; ?> Treffer</span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="phinit-sitemap-card__empty">Keine Tags vorhanden.</p>
            <?php endif; ?>
        </article>
    </section>

    <?php
    get_theme_part('partials/blog-archive-pagination', [
        'siteUrl' => $siteUrl,
        'blogPage' => $currentPage,
        'blogPages' => $totalPages,
        'blogBaseUrl' => $tagBaseUrl,
        'queryParams' => $blogQuery !== '' ? ['q' => $blogQuery] : [],
    ]);
    ?>
</div>
<?php else: ?>
<div class="container blog-shell blog-shell--archive">
    <h1 class="visually-hidden"><?php echo htmlspecialchars('Tag: ' . $tagName, ENT_QUOTES); ?></h1>
    <p class="blog-search-hint" data-anim>
        Schlagwort <strong>#<?php echo htmlspecialchars($tagName, ENT_QUOTES); ?></strong>
        &mdash; <?php echo $total; ?> Treffer
        &mdash; <?php echo htmlspecialchars($archiveSummary, ENT_QUOTES); ?>
    </p>

    <?php
    get_theme_part('partials/blog-archive-toolbar', [
        'siteUrl' => $siteUrl,
        'blogQuery' => $blogQuery,
        'blogTotal' => $total,
        'blogBaseUrl' => $tagBaseUrl,
        'backUrl' => $blogUrl,
        'backLabel' => 'Alle Artikel',
    ]);
    ?>

    <?php if (!empty($posts)): ?>
    <div class="article-list article-list--framed" data-anim data-anim-delay="1">
        <?php foreach ($posts as $archivePost): ?>
            <?php get_theme_part('partials/post-card', [
                'card' => is_object($archivePost) ? (array) $archivePost : (array) $archivePost,
                'siteUrl' => $siteUrl,
                'show_excerpt' => true,
                'show_meta' => true,
                'exc_len' => 180,
            ]); ?>
        <?php endforeach; ?>
    </div>

    <?php
    get_theme_part('partials/blog-archive-pagination', [
        'siteUrl' => $siteUrl,
        'blogPage' => $currentPage,
        'blogPages' => $totalPages,
        'blogBaseUrl' => $tagBaseUrl,
        'queryParams' => $blogQuery !== '' ? ['q' => $blogQuery] : [],
    ]);
    ?>
    <?php else: ?>
    <div class="empty-state" data-anim data-anim-delay="1">
        <p class="empty-state__icon">🏷️</p>
        <p><strong><?php echo htmlspecialchars($isOverview ? 'Keine Schlagwörter gefunden' : 'Keine Beiträge mit diesem Tag', ENT_QUOTES); ?></strong></p>
        <p class="empty-state__text">
            <?php if ($isOverview): ?>
                Für die aktuelle Auswahl wurden noch keine Schlagwörter gefunden.
            <?php else: ?>
                Für <strong>#<?php echo htmlspecialchars($tagName, ENT_QUOTES); ?></strong> gibt es aktuell noch keine veröffentlichten Artikel.
            <?php endif; ?>
        </p>
        <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>" class="btn btn-primary empty-state__action">← Zum Blog</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
