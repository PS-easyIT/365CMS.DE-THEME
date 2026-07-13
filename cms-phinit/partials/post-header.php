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
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$categorySlug = rawurlencode(phinit_display_text($post['category_slug'] ?? $post['category_name'] ?? ''));
$authorId = (int) ($post['author_id'] ?? 0);
$authorLink = function_exists('phinit_resolve_post_author_link')
    ? phinit_resolve_post_author_link($post, $currentLocale, $siteUrl)
    : ['url' => '', 'isExternal' => false];
$authorUrl = $authorLink['url'];
$authorUrlIsExternal = $authorLink['isExternal'];
$categoryUrl = !empty($post['category_name'])
    ? (function_exists('phinit_localized_href') ? phinit_localized_href('/kategorie/' . $categorySlug, $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/kategorie/' . $categorySlug)
    : '';
$postHeroImage = function_exists('phinit_normalize_public_media_url')
    ? phinit_normalize_public_media_url((string) ($post['featured_image'] ?? ''), false, $siteUrl)
    : (string) ($post['featured_image'] ?? '');
$publishedTimestamp = $publishedAt !== '' ? strtotime($publishedAt) : false;
?>
<header class="post-header" data-anim>

    <?php echo phinit_render_favorite_button($favoriteControl); ?>

    <?php if ($showPostHero && $postHeroImage !== ''): ?>
    <div class="post-hero-media">
        <img class="post-hero-img"
             src="<?php echo htmlspecialchars($postHeroImage, ENT_QUOTES); ?>"
             alt="<?php echo htmlspecialchars((string) ($post['title'] ?? ''), ENT_QUOTES); ?>"
                             <?php echo phinit_image_loading_attributes(true); ?>
                             <?php echo phinit_image_dimension_attributes($postHeroImage); ?>
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
                <?php if ($publishedTimestamp !== false): ?>
                <time datetime="<?php echo htmlspecialchars($publishedAt, ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(phinit_format_date($publishedAt, 'numeric', $currentLocale), ENT_QUOTES); ?>
                </time>
                <?php else: ?>
                —
                <?php endif; ?>
                </strong>
            </span>
            <?php if (!empty($post['category_name'])): ?>
            <span class="post-meta__item post-meta__item--category">
                <span class="post-meta__icon" aria-hidden="true">🏷️</span>
                <a href="<?php echo htmlspecialchars($categoryUrl, ENT_QUOTES); ?>" class="post-meta__link"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></a>
            </span>
            <?php endif; ?>
            <?php if (!empty($post['author_name'])): ?>
            <span class="post-meta__item post-meta__item--author">
                <span class="post-meta__icon" aria-hidden="true">👤</span>
                <strong itemprop="author">
                <?php if ($authorUrl !== ''): ?>
                <a href="<?php echo htmlspecialchars($authorUrl, ENT_QUOTES); ?>" class="post-meta__link"<?php echo $authorUrlIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo phinit_escape_text($post['author_name'] ?? ''); ?></a>
                <?php else: ?>
                <?php echo phinit_escape_text($post['author_name'] ?? ''); ?>
                <?php endif; ?>
                </strong>
            </span>
            <?php endif; ?>
            <?php if ($showReadingTime && $readingTime > 0 && (!$showPostHero || $postHeroImage === '')): ?>
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
        </div>
        <?php endif; ?>
    </div>
</header>
