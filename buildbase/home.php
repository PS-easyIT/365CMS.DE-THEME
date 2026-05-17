<?php
/**
 * BuildBase Theme – Home / Startseite
 *
 * @package BuildBase_Theme
 */

if (!defined('ABSPATH')) exit;

try {
    $c = \CMS\Services\ThemeCustomizer::instance();
} catch (\Throwable $e) { $c = null; }

$heroBadge    = $c?->get('build_hero', 'hero_badge',       '🔨 Bau & Handwerk Plattform')        ?? '🔨 Bau & Handwerk Plattform';
$heroHeadline = $c?->get('build_hero', 'hero_headline',    'Ihr Handwerker-Netzwerk')             ?? 'Ihr Handwerker-Netzwerk';
$heroSubline  = $c?->get('build_hero', 'hero_subline',     'Finden Sie qualifizierte Handwerker und Baufirmen in Ihrer Region.') ?? '';
$heroCta      = $c?->get('build_hero', 'hero_cta_label',   'Handwerker finden')                  ?? 'Handwerker finden';
$heroCtaUrl   = '/handwerker';
$heroSecCta   = $c?->get('build_hero', 'hero_quote_request_label', 'Kostenvoranschlag anfordern') ?? 'Kostenvoranschlag anfordern';
$heroSecUrl   = '/angebot';
$showStats    = true;

$portfolioTitle = $c?->get('build_content', 'portfolio_section_title', 'Referenz-Projekte')       ?? 'Referenz-Projekte';
$reviewsTitle   = $c?->get('build_content', 'reviews_section_title',   'Kundenstimmen')           ?? 'Kundenstimmen';
$quoteTitle     = $c?->get('build_content', 'quote_section_title',     'Kostenloses Angebot')     ?? 'Kostenloses Angebot';
$quoteText      = $c?->get('build_content', 'quote_section_text',      'Beschreiben Sie Ihr Projekt und erhalten Sie Angebote von geprüften Handwerkern.') ?? '';
$ctaRegLabel    = $c?->get('build_content', 'register_cta_label',      'Jetzt registrieren')     ?? 'Jetzt registrieren';

$siteUrl    = SITE_URL;
$isLoggedIn = theme_is_logged_in();
$safe       = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$buildUrl   = static function (string $baseUrl, string $path): string {
    $trimmedPath = trim($path);
    if ($trimmedPath === '') {
        return rtrim($baseUrl, '/') . '/';
    }
    if (str_starts_with($trimmedPath, 'http://') || str_starts_with($trimmedPath, 'https://')) {
        return $trimmedPath;
    }
    if (str_starts_with($trimmedPath, '#')) {
        return rtrim($baseUrl, '/') . '/' . $trimmedPath;
    }
    return rtrim($baseUrl, '/') . '/' . ltrim($trimmedPath, '/');
};
?>

<main id="main" class="bb-main" role="main">

    <!-- Hero -->
    <section class="bb-hero" aria-label="Hero">
        <div class="bb-container">
            <?php if (!empty($heroBadge)) : ?>
                <div class="bb-hero-badge"><?php echo $safe($heroBadge); ?></div>
            <?php endif; ?>
            <h1><?php echo $safe($heroHeadline); ?></h1>
            <?php if (!empty($heroSubline)) : ?>
                <p class="bb-hero-sub"><?php echo $safe($heroSubline); ?></p>
            <?php endif; ?>
            <div class="bb-cta-group">
                <a href="<?php echo $safe($buildUrl($siteUrl, (string) $heroCtaUrl)); ?>" class="bb-btn bb-btn-primary"><?php echo $safe($heroCta); ?></a>
                <a href="<?php echo $safe($buildUrl($siteUrl, (string) $heroSecUrl)); ?>"  class="bb-btn bb-btn-outline"><?php echo $safe($heroSecCta); ?></a>
            </div>
            <?php if ($showStats) : ?>
                <div class="bb-stats-row">
                    <div class="bb-stat">
                        <span class="bb-stat-number">800+</span>
                        <span class="bb-stat-label">Handwerker</span>
                    </div>
                    <div class="bb-stat">
                        <span class="bb-stat-number">2.400+</span>
                        <span class="bb-stat-label">Abgeschlossene Projekte</span>
                    </div>
                    <div class="bb-stat">
                        <span class="bb-stat-number">4.8★</span>
                        <span class="bb-stat-label">Ø Bewertung</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Portfolio -->
    <section class="bb-section" aria-label="<?php echo $safe($portfolioTitle); ?>">
        <div class="bb-container">
            <div class="bb-section-header">
                <h2><?php echo $safe($portfolioTitle); ?></h2>
            </div>
            <div class="bb-grid">
                <div class="bb-card bb-card--portfolio-cta">
                    <a href="<?php echo $safe($siteUrl); ?>/projekte" class="bb-btn bb-btn-primary">
                        Alle Referenzen ansehen →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Angebot -->
    <?php if (!$isLoggedIn) : ?>
        <section class="bb-section bb-section--alt" aria-label="Angebot anfragen">
            <div class="bb-container bb-section-center">
                <h2><?php echo $safe($quoteTitle); ?></h2>
                <?php if (!empty($quoteText)) : ?>
                    <p class="bb-section-lead"><?php echo $safe($quoteText); ?></p>
                <?php endif; ?>
                <a href="<?php echo $safe($siteUrl); ?>/register" class="bb-btn bb-btn-primary"><?php echo $safe($ctaRegLabel); ?></a>
            </div>
        </section>
    <?php endif; ?>

</main>
