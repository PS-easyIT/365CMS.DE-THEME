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

    <?php include __DIR__ . '/partials/search-archive-panel.php'; ?>

    <?php if (!empty($results)): ?>
    <section class="search-results-panel" data-anim data-anim-delay="2">
        <div class="search-results-list">
        <?php foreach ($results as $result): ?>
            <?php $r = is_object($result) ? (array) $result : (array) $result; ?>
            <?php include __DIR__ . '/partials/search-result-row.php'; ?>
        <?php endforeach; ?>
        </div>
    </section>

    <?php elseif (!empty($queryTrimmed)): ?>
    <?php $searchEmptyStateMode = 'query'; ?>
    <?php include __DIR__ . '/partials/search-empty-state.php'; ?>
    <?php else: ?>
    <?php $searchEmptyStateMode = 'idle'; ?>
    <?php include __DIR__ . '/partials/search-empty-state.php'; ?>
    <?php endif; ?>

</div><!-- /.container -->
