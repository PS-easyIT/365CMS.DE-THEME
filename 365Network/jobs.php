<?php
/**
 * Stellenprofil-Verzeichnis – 365Network Theme
 *
 * Übersicht aller veröffentlichten Jobprofile aus dem
 * cms-jobprofile-generator Plugin.
 *
 * URL-Parameter:
 *   q      – Suchbegriff (Titel, Ort)
 *   type   – employment_type (fulltime|parttime|freelance|internship|mini)
 *   level  – experience_level (entry|junior|mid|senior|lead|executive)
 *   remote – remote_option (onsite|hybrid|remote)
 *   sort   – (latest|title|views)
 *   view   – (grid|list)
 *   page   – Seite
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
$hasPlugin = $pluginMgr->isPluginActive('cms-jobprofile-generator');

// ── Gültige Enum-Werte ──
$validTypes  = ['fulltime', 'parttime', 'freelance', 'internship', 'mini'];
$validLevels = ['entry', 'junior', 'mid', 'senior', 'lead', 'executive'];
$validRemote = ['onsite', 'hybrid', 'remote'];
$validSorts  = ['latest', 'title', 'views'];

// ── Parameter ──
$search = trim(strip_tags($_GET['q']      ?? ''));
$type   = in_array($_GET['type']   ?? '', $validTypes)  ? $_GET['type']   : '';
$level  = in_array($_GET['level']  ?? '', $validLevels) ? $_GET['level']  : '';
$remote = in_array($_GET['remote'] ?? '', $validRemote) ? $_GET['remote'] : '';
$sort   = in_array($_GET['sort']   ?? '', $validSorts)  ? $_GET['sort']   : 'latest';
$view   = ($_GET['view'] ?? 'grid') === 'list' ? 'list' : 'grid';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 18;

$jobs       = [];
$totalCount = 0;
$totalPages = 1;

// Labels
$typeLabels = [
    'fulltime'    => '👔 Vollzeit',
    'parttime'    => '🕐 Teilzeit',
    'freelance'   => '💼 Freiberuflich',
    'internship'  => '🎓 Praktikum',
    'mini'        => '🪙 Minijob',
];
$levelLabels = [
    'entry'     => '🌱 Einsteiger',
    'junior'    => '🔰 Junior',
    'mid'       => '⚡ Mittel',
    'senior'    => '🏆 Senior',
    'lead'      => '🚀 Lead',
    'executive' => '🏛️ Executive',
];
$remoteLabels = [
    'onsite'  => '🏢 Vor Ort',
    'hybrid'  => '🔄 Hybrid',
    'remote'  => '🌐 Remote',
];

if ($hasPlugin) {
    try {
        $where  = ["p.status = 'published'"];
        $params = [];

        if ($search !== '') {
            $term    = '%' . $search . '%';
            $where[] = "(p.title LIKE ? OR p.location LIKE ? OR p.summary LIKE ?)";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($type !== '') {
            $where[]  = "p.employment_type = ?";
            $params[] = $type;
        }
        if ($level !== '') {
            $where[]  = "p.experience_level = ?";
            $params[] = $level;
        }
        if ($remote !== '') {
            $where[]  = "p.remote_option = ?";
            $params[] = $remote;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = match ($sort) {
            'title' => 'ORDER BY p.title ASC',
            'views' => 'ORDER BY p.views DESC',
            default => 'ORDER BY p.published_at DESC, p.created_at DESC',
        };

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}jpg_profiles p {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(is_array($cRow) ? ($cRow['cnt'] ?? 0) : ($cRow->cnt ?? 0));
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->execute(
            "SELECT p.id, p.title, p.slug, p.location, p.employment_type, p.experience_level,
                    p.remote_option, p.salary_min, p.salary_max, p.salary_currency,
                    p.summary, p.published_at, p.views
             FROM {$prefix}jpg_profiles p {$whereSQL} {$orderSQL}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $jobs = $stmt->fetchAll() ?: [];

    } catch (\Throwable $e) { /* */ }
}

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main jobs-page" role="main" data-page="jobs">

    <section class="directory-hero jobs-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>💼 Jobprofile</h1>
                <p class="directory-hero-sub">
                    <?php if ($totalCount > 0): ?>
                        <strong><?php echo number_format($totalCount); ?></strong> veröffentlichte Stellenprofile
                    <?php elseif ($hasPlugin): ?>
                        Alle aktuellen Stellenprofile im Überblick
                    <?php else: ?>
                        Das jobprofile-generator Plugin aktivieren, um Stellen sichtbar zu machen.
                    <?php endif; ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Titel, Ort, Stichwort …"
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
            <span aria-current="page">Jobprofile</span>
        </div>
    </nav>

    <div class="directory-layout container">

        <aside class="directory-filters" aria-label="Jobprofil-Filter">
            <form method="GET" action="">
                <?php if ($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"><?php endif; ?>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">👔 Anstellungsart</h3>
                    <select name="type" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle</option>
                        <?php foreach ($typeLabels as $val => $lbl): ?>
                            <option value="<?php echo $val; ?>" <?php echo $type === $val ? 'selected' : ''; ?>><?php echo $lbl; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">📈 Erfahrungsstufe</h3>
                    <select name="level" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle</option>
                        <?php foreach ($levelLabels as $val => $lbl): ?>
                            <option value="<?php echo $val; ?>" <?php echo $level === $val ? 'selected' : ''; ?>><?php echo $lbl; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">🌐 Remote-Option</h3>
                    <div class="filter-link-list">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['remote' => '', 'page' => 1])); ?>"
                           class="filter-link <?php echo $remote === '' ? 'is-active' : ''; ?>">Alle</a>
                        <?php foreach ($remoteLabels as $val => $lbl): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['remote' => $val, 'page' => 1])); ?>"
                               class="filter-link <?php echo $remote === $val ? 'is-active' : ''; ?>"><?php echo $lbl; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">🗂️ Sortierung</h3>
                    <select name="sort" class="filter-select" data-auto-submit-filter>
                        <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>🕐 Neueste zuerst</option>
                        <option value="title"  <?php echo $sort === 'title'  ? 'selected' : ''; ?>>🔤 Titel A–Z</option>
                        <option value="views"  <?php echo $sort === 'views'  ? 'selected' : ''; ?>>👁 Meiste Aufrufe</option>
                    </select>
                </div>

                <?php if ($search || $type || $level || $remote): ?>
                    <div class="filter-panel">
                        <a href="/jobs" class="btn btn-secondary filter-reset-btn">✖ Filter zurücksetzen</a>
                    </div>
                <?php endif; ?>
            </form>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0): ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Stellenprofile</span>
                    <?php endif; ?>
                </div>
                <div class="directory-toolbar-right">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'grid'])); ?>"
                       class="view-toggle-btn <?php echo $view === 'grid' ? 'is-active' : ''; ?>" aria-label="Rasteransicht">⊞</a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['view' => 'list'])); ?>"
                       class="view-toggle-btn <?php echo $view === 'list' ? 'is-active' : ''; ?>" aria-label="Listenansicht">≡</a>
                </div>
            </div>

            <?php if (!empty($jobs)): ?>
            <div class="directory-grid directory-grid--<?php echo $view; ?>">
                <?php foreach ($jobs as $job):
                    $id       = is_array($job) ? ($job['id']              ?? 0)  : ($job->id              ?? 0);
                    $slug     = is_array($job) ? ($job['slug']            ?? '')  : ($job->slug            ?? '');
                    $title    = htmlspecialchars(is_array($job) ? ($job['title']           ?? '') : ($job->title           ?? ''), ENT_QUOTES, 'UTF-8');
                    $location = htmlspecialchars(is_array($job) ? ($job['location']        ?? '') : ($job->location        ?? ''), ENT_QUOTES, 'UTF-8');
                    $empType  = is_array($job) ? ($job['employment_type']  ?? '') : ($job->employment_type  ?? '');
                    $expLevel = is_array($job) ? ($job['experience_level'] ?? '') : ($job->experience_level ?? '');
                    $remOpt   = is_array($job) ? ($job['remote_option']   ?? '') : ($job->remote_option   ?? '');
                    $salMin   = (int)(is_array($job) ? ($job['salary_min'] ?? 0) : ($job->salary_min ?? 0));
                    $salMax   = (int)(is_array($job) ? ($job['salary_max'] ?? 0) : ($job->salary_max ?? 0));
                    $currency = htmlspecialchars(is_array($job) ? ($job['salary_currency'] ?? 'EUR') : ($job->salary_currency ?? 'EUR'), ENT_QUOTES, 'UTF-8');
                    $summary  = htmlspecialchars(strip_tags(is_array($job) ? ($job['summary'] ?? '') : ($job->summary ?? '')), ENT_QUOTES, 'UTF-8');
                    $pubDate  = is_array($job) ? ($job['published_at'] ?? '') : ($job->published_at ?? '');
                    $href     = $slug
                        ? htmlspecialchars($siteUrl . '/jobs/' . $slug, ENT_QUOTES)
                        : htmlspecialchars($siteUrl . '/jobs/' . (int)$id, ENT_QUOTES);
                ?>
                <article class="directory-card job-card">
                    <div class="job-card-header">
                        <div class="job-card-icon">💼</div>
                        <div class="job-card-badges">
                            <?php if ($empType && isset($typeLabels[$empType])): ?>
                                <span class="job-badge job-badge--type"><?php echo $typeLabels[$empType]; ?></span>
                            <?php endif; ?>
                            <?php if ($remOpt && isset($remoteLabels[$remOpt])): ?>
                                <span class="job-badge job-badge--remote"><?php echo $remoteLabels[$remOpt]; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="job-card-body">
                        <h2 class="job-card-title">
                            <a href="<?php echo $href; ?>"><?php echo $title; ?></a>
                        </h2>
                        <div class="job-card-meta">
                            <?php if ($location): ?><span class="job-meta-item">📍 <?php echo $location; ?></span><?php endif; ?>
                            <?php if ($expLevel && isset($levelLabels[$expLevel])): ?><span class="job-meta-item"><?php echo $levelLabels[$expLevel]; ?></span><?php endif; ?>
                        </div>
                        <?php if ($salMin > 0 || $salMax > 0): ?>
                            <p class="job-card-salary">
                                💰
                                <?php if ($salMin > 0 && $salMax > 0): ?>
                                    <?php echo number_format($salMin, 0, ',', '.'); ?> – <?php echo number_format($salMax, 0, ',', '.'); ?> <?php echo $currency; ?>
                                <?php elseif ($salMin > 0): ?>
                                    ab <?php echo number_format($salMin, 0, ',', '.'); ?> <?php echo $currency; ?>
                                <?php else: ?>
                                    bis <?php echo number_format($salMax, 0, ',', '.'); ?> <?php echo $currency; ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($summary): ?><p class="job-card-summary"><?php echo mb_strimwidth(strip_tags($summary), 0, 130, '…'); ?></p><?php endif; ?>
                    </div>
                    <div class="job-card-footer">
                        <a href="<?php echo $href; ?>" class="btn btn-primary btn-sm">Profil ansehen</a>
                        <?php if ($pubDate): ?>
                            <span class="job-card-date"><?php echo date('d.m.Y', strtotime($pubDate)); ?></span>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin): ?>
            <div class="empty-state">
                <div class="empty-state-icon">💼</div>
                <h3>Keine Stellenprofile gefunden</h3>
                <p>Versuche andere Suchbegriffe oder setze die Filter zurück.</p>
                <a href="/jobs" class="btn btn-secondary empty-state-reset-btn">✖ Filter zurücksetzen</a>
            </div>

            <?php else: ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">💼</div>
                <h3>Jobprofile</h3>
                <p>Das <strong>cms-jobprofile-generator</strong> Plugin aktivieren, um Stellenprofile anzulegen und zu veröffentlichen.</p>
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
