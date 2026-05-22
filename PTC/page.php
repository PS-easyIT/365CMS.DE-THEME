<?php
/**
 * PTC Theme – Generische Seite
 *
 * @package PTC_Theme
 * @var array $page
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (empty($page) || !is_array($page)) {
    http_response_code(404);
    \CMS\ThemeManager::instance()->render('404');
    return;
}

/* ─── Customizer-Einstellungen ─── */
$showTitle      = ptc_customizer_get('blog', 'page_show_title',          '1');
$showUpdated    = ptc_customizer_get('blog', 'page_show_updated',        '1');
$showFeatured   = ptc_customizer_get('blog', 'page_show_featured_image', '1');
$pageMaxWidth   = (int) ptc_customizer_get('blog', 'page_max_width',    '960');

$pageTitle      = $page['title']          ?? '';
$pageContent    = $page['content']        ?? '';
$updatedAt      = $page['updated_at']     ?? '';
$featuredImage  = $page['featured_image'] ?? '';

$allowedTags = '<p><br><strong><b><em><i><u><s>'
    . '<h1><h2><h3><h4><h5><h6>'
    . '<ul><ol><li><dl><dt><dd>'
    . '<a><img>'
    . '<blockquote><pre><code>'
    . '<table><thead><tbody><tr><th><td>'
    . '<div><span><section><article><aside>'
    . '<hr><figure><figcaption><iframe><video><audio><source>';
?>

<?php if ($showTitle && $pageTitle && trim($pageTitle) !== '') : ?>
<section class="ptc-page-hero">
    <div class="ptc-container">
        <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if ($showUpdated && $updatedAt && trim($updatedAt) !== '') : ?>
            <p>Zuletzt aktualisiert: <?php echo htmlspecialchars(date('d.m.Y', strtotime($updatedAt)), ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($showFeatured && $featuredImage) : ?>
<div class="ptc-page-featured">
    <div class="ptc-container">
        <img src="<?php echo htmlspecialchars($featuredImage, ENT_QUOTES, 'UTF-8'); ?>"
             alt="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
    </div>
</div>
<?php endif; ?>

<div class="ptc-page-content">
    <div class="ptc-container">
        <?php if ($pageContent && trim($pageContent) !== '') : ?>
            <div class="ptc-prose ptc-prose--constrained sun-editor-editable">
                <?php echo strip_tags($pageContent, $allowedTags); ?>
            </div>
        <?php else : ?>
            <p class="ptc-text-muted">Diese Seite hat noch keinen Inhalt.</p>
        <?php endif; ?>
    </div>
</div>
