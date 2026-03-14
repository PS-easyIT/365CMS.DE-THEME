<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : '';
$query = isset($query) ? (string) $query : '';
$queryTrimmed = isset($queryTrimmed) ? (string) $queryTrimmed : trim($query);
$type = isset($type) ? (string) $type : '';
$resultCount = isset($resultCount) ? (int) $resultCount : 0;
$querySummary = isset($querySummary) ? (string) $querySummary : 'Noch kein Begriff';
$activeFilters = isset($activeFilters) && is_array($activeFilters) ? $activeFilters : [];
?>
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
                <span class="search-chip"><?php echo htmlspecialchars((string) $activeFilter, ENT_QUOTES); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
</section>
