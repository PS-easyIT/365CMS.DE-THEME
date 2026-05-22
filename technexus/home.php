<?php
/**
 * TechNexus Theme – Home / Startseite
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$heroBadge     = (string) tn_get_setting('tech_hero', 'hero_badge', 'IT-Expert Plattform');
$heroHeadline  = (string) tn_get_setting('tech_hero', 'hero_headline', 'Vernetze IT-Experten. Skaliere dein Team.');
$heroSubline   = (string) tn_get_setting('tech_hero', 'hero_subline', '');
$heroCta       = (string) tn_get_setting('tech_hero', 'hero_cta_label', 'Experten entdecken');
$heroCtaUrl    = tn_href((string) tn_get_setting('tech_hero', 'hero_cta_url', '/it-experts'));
$heroSecCta    = (string) tn_get_setting('tech_hero', 'hero_secondary_cta_label', 'Profil anlegen');
$heroSecCtaUrl = tn_href((string) tn_get_setting('tech_hero', 'hero_secondary_cta_url', '/register'));
$showStats     = filter_var(tn_get_setting('tech_hero', 'show_stats_bar', true), FILTER_VALIDATE_BOOLEAN);

$expertTitle = (string) tn_get_setting('tech_content', 'experts_section_title', 'Top IT-Experten');
$expertSubT  = (string) tn_get_setting('tech_content', 'experts_section_subtitle', '');
$ctaTitle    = (string) tn_get_setting('tech_content', 'cta_section_title', 'Bereit für das nächste Projekt?');
$ctaText     = (string) tn_get_setting('tech_content', 'cta_section_text', '');
$ctaRegLabel = (string) tn_get_setting('tech_content', 'register_cta_label', 'Jetzt kostenlos registrieren');

$isLoggedIn = theme_is_logged_in();

try {
    $landingSvc = \CMS\Services\LandingPageService::getInstance();
    $lpHeader   = $landingSvc->getHeader();
    $lpFeatures = $landingSvc->getFeatures();
} catch (\Throwable) {
    $lpHeader   = [];
    $lpFeatures = [];
}
?>

<main id="main" class="site-main" role="main">

    <section class="home-hero" aria-label="Hero">
        <div class="home-hero__mesh" aria-hidden="true"></div>
        <div class="container home-hero__inner">
            <?php if ($heroBadge !== '') : ?>
                <p class="hero-badge"><?php echo tn_html_attr($heroBadge); ?></p>
            <?php endif; ?>

            <h1><?php echo tn_safe_headline($heroHeadline); ?></h1>

            <?php if ($heroSubline !== '') : ?>
                <p class="home-hero__lead"><?php echo tn_html_attr($heroSubline); ?></p>
            <?php endif; ?>

            <div class="hero-cta-group">
                <a href="<?php echo tn_html_attr($heroCtaUrl); ?>" class="btn btn-primary">
                    <?php echo tn_html_attr($heroCta); ?>
                </a>
                <?php if (!$isLoggedIn) : ?>
                    <a href="<?php echo tn_html_attr($heroSecCtaUrl); ?>" class="btn btn-outline">
                        <?php echo tn_html_attr($heroSecCta); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ($showStats) : ?>
                <div class="hero-stats" aria-label="Plattform-Statistiken">
                    <div class="hero-stat">
                        <span class="hero-stat-number">500+</span>
                        <span class="hero-stat-label">IT-Experten</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">120+</span>
                        <span class="hero-stat-label">Tech-Firmen</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">300+</span>
                        <span class="hero-stat-label">Projekte</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($lpFeatures)) : ?>
        <section class="home-features tn-section tn-section--muted" aria-label="Funktionen">
            <div class="container">
                <div class="section-header">
                    <?php
                    $featTitle = (string) ($lpHeader['features_title'] ?? '');
                    $featSub   = (string) ($lpHeader['features_subtitle'] ?? '');
                    if ($featTitle !== '') : ?>
                        <h2><?php echo tn_html_attr($featTitle); ?></h2>
                    <?php endif; ?>
                    <?php if ($featSub !== '') : ?>
                        <p><?php echo tn_html_attr($featSub); ?></p>
                    <?php endif; ?>
                </div>
                <div class="tech-grid">
                    <?php foreach ($lpFeatures as $feat) :
                        $fTitle = (string) ($feat['title'] ?? '');
                        $fText  = (string) ($feat['text']  ?? '');
                        $fIcon  = (string) ($feat['icon']  ?? '');
                        if ($fTitle === '' && $fText === '') {
                            continue;
                        }
                    ?>
                        <article class="tech-card">
                            <?php if ($fIcon !== '') : ?>
                                <div class="feature-icon" aria-hidden="true"><?php echo tn_html_attr($fIcon); ?></div>
                            <?php endif; ?>
                            <?php if ($fTitle !== '') : ?>
                                <h3><?php echo tn_html_attr($fTitle); ?></h3>
                            <?php endif; ?>
                            <?php if ($fText !== '') : ?>
                                <p class="tech-card__meta"><?php echo tn_html_attr($fText); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="home-experts tn-section" aria-label="Experten">
        <div class="container">
            <div class="section-header">
                <h2><?php echo tn_html_attr($expertTitle); ?></h2>
                <?php if ($expertSubT !== '') : ?>
                    <p><?php echo tn_html_attr($expertSubT); ?></p>
                <?php endif; ?>
            </div>
            <div class="tech-grid">
                <div class="tech-card tech-card--placeholder">
                    <p class="tech-card__meta">
                        <a href="<?php echo tn_html_attr(theme_route_url('experts')); ?>" class="btn btn-primary">
                            Alle IT-Experten anzeigen
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php if (!$isLoggedIn) : ?>
        <section class="cta-section tn-section" aria-label="Registrierung">
            <div class="container cta-section__inner">
                <h2><?php echo tn_html_attr($ctaTitle); ?></h2>
                <?php if ($ctaText !== '') : ?>
                    <p><?php echo tn_html_attr($ctaText); ?></p>
                <?php endif; ?>
                <a href="<?php echo tn_html_attr(theme_route_url('register')); ?>" class="btn btn-outline">
                    <?php echo tn_html_attr($ctaRegLabel); ?>
                </a>
            </div>
        </section>
    <?php endif; ?>

</main>
