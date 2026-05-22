<?php
declare(strict_types=1);

/**
 * Business Theme – Generische Seite
 *
 * @package IT_Business_Theme
 * @var array $page
 */

if (!defined('ABSPATH')) {
    exit;
}

if (empty($page) || !is_array($page)) {
    http_response_code(404);
    \CMS\ThemeManager::instance()->render('404');
    return;
}

$pageTitle   = (string) ($page['title']      ?? '');
$pageContent = (string) ($page['content']    ?? '');
$updatedAt   = (string) ($page['updated_at'] ?? '');

$pageContent = biz_sanitize_content_html($pageContent);
?>

<section class="biz-page-hero">
    <div class="biz-container">
        <?php if (trim($pageTitle) !== '') : ?>
            <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php endif; ?>
        <?php
        if (trim($updatedAt) !== '') :
            $ts = strtotime($updatedAt);
            if ($ts !== false) :
                ?>
                <p>Zuletzt aktualisiert: <?php echo htmlspecialchars(date('d.m.Y', $ts), ENT_QUOTES, 'UTF-8'); ?></p>
            <?php
            endif;
        endif;
        ?>
    </div>
</section>

<div class="biz-page-content">
    <div class="biz-container">
        <?php if (trim($pageContent) !== '') : ?>
            <div class="biz-prose">
                <?php echo $pageContent; ?>
            </div>
        <?php else : ?>
            <p class="biz-prose-empty">Diese Seite hat noch keinen Inhalt.</p>
        <?php endif; ?>
    </div>
</div>
