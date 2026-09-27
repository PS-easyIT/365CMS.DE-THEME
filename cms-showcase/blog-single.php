<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Beitrag (Neuigkeiten, Release Notes, Anleitungen)
 *
 * Datenvertrag (365CMS 3.4, ThemeRouter): $post (Objekt: title, content = gerendertes,
 * sanitiertes HTML, excerpt, published_at, updated_at, author_name, category_id,
 * category_name, category_slug, featured_image, tags, tag_items).
 *
 * @package Showcase_Theme
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
$lead = showcase_excerpt($post, 300, false);
$published = (string) ($post->published_at ?? $post->created_at ?? '');
$authorName = trim((string) ($post->author_name ?? ''));
$categoryName = trim((string) ($post->category_name ?? ''));
$categorySlug = trim((string) ($post->category_slug ?? ''));
$image = showcase_media_url($post->featured_image ?? '');
$tags = showcase_post_tags($post);
$postId = (int) ($post->id ?? 0);
$permalink = showcase_post_link($post);

$toc = ['html' => $content, 'items' => []];
if (showcase_flag('layout', 'show_toc', true)) {
    $toc = showcase_toc($content, showcase_int('layout', 'toc_min_headings', 3, 2, 8));
}
$hasToc = $toc['items'] !== [];
$content = $toc['html'];

$related = showcase_get_posts(['limit' => 3, 'category_id' => (int) ($post->category_id ?? 0), 'exclude' => [$postId]]);
if (count($related) < 3) {
    $related = array_merge($related, showcase_get_posts([
        'limit' => 3 - count($related),
        'exclude' => array_merge([$postId], array_map(static fn(object $item): int => (int) ($item->id ?? 0), $related)),
    ]));
}
$initials = '';
foreach (array_slice(preg_split('/\s+/u', $authorName) ?: [], 0, 2) as $part) {
    $initials .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
}
?>

<header class="sc-pagehero sc-pagehero--article">
    <div class="sc-pagehero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-pagehero__inner">
        <nav class="sc-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo showcase_e(showcase_url('/')); ?>">Start</a></li>
                <li><a href="<?php echo showcase_e(showcase_url('/blog')); ?>">Neuigkeiten</a></li>
                <?php if ($categoryName !== '' && $categorySlug !== '') : ?>
                    <li><a href="<?php echo showcase_e(showcase_archive_url('category', $categorySlug)); ?>"><?php echo showcase_e($categoryName); ?></a></li>
                <?php endif; ?>
            </ol>
        </nav>
        <h1 class="sc-pagehero__title"><?php echo showcase_e($title); ?></h1>
        <?php if ($lead !== '') : ?>
            <p class="sc-pagehero__lead"><?php echo showcase_e($lead); ?></p>
        <?php endif; ?>
        <p class="sc-articlemeta">
            <?php if ($authorName !== '') : ?>
                <span class="sc-articlemeta__author"><span class="sc-avatar" aria-hidden="true"><?php echo showcase_e($initials); ?></span><?php echo showcase_e($authorName); ?></span>
            <?php endif; ?>
            <?php if (showcase_format_date($published) !== '') : ?>
                <span><?php echo showcase_icon('calendar'); ?><time datetime="<?php echo showcase_e(showcase_format_date($published, 'iso')); ?>"><?php echo showcase_e(showcase_format_date($published)); ?></time></span>
            <?php endif; ?>
            <span><?php echo showcase_icon('clock'); ?><?php echo showcase_reading_time($content); ?> Min. Lesezeit</span>
        </p>
    </div>
</header>

<div class="sc-container sc-doc<?php echo $hasToc ? ' sc-doc--toc' : ''; ?>">
    <?php if ($hasToc) : ?>
        <div class="sc-doc__toc">
            <div class="sc-sticky"><?php showcase_render_toc($toc['items']); ?></div>
        </div>
    <?php endif; ?>

    <article class="sc-doc__main" aria-label="<?php echo showcase_e($title); ?>">
        <?php if ($image !== '') : ?>
            <figure class="sc-media">
                <img src="<?php echo showcase_e($image); ?>" alt="" width="1200" height="675" loading="eager" decoding="async" fetchpriority="high">
            </figure>
        <?php endif; ?>

        <div class="sc-prose entry-content<?php echo $hasToc ? ' sc-prose--own-toc' : ''; ?>">
            <?php echo $content; ?>
        </div>

        <footer class="sc-articlefoot">
            <?php if ($tags !== []) : ?>
                <ul class="sc-taglist" aria-label="Schlagwörter">
                    <?php foreach ($tags as $tagItem) : ?>
                        <li><a href="<?php echo showcase_e($tagItem['url']); ?>">#<?php echo showcase_e($tagItem['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php showcase_share($permalink, $title); ?>
        </footer>
    </article>
</div>

<?php if ($related !== []) : ?>
    <section class="sc-section sc-section--surface" aria-labelledby="sc-related-title">
        <div class="sc-container">
            <div class="sc-news__head">
                <h2 class="sc-news__title" id="sc-related-title">Weiterlesen</h2>
                <a class="sc-more" href="<?php echo showcase_e(showcase_url('/blog')); ?>"><span>Alle Neuigkeiten</span><?php echo showcase_icon('arrow'); ?></a>
            </div>
            <div class="sc-postgrid">
                <?php foreach ($related as $item) : ?>
                    <?php showcase_post_card($item, 'h3'); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
