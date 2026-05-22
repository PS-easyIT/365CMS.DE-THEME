<?php
/**
 * Suchergebnisse – MedCare Pro Theme
 *
 * Erwartet: $results (array), $query (string), $total (int), $currentPage (int), $totalPages (int)
 *
 * @package MedCarePro_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe    = static fn(mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

$rawQuery = trim((string) ($_GET['q'] ?? $query ?? ''));
$query    = strip_tags($rawQuery);
$query    = function_exists('mb_substr') ? mb_substr($query, 0, 120) : substr($query, 0, 120);
$queryEsc = $safe($query);
$page     = max(1, (int) ($_GET['page'] ?? $currentPage ?? 1));

$searchUrl  = $safe(theme_route_url('search'));
$doctorsUrl = $safe(theme_route_url('doctors'));

$normalizeResultUrl = static function (mixed $value, string $fallback): string {
    $url = trim((string) $value);
    if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url) === 1 || preg_match('#^javascript:#i', $url) === 1) {
        return htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8');
    }
    if (str_starts_with($url, '//')) {
        return htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8');
    }
    if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $url) === 1 && filter_var($url, FILTER_VALIDATE_URL) === false) {
        return htmlspecialchars($fallback, ENT_QUOTES, 'UTF-8');
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
};

if (empty($results)) {
    try {
        $results    = [];
        $total      = 0;
        $totalPages = 1;
        if ($query !== '') {
            $results    = \CMS\Services\PostService::search($query, ['per_page' => 12, 'page' => $page]);
            $total      = \CMS\Services\PostService::searchCount($query);
            $totalPages = (int) ceil($total / 12);
        }
    } catch (\Throwable) {
        $results = [];
        $total = 0;
        $totalPages = 1;
    }
}
?>
<main id="main" class="mc-main mc-search-page" role="main">
    <div class="mc-container">

        <div class="mc-card mc-search-form-card">
            <form role="search" method="get" action="<?php echo $searchUrl; ?>" class="mc-search-form" aria-label="Suche">
                <label for="search-input" class="mc-visually-hidden">Suchbegriff eingeben</label>
                <input id="search-input"
                       type="search"
                       name="q"
                       value="<?php echo $queryEsc; ?>"
                       placeholder="Arzt, Fachgebiet, Diagnose …"
                       class="mc-input mc-search-form__input"
                       autofocus>
                <button type="submit" class="mc-btn mc-btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         focusable="false" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                    Suchen
                </button>
            </form>
        </div>

        <?php if ($query !== '') : ?>
        <h1 id="search-results-heading" class="mc-search-results__heading">
            Suchergebnisse für
            <em class="mc-search-results__term">&ldquo;<?php echo $queryEsc; ?>&rdquo;</em>
            <?php if (($total ?? 0) > 0) : ?>
                <span class="mc-search-results__count">(<?php echo (int) $total; ?> Treffer)</span>
            <?php endif; ?>
        </h1>

        <?php if (!empty($results)) : ?>
        <div class="mc-search-results__list" aria-live="polite">
            <?php foreach ($results as $r) :
                $fallbackUrl = mc_href('/' . rawurlencode((string) ($r->slug ?? $r->id ?? '')));
                $url     = $normalizeResultUrl($r->url ?? $fallbackUrl, $fallbackUrl);
                $title   = $safe((string) ($r->title ?? ''));
                $excerpt = $safe((string) ($r->excerpt ?? ''));
                $type    = (string) ($r->type ?? '');
                $typeEsc = $safe($type);
                $date    = isset($r->created_at) ? date('d.m.Y', strtotime((string) $r->created_at)) : '';
                $imgUrl  = $safe((string) ($r->thumbnail_url ?? ''));
            ?>
            <article class="mc-search-result">
                <?php if ($imgUrl !== '') : ?>
                <img src="<?php echo $imgUrl; ?>" alt="" aria-hidden="true" class="mc-search-result__avatar">
                <?php else : ?>
                <div class="mc-search-result__avatar mc-search-result__avatar--placeholder" aria-hidden="true">
                    <?php echo ($type === 'doctor') ? '👨‍⚕' : '📄'; ?>
                </div>
                <?php endif; ?>
                <div class="mc-search-result__body">
                    <h2 class="mc-search-result__name">
                        <a href="<?php echo $url; ?>"><?php echo $title; ?></a>
                    </h2>
                    <?php if ($excerpt !== '') : ?>
                    <p class="mc-search-result__excerpt"><?php echo $excerpt; ?></p>
                    <?php endif; ?>
                    <div class="mc-search-result__meta">
                        <?php if ($typeEsc !== '') : ?>
                        <span class="mc-specialty-badge"><?php echo $type === 'doctor' ? 'Arzt' : 'Artikel'; ?></span>
                        <?php endif; ?>
                        <?php if ($date !== '') : ?>
                        <span class="mc-search-result__date"><?php echo $safe($date); ?></span>
                        <?php endif; ?>
                        <a href="<?php echo $url; ?>" class="mc-btn mc-btn-outline mc-btn-sm mc-search-result__action">
                            Anzeigen →
                        </a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php else : ?>
        <div class="mc-card mc-empty-state">
            <div class="mc-empty-state__icon" aria-hidden="true">🔍</div>
            <h2 class="mc-empty-state__title">Kein Ergebnis gefunden</h2>
            <p class="mc-empty-state__text">
                Zu <strong><?php echo $queryEsc; ?></strong> wurde nichts gefunden. Bitte versuchen Sie andere Suchbegriffe oder durchsuchen Sie direkt unsere Ärzteliste.
            </p>
            <a href="<?php echo $doctorsUrl; ?>" class="mc-btn mc-btn-primary mc-empty-state__action">Arzt suchen</a>
        </div>
        <?php endif; ?>

        <?php if (($totalPages ?? 1) > 1) : ?>
        <nav class="mc-pagination" aria-label="Seitennavigation">
            <?php if ($page > 1) : ?>
            <a href="<?php echo $searchUrl; ?>?q=<?php echo rawurlencode($query); ?>&amp;page=<?php echo $page - 1; ?>" class="mc-btn mc-btn-outline mc-btn-sm" rel="prev">← Zurück</a>
            <?php endif; ?>
            <span class="mc-pagination__status">Seite <?php echo (int) $page; ?> von <?php echo (int) $totalPages; ?></span>
            <?php if ($page < $totalPages) : ?>
            <a href="<?php echo $searchUrl; ?>?q=<?php echo rawurlencode($query); ?>&amp;page=<?php echo $page + 1; ?>" class="mc-btn mc-btn-outline mc-btn-sm" rel="next">Weiter →</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php else : ?>
        <div class="mc-section-header">
            <h1 id="search-results-heading" class="mc-search-results__heading">
                <span class="mc-search-results__title-eyebrow">Arzt &amp; Ratgeber-Suche</span>
            </h1>
            <p>Geben Sie einen Suchbegriff ein, um Ärzte, Fachgebiete oder Gesundheitsartikel zu finden.</p>
        </div>
        <?php endif; ?>

    </div>
</main>
<?php get_footer(); ?>
