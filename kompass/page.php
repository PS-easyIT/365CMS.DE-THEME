<?php
declare(strict_types=1);

/**
 * Kompass – Informationsseite
 *
 * Seitenkopf mit Brotkrumen → Inhaltsverzeichnis „Auf dieser Seite“ (aus H2/H3) →
 * Inhalt → Stand, Drucken, Link kopieren, PDF → „War diese Seite hilfreich?“.
 *
 * Datenvertrag (365CMS 3.4): $page (Array; content = gerendertes, sanitiertes HTML).
 *
 * @package Kompass_Theme
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
$image = kompass_media_url($page['featured_image'] ?? '');
$updated = (string) ($page['updated_at'] ?? $page['created_at'] ?? '');

$toc = ['html' => $content, 'items' => []];
if (kompass_flag('layout', 'show_toc', true)) {
    $toc = kompass_toc($content, kompass_int('layout', 'toc_min_headings', 3, 2, 8));
}
$hasToc = $toc['items'] !== [];
$content = $toc['html'];
?>

<header class="kp-pagehead<?php echo $hideTitle ? ' kp-pagehead--compact' : ''; ?>">
    <div class="kp-container">
        <nav class="kp-breadcrumb" aria-label="Brotkrumen">
            <ol>
                <li><a href="<?php echo kompass_e(kompass_url('/')); ?>">Start</a></li>
                <li><span aria-current="page"><?php echo kompass_e($title); ?></span></li>
            </ol>
        </nav>
        <?php if (!$hideTitle) : ?>
            <h1 class="kp-pagehead__title"><?php echo kompass_e($title); ?></h1>
            <?php if ($lead !== '') : ?>
                <p class="kp-pagehead__lead"><?php echo kompass_e($lead); ?></p>
            <?php endif; ?>
        <?php else : ?>
            <h1 class="kp-visually-hidden"><?php echo kompass_e($title); ?></h1>
        <?php endif; ?>
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
                <img src="<?php echo kompass_e($image); ?>" alt="" width="1200" height="675" loading="eager" decoding="async">
            </figure>
        <?php endif; ?>

        <div class="kp-prose entry-content<?php echo $hasToc ? ' kp-prose--own-toc' : ''; ?>">
            <?php if (trim($content) !== '') : ?>
                <?php echo $content; ?>
            <?php else : ?>
                <p class="kp-empty">Diese Seite hat noch keinen Inhalt.</p>
            <?php endif; ?>
        </div>

        <?php kompass_page_tools($updated, true); ?>
        <?php kompass_feedback($title); ?>
    </article>
</div>
