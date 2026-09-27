<?php
declare(strict_types=1);

/**
 * Feder – Statische Seite
 *
 * Datenvertrag (365CMS 3.4): $page (Array; content = gerendertes, sanitiertes HTML,
 * title, meta_description, featured_image, hide_title, updated_at/content_updated_at).
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($page) || !is_array($page)) {
    require __DIR__ . '/404.php';
    return;
}

$title = trim((string) ($page['title'] ?? ''));
$content = (string) ($page['content'] ?? '');
$lead = trim((string) ($page['meta_description'] ?? $page['excerpt'] ?? ''));
$hideTitle = !empty($page['hide_title']);
$image = feder_media_url($page['featured_image'] ?? '');
$updated = (string) ($page['content_updated_at'] ?? $page['updated_at'] ?? '');
?>

<article class="fd-page" aria-label="<?php echo feder_e($title); ?>">
    <?php if (!$hideTitle) : ?>
        <header class="fd-page__header fd-measure">
            <h1 class="fd-page__title"><?php echo feder_e($title); ?></h1>
            <?php if ($lead !== '') : ?>
                <p class="fd-article__dek"><?php echo feder_e($lead); ?></p>
            <?php endif; ?>
            <?php if ($updated !== '' && feder_format_date($updated) !== '') : ?>
                <p class="fd-meta">Zuletzt aktualisiert am <time datetime="<?php echo feder_e(feder_format_date($updated, 'iso')); ?>"><?php echo feder_e(feder_format_date($updated)); ?></time></p>
            <?php endif; ?>
        </header>
    <?php else : ?>
        <h1 class="fd-visually-hidden"><?php echo feder_e($title); ?></h1>
    <?php endif; ?>

    <?php if ($image !== '') : ?>
        <figure class="fd-article__media fd-wide">
            <img src="<?php echo feder_e($image); ?>" alt="<?php echo feder_e($title); ?>" width="1600" height="900" loading="eager" decoding="async">
        </figure>
    <?php endif; ?>

    <div class="fd-prose entry-content fd-measure">
        <?php if (trim($content) !== '') : ?>
            <?php echo $content; ?>
        <?php else : ?>
            <p class="fd-empty">Diese Seite hat noch keinen Inhalt.</p>
        <?php endif; ?>
    </div>
</article>
