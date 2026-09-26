<?php
declare(strict_types=1);

/**
 * Beitragsübersicht (Blog, Kategorie, Schlagwort)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $posts (Objekte), $currentPage, $totalPages; bei Archiven zusätzlich $category bzw. $tag.
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

$posts       = isset($posts) && is_array($posts) ? $posts : [];
$currentPage = max(1, (int) ($currentPage ?? 1));
$totalPages  = max(1, (int) ($totalPages ?? 1));
$archiveTitle = 'Beiträge';
if (isset($category) && is_array($category) && trim((string) ($category['name'] ?? '')) !== '') {
    $archiveTitle = (string) $category['name'];
} elseif (isset($tag) && is_array($tag) && trim((string) ($tag['name'] ?? '')) !== '') {
    $archiveTitle = '#' . (string) $tag['name'];
}
$requestPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
$pageUrl = static function (int $number) use ($requestPath): string {
    $params = array_filter($_GET, 'is_scalar');
    unset($params['p']);
    if ($number > 1) {
        $params['p'] = $number;
    }
    $queryString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    return $requestPath . ($queryString !== '' ? '?' . $queryString : '');
};
?>
<section class="biz-page-hero">
    <div class="biz-container">
        <h1><?php echo $safe($archiveTitle); ?></h1>
    </div>
</section>

<div class="biz-page-content">
    <div class="biz-container">
        <?php if ($posts !== []) : ?>
        <div class="biz-grid">
            <?php foreach ($posts as $item) :
                $item    = is_array($item) ? (object) $item : $item;
                $itemUrl = $safeUrl(theme_post_url($item), $siteUrl . '/blog');
                $itemTitle = (string) ($item->title ?? '');
                $itemText  = trim(strip_tags((string) ($item->excerpt ?? '')));
                $itemMeta  = $formatDate($item->published_at ?? $item->created_at ?? '');
            ?>
                <article class="biz-card">
                    <?php if ($itemMeta !== '') : ?>
                        <p class="biz-meta"><?php echo $safe($itemMeta); ?></p>
                    <?php endif; ?>
                    <h2 class="biz-card-title"><a href="<?php echo $safe($itemUrl); ?>"><?php echo $safe($itemTitle); ?></a></h2>
                    <?php if ($itemText !== '') : ?>
                        <p class="biz-card-text"><?php echo $safe(function_exists('mb_substr') ? mb_substr($itemText, 0, 220) : substr($itemText, 0, 220)); ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1) : ?>
            <nav class="cms-pagination" aria-label="Seitennavigation">
                <?php if ($currentPage > 1) : ?>
                    <a href="<?php echo $safe($pageUrl($currentPage - 1)); ?>" class="btn-biz btn-biz-outline" rel="prev">← Zurück</a>
                <?php endif; ?>
                <span>Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?></span>
                <?php if ($currentPage < $totalPages) : ?>
                    <a href="<?php echo $safe($pageUrl($currentPage + 1)); ?>" class="btn-biz btn-biz-outline" rel="next">Weiter →</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
        <?php else : ?>
            <p class="biz-prose-empty">Noch keine Beiträge vorhanden.</p>
        <?php endif; ?>
    </div>
</div>
