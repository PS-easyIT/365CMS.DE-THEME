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

$queryTrimmed = trim($query);
$resultCount = count($results);
$querySummary = $queryTrimmed !== '' ? '„' . $queryTrimmed . '“' : 'Noch kein Begriff';
$typeLabels = [
    'post' => 'Beiträge',
    'page' => 'Seiten',
    'company' => 'Firmen',
    'event' => 'Events',
    'speakers' => 'Speakers',
];
$typeIcons = [
    'post' => '📝',
    'page' => '📄',
    'company' => '🏢',
    'event' => '📅',
    'speakers' => '🎤',
];
$activeFilters = [];
if ($type !== '') {
    $activeFilters[] = $typeLabels[$type] ?? ucfirst($type);
}
if ($location !== '') {
    $activeFilters[] = 'Ort: ' . $location;
}
if ($filter !== '') {
    $activeFilters[] = 'Filter: ' . $filter;
}

try {
    $_searchCustomizer = \CMS\Services\ThemeCustomizer::instance();
    $_searchExcerptLen = max(10, (int) $_searchCustomizer->get('typography', 'article_excerpt_length', 180));
} catch (\Throwable $_searchE) {
    $_searchExcerptLen = 180;
}
?>

<div class="container blog-shell search-shell search-shell--archive">

    <section class="search-archive-top" data-anim>
        <div class="search-archive-panel">
            <form method="GET" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search" class="search-archive-form">
                <div class="search-archive-form__row">
                    <label class="search-field" for="search-query">
                        <span class="search-field__icon" aria-hidden="true">🔎</span>
                        <input type="search" id="search-query" name="q"
                               value="<?php echo htmlspecialchars($query, ENT_QUOTES); ?>"
                               placeholder="Inhalte durchsuchen…" aria-label="Suche"
                               autofocus>
                    </label>

                    <button type="submit" class="btn btn-primary search-submit-btn">Suchen</button>

                    <select name="type" class="search-select" aria-label="Typ filtern">
                        <option value="">Alle Typen</option>
                        <option value="post" <?php echo $type === 'post' ? 'selected' : ''; ?>>Beiträge</option>
                        <option value="page" <?php echo $type === 'page' ? 'selected' : ''; ?>>Seiten</option>
                        <option value="company" <?php echo $type === 'company' ? 'selected' : ''; ?>>Firmen</option>
                        <option value="event" <?php echo $type === 'event' ? 'selected' : ''; ?>>Events</option>
                        <option value="speakers" <?php echo $type === 'speakers' ? 'selected' : ''; ?>>Speakers</option>
                    </select>

                    <?php if ($queryTrimmed !== '' || $type !== ''): ?>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search" class="search-reset-link">Zurücksetzen</a>
                    <?php endif; ?>
                </div>

                <div class="search-archive-summary" aria-label="Suchstatistiken">
                    <article class="search-summary-card">
                        <span class="search-summary-card__label">Treffer</span>
                        <strong class="search-summary-card__value"><?php echo (int) $resultCount; ?></strong>
                    </article>
                    <article class="search-summary-card">
                        <span class="search-summary-card__label">Gesucht nach</span>
                        <strong class="search-summary-card__value search-summary-card__value--query"><?php echo htmlspecialchars($querySummary, ENT_QUOTES); ?></strong>
                    </article>
                </div>

                <?php if ($queryTrimmed !== '' || !empty($activeFilters)): ?>
                <div class="search-active-filters">
                    <?php if ($queryTrimmed !== ''): ?>
                    <span class="search-chip search-chip--query">„<?php echo htmlspecialchars($queryTrimmed, ENT_QUOTES); ?>“</span>
                    <?php endif; ?>
                    <?php foreach ($activeFilters as $activeFilter): ?>
                    <span class="search-chip"><?php echo htmlspecialchars($activeFilter, ENT_QUOTES); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <?php if (!empty($results)): ?>
    <section class="search-results-panel" data-anim data-anim-delay="2">
        <div class="search-results-list">
        <?php foreach ($results as $result):
            $r = is_object($result) ? (array)$result : (array)$result;
            $rType      = $r['_type']       ?? 'post';
            $rTypeLabel = $r['_type_label'] ?? 'Beitrag';
            $rTypeIcon  = $typeIcons[$rType] ?? '📄';
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

            $rPath = $rUrl !== '#' ? (string) (parse_url($rUrl, PHP_URL_PATH) ?: '/') : 'Nicht verlinkt';
        ?>
        <article class="search-result-row search-result-row--<?php echo htmlspecialchars($rType, ENT_QUOTES); ?>">
            <div class="search-result-row__icon" aria-hidden="true"><?php echo htmlspecialchars($rTypeIcon, ENT_QUOTES); ?></div>
            <div class="search-result-body">
                <div class="search-result-topline">
                    <span class="search-result-type search-result-type--<?php echo htmlspecialchars($rType, ENT_QUOTES); ?>"><?php echo htmlspecialchars($rTypeLabel, ENT_QUOTES); ?></span>
                    <?php if (!empty($r['published_at'])): ?>
                    <time class="search-result-date" datetime="<?php echo htmlspecialchars((string) $r['published_at'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(date('d.m.Y', strtotime((string) $r['published_at'])), ENT_QUOTES); ?></time>
                    <?php endif; ?>
                </div>

                <h3 class="search-result-title">
                    <a href="<?php echo htmlspecialchars($rUrl, ENT_QUOTES); ?>">
                        <?php echo htmlspecialchars($rTitle, ENT_QUOTES); ?>
                    </a>
                </h3>

                <?php if (!empty($rExcerpt)): ?>
                <p class="search-result-excerpt"><?php echo htmlspecialchars(mb_strimwidth($rExcerpt, 0, $_searchExcerptLen, '…'), ENT_QUOTES); ?></p>
                <?php endif; ?>

                <div class="search-result-footer">
                    <span class="search-result-path"><?php echo htmlspecialchars($rPath, ENT_QUOTES); ?></span>
                    <?php if ($rUrl !== '#'): ?>
                    <a class="search-result-cta" href="<?php echo htmlspecialchars($rUrl, ENT_QUOTES); ?>">Ansehen →</a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
        </div>
    </section>

    <?php elseif (!empty($queryTrimmed)): ?>
    <section class="search-empty-state search-empty-state--query" data-anim data-anim-delay="2">
        <p class="search-empty-state__icon">🔍</p>
        <h2 class="search-empty-state__title">Keine Ergebnisse</h2>
        <p class="search-empty-state__text">
            Für „<strong><?php echo htmlspecialchars($queryTrimmed, ENT_QUOTES); ?></strong>" wurden leider keine passenden Inhalte gefunden. Versuche einen allgemeineren Begriff oder nimm den Typ-Filter zurück.
        </p>
        <ul class="search-empty-state__tips">
            <li>Nutze kürzere oder allgemeinere Begriffe.</li>
            <li>Teste ohne Typ-Filter, falls einer aktiv ist.</li>
            <li>Prüfe alternative Schreibweisen oder Synonyme.</li>
        </ul>
        <div class="search-empty-state__actions">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search" class="btn btn-outline">Neue Suche starten</a>
        </div>
    </section>
    <?php else: ?>
    <section class="search-empty-state search-empty-state--idle" data-anim data-anim-delay="2">
        <p class="search-empty-state__icon">🔎</p>
        <h2 class="search-empty-state__title">Suche starten</h2>
        <p class="search-empty-state__text">Gib einen Suchbegriff ein, um Artikel, Seiten und weitere Inhalte in einer kompakten Ergebnisübersicht zu finden.</p>
        <div class="search-empty-state__actions">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog" class="btn btn-outline">Zum Blog</a>
        </div>
    </section>
    <?php endif; ?>

</div><!-- /.container -->
