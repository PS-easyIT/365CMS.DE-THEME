<?php
/**
 * Search Results Template
 *
 * Erhält:
 *   $results - Array von Suchergebnissen (Seiten-Objekte)
 *   $query   - Suchbegriff (string)
 *
 * @package IT_Expert_Network_Theme
 * @var array  $results
 * @var string $query
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$results  = $results ?? [];
$query    = $query ?? '';
$type     = $type ?? '';
$location = $location ?? '';
$filter   = $filter ?? '';
$siteUrl  = SITE_URL;
$count    = count($results);
$homeUrl  = theme_safe_url($siteUrl . '/', $siteUrl . '/');
$searchUrl = theme_safe_url($siteUrl . '/search', $siteUrl . '/search');

// Typ-Badge-Mapping
$typeBadges = [
    'page'    => ['label' => 'Seite',   'class' => 'search-result-badge--page'],
    'expert'  => ['label' => 'Experte', 'class' => 'search-result-badge--expert'],
    'company' => ['label' => 'Firma',   'class' => 'search-result-badge--company'],
    'speaker' => ['label' => 'Speaker', 'class' => 'search-result-badge--speaker'],
    'event'   => ['label' => 'Event',   'class' => 'search-result-badge--event'],
];
?>

<main id="main" class="site-main" role="main">
    <div class="container">
        <div class="content-area content-area--spaced">

            <!-- Suchkopf -->
            <header class="search-results-header">
                <h1>
                    <?php if ($query && trim($query) !== '') : ?>
                        Suchergebnisse für: <em class="search-query-highlight">„<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>“</em>
                    <?php else : ?>
                        Suche
                    <?php endif; ?>
                </h1>
                <?php if ($query && trim($query) !== '') : ?>
                    <p class="search-results-count">
                        <?php echo $count; ?> Ergebnis<?php echo $count !== 1 ? 'se' : ''; ?> gefunden
                    </p>
                <?php endif; ?>
            </header>

            <!-- Suchformular -->
            <form action="<?php echo htmlspecialchars($searchUrl, ENT_QUOTES, 'UTF-8'); ?>" method="GET" class="search-form">
                  <input type="search"
                       name="q"
                       placeholder="Suche nach Seiten, Themen…"
                       value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>"
                       aria-label="Suchbegriff"
                      class="form-control search-form__input">
                <button type="submit" class="btn btn-primary">Suchen</button>
            </form>

            <!-- Ergebnisse -->
            <?php if ($count > 0) : ?>
                <div class="search-results-list">
                    <?php foreach ($results as $result) :
                        $resultTitle   = is_array($result) ? ($result['title'] ?? '') : ($result->title ?? '');
                        $resultSlug    = is_array($result) ? ($result['slug'] ?? '') : ($result->slug ?? '');
                        $resultExcerpt = is_array($result) ? ($result['meta_description'] ?? $result['content'] ?? '') : ($result->meta_description ?? $result->content ?? '');
                        $resultType    = sanitize_key((string) (is_array($result) ? ($result['_type'] ?? 'page') : ($result->_type ?? 'page')));
                        $resultSlug    = trim((string) $resultSlug, '/');
                        $resultSlug    = implode('/', array_map('rawurlencode', array_filter(explode('/', $resultSlug), static fn(string $segment): bool => $segment !== '')));
                        $resultUrl     = theme_safe_url($siteUrl . ($resultSlug !== '' ? '/' . $resultSlug : ''), $homeUrl);
                        $badge         = $typeBadges[$resultType] ?? $typeBadges['page'];

                        // Excerpt kürzen
                        if (mb_strlen(strip_tags($resultExcerpt)) > 200) {
                            $resultExcerpt = mb_substr(strip_tags($resultExcerpt), 0, 200) . '…';
                        } else {
                            $resultExcerpt = strip_tags($resultExcerpt);
                        }
                    ?>
                        <article class="search-result-item">
                            <h2 class="search-result-title">
                                <a href="<?php echo htmlspecialchars($resultUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($resultTitle, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                                <span class="search-result-badge <?php echo htmlspecialchars($badge['class'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($badge['label'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </h2>
                            <?php if ($resultExcerpt && trim($resultExcerpt) !== '') : ?>
                                <p class="search-result-excerpt">
                                    <?php echo htmlspecialchars($resultExcerpt, ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            <?php endif; ?>
                            <p class="search-result-meta">
                                <a href="<?php echo htmlspecialchars($resultUrl, ENT_QUOTES, 'UTF-8'); ?>" class="search-result-link">
                                    <?php echo htmlspecialchars($resultUrl, ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </p>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php else : ?>
                <div class="search-no-results">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                    <?php if ($query && trim($query) !== '') : ?>
                        <p>Keine Ergebnisse für „<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>“ gefunden.</p>
                        <p class="search-no-results-note">Versuche andere Suchbegriffe oder weniger Wörter.</p>
                    <?php else : ?>
                        <p>Gib einen Suchbegriff ein, um loszulegen.</p>
                    <?php endif; ?>
                    <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>"
                       class="btn btn-outline search-no-results-action">
                        Zur Startseite
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>
