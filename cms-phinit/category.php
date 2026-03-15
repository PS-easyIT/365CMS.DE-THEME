<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$category = isset($category) ? (array) $category : [];
$posts = isset($posts) && is_array($posts) ? $posts : [];
$total = isset($total) ? (int) $total : 0;
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$blogQuery = isset($query) ? trim((string) $query) : trim((string) ($_GET['q'] ?? ''));
$categoryName = trim((string) ($category['name'] ?? 'Kategorie'));
$categorySlug = trim((string) ($category['slug'] ?? ''));
$categoryDescription = trim((string) ($category['description'] ?? ''));
$blogBaseUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/kategorie/' . rawurlencode($categorySlug), $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/kategorie/' . rawurlencode($categorySlug);
$blogUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog';
?>

<div class="container blog-shell blog-shell--archive">
    <section class="phinit-archive-hero" data-anim>
        <div class="phinit-archive-hero__content">
            <span class="phinit-archive-hero__eyebrow">Kategorie-Archiv</span>
            <h1><?php echo htmlspecialchars($categoryName, ENT_QUOTES); ?></h1>
            <p class="phinit-archive-hero__lead">
                <?php echo htmlspecialchars($categoryDescription !== '' ? $categoryDescription : 'Alle veröffentlichten Beiträge zu diesem Themenbereich – kompakt gesammelt und filterbar.', ENT_QUOTES); ?>
            </p>
        </div>
        <div class="phinit-archive-hero__stats" aria-label="Archivstatistik">
            <div class="phinit-archive-stat">
                <span class="phinit-archive-stat__value"><?php echo $total; ?></span>
                <span class="phinit-archive-stat__label">Beiträge</span>
            </div>
            <div class="phinit-archive-stat">
                <span class="phinit-archive-stat__value"><?php echo htmlspecialchars($categoryName, ENT_QUOTES); ?></span>
                <span class="phinit-archive-stat__label">Thema</span>
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
        <p><strong>Keine Beiträge in dieser Kategorie</strong></p>
        <p class="empty-state__text">Aktuell wurden in <strong><?php echo htmlspecialchars($categoryName, ENT_QUOTES); ?></strong> noch keine passenden Artikel veröffentlicht.</p>
        <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>" class="btn btn-primary empty-state__action">← Zum Blog</a>
    </div>
    <?php endif; ?>
</div>
