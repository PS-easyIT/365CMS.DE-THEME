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
    <a href="<?php echo htmlspecialchars($siteUrl . '/tag/' . ((string) ($tag['slug'] ?? '')), ENT_QUOTES); ?>" class="tag-link">
        <?php echo phinit_escape_text($tag['name'] ?? ''); ?>
    </a>
    <?php endforeach; ?>
</div>
