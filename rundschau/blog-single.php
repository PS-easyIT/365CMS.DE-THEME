<?php
declare(strict_types=1);

/**
 * Rundschau – Artikel
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $post (Objekt: title, content = gerendertes,
 * sanitiertes HTML, excerpt, published_at, updated_at, author_id, author_name,
 * category_id, category_name, category_slug, featured_image, tags, tag_items).
 *
 * @package Rundschau_Theme
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
$teaser = rundschau_excerpt($post, 360, false);
$published = (string) ($post->published_at ?? $post->created_at ?? '');
$updated = (string) ($post->content_updated_at ?? $post->updated_at ?? '');
$publishedTs = rundschau_timestamp($published);
$updatedTs = rundschau_timestamp($updated);
$showUpdated = rundschau_flag('rs_article', 'show_updated', true) && $publishedTs !== null && $updatedTs !== null && $updatedTs - $publishedTs > 3600;
$authorName = trim((string) ($post->author_name ?? ''));
$authorUrl = rundschau_author_url((int) ($post->author_id ?? 0));
$categoryName = trim((string) ($post->category_name ?? ''));
$categorySlug = trim((string) ($post->category_slug ?? ''));
$image = rundschau_media_url($post->featured_image ?? '');
$tags = rundschau_post_tags($post);
$permalink = rundschau_post_link($post);
$showSidebar = rundschau_flag('layout', 'show_sidebar', true);
$postId = (int) ($post->id ?? 0);

$sameRessort = $showSidebar && (int) ($post->category_id ?? 0) > 0
    ? rundschau_get_posts(['limit' => 4, 'category_id' => (int) $post->category_id, 'exclude' => [$postId]])
    : [];
$popular = $showSidebar ? rundschau_popular_posts(5, rundschau_int('rs_home', 'popular_days', 30, 0, 365), [$postId]) : [];

$related = [];
if (rundschau_flag('rs_article', 'show_related', true)) {
    $related = rundschau_get_posts(['limit' => 4, 'category_id' => (int) ($post->category_id ?? 0), 'exclude' => array_merge([$postId], rundschau_ids($sameRessort)), 'with_content' => true]);
    if (count($related) < 4) {
        $related = array_merge($related, rundschau_get_posts(['limit' => 4 - count($related), 'exclude' => array_merge([$postId], rundschau_ids($related), rundschau_ids($sameRessort))]));
    }
}

$shareLinks = [
    ['label' => 'Per E-Mail teilen', 'icon' => 'mail', 'url' => 'mailto:?subject=' . rawurlencode($title) . '&body=' . rawurlencode($permalink)],
    ['label' => 'Per WhatsApp teilen', 'icon' => 'whatsapp', 'url' => 'https://wa.me/?text=' . rawurlencode($title . ' ' . $permalink)],
    ['label' => 'Auf LinkedIn teilen', 'icon' => 'linkedin', 'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($permalink)],
];
?>

<article class="rs-article <?php echo rundschau_e(rundschau_ressort_class($categorySlug)); ?>" aria-labelledby="rs-article-title">
    <div class="rs-container rs-layout<?php echo $showSidebar ? ' rs-layout--sidebar' : ' rs-layout--narrow'; ?>">
        <div class="rs-layout__main">
            <nav class="rs-breadcrumb" aria-label="Brotkrumen">
                <ol>
                    <li><a href="<?php echo rundschau_e(rundschau_url('/')); ?>">Startseite</a></li>
                    <?php if ($categoryName !== '') : ?>
                        <li><a href="<?php echo rundschau_e(rundschau_archive_url('category', $categorySlug)); ?>"><?php echo rundschau_e($categoryName); ?></a></li>
                    <?php else : ?>
                        <li><a href="<?php echo rundschau_e(rundschau_url('/blog')); ?>">Meldungen</a></li>
                    <?php endif; ?>
                </ol>
            </nav>

            <header class="rs-article__head">
                <?php if ($categoryName !== '') : ?>
                    <?php echo rundschau_kicker($post); ?>
                <?php endif; ?>
                <h1 class="rs-article__title" id="rs-article-title"><?php echo rundschau_e($title); ?></h1>
                <?php if ($teaser !== '') : ?>
                    <p class="rs-article__teaser"><?php echo rundschau_e($teaser); ?></p>
                <?php endif; ?>

                <div class="rs-article__meta">
                    <p class="rs-byline">
                        <span class="rs-byline__avatar" aria-hidden="true"><?php echo rundschau_e(rundschau_initials($authorName !== '' ? $authorName : 'Redaktion')); ?></span>
                        <span class="rs-byline__text">
                            <?php if ($authorName !== '') : ?>
                                <span class="rs-byline__author">Von <?php if ($authorUrl !== '') : ?><a href="<?php echo rundschau_e($authorUrl); ?>" rel="author"><?php echo rundschau_e($authorName); ?></a><?php else : ?><?php echo rundschau_e($authorName); ?><?php endif; ?></span>
                            <?php endif; ?>
                            <span class="rs-byline__date">
                                <time datetime="<?php echo rundschau_e(rundschau_format_date($published, 'iso')); ?>"><?php echo rundschau_e(rundschau_format_date($published, 'datetime')); ?></time>
                                <?php if ($showUpdated) : ?>
                                    · aktualisiert <time datetime="<?php echo rundschau_e(rundschau_format_date($updated, 'iso')); ?>"><?php echo rundschau_e(rundschau_format_date($updated, 'datetime')); ?></time>
                                <?php endif; ?>
                                <?php if (rundschau_flag('rs_article', 'show_reading_time', true)) : ?>
                                    · <?php echo rundschau_reading_time($content); ?> Min. Lesezeit
                                <?php endif; ?>
                            </span>
                        </span>
                    </p>

                    <?php if (rundschau_flag('rs_article', 'show_share', true)) : ?>
                        <ul class="rs-share" aria-label="Artikel teilen">
                            <li><button type="button" class="rs-share__btn" data-rs-copy="<?php echo rundschau_e($permalink); ?>"><?php echo rundschau_icon('link'); ?><span class="rs-visually-hidden" data-rs-copy-label>Link kopieren</span></button></li>
                            <?php foreach ($shareLinks as $share) : ?>
                                <li><a class="rs-share__btn" href="<?php echo rundschau_e($share['url']); ?>"<?php echo str_starts_with($share['url'], 'http') ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo rundschau_icon($share['icon']); ?><span class="rs-visually-hidden"><?php echo rundschau_e($share['label']); ?></span></a></li>
                            <?php endforeach; ?>
                            <li><button type="button" class="rs-share__btn" data-rs-print><?php echo rundschau_icon('print'); ?><span class="rs-visually-hidden">Artikel drucken</span></button></li>
                        </ul>
                    <?php endif; ?>
                </div>
                <p class="rs-copy-status rs-visually-hidden" role="status" data-rs-copy-status></p>
            </header>

            <?php if ($image !== '') : ?>
                <figure class="rs-article__media">
                    <img src="<?php echo rundschau_e($image); ?>" alt="<?php echo rundschau_e($title); ?>" width="1200" height="675" loading="eager" decoding="async" fetchpriority="high">
                </figure>
            <?php endif; ?>

            <div class="rs-prose entry-content">
                <?php echo $content; ?>
            </div>

            <?php if ($tags !== []) : ?>
                <div class="rs-article__tags">
                    <h2 class="rs-article__tags-title">Themen</h2>
                    <ul class="rs-chips">
                        <?php foreach ($tags as $tagItem) : ?>
                            <li><a class="rs-chip" href="<?php echo rundschau_e($tagItem['url']); ?>"><?php echo rundschau_e($tagItem['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($showSidebar) : ?>
            <aside class="rs-layout__aside" aria-label="Seitenleiste">
                <div class="rs-sticky">
                    <?php if ($sameRessort !== []) : ?>
                        <section class="rs-box" aria-labelledby="rs-aside-ressort">
                            <h2 class="rs-box__title" id="rs-aside-ressort">Mehr aus <?php echo rundschau_e($categoryName !== '' ? $categoryName : 'diesem Ressort'); ?></h2>
                            <ul class="rs-plainlist">
                                <?php foreach ($sameRessort as $item) : ?>
                                    <li><?php rundschau_teaser($item, 'compact', 'h3'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>
                    <?php if ($popular !== []) : ?>
                        <section class="rs-box rs-box--popular" aria-labelledby="rs-aside-popular">
                            <h2 class="rs-box__title" id="rs-aside-popular"><?php echo rundschau_e(rundschau_text('rs_home', 'popular_heading', 'Meistgelesen')); ?></h2>
                            <ol class="rs-popular">
                                <?php foreach ($popular as $item) : ?>
                                    <li class="rs-popular__item"><a href="<?php echo rundschau_e(rundschau_post_link($item)); ?>"><?php echo rundschau_e((string) ($item->title ?? '')); ?></a></li>
                                <?php endforeach; ?>
                            </ol>
                        </section>
                    <?php endif; ?>
                </div>
            </aside>
        <?php endif; ?>
    </div>
</article>

<?php if ($related !== []) : ?>
    <section class="rs-related" aria-labelledby="rs-related-title">
        <div class="rs-container">
            <h2 class="rs-section-title" id="rs-related-title"><span><?php echo rundschau_e(rundschau_text('rs_article', 'related_heading', 'Mehr zum Thema')); ?></span></h2>
            <div class="rs-grid rs-grid--4">
                <?php foreach ($related as $item) : ?>
                    <?php rundschau_teaser($item, 'card', 'h3'); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
