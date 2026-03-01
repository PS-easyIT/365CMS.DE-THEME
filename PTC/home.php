<?php
/**
 * PTC Theme – Homepage
 *
 * Sektionen: Hero → Dienstleistungen → Termine → FAQ → Kontakt-CTA
 * Basierend auf dem PTC GmbH Corporate Design (Marineblau + Gold).
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = ptc_site_url();
?>

<!-- ██ HERO ██████████████████████████████████████████████████████████████ -->
<section class="ptc-hero" id="start">
    <div class="ptc-container">
        <div class="ptc-hero-inner">
            <div class="ptc-hero-content">
                <h1>
                    <?php
                    echo ptc_config(
                        'hero_headline',
                        'Willkommen bei <span class="highlight">PTC GmbH</span> – Ihr Partner für Personaldienstleistungen.'
                    );
                    ?>
                </h1>

                <p class="ptc-hero-lead">
                    <?php echo htmlspecialchars(
                        ptc_config('hero_text', 'Wir verbinden Menschen mit Chancen: Personalvermittlung, Arbeitnehmerüberlassung, Akademie & Bildung und Logistiklehrwerkstatt – alles aus einer Hand.'),
                        ENT_QUOTES, 'UTF-8'
                    ); ?>
                </p>

                <div class="ptc-hero-actions">
                    <a href="<?php echo $siteUrl; ?>/<?php echo htmlspecialchars(ptc_config('hero_cta_primary_url', '#dienstleistungen'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="btn-ptc btn-ptc-accent btn-ptc-lg">
                        <?php echo htmlspecialchars(ptc_config('hero_cta_primary_label', 'Entdecken Sie Ihre Möglichkeiten'), ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>
            </div>
            <div class="ptc-hero-visual">
                <div class="ptc-hero-image-placeholder" aria-hidden="true">
                    <span>👥</span>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ██ DIENSTLEISTUNGEN ██████████████████████████████████████████████████ -->
<section class="ptc-section" id="dienstleistungen">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <span class="ptc-section-tag">Unsere Leistungen</span>
            <h2>Was wir für Sie tun</h2>
            <p>Von der Personalvermittlung bis zur Ausbildung – wir begleiten Sie auf Ihrem Weg.</p>
        </div>

        <div class="ptc-services-grid">

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


<!-- ██ AKTUELLE TERMINE ██████████████████████████████████████████████████ -->
<section class="ptc-section ptc-section-alt" id="termine">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <h2>Aktuelle Termine</h2>
            <p>Informieren Sie sich über unsere nächsten Veranstaltungen und Workshops.</p>
        </div>

        <div class="ptc-events-grid">

            <article class="ptc-event-card">
                <div class="ptc-event-date">
                    <span class="ptc-event-icon">📅</span>
                    <strong>14. Nov.</strong>
                </div>
                <h4>Gabelstapler-Kurs</h4>
                <a href="#" class="ptc-event-link">Mehr erfahren →</a>
            </article>

            <article class="ptc-event-card">
                <div class="ptc-event-date">
                    <span class="ptc-event-icon">📅</span>
                    <strong>28. Nov.</strong>
                </div>
                <h4>Führungskräfte-Workshop</h4>
                <a href="#" class="ptc-event-link">Mehr erfahren →</a>
            </article>

            <article class="ptc-event-card">
                <div class="ptc-event-date">
                    <span class="ptc-event-icon">📅</span>
                    <strong>28. Nov.</strong>
                </div>
                <h4>Führungskräfte-Workshop</h4>
                <a href="#" class="ptc-event-link">Mehr erfahren →</a>
            </article>

            <article class="ptc-event-card">
                <div class="ptc-event-date">
                    <span class="ptc-event-icon">📅</span>
                    <strong>14. Nov.</strong>
                </div>
                <h4>Führung Gabelstapler-Workshop</h4>
                <a href="#" class="ptc-event-link">Mehr erfahren →</a>
            </article>

            <article class="ptc-event-card">
                <div class="ptc-event-date">
                    <span class="ptc-event-icon">📅</span>
                    <strong>28. Nov.</strong>
                </div>
                <h4>Personomnen und Personalromung</h4>
                <a href="#" class="ptc-event-link">Mehr erfahren →</a>
            </article>

        </div>
    </div>
</section>


<!-- ██ HÄUFIG GESTELLTE FRAGEN ██████████████████████████████████████████ -->
<section class="ptc-section" id="faq">
    <div class="ptc-container">

        <div class="ptc-section-head">
            <h2>Häufig gestellte Fragen</h2>
        </div>

        <div class="ptc-faq-list">

            <details class="ptc-faq-item">
                <summary>Was werte Ihr Personaldienstleistungen?</summary>
                <div class="ptc-faq-answer">
                    <p>Wir bieten ein breites Spektrum an Personaldienstleistungen: Von der klassischen Personalvermittlung über Arbeitnehmerüberlassung bis hin zu individuellen Bildungs- und Qualifizierungsangeboten in unserer Akademie und Logistiklehrwerkstatt.</p>
                </div>
            </details>

            <details class="ptc-faq-item">
                <summary>Was ist vim Personalinnerüberlassung?</summary>
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


<!-- ██ CTA ████████████████████████████████████████████████████████████████ -->
<section class="ptc-cta-section" id="kontakt">
    <div class="ptc-container">

        <h2><?php echo htmlspecialchars(ptc_config('cta_heading', 'Bereit für den nächsten Karriereschritt?'), ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars(ptc_config('cta_text', 'Ob Arbeitnehmer auf Jobsuche oder Unternehmen mit Personalbedarf – sprechen Sie uns an.'), ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="ptc-cta-actions">
            <a href="mailto:info@ptc-gmbh.de" class="btn-ptc btn-ptc-white btn-ptc-lg">
                ✉ E-Mail schreiben
            </a>
            <a href="tel:+4912345678" class="btn-ptc btn-ptc-outline-white btn-ptc-lg">
                📞 Jetzt anrufen
            </a>
        </div>

    </div>
</section>

<?php \CMS\Hooks::doAction('home_content'); ?>
