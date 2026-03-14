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
        $slug = trim((string)($_GET['slug'] ?? (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '')), '/ ');
        $page = $slug !== '' ? \CMS\PageManager::instance()->getPageBySlug($slug) : null;
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
$landingHeadingData = phinit_with_heading_ids($landingContent, [2, 3]);
$landingContent = phinit_enhance_content_images($landingHeadingData['html']);

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
?>

<!-- ═══════════════════════════════════════════════════════════════════════
     HERO-SECTION
═══════════════════════════════════════════════════════════════════════════ -->
<section class="landing-hero" aria-labelledby="landing-hero-title">
    <div class="container landing-hero__inner">

        <?php if (!empty($page['thumbnail'])): ?>
        <div class="landing-hero__media">
            <img src="<?php echo htmlspecialchars($page['thumbnail'], ENT_QUOTES); ?>"
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

            <?php if ($heroCta1 || $heroCta2): ?>
            <div class="landing-hero__ctas" data-anim data-anim-delay="2">
                <?php if ($heroCta1): ?>
                <a href="<?php echo htmlspecialchars($heroCta1Url, ENT_QUOTES); ?>" class="btn btn--landing-primary">
                    <?php echo htmlspecialchars($heroCta1, ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
                <?php if ($heroCta2): ?>
                <a href="<?php echo htmlspecialchars($heroCta2Url, ENT_QUOTES); ?>" class="btn btn--landing-outline">
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
                <?php if (!empty($feature['url'])): ?>
                <a href="<?php echo htmlspecialchars((string)$feature['url'], ENT_QUOTES); ?>" class="landing-feature-card__link">
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
        <div class="page-content page-content--wide" data-anim>
            <?php echo $landingContent; ?>
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
        <?php if ($ctaBtn): ?>
        <a href="<?php echo htmlspecialchars($ctaBtnUrl, ENT_QUOTES); ?>" class="btn btn--landing-primary" data-anim data-anim-delay="2">
            <?php echo htmlspecialchars($ctaBtn, ENT_QUOTES); ?>
        </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
