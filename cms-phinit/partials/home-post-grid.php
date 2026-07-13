<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$gridPosts = isset($gridPosts) && is_array($gridPosts) ? $gridPosts : [];
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$localizationService = class_exists('CMS\\Services\\ContentLocalizationService')
    ? \CMS\Services\ContentLocalizationService::getInstance()
    : null;
$permalinkService = class_exists('CMS\\Services\\PermalinkService')
    ? \CMS\Services\PermalinkService::getInstance()
    : null;

if (empty($_showTileGrid) || $gridPosts === []) {
    return;
}
?>
<section class="content-section home-section home-section--grid">
    <div class="section-header">
        <span class="section-label section-label--dark"><?php echo htmlspecialchars((string) $_tileLabel, ENT_QUOTES); ?></span>
    </div>

    <div class="posts-grid posts-grid--cols-<?php echo (int) $_tileCols; ?>">
        <?php foreach ($gridPosts as $i => $post): ?>
        <?php
        $post = is_array($post) ? $post : [];
        if ($localizationService !== null) {
            try {
                $post = $localizationService->localizePost($post, $currentLocale);
            } catch (\Throwable $_gridLocalizationError) {
                // Fallback: rendere mit den gelieferten Werten weiter.
            }
        }
        if ((string) ($post['permalink'] ?? '') === '' && $permalinkService !== null) {
            try {
                $post['permalink'] = $permalinkService->buildPostUrl($post, $currentLocale);
            } catch (\Throwable $_gridPermalinkError) {
                $post['permalink'] = '';
            }
        }
        $displayTitle = trim((string) ($post['title'] ?? ''));
        if ($displayTitle === '') {
            $displayTitle = trim((string) ($post['title_en'] ?? ''));
        }
        $displayExcerptSource = trim((string) ($post['excerpt'] ?? ''));
        if ($displayExcerptSource === '') {
            $displayExcerptSource = trim((string) ($post['excerpt_en'] ?? ''));
        }
        $displayContentSource = trim((string) ($post['content'] ?? ''));
        if ($displayContentSource === '') {
            $displayContentSource = trim((string) ($post['content_en'] ?? ''));
        }
        $displaySlug = trim((string) ($post['slug'] ?? ''));
        if ($displaySlug === '') {
            $displaySlug = trim((string) ($post['slug_en'] ?? ''));
        }
        $categoryLabel = trim((string) ($post['category_name'] ?? ''));
        $categorySlug = trim((string) ($post['category_slug'] ?? ''));
        if ($categorySlug === '' && $categoryLabel !== '') {
            $categorySlug = function_exists('phinit_display_text')
                ? phinit_display_text($categoryLabel)
                : $categoryLabel;
        }
        $categoryUrl = '';
        if ($categorySlug !== '') {
            $categoryUrl = function_exists('cms_get_archive_url')
                ? (string) cms_get_archive_url('category', $categorySlug, $currentLocale)
                : (function_exists('phinit_localized_href')
                    ? (string) phinit_localized_href('/kategorie/' . rawurlencode($categorySlug), $currentLocale, $siteUrl)
                    : rtrim($siteUrl, '/') . '/kategorie/' . rawurlencode($categorySlug));
        }
        $postPermalinkRaw = (string) ($post['permalink'] ?? (function_exists('phinit_build_post_url') ? phinit_build_post_url($post, $currentLocale) : ($siteUrl . '/blog/' . $displaySlug)));
        $postPermalink = function_exists('phinit_safe_public_url')
            ? (phinit_safe_public_url($postPermalinkRaw, $siteUrl, ['http', 'https']) ?: '#')
            : $postPermalinkRaw;
        $postFeaturedImage = function_exists('phinit_normalize_public_media_url')
            ? phinit_normalize_public_media_url((string) ($post['featured_image'] ?? ''), false, $siteUrl)
            : (string) ($post['featured_image'] ?? '');
        if ($postFeaturedImage !== '' && function_exists('phinit_get_local_image_path') && function_exists('phinit_public_image_url_with_mtime')) {
            $_gridImageReference = (string) ($post['featured_image'] ?? $postFeaturedImage);
            $_gridImagePath = phinit_get_local_image_path($_gridImageReference !== '' ? $_gridImageReference : $postFeaturedImage);
            if ($_gridImagePath !== '') {
                $postFeaturedImage = phinit_public_image_url_with_mtime($postFeaturedImage, $_gridImagePath);
            }
        }
        ?>
        <article class="post-card">
            <?php
            $postDateRaw = $post['published_at'] ?? ($post['created_at'] ?? '');
            $tileAuthorName = !empty($_showTileAuthor) ? trim((string) ($post['author_name'] ?? '')) : '';
            $tileAuthorId = $tileAuthorName !== '' ? (int) ($post['author_id'] ?? 0) : 0;
            $tileAuthorLink = $tileAuthorName !== '' && function_exists('phinit_resolve_post_author_link')
                ? phinit_resolve_post_author_link($post, $currentLocale, $siteUrl)
                : ['url' => '', 'isExternal' => false];
            $tileAuthorUrl = $tileAuthorLink['url'];
            $tileAuthorUrlIsExternal = $tileAuthorLink['isExternal'];
            $tileReadTime = !empty($post['read_time']) ? (int) $post['read_time'] : 0;
                if ($tileReadTime < 1 && $displayContentSource !== '') {
                $tileReadTimeSource = function_exists('phinit_excerpt_plain_text')
                    ? phinit_excerpt_plain_text($displayContentSource)
                    : strip_tags($displayContentSource);
                $tileReadTime = function_exists('phinit_reading_time')
                    ? phinit_reading_time($tileReadTimeSource)
                    : max(1, (int) round(str_word_count($tileReadTimeSource) / 220));
            }
            ?>

            <?php if ($postFeaturedImage !== ''): ?>
            <div class="post-card-thumb">
                <img src="<?php echo htmlspecialchars($postFeaturedImage, ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars($displayTitle, ENT_QUOTES); ?>"
                     <?php echo phinit_image_loading_attributes(true, false); ?>
                     <?php echo phinit_image_dimension_attributes((string) ($post['featured_image'] ?? $postFeaturedImage), 320, 180); ?>>
            </div>
            <?php else: ?>
            <div class="post-card-thumb post-card-thumb--placeholder">
                <span class="post-card-thumb__icon">📄</span>
            </div>
            <?php endif; ?>

            <div class="post-card-body">
                <h3 class="post-card-title">
                    <a href="<?php echo htmlspecialchars($postPermalink, ENT_QUOTES); ?>">
                        <?php echo phinit_escape_text($displayTitle !== '' ? $displayTitle : 'Ohne Titel'); ?>
                    </a>
                </h3>
                <?php if ($tileAuthorName !== ''): ?>
                <div class="post-card-top-meta post-card-top-meta--author">
                    <?php if ($tileAuthorUrl !== ''): ?>
                    <a href="<?php echo htmlspecialchars($tileAuthorUrl, ENT_QUOTES); ?>" class="post-card-top-meta__author"<?php echo $tileAuthorUrlIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo phinit_escape_text($tileAuthorName); ?></a>
                    <?php else: ?>
                    <span class="post-card-top-meta__author"><?php echo phinit_escape_text($tileAuthorName); ?></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($_showTileDate) && !empty($postDateRaw)): ?>
                <div class="post-card-top-meta">
                    <span class="post-card-top-meta__date"><?php echo htmlspecialchars(phinit_format_date((string) $postDateRaw, 'long', $currentLocale), ENT_QUOTES); ?></span>
                    <?php if ($tileReadTime > 0): ?>
                    <span class="post-card-top-meta__read" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>
                <?php elseif ($tileReadTime > 0): ?>
                <div class="post-card-top-meta">
                    <span class="post-card-top-meta__read" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?></span>
                </div>
                <?php endif; ?>
                <?php
                $_tileExc = function_exists('phinit_excerpt_plain_text')
                    ? phinit_excerpt_plain_text($displayExcerptSource)
                    : strip_tags($displayExcerptSource);
                if (trim($_tileExc) === '' && $displayContentSource !== '') {
                    $_tileExc = function_exists('phinit_excerpt_plain_text')
                        ? phinit_excerpt_plain_text($displayContentSource)
                        : strip_tags($displayContentSource);
                }
                if (!empty($_showTileExc) && trim($_tileExc) !== ''): ?>
                <p class="post-card-excerpt"><?php echo htmlspecialchars(mb_strimwidth($_tileExc, 0, (int) $_tileExcLen, '…'), ENT_QUOTES); ?></p>
                <?php endif; ?>
                <div class="post-card-meta">
                    <div class="post-card-meta__left">
                        <?php if (!empty($_showTileCat) && $categoryLabel !== '' && $categoryUrl !== ''): ?>
                        <a class="cat" href="<?php echo htmlspecialchars($categoryUrl, ENT_QUOTES); ?>"><?php echo phinit_escape_text($categoryLabel); ?></a>
                        <?php elseif (!empty($_showTileCat) && $categoryLabel !== ''): ?>
                        <span class="cat"><?php echo phinit_escape_text($categoryLabel); ?></span>
                        <?php endif; ?>
                    </div>
                    <a class="post-card-meta__more"
                            href="<?php echo htmlspecialchars($postPermalink, ENT_QUOTES); ?>"
                              aria-label="<?php echo phinit_escape_text($displayTitle !== '' ? $displayTitle : 'Ohne Titel'); ?>">
                        <span class="post-card-meta__more-label post-card-meta__more-label--desktop"><?php echo htmlspecialchars(phinit_t('continue_reading', [], $currentLocale), ENT_QUOTES); ?></span>
                        <span class="post-card-meta__more-label post-card-meta__more-label--mobile"><?php echo htmlspecialchars(phinit_t('continue_reading', [], $currentLocale), ENT_QUOTES); ?></span>
                    </a>
                </div>
            </div>

        </article>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="Seitennavigation">
        <?php if ($currentPage > 1): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $currentPage - 1; ?>" class="page-link" aria-label="Vorherige Seite">← Zurück</a>
        <?php endif; ?>

        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
            <?php if ($page === 1 || $page === $totalPages || abs($page - $currentPage) <= 2): ?>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $page; ?>" class="page-link <?php echo $page === $currentPage ? 'active' : ''; ?>" <?php echo $page === $currentPage ? 'aria-current="page"' : ''; ?>><?php echo $page; ?></a>
            <?php elseif (abs($page - $currentPage) === 3): ?>
            <span class="page-link dots" aria-hidden="true">…</span>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $currentPage + 1; ?>" class="page-link" aria-label="Nächste Seite">Weiter →</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
</section>