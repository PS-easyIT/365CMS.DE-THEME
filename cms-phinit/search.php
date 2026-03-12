<?php
/**
 * Suche Template – CMS Phinit Theme
 *
 * Wird vom Router via ThemeManager::render('search', [...]) aufgerufen.
 * Übergebene Variablen (via extract):
 *   $results  – Array von Suchergebnissen (Posts, Companies, Events, etc.)
 *   $query    – Suchbegriff
 *   $type     – Typ-Filter (leer = alle)
 *   $location – Orts-Filter
 *   $filter   – Zusatzfilter
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

$results  = isset($results)  ? (array)$results  : [];
$query    = isset($query)    ? (string)$query    : '';
$type     = isset($type)     ? (string)$type     : '';
$location = isset($location) ? (string)$location : '';
$filter   = isset($filter)   ? (string)$filter   : '';
?>

<div class="container page-shell page-shell--search">

    <!-- Search Header -->
    <div class="blog-page-header" data-anim>
        <h1>🔍 Suche</h1>
        <?php if (!empty($query)): ?>
        <p class="search-results-summary">
            <?php echo count($results); ?> Ergebnis<?php echo count($results) !== 1 ? 'se' : ''; ?>
            für „<strong><?php echo htmlspecialchars($query, ENT_QUOTES); ?></strong>"
        </p>
        <?php endif; ?>
    </div>

    <!-- Search Form -->
    <div class="search-form-section" data-anim data-anim-delay="1">
        <form method="GET" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search" class="search-inline-form">
            <div class="search-input-wrap">
                <input type="search" name="q" class="auth-input"
                       value="<?php echo htmlspecialchars($query, ENT_QUOTES); ?>"
                       placeholder="Suchbegriff eingeben…" aria-label="Suche"
                       autofocus>
                <button type="submit" class="btn btn-primary">🔍 Suchen</button>
            </div>
            <div class="search-filters">
                <select name="type" class="auth-input search-filter-select" aria-label="Typ filtern">
                    <option value="">Alle Typen</option>
                    <option value="post" <?php echo $type === 'post' ? 'selected' : ''; ?>>Beiträge</option>
                    <option value="page" <?php echo $type === 'page' ? 'selected' : ''; ?>>Seiten</option>
                    <option value="company" <?php echo $type === 'company' ? 'selected' : ''; ?>>Firmen</option>
                    <option value="event" <?php echo $type === 'event' ? 'selected' : ''; ?>>Events</option>
                    <option value="speakers" <?php echo $type === 'speakers' ? 'selected' : ''; ?>>Speakers</option>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($results)): ?>
    <div class="search-results" data-anim data-anim-delay="2">
        <?php foreach ($results as $result):
            $r = is_object($result) ? (array)$result : (array)$result;
            $rType      = $r['_type']       ?? 'post';
            $rTypeLabel = $r['_type_label'] ?? 'Beitrag';
            $rTitle     = $r['title']       ?? $r['name'] ?? 'Ohne Titel';
            $rExcerptSource = (string)($r['excerpt'] ?? $r['meta_description'] ?? $r['description'] ?? $r['content'] ?? '');
            $rExcerpt = function_exists('phinit_excerpt_plain_text')
                ? phinit_excerpt_plain_text($rExcerptSource)
                : strip_tags($rExcerptSource);
            $rSlug      = $r['slug']        ?? '';

            // URL-Logik je nach Typ
            if ($rType === 'post') {
                $rUrl = $siteUrl . '/blog/' . $rSlug;
            } elseif (!empty($rSlug)) {
                $rUrl = $siteUrl . '/' . $rSlug;
            } else {
                $rUrl = '#';
            }
        ?>
        <div class="search-result-item">
            <div class="search-result-type">
                <span class="badge badge-teal"><?php echo htmlspecialchars($rTypeLabel, ENT_QUOTES); ?></span>
            </div>
            <div class="search-result-body">
                <h3 class="search-result-title">
                    <a href="<?php echo htmlspecialchars($rUrl, ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($rTitle, ENT_QUOTES); ?>
                    </a>
                </h3>
                <?php if (!empty($rExcerpt)): ?>
                <p class="search-result-excerpt"><?php echo htmlspecialchars(mb_strimwidth($rExcerpt, 0, 200, '…'), ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($r['published_at'])): ?>
                <span class="search-result-date">📅 <?php echo htmlspecialchars(date('j. M Y', strtotime($r['published_at'])), ENT_QUOTES); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php elseif (!empty($query)): ?>
    <div class="search-empty-state" data-anim data-anim-delay="2">
        <p class="search-empty-state__icon">🔍</p>
        <h2 class="search-empty-state__title">Keine Ergebnisse</h2>
        <p class="search-empty-state__text">
            Für „<strong><?php echo htmlspecialchars($query, ENT_QUOTES); ?></strong>" wurden leider keine passenden Inhalte gefunden. Versuche einen anderen Suchbegriff.
        </p>
    </div>
    <?php else: ?>
    <div class="search-empty-state" data-anim data-anim-delay="2">
        <p class="search-empty-state__icon">🔎</p>
        <h2 class="search-empty-state__title">Suche starten</h2>
        <p class="search-empty-state__text">Gib einen Suchbegriff ein, um Artikel, Seiten und mehr zu finden.</p>
    </div>
    <?php endif; ?>

</div><!-- /.container -->
