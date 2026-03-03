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

// $page wird vom Router via ThemeManager::render('page', ['page' => $page]) bereitgestellt
// Falls nicht vorhanden (direkter Zugriff), Slug aus URL extrahieren
if (!isset($page) || empty($page)) {
    try {
        $pageService = \CMS\Services\PageService::instance();
        $slug        = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/ ');
        $page        = $pageService->getPageBySlug($slug);
    } catch (\Throwable $e) {
        $page = null;
    }
}

// Page ist stdClass aus Router → in Array konvertieren
if (is_object($page)) {
    $page = (array)$page;
}

// Nicht gefunden → Fehlermeldung im Content-Bereich anzeigen (Header wurde bereits gesendet)
$pageNotFound = empty($page);
?>

<div class="container" style="padding-top:28px;padding-bottom:40px;">

<?php if ($pageNotFound): ?>
    <div style="text-align:center;padding:4rem 2rem;">
        <p style="font-size:3rem;margin:0 0 1rem;">🔍</p>
        <h1 style="color:var(--text-primary);margin-bottom:.5rem;">Seite nicht gefunden</h1>
        <p style="color:var(--text-muted);max-width:480px;margin:0 auto 1.5rem;">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zurück zur Startseite</a>
    </div>
<?php else: ?>

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
        <?php echo $page['content'] ?? ''; ?>
    </div>

<?php endif; ?>
</div><!-- /.container -->
