<?php
/**
 * PersonalFlow Theme – Footer Template
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$pfTitle    = pf_site_title();
$pfTitleEsc = htmlspecialchars($pfTitle, ENT_QUOTES, 'UTF-8');

$pfFooterText        = (string) pf_get_setting('footer', 'footer_text', 'Die HR-Plattform für Talent-Matching und strukturierte Recruiting-Pipelines.');
$pfShowPipelineLinks = filter_var(pf_get_setting('footer', 'show_pipeline_links', true), FILTER_VALIDATE_BOOLEAN);
$pfShowEmployerLinks = filter_var(pf_get_setting('footer', 'show_employer_links', true), FILTER_VALIDATE_BOOLEAN);
$pfCopyrightTpl      = (string) pf_get_setting('footer', 'copyright_text', '&copy; {year} {site_title}. Alle Rechte vorbehalten.');

$pfCopyright = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), $pfTitleEsc],
    $pfCopyrightTpl
);
$pfHomeUrl       = htmlspecialchars(theme_route_url('home'),       ENT_QUOTES, 'UTF-8');
$pfJobsUrl       = htmlspecialchars(theme_route_url('jobs'),       ENT_QUOTES, 'UTF-8');
$pfCandidatesUrl = htmlspecialchars(theme_route_url('candidates'), ENT_QUOTES, 'UTF-8');
$pfEmployersUrl  = htmlspecialchars(theme_route_url('employers'),  ENT_QUOTES, 'UTF-8');
$pfPipelineUrl   = htmlspecialchars(theme_route_url('pipeline'),   ENT_QUOTES, 'UTF-8');
$pfPricingUrl    = htmlspecialchars(theme_route_url('pricing'),    ENT_QUOTES, 'UTF-8');
$pfRegisterUrl   = htmlspecialchars(theme_route_url('register'),   ENT_QUOTES, 'UTF-8');
?>
    </div><!-- #content .pf-site-content -->

    <footer id="pfFooter" class="pf-footer" role="contentinfo" aria-label="Fußbereich">
        <div class="pf-footer-content">
            <div class="pf-footer-container">
                <div class="pf-footer-widgets">

                    <div class="pf-footer-widget">
                        <h3><?php echo $pfTitleEsc; ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($pfFooterText, ENT_QUOTES, 'UTF-8')); ?></p>
                    </div>

                    <?php if ($pfShowPipelineLinks) : ?>
                        <div class="pf-footer-widget">
                            <h3>Für Kandidaten</h3>
                            <ul>
                                <li><a href="<?php echo $pfJobsUrl; ?>">Offene Stellen</a></li>
                                <li><a href="<?php echo $pfRegisterUrl; ?>?type=candidate">Profil anlegen</a></li>
                                <li><a href="<?php echo $pfPipelineUrl; ?>">Bewerbungs-Pipeline</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($pfShowEmployerLinks) : ?>
                        <div class="pf-footer-widget">
                            <h3>Für Arbeitgeber</h3>
                            <ul>
                                <li><a href="<?php echo $pfCandidatesUrl; ?>">Talente entdecken</a></li>
                                <li><a href="<?php echo $pfEmployersUrl; ?>">Arbeitgeber-Login</a></li>
                                <li><a href="<?php echo $pfPricingUrl; ?>">Preise &amp; Pakete</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="pf-footer-widget">
                        <h3>Service</h3>
                        <?php theme_nav_menu('footer-nav'); ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="pf-footer-bottom">
            <div class="pf-footer-container">
                <div class="pf-footer-bottom-row">
                    <div><?php echo $pfCopyright; ?></div>
                    <nav class="pf-footer-legal-nav" aria-label="Rechtliche Links">
                        <?php theme_nav_menu('footer-legal'); ?>
                    </nav>
                </div>
            </div>
        </div>
    </footer><!-- #pfFooter -->

</div><!-- #page -->

<?php \CMS\Hooks::doAction('before_footer'); ?>
</body>
</html>
