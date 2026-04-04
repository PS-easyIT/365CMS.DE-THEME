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
$pageContent = '';

// ── Customizer-Einstellungen (Seiten-Ansicht) ──────────────────────────────
try {
    $_pc = \CMS\Services\ThemeCustomizer::instance();
    $_pg_showTitle   = filter_var($_pc->get('pages', 'show_page_title',        true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_showHero    = filter_var($_pc->get('pages', 'show_page_hero',         true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_showDate    = filter_var($_pc->get('pages', 'show_page_updated_date', true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_layout      = (string)$_pc->get('pages', 'page_layout', 'full');     // full | narrow | two-col
    $_pg_showSidebar = filter_var($_pc->get('pages', 'show_page_sidebar',      true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_sidebarNav  = filter_var($_pc->get('pages', 'page_sidebar_show_nav',  true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_showToc     = filter_var($_pc->get('pages', 'show_page_toc',          false), FILTER_VALIDATE_BOOLEAN);
} catch (\Throwable $_e) {
    $_pg_showTitle = true; $_pg_showHero = true; $_pg_showDate = true; $_pg_layout = 'full';
    $_pg_showSidebar = true; $_pg_sidebarNav = true; $_pg_showToc = false;
}

$pageProvidedByRouter = isset($page) && !empty($page);

// $page wird vom Router via ThemeManager::render('page', ['page' => $page]) bereitgestellt
// Falls nicht vorhanden (direkter Zugriff), Slug aus URL extrahieren
if (!$pageProvidedByRouter) {
    try {
        $slug = trim((string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''), '/ ');
        $page = $slug !== '' ? \CMS\PageManager::instance()->getPageBySlug($slug) : null;
    } catch (\Throwable $e) {
        $page = null;
    }
}

// Page ist stdClass aus Router → in Array konvertieren
if (is_object($page)) {
    $page = (array)$page;
}

$pageId = (int)($page['id'] ?? 0);
$pageContent = (string)($page['content'] ?? '');
$isHubSitePage = (($page['content_type'] ?? '') === 'hub') || str_contains($pageContent, 'cms-hub-site');
$isCookieConsentPage = (($page['content_type'] ?? '') === 'cookie_consent') || (($page['slug'] ?? '') === 'cookie-einstellungen');
$isImageArchivePage = is_array($page) && phinit_is_image_archive_page($page);
if (!$pageProvidedByRouter && !$isHubSitePage) {
    $pageContent = phinit_prepare_renderable_content($pageContent, 'page', $pageId);
}

if ($isHubSitePage) {
    // Hub-Markup stammt bereits aus dem Core-Renderer und muss seine Struktur/
    // Klassen auf section/article/nav/details behalten. Eine zweite Default-
    // Sanitizer-Runde würde genau diese Selektoren wieder entfernen.
    $pageHeadingData = ['html' => $pageContent, 'toc' => []];
    $safePageContent = $pageContent;
} else {
    $pageContent = phinit_sanitize_renderable_content($pageContent, 'default');
    $pageHeadingData = phinit_with_heading_ids($pageContent, [2, 3, 4, 5, 6]);
    $pageContent = phinit_enhance_content_images($pageHeadingData['html']);
    $safePageContent = (string) sanitize_html($pageContent, 'default');
}

$favoriteControl = !$isHubSitePage
    ? phinit_get_favorite_control('page', (int) ($page['id'] ?? 0), [
        'title' => (string) ($page['title'] ?? 'Seite'),
        'url' => phinit_current_request_path(),
        'excerpt' => trim(mb_substr(strip_tags($pageContent), 0, 180)),
        'featured_image' => (string) ($page['featured_image'] ?? ''),
        'badge' => 'Seite',
    ])
    : [];

// Nicht gefunden → Fehlermeldung im Content-Bereich anzeigen (Header wurde bereits gesendet)
$pageNotFound = empty($page);

// TOC für Seiten generieren (wenn aktiviert)
$_pg_toc = [];
if (!$pageNotFound && $_pg_showToc) {
    $_pg_toc = $pageHeadingData['toc'];
}

// Navigationsmenü für Sidebar laden (wenn Layout = two-col)
$_pgNavItems = [];
if ($_pg_layout === 'two-col' && $_pg_sidebarNav) {
    try { $_pgNavItems = \CMS\ThemeManager::instance()->getMenu('primary'); } catch (\Throwable $_e) {}
}
?>

<?php
// Aktualisierungs-Pill (wird in beiden Layouts ans Ende des Contents gehängt)
$_pg_updatedPill = '';
if ($_pg_showDate && !empty($page['updated_at'])) {
    $_pg_pageTimestamp = strtotime((string) ($page['updated_at'] ?? ''));
    $_pg_dateFormatted = htmlspecialchars($_pg_pageTimestamp !== false ? date('j. F Y', $_pg_pageTimestamp) : '—', ENT_QUOTES);
    $_pg_updatedPill = '<div class="page-updated-pill-wrap"><span class="page-updated-pill">🕒 Zuletzt aktualisiert: ' . $_pg_dateFormatted . '</span></div>';
}
?>
<div class="container page-shell<?php echo $isHubSitePage ? ' page-shell--hub' : ' page-shell--compact'; ?>">

<?php if ($pageNotFound): ?>
    <div class="page-empty-state">
        <p class="page-empty-state__icon">🔍</p>
        <h1 class="page-empty-state__title">Seite nicht gefunden</h1>
        <p class="page-empty-state__text">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zurück zur Startseite</a>
    </div>
<?php elseif ($isHubSitePage): ?>
    <div class="page-content page-content--hub" data-anim>
        <?php phinit_render_sanitized_content($safePageContent, 'hub'); ?>
    </div>
<?php elseif ($isImageArchivePage): ?>
    <?php $imageArchive = phinit_build_image_archive_view_model($page); ?>
    <?php include __DIR__ . '/partials/page-image-archive.php'; ?>
<?php elseif ($isCookieConsentPage): ?>
    <?php include __DIR__ . '/partials/page-cookie-consent.php'; ?>
<?php else: ?>

    <!-- Seiten-Titel -->
    <?php if ($_pg_showTitle): ?>
    <?php include __DIR__ . '/partials/page-header-block.php'; ?>
    <?php endif; ?>

    <?php if ($_pg_layout === 'two-col' && $_pg_showSidebar): ?>
    <!-- 2-spaltig: Inhalt + Sidebar -->
    <div class="post-layout page-layout-grid">

        <div data-anim data-anim-delay="1">
            <?php if ($_pg_showToc && count($_pg_toc) >= 2): ?>
            <?php $pageTocClass = ''; ?>
            <?php include __DIR__ . '/partials/page-inline-toc.php'; ?>
            <?php endif; ?>
            <div class="page-content"><?php phinit_render_sanitized_content($safePageContent, 'default'); ?></div>
            <?php echo $_pg_updatedPill; ?>
        </div>

        <aside class="post-sidebar">
            <?php if ($_pg_sidebarNav && !empty($_pgNavItems)): ?>
            <?php include __DIR__ . '/partials/page-sidebar-nav.php'; ?>
            <?php endif; ?>
        </aside>

    </div>

    <?php else: ?>
    <!-- Volle Breite oder schmal -->
    <?php $pageContentClass = $_pg_layout === 'narrow' ? ' page-content--narrow' : ''; ?>
    <?php if ($_pg_showToc && count($_pg_toc) >= 2): ?>
    <?php $pageTocClass = $pageContentClass; ?>
    <?php include __DIR__ . '/partials/page-inline-toc.php'; ?>
    <?php endif; ?>
    <div class="page-content<?php echo $pageContentClass; ?>" data-anim data-anim-delay="1">
        <?php phinit_render_sanitized_content($safePageContent, 'default'); ?>
    </div>
    <?php echo $_pg_updatedPill; ?>
    <?php endif; ?>

<?php endif; ?>
</div><!-- /.container -->
