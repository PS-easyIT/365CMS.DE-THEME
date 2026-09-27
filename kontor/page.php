<?php
declare(strict_types=1);

/**
 * Kontor – Statische Seite (z. B. Über uns, Leistungen, Kontakt, Impressum)
 *
 * Datenvertrag (365CMS 3.4): $page (Array; content = gerendertes, sanitiertes HTML).
 *
 * @package Kontor_Theme
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
$image = kontor_media_url($page['featured_image'] ?? '');
$slug = strtolower(trim((string) ($page['slug'] ?? '')));
$isLegal = in_array($slug, ['impressum', 'datenschutz', 'datenschutzerklaerung', 'agb', 'imprint', 'privacy'], true);
$showSidebar = kontor_flag('layout', 'page_sidebar', true) && !$isLegal;
?>

<?php if (!$hideTitle) : ?>
    <header class="kt-pagehero">
        <div class="kt-container">
            <nav class="kt-breadcrumb" aria-label="Brotkrumen">
                <ol>
                    <li><a href="<?php echo kontor_e(kontor_url('/')); ?>">Start</a></li>
                    <li><span aria-current="page"><?php echo kontor_e($title); ?></span></li>
                </ol>
            </nav>
            <h1 class="kt-pagehero__title"><?php echo kontor_e($title); ?></h1>
            <?php if ($lead !== '') : ?>
                <p class="kt-pagehero__text"><?php echo kontor_e($lead); ?></p>
            <?php endif; ?>
        </div>
    </header>
<?php else : ?>
    <h1 class="kt-visually-hidden"><?php echo kontor_e($title); ?></h1>
<?php endif; ?>

<div class="kt-container kt-content<?php echo $showSidebar ? ' kt-content--sidebar' : ' kt-content--narrow'; ?>">
    <article class="kt-content__main" aria-label="<?php echo kontor_e($title); ?>">
        <?php if ($image !== '') : ?>
            <figure class="kt-content__media">
                <img src="<?php echo kontor_e($image); ?>" alt="<?php echo kontor_e($title); ?>" width="1200" height="675" loading="eager" decoding="async">
            </figure>
        <?php endif; ?>
        <div class="kt-prose entry-content">
            <?php if (trim($content) !== '') : ?>
                <?php echo $content; ?>
            <?php else : ?>
                <p class="kt-empty">Diese Seite hat noch keinen Inhalt.</p>
            <?php endif; ?>
        </div>
    </article>

    <?php if ($showSidebar) : ?>
        <div class="kt-content__aside">
            <div class="kt-sticky">
                <?php kontor_contact_card(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
