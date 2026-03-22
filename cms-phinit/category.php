<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = function_exists('home_url') ? home_url() : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$category = isset($category) ? (array) $category : [];
$posts = isset($posts) && is_array($posts) ? $posts : [];
$isOverview = !empty($isOverview);
$overviewItems = isset($overviewItems) && is_array($overviewItems) ? $overviewItems : [];
$total = isset($total) ? (int) $total : 0;
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$blogQuery = isset($query) ? trim((string) $query) : trim((string) ($_GET['q'] ?? ''));
$categoryName = trim((string) ($category['name'] ?? ($isOverview ? 'Kategorien' : 'Kategorie')));
$categorySlug = trim((string) ($category['slug'] ?? ''));
$categoryDescription = trim((string) ($category['description'] ?? ''));
$blogBaseUrl = function_exists('cms_get_archive_url')
    ? cms_get_archive_url('category', $isOverview ? '' : $categorySlug, $currentLocale)
    : (function_exists('phinit_localized_href')
        ? phinit_localized_href('/kategorie/' . rawurlencode($categorySlug), $currentLocale, $siteUrl)
        : rtrim($siteUrl, '/') . '/kategorie/' . rawurlencode($categorySlug));
$homeUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/';
$blogUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog';
$archiveSummary = $isOverview
    ? ($categoryDescription !== ''
        ? $categoryDescription
        : 'Alle veröffentlichten Themenbereiche im Überblick – direkt zum passenden Archiv springen.')
    : ($categoryDescription !== ''
        ? $categoryDescription
        : 'Alle veröffentlichten Beiträge zu diesem Themenbereich – kompakt gesammelt und filterbar.');
?>

<?php if ($isOverview): ?>
<div class="container phinit-special-page">
    <section class="phinit-special-hero" data-anim>
        <div class="phinit-special-hero__content">
            <span class="phinit-special-hero__eyebrow">Archiv-Navigation</span>
            <h1>Kategorien-Sitemap</h1>
            <p class="phinit-special-hero__lead"><?php echo htmlspecialchars($archiveSummary, ENT_QUOTES); ?></p>
        </div>
        <div class="phinit-special-hero__stats" aria-label="Kategorien Sitemap Statistik">
            <div class="phinit-special-stat">
                <span class="phinit-special-stat__value"><?php echo $total; ?></span>
                <span class="phinit-special-stat__label">Bereiche</span>
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
        'blogBaseUrl' => $blogBaseUrl,
        'backUrl' => $blogUrl,
        'backLabel' => 'Alle Artikel',
        'secondaryUrl' => $homeUrl,
        'secondaryLabel' => 'Zur Startseite',
    ]);
    ?>

    <section class="phinit-sitemap-grid" data-anim data-anim-delay="1">
        <article class="phinit-sitemap-card">
            <header class="phinit-sitemap-card__header">
                <h2>Alle Kategorien</h2>
                <span class="phinit-sitemap-card__count"><?php echo $total; ?></span>
            </header>

            <?php if (!empty($overviewItems)): ?>
            <ul class="phinit-sitemap-list">
                <?php foreach ($overviewItems as $overviewItem): ?>
                    <?php
                    $itemTitle = trim((string) ($overviewItem['title'] ?? 'Kategorie'));
                    $itemDescription = trim((string) ($overviewItem['description'] ?? ''));
                    $itemCount = (int) ($overviewItem['count'] ?? 0);
                    $itemUrl = trim((string) ($overviewItem['url'] ?? ''));
                    ?>
                <li class="phinit-sitemap-list__item">
                    <a href="<?php echo htmlspecialchars($itemUrl !== '' ? $itemUrl : '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($itemTitle, ENT_QUOTES); ?></a>
                    <span class="phinit-sitemap-list__meta"><?php echo $itemCount; ?> Beiträge<?php echo $itemDescription !== '' ? ' • ' . htmlspecialchars($itemDescription, ENT_QUOTES) : ''; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="phinit-sitemap-card__empty">Keine Kategorien vorhanden.</p>
            <?php endif; ?>
        </article>
    </section>

    <?php
    get_theme_part('partials/blog-archive-pagination', [
        'siteUrl' => $siteUrl,
        'blogPage' => $currentPage,
        'blogPages' => $totalPages,
        'blogBaseUrl' => $blogBaseUrl,
        'queryParams' => $blogQuery !== '' ? ['q' => $blogQuery] : [],
    ]);
    ?>
</div>
<?php else: ?>
<div class="container blog-shell blog-shell--archive">
    <h1 class="visually-hidden"><?php echo htmlspecialchars('Kategorie: ' . $categoryName, ENT_QUOTES); ?></h1>
    <p class="blog-search-hint" data-anim>
        Kategorie <strong><?php echo htmlspecialchars($categoryName, ENT_QUOTES); ?></strong>
        &mdash; <?php echo $total; ?> Beiträge
        &mdash; <?php echo htmlspecialchars($archiveSummary, ENT_QUOTES); ?>
    </p>

    <?php
    get_theme_part('partials/blog-archive-toolbar', [
        'siteUrl' => $siteUrl,
        'blogQuery' => $blogQuery,
        'blogTotal' => $total,
        'blogBaseUrl' => $blogBaseUrl,
        'backUrl' => $blogUrl,
        'backLabel' => 'Alle Artikel',
        'secondaryUrl' => $homeUrl,
        'secondaryLabel' => 'Zur Startseite',
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
        'blogBaseUrl' => $blogBaseUrl,
        'queryParams' => $blogQuery !== '' ? ['q' => $blogQuery] : [],
    ]);
    ?>
    <?php else: ?>
    <div class="empty-state" data-anim data-anim-delay="1">
        <p class="empty-state__icon">📂</p>
        <p><strong><?php echo htmlspecialchars($isOverview ? 'Keine Kategorien gefunden' : 'Keine Beiträge in dieser Kategorie', ENT_QUOTES); ?></strong></p>
        <p class="empty-state__text">
            <?php if ($isOverview): ?>
                Für die aktuelle Auswahl wurden noch keine Kategorien gefunden.
            <?php else: ?>
                Aktuell wurden in <strong><?php echo htmlspecialchars($categoryName, ENT_QUOTES); ?></strong> noch keine passenden Artikel veröffentlicht.
            <?php endif; ?>
        </p>
        <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>" class="btn btn-primary empty-state__action">← Zum Blog</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
