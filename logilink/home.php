<?php
if (!defined('ABSPATH')) exit;
try { $c = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable $e) { $c = null; }

$heroBadge    = $c?->get('logistics_hero', 'hero_badge',       '🚛 Logistik & Transport Plattform')                ?? '🚛 Logistik & Transport Plattform';
$heroHeadline = $c?->get('logistics_hero', 'hero_headline',    'Die smarte Lösung für Logistik & Speditionen')    ?? '';
$heroSubline  = $c?->get('logistics_hero', 'hero_subline',     'Sendungen verfolgen, Partner vernetzen, Prozesse optimieren.') ?? '';
$heroCta      = $c?->get('logistics_hero', 'hero_cta_label',   'Sendung verfolgen')      ?? 'Sendung verfolgen';
$heroCtaUrl   = '/tracking';
$heroSecCta   = $c?->get('logistics_hero', 'hero_tracking_cta_label', 'Partner werden') ?? 'Partner werden';
$heroSecUrl   = '/register';
$showStats    = true;

$kpiTitle     = $c?->get('logistics_content', 'services_section_title',  'Aktuelle Plattform-Daten') ?? 'Aktuelle Plattform-Daten';
$kpiShipments = $c?->get('logistics_content', 'kpi_punctuality_label', 'Pünktlichkeitsrate') ?? 'Pünktlichkeitsrate';
$kpiPartners  = $c?->get('logistics_content', 'kpi_efficiency_label',  'Last-Mile-Effizienz')       ?? 'Last-Mile-Effizienz';
$kpiRoutes    = $c?->get('logistics_content', 'kpi_damage_label',    'Schadensquote')  ?? 'Schadensquote';
$ctaTitle     = $c?->get('logistics_content', 'cta_section_title',   'Jetzt Netzwerk erweitern') ?? '';
$ctaRegLabel  = 'Kostenlos registrieren';

$statusLabels = [
    'warehouse' => $c?->get('logistics_tracking', 'status_label_warehouse', 'Im Lager')       ?? 'Im Lager',
    'picked'    => $c?->get('logistics_tracking', 'status_label_picked',    'Abgeholt')        ?? 'Abgeholt',
    'transit'   => $c?->get('logistics_tracking', 'status_label_transit',   'Unterwegs')       ?? 'Unterwegs',
    'delivered' => $c?->get('logistics_tracking', 'status_label_delivered', 'Zugestellt')      ?? 'Zugestellt',
    'delayed'   => $c?->get('logistics_tracking', 'status_label_delayed',   'Verzögert')       ?? 'Verzögert',
    'returned'  => $c?->get('logistics_tracking', 'status_label_returned',  'Retour')          ?? 'Retour',
];

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
<main id="main" class="ll-main" role="main">

    <section class="ll-hero" aria-label="Hero">
        <div class="ll-container">
            <?php if (!empty($heroBadge)) : ?>
                <div class="ll-hero-badge"><?php echo $safe($heroBadge); ?></div>
            <?php endif; ?>
            <h1><?php echo $safe($heroHeadline); ?></h1>
            <?php if (!empty($heroSubline)) : ?>
                <p class="ll-hero-sub"><?php echo $safe($heroSubline); ?></p>
            <?php endif; ?>
            <div class="ll-cta-group">
                <a href="<?php echo $safe($buildUrl($siteUrl, (string) $heroCtaUrl)); ?>" class="ll-btn ll-btn-accent"><?php echo $safe($heroCta); ?></a>
                <a href="<?php echo $safe($buildUrl($siteUrl, (string) $heroSecUrl));  ?>" class="ll-btn ll-btn-outline"><?php echo $safe($heroSecCta); ?></a>
            </div>
            <?php if ($showStats) : ?>
                <div class="ll-stats-row">
                    <div class="ll-stat"><span class="ll-stat-number">4.800+</span><span class="ll-stat-label">Sendungen</span></div>
                    <div class="ll-stat"><span class="ll-stat-number">220+</span><span class="ll-stat-label">Partner</span></div>
                    <div class="ll-stat"><span class="ll-stat-number">98%</span><span class="ll-stat-label">Liefertreue</span></div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Status Legende -->
    <section class="ll-section ll-section--alt" aria-label="Sendungsstatus-Legende">
        <div class="ll-container">
            <div class="ll-section-header"><h2>Sendungsstatus</h2></div>
            <div class="ll-status-legend-row">
                <?php foreach ($statusLabels as $key => $label) : ?>
                    <span class="ll-status-badge ll-status--<?php echo $safe($key); ?>"><?php echo $safe($label); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- KPI -->
    <section class="ll-section" aria-label="Plattform-Kennzahlen">
        <div class="ll-container">
            <div class="ll-section-header"><h2><?php echo $safe($kpiTitle); ?></h2></div>
            <div class="ll-grid">
                <div class="ll-kpi-card"><div class="ll-kpi-number">4.800</div><div class="ll-kpi-label"><?php echo $safe($kpiShipments); ?></div></div>
                <div class="ll-kpi-card"><div class="ll-kpi-number">220</div><div class="ll-kpi-label"><?php echo $safe($kpiPartners); ?></div></div>
                <div class="ll-kpi-card"><div class="ll-kpi-number">1.200</div><div class="ll-kpi-label"><?php echo $safe($kpiRoutes); ?></div></div>
            </div>
        </div>
    </section>

    <?php if (!$isLoggedIn && !empty($ctaTitle)) : ?>
        <section class="ll-section ll-section--network-cta">
            <div class="ll-container">
                <h2 class="ll-network-cta-title"><?php echo $safe($ctaTitle); ?></h2>
                <a href="<?php echo $safe($siteUrl); ?>/register" class="ll-btn ll-btn-accent ll-network-cta-button">
                    <?php echo $safe($ctaRegLabel ?: 'Jetzt registrieren'); ?>
                </a>
            </div>
        </section>
    <?php endif; ?>

</main>
