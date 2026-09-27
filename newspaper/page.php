<?php
/**
 * CMS Newspaper Theme – Generic static page (CMS Page record)
 *
 * @package CmsNewspaper_Theme
 * @var array $page
 */

if (!defined('ABSPATH')) {
    exit;
}

if (empty($page) || !is_array($page)) {
    http_response_code(404);
    try {
        \CMS\ThemeManager::instance()->render('404');
    } catch (\Throwable) {
        require __DIR__ . '/404.php';
    }
    return;
}

$pageTitle   = (string) ($page['title']      ?? '');
$pageContent = (string) ($page['content']    ?? '');
$updatedAt   = (string) ($page['updated_at'] ?? '');

$allowedTags = '<p><br><strong><b><em><i><u><s>'
    . '<h1><h2><h3><h4><h5><h6>'
    . '<ul><ol><li><dl><dt><dd>'
    . '<a><img>'
    . '<blockquote><pre><code>'
    . '<table><thead><tbody><tr><th><td>'
    . '<div><span><section><article><aside>'
    . '<hr><figure><figcaption>';

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>

<section class="news-page-hero" aria-label="<?php echo $safe($pageTitle); ?>">
    <div class="news-container">
        <span class="news-kicker">Seite</span>
        <?php if (trim($pageTitle) !== '') : ?>
            <h1><?php echo $safe($pageTitle); ?></h1>
        <?php endif; ?>
        <?php
        if (trim($updatedAt) !== '') :
            $ts = strtotime($updatedAt);
            if ($ts !== false) :
                ?>
                <p>Zuletzt aktualisiert: <?php echo $safe(date('d.m.Y', $ts)); ?></p>
            <?php
            endif;
        endif;
        ?>
    </div>
</section>

<div class="news-page-content">
    <div class="news-container">
        <?php if (trim($pageContent) !== '') : ?>
            <div class="news-prose">
                <?php echo strip_tags($pageContent, $allowedTags); ?>
            </div>
        <?php else : ?>
            <p class="news-prose-empty">Diese Seite hat noch keinen Inhalt.</p>
        <?php endif; ?>
    </div>
</div>
