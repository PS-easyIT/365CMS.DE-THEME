<?php
declare(strict_types=1);

/**
 * Business Theme – Homepage / Company Presentation
 *
 * Zeigt alle Sektionen der Unternehmens-Landingpage:
 * Hero → Leistungen → Über uns → Zahlen & Fakten → CTA
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = biz_site_url();

try {
    $aboutImg = (string) \CMS\Services\ThemeCustomizer::instance()->get('biz_content', 'about_image', '');
} catch (\Throwable) {
    $aboutImg = '';
}
$aboutImg = biz_safe_url($aboutImg);
?>

<!-- ██ HERO ██████████████████████████████████████████████████████████████ -->
<section class="biz-hero" id="start">
    <div class="biz-container">
        <div class="biz-hero-inner">

            <div class="biz-hero-badge">
                <?php echo htmlspecialchars((string) biz_config('hero_badge', 'Ihr verlässlicher IT-Partner'), ENT_QUOTES, 'UTF-8'); ?>
            </div>

            <h1>
                <?php
                $headline = (string) biz_config(
                    'hero_headline',
                    'Wir bringen <span class="highlight">Ihr Unternehmen</span> erfolgreich in die Zukunft.'
                );
                echo biz_safe_headline($headline);
                ?>
            </h1>

            <p class="biz-hero-lead">
                <?php echo htmlspecialchars(
                    (string) biz_config('hero_text', 'Mit fundierter Expertise, modernsten Technologien und einem engagierten Team entwickeln wir maßgeschneiderte Lösungen für Ihre digitalen Herausforderungen.'),
                    ENT_QUOTES,
                    'UTF-8'
                ); ?>
            </p>

            <div class="biz-hero-actions">
                <a href="<?php echo htmlspecialchars(biz_href((string) biz_config('hero_cta_primary_url', '#leistungen')), ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn-biz btn-biz-primary btn-biz-lg">
                    <?php echo htmlspecialchars((string) biz_config('hero_cta_primary_label', 'Leistungen entdecken'), ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <a href="<?php echo htmlspecialchars(biz_href((string) biz_config('hero_cta_secondary_url', '#kontakt')), ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn-biz btn-biz-outline btn-biz-lg">
                    <?php echo htmlspecialchars((string) biz_config('hero_cta_secondary_label', 'Kontakt aufnehmen'), ENT_QUOTES, 'UTF-8'); ?>
                </a>
            </div>

        </div>
    </div>
</section>


<!-- ██ LEISTUNGEN ████████████████████████████████████████████████████████ -->
<section class="biz-section" id="leistungen">
    <div class="biz-container">

        <div class="biz-section-head">
            <span class="biz-eyebrow">Was wir bieten</span>
            <h2>Unsere Leistungen</h2>
            <p>Von der Strategie bis zur Umsetzung – wir begleiten Sie auf dem gesamten Weg Ihrer digitalen Transformation.</p>
        </div>

        <div class="biz-services-grid">

            <div class="biz-service-card">
                <div class="biz-service-icon" aria-hidden="true">💡</div>
                <h3>IT-Beratung</h3>
                <p>Strategische Beratung für Ihre digitale Transformation. Wir analysieren Ihre Prozesse und entwickeln zukunftssichere IT-Konzepte.</p>
            </div>

            <div class="biz-service-card">
                <div class="biz-service-icon" aria-hidden="true">⚙️</div>
                <h3>Softwareentwicklung</h3>
                <p>Maßgeschneiderte Softwarelösungen für Ihre spezifischen Anforderungen. Von Web-Apps bis hin zu komplexen Enterprise-Systemen.</p>
            </div>

            <div class="biz-service-card">
                <div class="biz-service-icon" aria-hidden="true">🛡️</div>
                <h3>IT-Security</h3>
                <p>Umfassende Sicherheitslösungen zum Schutz Ihrer Daten und Systeme. Schwachstellenanalyse, Penetrationstests und Security-Audits.</p>
            </div>

            <div class="biz-service-card">
                <div class="biz-service-icon" aria-hidden="true">☁️</div>
                <h3>Cloud Services</h3>
                <p>Nahtlose Migration in die Cloud und optimales Management Ihrer Cloud-Infrastruktur für maximale Skalierbarkeit und Effizienz.</p>
            </div>

            <div class="biz-service-card">
                <div class="biz-service-icon" aria-hidden="true">📊</div>
                <h3>Data & Analytics</h3>
                <p>Aus Ihren Daten werden wertvolle Erkenntnisse. Business Intelligence, Reporting-Dashboards und KI-gestützte Analysen.</p>
            </div>

            <div class="biz-service-card">
                <div class="biz-service-icon" aria-hidden="true">🎓</div>
                <h3>Schulungen</h3>
                <p>Praxisnahe Weiterbildungen für Ihr Team. Von Grundlagen bis zu spezialisierten Intensivkursen – flexibel und bedarfsgerecht.</p>
            </div>

        </div>
    </div>
</section>


<!-- ██ ÜBER UNS ██████████████████████████████████████████████████████████ -->
<section class="biz-section biz-section-alt" id="ueber-uns">
    <div class="biz-container">
        <div class="biz-about-grid">

            <!-- Visual -->
            <div class="biz-about-visual">
                <?php if ($aboutImg !== '') : ?>
                    <img src="<?php echo htmlspecialchars($aboutImg, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="Über uns" loading="lazy">
                <?php else : ?>
                    <span aria-hidden="true">🏢</span>
                <?php endif; ?>
            </div>

            <!-- Text -->
            <div class="biz-about-text">
                <span class="biz-eyebrow">Über uns</span>
                <h2><?php echo htmlspecialchars((string) biz_config('about_heading', 'Der Experte an Ihrer Seite'), ENT_QUOTES, 'UTF-8'); ?></h2>

                <p>Seit über einem Jahrzehnt unterstützen wir Unternehmen dabei, die Potenziale der Digitalisierung voll auszuschöpfen. Unser Team aus erfahrenen IT-Experten verbindet technisches Know-how mit tiefem Branchenverständnis.</p>

                <p>Wir glauben an langfristige Partnerschaften und nachhaltige Lösungen – nicht an schnelle Fixes. Ihre Ziele sind unsere Ziele.</p>

                <ul class="biz-check-list">
                    <li>Über 200 erfolgreich abgeschlossene Projekte</li>
                    <li>Zertifizierte Experten für alle gängigen Technologien</li>
                    <li>ISO 27001-konforme Sicherheitsstandards</li>
                    <li>Persönlicher Ansprechpartner für jedes Projekt</li>
                </ul>

                <a href="<?php echo htmlspecialchars(biz_href('#kontakt'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-primary">
                    Jetzt kennenlernen →
                </a>
            </div>

        </div>
    </div>
</section>


<!-- ██ ZAHLEN & FAKTEN ████████████████████████████████████████████████████ -->
<section class="biz-section biz-section-dark" id="zahlen">
    <div class="biz-container">

        <div class="biz-section-head">
            <span class="biz-eyebrow">Zahlen &amp; Fakten</span>
            <h2>Das spricht für uns</h2>
        </div>

        <div class="biz-stats-grid">
            <div>
                <div class="biz-stat-value">10+</div>
                <div class="biz-stat-label">Jahre Erfahrung</div>
            </div>
            <div>
                <div class="biz-stat-value">200+</div>
                <div class="biz-stat-label">Abgeschlossene Projekte</div>
            </div>
            <div>
                <div class="biz-stat-value">98&thinsp;%</div>
                <div class="biz-stat-label">Kundenzufriedenheit</div>
            </div>
            <div>
                <div class="biz-stat-value">50+</div>
                <div class="biz-stat-label">Zertifizierte Experten</div>
            </div>
        </div>

    </div>
</section>


<!-- ██ CTA ████████████████████████████████████████████████████████████████ -->
<section class="biz-cta-section" id="kontakt">
    <div class="biz-container">

        <span class="biz-eyebrow">Kontakt</span>
        <h2><?php echo htmlspecialchars((string) biz_config('cta_heading', 'Bereit für den nächsten Schritt?'), ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars((string) biz_config('cta_text', 'Kontaktieren Sie uns noch heute für ein unverbindliches Erstgespräch. Wir freuen uns auf Ihre Anfrage.'), ENT_QUOTES, 'UTF-8'); ?></p>

        <div class="biz-cta-actions">
            <a href="<?php echo htmlspecialchars(biz_href('mailto:info@example.com'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-white btn-biz-lg">
                ✉ E-Mail schreiben
            </a>
            <a href="<?php echo htmlspecialchars(biz_href('tel:+491234567890'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-outline-white btn-biz-lg">
                📞 Jetzt anrufen
            </a>
        </div>

    </div>
</section>
