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

// ── Services (eigene Kategorie) ──────────────────────────────────────────────
$svc = ptc_customizer_category('services');
$svcGet = function (string $key, mixed $default = '') use ($svc): mixed {
    return $svc[$key] ?? $default;
};
$svcBool = function (string $key, bool $default = true) use ($svcGet): bool {
    return filter_var($svcGet($key, $default), FILTER_VALIDATE_BOOLEAN);
};

// ── Events (eigene Kategorie) ────────────────────────────────────────────────
$evt = ptc_customizer_category('events');
$evtGet = function (string $key, mixed $default = '') use ($evt): mixed {
    return $evt[$key] ?? $default;
};
$evtBool = function (string $key, bool $default = true) use ($evtGet): bool {
    return filter_var($evtGet($key, $default), FILTER_VALIDATE_BOOLEAN);
};

// ── FAQ (eigene Kategorie) ───────────────────────────────────────────────────
$faqCat = ptc_customizer_category('faq');
$faqGet = function (string $key, mixed $default = '') use ($faqCat): mixed {
    return $faqCat[$key] ?? $default;
};
$faqBool = function (string $key, bool $default = true) use ($faqGet): bool {
    return filter_var($faqGet($key, $default), FILTER_VALIDATE_BOOLEAN);
};

// Sektions-Sichtbarkeit
$showHero     = $hpBool('show_hero', true);
$showServices = $svcBool('show_services', true);
$showEvents   = $evtBool('show_events', true);
$showFaq      = $faqBool('show_faq', true);
$showCta      = $hpBool('show_cta', true);

// Hero
$heroBadge        = (string) $hpGet('hero_badge', 'Ihr Spezialist für Personalvermittlung und berufliche Weiterbildung');
$heroTitle        = (string) $hpGet('hero_title', 'Menschen verbinden. <span class="highlight">Kompetenz entwickeln.</span>');
$heroText         = (string) $hpGet('hero_text', 'Personalvermittlung, Arbeitnehmerüberlassung und praxisnahe Qualifizierung – für Kandidaten und Arbeitgeber aus einer Hand.');
$heroCta1Label    = (string) $hpGet('hero_cta_primary_label', 'Für Kandidaten');
$heroCta1Url      = (string) $hpGet('hero_cta_primary_url', '#kandidaten');
$heroCta2Label    = (string) $hpGet('hero_cta_secondary_label', 'Für Arbeitgeber');
$heroCta2Url      = (string) $hpGet('hero_cta_secondary_url', '#arbeitgeber');
$heroBgImage      = (string) $hpGet('hero_bg_image', '');

// Services
$servicesTag      = (string) $svcGet('services_tag', 'Unsere Leistungen');
$servicesTitle    = (string) $svcGet('services_title', 'Unsere Dienstleistungen');
$servicesSubtitle = (string) $svcGet('services_subtitle', 'Von der Vermittlung über Zeitarbeit bis zur beruflichen Qualifizierung – passgenau für Ihren Bedarf.');
$servicesCols     = (string) $svcGet('services_columns', '3');
$servicesBgStyle  = (string) $svcGet('services_bg_style', 'default');
$servicesCardStyle = (string) $svcGet('services_card_style', 'bordered');
$servicesIconStyle = (string) $svcGet('services_icon_style', 'circle');
$servicesShowHover = $svcBool('services_show_hover', true);
$servicesShowIcons = $svcBool('services_show_icons', true);
$servicesMaxItems  = (int) $svcGet('services_max_items', 6);
$servicesShowCta  = $svcBool('services_show_cta', false);
$servicesCtaLabel = (string) $svcGet('services_cta_label', 'Alle Leistungen entdecken');
$servicesCtaUrl   = (string) $svcGet('services_cta_url', '/leistungen');

// Dynamische Service-Karten (1–12)
$serviceCards = [];
for ($i = 1; $i <= 12; $i++) {
    $icon  = trim((string) $svcGet("service_{$i}_icon", ''));
    $image = trim((string) $svcGet("service_{$i}_image", ''));
    $title = trim((string) $svcGet("service_{$i}_title", ''));
    $text  = trim((string) $svcGet("service_{$i}_text", ''));
    $url   = trim((string) $svcGet("service_{$i}_url", ''));
    if ($icon !== '' || $title !== '' || $image !== '') {
        $serviceCards[] = ['icon' => $icon, 'image' => $image, 'title' => $title, 'text' => $text, 'url' => $url];
    }
}
if (count($serviceCards) > $servicesMaxItems) {
    $serviceCards = array_slice($serviceCards, 0, $servicesMaxItems);
}

// Events
$eventsTag          = (string) $evtGet('events_tag', 'Veranstaltungen');
$eventsTitle        = (string) $evtGet('events_title', 'Aktuelle Termine & Angebote');
$eventsSubtitle     = (string) $evtGet('events_subtitle', 'Entdecken Sie unsere aktuellen Kursangebote, Workshops und Veranstaltungen.');
$eventsSource       = (string) $evtGet('events_source', 'auto');
$eventsMaxItems     = (int) $evtGet('events_max_items', 6);
$eventsCols         = (string) $evtGet('events_columns', '3');
$eventsBgStyle      = (string) $evtGet('events_bg_style', 'alt');
$eventsCardStyle    = (string) $evtGet('events_card_style', 'bordered');
$eventsDateBadge    = $evtBool('events_show_date_badge', true);
$eventsLinkText     = (string) $evtGet('events_link_text', 'Mehr erfahren →');
$eventsShowEmpty    = $evtBool('events_show_empty', true);
$eventsEmptyText    = (string) $evtGet('events_empty_text', 'Neue Termine werden in Kürze veröffentlicht');
$eventsEmptyHint    = (string) $evtGet('events_empty_hint', 'Schauen Sie bald wieder vorbei oder kontaktieren Sie uns direkt.');
$eventsShowCta      = $evtBool('events_show_cta', false);
$eventsCtaLabel     = (string) $evtGet('events_cta_label', 'Alle Termine ansehen');
$eventsCtaUrl       = (string) $evtGet('events_cta_url', '/termine');

// FAQ
$faqTag            = (string) $faqGet('faq_tag', 'Wissenswertes');
$faqTitle          = (string) $faqGet('faq_title', 'Häufig gestellte Fragen');
$faqSubtitle       = (string) $faqGet('faq_subtitle', 'Hier finden Sie Antworten auf die wichtigsten Fragen zu unseren Dienstleistungen.');
$faqStyle          = (string) $faqGet('faq_style', 'accordion');
$faqMaxWidth       = (int) $faqGet('faq_max_width', 720);
$faqBgStyle        = (string) $faqGet('faq_bg_style', 'default');
$faqShowCta        = $faqBool('faq_show_cta', false);
$faqCtaText        = (string) $faqGet('faq_cta_text', 'Ihre Frage war nicht dabei?');
$faqCtaLabel       = (string) $faqGet('faq_cta_label', 'Kontaktieren Sie uns');
$faqCtaUrl         = (string) $faqGet('faq_cta_url', '/#kontakt');

// Dynamische FAQ-Items (1–8)
$faqItems = [];
for ($i = 1; $i <= 8; $i++) {
    $q = trim((string) $faqGet("faq_{$i}_question", ''));
    $a = trim((string) $faqGet("faq_{$i}_answer", ''));
    if ($q !== '') {
        $faqItems[] = ['question' => $q, 'answer' => $a];
    }
}

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

                <h1><?php echo ptc_safe_headline($heroTitle); ?></h1>

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

<!-- ██ VERMITTLUNGSPROZESS + DUAL CTA ████████████████████████████████████ -->
<section class="ptc-pipeline" id="kandidaten" aria-labelledby="ptc-pipeline-title">
    <div class="ptc-container">
        <div class="ptc-section-head">
            <span class="ptc-section-tag">Unser Prozess</span>
            <h2 id="ptc-pipeline-title">Vom Erstkontakt bis zur Einstellung</h2>
            <p>Transparente Schritte für Kandidaten und Arbeitgeber – nachvollziehbar in jeder Phase.</p>
        </div>
        <ol class="ptc-pipeline-steps">
            <li class="ptc-pipeline-step">
                <h3>Bewerbung</h3>
                <p>Profil, Qualifikation und Zielposition erfassen.</p>
            </li>
            <li class="ptc-pipeline-step">
                <h3>Vorauswahl</h3>
                <p>Abgleich mit offenen Stellen und Anforderungsprofilen.</p>
            </li>
            <li class="ptc-pipeline-step">
                <h3>Vermittlung</h3>
                <p>Gespräche, Feedback und passende Unternehmen.</p>
            </li>
            <li class="ptc-pipeline-step">
                <h3>Einstellung</h3>
                <p>Vertrag, Onboarding und optional Weiterbildung.</p>
            </li>
        </ol>
        <div class="ptc-dual-cta" id="arbeitgeber">
            <article class="ptc-dual-cta-card ptc-dual-cta-card--candidates">
                <h3>Für Kandidaten</h3>
                <p class="ptc-text-muted">Jobsuche, Bewerbungscoaching und Qualifizierung auf dem Weg zu Ihrem nächsten Schritt.</p>
                <a href="<?php echo htmlspecialchars($heroCta1Url, ENT_QUOTES, 'UTF-8'); ?>" class="btn-ptc btn-ptc-accent btn-ptc-sm">Stellen entdecken</a>
            </article>
            <article class="ptc-dual-cta-card ptc-dual-cta-card--employers">
                <h3>Für Arbeitgeber</h3>
                <p class="ptc-text-muted">Fachkräfte, Zeitarbeit und Schulungen – wenn Kapazität und Qualifikation zählen.</p>
                <a href="<?php echo htmlspecialchars($heroCta2Url, ENT_QUOTES, 'UTF-8'); ?>" class="btn-ptc btn-ptc-primary btn-ptc-sm">Personal anfragen</a>
            </article>
        </div>
    </div>
</section>

<?php if ($showServices): ?>
<!-- ██ DIENSTLEISTUNGEN ██████████████████████████████████████████████████ -->
<section class="ptc-section<?php echo $servicesBgStyle === 'alt' ? ' ptc-section-alt' : ($servicesBgStyle === 'navy' ? ' ptc-section-navy' : ''); ?>" id="dienstleistungen" data-section="services">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <?php if ($servicesTag !== ''): ?>
                <span class="ptc-section-tag"><?php echo htmlspecialchars($servicesTag, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($servicesTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($servicesSubtitle !== ''): ?>
                <p><?php echo htmlspecialchars($servicesSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <?php if (!empty($serviceCards)): ?>
        <div class="ptc-services-grid" data-cols="<?php echo htmlspecialchars($servicesCols, ENT_QUOTES, 'UTF-8'); ?>" data-card-style="<?php echo htmlspecialchars($servicesCardStyle, ENT_QUOTES, 'UTF-8'); ?>" data-icon-style="<?php echo htmlspecialchars($servicesIconStyle, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $servicesShowHover ? ' data-hover="true"' : ''; ?> data-icons="<?php echo $servicesShowIcons ? 'visible' : 'hidden'; ?>">

            <?php foreach ($serviceCards as $card): ?>
            <article class="ptc-service-card<?php echo $card['image'] !== '' ? ' ptc-service-card--has-image' : ''; ?>">
                <?php if ($card['image'] !== ''): ?>
                    <div class="ptc-service-image">
                        <img src="<?php echo htmlspecialchars($card['image'], ENT_QUOTES, 'UTF-8'); ?>"
                             alt="<?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?>"
                             loading="lazy">
                    </div>
                <?php elseif ($card['icon'] !== ''): ?>
                    <div class="ptc-service-icon"><?php echo htmlspecialchars($card['icon'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <div class="ptc-service-body">
                    <?php if ($card['title'] !== ''): ?>
                        <h3><?php echo htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php endif; ?>
                    <?php if ($card['text'] !== ''): ?>
                        <p><?php echo htmlspecialchars($card['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <?php if ($card['url'] !== ''): ?>
                        <a href="<?php echo htmlspecialchars($card['url'], ENT_QUOTES, 'UTF-8'); ?>" class="ptc-service-link">Mehr erfahren →</a>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>

        <?php if ($servicesShowCta && $servicesCtaLabel !== ''): ?>
        <div class="ptc-section-cta">
            <a href="<?php echo htmlspecialchars($servicesCtaUrl, ENT_QUOTES, 'UTF-8'); ?>"
               class="btn-ptc btn-ptc-accent btn-ptc-lg">
                <?php echo htmlspecialchars($servicesCtaLabel, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_after_services'); ?>

<?php if ($showEvents): ?>
<!-- ██ AKTUELLE TERMINE ██████████████████████████████████████████████████ -->
<section class="ptc-section<?php echo $eventsBgStyle === 'alt' ? ' ptc-section-alt' : ($eventsBgStyle === 'navy' ? ' ptc-section-navy' : ''); ?>" id="termine" data-section="events">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <?php if ($eventsTag !== ''): ?>
                <span class="ptc-section-tag"><?php echo htmlspecialchars($eventsTag, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($eventsTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($eventsSubtitle !== ''): ?>
                <p><?php echo htmlspecialchars($eventsSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <?php
        // Versuche Events aus dem Plugin zu laden
        $events = [];
        $usePlugin = ($eventsSource === 'auto' || $eventsSource === 'plugin');
        $useManual = ($eventsSource === 'auto' || $eventsSource === 'manual');

        if ($usePlugin && \CMS\PluginManager::instance()->isPluginActive('cms-events')) {
            try {
                $db     = \CMS\Database::instance();
                $prefix = $db->prefix();
                $stmt   = $db->prepare(
                    "SELECT * FROM {$prefix}events WHERE status = 'published' AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT ?"
                );
                $stmt->execute([$eventsMaxItems]);
                $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                $events = [];
            }
        }

        // Manuelle Termine als Fallback (oder als einzige Quelle)
        if (empty($events) && $useManual) {
            for ($i = 1; $i <= 6; $i++) {
                $eTitle = trim((string) $evtGet("event_{$i}_title", ''));
                if ($eTitle !== '') {
                    $events[] = [
                        'title'      => $eTitle,
                        'event_date' => trim((string) $evtGet("event_{$i}_date", '')),
                        'text'       => trim((string) $evtGet("event_{$i}_text", '')),
                        'url'        => trim((string) $evtGet("event_{$i}_url", '')),
                        'is_manual'  => true,
                    ];
                }
            }
        }
        ?>

        <?php if (!empty($events)): ?>
            <div class="ptc-events-grid" data-cols="<?php echo htmlspecialchars($eventsCols, ENT_QUOTES, 'UTF-8'); ?>" data-card-style="<?php echo htmlspecialchars($eventsCardStyle, ENT_QUOTES, 'UTF-8'); ?>">
                <?php foreach ($events as $event): ?>
                    <article class="ptc-event-card">
                        <?php if ($eventsDateBadge): ?>
                        <div class="ptc-event-date">
                            <span class="ptc-event-icon">📅</span>
                            <strong><?php
                                if (!empty($event['event_date'])) {
                                    if (!empty($event['is_manual'])) {
                                        echo htmlspecialchars($event['event_date'], ENT_QUOTES, 'UTF-8');
                                    } else {
                                        $d = new \DateTime($event['event_date']);
                                        echo htmlspecialchars($d->format('d. M.'), ENT_QUOTES, 'UTF-8');
                                    }
                                } else {
                                    echo 'Demnächst';
                                }
                            ?></strong>
                        </div>
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($event['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
                        <?php if (!empty($event['text'])): ?>
                            <p class="ptc-text-muted ptc-text-muted--sm"><?php echo htmlspecialchars($event['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <?php
                        $eventUrl = '';
                        if (!empty($event['url'])) {
                            $eventUrl = $event['url'];
                        } elseif (!empty($event['id']) && empty($event['is_manual'])) {
                            $eventUrl = $siteUrl . '/events/' . (int)$event['id'];
                        }
                        if ($eventUrl !== ''):
                        ?>
                            <a href="<?php echo htmlspecialchars($eventUrl, ENT_QUOTES, 'UTF-8'); ?>" class="ptc-event-link"><?php echo htmlspecialchars($eventsLinkText, ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php elseif ($eventsShowEmpty): ?>
            <div class="ptc-events-grid">
                <article class="ptc-event-card">
                    <div class="ptc-event-date">
                        <span class="ptc-event-icon">📅</span>
                        <strong>Demnächst</strong>
                    </div>
                    <h4><?php echo htmlspecialchars($eventsEmptyText, ENT_QUOTES, 'UTF-8'); ?></h4>
                    <?php if ($eventsEmptyHint !== ''): ?>
                        <p class="ptc-text-muted ptc-text-muted--sm"><?php echo htmlspecialchars($eventsEmptyHint, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </article>
            </div>
        <?php endif; ?>

        <?php if ($eventsShowCta && $eventsCtaLabel !== ''): ?>
        <div class="ptc-section-cta">
            <a href="<?php echo htmlspecialchars($eventsCtaUrl, ENT_QUOTES, 'UTF-8'); ?>"
               class="btn-ptc btn-ptc-accent btn-ptc-lg">
                <?php echo htmlspecialchars($eventsCtaLabel, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
        <?php endif; ?>

        <?php
        // MS Booking Integration
        $bookingUrl   = trim((string) $evtGet('events_booking_url', ''));
        $bookingTitle = trim((string) $evtGet('events_booking_title', 'Online-Termin buchen'));
        $bookingHeight = (int) $evtGet('events_booking_height', 600);
        if ($bookingUrl !== ''):
        ?>
        <div class="ptc-booking-embed">
            <?php if ($bookingTitle !== ''): ?>
                <h3><?php echo htmlspecialchars($bookingTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
            <?php endif; ?>
            <iframe src="<?php echo htmlspecialchars($bookingUrl, ENT_QUOTES, 'UTF-8'); ?>"
                    width="100%" height="<?php echo (int) $bookingHeight; ?>"
                    frameborder="0" scrolling="yes"
                    title="<?php echo htmlspecialchars($bookingTitle, ENT_QUOTES, 'UTF-8'); ?>"
                    loading="lazy"></iframe>
        </div>
        <?php endif; ?>

    </div>
</section>
<?php endif; ?>

<?php \CMS\Hooks::doAction('home_after_events'); ?>

<?php if ($showFaq && !empty($faqItems)): ?>
<!-- ██ HÄUFIG GESTELLTE FRAGEN ██████████████████████████████████████████ -->
<section class="ptc-section<?php echo $faqBgStyle === 'alt' ? ' ptc-section-alt' : ''; ?>" id="faq" data-section="faq">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <?php if ($faqTag !== ''): ?>
                <span class="ptc-section-tag"><?php echo htmlspecialchars($faqTag, ENT_QUOTES, 'UTF-8'); ?></span>
            <?php endif; ?>
            <h2><?php echo htmlspecialchars($faqTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if ($faqSubtitle !== ''): ?>
                <p><?php echo htmlspecialchars($faqSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
        </div>

        <div class="ptc-faq-list<?php echo $faqMaxWidth > 0 ? ' ptc-faq-list--constrained' : ''; ?>">

            <?php foreach ($faqItems as $item): ?>
            <details class="ptc-faq-item"<?php echo $faqStyle === 'open' ? ' open' : ''; ?>>
                <summary><?php echo htmlspecialchars($item['question'], ENT_QUOTES, 'UTF-8'); ?></summary>
                <div class="ptc-faq-answer">
                    <p><?php echo nl2br(htmlspecialchars($item['answer'], ENT_QUOTES, 'UTF-8')); ?></p>
                </div>
            </details>
            <?php endforeach; ?>

        </div>

        <?php if ($faqShowCta): ?>
        <div class="ptc-section-cta ptc-section-cta--spaced">
            <?php if ($faqCtaText !== ''): ?>
                <p class="ptc-text-muted"><?php echo htmlspecialchars($faqCtaText, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($faqCtaLabel !== ''): ?>
                <a href="<?php echo htmlspecialchars($faqCtaUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn-ptc btn-ptc-accent">
                    <?php echo htmlspecialchars($faqCtaLabel, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

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
