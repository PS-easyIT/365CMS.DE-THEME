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

$pageTitle   = $page['title']   ?? '';
$pageContent = $page['content'] ?? '';
$updatedAt   = $page['updated_at'] ?? '';

$allowedTags = '<p><br><strong><b><em><i><u><s>'
    . '<h1><h2><h3><h4><h5><h6>'
    . '<ul><ol><li><dl><dt><dd>'
    . '<a><img>'
    . '<blockquote><pre><code>'
    . '<table><thead><tbody><tr><th><td>'
    . '<div><span><section><article><aside>'
    . '<hr><figure><figcaption>';
?>

<section class="ptc-page-hero">
    <div class="ptc-container">
        <?php if ($pageTitle && trim($pageTitle) !== '') : ?>
            <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php endif; ?>
        <?php if ($updatedAt && trim($updatedAt) !== '') : ?>
            <p>Zuletzt aktualisiert: <?php echo htmlspecialchars(date('d.m.Y', strtotime($updatedAt)), ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</section>

<div class="ptc-page-content">
    <div class="ptc-container">
        <?php if ($pageContent && trim($pageContent) !== '') : ?>
            <div class="ptc-prose" style="max-width:780px;">
                <?php echo strip_tags($pageContent, $allowedTags); ?>
            </div>
        <?php else : ?>
            <p style="color:var(--ptc-slate);font-style:italic;">Diese Seite hat noch keinen Inhalt.</p>
        <?php endif; ?>
    </div>
</div>
