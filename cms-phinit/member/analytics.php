<?php
/**
 * Member Analytics – CMS Phinit Theme
 *
 * Admin-only Analyse-Übersicht innerhalb des Memberbereichs.
 * Kombiniert SEO-, Traffic- und Feature-Nutzungsdaten.
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$auth = \CMS\Auth::instance();
if (!$auth->isLoggedIn()) {
    header('Location: ' . theme_login_url());
    exit;
}

if (!$auth->isAdmin()) {
    header('Location: ' . SITE_URL . '/member/dashboard');
    exit;
}

$currentUser = $auth->getCurrentUser();
$db          = \CMS\Database::instance();
$prefix      = $db->getPrefix();
$siteUrl     = SITE_URL;
$activePage  = 'analytics';
$themeDir    = \CMS\ThemeManager::instance()->getThemePath();

$analyticsService = \CMS\Services\AnalyticsService::getInstance();
$seoService       = \CMS\Services\SEOService::getInstance();

$visitorStats = [
    'total' => 0,
    'unique' => 0,
    'active_now' => 0,
    'bounce_rate' => '0%',
    'avg_session_duration' => '0s',
];
$topPages = [];
$featureUsage = ['totals' => [], 'top_features' => [], 'note' => ''];
$coreWebVitals = ['sample_count' => 0, 'problem_pages' => [], 'metrics' => [], 'note' => ''];
$auditRows = [];
$sitemapConfig = [];

try {
    $visitorStats = $analyticsService->getVisitorStats(30);
} catch (\Throwable) {
    $visitorStats = $visitorStats;
}

try {
    $topPages = $analyticsService->getTopPages(5);
} catch (\Throwable) {
    $topPages = [];
}

try {
    $featureUsage = $analyticsService->getFeatureUsageSummary(30);
} catch (\Throwable) {
    $featureUsage = ['totals' => [], 'top_features' => [], 'note' => ''];
}

try {
    $coreWebVitals = $analyticsService->getCoreWebVitals(30);
} catch (\Throwable) {
    $coreWebVitals = ['sample_count' => 0, 'problem_pages' => [], 'metrics' => [], 'note' => ''];
}

try {
    $auditRows = $seoService->getAuditRows();
} catch (\Throwable) {
    $auditRows = [];
}

try {
    $sitemapConfig = $seoService->getSitemapSettings();
} catch (\Throwable) {
    $sitemapConfig = [];
}

$seoSummary = [
    'audited_content' => count($auditRows),
    'missing_titles' => 0,
    'missing_descriptions' => 0,
    'missing_images' => 0,
    'noindex_items' => 0,
    'missing_keyphrases' => 0,
];

foreach ($auditRows as $row) {
    $metaTitle = trim((string) ($row['meta_title'] ?? ''));
    $metaDescription = trim((string) ($row['meta_description'] ?? ''));
    $featuredImage = trim((string) ($row['featured_image'] ?? ''));
    $focusKeyphrase = trim((string) ($row['focus_keyphrase'] ?? ''));
    $robotsIndex = (bool) ($row['robots_index'] ?? true);

    if ($metaTitle === '') {
        $seoSummary['missing_titles']++;
    }
    if ($metaDescription === '') {
        $seoSummary['missing_descriptions']++;
    }
    if ($featuredImage === '') {
        $seoSummary['missing_images']++;
    }
    if ($focusKeyphrase === '') {
        $seoSummary['missing_keyphrases']++;
    }
    if (!$robotsIndex) {
        $seoSummary['noindex_items']++;
    }
}

$seoAlerts = [];
if ($seoSummary['missing_descriptions'] > 0) {
    $seoAlerts[] = $seoSummary['missing_descriptions'] . ' Inhalte ohne Meta-Beschreibung';
}
if ($seoSummary['missing_titles'] > 0) {
    $seoAlerts[] = $seoSummary['missing_titles'] . ' Inhalte ohne Meta-Titel';
}
if ($seoSummary['missing_keyphrases'] > 0) {
    $seoAlerts[] = $seoSummary['missing_keyphrases'] . ' Inhalte ohne Fokus-Keyphrase';
}
if ($seoSummary['noindex_items'] > 0) {
    $seoAlerts[] = $seoSummary['noindex_items'] . ' Inhalte auf noindex gesetzt';
}

$topFeatures = array_slice((array) ($featureUsage['top_features'] ?? []), 0, 5);
$featureTotals = (array) ($featureUsage['totals'] ?? []);
$problemPages = array_slice((array) ($coreWebVitals['problem_pages'] ?? []), 0, 5);
$metrics = (array) ($coreWebVitals['metrics'] ?? []);

$postsPublished = 0;
$pendingComments = 0;
$trackedPageViews = 0;

try {
    $postsPublished = (int) ($db->get_var("SELECT COUNT(*) FROM {$prefix}posts WHERE " . phinit_post_publication_where()) ?: 0);
} catch (\Throwable) {
    $postsPublished = 0;
}

try {
    $pendingComments = (int) ($db->get_var("SELECT COUNT(*) FROM {$prefix}comments WHERE status = 'pending'") ?: 0);
} catch (\Throwable) {
    $pendingComments = 0;
}

try {
    $trackedPageViews = (int) ($db->get_var("SELECT COUNT(*) FROM {$prefix}page_views") ?: 0);
} catch (\Throwable) {
    $trackedPageViews = 0;
}

$homepageTitle = 'Nicht gesetzt';
$homepageDescription = 'Nicht gesetzt';
$globalDescription = 'Nicht gesetzt';
$titleFormat = 'Nicht gesetzt';
$titleSeparator = 'Nicht gesetzt';

try {
    $homepageTitle = $seoService->getHomepageTitle('Nicht gesetzt');
    $homepageDescription = $seoService->getHomepageDescription('Nicht gesetzt');
    $globalDescription = $seoService->getMetaDescription('Nicht gesetzt');
    $titleFormat = $seoService->getSiteTitleFormat();
    $titleSeparator = $seoService->getTitleSeparator();
} catch (\Throwable) {
    $homepageTitle = 'Nicht gesetzt';
    $homepageDescription = 'Nicht gesetzt';
    $globalDescription = 'Nicht gesetzt';
    $titleFormat = 'Nicht gesetzt';
    $titleSeparator = 'Nicht gesetzt';
}

$analyticsSettings = [
    'Homepage-Titel' => $homepageTitle,
    'Homepage-Beschreibung' => $homepageDescription,
    'Globale Description' => $globalDescription,
    'Titel-Format' => $titleFormat,
    'Separator' => $titleSeparator,
    'Sitemap Seiten' => !empty($sitemapConfig['include_pages']) ? 'Aktiv' : 'Inaktiv',
    'Sitemap Beiträge' => !empty($sitemapConfig['include_posts']) ? 'Aktiv' : 'Inaktiv',
    'Sitemap Bilder' => !empty($sitemapConfig['include_images']) ? 'Aktiv' : 'Inaktiv',
    'News-Sitemap' => !empty($sitemapConfig['include_news']) ? 'Aktiv' : 'Inaktiv',
];

include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main" id="main-content">
        <section class="member-analytics-hero" data-anim>
            <div class="member-analytics-hero__content">
                <span class="member-analytics-hero__eyebrow">📈 Admin Analytics</span>
                <h1>Analyse & SEO-Zentrale</h1>
                <p>Hier laufen die wichtigsten Member-Admin-Kennzahlen zusammen: Traffic, Core Web Vitals, SEO-Abdeckung und die meistgenutzten CMS-Funktionen.</p>
            </div>
            <div class="member-analytics-hero__actions">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/admin/analytics" class="member-analytics-link">Admin Analytics öffnen</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/admin/seo-dashboard" class="member-analytics-link">SEO-Dashboard öffnen</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/admin/" class="member-analytics-link">Admincenter</a>
            </div>
        </section>

        <div class="member-dashboard-overview member-dashboard-overview--analytics" data-anim data-anim-delay="1">
            <div class="member-overview-card">
                <span class="member-overview-card__icon">👁️</span>
                <strong><?php echo number_format((int) ($visitorStats['total'] ?? 0)); ?></strong>
                <span>Seitenaufrufe in 30 Tagen</span>
            </div>
            <div class="member-overview-card">
                <span class="member-overview-card__icon">🙋</span>
                <strong><?php echo number_format((int) ($visitorStats['unique'] ?? 0)); ?></strong>
                <span>Unique Visitors</span>
            </div>
            <div class="member-overview-card">
                <span class="member-overview-card__icon">🧭</span>
                <strong><?php echo htmlspecialchars((string) ($visitorStats['bounce_rate'] ?? '0%'), ENT_QUOTES); ?></strong>
                <span>Absprungrate</span>
            </div>
            <div class="member-overview-card">
                <span class="member-overview-card__icon">⚡</span>
                <strong><?php echo number_format((int) ($coreWebVitals['sample_count'] ?? 0)); ?></strong>
                <span>Core-Web-Vitals-Samples</span>
            </div>
        </div>

        <div class="member-grid-2 member-grid-2--dashboard" data-anim data-anim-delay="1.5">
            <div class="member-card">
                <div class="member-card-header">
                    <h3>🔎 SEO-Überblick</h3>
                </div>
                <div class="member-analytics-summary-grid">
                    <div class="member-analytics-mini-card">
                        <span>Geprüfte Inhalte</span>
                        <strong><?php echo number_format($seoSummary['audited_content']); ?></strong>
                    </div>
                    <div class="member-analytics-mini-card">
                        <span>Beiträge live</span>
                        <strong><?php echo number_format($postsPublished); ?></strong>
                    </div>
                    <div class="member-analytics-mini-card">
                        <span>Kommentare offen</span>
                        <strong><?php echo number_format($pendingComments); ?></strong>
                    </div>
                    <div class="member-analytics-mini-card">
                        <span>Gesammelte Views</span>
                        <strong><?php echo number_format($trackedPageViews); ?></strong>
                    </div>
                </div>

                <ul class="member-analytics-list member-analytics-list--alerts">
                    <?php if ($seoAlerts === []): ?>
                    <li>✅ Keine unmittelbaren SEO-Auffälligkeiten in der Schnellprüfung gefunden.</li>
                    <?php else: ?>
                        <?php foreach ($seoAlerts as $alert): ?>
                        <li><?php echo htmlspecialchars($alert, ENT_QUOTES); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="member-card">
                <div class="member-card-header">
                    <h3>🧩 SEO-Konfiguration</h3>
                </div>
                <dl class="member-analytics-settings">
                    <?php foreach ($analyticsSettings as $label => $value): ?>
                    <div>
                        <dt><?php echo htmlspecialchars((string) $label, ENT_QUOTES); ?></dt>
                        <dd><?php echo htmlspecialchars((string) $value, ENT_QUOTES); ?></dd>
                    </div>
                    <?php endforeach; ?>
                </dl>
            </div>
        </div>

        <div class="member-grid-2 member-grid-2--dashboard" data-anim data-anim-delay="2">
            <div class="member-card">
                <div class="member-card-header">
                    <h3>📄 Top-Seiten</h3>
                </div>
                <ul class="member-analytics-list">
                    <?php if ($topPages === []): ?>
                    <li>Noch keine Page-View-Daten vorhanden.</li>
                    <?php else: ?>
                        <?php foreach ($topPages as $page): ?>
                        <li>
                            <span class="member-analytics-list__label"><code><?php echo htmlspecialchars((string) ($page['page_slug'] ?? '/'), ENT_QUOTES); ?></code></span>
                            <strong><?php echo number_format((int) ($page['views'] ?? 0)); ?></strong>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="member-card">
                <div class="member-card-header">
                    <h3>🛠️ Top-Features</h3>
                </div>
                <ul class="member-analytics-list">
                    <?php if ($topFeatures === []): ?>
                    <li>Noch keine Feature-Nutzungsdaten vorhanden.</li>
                    <?php else: ?>
                        <?php foreach ($topFeatures as $feature): ?>
                        <li>
                            <span class="member-analytics-list__label">
                                <?php echo htmlspecialchars((string) ($feature['feature_label'] ?? 'Feature'), ENT_QUOTES); ?>
                                <small><?php echo htmlspecialchars((string) ($feature['feature_area'] ?? 'Bereich'), ENT_QUOTES); ?></small>
                            </span>
                            <strong><?php echo number_format((int) ($feature['total_uses'] ?? 0)); ?></strong>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <p class="member-form-hint"><?php echo htmlspecialchars((string) ($featureUsage['note'] ?? ''), ENT_QUOTES); ?></p>
                <p class="member-form-hint">Aktive Features: <?php echo number_format((int) ($featureTotals['unique_features'] ?? 0)); ?> · Akteure/Sessions: <?php echo number_format((int) ($featureTotals['unique_actors'] ?? 0)); ?></p>
            </div>
        </div>

        <div class="member-card" data-anim data-anim-delay="2.5">
            <div class="member-card-header">
                <h3>⚙️ Core Web Vitals</h3>
            </div>

            <div class="member-analytics-vitals-grid">
                <?php foreach (['ttfb' => 'TTFB', 'lcp' => 'LCP', 'inp' => 'INP', 'cls' => 'CLS'] as $metricKey => $metricLabel):
                    $metric = (array) ($metrics[$metricKey] ?? []);
                    $rating = (string) ($metric['rating'] ?? 'unknown');
                ?>
                <article class="member-analytics-vital member-analytics-vital--<?php echo htmlspecialchars($rating, ENT_QUOTES); ?>">
                    <span><?php echo htmlspecialchars($metricLabel, ENT_QUOTES); ?></span>
                    <strong><?php echo htmlspecialchars((string) ($metric['display'] ?? '—'), ENT_QUOTES); ?></strong>
                    <small><?php echo htmlspecialchars((string) ($metric['rating_label'] ?? 'Keine Daten'), ENT_QUOTES); ?></small>
                </article>
                <?php endforeach; ?>
            </div>

            <p class="member-form-hint"><?php echo htmlspecialchars((string) ($coreWebVitals['note'] ?? ''), ENT_QUOTES); ?></p>

            <div class="member-session-list member-session-list--analytics">
                <?php if ($problemPages === []): ?>
                <div class="member-empty">
                    <p>✅ Keine auffälligen Problemseiten in den aktuellen Felddaten gefunden.</p>
                </div>
                <?php else: ?>
                    <?php foreach ($problemPages as $page): ?>
                    <article class="member-session-item">
                        <div class="member-session-item__main">
                            <strong><?php echo htmlspecialchars((string) ($page['page_path'] ?? '/'), ENT_QUOTES); ?></strong>
                            <span><?php echo (int) ($page['samples'] ?? 0); ?> Samples</span>
                        </div>
                        <div class="member-session-item__meta">
                            <span>LCP: <?php echo htmlspecialchars(isset($page['avg_lcp_ms']) ? (string) $page['avg_lcp_ms'] . ' ms' : '–', ENT_QUOTES); ?></span>
                            <span>INP: <?php echo htmlspecialchars(isset($page['avg_inp_ms']) ? (string) $page['avg_inp_ms'] . ' ms' : '–', ENT_QUOTES); ?></span>
                            <span>CLS: <?php echo htmlspecialchars(isset($page['avg_cls']) ? number_format((float) $page['avg_cls'], 3, ',', '.') : '–', ENT_QUOTES); ?></span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include $themeDir . 'footer.php'; ?>