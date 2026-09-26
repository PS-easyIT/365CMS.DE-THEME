<?php
declare(strict_types=1);

/**
 * Beitrags-Detailseite
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $post (Objekt: title, content = bereits gerendertes, sanitiertes HTML, published_at, author_name, category_name, featured_image).
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

$post = isset($post) && is_array($post) ? (object) $post : ($post ?? null);
$blogUrl = $siteUrl . '/blog';

$postTitle   = is_object($post) ? trim((string) ($post->title ?? '')) : '';
$postContent = is_object($post) ? (string) ($post->content ?? '') : '';
$postDate    = is_object($post) ? $formatDate($post->published_at ?? $post->created_at ?? '') : '';
$postAuthor  = is_object($post) ? trim((string) ($post->author_name ?? '')) : '';
$postCat     = is_object($post) ? trim((string) ($post->category_name ?? '')) : '';
$postImage   = is_object($post) ? $safeUrl((string) ($post->featured_image ?? '')) : '';
$metaParts   = array_values(array_filter([$postDate, $postAuthor, $postCat], static fn(string $v): bool => $v !== ''));
?>
<main id="main" class="site-main tn-section" role="main">
    <div class="container container--narrow">
        <?php if (is_object($post)) : ?>
            <article class="page-content tech-card tn-prose-card">
                <?php if ($postTitle !== '') : ?>
                    <h1 class="page-title"><?php echo $safe($postTitle); ?></h1>
                <?php endif; ?>
                <?php if ($metaParts !== []) : ?>
                    <p class="cms-post-meta"><?php echo $safe(implode(' · ', $metaParts)); ?></p>
                <?php endif; ?>
                <?php if ($postImage !== '') : ?>
                    <figure class="cms-post-image"><img src="<?php echo $safe($postImage); ?>" alt="<?php echo $safe($postTitle); ?>" loading="lazy" decoding="async"></figure>
                <?php endif; ?>
                <?php if (trim($postContent) !== '') : ?>
                    <div class="page-body prose">
                        <?php echo $postContent; ?>
                    </div>
                <?php endif; ?>
                <p><a href="<?php echo $safe($blogUrl); ?>" class="btn btn-outline">← Alle Beiträge</a></p>
            </article>
        <?php else : ?>
            <div class="tech-card tech-card--placeholder">
                <p>Beitrag nicht gefunden.</p>
                <a href="<?php echo $safe($siteUrl . '/'); ?>" class="btn btn-primary">Zur Startseite</a>
            </div>
        <?php endif; ?>
    </div>
</main>
