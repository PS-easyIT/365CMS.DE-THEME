<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$showPostHero = isset($showPostHero) ? (bool) $showPostHero : true;
$post = isset($post) && is_array($post) ? $post : [];
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$showPostMeta = isset($showPostMeta) ? (bool) $showPostMeta : true;
$showReadingTime = isset($showReadingTime) ? (bool) $showReadingTime : true;
$readingTime = isset($readingTime) ? (int) $readingTime : 0;
$commentCount = isset($commentCount) ? (int) $commentCount : 0;
$commentLinkTarget = isset($commentLinkTarget) ? trim((string) $commentLinkTarget) : '';
$extraMetaItems = isset($extraMetaItems) && is_array($extraMetaItems) ? $extraMetaItems : [];
$favoriteControl = isset($favoriteControl) && is_array($favoriteControl) ? $favoriteControl : [];
$publishedAt = (string) ($post['published_at'] ?? '');
$updatedAt = (string) ($post['updated_at'] ?? '');
$categorySlug = urlencode(phinit_display_text($post['category_name'] ?? ''));
?>
<header class="post-header" data-anim>

    <?php echo phinit_render_favorite_button($favoriteControl); ?>

    <?php if ($showPostHero && !empty($post['featured_image'])): ?>
    <div class="post-hero-media">
        <img class="post-hero-img"
             src="<?php echo htmlspecialchars((string) $post['featured_image'], ENT_QUOTES); ?>"
             alt="<?php echo htmlspecialchars((string) ($post['title'] ?? ''), ENT_QUOTES); ?>"
               <?php echo phinit_image_loading_attributes(true); ?>
             itemprop="image">
        <?php if (!empty($post['category_name'])): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . '/kategorie/' . $categorySlug, ENT_QUOTES); ?>"
           class="post-hero-badge badge badge-teal"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="post-header-body">
        <?php if ((!$showPostHero || empty($post['featured_image'])) && !empty($post['category_name'])): ?>
        <div class="post-cats">
            <a href="<?php echo htmlspecialchars($siteUrl . '/kategorie/' . $categorySlug, ENT_QUOTES); ?>"
               class="badge badge-teal"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></a>
        </div>
        <?php endif; ?>

        <h1 class="post-title" itemprop="headline">
            <?php echo phinit_escape_text($post['title'] ?? ''); ?>
        </h1>

        <?php if ($showPostMeta): ?>
        <div class="post-meta">
            <span>📅 <strong itemprop="datePublished">
                <time datetime="<?php echo htmlspecialchars($publishedAt, ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(date('j. F Y', strtotime($publishedAt !== '' ? $publishedAt : 'now')), ENT_QUOTES); ?>
                </time>
            </strong></span>
            <?php if (!empty($post['author_name'])): ?>
            <span>👤 <strong itemprop="author"><?php echo phinit_escape_text($post['author_name'] ?? ''); ?></strong></span>
            <?php endif; ?>
            <?php if ($showReadingTime): ?>
            <span class="reading-time-badge">&#x23F1; <strong><?php echo $readingTime; ?></strong>&thinsp;Min.</span>
            <?php endif; ?>
            <?php if ($commentCount > 0): ?>
            <span>
                <?php if ($commentLinkTarget !== ''): ?>
                <a href="<?php echo htmlspecialchars($commentLinkTarget, ENT_QUOTES); ?>" class="post-meta__link">💬 <?php echo $commentCount; ?> Kommentar<?php echo $commentCount !== 1 ? 'e' : ''; ?></a>
                <?php else: ?>
                💬 <?php echo $commentCount; ?> Kommentar<?php echo $commentCount !== 1 ? 'e' : ''; ?>
                <?php endif; ?>
            </span>
            <?php endif; ?>
            <?php foreach ($extraMetaItems as $extraMetaItem): ?>
            <?php $extraMetaText = trim((string) $extraMetaItem); ?>
            <?php if ($extraMetaText !== ''): ?>
            <span><?php echo htmlspecialchars($extraMetaText, ENT_QUOTES); ?></span>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($updatedAt !== '' && $updatedAt !== $publishedAt): ?>
            <span>🔄 Aktualisiert:
                <time datetime="<?php echo htmlspecialchars($updatedAt, ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(date('j. F Y', strtotime($updatedAt)), ENT_QUOTES); ?>
                </time>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</header>
