<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Seite im Dokumentationsstil
 *
 * Dunkler Seitenkopf → Inhaltsverzeichnis „Auf dieser Seite“ (aus H2/H3) → Inhalt.
 *
 * Datenvertrag (365CMS 3.4): $page (Array; content = gerendertes, sanitiertes HTML).
 *
 * @package Showcase_Theme
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
$image = showcase_media_url($page['featured_image'] ?? '');
$updated = (string) ($page['updated_at'] ?? $page['created_at'] ?? '');

$toc = ['html' => $content, 'items' => []];
if (showcase_flag('layout', 'show_toc', true)) {
    $toc = showcase_toc($content, showcase_int('layout', 'toc_min_headings', 3, 2, 8));
}
$hasToc = $toc['items'] !== [];
$content = $toc['html'];
?>

<header class="sc-pagehero<?php echo $hideTitle ? ' sc-pagehero--compact' : ''; ?>">
    <div class="sc-pagehero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-pagehero__inner">
        <nav class="sc-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo showcase_e(showcase_url('/')); ?>">Start</a></li>
                <li><span aria-current="page"><?php echo showcase_e($title); ?></span></li>
            </ol>
        </nav>
        <?php if (!$hideTitle) : ?>
            <h1 class="sc-pagehero__title"><?php echo showcase_e($title); ?></h1>
            <?php if ($lead !== '') : ?>
                <p class="sc-pagehero__lead"><?php echo showcase_e($lead); ?></p>
            <?php endif; ?>
        <?php else : ?>
            <h1 class="sc-visually-hidden"><?php echo showcase_e($title); ?></h1>
        <?php endif; ?>
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
                <img src="<?php echo showcase_e($image); ?>" alt="" width="1200" height="675" loading="eager" decoding="async">
            </figure>
        <?php endif; ?>

        <div class="sc-prose entry-content<?php echo $hasToc ? ' sc-prose--own-toc' : ''; ?>">
            <?php if (trim($content) !== '') : ?>
                <?php echo $content; ?>
            <?php else : ?>
                <p class="sc-empty">Diese Seite hat noch keinen Inhalt.</p>
            <?php endif; ?>
        </div>

        <?php if (showcase_format_date($updated) !== '') : ?>
            <p class="sc-doc__updated"><?php echo showcase_icon('clock'); ?>Zuletzt aktualisiert: <time datetime="<?php echo showcase_e(showcase_format_date($updated, 'iso')); ?>"><?php echo showcase_e(showcase_format_date($updated)); ?></time></p>
        <?php endif; ?>
    </article>
</div>
