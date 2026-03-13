<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$prevPost = isset($prevPost) && is_array($prevPost) ? $prevPost : null;
$nextPost = isset($nextPost) && is_array($nextPost) ? $nextPost : null;
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;

if (!$prevPost && !$nextPost) {
    return;
}
?>
<nav class="post-nav" aria-label="Artikel-Navigation">
    <?php if ($prevPost): ?>
    <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($prevPost['slug'] ?? ''), ENT_QUOTES); ?>">
        <span class="direction">← Vorheriger Beitrag</span>
        <span class="nav-title"><?php echo phinit_escape_text($prevPost['title'] ?? ''); ?></span>
    </a>
    <?php else: ?>
    <span></span>
    <?php endif; ?>

    <?php if ($nextPost): ?>
    <a href="<?php echo htmlspecialchars($siteUrl . '/blog/' . ($nextPost['slug'] ?? ''), ENT_QUOTES); ?>">
        <span class="direction">Nächster Beitrag →</span>
        <span class="nav-title"><?php echo phinit_escape_text($nextPost['title'] ?? ''); ?></span>
    </a>
    <?php endif; ?>
</nav>
