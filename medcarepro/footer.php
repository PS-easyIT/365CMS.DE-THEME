<?php
/**
 * MedCare Pro Theme – Footer Template
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$mcTitle    = mc_site_title();
$mcTitleEsc = htmlspecialchars($mcTitle, ENT_QUOTES, 'UTF-8');

$mcFooterText   = (string) mc_get_setting('footer',        'footer_text',          'Die Gesundheitsplattform für Ärzte und Patienten.');
$mcImprintTitle = (string) mc_get_setting('dsgvo_medical', 'imprint_doctor_title', '');
$mcCopyrightTpl = (string) mc_get_setting('footer',        'copyright_text',       '&copy; {year} {site_title}. Alle Rechte vorbehalten.');
$mcDisclaimer   = (string) mc_get_setting('footer',        'footer_disclaimer',    '');

$mcCopyright = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), $mcTitleEsc],
    $mcCopyrightTpl
);

$mcDoctorsUrl  = htmlspecialchars(theme_route_url('doctors'),  ENT_QUOTES, 'UTF-8');
$mcBookingUrl  = htmlspecialchars(theme_route_url('booking'),  ENT_QUOTES, 'UTF-8');
$mcFieldsUrl   = htmlspecialchars(theme_route_url('fields'),   ENT_QUOTES, 'UTF-8');
$mcRegisterUrl = htmlspecialchars(theme_route_url('register'), ENT_QUOTES, 'UTF-8');
$mcLoginUrl    = htmlspecialchars(theme_route_url('login'),    ENT_QUOTES, 'UTF-8');
$mcPricingUrl  = htmlspecialchars(mc_href('/pricing'),         ENT_QUOTES, 'UTF-8');
$mcPrivacyUrl  = htmlspecialchars(theme_route_url('privacy'),  ENT_QUOTES, 'UTF-8');
$mcImprintUrl  = htmlspecialchars(theme_route_url('imprint'),  ENT_QUOTES, 'UTF-8');
$mcTermsUrl    = htmlspecialchars(theme_route_url('terms'),    ENT_QUOTES, 'UTF-8');
$mcCookiesUrl  = htmlspecialchars(theme_route_url('cookies'),  ENT_QUOTES, 'UTF-8');
?>
    </div><!-- #content -->
    <footer id="mc-footer" class="mc-footer" role="contentinfo">
        <div class="mc-container">
            <div class="mc-footer-widgets">
                <div class="mc-footer-col mc-footer-col--brand">
                    <h3 class="mc-footer-brand"><?php echo $mcTitleEsc; ?></h3>
                    <p class="mc-footer-text"><?php echo nl2br(htmlspecialchars($mcFooterText, ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php if ($mcImprintTitle !== '') : ?>
                        <p class="mc-footer-imprint"><?php echo htmlspecialchars($mcImprintTitle, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>
                <div class="mc-footer-col">
                    <h3>Für Patienten</h3>
                    <ul>
                        <li><a href="<?php echo $mcDoctorsUrl; ?>">Arzt suchen</a></li>
                        <li><a href="<?php echo $mcBookingUrl; ?>">Termin buchen</a></li>
                        <li><a href="<?php echo $mcFieldsUrl;  ?>">Fachgebiete</a></li>
                    </ul>
                </div>
                <div class="mc-footer-col">
                    <h3>Für Ärzte</h3>
                    <ul>
                        <li><a href="<?php echo $mcRegisterUrl; ?>">Profil anlegen</a></li>
                        <li><a href="<?php echo $mcPricingUrl;  ?>">Pakete &amp; Preise</a></li>
                        <li><a href="<?php echo $mcLoginUrl;    ?>">Anmelden</a></li>
                    </ul>
                </div>
                <div class="mc-footer-col">
                    <h3>Rechtliches</h3>
                    <ul>
                        <li><a href="<?php echo $mcPrivacyUrl; ?>">Datenschutz</a></li>
                        <li><a href="<?php echo $mcImprintUrl; ?>">Impressum</a></li>
                        <li><a href="<?php echo $mcTermsUrl;   ?>">AGB</a></li>
                        <li><a href="<?php echo $mcCookiesUrl; ?>">Cookie-Richtlinie</a></li>
                    </ul>
                </div>
            </div>
            <?php if (trim($mcDisclaimer) !== '') : ?>
            <div class="mc-footer-disclaimer" role="note">
                <p><?php echo nl2br(htmlspecialchars($mcDisclaimer, ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <div class="mc-footer-bottom">
            <div class="mc-container">
                <span class="mc-footer-copyright"><?php echo $mcCopyright; ?></span>
                <nav class="mc-footer-legal" aria-label="Rechtliche Links">
                    <a href="<?php echo $mcPrivacyUrl; ?>">Datenschutz</a>
                    <a href="<?php echo $mcImprintUrl; ?>">Impressum</a>
                    <a href="<?php echo $mcTermsUrl;   ?>">AGB</a>
                </nav>
            </div>
        </div>
    </footer>
</div><!-- #page -->
<?php \CMS\Hooks::doAction('before_footer'); ?>
</body>
</html>
