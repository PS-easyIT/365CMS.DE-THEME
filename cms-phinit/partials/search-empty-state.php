<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : '';
$queryTrimmed = isset($queryTrimmed) ? (string) $queryTrimmed : '';
$searchEmptyStateMode = isset($searchEmptyStateMode) ? (string) $searchEmptyStateMode : 'idle';
?>
<?php if ($searchEmptyStateMode === 'query'): ?>
<section class="search-empty-state search-empty-state--query" data-anim data-anim-delay="2">
    <p class="search-empty-state__icon">🔍</p>
    <h2 class="search-empty-state__title">Keine Ergebnisse</h2>
    <p class="search-empty-state__text">
        Für „<strong><?php echo htmlspecialchars($queryTrimmed, ENT_QUOTES); ?></strong>“ wurden leider keine passenden Inhalte gefunden. Versuche einen allgemeineren Begriff oder nimm den Typ-Filter zurück.
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
