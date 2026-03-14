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
$authorId = (int) ($post['author_id'] ?? 0);
$authorUrl = $authorId > 0 ? $siteUrl . '/author/user-' . $authorId : '';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
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
        <?php if ($showReadingTime && $readingTime > 0): ?>
        <span class="post-hero-reading-badge" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $readingTime], $currentLocale), ENT_QUOTES); ?>">
            <?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $readingTime], $currentLocale), ENT_QUOTES); ?>
        </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="post-header-body">
        <h1 class="post-title" itemprop="headline">
            <?php echo phinit_escape_text($post['title'] ?? ''); ?>
        </h1>

        <?php if ($showPostMeta): ?>
        <div class="post-meta">
            <span class="post-meta__item post-meta__item--date">
                <span class="post-meta__icon" aria-hidden="true">📅</span>
                <strong itemprop="datePublished">
                <time datetime="<?php echo htmlspecialchars($publishedAt, ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(phinit_format_date($publishedAt !== '' ? $publishedAt : 'now', 'numeric', $currentLocale), ENT_QUOTES); ?>
                </time>
                </strong>
            </span>
            <?php if (!empty($post['category_name'])): ?>
            <span class="post-meta__item post-meta__item--category">
                <span class="post-meta__icon" aria-hidden="true">🏷️</span>
                <a href="<?php echo htmlspecialchars($siteUrl . '/kategorie/' . $categorySlug, ENT_QUOTES); ?>" class="post-meta__link"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></a>
            </span>
            <?php endif; ?>
            <?php if (!empty($post['author_name'])): ?>
            <span class="post-meta__item post-meta__item--author">
                <span class="post-meta__icon" aria-hidden="true">👤</span>
                <strong itemprop="author">
                <?php if ($authorUrl !== ''): ?>
                <a href="<?php echo htmlspecialchars($authorUrl, ENT_QUOTES); ?>" class="post-meta__link"><?php echo phinit_escape_text($post['author_name'] ?? ''); ?></a>
                <?php else: ?>
                <?php echo phinit_escape_text($post['author_name'] ?? ''); ?>
                <?php endif; ?>
                </strong>
            </span>
            <?php endif; ?>
            <?php if ($showReadingTime && $readingTime > 0 && (!$showPostHero || empty($post['featured_image']))): ?>
            <span class="post-meta__item reading-time-badge">
                <?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $readingTime], $currentLocale), ENT_QUOTES); ?>
            </span>
            <?php endif; ?>
            <?php if ($commentCount > 0): ?>
            <span class="post-meta__item post-meta__item--comments">
                <?php if ($commentLinkTarget !== ''): ?>
                <a href="<?php echo htmlspecialchars($commentLinkTarget, ENT_QUOTES); ?>" class="post-meta__link"><span class="post-meta__icon" aria-hidden="true">💬</span><?php echo htmlspecialchars(phinit_comment_count_text($commentCount, $currentLocale), ENT_QUOTES); ?></a>
                <?php else: ?>
                <span class="post-meta__icon" aria-hidden="true">💬</span><?php echo htmlspecialchars(phinit_comment_count_text($commentCount, $currentLocale), ENT_QUOTES); ?>
                <?php endif; ?>
            </span>
            <?php endif; ?>
            <?php foreach ($extraMetaItems as $extraMetaItem): ?>
            <?php $extraMetaText = trim((string) $extraMetaItem); ?>
            <?php if ($extraMetaText !== ''): ?>
            <span class="post-meta__item post-meta__item--extra"><?php echo htmlspecialchars($extraMetaText, ENT_QUOTES); ?></span>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php if ($updatedAt !== '' && $updatedAt !== $publishedAt): ?>
            <span class="post-meta__item post-meta__item--updated">
                <span class="post-meta__icon" aria-hidden="true">🔄</span>
                <span class="post-meta__label"><?php echo htmlspecialchars(phinit_t('updated_label', [], $currentLocale), ENT_QUOTES); ?></span>
                <time datetime="<?php echo htmlspecialchars($updatedAt, ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(phinit_format_date($updatedAt, 'numeric', $currentLocale), ENT_QUOTES); ?>
                </time>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</header>
