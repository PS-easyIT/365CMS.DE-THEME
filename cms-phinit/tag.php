<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$tag = isset($tag) ? (array) $tag : [];
$posts = isset($posts) && is_array($posts) ? $posts : [];
$total = isset($total) ? (int) $total : 0;
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$blogQuery = isset($query) ? trim((string) $query) : trim((string) ($_GET['q'] ?? ''));
$tagName = trim((string) ($tag['name'] ?? 'Tag'));
$tagSlug = trim((string) ($tag['slug'] ?? ''));
$tagBaseUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/tag/' . rawurlencode($tagSlug), $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/tag/' . rawurlencode($tagSlug);
$blogUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/blog', $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/blog';
?>

<div class="container blog-shell blog-shell--archive">
    <section class="phinit-archive-hero" data-anim>
        <div class="phinit-archive-hero__content">
            <span class="phinit-archive-hero__eyebrow">Tag-Archiv</span>
            <h1>#<?php echo htmlspecialchars($tagName, ENT_QUOTES); ?></h1>
            <p class="phinit-archive-hero__lead">Alle Beiträge, die mit diesem Schlagwort versehen wurden – praktisch für Serienthemen, Tools, Releases und How-to-Cluster.</p>
        </div>
        <div class="phinit-archive-hero__stats" aria-label="Archivstatistik">
            <div class="phinit-archive-stat">
                <span class="phinit-archive-stat__value"><?php echo $total; ?></span>
                <span class="phinit-archive-stat__label">Treffer</span>
            </div>
            <div class="phinit-archive-stat">
                <span class="phinit-archive-stat__value">#<?php echo htmlspecialchars($tagName, ENT_QUOTES); ?></span>
                <span class="phinit-archive-stat__label">Schlagwort</span>
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
        <p><strong>Keine Beiträge mit diesem Tag</strong></p>
        <p class="empty-state__text">Für <strong>#<?php echo htmlspecialchars($tagName, ENT_QUOTES); ?></strong> gibt es aktuell noch keine veröffentlichten Artikel.</p>
        <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES); ?>" class="btn btn-primary empty-state__action">← Zum Blog</a>
    </div>
    <?php endif; ?>
</div>
