<?php
/**
 * PTC Theme – Homepage
 *
 * Alle Sektionen nutzen ptc_customizer_get() → Werte aus dem Theme-Customizer.
 * Sektionen: Hero → Dienstleistungen → Termine → FAQ → Kontakt-CTA
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = ptc_site_url();

// ── Customizer-Einstellungen (homepage-Kategorie) ────────────────────────────
$hp = ptc_customizer_category('homepage');
$hpGet = function (string $key, mixed $default = '') use ($hp): mixed {
    return $hp[$key] ?? $default;
};
$hpBool = function (string $key, bool $default = true) use ($hpGet): bool {
    return filter_var($hpGet($key, $default), FILTER_VALIDATE_BOOLEAN);
};

// Sektions-Sichtbarkeit
$showHero     = $hpBool('show_hero', true);
$showServices = $hpBool('show_services', true);
$showEvents   = $hpBool('show_events', true);
$showFaq      = $hpBool('show_faq', true);
$showCta      = $hpBool('show_cta', true);

// Hero
$heroBadge        = (string) $hpGet('hero_badge', 'Ihr Partner für Bildung, Karriere und Zukunft');
$heroTitle        = (string) $hpGet('hero_title', 'Willkommen bei <span class="highlight">PTC GmbH</span> – Ihr Partner für Personaldienstleistungen.');
$heroText         = (string) $hpGet('hero_text', 'Wir verbinden Menschen mit Chancen: Personalvermittlung, Arbeitnehmerüberlassung, Akademie & Bildung und Logistiklehrwerkstatt – alles aus einer Hand.');
$heroCta1Label    = (string) $hpGet('hero_cta_primary_label', 'Entdecken Sie Ihre Möglichkeiten');
$heroCta1Url      = (string) $hpGet('hero_cta_primary_url', '#dienstleistungen');
$heroCta2Label    = (string) $hpGet('hero_cta_secondary_label', 'Kontakt aufnehmen');
$heroCta2Url      = (string) $hpGet('hero_cta_secondary_url', '#kontakt');
$heroBgImage      = (string) $hpGet('hero_bg_image', '');

// Services
$servicesTitle    = (string) $hpGet('services_title', 'Unsere Dienstleistungen');
$servicesSubtitle = (string) $hpGet('services_subtitle', 'Von Aktivierung über Logistik bis hin zur Personalvermittlung – wir bieten maßgeschneiderte Lösungen.');
$servicesCols     = (string) $hpGet('services_columns', '3');

// Events
$eventsTitle      = (string) $hpGet('events_title', 'Aktuelle Termine & Angebote');
$eventsSubtitle   = (string) $hpGet('events_subtitle', 'Entdecken Sie unsere aktuellen Kursangebote, Workshops und Veranstaltungen.');

// FAQ
$faqTitle         = (string) $hpGet('faq_title', 'Häufig gestellte Fragen');
$faqSubtitle      = (string) $hpGet('faq_subtitle', 'Hier finden Sie Antworten auf die wichtigsten Fragen zu unseren Dienstleistungen.');

// CTA
$ctaTitle         = (string) $hpGet('cta_title', 'Bereit für den nächsten Karriereschritt?');
$ctaText          = (string) $hpGet('cta_text', 'Ob Arbeitnehmer auf Jobsuche oder Unternehmen mit Personalbedarf – sprechen Sie uns an. Wir finden die passende Lösung für Sie.');
$ctaBtnLabel      = (string) $hpGet('cta_button_label', 'Jetzt Kontakt aufnehmen');
$ctaBtnUrl        = (string) $hpGet('cta_button_url', '/#kontakt');
$ctaSecLabel      = (string) $hpGet('cta_secondary_label', 'Unsere Leistungen entdecken');
$ctaSecUrl        = (string) $hpGet('cta_secondary_url', '/#dienstleistungen');

// Footer-Kontaktdaten (für CTA-Buttons)
$footerEmail = ptc_customizer_get('footer', 'footer_email', '');
$footerPhone = ptc_customizer_get('footer', 'footer_phone', '');
?>

<?php \CMS\Hooks::doAction('home_before_hero'); ?>

<?php if ($showHero): ?>
<!-- ██ HERO ██████████████████████████████████████████████████████████████ -->
<section class="ptc-hero" id="start">
    <div class="ptc-container">
        <div class="ptc-hero-inner">
            <div class="ptc-hero-content">

                <?php if ($heroBadge !== ''): ?>
                    <span class="ptc-hero-badge"><?php echo htmlspecialchars($heroBadge, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>

                <h1><?php echo $heroTitle; /* HTML erlaubt – aus Customizer */ ?></h1>

                <?php if ($heroText !== ''): ?>
                    <p class="ptc-hero-lead">
                        <?php echo htmlspecialchars($heroText, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>

                <div class="ptc-hero-actions">
                    <?php if ($heroCta1Label !== ''): ?>
                        <a href="<?php echo htmlspecialchars($heroCta1Url, ENT_QUOTES, 'UTF-8'); ?>"
                           class="btn-ptc btn-ptc-accent btn-ptc-lg">
                            <?php echo htmlspecialchars($heroCta1Label, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($heroCta2Label !== ''): ?>
                        <a href="<?php echo htmlspecialchars($heroCta2Url, ENT_QUOTES, 'UTF-8'); ?>"
                           class="btn-ptc btn-ptc-outline btn-ptc-lg">
                            <?php echo htmlspecialchars($heroCta2Label, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hero-Bild (aus Customizer → Startseite → Hero-Hintergrundbild) -->
            <div class="ptc-hero-visual">
                <?php if ($heroBgImage !== ''): ?>
                    <div class="ptc-hero-image-box">
                        <img src="<?php echo htmlspecialchars($heroBgImage, ENT_QUOTES, 'UTF-8'); ?>"
                             alt="<?php echo ptc_site_title(); ?> – Hero"
                             class="ptc-hero-img" loading="lazy">
                    </div>
                <?php else: ?>
                    <div class="ptc-hero-image-placeholder" aria-hidden="true">
                        <span>👥</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_after_hero'); ?>

<?php if ($showServices): ?>
<!-- ██ DIENSTLEISTUNGEN ██████████████████████████████████████████████████ -->
<section class="ptc-section" id="dienstleistungen">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <span class="ptc-section-tag">Unsere Leistungen</span>
            <h2><?php echo htmlspecialchars($servicesTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($servicesSubtitle !== ''): ?>
                <p><?php echo htmlspecialchars($servicesSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <div class="ptc-services-grid" data-cols="<?php echo htmlspecialchars($servicesCols, ENT_QUOTES, 'UTF-8'); ?>">

            <article class="ptc-service-card">
                <div class="ptc-service-icon">🎯</div>
                <h3>Aktivierung und Vermittlung</h3>
                <p>Förderung für Ihren Erfolg – individuelle Aktivierungsmaßnahmen und passgenaue Vermittlung in den Arbeitsmarkt.</p>
            </article>

            <article class="ptc-service-card">
                <div class="ptc-service-icon">🏗️</div>
                <h3>Logistiklehrwerkstatt</h3>
                <p>Fachpraxis auf höchstem Niveau – praxisnahe Qualifizierung in Logistik, Lagerwirtschaft und Gabelstaplerführung.</p>
            </article>

            <article class="ptc-service-card">
                <div class="ptc-service-icon">🎓</div>
                <h3>Akademie und Bildung</h3>
                <p>Individuelle Coachings, Führungskräfte-Workshops und zertifizierte Weiterbildungsprogramme für Ihre Karriere.</p>
            </article>

            <article class="ptc-service-card">
                <div class="ptc-service-icon">🤝</div>
                <h3>Personalvermittlung</h3>
                <p>Den perfekten Job finden – wir bringen qualifizierte Fachkräfte und Unternehmen zusammen.</p>
            </article>

            <article class="ptc-service-card">
                <div class="ptc-service-icon">🔄</div>
                <h3>Arbeitnehmerüberlassung</h3>
                <p>Flexibilität für Ihr Unternehmen – temporäre Fachkräfte genau dann, wenn Sie sie brauchen.</p>
            </article>

            <article class="ptc-service-card">
                <div class="ptc-service-icon">🚀</div>
                <h3>Ausbildung</h3>
                <p>Start in Ihre berufliche Zukunft – Ausbildungsplätze und Einstiegsprogramme für junge Talente.</p>
            </article>

        </div>
    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_after_services'); ?>

<?php if ($showEvents): ?>
<!-- ██ AKTUELLE TERMINE ██████████████████████████████████████████████████ -->
<section class="ptc-section ptc-section-alt" id="termine">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <h2><?php echo htmlspecialchars($eventsTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($eventsSubtitle !== ''): ?>
                <p><?php echo htmlspecialchars($eventsSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <?php
        // Versuche Events aus dem Plugin zu laden
        $hasEventsPlugin = \CMS\PluginManager::instance()->isPluginActive('cms-events');
        $events = [];

        if ($hasEventsPlugin) {
            try {
                $db     = \CMS\Database::instance();
                $prefix = $db->prefix();
                $stmt   = $db->prepare(
                    "SELECT * FROM {$prefix}events WHERE status = 'published' AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5"
                );
                $stmt->execute();
                $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                $events = [];
            }
        }
        ?>

        <?php if (!empty($events)): ?>
            <div class="ptc-events-grid">
                <?php foreach ($events as $event): ?>
                    <article class="ptc-event-card">
                        <div class="ptc-event-date">
                            <span class="ptc-event-icon">📅</span>
                            <strong><?php
                                $d = new \DateTime($event['event_date']);
                                echo htmlspecialchars($d->format('d. M.'), ENT_QUOTES, 'UTF-8');
                            ?></strong>
                        </div>
                        <h4><?php echo htmlspecialchars($event['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
                        <a href="<?php echo $siteUrl; ?>/events/<?php echo (int)$event['id']; ?>" class="ptc-event-link">Mehr erfahren →</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="ptc-events-grid">
                <article class="ptc-event-card">
                    <div class="ptc-event-date">
                        <span class="ptc-event-icon">📅</span>
                        <strong>Demnächst</strong>
                    </div>
                    <h4>Neue Termine werden in Kürze veröffentlicht</h4>
                    <p style="color:var(--ptc-muted);font-size:.9rem;">Schauen Sie bald wieder vorbei oder kontaktieren Sie uns direkt.</p>
                </article>
            </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_after_events'); ?>

<?php if ($showFaq): ?>
<!-- ██ HÄUFIG GESTELLTE FRAGEN ██████████████████████████████████████████ -->
<section class="ptc-section" id="faq">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <h2><?php echo htmlspecialchars($faqTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($faqSubtitle !== ''): ?>
                <p><?php echo htmlspecialchars($faqSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <div class="ptc-faq-list">

            <details class="ptc-faq-item">
                <summary>Was sind Ihre Personaldienstleistungen?</summary>
                <div class="ptc-faq-answer">
                    <p>Wir bieten ein breites Spektrum an Personaldienstleistungen: Von der klassischen Personalvermittlung über Arbeitnehmerüberlassung bis hin zu individuellen Bildungs- und Qualifizierungsangeboten in unserer Akademie und Logistiklehrwerkstatt.</p>
                </div>
            </details>

            <details class="ptc-faq-item">
                <summary>Was ist Arbeitnehmerüberlassung?</summary>
                <div class="ptc-faq-answer">
                    <p>Bei der Arbeitnehmerüberlassung stellen wir Ihnen qualifizierte Mitarbeiter temporär zur Verfügung. Sie profitieren von Flexibilität, während die Fachkräfte bei uns angestellt bleiben. So können Sie schnell auf Personalbedarfe reagieren.</p>
                </div>
            </details>

            <details class="ptc-faq-item">
                <summary>Welche Weiterbildungen bieten Sie an?</summary>
                <div class="ptc-faq-answer">
                    <p>Unsere Akademie bietet Führungskräfte-Workshops, Gabelstaplerschulungen, individuelle Coachings und zertifizierte Weiterbildungsprogramme. Alle Angebote werden praxisnah durchgeführt und können auf Ihre Bedürfnisse angepasst werden.</p>
                </div>
            </details>

        </div>
    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_after_faq'); ?>

<?php if ($showCta): ?>
<!-- ██ CTA ████████████████████████████████████████████████████████████████ -->
<section class="ptc-cta-section" id="kontakt">
    <div class="ptc-container">

        <h2><?php echo htmlspecialchars($ctaTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="ptc-cta-actions">
            <?php if ($ctaBtnLabel !== ''): ?>
                <a href="<?php echo htmlspecialchars($ctaBtnUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn-ptc btn-ptc-white btn-ptc-lg">
                    <?php echo htmlspecialchars($ctaBtnLabel, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endif; ?>
            <?php if ($ctaSecLabel !== ''): ?>
                <a href="<?php echo htmlspecialchars($ctaSecUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn-ptc btn-ptc-outline-white btn-ptc-lg">
                    <?php echo htmlspecialchars($ctaSecLabel, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endif; ?>
        </div>

    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_content'); ?>
