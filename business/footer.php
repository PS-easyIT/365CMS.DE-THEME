<?php
declare(strict_types=1);

/**
 * Business Theme – Footer
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl   = biz_site_url();
$siteTitle = biz_site_title();
$tagline   = (string) biz_config('footer_tagline', 'Ihr Partner für digitale Innovation.');

try {
    $copyRaw = (string) \CMS\Services\ThemeCustomizer::instance()->get(
        'footer',
        'footer_copyright',
        '© {year} {site_title}. Alle Rechte vorbehalten.'
    );
} catch (\Throwable) {
    $copyRaw = '© {year} {site_title}. Alle Rechte vorbehalten.';
}
$copyText = htmlspecialchars(
    strtr($copyRaw, [
        '{year}'       => gmdate('Y'),
        '{site_title}' => \CMS\ThemeManager::instance()->getSiteTitle(),
    ]),
    ENT_QUOTES,
    'UTF-8'
);
?>
    </main><!-- /#main-content -->

    <footer class="biz-footer" id="biz-footer" role="contentinfo">
        <div class="biz-container">
            <div class="biz-footer-grid">

                <!-- Brand -->
                <div class="biz-footer-brand">
                    <a href="<?php echo htmlspecialchars(biz_href('/'), ENT_QUOTES, 'UTF-8'); ?>" class="biz-logo biz-footer-brand-link biz-focus-shadow">
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                             aria-hidden="true" focusable="false" class="biz-footer-brand-icon">
                            <rect width="32" height="32" rx="6" fill="#c08a2e"/>
                            <path d="M8 10h4v12H8V10zm6 0h4v12h-4V10zm6 4h4v8h-4v-8z" fill="#0c1320"/>
                        </svg>
                        <span class="biz-logo-text"><?php echo $siteTitle; ?></span>
                    </a>
                    <p><?php echo htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="biz-social">
                        <a href="#" aria-label="LinkedIn">in</a>
                        <a href="#" aria-label="Twitter/X">𝕏</a>
                        <a href="#" aria-label="E-Mail">✉</a>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="biz-footer-col">
                    <h4>Navigation</h4>
                    <?php biz_nav_menu('footer-nav'); ?>
                </div>

                <!-- Services -->
                <div class="biz-footer-col">
                    <h4>Leistungen</h4>
                    <ul>
                        <li><a href="<?php echo htmlspecialchars(biz_href('#leistungen'), ENT_QUOTES, 'UTF-8'); ?>">Beratung</a></li>
                        <li><a href="<?php echo htmlspecialchars(biz_href('#leistungen'), ENT_QUOTES, 'UTF-8'); ?>">Entwicklung</a></li>
                        <li><a href="<?php echo htmlspecialchars(biz_href('#leistungen'), ENT_QUOTES, 'UTF-8'); ?>">Support</a></li>
                        <li><a href="<?php echo htmlspecialchars(biz_href('#leistungen'), ENT_QUOTES, 'UTF-8'); ?>">Schulungen</a></li>
                    </ul>
                </div>

                <!-- Kontakt -->
                <div class="biz-footer-col">
                    <h4>Kontakt</h4>
                    <ul>
                        <li><a href="<?php echo htmlspecialchars(biz_href('#kontakt'), ENT_QUOTES, 'UTF-8'); ?>">Kontaktformular</a></li>
                        <li><a href="<?php echo htmlspecialchars(biz_href('/impressum'), ENT_QUOTES, 'UTF-8'); ?>">Impressum</a></li>
                        <li><a href="<?php echo htmlspecialchars(biz_href('/datenschutz'), ENT_QUOTES, 'UTF-8'); ?>">Datenschutz</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="biz-footer-bottom">
                <span><?php echo $copyText; ?></span>
                <nav class="biz-footer-legal" aria-label="Rechtliche Links">
                    <?php biz_nav_menu('footer-legal'); ?>
                </nav>
            </div>
        </div>
    </footer>

</div><!-- /.biz-site -->

<?php \CMS\Hooks::doAction('before_footer'); ?>

</body>
</html>
