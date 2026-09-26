<?php
declare(strict_types=1);

/**
 * Suche
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $results (Arrays: _type, _type_label, slug, title, meta_description), $query (string).
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe    = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl = rtrim((string) SITE_URL, '/');
$safeUrl = static function (string $url, string $fallback = '') use ($siteUrl): string {
    $url = trim($url);
    if ($url === '' || preg_match('/[\x00-\x1F\x7F]/', $url) === 1) {
        return $fallback;
    }
    if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
        return $siteUrl . $url;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    return in_array($scheme, ['http', 'https'], true) && filter_var($url, FILTER_VALIDATE_URL) ? $url : $fallback;
};
$formatDate = static function (mixed $value): string {
    $ts = is_string($value) && $value !== '' ? strtotime($value) : false;

    return $ts !== false ? date('d.m.Y', $ts) : '';
};

$results   = isset($results) && is_array($results) ? $results : [];
$query     = trim(strip_tags((string) ($query ?? '')));
$query     = function_exists('mb_substr') ? mb_substr($query, 0, 120) : substr($query, 0, 120);
$searchUrl = $siteUrl . '/search';
?>
<main id="main" class="site-main tn-section" role="main">
    <div class="container">
        <?php if ($query !== '') : ?>
            <h1>Suchergebnisse für „<?php echo $safe($query); ?>“<?php if ($results !== []) : ?> (<?php echo count($results); ?>)<?php endif; ?></h1>
        <?php else : ?>
            <h1>Suche</h1>
        <?php endif; ?>
        <form role="search" method="get" action="<?php echo $safe($searchUrl); ?>" class="tn-search-form">
            <label for="cms-search-q" class="cms-visually-hidden">Suchbegriff</label>
            <input id="cms-search-q" type="search" name="q" value="<?php echo $safe($query); ?>" placeholder="Suchbegriff eingeben …" class="tn-input" maxlength="120">
            <button type="submit" class="btn btn-primary">Suchen</button>
        </form>
        <?php if ($results !== []) : ?>
        <div class="tech-grid">
            <?php foreach ($results as $item) :
                $item      = is_array($item) ? (object) $item : $item;
                $itemUrl   = $safeUrl(theme_search_result_url($item), $siteUrl . '/');
                $itemTitle = (string) ($item->title ?? $item->name ?? '');
                $itemText  = trim(strip_tags((string) ($item->meta_description ?? $item->excerpt ?? '')));
                $itemMeta  = (string) ($item->_type_label ?? '');
            ?>
                <article class="tech-card tn-post-card">
                    <?php if ($itemMeta !== '') : ?>
                        <p class="tn-post-card__date"><?php echo $safe($itemMeta); ?></p>
                    <?php endif; ?>
                    <h2 class="tn-post-card__title"><a href="<?php echo $safe($itemUrl); ?>"><?php echo $safe($itemTitle); ?></a></h2>
                    <?php if ($itemText !== '') : ?>
                        <p class="tech-card__meta"><?php echo $safe(function_exists('mb_substr') ? mb_substr($itemText, 0, 220) : substr($itemText, 0, 220)); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php elseif ($query !== '') : ?>
            <p class="tech-card tech-card--placeholder">Keine Treffer. Bitte versuchen Sie einen anderen Suchbegriff.</p>
        <?php endif; ?>
    </div>
</main>
