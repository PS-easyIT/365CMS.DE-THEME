<?php
/**
 * Homepage Template – CMS Phinit Theme
 *
 * Layout (alle Sektionen per Customizer konfigurierbar):
 *  1. Aktuelle Artikel-Liste (horizontal)
 *  2. Themenbereiche mit Repo-Card (optional) und Info-Cards
 *  3. Kachel-Grid (paginiert)
 *  4. RSS-Feed-Sektion (cms-feed Plugin)
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/theme-home-helpers.php';

$siteUrl      = SITE_URL;

// ── Customizer-Einstellungen laden ──────────────────────────────────────────
$homepageViewModel = phinit_get_homepage_view_model();
$_spRepo = (int) ($homepageViewModel['_spRepo'] ?? 32);
$_spList = (int) ($homepageViewModel['_spList'] ?? 32);
$_spInfo = (int) ($homepageViewModel['_spInfo'] ?? 32);
$_spGrid = (int) ($homepageViewModel['_spGrid'] ?? 32);
$_spRss = (int) ($homepageViewModel['_spRss'] ?? 0);
$_listSidebarWidth = (int) ($homepageViewModel['_listSidebarWidth'] ?? 260);
$_tileImageH = (int) ($homepageViewModel['_tileImageH'] ?? 161);
$homeShellStyle = implode(' ', [
    '--home-sp-repo: ' . (int) $_spRepo . 'px;',
    '--home-sp-list: ' . (int) $_spList . 'px;',
    '--home-sp-info: ' . (int) $_spInfo . 'px;',
    '--home-sp-grid: ' . (int) $_spGrid . 'px;',
    '--home-sp-rss: ' . (int) $_spRss . 'px;',
    '--hp-sidebar-w: ' . (int) $_listSidebarWidth . 'px;',
    '--hp-tile-thumb-h: ' . (int) $_tileImageH . 'px;',
]);

// ── Startseiten-Daten laden (Posts, Grid-Paginierung, Sidebar-Featured) ──────
$homepagePostsPayload = phinit_get_homepage_posts_payload($homepageViewModel);
$featuredPosts = is_array($homepagePostsPayload['featuredPosts'] ?? null) ? $homepagePostsPayload['featuredPosts'] : [];
$featuredBannerPost = is_array($homepagePostsPayload['featuredBannerPost'] ?? null) ? $homepagePostsPayload['featuredBannerPost'] : null;
$featuredBannerPosts = is_array($homepagePostsPayload['featuredBannerPosts'] ?? null) ? $homepagePostsPayload['featuredBannerPosts'] : [];
$gridPosts = is_array($homepagePostsPayload['gridPosts'] ?? null) ? $homepagePostsPayload['gridPosts'] : [];
$sbFeaturedPosts = is_array($homepagePostsPayload['sbFeaturedPosts'] ?? null) ? $homepagePostsPayload['sbFeaturedPosts'] : [];
$currentPage = (int) ($homepagePostsPayload['currentPage'] ?? 1);
$totalPages = (int) ($homepagePostsPayload['totalPages'] ?? 1);
$feedSections = phinit_get_homepage_feed_sections();
?>
<?php \CMS\Hooks::doAction('home_content'); ?>
<div class="container home-shell" style="<?php echo htmlspecialchars($homeShellStyle, ENT_QUOTES); ?>">

    <!-- ── Featured-Banner ──────────────────────────────────── -->
    <?php get_theme_part('partials/home-featured-banner', array_merge($homepageViewModel, [
        'featuredBannerPost' => $featuredBannerPost,
        'featuredBannerPosts' => $featuredBannerPosts,
        'siteUrl' => $siteUrl,
    ])); ?>

    <!-- ── Artikel-Liste ─────────────────────────────────────── -->
    <?php get_theme_part('partials/home-article-list', array_merge($homepageViewModel, [
        'featuredPosts' => $featuredPosts,
        'sbFeaturedPosts' => $sbFeaturedPosts,
        'siteUrl' => $siteUrl,
    ])); ?>

    <!-- ── Kategorie-Cards (Info-Grid) ───────────────────────── -->
    <?php get_theme_part('partials/home-info-grid', array_merge($homepageViewModel, [
        'siteUrl' => $siteUrl,
    ])); ?>

    <?php get_theme_part('partials/home-post-grid', array_merge($homepageViewModel, [
        'gridPosts' => $gridPosts,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'siteUrl' => $siteUrl,
    ])); ?>

    <?php get_theme_part('partials/home-feed-sections', [
        'feedSections' => $feedSections,
    ]); ?>

</div><!-- /.container -->
