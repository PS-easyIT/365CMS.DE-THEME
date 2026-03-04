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

// ── Customizer-Einstellungen (Seiten-Ansicht) ──────────────────────────────
try {
    $_pc = \CMS\Services\ThemeCustomizer::instance();
    $_pg_showTitle   = filter_var($_pc->get('pages', 'show_page_title',        true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_showDate    = filter_var($_pc->get('pages', 'show_page_updated_date', true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_layout      = (string)$_pc->get('pages', 'page_layout', 'full');     // full | narrow | two-col
    $_pg_showSidebar = filter_var($_pc->get('pages', 'show_page_sidebar',      true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_sidebarNav  = filter_var($_pc->get('pages', 'page_sidebar_show_nav',  true),  FILTER_VALIDATE_BOOLEAN);
    $_pg_showToc     = filter_var($_pc->get('pages', 'show_page_toc',          false), FILTER_VALIDATE_BOOLEAN);
} catch (\Throwable $_e) {
    $_pg_showTitle = true; $_pg_showDate = true; $_pg_layout = 'full';
    $_pg_showSidebar = true; $_pg_sidebarNav = true; $_pg_showToc = false;
}

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

<?php $_pg_padTop = 'padding-top:28px'; ?>
<?php
// Aktualisierungs-Pill (wird in beiden Layouts ans Ende des Contents gehängt)
$_pg_updatedPill = '';
if ($_pg_showDate && !empty($page['updated_at'])) {
    $_pg_dateFormatted = htmlspecialchars(date('j. F Y', strtotime($page['updated_at'])), ENT_QUOTES);
    $_pg_updatedPill = '<div class="page-updated-pill-wrap"><span class="page-updated-pill">🕒 Zuletzt aktualisiert: ' . $_pg_dateFormatted . '</span></div>';
}
?>
<div class="container" style="<?php echo $_pg_padTop; ?>;padding-bottom:13px;">

<?php if ($pageNotFound): ?>
    <div style="text-align:center;padding:4rem 2rem;">
        <p style="font-size:3rem;margin:0 0 1rem;">🔍</p>
        <h1 style="color:var(--text-primary);margin-bottom:.5rem;">Seite nicht gefunden</h1>
        <p style="color:var(--text-muted);max-width:480px;margin:0 auto 1.5rem;">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zurück zur Startseite</a>
    </div>
<?php else: ?>

    <!-- Seiten-Titel -->
    <?php if ($_pg_showTitle): ?>
    <div class="page-header-block" data-anim>
        <h1><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></h1>
    </div>
    <?php endif; ?>

    <?php if ($_pg_layout === 'two-col' && $_pg_showSidebar): ?>
    <!-- 2-spaltig: Inhalt + Sidebar -->
    <div class="post-layout"<?php echo ($_pg_layout === 'two-col') ? ' style="display:grid;grid-template-columns:1fr var(--sidebar-width,300px);gap:2rem;align-items:start;"' : ''; ?>>

        <div data-anim data-anim-delay="1">
            <?php if ($_pg_showToc && count($_pg_toc) >= 2): ?>
            <nav class="toc-box" style="background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;">
                <strong style="font-size:.88rem;color:var(--text-secondary);">&#x1F4CB; Inhaltsverzeichnis</strong>
                <ol style="margin:10px 0 0 18px;padding:0;font-size:.85rem;line-height:1.8;">
                    <?php foreach ($_pg_toc as $_ti): ?>
                    <li<?php echo $_ti['level'] === 3 ? ' style="margin-left:16px;"' : ''; ?>>
                        <a href="#<?php echo htmlspecialchars($_ti['id'], ENT_QUOTES); ?>" style="color:var(--text-secondary);text-decoration:none;"><?php echo htmlspecialchars($_ti['text'], ENT_QUOTES); ?></a>
                    </li>
                    <?php endforeach; ?>
                </ol>
            </nav>
            <?php endif; ?>
            <div class="page-content"><?php echo $page['content'] ?? ''; ?></div>
            <?php echo $_pg_updatedPill; ?>
        </div>

        <aside class="post-sidebar">
            <?php if ($_pg_sidebarNav && !empty($_pgNavItems)): ?>
            <div class="sidebar-widget" style="background:var(--bg-primary);border:1px solid var(--border-color);border-radius:var(--radius);padding:16px 18px;margin-bottom:20px;">
                <h4 style="font-size:.9rem;font-weight:700;margin:0 0 12px;color:var(--text-primary);">Navigation</h4>
                <nav>
                    <?php foreach ($_pgNavItems as $_ni): ?>
                    <a href="<?php echo htmlspecialchars($_ni['url'] ?? '#', ENT_QUOTES); ?>"
                       style="display:block;padding:5px 0;font-size:.85rem;color:var(--text-secondary);text-decoration:none;border-bottom:1px solid var(--border-color);"><?php echo htmlspecialchars($_ni['label'] ?? '', ENT_QUOTES); ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <?php endif; ?>
        </aside>

    </div>

    <?php else: ?>
    <!-- Volle Breite oder schmal -->
    <?php $pageContentStyle = ($_pg_layout === 'narrow') ? 'max-width:860px;margin-left:auto;margin-right:auto;' : ''; ?>
    <?php if ($_pg_showToc && count($_pg_toc) >= 2): ?>
    <nav class="toc-box" style="<?php echo $pageContentStyle; ?>background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;" data-anim>
        <strong style="font-size:.88rem;color:var(--text-secondary);">&#x1F4CB; Inhaltsverzeichnis</strong>
        <ol style="margin:10px 0 0 18px;padding:0;font-size:.85rem;line-height:1.8;">
            <?php foreach ($_pg_toc as $_ti): ?>
            <li<?php echo $_ti['level'] === 3 ? ' style="margin-left:16px;"' : ''; ?>>
                <a href="#<?php echo htmlspecialchars($_ti['id'], ENT_QUOTES); ?>" style="color:var(--text-secondary);text-decoration:none;"><?php echo htmlspecialchars($_ti['text'], ENT_QUOTES); ?></a>
            </li>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php endif; ?>
    <div class="page-content" data-anim data-anim-delay="1" style="<?php echo $pageContentStyle; ?>">
        <?php echo $page['content'] ?? ''; ?>
    </div>
    <?php echo $_pg_updatedPill; ?>
    <?php endif; ?>

<?php endif; ?>
</div><!-- /.container -->
