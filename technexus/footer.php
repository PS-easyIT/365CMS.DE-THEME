<?php
/**
 * TechNexus Theme – Footer Template
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$tnTitle    = tn_site_title();
$tnTitleEsc = tn_html_attr($tnTitle);
$tnIsLogged = theme_is_logged_in();

$footerText = (string) tn_get_setting(
    'footer',
    'footer_text',
    'Die führende Plattform für IT-Experten, Tech-Teams und Softwarehäuser.'
);
$showTechLinks = filter_var(
    tn_get_setting('footer', 'show_tech_links', true),
    FILTER_VALIDATE_BOOLEAN
);
$copyrightTemplate = (string) tn_get_setting(
    'footer',
    'copyright_text',
    '&copy; {year} {site_title}. Alle Rechte vorbehalten.'
);

$copyrightText = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), $tnTitleEsc],
    $copyrightTemplate
);
?>
    </div><!-- #content .site-content -->

    <footer id="colophon" class="site-footer" role="contentinfo" aria-label="Fußbereich">

        <div class="footer-content-band">
            <div class="footer-container">
                <div class="footer-widgets">

                    <div class="footer-widget">
                        <h3 class="footer-widget-title"><?php echo $tnTitleEsc; ?></h3>
                        <p><?php echo nl2br(tn_html_attr($footerText)); ?></p>
                    </div>

                    <?php if ($showTechLinks) : ?>
                    <div class="footer-widget">
                        <h3 class="footer-widget-title">Netzwerk</h3>
                        <ul>
                            <li><a href="<?php echo tn_html_attr(theme_route_url('experts')); ?>">IT-Experten</a></li>
                            <li><a href="<?php echo tn_html_attr(theme_route_url('companies')); ?>">Tech-Unternehmen</a></li>
                            <li><a href="<?php echo tn_html_attr(theme_route_url('events')); ?>">Tech Events</a></li>
                            <li><a href="<?php echo tn_html_attr(theme_route_url('jobs')); ?>">IT Jobs</a></li>
                        </ul>
                    </div>

                    <div class="footer-widget">
                        <h3 class="footer-widget-title">Mitglieder</h3>
                        <ul>
                            <?php if ($tnIsLogged) : ?>
                                <li><a href="<?php echo tn_html_attr(theme_route_url('member')); ?>">Mein Dashboard</a></li>
                                <li><a href="<?php echo tn_html_attr(theme_route_url('member/profile')); ?>">Mein Profil</a></li>
                                <li><a href="<?php echo tn_html_attr(theme_route_url('logout')); ?>">Abmelden</a></li>
                            <?php else : ?>
                                <li><a href="<?php echo tn_html_attr(theme_route_url('login')); ?>">Anmelden</a></li>
                                <li><a href="<?php echo tn_html_attr(theme_route_url('register')); ?>">Registrieren</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php else : ?>
                    <div class="footer-widget">
                        <?php theme_nav_menu('footer-nav'); ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-container footer-bottom-inner">
                <p class="footer-copyright"><?php echo $copyrightText; ?></p>
                <?php
                try {
                    $legalMenu = \CMS\ThemeManager::instance()->getMenu('footer-legal');
                } catch (\Throwable) {
                    $legalMenu = [];
                }
                if (!empty($legalMenu)) : ?>
                    <nav class="footer-legal-nav" aria-label="Rechtliche Links">
                        <?php theme_nav_menu('footer-legal'); ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>

    </footer>

</div><!-- #page -->

<?php \CMS\Hooks::doAction('before_footer'); ?>
</body>
</html>
