<?php
/**
 * LogiLink Theme – Footer Template
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$llTitle    = ll_site_title();
$llTitleEsc = htmlspecialchars($llTitle, ENT_QUOTES, 'UTF-8');
$llSafe     = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$llFooterText = (string) ll_get_setting(
    'footer',
    'footer_text',
    'Schnelle, zuverlässige Logistiklösungen für nationale und internationale Sendungen.'
);
$llCopyTpl    = (string) ll_get_setting(
    'footer',
    'copyright_text',
    '&copy; {year} {site_title}. Alle Rechte vorbehalten.'
);
$llCopyright = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), $llTitleEsc],
    $llCopyTpl
);

$llTrackUrl    = $llSafe(theme_route_url('tracking'));
$llPartnersUrl = $llSafe(theme_route_url('partners'));
$llRoutesUrl   = $llSafe(theme_route_url('routes'));
$llRegisterUrl = $llSafe(theme_route_url('register'));
$llLoginUrl    = $llSafe(theme_route_url('login'));
$llMemberUrl   = $llSafe(theme_route_url('member'));
$llHomeUrl     = $llSafe(theme_route_url('home'));
?>
    </div><!-- #content -->

    <footer id="ll-footer" class="ll-footer" role="contentinfo">
        <div class="ll-container">
            <div class="ll-footer-widgets">
                <div class="ll-footer-brand">
                    <a href="<?php echo $llHomeUrl; ?>" class="ll-brand-line">
                        <span aria-hidden="true">🚛</span>
                        <?php echo $llTitleEsc; ?>
                    </a>
                    <p><?php echo nl2br($llSafe($llFooterText)); ?></p>
                </div>
                <div>
                    <h3>Service</h3>
                    <ul>
                        <li><a href="<?php echo $llTrackUrl; ?>">Sendung verfolgen</a></li>
                        <li><a href="<?php echo $llPartnersUrl; ?>">Spediteure</a></li>
                        <li><a href="<?php echo $llRoutesUrl; ?>">Routen</a></li>
                    </ul>
                </div>
                <div>
                    <h3>Konto</h3>
                    <ul>
                        <li><a href="<?php echo $llRegisterUrl; ?>">Registrieren</a></li>
                        <li><a href="<?php echo $llLoginUrl; ?>">Anmelden</a></li>
                        <li><a href="<?php echo $llMemberUrl; ?>">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h3>Navigation</h3>
                    <?php theme_nav_menu('footer-nav'); ?>
                </div>
            </div>
        </div>

        <div class="ll-footer-bottom">
            <div class="ll-container ll-footer-bottom-inner">
                <div><?php echo $llCopyright; ?></div>
                <div class="ll-footer-legal">
                    <?php theme_nav_menu('footer-legal'); ?>
                </div>
            </div>
        </div>
    </footer>

</div><!-- #page -->
<?php \CMS\Hooks::doAction('before_footer'); ?>
</body>
</html>
