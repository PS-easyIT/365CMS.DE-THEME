<?php
declare(strict_types=1);

/**
 * BuildBase Theme – Home / Startseite
 *
 * @package BuildBase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

try {
    $c = \CMS\Services\ThemeCustomizer::instance();
} catch (\Throwable $e) {
    $c = null;
}

$heroBadge    = (string) ($c?->get('build_hero', 'hero_badge',              '🔨 Handwerk trifft Qualität')                                       ?? '🔨 Handwerk trifft Qualität');
$heroHeadline = (string) ($c?->get('build_hero', 'hero_headline',           'Ihr Bau- & Handwerker-Verzeichnis')                                 ?? 'Ihr Bau- & Handwerker-Verzeichnis');
$heroSubline  = (string) ($c?->get('build_hero', 'hero_subline',            'Finden Sie qualifizierte Handwerker und Baufirmen in Ihrer Region.') ?? '');
$heroCta      = (string) ($c?->get('build_hero', 'hero_cta_label',          'Handwerker finden')                                                 ?? 'Handwerker finden');
$heroSecCta   = (string) ($c?->get('build_hero', 'hero_quote_request_label','Kostenvoranschlag anfordern')                                       ?? 'Kostenvoranschlag anfordern');

$showStats    = filter_var($c?->get('build_hero', 'show_hero_stats', true) ?? true, FILTER_VALIDATE_BOOLEAN);
$statValue1   = (string) ($c?->get('build_hero', 'hero_stat_1_value', '800+')                       ?? '800+');
$statLabel1   = (string) ($c?->get('build_hero', 'hero_stat_1_label', 'Handwerker im Netzwerk')     ?? 'Handwerker im Netzwerk');
$statValue2   = (string) ($c?->get('build_hero', 'hero_stat_2_value', '2.400+')                     ?? '2.400+');
$statLabel2   = (string) ($c?->get('build_hero', 'hero_stat_2_label', 'Abgeschlossene Projekte')    ?? 'Abgeschlossene Projekte');
$statValue3   = (string) ($c?->get('build_hero', 'hero_stat_3_value', '4,8★')                       ?? '4,8★');
$statLabel3   = (string) ($c?->get('build_hero', 'hero_stat_3_label', 'Ø Kundenbewertung')          ?? 'Ø Kundenbewertung');

$portfolioTitle = (string) ($c?->get('build_content', 'portfolio_section_title',    'Referenz-Projekte')                       ?? 'Referenz-Projekte');
$portfolioSub   = (string) ($c?->get('build_content', 'portfolio_section_subtitle', 'Realisierte Projekte mit Qualitätsnachweis') ?? '');
$ctaTitle       = (string) ($c?->get('build_content', 'cta_section_title',          'Qualität statt Kompromisse')              ?? 'Qualität statt Kompromisse');
$ctaText        = (string) ($c?->get('build_content', 'cta_section_text',           'Registrieren Sie Ihren Betrieb kostenlos und werden Sie sichtbar für Kunden in Ihrer Region.') ?? '');
$ctaBtnLabel    = (string) ($c?->get('build_content', 'cta_button_label',           'Jetzt Betrieb registrieren')              ?? 'Jetzt Betrieb registrieren');

$siteUrl    = buildbase_safe_url((string) SITE_URL, '/');
$isLoggedIn = theme_is_logged_in();
$safe       = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$buildUrl = static function (string $baseUrl, string $path): string {
    $trimmed = trim($path);
    if ($trimmed === '') {
        return buildbase_safe_url(rtrim($baseUrl, '/') . '/', rtrim($baseUrl, '/') . '/');
    }
    if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
        return buildbase_safe_url($trimmed, rtrim($baseUrl, '/') . '/');
    }
    if (str_starts_with($trimmed, '#')) {
        return buildbase_safe_url(rtrim($baseUrl, '/') . '/' . $trimmed, rtrim($baseUrl, '/') . '/');
    }
    return buildbase_safe_url(rtrim($baseUrl, '/') . '/' . ltrim($trimmed, '/'), rtrim($baseUrl, '/') . '/');
};

$heroCtaUrl  = $buildUrl($siteUrl, '/handwerker');
$heroSecUrl  = $buildUrl($siteUrl, '/angebot');
$projectsUrl = $buildUrl($siteUrl, '/projekte');
$registerUrl = function_exists('theme_route_url') ? theme_route_url('register') : $buildUrl($siteUrl, '/register');
?>

<main id="main" class="bb-main" role="main">

    <section class="bb-hero" aria-label="Hero">
        <div class="bb-container">
            <?php if ($heroBadge !== '') : ?>
                <div class="bb-hero-badge"><?php echo $safe($heroBadge); ?></div>
            <?php endif; ?>
            <h1><?php echo $safe($heroHeadline); ?></h1>
            <?php if ($heroSubline !== '') : ?>
                <p class="bb-hero-sub"><?php echo $safe($heroSubline); ?></p>
            <?php endif; ?>
            <div class="bb-cta-group">
                <a href="<?php echo $safe($heroCtaUrl); ?>" class="bb-btn bb-btn-primary"><?php echo $safe($heroCta); ?></a>
                <a href="<?php echo $safe($heroSecUrl); ?>" class="bb-btn bb-btn-outline"><?php echo $safe($heroSecCta); ?></a>
            </div>
            <?php if ($showStats) : ?>
                <div class="bb-stats-row" role="list" aria-label="Kennzahlen">
                    <div class="bb-stat" role="listitem">
                        <span class="bb-stat-number"><?php echo $safe($statValue1); ?></span>
                        <span class="bb-stat-label"><?php echo $safe($statLabel1); ?></span>
                    </div>
                    <div class="bb-stat" role="listitem">
                        <span class="bb-stat-number"><?php echo $safe($statValue2); ?></span>
                        <span class="bb-stat-label"><?php echo $safe($statLabel2); ?></span>
                    </div>
                    <div class="bb-stat" role="listitem">
                        <span class="bb-stat-number"><?php echo $safe($statValue3); ?></span>
                        <span class="bb-stat-label"><?php echo $safe($statLabel3); ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="bb-section" aria-label="<?php echo $safe($portfolioTitle); ?>">
        <div class="bb-container">
            <div class="bb-section-header">
                <h2><?php echo $safe($portfolioTitle); ?></h2>
                <?php if ($portfolioSub !== '') : ?>
                    <p class="bb-section-lead"><?php echo $safe($portfolioSub); ?></p>
                <?php endif; ?>
            </div>
            <div class="bb-grid">
                <div class="bb-card bb-card--portfolio-cta">
                    <a href="<?php echo $safe($projectsUrl); ?>" class="bb-btn bb-btn-primary">
                        Alle Referenzen ansehen &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php if (!$isLoggedIn) : ?>
        <section class="bb-section bb-section--alt" aria-label="Betrieb registrieren">
            <div class="bb-container bb-section-center">
                <h2><?php echo $safe($ctaTitle); ?></h2>
                <?php if ($ctaText !== '') : ?>
                    <p class="bb-section-lead"><?php echo $safe($ctaText); ?></p>
                <?php endif; ?>
                <a href="<?php echo $safe($registerUrl); ?>" class="bb-btn bb-btn-primary"><?php echo $safe($ctaBtnLabel); ?></a>
            </div>
        </section>
    <?php endif; ?>

</main>
