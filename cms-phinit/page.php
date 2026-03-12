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
$pageContent = (string)($page['content'] ?? '');

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

// $page wird vom Router via ThemeManager::render('page', ['page' => $page]) bereitgestellt
// Falls nicht vorhanden (direkter Zugriff), Slug aus URL extrahieren
if (!isset($page) || empty($page)) {
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

$pageContent = (string)($page['content'] ?? $pageContent);
$isHubSitePage = (($page['content_type'] ?? '') === 'hub') || str_contains($pageContent, 'cms-hub-site');

// Nicht gefunden → Fehlermeldung im Content-Bereich anzeigen (Header wurde bereits gesendet)
$pageNotFound = empty($page);

// TOC für Seiten generieren (wenn aktiviert)
$_pg_toc = [];
if (!$pageNotFound && $_pg_showToc) {
    $usedPgSlugs = [];
    preg_match_all('/<h([23])[^>]*>(.*?)<\/h\1>/si', $page['content'] ?? '', $_hm, PREG_SET_ORDER);
    foreach ($_hm as $_hx) {
        $text = trim(strip_tags($_hx[2]));
        $id   = mb_strtolower($text, 'UTF-8');
        $id   = preg_replace('/[äÄ]/', 'ae', preg_replace('/[öÖ]/', 'oe', preg_replace('/[üÜ]/', 'ue', preg_replace('/ß/', 'ss', $id))));
        $id   = trim(preg_replace('/[^a-z0-9]+/', '-', $id), '-') ?: 'heading';
        $base = $id; $i = 2;
        while (in_array($id, $usedPgSlugs, true)) { $id = $base . '-' . $i++; }
        $usedPgSlugs[] = $id;
        $_pg_toc[] = ['level' => (int)$_hx[1], 'id' => $id, 'text' => $text];
    }
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
    $_pg_dateFormatted = htmlspecialchars(date('j. F Y', strtotime($page['updated_at'])), ENT_QUOTES);
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
        <?php echo $pageContent; ?>
    </div>
<?php else: ?>

    <!-- Seiten-Titel -->
    <?php if ($_pg_showTitle): ?>
    <div class="page-header-block<?php echo (!empty($page['featured_image']) && $_pg_showHero) ? ' page-header-block--with-image' : ''; ?>" data-anim>
        <?php if (!empty($page['featured_image']) && $_pg_showHero): ?>
        <img class="page-hero-img"
             src="<?php echo htmlspecialchars($page['featured_image'], ENT_QUOTES); ?>"
             alt="<?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?>"
             loading="eager">
        <?php endif; ?>
        <div class="page-header-body">
            <h1><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></h1>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($_pg_layout === 'two-col' && $_pg_showSidebar): ?>
    <!-- 2-spaltig: Inhalt + Sidebar -->
    <div class="post-layout page-layout-grid">

        <div data-anim data-anim-delay="1">
            <?php if ($_pg_showToc && count($_pg_toc) >= 2): ?>
            <details class="toc-box page-toc page-toc--inline" data-inline-toc>
                <summary class="page-toc__summary">
                    <span class="page-toc__summary-main">
                        <span class="page-toc__summary-icon" aria-hidden="true">&#x1F4CB;</span>
                        <span class="page-toc__summary-copy">
                            <span class="page-toc__eyebrow">Schnellnavigation</span>
                            <span class="page-toc__summary-text">Inhaltsverzeichnis</span>
                        </span>
                    </span>
                    <span class="page-toc__summary-meta">
                        <span class="page-toc__count"><?php echo (int) count($_pg_toc); ?> Punkte</span>
                        <span class="page-toc__hint" aria-hidden="true"></span>
                        <span class="page-toc__chevron" aria-hidden="true">▾</span>
                    </span>
                </summary>
                <nav class="page-toc__body" aria-label="Inhaltsverzeichnis der Seite">
                    <ol class="page-toc__list">
                        <?php foreach ($_pg_toc as $_ti): ?>
                        <li class="page-toc__item<?php echo $_ti['level'] === 3 ? ' page-toc__item--nested' : ''; ?>">
                            <a href="#<?php echo htmlspecialchars($_ti['id'], ENT_QUOTES); ?>" class="page-toc__link"><?php echo htmlspecialchars($_ti['text'], ENT_QUOTES); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </details>
            <?php endif; ?>
            <div class="page-content"><?php echo $pageContent; ?></div>
            <?php echo $_pg_updatedPill; ?>
        </div>

        <aside class="post-sidebar">
            <?php if ($_pg_sidebarNav && !empty($_pgNavItems)): ?>
            <div class="sidebar-widget page-sidebar-nav">
                <h4 class="page-sidebar-nav__title">Navigation</h4>
                <nav>
                    <?php foreach ($_pgNavItems as $_ni): ?>
                    <a href="<?php echo htmlspecialchars($_ni['url'] ?? '#', ENT_QUOTES); ?>"
                       class="page-sidebar-nav__link"><?php echo htmlspecialchars($_ni['label'] ?? '', ENT_QUOTES); ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <?php endif; ?>
        </aside>

    </div>

    <?php else: ?>
    <!-- Volle Breite oder schmal -->
    <?php $pageContentClass = $_pg_layout === 'narrow' ? ' page-content--narrow' : ''; ?>
    <?php if ($_pg_showToc && count($_pg_toc) >= 2): ?>
    <details class="toc-box page-toc page-toc--inline<?php echo $pageContentClass; ?>" data-inline-toc data-anim>
        <summary class="page-toc__summary">
            <span class="page-toc__summary-main">
                <span class="page-toc__summary-icon" aria-hidden="true">&#x1F4CB;</span>
                <span class="page-toc__summary-copy">
                    <span class="page-toc__eyebrow">Schnellnavigation</span>
                    <span class="page-toc__summary-text">Inhaltsverzeichnis</span>
                </span>
            </span>
            <span class="page-toc__summary-meta">
                <span class="page-toc__count"><?php echo (int) count($_pg_toc); ?> Punkte</span>
                <span class="page-toc__hint" aria-hidden="true"></span>
                <span class="page-toc__chevron" aria-hidden="true">▾</span>
            </span>
        </summary>
        <nav class="page-toc__body" aria-label="Inhaltsverzeichnis der Seite">
            <ol class="page-toc__list">
                <?php foreach ($_pg_toc as $_ti): ?>
                <li class="page-toc__item<?php echo $_ti['level'] === 3 ? ' page-toc__item--nested' : ''; ?>">
                    <a href="#<?php echo htmlspecialchars($_ti['id'], ENT_QUOTES); ?>" class="page-toc__link"><?php echo htmlspecialchars($_ti['text'], ENT_QUOTES); ?></a>
                </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </details>
    <?php endif; ?>
    <div class="page-content<?php echo $pageContentClass; ?>" data-anim data-anim-delay="1">
        <?php echo $pageContent; ?>
    </div>
    <?php echo $_pg_updatedPill; ?>
    <?php endif; ?>

<?php endif; ?>
</div><!-- /.container -->
