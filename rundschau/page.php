<?php
declare(strict_types=1);

/**
 * Rundschau – Statische Seite
 *
 * Datenvertrag (365CMS 3.4): $page (Array; content = gerendertes, sanitiertes HTML).
 *
 * @package Rundschau_Theme
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
$image = rundschau_media_url($page['featured_image'] ?? '');
$updated = (string) ($page['content_updated_at'] ?? $page['updated_at'] ?? '');
?>

<article class="rs-container rs-layout rs-layout--narrow rs-page" aria-label="<?php echo rundschau_e($title); ?>">
    <div class="rs-layout__main">
        <nav class="rs-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo rundschau_e(rundschau_url('/')); ?>">Startseite</a></li>
                <li><span aria-current="page"><?php echo rundschau_e($title); ?></span></li>
            </ol>
        </nav>

        <?php if ($hideTitle) : ?>
            <h1 class="rs-visually-hidden"><?php echo rundschau_e($title); ?></h1>
        <?php else : ?>
            <header class="rs-article__head">
                <h1 class="rs-article__title"><?php echo rundschau_e($title); ?></h1>
                <?php if ($lead !== '') : ?>
                    <p class="rs-article__teaser"><?php echo rundschau_e($lead); ?></p>
                <?php endif; ?>
                <?php if (rundschau_format_date($updated) !== '') : ?>
                    <p class="rs-meta">Stand: <time datetime="<?php echo rundschau_e(rundschau_format_date($updated, 'iso')); ?>"><?php echo rundschau_e(rundschau_format_date($updated)); ?></time></p>
                <?php endif; ?>
            </header>
        <?php endif; ?>

        <?php if ($image !== '') : ?>
            <figure class="rs-article__media">
                <img src="<?php echo rundschau_e($image); ?>" alt="<?php echo rundschau_e($title); ?>" width="1200" height="675" loading="eager" decoding="async">
            </figure>
        <?php endif; ?>

        <div class="rs-prose entry-content">
            <?php if (trim($content) !== '') : ?>
                <?php echo $content; ?>
            <?php else : ?>
                <p class="rs-empty">Diese Seite hat noch keinen Inhalt.</p>
            <?php endif; ?>
        </div>
    </div>
</article>
