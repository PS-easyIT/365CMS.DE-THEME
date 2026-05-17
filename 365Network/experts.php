<?php
/**
 * Experten-Verzeichnis – 365Network Theme
 *
 * Vollständige Experten-Übersicht mit Skill-, Standort- und
 * Verfügbarkeits-Filter sowie Grid/Listenansicht.
 * Verbindet sich mit dem cms-experts Plugin.
 *
 * URL-Parameter:
 *   q        – Suchbegriff (Name, Position, Firma)
 *   location – Standort­stadt
 *   skill    – Skill-Name (LIKE)
 *   avail    – Verfügbarkeit (available|limited|booked)
 *   sort     – (latest|name|experience)
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
$hasPlugin = $pluginMgr->isPluginActive('cms-experts');
$homeUrl   = theme_safe_url($siteUrl . '/', $siteUrl . '/');
$expertsBaseUrl = theme_safe_url($siteUrl . '/experts', $siteUrl . '/experts');

// ── Parameter ──
$search   = trim(strip_tags($_GET['q'] ?? ''));
$location = trim(strip_tags($_GET['location'] ?? ''));
$skill    = trim(strip_tags($_GET['skill'] ?? ''));
$avail    = in_array($_GET['avail'] ?? '', ['available', 'limited', 'booked']) ? $_GET['avail'] : '';
$sort     = in_array($_GET['sort'] ?? '', ['latest', 'name', 'experience']) ? $_GET['sort'] : 'latest';
$view     = ($_GET['view'] ?? 'grid') === 'list' ? 'list' : 'grid';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 18;

$experts      = [];
$locations    = [];
$skills       = [];
$totalCount   = 0;
$totalPages   = 1;

if ($hasPlugin) {
    try {
        $where  = ['e.status = ?'];
        $params = ['active'];

        if ($search !== '') {
            $term = '%' . $search . '%';
            $where[] = "(CONCAT(e.first_name, ' ', e.last_name) LIKE ? OR e.position LIKE ? OR e.company LIKE ?)";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($location !== '') {
            $where[]  = "e.location_city LIKE ?";
            $params[] = '%' . $location . '%';
        }
        if ($skill !== '') {
            $where[] = "EXISTS (SELECT 1 FROM {$prefix}expert_skills es WHERE es.expert_id = e.id AND es.skill_name LIKE ?)";
            $params[] = '%' . $skill . '%';
        }
        if ($avail !== '') {
            $where[]  = "e.availability = ?";
            $params[] = $avail;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = match ($sort) {
            'name'       => 'ORDER BY e.last_name ASC, e.first_name ASC',
            'experience' => 'ORDER BY e.experience_years DESC',
            default      => 'ORDER BY e.created_at DESC',
        };

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}experts e {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(is_array($cRow) ? ($cRow['cnt'] ?? 0) : ($cRow->cnt ?? 0));
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->execute(
            "SELECT e.*,
                    CONCAT(e.first_name, ' ', e.last_name) AS display_name,
                    (SELECT GROUP_CONCAT(skill_name ORDER BY skill_name SEPARATOR ', ')
                     FROM {$prefix}expert_skills WHERE expert_id = e.id LIMIT 5) AS top_skills
             FROM {$prefix}experts e
             {$whereSQL} {$orderSQL}
             LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );
        $experts = $stmt->fetchAll() ?: [];

        // Filter-Optionen laden
        $locRows = $db->execute(
            "SELECT DISTINCT location_city FROM {$prefix}experts WHERE status = 'active' AND location_city IS NOT NULL AND location_city != '' ORDER BY location_city ASC LIMIT 50"
        )->fetchAll() ?: [];
        $locations = array_column(array_map('get_object_vars', array_filter($locRows, 'is_object')) + array_filter($locRows, 'is_array'), 'location_city');

        $skillRows = $db->execute(
            "SELECT DISTINCT skill_name FROM {$prefix}expert_skills ORDER BY skill_name ASC LIMIT 80"
        )->fetchAll() ?: [];
        $skills = array_map(fn($r) => is_array($r) ? ($r['skill_name'] ?? '') : ($r->skill_name ?? ''), $skillRows);

    } catch (\Throwable $e) { /* */ }
}

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main experts-page" role="main" data-page="experts">

    <section class="directory-hero experts-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>👤 Experten-Verzeichnis</h1>
                <p class="directory-hero-sub">
                    <?php if ($totalCount > 0): ?>
                        <strong><?php echo number_format($totalCount); ?></strong> Exper<?php echo $totalCount !== 1 ? 'ten' : 'te'; ?> in der Datenbank
                    <?php elseif ($hasPlugin): ?>
                        IT-Experten, Berater und Fachkräfte aus der DACH-Region
                    <?php else: ?>
                        Das Experten-Plugin aktivieren, um das Verzeichnis zu nutzen.
                    <?php endif; ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Name, Position, Technologie …"
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
            <span aria-current="page">Experten</span>
        </div>
    </nav>

    <div class="directory-layout container">

        <!-- Sidebar: Filter -->
        <aside class="directory-filters" aria-label="Experten-Filter">

            <form method="GET" action="" id="expert-filter-form">
                <?php if ($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"><?php endif; ?>

                <!-- Standort -->
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

                <!-- Skills -->
                <?php if (!empty($skills)): ?>
                <div class="filter-panel">
                    <h3 class="filter-panel-title">🛠️ Skills</h3>
                    <select name="skill" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle Skills</option>
                        <?php foreach ($skills as $sk): if (!$sk) continue; ?>
                            <option value="<?php echo htmlspecialchars($sk, ENT_QUOTES); ?>"
                                <?php echo $skill === $sk ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sk, ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <!-- Verfügbarkeit -->
                <div class="filter-panel">
                    <h3 class="filter-panel-title">✅ Verfügbarkeit</h3>
                    <div class="filter-link-list">
                        <?php
                        $availOpts = ['' => 'Alle', 'available' => '✅ Verfügbar', 'limited' => '🟡 Eingeschränkt', 'booked' => '🔴 Gebucht'];
                        foreach ($availOpts as $val => $label): ?>
                            <a href="<?php echo htmlspecialchars(theme_build_query_url('/experts', $_GET, ['avail' => $val, 'page' => '1']), ENT_QUOTES, 'UTF-8'); ?>"
                               class="filter-link <?php echo $avail === $val ? 'is-active' : ''; ?>">
                                <?php echo $label; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sortierung -->
                <div class="filter-panel">
                    <h3 class="filter-panel-title">🗂️ Sortierung</h3>
                    <select name="sort" class="filter-select" data-auto-submit-filter>
                        <option value="latest"     <?php echo $sort === 'latest'     ? 'selected' : ''; ?>>🕐 Neueste zuerst</option>
                        <option value="name"       <?php echo $sort === 'name'       ? 'selected' : ''; ?>>🔤 Name A–Z</option>
                        <option value="experience" <?php echo $sort === 'experience' ? 'selected' : ''; ?>>⭐ Erfahrung</option>
                    </select>
                </div>

                <?php if ($search || $location || $skill || $avail): ?>
                    <div class="filter-panel">
                        <a href="<?php echo htmlspecialchars($expertsBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary filter-reset-btn">✖ Filter zurücksetzen</a>
                    </div>
                <?php endif; ?>
            </form>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0): ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Experten</span>
                    <?php endif; ?>
                </div>
                <div class="directory-toolbar-right">
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/experts', $_GET, ['view' => 'grid']), ENT_QUOTES, 'UTF-8'); ?>"
                       class="view-toggle-btn <?php echo $view === 'grid' ? 'is-active' : ''; ?>" aria-label="Rasteransicht">⊞</a>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/experts', $_GET, ['view' => 'list']), ENT_QUOTES, 'UTF-8'); ?>"
                       class="view-toggle-btn <?php echo $view === 'list' ? 'is-active' : ''; ?>" aria-label="Listenansicht">≡</a>
                </div>
            </div>

            <?php if (!empty($experts)): ?>
            <div class="directory-grid directory-grid--<?php echo $view; ?>">
                <?php foreach ($experts as $exp):
                    $id         = is_array($exp) ? ($exp['id'] ?? 0)            : ($exp->id ?? 0);
                    $name       = htmlspecialchars(is_array($exp) ? ($exp['display_name'] ?? '') : ($exp->display_name ?? ''), ENT_QUOTES, 'UTF-8');
                    $position   = htmlspecialchars(is_array($exp) ? ($exp['position']     ?? '') : ($exp->position     ?? ''), ENT_QUOTES, 'UTF-8');
                    $company    = htmlspecialchars(is_array($exp) ? ($exp['company']      ?? '') : ($exp->company      ?? ''), ENT_QUOTES, 'UTF-8');
                    $city       = htmlspecialchars(is_array($exp) ? ($exp['location_city']?? '') : ($exp->location_city?? ''), ENT_QUOTES, 'UTF-8');
                    $photo      = is_array($exp) ? ($exp['photo_url']       ?? '') : ($exp->photo_url       ?? '');
                    $expYears   = (int)(is_array($exp) ? ($exp['experience_years'] ?? 0) : ($exp->experience_years ?? 0));
                    $avlbl      = is_array($exp) ? ($exp['availability'] ?? '') : ($exp->availability ?? '');
                    $topSkills  = htmlspecialchars(is_array($exp) ? ($exp['top_skills'] ?? '') : ($exp->top_skills ?? ''), ENT_QUOTES, 'UTF-8');
                    $avlblClass = match ($avlbl) { 'available' => 'badge-available', 'limited' => 'badge-limited', default => 'badge-booked' };
                    $avlblLabel = match ($avlbl) { 'available' => '✅ Verfügbar', 'limited' => '🟡 Eingeschränkt', default => '🔴 Gebucht' };
                    $photoUrl   = theme_safe_url((string) $photo);
                    $detailUrl  = theme_safe_url($siteUrl . '/experts/' . (int) $id, $expertsBaseUrl);
                ?>
                <article class="directory-card expert-card">
                    <div class="expert-card-photo">
                        <?php if ($photoUrl): ?>
                            <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo $name; ?>" loading="lazy" width="80" height="80">
                        <?php else: ?>
                            <div class="expert-card-avatar"><?php echo mb_strtoupper(mb_substr(strip_tags($name), 0, 2)); ?></div>
                        <?php endif; ?>
                        <span class="availability-badge <?php echo $avlblClass; ?>"><?php echo $avlblLabel; ?></span>
                    </div>
                    <div class="expert-card-body">
                        <h2 class="expert-card-name">
                            <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo $name; ?>
                            </a>
                        </h2>
                        <?php if ($position): ?><p class="expert-card-position"><?php echo $position; ?></p><?php endif; ?>
                        <?php if ($company):  ?><p class="expert-card-company">🏢 <?php echo $company; ?></p><?php endif; ?>
                        <?php if ($city):     ?><p class="expert-card-location">📍 <?php echo $city; ?></p><?php endif; ?>
                        <?php if ($expYears > 0): ?><p class="expert-card-exp">⭐ <?php echo $expYears; ?> Jahre Erfahrung</p><?php endif; ?>
                        <?php if ($topSkills): ?>
                            <div class="expert-card-skills">
                                <?php foreach (explode(', ', $topSkills) as $sk): if (!trim($sk)) continue; ?>
                                    <span class="skill-chip"><?php echo htmlspecialchars(trim($sk), ENT_QUOTES); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="expert-card-footer">
                        <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>"
                           class="btn btn-primary btn-sm">Profil ansehen</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin): ?>
            <div class="empty-state">
                <div class="empty-state-icon">👤</div>
                <h3>Keine Experten gefunden</h3>
                <p>Versuche andere Suchbegriffe oder setze die Filter zurück.</p>
                <a href="<?php echo htmlspecialchars($expertsBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary empty-state-reset-btn">✖ Filter zurücksetzen</a>
            </div>

            <?php else: ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">👤</div>
                <h3>Experten-Verzeichnis</h3>
                <p>Das <strong>cms-experts</strong> Plugin aktivieren, um Experten-Profile anzulegen und zu durchsuchen.</p>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="directory-pagination" aria-label="Seitennavigation">
                <?php if ($page > 1): ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/experts', $_GET, ['page' => (string) ($page - 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="pagination-btn">← Zurück</a>
                <?php endif; ?>
                <div class="pagination-pages">
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/experts', $_GET, ['page' => (string) $i]), ENT_QUOTES, 'UTF-8'); ?>"
                           class="pagination-page <?php echo $i === $page ? 'is-current' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/experts', $_GET, ['page' => (string) ($page + 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="pagination-btn">Weiter →</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
