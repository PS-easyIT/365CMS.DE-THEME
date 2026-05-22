<?php
/**
 * LogiLink Theme – Home Template
 *
 * Logistik-Hero mit Routenlinien-Visual, Status-Stepper, KPI-Cards,
 * Service-Übersicht, Kapazitäts-Buchungs-Skizze, Partner-Netzwerk
 * und Registrierungs-CTA – alle Inhalte aus dem Customizer gespeist.
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

// Hero (logistics_hero)
$heroBadge    = (string) ll_get_setting('logistics_hero', 'hero_badge',              '🚛 Logistik-Netzwerk');
$heroHeadline = (string) ll_get_setting('logistics_hero', 'hero_headline',           'Sendungen verfolgen. Kapazitäten buchen. Netzwerk wachsen.');
$heroSubline  = (string) ll_get_setting('logistics_hero', 'hero_subline',            'Das digitale Logistik-Netzwerk für Speditionen, Kurierdienste und Lagerdienstleister.');
$heroCtaLabel = (string) ll_get_setting('logistics_hero', 'hero_cta_label',          'Kapazitäten buchen');
$heroSecLabel = (string) ll_get_setting('logistics_hero', 'hero_tracking_cta_label', 'Sendung verfolgen');

// Tracking-System (logistics_tracking)
$trackingTitle = (string) ll_get_setting('logistics_tracking', 'tracking_section_title', 'Sendungsverfolgung');
$statusLabels  = [
    'warehouse' => (string) ll_get_setting('logistics_tracking', 'status_label_warehouse', 'Eingelagert'),
    'picked'    => (string) ll_get_setting('logistics_tracking', 'status_label_picked',    'Kommissioniert'),
    'transit'   => (string) ll_get_setting('logistics_tracking', 'status_label_transit',   'In Zustellung'),
    'delivered' => (string) ll_get_setting('logistics_tracking', 'status_label_delivered', 'Erfolgreich zugestellt'),
    'delayed'   => (string) ll_get_setting('logistics_tracking', 'status_label_delayed',   'Verzögerung'),
    'returned'  => (string) ll_get_setting('logistics_tracking', 'status_label_returned',  'Retoure in Bearbeitung'),
];

// Content (logistics_content)
$servicesTitle    = (string) ll_get_setting('logistics_content', 'services_section_title', 'Unsere Logistik-Dienstleistungen');
$capacityTitle    = (string) ll_get_setting('logistics_content', 'capacity_booking_title', 'Kapazität buchen');
$capacityUnits    = (string) ll_get_setting('logistics_content', 'capacity_units_label',   'Paletten / Gewicht (kg) / m³');
$kpiPunctLabel    = (string) ll_get_setting('logistics_content', 'kpi_punctuality_label',  'Pünktlichkeitsrate');
$kpiEfficiencyLbl = (string) ll_get_setting('logistics_content', 'kpi_efficiency_label',   'Last-Mile-Effizienz');
$kpiDamageLabel   = (string) ll_get_setting('logistics_content', 'kpi_damage_label',       'Schadensquote');
$networkTitle     = (string) ll_get_setting('logistics_content', 'network_section_title',  'Logistik-Partner im Netzwerk');
$ctaTitle         = (string) ll_get_setting('logistics_content', 'cta_section_title',      'Werde Teil des Netzwerks');
$ctaText          = (string) ll_get_setting('logistics_content', 'cta_section_text',       'Registriere dein Logistik-Unternehmen und erhalte Zugang zu tausenden Buchungsanfragen täglich.');

$heroCtaUrl = $safe(theme_route_url('register'));
$heroSecUrl = $safe(theme_route_url('tracking'));
$regUrl     = $safe(theme_route_url('register'));
$isLoggedIn = theme_is_logged_in();

// Demo-Datensatz für Status-/Card-Beispiele.
$demoShipments = [
    [
        'tracking' => 'LL-2407-8821-DE',
        'origin'   => 'Hamburg',
        'dest'     => 'München',
        'status'   => 'transit',
        'eta'      => 'ETA 18:45',
        'leg'      => 'Leg 02 / Hub Frankfurt',
    ],
    [
        'tracking' => 'LL-2407-8854-AT',
        'origin'   => 'Wien',
        'dest'     => 'Salzburg',
        'status'   => 'picked',
        'eta'      => 'ETA Morgen 09:30',
        'leg'      => 'Pick-up bestätigt',
    ],
    [
        'tracking' => 'LL-2407-8867-CH',
        'origin'   => 'Zürich',
        'dest'     => 'Genève',
        'status'   => 'delivered',
        'eta'      => 'Geliefert 16:12',
        'leg'      => 'POD signed',
    ],
    [
        'tracking' => 'LL-2407-8901-DE',
        'origin'   => 'Berlin',
        'dest'     => 'Köln',
        'status'   => 'delayed',
        'eta'      => 'Verzug +90 min',
        'leg'      => 'Verkehr A2',
    ],
];

$services = [
    [
        'icon' => 'truck',
        'title' => 'Stückgut & Teilladungen',
        'desc'  => 'Direktbuchung von Stückgut bis Komplettladung – ab 1 Palette, bundesweit unter 24h.',
    ],
    [
        'icon' => 'cube',
        'title' => 'Kontraktlogistik',
        'desc'  => 'Lager, Kommissionierung, Wertschöpfung. Vollintegrierte Prozesse mit Echtzeit-Status.',
    ],
    [
        'icon' => 'route',
        'title' => 'Last-Mile-Delivery',
        'desc'  => 'Hyperlokales Zustellnetz mit Live-Tracking und Empfänger-Reschedule.',
    ],
    [
        'icon' => 'globe',
        'title' => 'International & Customs',
        'desc'  => 'Grenzüberschreitende Sendungen mit Zollabwicklung und Multimodal-Optionen.',
    ],
];

$serviceIcon = static function (string $key): string {
    return match ($key) {
        'truck' => '<path d="M3 6h11v8H3z" /><path d="M14 8h4l3 3v3h-7z" /><circle cx="7" cy="17" r="2" /><circle cx="17" cy="17" r="2" />',
        'cube'  => '<path d="M12 3l8 4v10l-8 4-8-4V7z"/><path d="M12 3v18M4 7l8 4 8-4" />',
        'route' => '<circle cx="6" cy="6" r="2.5"/><circle cx="18" cy="18" r="2.5"/><path d="M8 7c4 0 6 3 6 5s-2 5-6 5"/>',
        'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a13 13 0 010 18M12 3a13 13 0 000 18"/>',
        default => '<circle cx="12" cy="12" r="6"/>',
    };
};
?>
<main id="main" class="ll-main" role="main">

    <!-- ════════════════════════════════════════════════════
         Hero — operativ, route-line statt SaaS-Gradient
         ════════════════════════════════════════════════════ -->
    <section class="ll-hero" aria-label="Übersicht">
        <div class="ll-container">
            <div class="ll-hero-grid">

                <div class="ll-hero-copy">
                    <?php if ($heroBadge !== '') : ?>
                        <span class="ll-hero-badge"><?php echo $safe($heroBadge); ?></span>
                    <?php endif; ?>
                    <h1><?php echo ll_safe_headline($heroHeadline); ?></h1>
                    <?php if ($heroSubline !== '') : ?>
                        <p class="ll-hero-sub"><?php echo $safe($heroSubline); ?></p>
                    <?php endif; ?>
                    <div class="ll-cta-group">
                        <a href="<?php echo $heroCtaUrl; ?>" class="ll-btn ll-btn-accent ll-btn-lg">
                            <?php echo $safe($heroCtaLabel); ?>
                        </a>
                        <a href="<?php echo $heroSecUrl; ?>" class="ll-btn ll-btn-outline ll-btn-lg">
                            <?php echo $safe($heroSecLabel); ?>
                        </a>
                    </div>
                    <div class="ll-stats-row" aria-label="Plattform-Eckdaten">
                        <div class="ll-stat">
                            <span class="ll-stat-number">4.800+</span>
                            <span class="ll-stat-label">Sendungen / Tag</span>
                        </div>
                        <div class="ll-stat">
                            <span class="ll-stat-number">220+</span>
                            <span class="ll-stat-label">Partner</span>
                        </div>
                        <div class="ll-stat">
                            <span class="ll-stat-number">98%</span>
                            <span class="ll-stat-label">Liefertreue</span>
                        </div>
                    </div>
                </div>

                <!-- Route-Line Visual (signaturhaftes Logistik-Element) -->
                <div class="ll-route" role="img" aria-label="Live-Route: Hamburg via Frankfurt nach München, aktuell im Hauptlauf.">
                    <div class="ll-route-meta">
                        <span>Route 47 · Live</span>
                        <span class="ll-status-pill"><?php echo $safe($statusLabels['transit']); ?></span>
                    </div>
                    <div class="ll-route-line">
                        <div class="ll-route-stop is-done">
                            <span class="ll-route-city">Hamburg</span>
                            <span class="ll-route-time">09:14 · OUT</span>
                        </div>
                        <div class="ll-route-stop is-active">
                            <span class="ll-route-city">Frankfurt</span>
                            <span class="ll-route-time">14:30 · HUB</span>
                        </div>
                        <div class="ll-route-stop is-future">
                            <span class="ll-route-city">München</span>
                            <span class="ll-route-time">ETA 18:45</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         Status-Stepper & Legende
         ════════════════════════════════════════════════════ -->
    <section class="ll-section" aria-label="Sendungsstatus-System">
        <div class="ll-container">
            <div class="ll-section-header">
                <span class="ll-eyebrow">Status-System</span>
                <h2><?php echo $safe($trackingTitle); ?></h2>
                <p>Sechsstufiges Status-Modell – jede Stufe semantisch farbcodiert, in Tracking-IDs konsistent gespiegelt.</p>
            </div>

            <ol class="ll-stepper" aria-label="Status-Stufen einer Sendung">
                <li class="ll-stepper-step ll-stepper-step--warehouse">
                    <span class="ll-step-num">01</span>
                    <span class="ll-step-label"><?php echo $safe($statusLabels['warehouse']); ?></span>
                </li>
                <li class="ll-stepper-step ll-stepper-step--picked">
                    <span class="ll-step-num">02</span>
                    <span class="ll-step-label"><?php echo $safe($statusLabels['picked']); ?></span>
                </li>
                <li class="ll-stepper-step ll-stepper-step--transit">
                    <span class="ll-step-num">03</span>
                    <span class="ll-step-label"><?php echo $safe($statusLabels['transit']); ?></span>
                </li>
                <li class="ll-stepper-step ll-stepper-step--delivered">
                    <span class="ll-step-num">04</span>
                    <span class="ll-step-label"><?php echo $safe($statusLabels['delivered']); ?></span>
                </li>
                <li class="ll-stepper-step ll-stepper-step--delayed">
                    <span class="ll-step-num">05</span>
                    <span class="ll-step-label"><?php echo $safe($statusLabels['delayed']); ?></span>
                </li>
                <li class="ll-stepper-step ll-stepper-step--returned">
                    <span class="ll-step-num">06</span>
                    <span class="ll-step-label"><?php echo $safe($statusLabels['returned']); ?></span>
                </li>
            </ol>

            <div class="ll-status-legend-row" aria-label="Status-Badge-Übersicht">
                <?php foreach ($statusLabels as $key => $label) : ?>
                    <span class="ll-status-badge ll-status--<?php echo $safe($key); ?>"><?php echo $safe($label); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         Demo-Sendungen — Card-Pattern (links-anchored Status)
         ════════════════════════════════════════════════════ -->
    <section class="ll-section ll-section--alt" aria-label="Aktuelle Sendungen (Demo)">
        <div class="ll-container">
            <div class="ll-section-header">
                <span class="ll-eyebrow">Live-Demo</span>
                <h2>Sendungen im Überblick</h2>
                <p>Card-Layout für Scan-Lesbarkeit: Status links, Tracking-ID in Monospace, Route + ETA als operative Kerninfo.</p>
            </div>

            <div class="ll-shipment-grid">
                <?php foreach ($demoShipments as $s) : ?>
                    <article class="ll-shipment ll-shipment--<?php echo $safe($s['status']); ?> ll-reveal">
                        <div class="ll-shipment-head">
                            <span class="ll-tracking-id"><?php echo $safe($s['tracking']); ?></span>
                            <span class="ll-status-badge ll-status--<?php echo $safe($s['status']); ?>">
                                <?php echo $safe($statusLabels[$s['status']]); ?>
                            </span>
                        </div>
                        <div class="ll-shipment-route">
                            <span><?php echo $safe($s['origin']); ?></span>
                            <span class="ll-route-arrow" aria-hidden="true">→</span>
                            <span><?php echo $safe($s['dest']); ?></span>
                        </div>
                        <div class="ll-shipment-meta">
                            <span><?php echo $safe($s['leg']); ?></span>
                            <span class="ll-shipment-eta"><?php echo $safe($s['eta']); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         KPI Dashboard
         ════════════════════════════════════════════════════ -->
    <section class="ll-section ll-section-dark" aria-label="Plattform-Kennzahlen">
        <div class="ll-container">
            <div class="ll-section-header">
                <span class="ll-eyebrow">Operational Metrics</span>
                <h2>Plattform-Performance</h2>
                <p>Live-Indikatoren aller verbundenen Carrier – aktualisiert je Sendungs-Event.</p>
            </div>

            <div class="ll-kpi-grid">
                <div class="ll-kpi-card ll-reveal">
                    <div class="ll-kpi-kicker">Q3 / 2026</div>
                    <div class="ll-kpi-number">98.4%</div>
                    <div class="ll-kpi-label"><?php echo $safe($kpiPunctLabel); ?></div>
                    <div class="ll-kpi-delta ll-kpi-delta--up">▲ +0.6 pp</div>
                </div>
                <div class="ll-kpi-card ll-reveal">
                    <div class="ll-kpi-kicker">Last 30d</div>
                    <div class="ll-kpi-number">92.1%</div>
                    <div class="ll-kpi-label"><?php echo $safe($kpiEfficiencyLbl); ?></div>
                    <div class="ll-kpi-delta ll-kpi-delta--up">▲ +1.2 pp</div>
                </div>
                <div class="ll-kpi-card ll-reveal">
                    <div class="ll-kpi-kicker">Last 30d</div>
                    <div class="ll-kpi-number">0.18%</div>
                    <div class="ll-kpi-label"><?php echo $safe($kpiDamageLabel); ?></div>
                    <div class="ll-kpi-delta ll-kpi-delta--down">▼ -0.04 pp</div>
                </div>
                <div class="ll-kpi-card ll-reveal">
                    <div class="ll-kpi-kicker">Active</div>
                    <div class="ll-kpi-number">1.247</div>
                    <div class="ll-kpi-label">Sendungen im Hauptlauf</div>
                    <div class="ll-kpi-delta ll-kpi-delta--flat">— stabil</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         Services
         ════════════════════════════════════════════════════ -->
    <section class="ll-section" aria-label="Logistik-Dienstleistungen">
        <div class="ll-container">
            <div class="ll-section-header">
                <span class="ll-eyebrow">Services</span>
                <h2><?php echo $safe($servicesTitle); ?></h2>
                <p>Vier Kernbereiche – modular kombinierbar je Sendung, je Lane, je Kunde.</p>
            </div>

            <div class="ll-service-grid">
                <?php foreach ($services as $svc) : ?>
                    <article class="ll-service-card ll-reveal">
                        <span class="ll-service-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"
                                 focusable="false" aria-hidden="true">
                                <?php echo $serviceIcon((string) $svc['icon']); ?>
                            </svg>
                        </span>
                        <h3><?php echo $safe((string) $svc['title']); ?></h3>
                        <p><?php echo $safe((string) $svc['desc']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         Capacity Booking (Skizze)
         ════════════════════════════════════════════════════ -->
    <section class="ll-section ll-section--alt" aria-label="Kapazität buchen">
        <div class="ll-container">
            <div class="ll-section-header">
                <span class="ll-eyebrow">Direct Booking</span>
                <h2><?php echo $safe($capacityTitle); ?></h2>
                <p>Anfrage-Skizze: tatsächliche Buchung erfolgt nach Login im Dashboard.</p>
            </div>

            <form class="ll-capacity-form ll-reveal" method="post" action="<?php echo $regUrl; ?>" aria-label="Kapazitätsanfrage">
                <div class="ll-form-field">
                    <label for="ll-cap-origin">Von</label>
                    <input id="ll-cap-origin" type="text" name="origin" placeholder="z. B. Hamburg" autocomplete="off">
                </div>
                <div class="ll-form-field">
                    <label for="ll-cap-dest">Nach</label>
                    <input id="ll-cap-dest" type="text" name="dest" placeholder="z. B. München" autocomplete="off">
                </div>
                <div class="ll-form-field">
                    <label for="ll-cap-units"><?php echo $safe($capacityUnits); ?></label>
                    <input id="ll-cap-units" type="text" name="units" placeholder="4 Paletten · 850 kg · 6,4 m³" autocomplete="off">
                </div>
                <div class="ll-form-field">
                    <label for="ll-cap-date">Abholtermin</label>
                    <input id="ll-cap-date" type="date" name="pickup_date">
                </div>
                <div class="ll-form-actions">
                    <button type="submit" class="ll-btn ll-btn-accent">Anfrage starten</button>
                </div>
            </form>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         Partner Netzwerk
         ════════════════════════════════════════════════════ -->
    <section class="ll-section" aria-label="Logistik-Partner">
        <div class="ll-container">
            <div class="ll-section-header">
                <span class="ll-eyebrow">Network</span>
                <h2><?php echo $safe($networkTitle); ?></h2>
                <p>Carrier, Spediteure und Lagerbetreiber – europaweit verbunden über ein gemeinsames Status-Protokoll.</p>
            </div>

            <div class="ll-partner-row">
                <div class="ll-partner">DACH-Spedition</div>
                <div class="ll-partner">EU-Cargo</div>
                <div class="ll-partner">NordLog</div>
                <div class="ll-partner">SüdExpress</div>
                <div class="ll-partner">AlpenTrans</div>
                <div class="ll-partner">LastMile.eu</div>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════════════
         CTA – Registrierung
         ════════════════════════════════════════════════════ -->
    <?php if (!$isLoggedIn && $ctaTitle !== '') : ?>
        <section class="ll-section--network-cta" aria-label="Registrierungsaufruf">
            <div class="ll-container">
                <h2 class="ll-network-cta-title"><?php echo $safe($ctaTitle); ?></h2>
                <?php if ($ctaText !== '') : ?>
                    <p class="ll-network-cta-text"><?php echo $safe($ctaText); ?></p>
                <?php endif; ?>
                <a href="<?php echo $regUrl; ?>" class="ll-btn ll-btn-accent ll-btn-lg ll-network-cta-button">
                    Kostenlos registrieren
                </a>
            </div>
        </section>
    <?php endif; ?>

</main>
