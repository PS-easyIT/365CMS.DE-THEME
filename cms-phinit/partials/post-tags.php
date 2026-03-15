<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$showPostTags = isset($showPostTags) ? (bool) $showPostTags : false;
$postTags = isset($postTags) && is_array($postTags) ? $postTags : [];
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;

if (!$showPostTags || empty($postTags)) {
    return;
}
?>
<div class="post-tags" data-anim>
    <span>🏷️ Tags:</span>
    <?php foreach ($postTags as $tag): ?>
    <?php $tagUrl = function_exists('phinit_localized_href') ? phinit_localized_href('/tag/' . rawurlencode((string) ($tag['slug'] ?? '')), null, $siteUrl) : rtrim($siteUrl, '/') . '/tag/' . rawurlencode((string) ($tag['slug'] ?? '')); ?>
    <a href="<?php echo htmlspecialchars($tagUrl, ENT_QUOTES); ?>" class="tag-link">
        <?php echo phinit_escape_text($tag['name'] ?? ''); ?>
    </a>
    <?php endforeach; ?>
</div>
