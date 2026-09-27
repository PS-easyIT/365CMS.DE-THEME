<?php
declare(strict_types=1);

/**
 * Feder – Beitrag
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $post (Objekt: title, content = gerendertes,
 * sanitiertes HTML, excerpt, published_at, updated_at, author_id, author_name,
 * category_id, category_name, category_slug, featured_image, tags, tag_items).
 *
 * @package Feder_Theme
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
$excerpt = feder_excerpt($post, 320, false);
$published = (string) ($post->published_at ?? $post->created_at ?? '');
$updated = (string) ($post->content_updated_at ?? $post->updated_at ?? '');
$showUpdated = $updated !== '' && $published !== '' && strtotime($updated) !== false && strtotime($published) !== false
    && strtotime($updated) - strtotime($published) > 86400;
$authorName = trim((string) ($post->author_name ?? ''));
$authorUrl = feder_author_url((int) ($post->author_id ?? 0));
$categoryName = trim((string) ($post->category_name ?? ''));
$categorySlug = trim((string) ($post->category_slug ?? ''));
$image = feder_media_url($post->featured_image ?? '');
$tags = feder_post_tags($post);
$permalink = feder_post_link($post);

$showReadingTime = feder_flag('feder_article', 'show_reading_time', true);
$showShare = feder_flag('feder_article', 'show_share', true);
$showAuthorBox = feder_flag('feder_article', 'show_author_box', true);
$showNav = feder_flag('feder_article', 'show_post_navigation', true);
$showRelated = feder_flag('feder_article', 'show_related', true);
$dropcap = feder_flag('typography', 'enable_dropcap', true);

$adjacent = $showNav ? feder_adjacent_posts($post) : ['prev' => null, 'next' => null];
$related = $showRelated ? feder_related_posts($post, 3) : [];

$boxName = feder_text('feder_author', 'author_name') ?: ($authorName !== '' ? $authorName : feder_site_title());
$boxBio = feder_text('feder_author', 'author_bio');
$boxImage = feder_media_url(feder_text('feder_author', 'author_image'));
$boxUrl = feder_safe_url(feder_text('feder_author', 'author_url', '/ueber-uns'));

$shareLinks = [
    ['label' => 'Per E-Mail teilen', 'icon' => 'mail', 'url' => 'mailto:?subject=' . rawurlencode($title) . '&body=' . rawurlencode($permalink)],
    ['label' => 'Auf LinkedIn teilen', 'icon' => 'linkedin', 'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($permalink)],
    ['label' => 'Auf Bluesky teilen', 'icon' => 'bluesky', 'url' => 'https://bsky.app/intent/compose?text=' . rawurlencode($title . ' ' . $permalink)],
];
?>

<article class="fd-article" aria-labelledby="fd-article-title">
    <header class="fd-article__header fd-measure">
        <?php if ($categoryName !== '') : ?>
            <p class="fd-kicker"><a href="<?php echo feder_e(feder_archive_url('category', $categorySlug)); ?>"><?php echo feder_e($categoryName); ?></a></p>
        <?php endif; ?>
        <h1 class="fd-article__title" id="fd-article-title"><?php echo feder_e($title); ?></h1>
        <?php if ($excerpt !== '') : ?>
            <p class="fd-article__dek"><?php echo feder_e($excerpt); ?></p>
        <?php endif; ?>
        <div class="fd-byline">
            <span class="fd-byline__avatar" aria-hidden="true"><?php echo feder_e(feder_initials($authorName !== '' ? $authorName : feder_site_title())); ?></span>
            <p class="fd-byline__text">
                <?php if ($authorName !== '') : ?>
                    <span>von <?php if ($authorUrl !== '') : ?><a href="<?php echo feder_e($authorUrl); ?>" rel="author"><?php echo feder_e($authorName); ?></a><?php else : ?><?php echo feder_e($authorName); ?><?php endif; ?></span>
                <?php endif; ?>
                <span class="fd-byline__meta">
                    <time datetime="<?php echo feder_e(feder_format_date($published, 'iso')); ?>"><?php echo feder_e(feder_format_date($published)); ?></time>
                    <?php if ($showUpdated) : ?>
                        <span class="fd-dot" aria-hidden="true">·</span><span>aktualisiert am <time datetime="<?php echo feder_e(feder_format_date($updated, 'iso')); ?>"><?php echo feder_e(feder_format_date($updated)); ?></time></span>
                    <?php endif; ?>
                    <?php if ($showReadingTime) : ?>
                        <span class="fd-dot" aria-hidden="true">·</span><span><?php echo feder_reading_time($content); ?> Min. Lesezeit</span>
                    <?php endif; ?>
                </span>
            </p>
        </div>
    </header>

    <?php if ($image !== '') : ?>
        <figure class="fd-article__media fd-wide">
            <img src="<?php echo feder_e($image); ?>" alt="<?php echo feder_e($title); ?>" width="1600" height="900" loading="eager" decoding="async" fetchpriority="high">
        </figure>
    <?php endif; ?>

    <div class="fd-prose entry-content fd-measure<?php echo $dropcap ? ' fd-prose--dropcap' : ''; ?>" data-fd-article-body>
        <?php echo $content; ?>
    </div>

    <footer class="fd-article__footer fd-measure">
        <?php if ($tags !== []) : ?>
            <ul class="fd-tags" aria-label="Schlagwörter">
                <?php foreach ($tags as $tagItem) : ?>
                    <li><a class="fd-chip fd-chip--small" href="<?php echo feder_e($tagItem['url']); ?>">#<?php echo feder_e($tagItem['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($showShare) : ?>
            <div class="fd-share">
                <p class="fd-share__label"><?php echo feder_icon('share'); ?><span>Teilen</span></p>
                <ul class="fd-share__list">
                    <li><button type="button" class="fd-share__button" data-fd-copy="<?php echo feder_e($permalink); ?>"><?php echo feder_icon('link'); ?><span data-fd-copy-label>Link kopieren</span></button></li>
                    <?php foreach ($shareLinks as $share) : ?>
                        <li><a class="fd-share__button" href="<?php echo feder_e($share['url']); ?>"<?php echo str_starts_with($share['url'], 'http') ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo feder_icon($share['icon']); ?><span class="fd-visually-hidden"><?php echo feder_e($share['label']); ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($showAuthorBox) : ?>
            <aside class="fd-authorbox" aria-label="Über die Autorin / den Autor">
                <div class="fd-authorbox__portrait" aria-hidden="true">
                    <?php if ($boxImage !== '') : ?>
                        <img src="<?php echo feder_e($boxImage); ?>" alt="" width="72" height="72" loading="lazy" decoding="async">
                    <?php else : ?>
                        <span><?php echo feder_e(feder_initials($boxName)); ?></span>
                    <?php endif; ?>
                </div>
                <div class="fd-authorbox__body">
                    <p class="fd-authorbox__name"><?php echo feder_e($boxName); ?></p>
                    <?php if ($boxBio !== '') : ?>
                        <p class="fd-authorbox__bio"><?php echo feder_e($boxBio); ?></p>
                    <?php endif; ?>
                    <?php if ($boxUrl !== '') : ?>
                        <a class="fd-link-arrow" href="<?php echo feder_e(feder_url($boxUrl)); ?>">Mehr über mich<?php echo feder_icon('arrow'); ?></a>
                    <?php endif; ?>
                </div>
            </aside>
        <?php endif; ?>

        <?php if ($adjacent['prev'] !== null || $adjacent['next'] !== null) : ?>
            <nav class="fd-postnav" aria-label="Weitere Beiträge">
                <?php if ($adjacent['prev'] !== null) : ?>
                    <a class="fd-postnav__link fd-postnav__link--prev" href="<?php echo feder_e(feder_post_link($adjacent['prev'])); ?>" rel="prev">
                        <span class="fd-postnav__label"><?php echo feder_icon('arrow-left'); ?>Älterer Text</span>
                        <span class="fd-postnav__title"><?php echo feder_e((string) ($adjacent['prev']->title ?? '')); ?></span>
                    </a>
                <?php endif; ?>
                <?php if ($adjacent['next'] !== null) : ?>
                    <a class="fd-postnav__link fd-postnav__link--next" href="<?php echo feder_e(feder_post_link($adjacent['next'])); ?>" rel="next">
                        <span class="fd-postnav__label">Neuerer Text<?php echo feder_icon('arrow'); ?></span>
                        <span class="fd-postnav__title"><?php echo feder_e((string) ($adjacent['next']->title ?? '')); ?></span>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </footer>
</article>

<?php if ($related !== []) : ?>
    <section class="fd-related fd-wide" aria-labelledby="fd-related-title">
        <h2 class="fd-section-title" id="fd-related-title"><?php echo feder_e(feder_text('feder_article', 'related_heading', 'Weiterlesen')); ?></h2>
        <ul class="fd-related__grid">
            <?php foreach ($related as $item) :
                $itemUrl = feder_post_link($item);
                $itemImage = feder_media_url($item->featured_image ?? '');
                ?>
                <li>
                    <article class="fd-teaser">
                        <a class="fd-teaser__media<?php echo $itemImage === '' ? ' fd-teaser__media--empty' : ''; ?>" href="<?php echo feder_e($itemUrl); ?>" tabindex="-1" aria-hidden="true">
                            <?php if ($itemImage !== '') : ?>
                                <img src="<?php echo feder_e($itemImage); ?>" alt="" width="400" height="250" loading="lazy" decoding="async">
                            <?php else : ?>
                                <span><?php echo feder_e(feder_initials((string) ($item->title ?? ''))); ?></span>
                            <?php endif; ?>
                        </a>
                        <p class="fd-meta"><time datetime="<?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '', 'iso')); ?>"><?php echo feder_e(feder_format_date($item->published_at ?? $item->created_at ?? '')); ?></time></p>
                        <h3 class="fd-teaser__title"><a href="<?php echo feder_e($itemUrl); ?>"><?php echo feder_e((string) ($item->title ?? '')); ?></a></h3>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
