<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Fallback-Template (z. B. Blog, Kategorie, Schlagwort): Der Core übergibt $posts (Objekte)
// sowie $currentPage/$totalPages; bei Archiven zusätzlich $category bzw. $tag.
$posts       = isset($posts) && is_array($posts) ? $posts : [];
$currentPage = max(1, (int) ($currentPage ?? 1));
$totalPages  = max(1, (int) ($totalPages ?? 1));
$safe        = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl     = academy365_safe_url(rtrim((string) SITE_URL, '/') . '/', '/');

$archiveTitle = 'Beiträge';
if (isset($category) && is_array($category) && trim((string) ($category['name'] ?? '')) !== '') {
    $archiveTitle = (string) $category['name'];
} elseif (isset($tag) && is_array($tag) && trim((string) ($tag['name'] ?? '')) !== '') {
    $archiveTitle = '#' . (string) $tag['name'];
}

$requestPath = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
$pageUrl = static function (int $number) use ($requestPath): string {
    $query = $_GET;
    unset($query['p']);
    if ($number > 1) {
        $query['p'] = $number;
    }
    $queryString = http_build_query(array_filter($query, 'is_scalar'), '', '&', PHP_QUERY_RFC3986);

    return $requestPath . ($queryString !== '' ? '?' . $queryString : '');
};
?>
<main id="main" class="ac-main-content ac-archive-content" role="main">
    <div class="ac-container">
        <header class="ac-archive-header">
            <h1 class="ac-archive-title"><?php echo $safe($archiveTitle); ?></h1>
        </header>
        <?php if ($posts !== []) : ?>
            <div class="ac-courses-grid">
                <?php foreach ($posts as $post) :
                    $post      = is_array($post) ? (object) $post : $post;
                    $postUrl   = academy365_safe_url(theme_post_url($post), $siteUrl);
                    $postTitle = (string) ($post->title ?? '');
                    $excerpt   = trim(strip_tags((string) ($post->excerpt ?? '')));
                ?>
                    <article class="ac-card">
                        <div class="ac-card-thumb"></div>
                        <div class="ac-card-body">
                            <h2 class="ac-card-title">
                                <a href="<?php echo $safe($postUrl); ?>"><?php echo $safe($postTitle); ?></a>
                            </h2>
                            <?php if ($excerpt !== '') : ?>
                                <div class="ac-card-excerpt"><?php echo $safe($excerpt); ?></div>
                            <?php endif; ?>
                            <a href="<?php echo $safe($postUrl); ?>" class="ac-btn ac-btn-secondary ac-btn-sm">Weiterlesen</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($totalPages > 1) : ?>
                <nav class="ac-pagination" aria-label="Seitennavigation">
                    <?php if ($currentPage > 1) : ?>
                        <a href="<?php echo $safe($pageUrl($currentPage - 1)); ?>" class="ac-btn ac-btn-ghost" rel="prev">← Zurück</a>
                    <?php endif; ?>
                    <span>Seite <?php echo $currentPage; ?> von <?php echo $totalPages; ?></span>
                    <?php if ($currentPage < $totalPages) : ?>
                        <a href="<?php echo $safe($pageUrl($currentPage + 1)); ?>" class="ac-btn ac-btn-ghost" rel="next">Weiter →</a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php else : ?>
            <div class="ac-empty-state">
                <span class="ac-empty-emoji" aria-hidden="true">📚</span>
                <p>Noch keine Beiträge vorhanden.</p>
                <a href="<?php echo $safe($siteUrl); ?>" class="ac-btn ac-btn-primary">Zurück zur Startseite</a>
            </div>
        <?php endif; ?>
    </div>
</main>
