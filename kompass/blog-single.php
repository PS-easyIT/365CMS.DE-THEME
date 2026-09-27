<?php
declare(strict_types=1);

/**
 * Kompass – Beitrag / Meldung
 *
 * Seitenkopf mit Thema, Datum, Aktualisierung und Lesezeit → Inhaltsverzeichnis → Inhalt
 * → Stichwörter, Werkzeuge, Rückmeldung → weitere Informationen zum Thema.
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $post (Objekt: title, content = gerendertes,
 * sanitiertes HTML, excerpt, published_at, updated_at, author_name, category_id,
 * category_name, category_slug, featured_image, tags, tag_items).
 *
 * @package Kompass_Theme
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
$lead = kompass_excerpt($post, 320, false);
$published = (string) ($post->published_at ?? $post->created_at ?? '');
$updated = (string) ($post->updated_at ?? '');
$authorName = trim((string) ($post->author_name ?? ''));
$categoryName = trim((string) ($post->category_name ?? ''));
$categorySlug = trim((string) ($post->category_slug ?? ''));
$image = kompass_media_url($post->featured_image ?? '');
$tags = kompass_post_tags($post);
$postId = (int) ($post->id ?? 0);
$readingTime = kompass_reading_time($content);

$publishedTs = strtotime($published) ?: 0;
$updatedTs = strtotime($updated) ?: 0;
$showUpdated = $updatedTs > 0 && $publishedTs > 0 && $updatedTs - $publishedTs > 86400;

$toc = ['html' => $content, 'items' => []];
if (kompass_flag('layout', 'show_toc', true)) {
    $toc = kompass_toc($content, kompass_int('layout', 'toc_min_headings', 3, 2, 8));
}
$hasToc = $toc['items'] !== [];
$content = $toc['html'];

$related = kompass_get_posts(['limit' => 3, 'category_id' => (int) ($post->category_id ?? 0), 'exclude' => [$postId]]);
if (count($related) < 3) {
    $related = array_merge($related, kompass_get_posts([
        'limit' => 3 - count($related),
        'exclude' => array_merge([$postId], array_map(static fn(object $item): int => (int) ($item->id ?? 0), $related)),
    ]));
}
?>

<header class="kp-pagehead kp-pagehead--article">
    <div class="kp-container">
        <nav class="kp-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kompass_e(kompass_url('/')); ?>">Start</a></li>
                <li><a href="<?php echo kompass_e(kompass_url('/blog')); ?>">Aktuelles</a></li>
                <?php if ($categoryName !== '' && $categorySlug !== '') : ?>
                    <li><a href="<?php echo kompass_e(kompass_archive_url('category', $categorySlug)); ?>"><?php echo kompass_e($categoryName); ?></a></li>
                <?php endif; ?>
                <li><span aria-current="page"><?php echo kompass_e($title); ?></span></li>
            </ol>
        </nav>
        <h1 class="kp-pagehead__title"><?php echo kompass_e($title); ?></h1>
        <?php if ($lead !== '') : ?>
            <p class="kp-pagehead__lead"><?php echo kompass_e($lead); ?></p>
        <?php endif; ?>
        <ul class="kp-meta">
            <?php if (kompass_format_date($published) !== '') : ?>
                <li><?php echo kompass_icon('calendar'); ?><span>Veröffentlicht am <time datetime="<?php echo kompass_e(kompass_format_date($published, 'iso')); ?>"><?php echo kompass_e(kompass_format_date($published)); ?></time></span></li>
            <?php endif; ?>
            <?php if ($showUpdated) : ?>
                <li><?php echo kompass_icon('success'); ?><span>Aktualisiert am <time datetime="<?php echo kompass_e(kompass_format_date($updated, 'iso')); ?>"><?php echo kompass_e(kompass_format_date($updated)); ?></time></span></li>
            <?php endif; ?>
            <li><?php echo kompass_icon('clock'); ?><span><?php echo $readingTime; ?> Min. Lesezeit</span></li>
            <?php if ($authorName !== '') : ?>
                <li><?php echo kompass_icon('people'); ?><span><?php echo kompass_e($authorName); ?></span></li>
            <?php endif; ?>
            <?php if ($categoryName !== '' && $categorySlug !== '') : ?>
                <li><?php echo kompass_icon(kompass_topic_icon($categorySlug)); ?><span>Thema: <a href="<?php echo kompass_e(kompass_archive_url('category', $categorySlug)); ?>"><?php echo kompass_e($categoryName); ?></a></span></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<div class="kp-container kp-layout<?php echo $hasToc ? ' kp-layout--toc' : ''; ?>">
    <?php if ($hasToc) : ?>
        <div class="kp-layout__toc">
            <div class="kp-sticky"><?php kompass_render_toc($toc['items']); ?></div>
        </div>
    <?php endif; ?>

    <article class="kp-layout__main" aria-label="<?php echo kompass_e($title); ?>">
        <?php if ($image !== '') : ?>
            <figure class="kp-media">
                <img src="<?php echo kompass_e($image); ?>" alt="" width="1200" height="675" loading="eager" decoding="async" fetchpriority="high">
            </figure>
        <?php endif; ?>

        <div class="kp-prose entry-content<?php echo $hasToc ? ' kp-prose--own-toc' : ''; ?>">
            <?php echo $content; ?>
        </div>

        <?php if ($tags !== []) : ?>
            <section class="kp-tagsbox" aria-labelledby="kp-tags-title">
                <h2 class="kp-tagsbox__title" id="kp-tags-title">Stichwörter</h2>
                <ul class="kp-chips">
                    <?php foreach ($tags as $tagItem) : ?>
                        <li><a class="kp-chip" href="<?php echo kompass_e($tagItem['url']); ?>"><?php echo kompass_e($tagItem['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php kompass_page_tools($showUpdated ? $updated : $published); ?>
        <?php kompass_feedback($title); ?>
    </article>
</div>

<?php if ($related !== []) : ?>
    <section class="kp-section kp-section--surface" aria-labelledby="kp-related-title">
        <div class="kp-container">
            <header class="kp-section__head">
                <h2 class="kp-section__title" id="kp-related-title">Weitere Informationen</h2>
                <a class="kp-more" href="<?php echo kompass_e(kompass_url('/blog')); ?>"><span>Alle Meldungen</span><?php echo kompass_icon('arrow'); ?></a>
            </header>
            <ul class="kp-newslist kp-newslist--grid">
                <?php foreach ($related as $item) : ?>
                    <?php kompass_news_item($item, 'h3'); ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
<?php endif; ?>
