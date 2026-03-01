<?php
/**
 * PTC Theme – Footer
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl   = ptc_site_url();
$siteTitle = ptc_site_title();
$tagline   = ptc_config('footer_tagline', 'Ihr Partner für Bildung, Karriere und Zukunft.');
?>
    </main><!-- /#main-content -->

    <?php \CMS\Hooks::doAction('before_footer'); ?>

    <footer class="ptc-footer" id="ptc-footer" role="contentinfo">
        <div class="ptc-container">
            <div class="ptc-footer-grid">

                <!-- Brand -->
                <div class="ptc-footer-brand">
                    <a href="<?php echo $siteUrl; ?>/" class="ptc-logo ptc-logo--footer" aria-label="<?php echo $siteTitle; ?>">
                        <svg width="28" height="28" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="18" cy="18" r="18" fill="#D4A017"/>
                            <path d="M10 12h5v3h-5zM10 17h5v3h-5zM10 22h5v3h-5zM17 12h9v3h-9zM17 17h7v3h-7zM17 22h5v3h-5z" fill="#002D5D"/>
                        </svg>
                        <span class="ptc-logo-text"><?php echo $siteTitle; ?></span>
                    </a>
                    <p><?php echo htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="ptc-social">
                        <a href="#" aria-label="Telefon">📞</a>
                        <a href="#" aria-label="E-Mail">✉️</a>
                        <a href="#" aria-label="LinkedIn">in</a>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="ptc-footer-col">
                    <h4>Navigation</h4>
                    <?php ptc_nav_menu('footer-nav'); ?>
                </div>

                <!-- Dienstleistungen -->
                <div class="ptc-footer-col">
                    <h4>Dienstleistungen</h4>
                    <ul>
                        <li><a href="<?php echo $siteUrl; ?>/#dienstleistungen">Personalvermittlung</a></li>
                        <li><a href="<?php echo $siteUrl; ?>/#dienstleistungen">Arbeitnehmerüberlassung</a></li>
                        <li><a href="<?php echo $siteUrl; ?>/#dienstleistungen">Akademie & Bildung</a></li>
                        <li><a href="<?php echo $siteUrl; ?>/#dienstleistungen">Logistiklehrwerkstatt</a></li>
                    </ul>
                </div>

                <!-- Kontakt -->
                <div class="ptc-footer-col">
                    <h4>Kontakt</h4>
                    <ul>
                        <li><a href="<?php echo $siteUrl; ?>/#kontakt">Kontaktformular</a></li>
                        <li><a href="<?php echo $siteUrl; ?>/impressum">Impressum</a></li>
                        <li><a href="<?php echo $siteUrl; ?>/datenschutz">Datenschutz</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="ptc-footer-bottom">
                <span>&copy; <?php echo gmdate('Y'); ?> <?php echo $siteTitle; ?>. Alle Rechte vorbehalten.</span>
                <nav class="ptc-footer-legal" aria-label="Rechtliche Links">
                    <?php ptc_nav_menu('footer-legal'); ?>
                </nav>
            </div>
        </div>
    </footer>

    <?php \CMS\Hooks::doAction('footer'); ?>

</div><!-- /.ptc-site -->

<?php \CMS\Hooks::doAction('body_end'); ?>

</body>
</html>
