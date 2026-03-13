<?php
/**
 * Homepage Template – CMS Phinit Theme
 *
 * Layout (alle Sektionen per Customizer konfigurierbar):
 *  1. Repo-Card (optional)
 *  2. Aktuelle Artikel-Liste (horizontal)
 *  3. Info-Cards 2-spaltig (Kategorie-Highlights)
 *  4. Kachel-Grid (paginiert)
 *  5. RSS-Feed-Sektion (cms-feed Plugin)
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
extract($homepageViewModel, EXTR_SKIP);

// ── Startseiten-Daten laden (Posts, Grid-Paginierung, Sidebar-Featured) ──────
extract(phinit_get_homepage_posts_payload($homepageViewModel), EXTR_SKIP);
$feedSections = phinit_get_homepage_feed_sections();
?>
<style>
    .home-shell {
        --home-sp-repo: <?php echo (int)$_spRepo; ?>px;
        --home-sp-list: <?php echo (int)$_spList; ?>px;
        --home-sp-info: <?php echo (int)$_spInfo; ?>px;
        --home-sp-grid: <?php echo (int)$_spGrid; ?>px;
        --home-sp-rss: <?php echo (int)$_spRss; ?>px;
        --hp-sidebar-w: <?php echo (int)$_listSidebarWidth; ?>px;
        --hp-tile-thumb-h: <?php echo (int)$_tileImageH; ?>px;
        --home-sp-top: <?php echo (int)$_homeHeaderSpacing; ?>px;
    }
    <?php if (!empty($_sbProj1LogoUrl)): ?>
    .sb-project-card--project1 { background-image: url('<?php echo htmlspecialchars($_sbProj1LogoUrl, ENT_QUOTES); ?>'); }
    <?php endif; ?>
    <?php if (!empty($_sbProj2LogoUrl)): ?>
    .sb-project-card--project2 { background-image: url('<?php echo htmlspecialchars($_sbProj2LogoUrl, ENT_QUOTES); ?>'); }
    <?php endif; ?>
</style>
<?php \CMS\Hooks::doAction('home_content'); ?>
<div class="container home-shell">

    <!-- ── Repo-Card ─────────────────────────────────────────────── -->
    <?php get_theme_part('partials/home-repo-card', $homepageViewModel); ?>

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
