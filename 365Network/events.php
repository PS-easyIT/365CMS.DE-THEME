<?php
/**
 * Events-Verzeichnis – 365Network Theme
 *
 * Vollständige Event-Übersicht mit Kategorie-, Ort-, Monats-
 * und Format-Filter. Verbindet sich mit dem cms-events Plugin.
 *
 * URL-Parameter:
 *   q        – Suchbegriff
 *   type     – Kategorie
 *   location – Stadt
 *   month    – YYYY-MM
 *   past     – 1 für vergangene Events
 *   sort     – (date|name|latest)
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
$hasPlugin = $pluginMgr->isPluginActive('cms-events');

// ── Parameter ──
$search   = trim(strip_tags($_GET['q'] ?? ''));
$type     = trim(strip_tags($_GET['type'] ?? ''));
$location = trim(strip_tags($_GET['location'] ?? ''));
$month    = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : '';
$past     = !empty($_GET['past']);
$sort     = in_array($_GET['sort'] ?? '', ['date', 'name', 'latest']) ? $_GET['sort'] : 'date';
$view     = ($_GET['view'] ?? 'grid') === 'list' ? 'list' : 'grid';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 18;

$events     = [];
$categories = [];
$locations  = [];
$totalCount = 0;
$totalPages = 1;

if ($hasPlugin) {
    try {
        $where  = ["e.status = 'published'"];
        $params = [];

        if (!$past) {
            $where[] = "e.event_date >= CURDATE()";
        } else {
            $where[] = "e.event_date < CURDATE()";
        }
        if ($search !== '') {
            $term    = '%' . $search . '%';
            $where[] = "(e.title LIKE ? OR e.excerpt LIKE ? OR e.city LIKE ?)";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($type !== '') {
            $where[]  = "e.category = ?";
            $params[] = $type;
        }
        if ($location !== '') {
            $where[]  = "e.city LIKE ?";
            $params[] = '%' . $location . '%';
        }
        if ($month !== '') {
            $where[]  = "DATE_FORMAT(e.event_date, '%Y-%m') = ?";
            $params[] = $month;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = match ($sort) {
            'name'   => 'ORDER BY e.title ASC',
            'latest' => 'ORDER BY e.created_at DESC',
            default  => 'ORDER BY e.event_date ASC',
        };

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}events e {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(($cRow)->cnt ?? 0);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->execute(
            "SELECT e.* FROM {$prefix}events e {$whereSQL} {$orderSQL} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $events = $stmt->fetchAll() ?: [];

        // Kategorien für Filter
        $catRows = $db->execute(
            "SELECT DISTINCT category FROM {$prefix}events WHERE status = 'published' AND category IS NOT NULL AND category != '' ORDER BY category ASC LIMIT 50"
        )->fetchAll() ?: [];
        $categories = array_map(fn($r) => is_array($r) ? ($r['category'] ?? '') : ($r->category ?? ''), $catRows);

        // Städte für Filter
        $locRows = $db->execute(
            "SELECT DISTINCT city FROM {$prefix}events WHERE status = 'published' AND city IS NOT NULL AND city != '' ORDER BY city ASC LIMIT 50"
        )->fetchAll() ?: [];
        $locations = array_map(fn($r) => is_array($r) ? ($r['city'] ?? '') : ($r->city ?? ''), $locRows);

    } catch (\Throwable $e) { /* */ }
}

$monthsDE = ['', 'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main events-page" role="main" data-page="events">

    <section class="directory-hero events-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>📅 Events &amp; Konferenzen</h1>
                <p class="directory-hero-sub">
                    <?php if ($totalCount > 0): ?>
                        <strong><?php echo number_format($totalCount); ?></strong> <?php echo $past ? 'vergangene' : 'kommende'; ?> Event<?php echo $totalCount !== 1 ? 's' : ''; ?>
                    <?php elseif ($hasPlugin): ?>
                        IT-Konferenzen, Meetups und Workshops in der DACH-Region
                    <?php else: ?>
                        Das Events-Plugin aktivieren, um Events zu verwalten.
                    <?php endif; ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Event-Titel, Ort …"
                           class="directory-search-input">
                    <?php if ($past): ?><input type="hidden" name="past" value="1"><?php endif; ?>
                    <button type="submit" class="btn btn-primary directory-search-btn">🔍 Suchen</button>
                </div>
            </form>
        </div>
    </section>

    <nav class="breadcrumb-bar" aria-label="Pfadnavigation">
        <div class="container">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/">Startseite</a>
            <span class="breadcrumb-sep">›</span>
            <span aria-current="page">Events</span>
        </div>
    </nav>

    <div class="directory-layout container">

        <!-- Sidebar: Filter -->
        <aside class="directory-filters" aria-label="Events-Filter">
            <form method="GET" action="">
                <?php if ($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"><?php endif; ?>

                <!-- Vergangen / Kommend -->
                <div class="filter-panel">
                    <h3 class="filter-panel-title">⏱️ Zeitraum</h3>
                    <div class="filter-link-list">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['past' => '', 'page' => 1])); ?>"
                           class="filter-link <?php echo !$past ? 'is-active' : ''; ?>">📅 Kommende</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['past' => '1', 'page' => 1])); ?>"
                           class="filter-link <?php echo $past ? 'is-active' : ''; ?>">📂 Vergangene</a>
                    </div>
                </div>

                <?php if (!empty($categories)): ?>
                <div class="filter-panel">
                    <h3 class="filter-panel-title">🏷️ Kategorie</h3>
                    <select name="type" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle Kategorien</option>
                        <?php foreach ($categories as $cat): if (!$cat) continue; ?>
                            <option value="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>"
                                <?php echo $type === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat, ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (!empty($locations)): ?>
                <div class="filter-panel">
                    <h3 class="filter-panel-title">📍 Stadt</h3>
                    <select name="location" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle Städte</option>
                        <?php foreach ($locations as $loc): if (!$loc) continue; ?>
                            <option value="<?php echo htmlspecialchars($loc, ENT_QUOTES); ?>"
                                <?php echo $location === $loc ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($loc, ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">🗂️ Sortierung</h3>
                    <select name="sort" class="filter-select" data-auto-submit-filter>
                        <option value="date"   <?php echo $sort === 'date'   ? 'selected' : ''; ?>>📅 Datum</option>
                        <option value="name"   <?php echo $sort === 'name'   ? 'selected' : ''; ?>>🔤 Titel A–Z</option>
                        <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>🕐 Neueste</option>
                    </select>
                </div>

                <?php if ($search || $type || $location || $month): ?>
                    <div class="filter-panel">
                        <a href="/events<?php echo $past ? '?past=1' : ''; ?>" class="btn btn-secondary filter-reset-btn">✖ Filter zurücksetzen</a>
                    </div>
                <?php endif; ?>
            </form>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0): ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Events</span>
                    <?php endif; ?>
                </div>
                <div class="directory-toolbar-right">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'grid'])); ?>"
                       class="view-toggle-btn <?php echo $view === 'grid' ? 'is-active' : ''; ?>" aria-label="Rasteransicht">⊞</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'list'])); ?>"
                       class="view-toggle-btn <?php echo $view === 'list' ? 'is-active' : ''; ?>" aria-label="Listenansicht">≡</a>
                </div>
            </div>

            <?php if (!empty($events)): ?>
            <div class="directory-grid directory-grid--<?php echo $view; ?>">
                <?php foreach ($events as $ev):
                    $id      = is_array($ev) ? ($ev['id']          ?? 0)  : ($ev->id          ?? 0);
                    $title   = htmlspecialchars(is_array($ev) ? ($ev['title']   ?? '') : ($ev->title   ?? ''), ENT_QUOTES, 'UTF-8');
                    $excerpt = htmlspecialchars(strip_tags(is_array($ev) ? ($ev['excerpt'] ?? '') : ($ev->excerpt ?? '')), ENT_QUOTES, 'UTF-8');
                    $evDate  = is_array($ev) ? ($ev['event_date']  ?? '') : ($ev->event_date  ?? '');
                    $evTime  = is_array($ev) ? ($ev['event_time']  ?? '') : ($ev->event_time  ?? '');
                    $city    = htmlspecialchars(is_array($ev) ? ($ev['city']    ?? '') : ($ev->city    ?? ''), ENT_QUOTES, 'UTF-8');
                    $cat     = htmlspecialchars(is_array($ev) ? ($ev['category']?? '') : ($ev->category?? ''), ENT_QUOTES, 'UTF-8');
                    $image   = is_array($ev) ? ($ev['image_url']   ?? '') : ($ev->image_url   ?? '');
                    $online  = (bool)(is_array($ev) ? ($ev['is_online'] ?? false) : ($ev->is_online ?? false));
                    $featd   = (bool)(is_array($ev) ? ($ev['is_featured'] ?? false) : ($ev->is_featured ?? false));
                    $price   = is_array($ev) ? ($ev['price_type']  ?? 'free') : ($ev->price_type  ?? 'free');

                    // Datum formatieren
                    $evDateFormatted = '';
                    $evDay = '';
                    $evMonth = '';
                    if ($evDate) {
                        $ts = strtotime($evDate);
                        $evDay   = date('d', $ts);
                        $evMonth = $monthsDE[(int)date('n', $ts)] ?? '';
                        $evDateFormatted = date('d.m.Y', $ts);
                    }
                ?>
                <article class="directory-card event-card<?php echo $featd ? ' event-card--featured' : ''; ?>">
                    <?php if ($featd): ?><span class="event-badge event-badge--featured">⭐ Featured</span><?php endif; ?>
                    <?php if ($online): ?><span class="event-badge event-badge--online">🖥️ Online</span><?php endif; ?>
                    <?php if ($image): ?>
                        <div class="event-card-image">
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/events/<?php echo (int)$id; ?>">
                                <img src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $title; ?>" loading="lazy" width="400" height="200">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="event-card-body">
                        <?php if ($evDay): ?>
                        <div class="event-card-date-badge">
                            <span class="event-date-day"><?php echo $evDay; ?></span>
                            <span class="event-date-month"><?php echo $evMonth; ?></span>
                        </div>
                        <?php endif; ?>
                        <h2 class="event-card-title">
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/events/<?php echo (int)$id; ?>">
                                <?php echo $title; ?>
                            </a>
                        </h2>
                        <div class="event-card-meta">
                            <?php if ($evDateFormatted): ?><span>📅 <?php echo $evDateFormatted; ?><?php echo $evTime ? ' · ' . mb_substr($evTime, 0, 5) . ' Uhr' : ''; ?></span><?php endif; ?>
                            <?php if ($city): ?><span>📍 <?php echo $city; ?></span><?php endif; ?>
                            <?php if ($cat): ?><span class="event-cat-badge"><?php echo $cat; ?></span><?php endif; ?>
                        </div>
                        <?php if ($excerpt): ?><p class="event-card-excerpt"><?php echo mb_strimwidth(strip_tags($excerpt), 0, 120, '…'); ?></p><?php endif; ?>
                        <div class="event-card-footer">
                            <?php
                            $priceLabel = match ($price) { 'free' => '🆓 Kostenlos', 'paid' => '💳 Kostenpflichtig', 'donation' => '💝 Spende', default => '' };
                            if ($priceLabel): ?><span class="event-price-badge"><?php echo $priceLabel; ?></span><?php endif; ?>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/events/<?php echo (int)$id; ?>"
                               class="btn btn-primary btn-sm">Details</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <h3>Keine Events gefunden</h3>
                <p><?php echo $past ? 'Keine vergangenen Events mit diesen Filtern.' : 'Aktuell keine kommenden Events geplant.'; ?></p>
                <a href="/events" class="btn btn-secondary empty-state-reset-btn">✖ Filter zurücksetzen</a>
            </div>

            <?php else: ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">📅</div>
                <h3>Events-Verzeichnis</h3>
                <p>Das <strong>cms-events</strong> Plugin aktivieren, um Events zu verwalten und anzuzeigen.</p>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="directory-pagination" aria-label="Seitennavigation">
                <?php if ($page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-btn">← Zurück</a>
                <?php endif; ?>
                <div class="pagination-pages">
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                           class="pagination-page <?php echo $i === $page ? 'is-current' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($page < $totalPages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-btn">Weiter →</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
