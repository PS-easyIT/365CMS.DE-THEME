<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$featuredBannerPost = isset($featuredBannerPost) && is_array($featuredBannerPost) ? $featuredBannerPost : [];
$featuredBannerPosts = isset($featuredBannerPosts) && is_array($featuredBannerPosts)
    ? array_values(array_filter($featuredBannerPosts, static fn($post): bool => is_array($post) && $post !== []))
    : [];

if ($featuredBannerPosts === [] && $featuredBannerPost !== []) {
    $featuredBannerPosts = [$featuredBannerPost];
}

$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';

if (empty($_showFeaturedBanner) || $featuredBannerPosts === []) {
    return;
}

$isRotatingFeaturedBanner = count($featuredBannerPosts) > 1;
$rotationKey = implode('-', array_map(static fn(array $post): string => (string) ((int) ($post['id'] ?? 0)), $featuredBannerPosts));
$bannerLabel = trim((string) ($_featuredBannerLabel ?? 'Featured'));
$bannerButtonText = trim((string) ($_featuredBannerButtonText ?? 'Weiter lesen'));
?>
<section class="content-section home-section home-section--featured"
         data-anim
         data-anim-delay="1"
         <?php echo $isRotatingFeaturedBanner ? 'data-featured-banner-rotator data-rotation-key="' . htmlspecialchars($rotationKey, ENT_QUOTES) . '"' : ''; ?>>
    <?php foreach ($featuredBannerPosts as $bannerIndex => $featuredBannerPost): ?>
        <?php
        $isActiveBanner = $bannerIndex === 0;
        $bannerTitle = $isRotatingFeaturedBanner ? '' : trim((string) ($_featuredBannerTitle ?? ''));
        if ($bannerTitle === '') {
            $bannerTitle = trim((string) ($featuredBannerPost['title'] ?? ''));
        }
        if ($bannerTitle === '') {
            $bannerTitle = 'Featured';
        }

        $bannerText = $isRotatingFeaturedBanner ? '' : trim((string) ($_featuredBannerText ?? ''));
        if ($bannerText === '') {
            $bannerText = trim((string) ($featuredBannerPost['excerpt'] ?? ''));
        }
        if ($bannerText === '' && !empty($featuredBannerPost['content'])) {
            $bannerText = function_exists('phinit_excerpt_plain_text')
                ? phinit_excerpt_plain_text((string) $featuredBannerPost['content'])
                : strip_tags((string) $featuredBannerPost['content']);
        }

        $bannerHrefRaw = (string) ($featuredBannerPost['permalink'] ?? '');
        if ($bannerHrefRaw === '' && function_exists('phinit_build_post_url')) {
            $bannerHrefRaw = phinit_build_post_url($featuredBannerPost, $currentLocale);
        }
        $bannerHref = function_exists('phinit_safe_public_url')
            ? (phinit_safe_public_url($bannerHrefRaw, $siteUrl, ['http', 'https']) ?: '#')
            : ($bannerHrefRaw !== '' ? $bannerHrefRaw : '#');

        $bannerImage = function_exists('phinit_normalize_public_media_url')
            ? phinit_normalize_public_media_url((string) ($featuredBannerPost['featured_image'] ?? ''), false, $siteUrl)
            : (string) ($featuredBannerPost['featured_image'] ?? '');
        $bannerDateRaw = $featuredBannerPost['published_at'] ?? ($featuredBannerPost['created_at'] ?? '');
        ?>
    <article class="home-featured-banner<?php echo $bannerImage === '' ? ' home-featured-banner--no-media' : ''; ?><?php echo $isActiveBanner ? ' is-active' : ''; ?>"
             data-featured-banner-slide
             aria-hidden="<?php echo $isActiveBanner ? 'false' : 'true'; ?>"
             <?php echo $isActiveBanner ? '' : 'hidden'; ?>>
        <?php if ($bannerImage !== ''): ?>
        <a href="<?php echo htmlspecialchars($bannerHref, ENT_QUOTES); ?>" class="home-featured-banner__media" aria-label="<?php echo htmlspecialchars($bannerTitle, ENT_QUOTES); ?>">
            <img src="<?php echo htmlspecialchars($bannerImage, ENT_QUOTES); ?>"
                 alt="<?php echo htmlspecialchars($bannerTitle, ENT_QUOTES); ?>"
                 <?php echo phinit_image_loading_attributes(true, true); ?>
                 <?php echo phinit_image_dimension_attributes($bannerImage, 180, 120); ?>>
        </a>
        <?php endif; ?>
        <div class="home-featured-banner__body">
            <?php if ($bannerLabel !== ''): ?>
            <span class="home-featured-banner__label"><?php echo htmlspecialchars($bannerLabel, ENT_QUOTES); ?></span>
            <?php endif; ?>
            <h2 class="home-featured-banner__title">
                <a href="<?php echo htmlspecialchars($bannerHref, ENT_QUOTES); ?>"><?php echo htmlspecialchars($bannerTitle, ENT_QUOTES); ?></a>
            </h2>
            <?php if ($bannerText !== ''): ?>
            <p class="home-featured-banner__text"><?php echo htmlspecialchars(mb_strimwidth($bannerText, 0, 220, '…'), ENT_QUOTES); ?></p>
            <?php endif; ?>
            <div class="home-featured-banner__footer">
                <?php if (!empty($bannerDateRaw)): ?>
                <time datetime="<?php echo htmlspecialchars(date('c', strtotime((string) $bannerDateRaw)), ENT_QUOTES); ?>">
                    <?php echo htmlspecialchars(phinit_format_date((string) $bannerDateRaw, 'long', $currentLocale), ENT_QUOTES); ?>
                </time>
                <?php endif; ?>
                <?php if ($bannerButtonText !== ''): ?>
                <a href="<?php echo htmlspecialchars($bannerHref, ENT_QUOTES); ?>" class="home-featured-banner__button">
                    <?php echo htmlspecialchars($bannerButtonText, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php endforeach; ?>
</section>
