<?php
/**
 * Speaker-Verzeichnis – 365Network Theme
 *
 * Vollständige Speaker-Übersicht mit Themen-, Sprach- und
 * Verfügbarkeits-Filter sowie Grid/Listenansicht.
 * Verbindet sich mit dem cms-speakers Plugin.
 *
 * URL-Parameter:
 *   q     – Suchbegriff (Name, Position, Thema)
 *   topic – Themengebiet
 *   lang  – Sprache
 *   avail – Verfügbarkeit (available|limited|booked)
 *   sort  – (latest|name|events)
 *   view  – (grid|list)
 *   page  – Seite
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
$hasPlugin = $pluginMgr->isPluginActive('cms-speakers');
$homeUrl   = theme_safe_url($siteUrl . '/', $siteUrl . '/');
$speakersBaseUrl = theme_safe_url($siteUrl . '/speakers', $siteUrl . '/speakers');

// ── Parameter ──
$search = trim(strip_tags($_GET['q'] ?? ''));
$topic  = trim(strip_tags($_GET['topic'] ?? ''));
$lang   = trim(strip_tags($_GET['lang'] ?? ''));
$avail  = in_array($_GET['avail'] ?? '', ['available', 'limited', 'booked']) ? $_GET['avail'] : '';
$sort   = in_array($_GET['sort'] ?? '', ['latest', 'name', 'events']) ? $_GET['sort'] : 'latest';
$view   = ($_GET['view'] ?? 'grid') === 'list' ? 'list' : 'grid';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 18;

$speakers   = [];
$topics     = [];
$totalCount = 0;
$totalPages = 1;

if ($hasPlugin) {
    try {
        $where  = ["s.status = 'active'"];
        $params = [];

        if ($search !== '') {
            $term    = '%' . $search . '%';
            $where[] = "(CONCAT(s.first_name,' ',s.last_name) LIKE ? OR s.position LIKE ? OR s.company LIKE ?)";
            $params  = array_merge($params, [$term, $term, $term]);
        }
        if ($topic !== '') {
            $where[] = "EXISTS (SELECT 1 FROM {$prefix}speaker_topics st WHERE st.speaker_id = s.id AND st.topic_name LIKE ?)";
            $params[] = '%' . $topic . '%';
        }
        if ($avail !== '') {
            $where[]  = "s.availability = ?";
            $params[] = $avail;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);
        $orderSQL = match ($sort) {
            'name'   => 'ORDER BY s.last_name ASC, s.first_name ASC',
            'events' => 'ORDER BY (SELECT COUNT(*) FROM ' . $prefix . 'speaker_events se WHERE se.speaker_id = s.id) DESC',
            default  => 'ORDER BY s.is_featured DESC, s.created_at DESC',
        };

        $cRow = $db->execute(
            "SELECT COUNT(*) AS cnt FROM {$prefix}speakers s {$whereSQL}", $params
        )->fetch();
        $totalCount = (int)(($cRow)->cnt ?? 0);
        $totalPages = max(1, (int)ceil($totalCount / $perPage));
        $page   = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->execute(
            "SELECT s.*,
                    CONCAT(s.first_name, ' ', s.last_name) AS display_name,
                    (SELECT GROUP_CONCAT(topic_name ORDER BY sort_order SEPARATOR ', ')
                     FROM {$prefix}speaker_topics WHERE speaker_id = s.id LIMIT 4) AS top_topics,
                    (SELECT COUNT(*) FROM {$prefix}speaker_events WHERE speaker_id = s.id) AS event_count
             FROM {$prefix}speakers s {$whereSQL} {$orderSQL}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        $speakers = $stmt->fetchAll() ?: [];

        // Themen für Filter
        $topicRows = $db->execute(
            "SELECT DISTINCT topic_name FROM {$prefix}speaker_topics ORDER BY topic_name ASC LIMIT 60"
        )->fetchAll() ?: [];
        $topics = array_map(fn($r) => is_array($r) ? ($r['topic_name'] ?? '') : ($r->topic_name ?? ''), $topicRows);

    } catch (\Throwable $e) { /* */ }
}

require_once __DIR__ . '/header.php';
?>

<main id="main" class="site-main speakers-page" role="main" data-page="speakers">

    <section class="directory-hero speakers-hero" data-section="page-hero">
        <div class="directory-hero-inner">
            <div class="directory-hero-text">
                <h1>🎤 Speaker-Verzeichnis</h1>
                <p class="directory-hero-sub">
                    <?php if ($totalCount > 0): ?>
                        <strong><?php echo number_format($totalCount); ?></strong> Speaker im Verzeichnis
                    <?php elseif ($hasPlugin): ?>
                        Keynote-Speaker, Workshop-Trainer und Moderatoren für Ihr Event
                    <?php else: ?>
                        Das Speaker-Plugin aktivieren, um das Verzeichnis zu nutzen.
                    <?php endif; ?>
                </p>
            </div>
            <form class="directory-search-form" method="GET" action="">
                <div class="directory-search-row">
                    <input type="search" name="q"
                           value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="Name, Thema, Position …"
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
            <span aria-current="page">Speaker</span>
        </div>
    </nav>

    <div class="directory-layout container">

        <!-- Sidebar: Filter -->
        <aside class="directory-filters" aria-label="Speaker-Filter">
            <form method="GET" action="">
                <?php if ($search): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>"><?php endif; ?>

                <?php if (!empty($topics)): ?>
                <div class="filter-panel">
                    <h3 class="filter-panel-title">💡 Thema</h3>
                    <select name="topic" class="filter-select" data-auto-submit-filter>
                        <option value="">Alle Themen</option>
                        <?php foreach ($topics as $t): if (!$t) continue; ?>
                            <option value="<?php echo htmlspecialchars($t, ENT_QUOTES); ?>"
                                <?php echo $topic === $t ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t, ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="filter-panel">
                    <h3 class="filter-panel-title">✅ Verfügbarkeit</h3>
                    <div class="filter-link-list">
                        <?php
                        $availOpts = ['' => 'Alle', 'available' => '✅ Verfügbar', 'limited' => '🟡 Eingeschränkt', 'booked' => '🔴 Gebucht'];
                        foreach ($availOpts as $val => $lbl): ?>
                            <a href="<?php echo htmlspecialchars(theme_build_query_url('/speakers', $_GET, ['avail' => $val, 'page' => '1']), ENT_QUOTES, 'UTF-8'); ?>"
                               class="filter-link <?php echo $avail === $val ? 'is-active' : ''; ?>">
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
                        <option value="events" <?php echo $sort === 'events' ? 'selected' : ''; ?>>🎤 Meiste Events</option>
                    </select>
                </div>

                <?php if ($search || $topic || $avail): ?>
                    <div class="filter-panel">
                        <a href="<?php echo htmlspecialchars($speakersBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary filter-reset-btn">✖ Filter zurücksetzen</a>
                    </div>
                <?php endif; ?>
            </form>
        </aside>

        <div class="directory-main">

            <div class="directory-toolbar">
                <div class="directory-toolbar-left">
                    <?php if ($totalCount > 0): ?>
                        <span class="directory-result-count"><?php echo number_format($totalCount); ?> Speaker</span>
                    <?php endif; ?>
                </div>
                <div class="directory-toolbar-right">
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/speakers', $_GET, ['view' => 'grid']), ENT_QUOTES, 'UTF-8'); ?>"
                       class="view-toggle-btn <?php echo $view === 'grid' ? 'is-active' : ''; ?>" aria-label="Rasteransicht">⊞</a>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/speakers', $_GET, ['view' => 'list']), ENT_QUOTES, 'UTF-8'); ?>"
                       class="view-toggle-btn <?php echo $view === 'list' ? 'is-active' : ''; ?>" aria-label="Listenansicht">≡</a>
                </div>
            </div>

            <?php if (!empty($speakers)): ?>
            <div class="directory-grid directory-grid--<?php echo $view; ?>">
                <?php foreach ($speakers as $sp):
                    $id         = is_array($sp) ? ($sp['id']          ?? 0)  : ($sp->id          ?? 0);
                    $name       = htmlspecialchars(is_array($sp) ? ($sp['display_name'] ?? '') : ($sp->display_name ?? ''), ENT_QUOTES, 'UTF-8');
                    $title      = htmlspecialchars(is_array($sp) ? ($sp['title']       ?? '') : ($sp->title       ?? ''), ENT_QUOTES, 'UTF-8');
                    $position   = htmlspecialchars(is_array($sp) ? ($sp['position']    ?? '') : ($sp->position    ?? ''), ENT_QUOTES, 'UTF-8');
                    $company    = htmlspecialchars(is_array($sp) ? ($sp['company']     ?? '') : ($sp->company     ?? ''), ENT_QUOTES, 'UTF-8');
                    $city       = htmlspecialchars(is_array($sp) ? ($sp['location_city'] ?? '') : ($sp->location_city ?? ''), ENT_QUOTES, 'UTF-8');
                    $photo      = is_array($sp) ? ($sp['photo_url']    ?? '') : ($sp->photo_url    ?? '');
                    $shortBio   = htmlspecialchars(strip_tags(is_array($sp) ? ($sp['short_bio'] ?? '') : ($sp->short_bio ?? '')), ENT_QUOTES, 'UTF-8');
                    $avlbl      = is_array($sp) ? ($sp['availability'] ?? '') : ($sp->availability ?? '');
                    $featured   = (bool)(is_array($sp) ? ($sp['is_featured'] ?? false) : ($sp->is_featured ?? false));
                    $topTopics  = htmlspecialchars(is_array($sp) ? ($sp['top_topics'] ?? '') : ($sp->top_topics ?? ''), ENT_QUOTES, 'UTF-8');
                    $evtCount   = (int)(is_array($sp) ? ($sp['event_count'] ?? 0) : ($sp->event_count ?? 0));
                    $avlblClass = match ($avlbl) { 'available' => 'badge-available', 'limited' => 'badge-limited', default => 'badge-booked' };
                    $avlblLabel = match ($avlbl) { 'available' => '✅ Verfügbar', 'limited' => '🟡 Eingeschränkt', default => '🔴 Gebucht' };
                    $displayTitle = $title ? $title . ' ' . strip_tags($name) : strip_tags($name);
                    $photoUrl   = theme_safe_url((string) $photo);
                    $detailUrl  = theme_safe_url($siteUrl . '/speakers/' . (int) $id, $speakersBaseUrl);
                ?>
                <article class="directory-card speaker-card<?php echo $featured ? ' speaker-card--featured' : ''; ?>">
                    <?php if ($featured): ?><span class="speaker-badge speaker-badge--featured">⭐ Featured</span><?php endif; ?>
                    <div class="speaker-card-photo">
                        <?php if ($photoUrl): ?>
                            <img src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo $name; ?>" loading="lazy" width="80" height="80">
                        <?php else: ?>
                            <div class="speaker-card-avatar">🎤</div>
                        <?php endif; ?>
                        <span class="availability-badge <?php echo $avlblClass; ?>"><?php echo $avlblLabel; ?></span>
                    </div>
                    <div class="speaker-card-body">
                        <h2 class="speaker-card-name">
                            <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($displayTitle, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h2>
                        <?php if ($position): ?><p class="speaker-card-position"><?php echo $position; ?></p><?php endif; ?>
                        <?php if ($company):  ?><p class="speaker-card-company">🏢 <?php echo $company; ?></p><?php endif; ?>
                        <?php if ($city):     ?><p class="speaker-card-location">📍 <?php echo $city; ?></p><?php endif; ?>
                        <?php if ($evtCount > 0): ?><p class="speaker-card-events">🎤 <?php echo $evtCount; ?> Event<?php echo $evtCount !== 1 ? 's' : ''; ?></p><?php endif; ?>
                        <?php if ($shortBio): ?><p class="speaker-card-bio"><?php echo mb_strimwidth(strip_tags($shortBio), 0, 120, '…'); ?></p><?php endif; ?>
                        <?php if ($topTopics): ?>
                            <div class="speaker-card-topics">
                                <?php foreach (explode(', ', $topTopics) as $tp): if (!trim($tp)) continue; ?>
                                    <span class="topic-chip"><?php echo htmlspecialchars(trim($tp), ENT_QUOTES); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="speaker-card-footer">
                        <a href="<?php echo htmlspecialchars($detailUrl, ENT_QUOTES, 'UTF-8'); ?>"
                           class="btn btn-primary btn-sm">Profil ansehen</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php elseif ($hasPlugin): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎤</div>
                <h3>Keine Speaker gefunden</h3>
                <p>Versuche andere Suchbegriffe oder setze die Filter zurück.</p>
                <a href="<?php echo htmlspecialchars($speakersBaseUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary empty-state-reset-btn">✖ Filter zurücksetzen</a>
            </div>

            <?php else: ?>
            <div class="directory-placeholder">
                <div class="directory-placeholder-icon">🎤</div>
                <h3>Speaker-Verzeichnis</h3>
                <p>Das <strong>cms-speakers</strong> Plugin aktivieren, um Speaker-Profile anzulegen und zu durchsuchen.</p>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="directory-pagination" aria-label="Seitennavigation">
                <?php if ($page > 1): ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/speakers', $_GET, ['page' => (string) ($page - 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="pagination-btn">← Zurück</a>
                <?php endif; ?>
                <div class="pagination-pages">
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="<?php echo htmlspecialchars(theme_build_query_url('/speakers', $_GET, ['page' => (string) $i]), ENT_QUOTES, 'UTF-8'); ?>"
                           class="pagination-page <?php echo $i === $page ? 'is-current' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
                <?php if ($page < $totalPages): ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/speakers', $_GET, ['page' => (string) ($page + 1)]), ENT_QUOTES, 'UTF-8'); ?>" class="pagination-btn">Weiter →</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>
