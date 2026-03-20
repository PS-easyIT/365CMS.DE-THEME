<?php
/**
 * Firmen-Verzeichnis – 365Network Theme
 *
 * Vollständige Firmenübersicht mit Branche-, Größen- und
 * Standort-Filter sowie Grid/Listenansicht.
 * Verbindet sich mit dem cms-companies Plugin.
 *
 * URL-Parameter:
 *   q        – Suchbegriff (Name, Branche)
 *   sector   – Branche (LIKE)
 *   size     – Firmengröße
 *   location – Standortstadt
 *   sort     – (latest|name|size)
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
$hasPlugin = $pluginMgr->isPluginActive('cms-companies');

// ── Parameter ──
$search   = trim(strip_tags($_GET['q'] ?? ''));
$sector   = trim(strip_tags($_GET['sector'] ?? ''));
$size     = trim(strip_tags($_GET['size'] ?? ''));
$location = trim(strip_tags($_GET['location'] ?? ''));
$sort     = in_array($_GET['sort'] ?? '', ['latest', 'name', 'size']) ? $_GET['sort'] : 'latest';
$view     = ($_GET['view'] ?? 'grid') === 'list' ? 'list' : 'grid';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 18;

$companies  = [];
$sectors    = [];
$locations  = [];
$totalCount = 0;
$totalPages = 1;

if ($hasPlugin) {
    try {
        $where  = ['c.status = ?'];
        $params = ['active'];

        if ($search !== '') {
            $term    = '%' . $search . '%';
            $where[] = "(c.name LIKE ? OR c.industry LIKE ? OR c.description LIKE ?)";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($sector !== '') {
            $where[]  = "c.industry LIKE ?";
            $params[] = '%' . $sector . '%';
        }
        if ($size !== '') {
            $where[]  = "c.company_size = ?";
            $params[] = $size;
        }
        if ($location !== '') {
            $where[]  = "c.location_city LIKE ?";
            $params[] = '%' . $location . '%';
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = match ($sort) {
            'name' => 'ORDER BY c.name ASC',
            'size' => 'ORDER BY c.employee_count DESC',
            default => 'ORDER BY c.created_at DESC',
        };

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}companies c {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(($cRow)->cnt ?? 0);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->execute(
            "SELECT c.* FROM {$prefix}companies c {$whereSQL} {$orderSQL} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $companies = $stmt->fetchAll() ?: [];

        // Filter-Optionen
        $secRows = $db->execute(
            "SELECT DISTINCT industry FROM {$prefix}companies WHERE status = 'active' AND industry IS NOT NULL AND industry != '' ORDER BY industry ASC LIMIT 50"
        )->fetchAll() ?: [];
        $sectors = array_map(fn($r) => is_array($r) ? ($r['industry'] ?? '') : ($r->industry ?? ''), $secRows);

        $locRows = $db->execute(
            "SELECT DISTINCT location_city FROM {$prefix}companies WHERE status = 'active' AND location_city IS NOT NULL AND location_city != '' ORDER BY location_city ASC LIMIT 50"
        )->fetchAll() ?: [];
        $locations = array_map(fn($r) => is_array($r) ? ($r['location_city'] ?? '') : ($r->location_city ?? ''), $locRows);

    } catch (\Throwable $e) { /* */ }
}

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main companies-page" role="main" data-page="companies">

    <section class="directory-hero companies-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>🏢 Firmen-Verzeichnis</h1>
                <p class="directory-hero-sub">
                    <?php if ($totalCount > 0): ?>
                        <strong><?php echo number_format($totalCount); ?></strong> Unternehmen im Verzeichnis
                    <?php elseif ($hasPlugin): ?>
                        IT-Unternehmen, Agenturen und Partner aus der DACH-Region
                    <?php else: ?>
                        Das Firmen-Plugin aktivieren, um das Verzeichnis zu nutzen.
                    <?php endif; ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Firmenname, Branche …"
                           class="directory-search-input">
                    <button type="submit" class="btn btn-primary directory-search-btn">🔍 Suchen</button>
                </div>
            </form>
        </div>
    </section>

    <nav class="breadcrumb-bar" aria-label="Pfadnavigation">
        <div class="container">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/">Startseite</a>
            <span class="breadcrumb-sep">›</span>
            <span aria-current="page">Firmen</span>
        </div>
    </nav>

    <div class="directory-layout container">

        <!-- Sidebar: Filter -->
        <aside class="directory-filters" aria-label="Firmen-Filter">
            <form method="GET" action="">
                <?php if ($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"><?php endif; ?>

                <?php if (!empty($sectors)): ?>
                <div class="filter-panel">
                    <h3 class="filter-panel-title">🏭 Branche</h3>
                    <select name="sector" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle Branchen</option>
                        <?php foreach ($sectors as $sec): if (!$sec) continue; ?>
                            <option value="<?php echo htmlspecialchars($sec, ENT_QUOTES); ?>"
                                <?php echo $sector === $sec ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sec, ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if (!empty($locations)): ?>
                <div class="filter-panel">
                    <h3 class="filter-panel-title">📍 Standort</h3>
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
                    <h3 class="filter-panel-title">📊 Unternehmensgröße</h3>
                    <div class="filter-link-list">
                        <?php
                        $sizeOpts = ['' => 'Alle', '1-10' => '1–10 Mitarbeiter', '11-50' => '11–50', '51-200' => '51–200', '201-500' => '201–500', '500+' => '500+'];
                        foreach ($sizeOpts as $val => $lbl): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['size' => $val, 'page' => 1])); ?>"
                               class="filter-link <?php echo $size === $val ? 'is-active' : ''; ?>">
                                <?php echo $lbl; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">🗂️ Sortierung</h3>
                    <select name="sort" class="filter-select" data-auto-submit-filter>
                        <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>🕐 Neueste zuerst</option>
                        <option value="name"   <?php echo $sort === 'name'   ? 'selected' : ''; ?>>🔤 Name A–Z</option>
                        <option value="size"   <?php echo $sort === 'size'   ? 'selected' : ''; ?>>📊 Größe</option>
                    </select>
                </div>

                <?php if ($search || $sector || $size || $location): ?>
                    <div class="filter-panel">
                        <a href="/companies" class="btn btn-secondary filter-reset-btn">✖ Filter zurücksetzen</a>
                    </div>
                <?php endif; ?>
            </form>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0): ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Unternehmen</span>
                    <?php endif; ?>
                </div>
                <div class="directory-toolbar-right">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'grid'])); ?>"
                       class="view-toggle-btn <?php echo $view === 'grid' ? 'is-active' : ''; ?>" aria-label="Rasteransicht">⊞</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'list'])); ?>"
                       class="view-toggle-btn <?php echo $view === 'list' ? 'is-active' : ''; ?>" aria-label="Listenansicht">≡</a>
                </div>
            </div>

            <?php if (!empty($companies)): ?>
            <div class="directory-grid directory-grid--<?php echo $view; ?>">
                <?php foreach ($companies as $co):
                    $id      = is_array($co) ? ($co['id']            ?? 0)  : ($co->id            ?? 0);
                    $name    = htmlspecialchars(is_array($co) ? ($co['name']          ?? '') : ($co->name          ?? ''), ENT_QUOTES, 'UTF-8');
                    $logo    = is_array($co) ? ($co['logo_url']       ?? '') : ($co->logo_url       ?? '');
                    $indust  = htmlspecialchars(is_array($co) ? ($co['industry']      ?? '') : ($co->industry      ?? ''), ENT_QUOTES, 'UTF-8');
                    $city    = htmlspecialchars(is_array($co) ? ($co['location_city'] ?? '') : ($co->location_city ?? ''), ENT_QUOTES, 'UTF-8');
                    $web     = htmlspecialchars(is_array($co) ? ($co['website']       ?? '') : ($co->website       ?? ''), ENT_QUOTES, 'UTF-8');
                    $desc    = strip_tags(is_array($co) ? ($co['description'] ?? '') : ($co->description ?? ''));
                    $partner = (bool)(is_array($co) ? ($co['is_partner'] ?? false) : ($co->is_partner ?? false));
                    $top     = (bool)(is_array($co) ? ($co['is_top_partner'] ?? false) : ($co->is_top_partner ?? false));
                    $empCnt  = (int)(is_array($co) ? ($co['employee_count'] ?? 0) : ($co->employee_count ?? 0));
                ?>
                <article class="directory-card company-card<?php echo $top ? ' company-card--top-partner' : ''; ?>">
                    <?php if ($top): ?><span class="partner-badge">⭐ Top Partner</span>
                    <?php elseif ($partner): ?><span class="partner-badge partner-badge--standard">🤝 Partner</span><?php endif; ?>
                    <div class="company-card-logo">
                        <?php if ($logo): ?>
                            <img src="<?php echo htmlspecialchars($logo, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo $name; ?>" loading="lazy" width="80" height="80">
                        <?php else: ?>
                            <div class="company-card-avatar"><?php echo mb_strtoupper(mb_substr(strip_tags($name), 0, 2)); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="company-card-body">
                        <h2 class="company-card-name">
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/companies/<?php echo (int)$id; ?>">
                                <?php echo $name; ?>
                            </a>
                        </h2>
                        <?php if ($indust): ?><p class="company-card-industry">🏭 <?php echo $indust; ?></p><?php endif; ?>
                        <?php if ($city):   ?><p class="company-card-location">📍 <?php echo $city; ?></p><?php endif; ?>
                        <?php if ($empCnt > 0): ?><p class="company-card-size">👥 <?php echo number_format($empCnt); ?> Mitarbeiter</p><?php endif; ?>
                        <?php if ($desc):   ?><p class="company-card-desc"><?php echo htmlspecialchars(mb_strimwidth($desc, 0, 140, '…'), ENT_QUOTES, 'UTF-8'); ?></p><?php endif; ?>
                    </div>
                    <div class="company-card-footer">
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/companies/<?php echo (int)$id; ?>"
                           class="btn btn-primary btn-sm">Profil ansehen</a>
                        <?php if ($web): ?>
                            <a href="<?php echo $web; ?>" target="_blank" rel="noopener noreferrer"
                               class="btn btn-secondary btn-sm">🌐 Website</a>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🏢</div>
                <h3>Keine Unternehmen gefunden</h3>
                <p>Versuche andere Suchbegriffe oder setze die Filter zurück.</p>
                <a href="/companies" class="btn btn-secondary empty-state-reset-btn">✖ Filter zurücksetzen</a>
            </div>

            <?php else: ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">🏢</div>
                <h3>Firmen-Verzeichnis</h3>
                <p>Das <strong>cms-companies</strong> Plugin aktivieren, um Unternehmensprofile anzulegen und zu durchsuchen.</p>
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
