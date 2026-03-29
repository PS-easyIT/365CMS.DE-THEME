<?php
/**
 * Buchungsübersicht – 365Network Theme
 *
 * Zeigt alle buchbaren Anbieter (Provider) mit ihren Leistungen.
 * Unterstützt Vorauswahl per URL-Parameter.
 *
 * URL-Parameter:
 *   expert   – Provider-ID (Vorauswahl)
 *   service  – Service-ID  (Vorauswahl)
 *   date     – Datum YYYY-MM-DD (Vorauswahl)
 *   q        – Suchbegriff (Name, Leistung)
 *   type     – location_type (online|onsite|hybrid)
 *   sort     – (latest|name|price)
 *   view     – (grid|list)
 *   page     – Seite
 *
 * @package IT_Expert_Network_Theme
 * @version 1.0.0
 */
declare(strict_types=1);

if (!defined('ABSPATH')) { exit; }

$db        = \CMS\Database::instance();
$prefix    = $db->prefix();
$pluginMgr = \CMS\PluginManager::instance();
$siteUrl   = SITE_URL;
$hasPlugin = $pluginMgr->isPluginActive('cms-booking');
$homeUrl   = theme_safe_url($siteUrl . '/', $siteUrl . '/');
$bookingBaseUrl = theme_safe_url($siteUrl . '/booking', $siteUrl . '/booking');

// ── Parameter ──
$search    = trim(strip_tags($_GET['q']      ?? ''));
$typeFilter = in_array($_GET['type'] ?? '', ['online', 'onsite', 'hybrid']) ? $_GET['type'] : '';
$sort      = in_array($_GET['sort'] ?? '', ['latest', 'name', 'price']) ? $_GET['sort'] : 'name';
$view      = ($_GET['view'] ?? 'grid') === 'list' ? 'list' : 'grid';
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 12;

// Vorauswahl
$preExpert  = (int)($_GET['expert']  ?? 0);
$preService = (int)($_GET['service'] ?? 0);
$preDate    = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'] ?? '') ? $_GET['date'] : '';

$providers   = [];
$services    = [];         // serviceId → [rows]
$totalCount  = 0;
$totalPages  = 1;

$locationTypeLabels = [
    'online'  => '💻 Online',
    'onsite'  => '📍 Vor Ort',
    'hybrid'  => '🔄 Hybrid',
];
$bookingTypeLabels = [
    'instant'      => '⚡ Sofortbuchung',
    'confirmation' => '✅ Auf Bestätigung',
    'request'      => '📋 Anfrage',
];

$bookingQuery = [
    'q' => $search,
    'type' => $typeFilter,
    'sort' => $sort,
    'view' => $view,
    'expert' => $preExpert > 0 ? (string)$preExpert : '',
    'service' => $preService > 0 ? (string)$preService : '',
    'date' => $preDate,
];

if ($hasPlugin) {
    try {
        $where  = ["bp.status = 'active'"];
        $params = [];

        if ($search !== '') {
            $term    = '%' . $search . '%';
            $where[] = "(bp.display_name LIKE ? OR bp.bio LIKE ? OR EXISTS (SELECT 1 FROM {$prefix}booking_services bs2 WHERE bs2.provider_id = bp.id AND bs2.title LIKE ?))";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($typeFilter !== '') {
            $where[] = "EXISTS (SELECT 1 FROM {$prefix}booking_services bst WHERE bst.provider_id = bp.id AND bst.location_type = ? AND bst.status = 'active')";
            $params[] = $typeFilter;
        }
        if ($preExpert > 0) {
            $where[]  = "bp.id = ?";
            $params[] = $preExpert;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = match ($sort) {
            'name'  => 'ORDER BY bp.display_name ASC',
            'price' => 'ORDER BY (SELECT MIN(price_cents) FROM ' . $prefix . 'booking_services WHERE provider_id = bp.id AND status = \'active\') ASC',
            default => 'ORDER BY bp.created_at DESC',
        };

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}booking_providers bp {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(is_array($cRow) ? ($cRow['cnt'] ?? 0) : ($cRow->cnt ?? 0));
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $provStmt = $db->execute(
            "SELECT bp.id, bp.display_name, bp.slug, bp.avatar_url, bp.bio, bp.timezone, bp.currency
             FROM {$prefix}booking_providers bp {$whereSQL} {$orderSQL}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $providers = $provStmt->fetchAll() ?: [];

        if (!empty($providers)) {
            $ids = array_map(fn($p) => (int)(is_array($p) ? ($p['id'] ?? 0) : ($p->id ?? 0)), $providers);
            $in  = implode(',', $ids);
            $serviceRows = $db->execute(
                "SELECT * FROM {$prefix}booking_services
                 WHERE provider_id IN ({$in}) AND status = 'active'
                 ORDER BY provider_id ASC, sort_order ASC, price_cents ASC"
            )->fetchAll() ?: [];

            foreach ($serviceRows as $svc) {
                $pid = (int)(is_array($svc) ? ($svc['provider_id'] ?? 0) : ($svc->provider_id ?? 0));
                $services[$pid][] = $svc;
            }
        }

    } catch (\Throwable $e) { /* */ }
}

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main booking-page" role="main" data-page="booking">

    <section class="directory-hero booking-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>📅 Termin buchen</h1>
                <p class="directory-hero-sub">
                    <?php if ($totalCount > 0): ?>
                        <strong><?php echo number_format($totalCount); ?></strong> Anbieter mit buchbaren Leistungen
                    <?php elseif ($hasPlugin): ?>
                        Alle verfügbaren Experten und ihre buchbaren Leistungen
                    <?php else: ?>
                        Das cms-booking Plugin aktivieren, um Online-Terminbuchungen anzubieten.
                    <?php endif; ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Anbieter, Leistung …"
                           class="directory-search-input">
                    <button type="submit" class="btn btn-primary directory-search-btn">🔍 Suchen</button>
                </div>
            </form>
        </div>
    </section>

    <nav class="breadcrumb-bar" aria-label="Pfadnavigation">
        <div class="container">
            <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">Startseite</a>
            <span class="breadcrumb-sep">›</span>
            <span aria-current="page">Terminbuchung</span>
        </div>
    </nav>

    <?php if ($preExpert > 0 && !empty($providers)): ?>
    <!-- Vorauswahl-Banner -->
    <div class="container booking-preselect-shell">
        <div class="booking-preselect-banner">
            <span>🔖 Vorauswahl aktiv – Anbieter #<?php echo $preExpert; ?></span>
            <a href="<?php echo htmlspecialchars($bookingBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary btn-sm booking-preselect-clear">✖ Aufheben</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="directory-layout container">

        <aside class="directory-filters" aria-label="Buchungsfilter">
            <form method="GET" action="">
                <?php if ($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"><?php endif; ?>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">🗓️ Leistungsart</h3>
                    <div class="booking-filter-link-list">
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['type' => '', 'page' => '1']), ENT_QUOTES, 'UTF-8'); ?>"
                           class="filter-link <?php echo $typeFilter === '' ? 'is-active' : ''; ?>">Alle</a>
                        <?php foreach ($locationTypeLabels as $val => $lbl): ?>
                            <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['type' => $val, 'page' => '1']), ENT_QUOTES, 'UTF-8'); ?>"
                               class="filter-link <?php echo $typeFilter === $val ? 'is-active' : ''; ?>"><?php echo $lbl; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">🗂️ Sortierung</h3>
                    <select name="sort" class="filter-select" data-auto-submit-filter>
                        <option value="name"   <?php echo $sort === 'name'   ? 'selected' : ''; ?>>🔤 Name A–Z</option>
                        <option value="price"  <?php echo $sort === 'price'  ? 'selected' : ''; ?>>💰 Günstigste zuerst</option>
                        <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>🕐 Neueste zuerst</option>
                    </select>
                </div>

                <?php if ($search || $typeFilter || $preExpert): ?>
                    <div class="filter-panel">
                        <a href="<?php echo htmlspecialchars($bookingBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary booking-filter-reset">✖ Filter zurücksetzen</a>
                    </div>
                <?php endif; ?>
            </form>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0): ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Anbieter</span>
                    <?php endif; ?>
                </div>
                <div class="directory-toolbar-right">
                          <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['view' => 'grid']), ENT_QUOTES, 'UTF-8'); ?>"
                       class="view-toggle-btn <?php echo $view === 'grid' ? 'is-active' : ''; ?>" aria-label="Rasteransicht">⊞</a>
                          <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['view' => 'list']), ENT_QUOTES, 'UTF-8'); ?>"
                       class="view-toggle-btn <?php echo $view === 'list' ? 'is-active' : ''; ?>" aria-label="Listenansicht">≡</a>
                </div>
            </div>

            <?php if (!empty($providers)): ?>
            <div class="directory-grid directory-grid--<?php echo $view; ?>">
                <?php foreach ($providers as $prov):
                    $pid      = (int)(is_array($prov) ? ($prov['id']           ?? 0)  : ($prov->id           ?? 0));
                    $pName    = htmlspecialchars(is_array($prov) ? ($prov['display_name'] ?? '') : ($prov->display_name ?? ''), ENT_QUOTES, 'UTF-8');
                    $pSlug    = is_array($prov) ? ($prov['slug']       ?? '')  : ($prov->slug       ?? '');
                    $pAvatar  = theme_safe_url((string)(is_array($prov) ? ($prov['avatar_url'] ?? '')  : ($prov->avatar_url ?? '')));
                    $pBio     = htmlspecialchars(strip_tags(is_array($prov) ? ($prov['bio'] ?? '') : ($prov->bio ?? '')), ENT_QUOTES, 'UTF-8');
                    $pCurr    = htmlspecialchars(is_array($prov) ? ($prov['currency'] ?? 'EUR') : ($prov->currency ?? 'EUR'), ENT_QUOTES, 'UTF-8');
                    $provServices = $services[$pid] ?? [];
                ?>
                <article class="directory-card booking-provider-card" id="provider-<?php echo $pid; ?>">
                    <div class="booking-provider-header">
                        <div class="booking-provider-avatar">
                            <?php if ($pAvatar): ?>
                                <img src="<?php echo htmlspecialchars($pAvatar, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $pName; ?>" loading="lazy" width="64" height="64">
                            <?php else: ?>
                                <div class="booking-provider-avatar-placeholder">📅</div>
                            <?php endif; ?>
                        </div>
                        <div class="booking-provider-info">
                            <h2 class="booking-provider-name"><?php echo $pName; ?></h2>
                            <?php if ($pBio): ?><p class="booking-provider-bio"><?php echo mb_strimwidth(strip_tags($pBio), 0, 120, '…'); ?></p><?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($provServices)): ?>
                    <div class="booking-services-list">
                        <?php foreach ($provServices as $svc):
                            $svcId       = (int)(is_array($svc) ? ($svc['id']            ?? 0)  : ($svc->id            ?? 0));
                            $svcTitle    = htmlspecialchars(is_array($svc) ? ($svc['title']         ?? '') : ($svc->title         ?? ''), ENT_QUOTES, 'UTF-8');
                            $svcDur      = (int)(is_array($svc) ? ($svc['duration_min']  ?? 0)  : ($svc->duration_min  ?? 0));
                            $svcPriceCts = (int)(is_array($svc) ? ($svc['price_cents']   ?? 0)  : ($svc->price_cents   ?? 0));
                            $svcLocType  = is_array($svc) ? ($svc['location_type'] ?? '') : ($svc->location_type ?? '');
                            $svcBookType = is_array($svc) ? ($svc['booking_type']  ?? '') : ($svc->booking_type  ?? '');
                            $svcPrice    = $svcPriceCts > 0 ? number_format($svcPriceCts / 100, 2, ',', '.') . ' ' . $pCurr : 'Kostenlos';
                            $bookUrl     = htmlspecialchars(theme_build_query_url('/booking/book', [
                                'provider' => (string)$pid,
                                'service' => (string)$svcId,
                                'date' => $preDate,
                            ]), ENT_QUOTES, 'UTF-8');
                            $highlight   = $preService === $svcId;
                        ?>
                        <div class="booking-service-row <?php echo $highlight ? 'is-highlighted' : ''; ?>">
                            <div class="booking-service-meta">
                                <strong class="booking-service-title"><?php echo $svcTitle; ?></strong>
                                <div class="booking-service-details">
                                    <?php if ($svcDur > 0): ?><span>⏱ <?php echo $svcDur; ?> Min.</span><?php endif; ?>
                                    <?php if ($svcLocType && isset($locationTypeLabels[$svcLocType])): ?><span><?php echo $locationTypeLabels[$svcLocType]; ?></span><?php endif; ?>
                                    <?php if ($svcBookType && isset($bookingTypeLabels[$svcBookType])): ?><span><?php echo $bookingTypeLabels[$svcBookType]; ?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="booking-service-action">
                                <span class="booking-service-price"><?php echo $svcPrice; ?></span>
                                <a href="<?php echo $bookUrl; ?>" class="btn btn-primary btn-sm">
                                    📅 Buchen
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="booking-no-services">Aktuell keine buchbaren Leistungen verfügbar.</p>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <h3>Keine Anbieter gefunden</h3>
                <p>Versuche andere Suchbegriffe oder setze die Filter zurück.</p>
                <a href="<?php echo htmlspecialchars($bookingBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary booking-empty-reset">✖ Filter zurücksetzen</a>
            </div>

            <?php else: ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">📅</div>
                <h3>Terminbuchung</h3>
                <p>Das <strong>cms-booking</strong> Plugin aktivieren, um Online-Terminbuchungen für Experten und Speaker zu ermöglichen.</p>
            </div>
            <?php endif; ?>

            <?php if ($totalPages > 1): ?>
            <nav class="directory-pagination" aria-label="Seitennavigation">
                <?php if ($page > 1): ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['page' => (string)($page - 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="pagination-btn">← Zurück</a>
                <?php endif; ?>
                <div class="pagination-pages">
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['page' => (string)$i]), ENT_QUOTES, 'UTF-8'); ?>"
                           class="pagination-page <?php echo $i === $page ? 'is-current' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/booking', $bookingQuery, ['page' => (string)($page + 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="pagination-btn">Weiter →</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
