<?php
/**
 * Statische Seite – Landing-Page-Template
 *
 * Template-ID: page-landing
 * Layout:
 *  - Vollbreite Hero-Sektion mit Subtitle und CTA-Buttons
 *  - Feature-Cards-Grid (aus $page['meta']['features'])
 *  - Optionaler Freitext-Block (normaler $page['content'])
 *  - Optionaler CTA-Banner am Ende
 *  - Keine Sidebar
 *
 * Pflichtfeld-Schlüssel in page[meta]:
 *   hero_subtitle    – kurzer Beschreibungstext unter dem Titel
 *   hero_cta_label   – Primärer CTA-Button-Text
 *   hero_cta_url     – CTA-Button-URL
 *   hero_cta2_label  – (optional) Sekundärer CTA-Button-Text
 *   hero_cta2_url    – (optional) Sekundärer CTA-Button-URL
 *   features         – Array von { icon, title, text }
 *   cta_title        – (optional) Abschluss-CTA-Überschrift
 *   cta_text         – (optional) Abschluss-CTA-Text
 *   cta_button_label – (optional) Abschluss-CTA-Button-Text
 *   cta_button_url   – (optional) Abschluss-CTA-Button-URL
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

$pageProvidedByRouter = isset($page) && !empty($page);

if ($pageProvidedByRouter) {
    $page = is_object($page) ? (array)$page : (array)$page;
} else {
    try {
        $page = function_exists('phinit_get_page_by_request_path')
            ? phinit_get_page_by_request_path(phinit_current_request_path())
            : null;
        if (!$page) {
            http_response_code(404);
            get_theme_part('404');
            exit;
        }
    } catch (\Throwable $e) {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }
}

$landingContent = (string)($page['content'] ?? '');
if (!$pageProvidedByRouter) {
    $landingContent = phinit_prepare_renderable_content($landingContent, 'page', (int)($page['id'] ?? 0));
}
$landingContent = phinit_sanitize_renderable_content($landingContent, 'default');
$landingHeadingData = phinit_with_heading_ids($landingContent, [2, 3, 4, 5, 6]);
$landingContent = phinit_enhance_content_images($landingHeadingData['html']);
$safeLandingContent = $landingContent;

// Meta-Daten auslesen
$meta       = is_array($page['meta'] ?? null) ? $page['meta'] : [];
$heroSub    = (string)($meta['hero_subtitle']    ?? '');
$heroCta1   = (string)($meta['hero_cta_label']   ?? '');
$heroCta1Url= (string)($meta['hero_cta_url']     ?? '#');
$heroCta2   = (string)($meta['hero_cta2_label']  ?? '');
$heroCta2Url= (string)($meta['hero_cta2_url']    ?? '#');
$features   = (array) ($meta['features']         ?? []);
$ctaTitle   = (string)($meta['cta_title']        ?? '');
$ctaText    = (string)($meta['cta_text']         ?? '');
$ctaBtn     = (string)($meta['cta_button_label'] ?? '');
$ctaBtnUrl  = (string)($meta['cta_button_url']   ?? '#');
$heroImage  = function_exists('phinit_normalize_public_media_url')
    ? phinit_normalize_public_media_url((string) ($page['thumbnail'] ?? ''), false, $siteUrl)
    : (string) ($page['thumbnail'] ?? '');
$heroCta1Href = function_exists('phinit_safe_public_url')
    ? phinit_safe_public_url($heroCta1Url, $siteUrl, ['http', 'https'])
    : $heroCta1Url;
$heroCta2Href = function_exists('phinit_safe_public_url')
    ? phinit_safe_public_url($heroCta2Url, $siteUrl, ['http', 'https'])
    : $heroCta2Url;
$ctaBtnHref = function_exists('phinit_safe_public_url')
    ? phinit_safe_public_url($ctaBtnUrl, $siteUrl, ['http', 'https'])
    : $ctaBtnUrl;
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     HERO-SECTION
═══════════════════════════════════════════════════════════════════════════ -->
<section class="landing-hero" aria-labelledby="landing-hero-title">
    <div class="container landing-hero__inner">

        <?php if ($heroImage !== ''): ?>
        <div class="landing-hero__media">
            <img src="<?php echo htmlspecialchars($heroImage, ENT_QUOTES); ?>"
                 alt="<?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?>"
                 class="landing-hero__img"
                  <?php echo phinit_image_loading_attributes(true); ?>>
        </div>
        <?php endif; ?>

        <div class="landing-hero__content">
            <h1 class="landing-hero__title" id="landing-hero-title" data-anim>
                <?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?>
            </h1>

            <?php if ($heroSub): ?>
            <p class="landing-hero__sub" data-anim data-anim-delay="1">
                <?php echo htmlspecialchars($heroSub, ENT_QUOTES); ?>
            </p>
            <?php endif; ?>

            <?php if (($heroCta1 !== '' && $heroCta1Href !== '') || ($heroCta2 !== '' && $heroCta2Href !== '')): ?>
            <div class="landing-hero__ctas" data-anim data-anim-delay="2">
                <?php if ($heroCta1 !== '' && $heroCta1Href !== ''): ?>
                <a href="<?php echo htmlspecialchars($heroCta1Href, ENT_QUOTES); ?>" class="btn btn--landing-primary">
                    <?php echo htmlspecialchars($heroCta1, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
                <?php if ($heroCta2 !== '' && $heroCta2Href !== ''): ?>
                <a href="<?php echo htmlspecialchars($heroCta2Href, ENT_QUOTES); ?>" class="btn btn--landing-outline">
                    <?php echo htmlspecialchars($heroCta2, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════════════
     FEATURE-CARDS
═══════════════════════════════════════════════════════════════════════════ -->
<?php if (!empty($features)): ?>
<section class="landing-features" aria-label="Features">
    <div class="container">
        <div class="landing-features__grid">
            <?php foreach ($features as $i => $feature): ?>
            <div class="landing-feature-card" data-anim data-anim-delay="<?php echo min((int)$i, 5); ?>">
                <?php $featureHref = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) ($feature['url'] ?? ''), $siteUrl, ['http', 'https']) : (string) ($feature['url'] ?? ''); ?>
                <?php if (!empty($feature['icon'])): ?>
                <div class="landing-feature-card__icon" aria-hidden="true">
                    <?php echo htmlspecialchars((string)$feature['icon'], ENT_QUOTES); ?>
                </div>
                <?php endif; ?>
                <h3 class="landing-feature-card__title">
                    <?php echo htmlspecialchars((string)($feature['title'] ?? ''), ENT_QUOTES); ?>
                </h3>
                <?php if (!empty($feature['text'])): ?>
                <p class="landing-feature-card__text">
                    <?php echo htmlspecialchars((string)$feature['text'], ENT_QUOTES); ?>
                </p>
                <?php endif; ?>
                <?php if ($featureHref !== ''): ?>
                <a href="<?php echo htmlspecialchars($featureHref, ENT_QUOTES); ?>" class="landing-feature-card__link">
                    Mehr erfahren →
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     FREIER SEITENINHALT (optional)
═══════════════════════════════════════════════════════════════════════════ -->
<?php if (phinit_has_visible_content($landingContent)): ?>
<section class="landing-content">
    <div class="container">
        <div class="page-content page-content--wide is-visible" data-anim>
            <?php phinit_render_prepared_content($safeLandingContent); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════════════════════════════════════════════════════
     ABSCHLUSS-CTA BANNER (optional)
═══════════════════════════════════════════════════════════════════════════ -->
<?php if ($ctaTitle || $ctaBtn): ?>
<section class="landing-cta" aria-label="Call to Action">
    <div class="container landing-cta__inner">
        <?php if ($ctaTitle): ?>
        <h2 class="landing-cta__title" data-anim>
            <?php echo htmlspecialchars($ctaTitle, ENT_QUOTES); ?>
        </h2>
        <?php endif; ?>
        <?php if ($ctaText): ?>
        <p class="landing-cta__text" data-anim data-anim-delay="1">
            <?php echo htmlspecialchars($ctaText, ENT_QUOTES); ?>
        </p>
        <?php endif; ?>
        <?php if ($ctaBtn !== '' && $ctaBtnHref !== ''): ?>
        <a href="<?php echo htmlspecialchars($ctaBtnHref, ENT_QUOTES); ?>" class="btn btn--landing-primary" data-anim data-anim-delay="2">
            <?php echo htmlspecialchars($ctaBtn, ENT_QUOTES); ?>
        </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
