<?php
/**
 * Feed-Aggregator – 365Network Theme
 *
 * Vollständige Beitrags-Aggregator-Seite mit Quelle-Filter,
 * Kategorie-Filter und Paginierung.
 * Verbindet sich mit dem cms-feed Plugin.
 *
 * URL-Parameter:
 *   q       – Suchbegriff
 *   source  – Feed-Quelle (ID)
 *   cat     – Kategorie
 *   sort    – (latest|title)
 *   page    – Seite
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
$hasPlugin = $pluginMgr->isPluginActive('cms-feed');

// ── Parameter ──
$search = trim(strip_tags($_GET['q'] ?? ''));
$source = (int)($_GET['source'] ?? 0);
$cat    = trim(strip_tags($_GET['cat'] ?? ''));
$sort   = ($_GET['sort'] ?? 'latest') === 'title' ? 'title' : 'latest';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 18;

$items      = [];
$sources    = [];
$totalCount = 0;
$totalPages = 1;

if ($hasPlugin) {
    try {
        $where  = ['fi.is_hidden = 0'];
        $params = [];

        if ($search !== '') {
            $term = '%' . $search . '%';
            $where[] = "(fi.title LIKE ? OR fi.description LIKE ? OR fi.author LIKE ?)";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($source > 0) {
            $where[]  = "fi.feed_id = ?";
            $params[] = $source;
        }
        if ($cat !== '') {
            $where[]  = "fi.category LIKE ?";
            $params[] = '%' . $cat . '%';
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = $sort === 'title' ? 'ORDER BY fi.title ASC' : 'ORDER BY fi.pub_date DESC';

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}feed_items fi {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(($cRow)->cnt ?? 0);
        $totalPages = (int)ceil($totalCount / $perPage);
        $page = min($page, max(1, $totalPages));
        $offset = ($page - 1) * $perPage;

        $stmt = $db->execute(
            "SELECT fi.*, f.name AS feed_name, f.favicon AS feed_icon
             FROM {$prefix}feed_items fi
             LEFT JOIN {$prefix}feeds f ON fi.feed_id = f.id
             {$whereSQL} {$orderSQL}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $items = $stmt->fetchAll() ?: [];

        // Feed-Quellen für Filter
        $srcRows = $db->execute(
            "SELECT id, name FROM {$prefix}feeds WHERE is_active = 1 ORDER BY name ASC"
        )->fetchAll() ?: [];
        $sources = array_map(fn($r) => [
            'id' => (int)(is_array($r) ? ($r['id'] ?? 0) : ($r->id ?? 0)),
            'name' => is_array($r) ? ($r['name'] ?? '') : ($r->name ?? ''),
        ], $srcRows);

    } catch (\Throwable $e) { /* */ }
}

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main feeds-page" role="main" data-page="feeds">

    <section class="directory-hero feeds-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>📰 Feed-Aggregator</h1>
                <p class="directory-hero-sub">
                    <?php
                    if ($totalCount > 0) {
                        echo '<strong>' . number_format($totalCount) . '</strong> Beitrag' . ($totalCount !== 1 ? 'e' : '') . ' aus ' . count($sources) . ' Quellen';
                    } elseif ($hasPlugin) {
                        echo 'Aktuelle Beiträge aus allen abonnierten Quellen im Überblick';
                    } else {
                        echo 'Das Feed-Plugin aktivieren, um externe Beiträge zu aggregieren.';
                    }
                    ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Beitragsthema, Autor …"
                           class="directory-search-input">
                    <?php if ($source) : ?><input type="hidden" name="source" value="<?php echo $source; ?>"><?php endif; ?>
                    <button type="submit" class="btn btn-primary directory-search-btn">🔍 Suchen</button>
                </div>
            </form>
        </div>
    </section>

    <nav class="breadcrumb-bar" aria-label="Pfadnavigation">
        <div class="container">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/">Startseite</a>
            <span class="breadcrumb-sep">›</span>
            <span aria-current="page">Feeds</span>
        </div>
    </nav>

    <div class="directory-layout container">

        <!-- Sidebar: Quellen -->
        <aside class="directory-filters" aria-label="Feed-Quellen">
            <div class="filter-panel">
                <h3 class="filter-panel-title">📡 Quellen</h3>
                <div class="filter-link-list">
                    <a href="?<?php echo $search ? 'q=' . urlencode($search) : ''; ?>"
                       class="feed-source-link <?php echo $source === 0 ? 'is-active' : ''; ?>">
                        Alle Quellen
                    </a>
                    <?php foreach ($sources as $src) : ?>
                        <a href="?source=<?php echo $src['id']; ?><?php echo $search ? '&q=' . urlencode($search) : ''; ?>"
                           class="feed-source-link <?php echo $source === $src['id'] ? 'is-active' : ''; ?>">
                            <?php echo htmlspecialchars($src['name'], ENT_QUOTES); ?>
                        </a>
                    <?php endforeach; ?>
                    <?php if (empty($sources) && $hasPlugin) : ?>
                        <p class="form-text">Noch keine Quellen konfiguriert.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="filter-panel">
                <h3 class="filter-panel-title">🗂️ Optionen</h3>
                <form method="GET" action="">
                    <?php if ($source) : ?><input type="hidden" name="source" value="<?php echo $source; ?>"><?php endif; ?>
                    <div class="filter-group">
                        <label class="filter-label" for="f-sort">Sortierung</label>
                        <select name="sort" id="f-sort" class="filter-select" data-auto-submit-filter>
                            <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>🕐 Neueste zuerst</option>
                            <option value="title"  <?php echo $sort === 'title' ? 'selected' : ''; ?>>🔤 Titel A–Z</option>
                        </select>
                    </div>
                </form>
            </div>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0) : ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Beiträge</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($items)) : ?>
            <div class="feeds-grid">
                <?php foreach ($items as $item) :
                    $iTitle   = htmlspecialchars(is_array($item) ? ($item['title'] ?? '') : ($item->title ?? ''), ENT_QUOTES, 'UTF-8');
                    $iLink    = htmlspecialchars(is_array($item) ? ($item['link'] ?? '#') : ($item->link ?? '#'), ENT_QUOTES, 'UTF-8');
                    $iDesc    = strip_tags(is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''));
                    $iImage   = is_array($item) ? ($item['image_url'] ?? '') : ($item->image_url ?? '');
                    $iAuthor  = htmlspecialchars(is_array($item) ? ($item['author'] ?? '') : ($item->author ?? ''), ENT_QUOTES, 'UTF-8');
                    $iDate    = is_array($item) ? ($item['pub_date'] ?? '') : ($item->pub_date ?? '');
                    $iDateF   = $iDate ? date('d.m.Y', strtotime($iDate)) : '';
                    $iFeed    = htmlspecialchars(is_array($item) ? ($item['feed_name'] ?? '') : ($item->feed_name ?? ''), ENT_QUOTES, 'UTF-8');
                    $iFavicon = is_array($item) ? ($item['feed_icon'] ?? '') : ($item->feed_icon ?? '');
                    $iCat     = htmlspecialchars(is_array($item) ? ($item['category'] ?? '') : ($item->category ?? ''), ENT_QUOTES, 'UTF-8');
                ?>
                <article class="feed-dir-card">
                    <?php if ($iImage) : ?>
                        <div class="feed-dir-image">
                            <a href="<?php echo $iLink; ?>" target="_blank" rel="noopener noreferrer">
                                <img src="<?php echo htmlspecialchars($iImage, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo $iTitle; ?>" loading="lazy" width="400" height="200">
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="feed-dir-body">
                        <div class="feed-dir-source">
                            <?php if ($iFavicon) : ?><img src="<?php echo htmlspecialchars($iFavicon, ENT_QUOTES); ?>" alt="" width="16" height="16" loading="lazy"><?php endif; ?>
                            <?php if ($iFeed) : ?><span><?php echo $iFeed; ?></span><?php endif; ?>
                            <?php if ($iCat) : ?><span class="feed-cat-badge"><?php echo $iCat; ?></span><?php endif; ?>
                        </div>
                        <h2 class="feed-dir-title">
                            <a href="<?php echo $iLink; ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo $iTitle; ?>
                            </a>
                        </h2>
                        <?php if ($iDesc) : ?>
                            <p class="feed-dir-desc"><?php echo htmlspecialchars(mb_strimwidth($iDesc, 0, 150, '…'), ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <div class="feed-dir-meta">
                            <?php if ($iAuthor) : ?><span>✍️ <?php echo $iAuthor; ?></span><?php endif; ?>
                            <?php if ($iDateF) : ?><span>📅 <?php echo $iDateF; ?></span><?php endif; ?>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin) : ?>
            <div class="empty-state">
                <div class="empty-state-icon">📰</div>
                <h3>Keine Beiträge gefunden</h3>
                <p>Versuche andere Suchbegriffe oder lass alle Quellen anzeigen.</p>
            </div>

            <?php else : ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">📰</div>
                <h3>Feed-Aggregator</h3>
                <p>Das <strong>cms-feed</strong> Plugin aktivieren, um externe Beitragsquellen zu aggregieren.</p>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1) : ?>
            <nav class="directory-pagination" aria-label="Seitennavigation">
                <?php if ($page > 1) : ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-btn">← Zurück</a>
                <?php endif; ?>
                <div class="pagination-pages">
                    <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++) : ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                           class="pagination-page <?php echo $i === $page ? 'is-current' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($page < $totalPages) : ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-btn">Weiter →</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
