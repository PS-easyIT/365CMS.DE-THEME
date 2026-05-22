<?php
/**
 * MedCare Pro Theme – Home Template
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

// Hero
$heroBadge    = (string) mc_get_setting('medical_hero', 'hero_badge',               '✚ Gesundheitsplattform');
$heroHeadline = (string) mc_get_setting('medical_hero', 'hero_headline',            'Ihren Arzt einfach online finden');
$heroSubline  = (string) mc_get_setting('medical_hero', 'hero_subline',             'Ärzte, Kliniken und Fachspezialisten in Ihrer Region – schnell, sicher und kostenlos.');
$heroCta      = (string) mc_get_setting('medical_hero', 'hero_cta_label',           'Arzt suchen');
$heroCtaUrl   = (string) mc_get_setting('medical_hero', 'hero_cta_url',             '/aerzte');
$heroSecCta   = (string) mc_get_setting('medical_hero', 'hero_secondary_cta_label', 'Termin buchen');
$heroSecUrl   = (string) mc_get_setting('medical_hero', 'hero_secondary_cta_url',   '/termin');
$showStats    = filter_var(mc_get_setting('medical_hero', 'show_stats_bar', true), FILTER_VALIDATE_BOOLEAN);
$statNum1     = (string) mc_get_setting('medical_hero', 'stat_doctors_count',     '1.500+');
$statLbl1     = (string) mc_get_setting('medical_hero', 'stat_doctors_label',     'Ärzte & Kliniken');
$statNum2     = (string) mc_get_setting('medical_hero', 'stat_specialties_count', '40+');
$statLbl2     = (string) mc_get_setting('medical_hero', 'stat_specialties_label', 'Fachgebiete');
$statLbl3     = (string) mc_get_setting('medical_hero', 'stat_booking_label',     'Online-Termine');

// Content
$doctorsTitle = (string) mc_get_setting('medical_content', 'doctor_section_title',      'Unsere Fachärzte');
$specTitle    = (string) mc_get_setting('medical_content', 'specialties_section_title', 'Medizinische Fachbereiche');
$bookingTitle = (string) mc_get_setting('medical_content', 'booking_section_title',     'Termin vereinbaren');
$bookingText  = (string) mc_get_setting('medical_content', 'booking_intro_text',        'Online-Terminbuchung rund um die Uhr – ohne Warteschleife.');
$gkvLabel     = (string) mc_get_setting('medical_content', 'insurance_label_public',    'Kassenpatient (GKV)');
$pkvLabel     = (string) mc_get_setting('medical_content', 'insurance_label_private',   'Privatpatient (PKV)');
$emergInfo    = (string) mc_get_setting('medical_content', 'emergency_info_text',       '');
$ctaTitle     = (string) mc_get_setting('medical_content', 'cta_section_title',         'Ihr Online-Patientenportal');
$ctaText      = (string) mc_get_setting('medical_content', 'cta_section_text',          '');

$isLoggedIn = theme_is_logged_in();
$doctorsUrl = $safe(mc_href($heroCtaUrl));
$bookingUrl = $safe(mc_href($heroSecUrl));
$fieldsUrl  = $safe(theme_route_url('fields'));
$registerUrl= $safe(theme_route_url('register'));
$searchActionUrl = $safe(mc_href('/aerzte'));
?>
<main id="main" class="mc-main" role="main">

    <?php if (trim($emergInfo) !== '') : ?>
    <div class="mc-emergency-notice" role="alert" aria-live="polite">
        <div class="mc-container mc-emergency-notice-row">
            <strong class="mc-emergency-notice-tag">
                <span aria-hidden="true">⚕</span> Wichtiger Hinweis
            </strong>
            <span><?php echo $safe($emergInfo); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- ═══ Hero ═══════════════════════════════════════════════════════════ -->
    <section class="mc-hero" aria-labelledby="hero-heading">
        <div class="mc-hero-backdrop" aria-hidden="true"></div>
        <div class="mc-container mc-hero-inner">
            <?php if (trim($heroBadge) !== '') : ?>
                <div class="mc-hero-badge"><?php echo $safe($heroBadge); ?></div>
            <?php endif; ?>
            <h1 id="hero-heading" class="mc-hero-heading"><?php echo $safe($heroHeadline); ?></h1>
            <?php if (trim($heroSubline) !== '') : ?>
                <p class="mc-hero-sub"><?php echo $safe($heroSubline); ?></p>
            <?php endif; ?>

            <form class="mc-hero-search"
                  role="search"
                  method="get"
                  action="<?php echo $searchActionUrl; ?>"
                  aria-label="Arzt suchen">
                <label for="hero-search-input" class="mc-visually-hidden">Arzt, Fachgebiet oder PLZ eingeben</label>
                <input id="hero-search-input"
                       type="search"
                       name="q"
                       placeholder="Arzt, Fachgebiet oder PLZ …"
                       autocomplete="off"
                       class="mc-hero-search-input">
                <button type="submit" class="mc-btn mc-btn-primary mc-hero-search-submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         focusable="false" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <?php echo $safe($heroCta); ?>
                </button>
            </form>

            <div class="mc-hero-actions">
                <a href="<?php echo $bookingUrl; ?>" class="mc-btn mc-btn-accent mc-btn-lg">
                    <span class="mc-btn-icon" aria-hidden="true">🗓</span>
                    <?php echo $safe($heroSecCta); ?>
                </a>
                <a href="<?php echo $doctorsUrl; ?>" class="mc-btn mc-btn-onhero">
                    Alle Ärzte anzeigen
                </a>
            </div>

            <?php if ($showStats) : ?>
            <ul class="mc-stats-row" aria-label="Plattform-Statistiken">
                <li class="mc-stat">
                    <span class="mc-stat-number"><?php echo $safe($statNum1); ?></span>
                    <span class="mc-stat-label"><?php echo $safe($statLbl1); ?></span>
                </li>
                <li class="mc-stat">
                    <span class="mc-stat-number"><?php echo $safe($statNum2); ?></span>
                    <span class="mc-stat-label"><?php echo $safe($statLbl2); ?></span>
                </li>
                <li class="mc-stat">
                    <span class="mc-stat-number">24/7</span>
                    <span class="mc-stat-label"><?php echo $safe($statLbl3); ?></span>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </section><!-- /.mc-hero -->

    <!-- ═══ Fachgebiete ════════════════════════════════════════════════════ -->
    <section class="mc-section mc-section--specialties" aria-labelledby="specialties-heading">
        <div class="mc-container">
            <div class="mc-section-header">
                <h2 id="specialties-heading"><?php echo $safe($specTitle); ?></h2>
                <p>Kompetente Versorgung in allen medizinischen Fachbereichen</p>
            </div>
            <?php
            $specialties = [
                ['slug' => 'general',     'label' => 'Allgemeinmedizin', 'icon' => '🩺', 'url' => '/fachgebiet/allgemeinmedizin'],
                ['slug' => 'cardio',      'label' => 'Kardiologie',      'icon' => '❤',  'url' => '/fachgebiet/kardiologie'],
                ['slug' => 'neuro',       'label' => 'Neurologie',       'icon' => '🧠', 'url' => '/fachgebiet/neurologie'],
                ['slug' => 'ortho',       'label' => 'Orthopädie',       'icon' => '🦴', 'url' => '/fachgebiet/orthopaedie'],
                ['slug' => 'derma',       'label' => 'Dermatologie',     'icon' => '🔬', 'url' => '/fachgebiet/dermatologie'],
                ['slug' => 'dental',      'label' => 'Zahnmedizin',      'icon' => '🦷', 'url' => '/fachgebiet/zahnmedizin'],
                ['slug' => 'psychology',  'label' => 'Psychologie',      'icon' => '🧘', 'url' => '/fachgebiet/psychologie'],
                ['slug' => 'surgery',     'label' => 'Chirurgie',        'icon' => '🩹', 'url' => '/fachgebiet/chirurgie'],
            ];
            ?>
            <ul class="mc-specialties-grid">
                <?php foreach ($specialties as $sp) : ?>
                <li class="mc-specialty">
                    <a href="<?php echo $safe(mc_href((string) $sp['url'])); ?>"
                       class="mc-specialty-card mc-specialty-card--<?php echo $safe($sp['slug']); ?>"
                       aria-label="Fachgebiet <?php echo $safe($sp['label']); ?> anzeigen">
                        <span class="mc-specialty-icon" aria-hidden="true"><?php echo $safe($sp['icon']); ?></span>
                        <span class="mc-specialty-name"><?php echo $safe($sp['label']); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
                <li class="mc-specialty">
                    <a href="<?php echo $fieldsUrl; ?>"
                       class="mc-specialty-card mc-specialty-card--more"
                       aria-label="Alle Fachgebiete anzeigen">
                        <span class="mc-specialty-icon" aria-hidden="true">→</span>
                        <span class="mc-specialty-name">Alle Fachgebiete</span>
                    </a>
                </li>
            </ul>
        </div>
    </section><!-- /.mc-specialties -->

    <!-- ═══ Ärzte ══════════════════════════════════════════════════════════ -->
    <section class="mc-section mc-section--doctors" aria-labelledby="doctors-heading">
        <div class="mc-container">
            <div class="mc-section-header">
                <h2 id="doctors-heading"><?php echo $safe($doctorsTitle); ?></h2>
                <p>Qualifizierte Experten für Ihre Gesundheit – geprüft und zertifiziert</p>
            </div>
            <div class="mc-insurance-filter" role="group" aria-label="Nach Versicherungstyp filtern">
                <button type="button"
                        class="mc-insurance-btn is-active"
                        data-insurance="all"
                        aria-pressed="true">Alle</button>
                <button type="button"
                        class="mc-insurance-btn"
                        data-insurance="gkv"
                        aria-pressed="false">
                    <span class="mc-insurance-badge mc-insurance--gkv"><?php echo $safe($gkvLabel); ?></span>
                </button>
                <button type="button"
                        class="mc-insurance-btn"
                        data-insurance="pkv"
                        aria-pressed="false">
                    <span class="mc-insurance-badge mc-insurance--pkv"><?php echo $safe($pkvLabel); ?></span>
                </button>
            </div>
            <div class="mc-doctor-cta-card">
                <div class="mc-doctor-cta-glyph" aria-hidden="true">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                         focusable="false" aria-hidden="true">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21c0-4.418 3.582-8 8-8s8 3.582 8 8"/>
                    </svg>
                </div>
                <h3 class="mc-doctor-cta-title">Alle Ärzte &amp; Therapeuten entdecken</h3>
                <p class="mc-doctor-cta-lead">
                    Finden Sie den passenden Spezialisten in Ihrer Nähe – mit Bewertungen, Öffnungszeiten und Online-Terminbuchung.
                </p>
                <a href="<?php echo $doctorsUrl; ?>" class="mc-btn mc-btn-primary">
                    Alle Ärzte anzeigen
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section><!-- /.mc-doctors -->

    <!-- ═══ Termin-CTA ═════════════════════════════════════════════════════ -->
    <?php if (!$isLoggedIn) : ?>
    <section class="mc-section mc-section--booking" aria-labelledby="booking-heading">
        <div class="mc-container">
            <div class="mc-booking-cta">
                <div class="mc-booking-cta__text">
                    <h2 id="booking-heading"><?php echo $safe($bookingTitle); ?></h2>
                    <?php if (trim($bookingText) !== '') : ?>
                        <p class="mc-booking-cta__lead"><?php echo $safe($bookingText); ?></p>
                    <?php endif; ?>
                    <ul class="mc-booking-features">
                        <li><span class="mc-tick" aria-hidden="true">✓</span> Sofortige Online-Buchung – 24 Stunden, 7 Tage die Woche</li>
                        <li><span class="mc-tick" aria-hidden="true">✓</span> Automatische Erinnerungen per E-Mail</li>
                        <li><span class="mc-tick" aria-hidden="true">✓</span> DSGVO-konforme Datenhaltung nach § 203 StGB</li>
                        <li><span class="mc-tick" aria-hidden="true">✓</span> Für GKV- und PKV-Patienten verfügbar</li>
                    </ul>
                </div>
                <div class="mc-booking-cta__actions">
                    <a href="<?php echo $bookingUrl; ?>" class="mc-btn mc-btn-primary mc-btn-lg">
                        <span class="mc-btn-icon" aria-hidden="true">🗓</span>
                        Termin buchen
                    </a>
                    <a href="<?php echo $registerUrl; ?>" class="mc-btn mc-btn-outline">
                        Als Arzt registrieren
                    </a>
                    <p class="mc-booking-dsgvo-note">
                        Ihre Daten werden gemäß DSGVO &amp; § 203 StGB geschützt.
                    </p>
                </div>
            </div>
        </div>
    </section><!-- /.mc-booking-cta -->
    <?php endif; ?>

    <!-- ═══ Trust ═════════════════════════════════════════════════════════ -->
    <section class="mc-section mc-section--trust" aria-labelledby="trust-heading">
        <div class="mc-container">
            <div class="mc-section-header">
                <h2 id="trust-heading">Warum MedCare Pro?</h2>
                <p>Qualität, Datenschutz und Verlässlichkeit – für Patienten und Ärzte</p>
            </div>
            <ul class="mc-trust-grid">
                <li class="mc-trust-item">
                    <div class="mc-trust-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                             focusable="false" aria-hidden="true">
                            <rect x="5" y="11" width="14" height="9" rx="2"/>
                            <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                        </svg>
                    </div>
                    <h3>DSGVO &amp; § 203 StGB</h3>
                    <p>Patientendaten werden nach höchsten Datenschutzstandards verarbeitet. Keine Datenweitergabe an Dritte.</p>
                </li>
                <li class="mc-trust-item">
                    <div class="mc-trust-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                             focusable="false" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                    </div>
                    <h3>Geprüfte Ärzte</h3>
                    <p>Jedes Arztprofil wird vor der Freischaltung auf Approbation und Qualifikation verifiziert.</p>
                </li>
                <li class="mc-trust-item">
                    <div class="mc-trust-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                             focusable="false" aria-hidden="true">
                            <rect x="6" y="3" width="12" height="18" rx="2"/>
                            <path d="M11 18h2"/>
                        </svg>
                    </div>
                    <h3>Online-Buchung 24/7</h3>
                    <p>Termin online vereinbaren – ohne Warteschleife, rund um die Uhr verfügbar.</p>
                </li>
                <li class="mc-trust-item">
                    <div class="mc-trust-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                             focusable="false" aria-hidden="true">
                            <path d="M12 17.3l-6.18 3.7 1.64-7.03L2 9.24l7.19-.61L12 2l2.81 6.63 7.19.61-5.46 4.73 1.64 7.03z"/>
                        </svg>
                    </div>
                    <h3>Echte Bewertungen</h3>
                    <p>Verifizierte Patientenbewertungen helfen, den richtigen Arzt für Ihre Bedürfnisse zu finden.</p>
                </li>
            </ul>
        </div>
    </section><!-- /.mc-trust -->

    <!-- ═══ Registrierungs-CTA ═════════════════════════════════════════════ -->
    <?php if (!$isLoggedIn && trim($ctaTitle) !== '') : ?>
    <section class="mc-section mc-section--cta" aria-labelledby="cta-heading">
        <div class="mc-container mc-cta-center">
            <h2 id="cta-heading"><?php echo $safe($ctaTitle); ?></h2>
            <?php if (trim($ctaText) !== '') : ?>
                <p class="mc-cta-lead"><?php echo $safe($ctaText); ?></p>
            <?php else : ?>
                <p class="mc-cta-lead">
                    Registrieren Sie sich und verwalten Sie Termine, Befunde und Nachrichten sicher und papierlos.
                </p>
            <?php endif; ?>
            <div class="mc-cta-actions">
                <a href="<?php echo $registerUrl; ?>" class="mc-btn mc-btn-primary mc-btn-lg">
                    Jetzt kostenlos registrieren
                </a>
                <a href="<?php echo $doctorsUrl; ?>" class="mc-btn mc-btn-outline">
                    Arzt suchen
                </a>
            </div>
        </div>
    </section><!-- /.mc-cta-section -->
    <?php endif; ?>

</main><!-- #main -->
