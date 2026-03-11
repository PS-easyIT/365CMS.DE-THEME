<?php
/**
 * Statische Seite – Vollbreite-Template (ohne Sidebar, volle Container-Breite)
 *
 * Template-ID: page-wide
 * Unterschiede zu page.php:
 *  - Kein 2-Spalten-Layout, kein .sidebar
 *  - Volle Container-Breite (kein max-width)
 *  - Geeignet für Landing-Seiten, Portfolio, Showcases
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

try {
    $slug = trim((string)($_GET['slug'] ?? (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '')), '/ ');
    $page = $slug !== '' ? \CMS\PageManager::instance()->getPageBySlug($slug) : null;
    if (!$page) {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }
} catch (\Throwable $e) {
    http_response_code(404);
    get_theme_part('404');
    exit;
}
?>

<div class="container page-shell page-shell--wide">

    <!-- Breadcrumb + Seitentitel -->
    <div class="page-header-block page-header-block--wide" data-anim>
        <div class="breadcrumbs">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>">Startseite</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></span>
        </div>
        <h1><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></h1>
        <?php if (!empty($page['updated_at'])): ?>
        <p class="page-last-updated">
            Zuletzt aktualisiert: <?php echo htmlspecialchars(date('j. F Y', strtotime($page['updated_at'])), ENT_QUOTES); ?>
        </p>
        <?php endif; ?>
    </div>

    <!-- Seiteninhalt volle Breite -->
    <div class="page-content page-content--full" data-anim data-anim-delay="1">
        <?php echo $page['content'] ?? ''; ?>
    </div>

</div><!-- /.container -->
