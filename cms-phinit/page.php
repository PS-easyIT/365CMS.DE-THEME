<?php
/**
 * Statische Seite – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

try {
    $pageService = \CMS\Services\PageService::instance();
    $slug        = trim($_GET['slug'] ?? $_SERVER['REQUEST_URI'] ?? '', '/ ');
    $page        = $pageService->getPageBySlug($slug);
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

<div class="container" style="padding-top:28px;padding-bottom:40px;">
<div class="content-layout">

    <!-- ── Haupt-Inhalt ─────────────────────────────────────── -->
    <div class="main-column">

        <!-- Seiten-Header mit Breadcrumb -->
        <div class="page-header-block" data-anim>
            <div class="breadcrumbs" style="margin-bottom:10px;">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>">Startseite</a>
                <span>/</span>
                <span><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></span>
            </div>
            <h1><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></h1>
            <?php if (!empty($page['updated_at'])): ?>
            <p style="font-size:var(--fs-xs);color:var(--text-light);margin-top:6px;">
                Zuletzt aktualisiert: <?php echo htmlspecialchars(date('j. F Y', strtotime($page['updated_at'])), ENT_QUOTES); ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Seiten-Inhalt -->
        <div class="page-content" data-anim data-anim-delay="1">
            <?php
            // Nur nach vorheriger Sanitierung ausgeben
            echo $page['content'] ?? '';
            ?>
        </div>

    </div><!-- /.main-column -->

    <!-- ── Sidebar ────────────────────────────────────────── -->
    <aside class="sidebar" aria-label="Seitenleiste">

        <div class="social-widget">
            <div class="social-widget-title">Kontakt & Social</div>
            <div class="social-icons">
                <a href="https://linkedin.com" class="li" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in</a>
                <a href="https://github.com"   class="gh" target="_blank" rel="noopener noreferrer" aria-label="GitHub">gh</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/feed" class="rss" aria-label="RSS-Feed">⊞</a>
            </div>
        </div>

        <div class="toc" style="border-left-color:var(--accent-color);">
            <div class="toc-title">🔗 Navigation</div>
            <ul class="toc-list" role="list">
                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/">Startseite</a></li>
                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/ueber-uns">Über mich</a></li>
                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kontakt">Kontakt</a></li>
                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/impressum">Impressum</a></li>
                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutzerklaerung">Datenschutzerklärung</a></li>
            </ul>
        </div>

    </aside>

</div><!-- /.content-layout -->
</div><!-- /.container -->
