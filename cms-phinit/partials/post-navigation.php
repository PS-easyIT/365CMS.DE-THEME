<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$prevPost = isset($prevPost) && is_array($prevPost) ? $prevPost : null;
$nextPost = isset($nextPost) && is_array($nextPost) ? $nextPost : null;
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$permalinkService = class_exists('CMS\\Services\\PermalinkService') ? \CMS\Services\PermalinkService::getInstance() : null;

if (!$prevPost && !$nextPost) {
    return;
}
?>
<nav class="post-nav" aria-label="<?php echo htmlspecialchars(phinit_t('article_navigation', [], $currentLocale), ENT_QUOTES); ?>">
    <?php if ($prevPost): ?>
    <?php $prevUrl = $permalinkService ? $permalinkService->buildPostUrl($prevPost, $currentLocale) : ($siteUrl . '/blog/' . ($prevPost['slug'] ?? '')); ?>
    <a href="<?php echo htmlspecialchars($prevUrl, ENT_QUOTES); ?>">
        <span class="direction"><?php echo htmlspecialchars(phinit_t('previous_post', [], $currentLocale), ENT_QUOTES); ?></span>
        <span class="nav-title"><?php echo phinit_escape_text($prevPost['title'] ?? ''); ?></span>
    </a>
    <?php else: ?>
    <span></span>
    <?php endif; ?>

    <?php if ($nextPost): ?>
    <?php $nextUrl = $permalinkService ? $permalinkService->buildPostUrl($nextPost, $currentLocale) : ($siteUrl . '/blog/' . ($nextPost['slug'] ?? '')); ?>
    <a href="<?php echo htmlspecialchars($nextUrl, ENT_QUOTES); ?>">
        <span class="direction"><?php echo htmlspecialchars(phinit_t('next_post', [], $currentLocale), ENT_QUOTES); ?></span>
        <span class="nav-title"><?php echo phinit_escape_text($nextPost['title'] ?? ''); ?></span>
    </a>
    <?php endif; ?>
</nav>
