<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$featuredPosts = isset($featuredPosts) && is_array($featuredPosts) ? $featuredPosts : [];
$sbFeaturedPosts = isset($sbFeaturedPosts) && is_array($sbFeaturedPosts) ? $sbFeaturedPosts : [];
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$localizationService = class_exists('CMS\\Services\\ContentLocalizationService')
    ? \CMS\Services\ContentLocalizationService::getInstance()
    : null;

$_renderSidebarWidgetTitle = static function (string $title, string $defaultIcon = ''): string {
    $rawTitle = trim($title);
    $resolvedIcon = trim($defaultIcon);
    $resolvedText = $rawTitle;

    if ($rawTitle !== '' && preg_match('/^([^\p{L}\p{N}]+)\s*(.+)$/u', $rawTitle, $matches) === 1) {
        $candidateIcon = trim((string) ($matches[1] ?? ''));
        $candidateText = trim((string) ($matches[2] ?? ''));

        if ($candidateText !== '') {
            $resolvedIcon = $candidateIcon !== '' ? $candidateIcon : $resolvedIcon;
            $resolvedText = $candidateText;
        }
    }

    $iconHtml = $resolvedIcon !== ''
        ? '<span class="sb-widget-title__icon" aria-hidden="true">' . htmlspecialchars($resolvedIcon, ENT_QUOTES) . '</span>'
        : '';

    return '<div class="sb-widget-title">'
        . $iconHtml
        . '<span class="sb-widget-title__text">' . htmlspecialchars($resolvedText, ENT_QUOTES) . '</span>'
        . '</div>';
};

if (empty($_showList) || $featuredPosts === []) {
    return;
}
?>
<section class="content-section home-section home-section--list">
    <h2 class="visually-hidden"><?php echo htmlspecialchars((string) $_listLabel, ENT_QUOTES); ?></h2>
    <div class="section-header">
        <span class="section-label section-label--dark"><?php echo htmlspecialchars((string) $_listLabel, ENT_QUOTES); ?></span>
    </div>
    <?php if ($_showListSidebar): ?>
    <div class="homepage-list-with-sidebar">
    <div class="homepage-list-main">
    <?php endif; ?>
    <div class="article-list article-list--framed">
        <?php foreach ($featuredPosts as $postIndex => $post):
            $_articleImageEager = $postIndex < 2;
            get_theme_part('partials/post-card', [
                'card' => (array) $post,
                'siteUrl' => $siteUrl,
                'show_excerpt' => $_showExcerpt,
                'show_meta' => $_showMeta,
                'exc_len' => $_listExcLen,
                'show_cat' => $_showMetaCat,
                'show_date' => $_showMetaDate,
                'show_rt' => $_showMetaRT,
                'above_the_fold_image' => $_articleImageEager,
                'image_high_priority' => $postIndex === 0,
            ]);
        endforeach; ?>
    </div>

    <?php if ($_showListSidebar):
        $_sbFeaturedActive = $_sbShowFeaturedPosts && !empty($sbFeaturedPosts);
        $_sbFeatProjMode = $_sbFeaturedActive && $_sbShowProjects;
        $_sbFeatSocialMode = $_sbFeaturedActive && $_sbShowSocial;
        $_sbFeatSlice = array_slice($sbFeaturedPosts, 0, 6);
        $_sbEnableFeaturedRotation = count($_sbFeatSlice) > 2;
        $_sbFeaturedBadgeStyle = in_array((string) ($_sbFeaturedBadgeStyle ?? 'teal'), ['neutral', 'teal', 'gold', 'dark'], true)
            ? (string) $_sbFeaturedBadgeStyle
            : 'teal';
        $_sbFeaturedRotateSeconds = max(2, min(60, (int) ($_sbFeaturedRotateSeconds ?? 6)));
        $_sbFeaturedRotateInterval = $_sbFeaturedRotateSeconds * 1000;
        $_sbFeaturedTitleSize = max(10, min(20, (float) ($_sbFeaturedTitleSize ?? 12.5)));
        $_sbFeaturedBadgeSize = max(8, min(18, (float) ($_sbFeaturedBadgeSize ?? 10)));
        $_sbFeaturedImageLayout = in_array((string) ($_sbFeaturedImageLayout ?? 'auto'), ['auto', 'side', 'below'], true)
            ? (string) $_sbFeaturedImageLayout
            : 'auto';
        $_sbFeaturedHasCustomImages = array_any(
            $_sbFeatSlice,
            static fn(mixed $_sbFeatCandidate): bool => is_array($_sbFeatCandidate)
                && trim((string) ($_sbFeatCandidate['custom_sidebar_image'] ?? '')) !== ''
        );
        $_sbResolvedFeaturedImageLayout = $_sbFeaturedImageLayout;
        if ($_sbResolvedFeaturedImageLayout === 'auto') {
            $_sbResolvedFeaturedImageLayout = ($_sbFeatProjMode || $_sbFeatSocialMode) ? 'side' : 'below';
        }
        $_sbAsideClass = 'homepage-list-sidebar'
            . ($_sbFeaturedActive ? ' homepage-list-sidebar--feat' : '')
            . ($_sbFeatProjMode ? ' homepage-list-sidebar--feat-proj' : '')
            . ($_sbFeatSocialMode ? ' homepage-list-sidebar--feat-social' : '');
        $_projectCards = [
            [$_sbProj1Name, $_sbProj1Desc, $_sbProj1Url, $_sbProj1LogoUrl],
            [$_sbProj2Name, $_sbProj2Desc, $_sbProj2Url, $_sbProj2LogoUrl],
        ];
        $_sbDefaultOrder = ['identity', 'carousel', 'quicklinks', 'm365links', 'contact', 'about', 'projects', 'featured', 'status', 'downloads', 'newsletter', 'social', 'notice', 'custom'];
        $_sbOrderTokens = array_values(array_filter(array_map(
            static fn(string $item): string => strtolower(trim($item)),
            preg_split('/[\r\n,;|]+/', (string) ($_sbWidgetOrder ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: []
        ), static fn(string $item): bool => in_array($item, $_sbDefaultOrder, true)));
        $_sbOrderTokens = array_values(array_unique($_sbOrderTokens));
        foreach ($_sbDefaultOrder as $_sbDefaultIndex => $_sbDefaultWidgetKey) {
            if (in_array($_sbDefaultWidgetKey, $_sbOrderTokens, true)) {
                continue;
            }

            $_sbInsertBefore = null;
            for ($_sbNextIndex = $_sbDefaultIndex + 1, $_sbDefaultCount = count($_sbDefaultOrder); $_sbNextIndex < $_sbDefaultCount; $_sbNextIndex++) {
                $_sbCandidateKey = $_sbDefaultOrder[$_sbNextIndex];
                $_sbCandidatePosition = array_search($_sbCandidateKey, $_sbOrderTokens, true);

                if ($_sbCandidatePosition !== false) {
                    $_sbInsertBefore = (int) $_sbCandidatePosition;
                    break;
                }
            }

            if ($_sbInsertBefore === null) {
                $_sbOrderTokens[] = $_sbDefaultWidgetKey;
                continue;
            }

            array_splice($_sbOrderTokens, $_sbInsertBefore, 0, [$_sbDefaultWidgetKey]);
        }
        $_sbOrderMap = array_flip($_sbOrderTokens);
        $_sbWidgetOrderStyle = static function (string $widgetKey) use ($_sbOrderMap): string {
            $order = (int) ($_sbOrderMap[$widgetKey] ?? 99);

            return ' style="order: ' . $order . ';"';
        };
        $_sbCarouselPosts = !empty($_sbShowArticleCarousel)
            ? array_slice($featuredPosts, 0, max(2, min(6, (int) ($_sbArticleCarouselCount ?? 4))))
            : [];
        $_sbCarouselActive = count($_sbCarouselPosts) > 0;
        $_sbCarouselRotateInterval = max(3, min(30, (int) ($_sbArticleCarouselRotateSeconds ?? 7))) * 1000;
        $_sbCarouselImageHeight = max(72, min(150, (int) ($_sbArticleCarouselImageHeight ?? 96)));
    ?>
    </div><!-- /.homepage-list-main -->
    <aside class="<?php echo $_sbAsideClass; ?>">

        <?php if ($_sbShowIdentity && (!empty($_sbIdentityLogoUrl) || !empty($_sbIdentityTagline) || !empty($_sbIdentityBadgeText))): ?>
        <div class="sb-widget sb-widget--identity"<?php echo $_sbWidgetOrderStyle('identity'); ?>>
            <?php $_idLink = !empty($_sbIdentityLinkUrl) ? (string) $_sbIdentityLinkUrl : '/'; ?>
            <?php $_idHref = function_exists('phinit_safe_public_url') ? (phinit_safe_public_url($_idLink, $siteUrl, ['http', 'https']) ?: '/') : $_idLink; ?>
            <?php $_sbIdentitySiteName = trim((string) (defined('SITE_NAME') ? SITE_NAME : '')); ?>
            <?php $_sbIdentityAriaBase = $_sbIdentitySiteName !== '' ? $_sbIdentitySiteName : 'Startseite'; ?>
            <?php $_sbIdentityAriaLabel = empty($_sbIdentityTagline)
                ? (function_exists('phinit_t')
                    ? phinit_t('site_home_aria', ['site' => $_sbIdentityAriaBase], $currentLocale)
                    : $_sbIdentityAriaBase)
                : ''; ?>
            <?php $_sbIdentityLogoReference = (string) ($_sbIdentityLogoUrl ?? ''); ?>
            <?php $_sbIdentityLogoSrc = function_exists('phinit_normalize_public_media_url') ? phinit_normalize_public_media_url($_sbIdentityLogoReference, false, $siteUrl) : $_sbIdentityLogoReference; ?>
            <?php $_sbIdentityLogoSources = function_exists('phinit_get_picture_sources')
                ? phinit_get_picture_sources($_sbIdentityLogoReference !== '' ? $_sbIdentityLogoReference : $_sbIdentityLogoSrc, $siteUrl, 130, 42)
                : [
                    'url' => $_sbIdentityLogoSrc,
                    'webp_url' => '',
                    'width' => 130,
                    'height' => 42,
                ]; ?>
            <a href="<?php echo htmlspecialchars($_idHref, ENT_QUOTES); ?>" class="sb-identity<?php echo !empty($_sbIdentityBadgeText) ? ' sb-identity--has-badge' : ''; ?>"<?php echo $_sbIdentityAriaLabel !== '' ? ' aria-label="' . htmlspecialchars($_sbIdentityAriaLabel, ENT_QUOTES) . '"' : ''; ?>>
                <?php if ($_sbIdentityLogoSrc !== '' || !empty($_sbIdentityBadgeText)): ?>
                <span class="sb-identity-brand">
                <?php if ($_sbIdentityLogoSrc !== ''): ?>
                <picture>
                    <?php if (($_sbIdentityLogoSources['webp_url'] ?? '') !== ''): ?>
                    <source srcset="<?php echo htmlspecialchars((string) ($_sbIdentityLogoSources['webp_url'] ?? ''), ENT_QUOTES); ?>" type="image/webp">
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars((string) ($_sbIdentityLogoSources['url'] ?? $_sbIdentityLogoSrc), ENT_QUOTES); ?>"
                         alt="" aria-hidden="true" class="sb-identity-logo" <?php echo phinit_image_loading_attributes(); ?> <?php echo phinit_image_dimension_attributes($_sbIdentityLogoReference !== '' ? $_sbIdentityLogoReference : $_sbIdentityLogoSrc, 130, 42); ?>>
                </picture>
                <?php endif; ?>
                <?php if (!empty($_sbIdentityBadgeText)): ?>
                <span class="sb-identity-badge"><?php echo htmlspecialchars((string) $_sbIdentityBadgeText, ENT_QUOTES); ?></span>
                <?php endif; ?>
                </span>
                <?php endif; ?>
                <?php if (!empty($_sbIdentityTagline)): ?>
                <span class="sb-identity-tagline"><?php echo htmlspecialchars($_sbIdentityTagline, ENT_QUOTES); ?></span>
                <?php endif; ?>
            </a>
        </div>
        <?php endif; ?>

        <?php if ($_sbCarouselActive): ?>
        <div class="sb-widget sb-widget--carousel"
             style="order: <?php echo (int) ($_sbOrderMap['carousel'] ?? 99); ?>; --sb-carousel-image-h: <?php echo (int) $_sbCarouselImageHeight; ?>px;"
             data-sidebar-carousel
             data-rotate-interval="<?php echo (int) $_sbCarouselRotateInterval; ?>">
            <?php echo $_renderSidebarWidgetTitle((string) ($_sbArticleCarouselLabel ?? 'Artikel-Karussell'), '📰'); ?>
            <div class="sb-carousel-track" aria-live="polite">
                <?php foreach ($_sbCarouselPosts as $_carouselIndex => $_carouselPost):
                    $_carouselPost = is_array($_carouselPost) ? $_carouselPost : [];
                    $_carouselIndex = (int) $_carouselIndex;
                    $_carouselIsActive = $_carouselIndex === 0;
                    $_carouselHrefRaw = (string) ($_carouselPost['permalink'] ?? (function_exists('phinit_build_post_url') ? phinit_build_post_url($_carouselPost, $currentLocale) : ($siteUrl . '/blog/' . ($_carouselPost['slug'] ?? ''))));
                    $_carouselHref = function_exists('phinit_safe_public_url')
                        ? (phinit_safe_public_url($_carouselHrefRaw, $siteUrl, ['http', 'https']) ?: '#')
                        : $_carouselHrefRaw;
                    $_carouselTitleRaw = (string) ($_carouselPost['title'] ?? '');
                    $_carouselTitle = htmlspecialchars($_carouselTitleRaw, ENT_QUOTES);
                    $_carouselThumb = !empty($_carouselPost['featured_image'])
                        ? (function_exists('phinit_normalize_public_media_url')
                            ? phinit_normalize_public_media_url((string) $_carouselPost['featured_image'], false, $siteUrl)
                            : (string) $_carouselPost['featured_image'])
                        : '';
                    $_carouselCat = trim((string) ($_carouselPost['category_name'] ?? ''));
                    $_carouselDateRaw = $_carouselPost['published_at'] ?? ($_carouselPost['created_at'] ?? '');
                    $_carouselDate = !empty($_carouselDateRaw) ? date('j. M Y', strtotime((string) $_carouselDateRaw)) : '';
                    $_carouselDateIso = !empty($_carouselDateRaw) ? date('c', strtotime((string) $_carouselDateRaw)) : '';
                ?>
                <article class="sb-carousel-slide<?php echo $_carouselIsActive ? ' is-active' : ''; ?>"
                         data-sidebar-carousel-slide
                         data-slide-index="<?php echo $_carouselIndex; ?>"
                         aria-hidden="<?php echo $_carouselIsActive ? 'false' : 'true'; ?>">
                    <a href="<?php echo htmlspecialchars($_carouselHref, ENT_QUOTES); ?>" class="sb-carousel-link" tabindex="<?php echo $_carouselIsActive ? '0' : '-1'; ?>">
                        <?php if ($_carouselThumb !== ''): ?>
                        <img src="<?php echo htmlspecialchars($_carouselThumb, ENT_QUOTES); ?>"
                             alt="<?php echo $_carouselTitle; ?>"
                             class="sb-carousel-image" <?php echo phinit_image_loading_attributes(); ?> width="260" height="<?php echo (int) $_sbCarouselImageHeight; ?>">
                        <?php else: ?>
                        <div class="sb-carousel-image sb-carousel-image--placeholder" aria-hidden="true">
                            <?php echo htmlspecialchars(mb_substr(strip_tags($_carouselTitleRaw !== '' ? $_carouselTitleRaw : '?'), 0, 1), ENT_QUOTES); ?>
                        </div>
                        <?php endif; ?>
                        <div class="sb-carousel-content">
                            <?php if ($_carouselCat !== ''): ?>
                            <span class="sb-carousel-kicker"><?php echo htmlspecialchars($_carouselCat, ENT_QUOTES); ?></span>
                            <?php endif; ?>
                            <h3 class="sb-carousel-title"><?php echo $_carouselTitle; ?></h3>
                            <?php if ($_carouselDate !== ''): ?>
                            <time class="sb-carousel-date" datetime="<?php echo htmlspecialchars($_carouselDateIso, ENT_QUOTES); ?>"><?php echo htmlspecialchars($_carouselDate, ENT_QUOTES); ?></time>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
            <?php if (count($_sbCarouselPosts) > 1): ?>
            <div class="sb-carousel-controls" aria-label="Artikel-Karussell steuern">
                <button type="button" class="sb-carousel-btn" data-sidebar-carousel-prev aria-label="Vorheriger Artikel">‹</button>
                <div class="sb-carousel-dots" aria-label="Artikel auswählen">
                    <?php foreach ($_sbCarouselPosts as $_carouselDotIndex => $_carouselDotPost): ?>
                    <button type="button"
                            class="sb-carousel-dot<?php echo (int) $_carouselDotIndex === 0 ? ' is-active' : ''; ?>"
                            data-sidebar-carousel-dot
                            data-slide-target="<?php echo (int) $_carouselDotIndex; ?>"
                            aria-label="Artikel <?php echo (int) $_carouselDotIndex + 1; ?> anzeigen"
                            aria-pressed="<?php echo (int) $_carouselDotIndex === 0 ? 'true' : 'false'; ?>"></button>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="sb-carousel-btn" data-sidebar-carousel-next aria-label="Nächster Artikel">›</button>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($_sbShowQuicklinks) && trim((string) ($_sbQuicklinksItems ?? '')) !== ''): ?>
        <?php
        $_sbQuicklinkRows = [];
        foreach (array_filter(array_map('trim', preg_split('/\R+/', (string) $_sbQuicklinksItems) ?: [])) as $_quicklinkLine) {
            $_quicklinkParts = array_map('trim', explode('|', $_quicklinkLine, 3));
            $_quicklinkLabel = (string) ($_quicklinkParts[0] ?? '');
            $_quicklinkUrl = (string) ($_quicklinkParts[1] ?? '');
            $_quicklinkIcon = (string) ($_quicklinkParts[2] ?? '↗');

            if ($_quicklinkLabel === '' || $_quicklinkUrl === '') {
                continue;
            }

            $_quicklinkHref = function_exists('phinit_safe_public_url')
                ? phinit_safe_public_url($_quicklinkUrl, $siteUrl, ['http', 'https'])
                : $_quicklinkUrl;

            if ($_quicklinkHref === '') {
                continue;
            }

            $_sbQuicklinkRows[] = [$_quicklinkLabel, $_quicklinkHref, $_quicklinkIcon];
        }
        ?>
        <?php if ($_sbQuicklinkRows !== []): ?>
        <div class="sb-widget sb-widget--quicklinks"<?php echo $_sbWidgetOrderStyle('quicklinks'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) ($_sbQuicklinksLabel ?? 'Schnelllinks'), '⚡'); ?>
            <nav class="sb-quicklinks" aria-label="<?php echo htmlspecialchars((string) ($_sbQuicklinksLabel ?? 'Schnelllinks'), ENT_QUOTES); ?>">
                <?php foreach ($_sbQuicklinkRows as [$_quicklinkLabel, $_quicklinkHref, $_quicklinkIcon]): ?>
                <?php $_quicklinkIsExternal = preg_match('#^https?://#i', (string) $_quicklinkHref) === 1; ?>
                <a href="<?php echo htmlspecialchars((string) $_quicklinkHref, ENT_QUOTES); ?>" class="sb-quicklink"<?php echo $_quicklinkIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <span class="sb-quicklink__icon" aria-hidden="true"><?php echo htmlspecialchars((string) $_quicklinkIcon, ENT_QUOTES); ?></span>
                    <span class="sb-quicklink__label"><?php echo htmlspecialchars((string) $_quicklinkLabel, ENT_QUOTES); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($_sbShowM365Links) && trim((string) ($_sbM365LinksItems ?? '')) !== ''): ?>
        <?php
        $_sbM365LinkRows = [];
        foreach (array_filter(array_map('trim', preg_split('/\R+/', (string) $_sbM365LinksItems) ?: [])) as $_m365LinkLine) {
            $_m365LinkParts = array_map('trim', explode('|', $_m365LinkLine, 3));
            $_m365LinkLabel = (string) ($_m365LinkParts[0] ?? '');
            $_m365LinkUrl = (string) ($_m365LinkParts[1] ?? '');
            $_m365LinkIcon = array_key_exists(2, $_m365LinkParts) ? (string) $_m365LinkParts[2] : '↗';

            if ($_m365LinkLabel === '' || $_m365LinkUrl === '') {
                continue;
            }

            $_m365LinkHref = function_exists('phinit_safe_public_url')
                ? phinit_safe_public_url($_m365LinkUrl, $siteUrl, ['http', 'https'])
                : $_m365LinkUrl;

            if ($_m365LinkHref === '') {
                continue;
            }

            $_sbM365LinkRows[] = [$_m365LinkLabel, $_m365LinkHref, $_m365LinkIcon];
        }
        ?>
        <?php if ($_sbM365LinkRows !== []): ?>
        <div class="sb-widget sb-widget--m365links"<?php echo $_sbWidgetOrderStyle('m365links'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) ($_sbM365LinksLabel ?? 'Microsoft 365'), '☁️'); ?>
            <nav class="sb-quicklinks sb-quicklinks--m365" aria-label="<?php echo htmlspecialchars((string) ($_sbM365LinksLabel ?? 'Microsoft 365'), ENT_QUOTES); ?>">
                <?php foreach ($_sbM365LinkRows as [$_m365LinkLabel, $_m365LinkHref, $_m365LinkIcon]): ?>
                <?php $_m365LinkIsExternal = preg_match('#^https?://#i', (string) $_m365LinkHref) === 1; ?>
                <a href="<?php echo htmlspecialchars((string) $_m365LinkHref, ENT_QUOTES); ?>" class="sb-quicklink sb-quicklink--m365<?php echo $_m365LinkIcon === '' ? ' sb-quicklink--no-icon' : ''; ?>"<?php echo $_m365LinkIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <?php if ($_m365LinkIcon !== ''): ?>
                    <span class="sb-quicklink__icon" aria-hidden="true"><?php echo htmlspecialchars((string) $_m365LinkIcon, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <span class="sb-quicklink__label"><?php echo htmlspecialchars((string) $_m365LinkLabel, ENT_QUOTES); ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($_sbShowContact) && (!empty($_sbContactText) || !empty($_sbContactButtonText))): ?>
        <?php $_sbContactHref = function_exists('phinit_safe_public_url') ? (phinit_safe_public_url((string) ($_sbContactUrl ?? '/contact'), $siteUrl, ['http', 'https']) ?: '/contact') : (string) ($_sbContactUrl ?? '/contact'); ?>
        <?php $_sbContactIsExternal = preg_match('#^https?://#i', $_sbContactHref) === 1; ?>
        <div class="sb-widget sb-widget--contact"<?php echo $_sbWidgetOrderStyle('contact'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) ($_sbContactTitle ?? 'Kontakt'), '✉️'); ?>
            <?php if (!empty($_sbContactText)): ?>
            <p class="sb-contact-text"><?php echo nl2br(htmlspecialchars((string) $_sbContactText, ENT_QUOTES)); ?></p>
            <?php endif; ?>
            <?php if ($_sbContactHref !== '' && !empty($_sbContactButtonText)): ?>
            <a href="<?php echo htmlspecialchars($_sbContactHref, ENT_QUOTES); ?>" class="sb-widget-button sb-widget-button--primary"<?php echo $_sbContactIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <?php echo htmlspecialchars((string) $_sbContactButtonText, ENT_QUOTES); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($_sbShowAboutMe) && (!empty($_sbAboutName) || !empty($_sbAboutImageUrl) || !empty($_sbAboutText))): ?>
        <div class="sb-widget sb-widget--about"<?php echo $_sbWidgetOrderStyle('about'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) ($_sbAboutTitle ?? 'About Me'), '👤'); ?>
            <?php $_sbAboutImageSrc = function_exists('phinit_normalize_public_media_url') ? phinit_normalize_public_media_url((string) $_sbAboutImageUrl, false, $siteUrl) : (string) $_sbAboutImageUrl; ?>
            <div class="sb-about-card">
                <?php if ($_sbAboutImageSrc !== ''): ?>
                <img src="<?php echo htmlspecialchars($_sbAboutImageSrc, ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars((string) ($_sbAboutName ?: $_sbAboutTitle ?: 'About Me'), ENT_QUOTES); ?>"
                     class="sb-about-image" <?php echo phinit_image_loading_attributes(); ?> <?php echo phinit_image_dimension_attributes((string) $_sbAboutImageUrl, 72, 72); ?>>
                <?php endif; ?>
                <div class="sb-about-body">
                    <?php if (!empty($_sbAboutName)): ?>
                    <strong class="sb-about-name"><?php echo htmlspecialchars((string) $_sbAboutName, ENT_QUOTES); ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($_sbAboutText)): ?>
                    <p class="sb-about-text"><?php echo nl2br(htmlspecialchars((string) $_sbAboutText, ENT_QUOTES)); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($_sbAboutLinkUrl) && !empty($_sbAboutLinkText)): ?>
                    <a href="<?php echo htmlspecialchars((string) $_sbAboutLinkUrl, ENT_QUOTES); ?>" class="sb-about-link">
                        <?php echo htmlspecialchars((string) $_sbAboutLinkText, ENT_QUOTES); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($_sbFeatProjMode): ?>
        <div class="sb-widget sb-widget--projects sb-widget--projects-top"<?php echo $_sbWidgetOrderStyle('projects'); ?>>
            <?php echo $_renderSidebarWidgetTitle('Unsere Projekte', '🚀'); ?>
            <div class="sb-project-cards-grid">
            <?php foreach ($_projectCards as [$pName, $pDesc, $pUrl, $pLogo]):
                if (empty($pName) || empty($pUrl)) {
                    continue;
                }
                $_pHref = function_exists('phinit_safe_public_url')
                    ? phinit_safe_public_url((string) $pUrl, $siteUrl, ['http', 'https'])
                    : (string) $pUrl;
                if ($_pHref === '') {
                    continue;
                }
                $_pLogoSrc = function_exists('phinit_normalize_public_media_url')
                    ? phinit_normalize_public_media_url((string) $pLogo, false, $siteUrl)
                    : (string) $pLogo;
                $_pInitials = mb_strtoupper(mb_substr(preg_replace('/[^a-z0-9]/iu', '', strip_tags((string) $pName)), 0, 2));
                $_pCardClass = $_pLogoSrc !== ''
                    ? ((string) $pLogo === (string) $_sbProj1LogoUrl ? ' sb-project-card--project1' : ((string) $pLogo === (string) $_sbProj2LogoUrl ? ' sb-project-card--project2' : ''))
                    : '';
                $_pCardStyle = $_pLogoSrc !== ''
                    ? '--sb-project-card-image: url(\'' . htmlspecialchars($_pLogoSrc, ENT_QUOTES) . '\');'
                    : '';
            ?>
            <a href="<?php echo htmlspecialchars($_pHref, ENT_QUOTES); ?>"
               class="sb-project-card<?php echo $_pCardClass; ?>"
               <?php if ($_pCardStyle !== ''): ?>style="<?php echo $_pCardStyle; ?>"<?php endif; ?>
               target="_blank" rel="noopener noreferrer">
                <?php if ($_pLogoSrc === ''): ?>
                <div class="sb-project-placeholder-bg"
                     data-initials="<?php echo htmlspecialchars($_pInitials ?: '?', ENT_QUOTES); ?>"
                     aria-hidden="true"></div>
                <?php endif; ?>
                <div class="sb-project-body">
                    <strong class="sb-project-name"><?php echo htmlspecialchars((string) $pName, ENT_QUOTES); ?></strong>
                    <?php if (!empty($pDesc)): ?>
                    <span class="sb-project-desc"><?php echo htmlspecialchars((string) $pDesc, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($_sbShowFeaturedPosts && !empty($sbFeaturedPosts)): ?>
        <div class="sb-widget sb-widget--featured sb-widget--featured-badge-style-<?php echo htmlspecialchars($_sbFeaturedBadgeStyle, ENT_QUOTES); ?><?php echo $_sbEnableFeaturedRotation ? ' sb-widget--featured-rotating' : ''; ?>"
                                 style="order: <?php echo (int) ($_sbOrderMap['featured'] ?? 99); ?>; --sb-featured-title-size: <?php echo htmlspecialchars(number_format($_sbFeaturedTitleSize, 1, '.', ''), ENT_QUOTES); ?>px; --sb-featured-badge-size: <?php echo htmlspecialchars(number_format($_sbFeaturedBadgeSize, 1, '.', ''), ENT_QUOTES); ?>px;"
               <?php if ($_sbEnableFeaturedRotation): ?>data-featured-rotator data-rotate-interval="<?php echo (int) $_sbFeaturedRotateInterval; ?>"<?php endif; ?>>
            <?php echo $_renderSidebarWidgetTitle((string) $_sbFeaturedPostsLabel, '📌'); ?>
            <?php if ($_sbEnableFeaturedRotation): ?>
            <div class="sb-featured-rotator" aria-live="polite">
            <?php endif; ?>
            <?php foreach ($_sbFeatSlice as $_fpIndex => $_fp):
                $_fp = is_array($_fp) ? $_fp : [];
                if ($localizationService !== null) {
                    try {
                        $_fp = $localizationService->localizePost($_fp, $currentLocale);
                    } catch (\Throwable $_sidebarLocalizationError) {
                        // Fallback: Widget bleibt mit Originaldaten renderbar.
                    }
                }
                $_fpIndex = (int) $_fpIndex;
                $_fpIsActive = $_fpIndex === 0;
                $_fpHrefRaw = (string) ($_fp['permalink'] ?? (function_exists('phinit_build_post_url') ? phinit_build_post_url($_fp, $currentLocale) : ($siteUrl . '/blog/' . ($_fp['slug'] ?? ''))));
                $_fpHref = function_exists('phinit_safe_public_url')
                    ? (phinit_safe_public_url($_fpHrefRaw, $siteUrl, ['http', 'https']) ?: '#')
                    : $_fpHrefRaw;
                $_fpTitle = htmlspecialchars((string) ($_fp['title'] ?? ''), ENT_QUOTES);
                $_fpDateRaw = $_fp['published_at'] ?? ($_fp['created_at'] ?? '');
                $_fpDate = !empty($_fpDateRaw) ? date('j. M Y', strtotime((string) $_fpDateRaw)) : '';
                $_fpDateIso = !empty($_fpDateRaw) ? date('c', strtotime((string) $_fpDateRaw)) : '';
                $_fpCat = htmlspecialchars((string) ($_fp['category_name'] ?? ''), ENT_QUOTES);
                $_fpCustomThumb = !empty($_fp['custom_sidebar_image'])
                    ? (function_exists('phinit_normalize_public_media_url')
                        ? phinit_normalize_public_media_url((string) $_fp['custom_sidebar_image'], false, $siteUrl)
                        : (string) $_fp['custom_sidebar_image'])
                    : '';
                $_fpHasCustomThumb = $_fpCustomThumb !== '' || (string) ($_fp['sidebar_image_source'] ?? '') === 'custom';
                $_fpThumb = $_fpHasCustomThumb
                    ? $_fpCustomThumb
                    : (!empty($_fp['featured_image'])
                        ? (function_exists('phinit_normalize_public_media_url')
                            ? phinit_normalize_public_media_url((string) $_fp['featured_image'], false, $siteUrl)
                            : (string) $_fp['featured_image'])
                        : '');
                $_fpThumbWidth = 64;
                $_fpThumbHeight = 48;
                $_fpThumbReference = $_fpHasCustomThumb
                    ? (string) ($_fp['custom_sidebar_image'] ?? '')
                    : (string) ($_fp['featured_image'] ?? '');
                $_fpThumbSources = function_exists('phinit_get_thumbnail_picture_sources')
                    ? phinit_get_thumbnail_picture_sources(
                        $_fpThumbReference !== '' ? $_fpThumbReference : $_fpThumb,
                        $siteUrl,
                        $_fpThumbWidth,
                        $_fpThumbHeight,
                        'crop'
                    )
                    : [
                        'url' => $_fpThumb,
                        'avif_url' => '',
                        'webp_url' => '',
                        'width' => $_fpThumbWidth,
                        'height' => $_fpThumbHeight,
                    ];
                $_fpThumbPreferredUrl = trim((string) ($_fpThumbSources['avif_url'] ?? ''));
                if ($_fpThumbPreferredUrl === '') {
                    $_fpThumbPreferredUrl = trim((string) ($_fpThumbSources['webp_url'] ?? ''));
                }
                if ($_fpThumbPreferredUrl === '') {
                    $_fpThumbPreferredUrl = trim((string) ($_fpThumbSources['url'] ?? $_fpThumb));
                }
            ?>
            <a href="<?php echo htmlspecialchars($_fpHref, ENT_QUOTES); ?>"
               class="sb-featured-post<?php echo $_sbEnableFeaturedRotation ? ' sb-featured-post--slide' : ''; ?><?php echo $_fpIsActive ? ' is-active' : ''; ?>"
               <?php if ($_sbEnableFeaturedRotation): ?>data-featured-slide data-slide-index="<?php echo $_fpIndex; ?>" aria-hidden="<?php echo $_fpIsActive ? 'false' : 'true'; ?>" tabindex="<?php echo $_fpIsActive ? '0' : '-1'; ?>"<?php endif; ?>>
                <?php if ($_fpThumb !== ''): ?>
                <img src="<?php echo htmlspecialchars($_fpThumbPreferredUrl, ENT_QUOTES); ?>" alt=""
                     class="sb-featured-thumb<?php echo $_fpHasCustomThumb ? ' sb-featured-thumb--custom' : ''; ?>" <?php echo phinit_image_loading_attributes(); ?> width="<?php echo (int) $_fpThumbWidth; ?>" height="<?php echo (int) $_fpThumbHeight; ?>">
                <?php else: ?>
                <div class="sb-featured-thumb sb-featured-thumb--placeholder" aria-hidden="true">
                    <?php echo mb_substr(strip_tags((string) ($_fp['title'] ?? '?')), 0, 1); ?>
                </div>
                <?php endif; ?>
                <div class="sb-featured-body">
                    <span class="sb-featured-title-badge"><span class="sb-featured-title-badge__text"><?php echo $_fpTitle; ?></span></span>
                    <div class="sb-featured-body-inner">
                        <span class="sb-featured-title"><?php echo $_fpTitle; ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if ($_sbEnableFeaturedRotation): ?>
                <div class="sb-featured-rotator-controls" role="group" aria-label="Empfohlene Artikel steuern">
                    <button type="button"
                            class="sb-featured-rotator-arrow sb-featured-rotator-arrow--prev"
                            data-featured-prev
                            aria-label="Vorherigen Beitrag anzeigen">
                        <span aria-hidden="true">‹</span>
                    </button>
                <button type="button"
                            class="sb-featured-rotator-arrow sb-featured-rotator-arrow--next"
                            data-featured-next
                            aria-label="Nächsten Beitrag anzeigen">
                        <span aria-hidden="true">›</span>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!$_sbShowFeaturedPosts && $_sbShowProjects): ?>
        <div class="sb-widget sb-widget--projects"<?php echo $_sbWidgetOrderStyle('projects'); ?>>
            <?php echo $_renderSidebarWidgetTitle('Unsere Projekte', '🚀'); ?>
            <div class="sb-project-cards-grid">
            <?php foreach ($_projectCards as [$pName, $pDesc, $pUrl, $pLogo]):
                if (empty($pName) || empty($pUrl)) {
                    continue;
                }
                $_pHref = function_exists('phinit_safe_public_url')
                    ? phinit_safe_public_url((string) $pUrl, $siteUrl, ['http', 'https'])
                    : (string) $pUrl;
                if ($_pHref === '') {
                    continue;
                }
                $_pLogoSrc = function_exists('phinit_normalize_public_media_url')
                    ? phinit_normalize_public_media_url((string) $pLogo, false, $siteUrl)
                    : (string) $pLogo;
                $_pInitials = mb_strtoupper(mb_substr(preg_replace('/[^a-z0-9]/iu', '', strip_tags((string) $pName)), 0, 2));
                $_pCardClass = $_pLogoSrc !== ''
                    ? ((string) $pLogo === (string) $_sbProj1LogoUrl ? ' sb-project-card--project1' : ((string) $pLogo === (string) $_sbProj2LogoUrl ? ' sb-project-card--project2' : ''))
                    : '';
                $_pCardStyle = $_pLogoSrc !== ''
                    ? '--sb-project-card-image: url(\'' . htmlspecialchars($_pLogoSrc, ENT_QUOTES) . '\');'
                    : '';
            ?>
            <a href="<?php echo htmlspecialchars($_pHref, ENT_QUOTES); ?>"
               class="sb-project-card<?php echo $_pCardClass; ?>"
               <?php if ($_pCardStyle !== ''): ?>style="<?php echo $_pCardStyle; ?>"<?php endif; ?>
               target="_blank" rel="noopener noreferrer">
                <?php if ($_pLogoSrc === ''): ?>
                <div class="sb-project-placeholder-bg"
                     data-initials="<?php echo htmlspecialchars($_pInitials ?: '?', ENT_QUOTES); ?>"
                     aria-hidden="true"></div>
                <?php endif; ?>
                <div class="sb-project-body">
                    <strong class="sb-project-name"><?php echo htmlspecialchars((string) $pName, ENT_QUOTES); ?></strong>
                    <?php if (!empty($pDesc)): ?>
                    <span class="sb-project-desc"><?php echo htmlspecialchars((string) $pDesc, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($_sbShowStatus): ?>
        <div class="sb-widget sb-widget--status"<?php echo $_sbWidgetOrderStyle('status'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) $_sbStatusLabel, '🟢'); ?>
            <?php $_sbSvcLines = array_filter(array_map('trim', explode("\n", (string) $_sbStatusServices))); ?>
            <?php if (!empty($_sbSvcLines)): ?>
            <ul class="sb-status-list">
            <?php foreach ($_sbSvcLines as $_svcLine):
                $_svcParts = explode('|', $_svcLine, 3);
                if (empty($_svcParts[0])) {
                    continue;
                }
                $_svcName = trim($_svcParts[0]);
                $_svcUrl = trim($_svcParts[1] ?? '#');
                $_svcSafeUrl = function_exists('phinit_safe_public_url')
                    ? phinit_safe_public_url($_svcUrl, $siteUrl, ['http', 'https'])
                    : $_svcUrl;
                if ($_svcSafeUrl === '') {
                    continue;
                }
                $_svcShort = mb_strtoupper(mb_substr(trim($_svcParts[2] ?? $_svcParts[0]), 0, 4));
            ?>
            <li class="sb-status-row">
                <a href="<?php echo htmlspecialchars($_svcSafeUrl, ENT_QUOTES); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="sb-status-service-link" title="<?php echo htmlspecialchars($_svcName, ENT_QUOTES); ?> Status">
                    <span class="sb-status-icon"><?php echo htmlspecialchars($_svcShort, ENT_QUOTES); ?></span>
                    <span class="sb-status-name"><?php echo htmlspecialchars($_svcName, ENT_QUOTES); ?></span>
                </a>
                <span class="sb-status-dot sb-status-dot--ok" title="Betrieb normal"></span>
            </li>
            <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <div class="sb-status-row sb-status-row--empty">
                <span class="sb-status-dot sb-status-dot--ok"></span>
                <span class="sb-status-empty-text">Keine bekannten Störungen</span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($_sbShowDownloads && !empty(trim((string) $_sbDownloadsItems))): ?>
        <div class="sb-widget sb-widget--downloads"<?php echo $_sbWidgetOrderStyle('downloads'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) $_sbDownloadsLabel, '📥'); ?>
            <ul class="sb-download-list">
            <?php foreach (array_filter(array_map('trim', explode("\n", (string) $_sbDownloadsItems))) as $_dl):
                $_dlParts = explode('|', $_dl, 2);
                if (count($_dlParts) < 2 || empty(trim($_dlParts[1]))) {
                    continue;
                }
                [$_dlTitle, $_dlUrl] = $_dlParts;
                $_dlSafeUrl = function_exists('phinit_safe_public_url')
                    ? phinit_safe_public_url(trim((string) $_dlUrl), $siteUrl, ['http', 'https'])
                    : trim((string) $_dlUrl);
                if ($_dlSafeUrl === '') {
                    continue;
                }
            ?>
            <li><a href="<?php echo htmlspecialchars($_dlSafeUrl, ENT_QUOTES); ?>"
                   target="_blank" rel="noopener noreferrer">📄 <?php echo htmlspecialchars(trim($_dlTitle), ENT_QUOTES); ?></a></li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (!empty($_sbShowNewsletter) && (!empty($_sbNewsletterText) || !empty($_sbNewsletterButtonText))): ?>
        <?php $_sbNewsletterHref = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) ($_sbNewsletterUrl ?? ''), $siteUrl, ['http', 'https']) : (string) ($_sbNewsletterUrl ?? ''); ?>
        <?php $_sbNewsletterIsExternal = preg_match('#^https?://#i', $_sbNewsletterHref) === 1; ?>
        <div class="sb-widget sb-widget--newsletter"<?php echo $_sbWidgetOrderStyle('newsletter'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) ($_sbNewsletterTitle ?? 'Updates abonnieren'), '📬'); ?>
            <?php if (!empty($_sbNewsletterText)): ?>
            <p class="sb-newsletter-text"><?php echo nl2br(htmlspecialchars((string) $_sbNewsletterText, ENT_QUOTES)); ?></p>
            <?php endif; ?>
            <?php if ($_sbNewsletterHref !== '' && !empty($_sbNewsletterButtonText)): ?>
            <a href="<?php echo htmlspecialchars($_sbNewsletterHref, ENT_QUOTES); ?>" class="sb-widget-button"<?php echo $_sbNewsletterIsExternal ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <?php echo htmlspecialchars((string) $_sbNewsletterButtonText, ENT_QUOTES); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php
        $_sbSocialSvg = [
            'linkedin' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            'github' => '<svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/></svg>',
            'gitlab' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M23.6 9.6l-2.3-7.1a.75.75 0 0 0-1.43-.02l-1.96 6.05H6.09L4.13 2.48A.75.75 0 0 0 2.7 2.5L.4 9.6a1.56 1.56 0 0 0 .56 1.74l11.04 8.02 11.04-8.02A1.56 1.56 0 0 0 23.6 9.6ZM12 17.7 7.94 8.53h8.12L12 17.7Z"/></svg>',
            'twitter' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.736-8.838L1.254 2.25H8.08l4.259 5.629L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>',
            'mastodon' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.03-1.52.03-1.67.002-.512.167-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/></svg>',
            'youtube' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
            'xing' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M18.188 0c-.517 0-.741.325-.927.66 0 0-7.455 13.224-7.702 13.657.015.024 4.919 9.023 4.919 9.023.17.308.436.66.967.66h3.454c.211 0 .375-.078.463-.22.089-.151.089-.346-.009-.536l-4.879-8.916c-.004-.006-.004-.016 0-.022L22.139.756c.095-.191.097-.387.006-.535C22.056.078 21.894 0 21.686 0h-3.498zM3.648 4.74c-.211 0-.385.074-.473.216-.09.149-.078.339.02.527l2.308 4.031c.005.01.005.02 0 .029L2.19 15.85c-.09.172-.085.338.004.484.088.143.256.22.47.22h3.461c.518 0 .766-.348.945-.667l3.338-5.985c-.012-.02-2.303-4.055-2.303-4.055-.17-.31-.432-.648-.962-.648H3.648v-.43z"/></svg>',
            'rss' => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/></svg>',
        ];
        $_sbSocialList = array_filter([
            'linkedin' => ['url' => $_sbSocialLinkedin, 'label' => $_sbLabelLinkedin],
            'github' => ['url' => $_sbSocialGithub, 'label' => $_sbLabelGithub],
            'gitlab' => ['url' => $_sbSocialGitlab, 'label' => $_sbLabelGitlab],
            'twitter' => ['url' => $_sbSocialTwitter, 'label' => 'Twitter / X'],
            'mastodon' => ['url' => $_sbSocialMastodon, 'label' => 'Mastodon'],
            'youtube' => ['url' => $_sbSocialYoutube, 'label' => 'YouTube'],
            'xing' => ['url' => $_sbSocialXing, 'label' => 'XING'],
            'rss' => ['url' => $_sbSocialRss, 'label' => $_sbLabelRss],
        ], static fn($s) => !empty(trim((string) $s['url'])));
        $_sbSocialList = array_filter(array_map(
            static function (array $socialItem) use ($siteUrl): array {
                $socialItem['url'] = function_exists('phinit_safe_public_url')
                    ? phinit_safe_public_url((string) ($socialItem['url'] ?? ''), $siteUrl, ['http', 'https'])
                    : (string) ($socialItem['url'] ?? '');

                return $socialItem;
            },
            $_sbSocialList
        ), static fn(array $socialItem): bool => $socialItem['url'] !== '');
        if ($_sbShowSocial && !empty($_sbSocialList)): ?>
        <div class="sb-widget sb-widget--social"<?php echo $_sbWidgetOrderStyle('social'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) $_sbSocialLabel, '👥'); ?>
            <div class="sb-social-links">
                <?php foreach ($_sbSocialList as $_sbKey => $_sbS): ?>
                <a href="<?php echo htmlspecialchars((string) $_sbS['url'], ENT_QUOTES); ?>"
                   class="sb-social-link sb-social-link--<?php echo htmlspecialchars((string) $_sbKey, ENT_QUOTES); ?>"
                   target="_blank" rel="noopener noreferrer me"
                   aria-label="<?php echo htmlspecialchars((string) $_sbS['label'], ENT_QUOTES); ?>">
                    <?php echo $_sbSocialSvg[$_sbKey] ?? htmlspecialchars((string) $_sbS['label'], ENT_QUOTES); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($_sbShowNotice && !empty($_sbNoticeTitle)): ?>
        <div class="sb-widget sb-widget--notice"<?php echo $_sbWidgetOrderStyle('notice'); ?>>
            <?php echo $_renderSidebarWidgetTitle((string) $_sbNoticeTitle, '💡'); ?>
            <div class="sb-notice-body">
                <?php if (!empty($_sbNoticeText)): ?>
                <p class="sb-notice-text"><?php echo htmlspecialchars((string) $_sbNoticeText, ENT_QUOTES); ?></p>
                <?php endif; ?>
                <?php if (!empty($_sbNoticeUrl) && !empty($_sbNoticeUrlText)): ?>
                <?php $_sbNoticeHref = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $_sbNoticeUrl, $siteUrl, ['http', 'https']) : (string) $_sbNoticeUrl; ?>
                <?php if ($_sbNoticeHref !== ''): ?>
                <a href="<?php echo htmlspecialchars($_sbNoticeHref, ENT_QUOTES); ?>" class="sb-notice-link">
                    <?php echo htmlspecialchars((string) $_sbNoticeUrlText, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty(trim((string) $_listSidebarContent))): ?>
        <?php $_safeListSidebarContent = phinit_sanitize_renderable_content((string) $_listSidebarContent, 'default'); ?>
        <div class="sb-widget"<?php echo $_sbWidgetOrderStyle('custom'); ?>>
            <?php echo $_safeListSidebarContent; ?>
        </div>
        <?php endif; ?>

    </aside>
    </div><!-- /.homepage-list-with-sidebar -->
    <?php endif; ?>

</section>