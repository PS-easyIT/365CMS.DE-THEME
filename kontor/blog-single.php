<?php
declare(strict_types=1);

/**
 * Kontor – Beitrag
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $post (Objekt: title, content = gerendertes,
 * sanitiertes HTML, excerpt, published_at, author_name, category_id, category_name,
 * category_slug, featured_image, tags, tag_items).
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$post = isset($post) && is_array($post) ? (object) $post : ($post ?? null);
if (!is_object($post)) {
    require __DIR__ . '/404.php';
    return;
}

$title = trim((string) ($post->title ?? ''));
$content = (string) ($post->content ?? '');
$lead = kontor_excerpt($post, 320, false);
$published = (string) ($post->published_at ?? $post->created_at ?? '');
$authorName = trim((string) ($post->author_name ?? ''));
$categoryName = trim((string) ($post->category_name ?? ''));
$categorySlug = trim((string) ($post->category_slug ?? ''));
$image = kontor_media_url($post->featured_image ?? '');
$tags = kontor_post_tags($post);
$postId = (int) ($post->id ?? 0);
$showSidebar = kontor_flag('layout', 'page_sidebar', true);
$permalink = kontor_post_link($post);

$related = kontor_get_posts(['limit' => 3, 'category_id' => (int) ($post->category_id ?? 0), 'exclude' => [$postId]]);
if (count($related) < 3) {
    $related = array_merge($related, kontor_get_posts(['limit' => 3 - count($related), 'exclude' => array_merge([$postId], array_map(static fn(object $p): int => (int) ($p->id ?? 0), $related))]));
}
$recent = $showSidebar ? kontor_get_posts(['limit' => 4, 'exclude' => [$postId]]) : [];
?>

<header class="kt-pagehero kt-pagehero--article">
    <div class="kt-container kt-pagehero__narrow">
        <nav class="kt-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kontor_e(kontor_url('/')); ?>">Start</a></li>
                <li><a href="<?php echo kontor_e(kontor_url('/blog')); ?>">Aktuelles</a></li>
                <?php if ($categoryName !== '') : ?>
                    <li><a href="<?php echo kontor_e(kontor_archive_url('category', $categorySlug)); ?>"><?php echo kontor_e($categoryName); ?></a></li>
                <?php endif; ?>
            </ol>
        </nav>
        <?php if ($categoryName !== '') : ?>
            <p><a class="kt-tag kt-tag--link" href="<?php echo kontor_e(kontor_archive_url('category', $categorySlug)); ?>"><?php echo kontor_e($categoryName); ?></a></p>
        <?php endif; ?>
        <h1 class="kt-pagehero__title"><?php echo kontor_e($title); ?></h1>
        <?php if ($lead !== '') : ?>
            <p class="kt-pagehero__text"><?php echo kontor_e($lead); ?></p>
        <?php endif; ?>
        <p class="kt-article-meta">
            <?php if ($authorName !== '') : ?><span class="kt-article-meta__author"><span class="kt-avatar" aria-hidden="true"><?php echo kontor_e(kontor_initials($authorName)); ?></span><?php echo kontor_e($authorName); ?></span><?php endif; ?>
            <span><?php echo kontor_icon('clock'); ?><time datetime="<?php echo kontor_e(kontor_format_date($published, 'iso')); ?>"><?php echo kontor_e(kontor_format_date($published)); ?></time></span>
            <span><?php echo kontor_reading_time($content); ?> Min. Lesezeit</span>
        </p>
    </div>
</header>

<div class="kt-container kt-content<?php echo $showSidebar ? ' kt-content--sidebar' : ''; ?>">
    <article class="kt-content__main" aria-label="<?php echo kontor_e($title); ?>">
        <?php if ($image !== '') : ?>
            <figure class="kt-content__media">
                <img src="<?php echo kontor_e($image); ?>" alt="<?php echo kontor_e($title); ?>" width="1200" height="675" loading="eager" decoding="async" fetchpriority="high">
            </figure>
        <?php endif; ?>

        <div class="kt-prose entry-content">
            <?php echo $content; ?>
        </div>

        <footer class="kt-article-footer">
            <?php if ($tags !== []) : ?>
                <ul class="kt-tags" aria-label="Schlagwörter">
                    <?php foreach ($tags as $tagItem) : ?>
                        <li><a class="kt-tag kt-tag--link" href="<?php echo kontor_e($tagItem['url']); ?>">#<?php echo kontor_e($tagItem['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <p class="kt-share">
                <span>Beitrag teilen:</span>
                <a href="<?php echo kontor_e('https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($permalink)); ?>" target="_blank" rel="noopener noreferrer"><?php echo kontor_icon('linkedin'); ?><span class="kt-visually-hidden">Auf LinkedIn teilen</span></a>
                <a href="<?php echo kontor_e('https://www.xing.com/spi/shares/new?url=' . rawurlencode($permalink)); ?>" target="_blank" rel="noopener noreferrer"><?php echo kontor_icon('xing'); ?><span class="kt-visually-hidden">Auf XING teilen</span></a>
                <a href="<?php echo kontor_e('mailto:?subject=' . rawurlencode($title) . '&body=' . rawurlencode($permalink)); ?>"><?php echo kontor_icon('mail'); ?><span class="kt-visually-hidden">Per E-Mail teilen</span></a>
            </p>
        </footer>
    </article>

    <?php if ($showSidebar) : ?>
        <div class="kt-content__aside">
            <div class="kt-sticky">
                <?php kontor_contact_card(); ?>
                <?php if ($recent !== []) : ?>
                    <section class="kt-sidebox" aria-labelledby="kt-recent-title">
                        <h2 class="kt-sidebox__title" id="kt-recent-title">Neueste Beiträge</h2>
                        <ul class="kt-sidebox__list">
                            <?php foreach ($recent as $item) : ?>
                                <li>
                                    <a href="<?php echo kontor_e(kontor_post_link($item)); ?>"><?php echo kontor_e((string) ($item->title ?? '')); ?></a>
                                    <time datetime="<?php echo kontor_e(kontor_format_date($item->published_at ?? $item->created_at ?? '', 'iso')); ?>"><?php echo kontor_e(kontor_format_date($item->published_at ?? $item->created_at ?? '', 'short')); ?></time>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($related !== []) : ?>
    <section class="kt-section kt-section--sand" aria-labelledby="kt-related-title">
        <div class="kt-container">
            <header class="kt-section__head kt-section__head--row">
                <h2 class="kt-section__title" id="kt-related-title">Das könnte Sie auch interessieren</h2>
                <a class="kt-link-arrow" href="<?php echo kontor_e(kontor_url('/blog')); ?>">Alle Beiträge<?php echo kontor_icon('arrow'); ?></a>
            </header>
            <div class="kt-postgrid">
                <?php foreach ($related as $item) : ?>
                    <?php kontor_post_card($item, 'h3'); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
