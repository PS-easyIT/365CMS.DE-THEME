<?php
/**
 * Footer Template
 *
 * @package IT_Expert_Network_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$themeManager = \CMS\ThemeManager::instance();
$customizer   = \CMS\Services\ThemeCustomizer::instance();
$siteTitle    = $themeManager->getSiteTitle();
$isLoggedIn   = theme_is_logged_in();
$siteUrl      = SITE_URL;

// Customizer Settings
$footerText        = $customizer->get('footer', 'footer_text', 'Die IT-Networking-Plattform für Experten, Unternehmen und Events. Vernetze dich mit der IT-Community.');
$showNetworkWidgets = $customizer->get('footer', 'show_network_widgets', true);
$copyrightTemplate = $customizer->get('footer', 'copyright_text', '&copy; {year} {site_title}. Alle Rechte vorbehalten.');

// Social Media Links
$socialLinks = [
    'twitter'   => ['url' => (string) $customizer->get('footer', 'social_twitter', ''),   'label' => 'Twitter / X',  'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768M13.232 10.232L20 4"/></svg>'],
    'instagram' => ['url' => (string) $customizer->get('footer', 'social_instagram', ''), 'label' => 'Instagram',    'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>'],
    'linkedin'  => ['url' => (string) $customizer->get('footer', 'social_linkedin', ''),  'label' => 'LinkedIn',     'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>'],
    'youtube'   => ['url' => (string) $customizer->get('footer', 'social_youtube', ''),   'label' => 'YouTube',      'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96c1.71.46 8.59.46 8.59.46s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>'],
];
$activeSocials = array_filter($socialLinks, fn($s) => !empty($s['url']));

// Placeholders for Copyright
$copyrightText = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8')],
    $copyrightTemplate
);
?>
    </div><!-- #content .site-content -->

    <footer id="colophon" class="site-footer" role="contentinfo" aria-label="Fußbereich">

        <!-- Footer Content Band -->
        <div class="footer-content-band">
            <div class="footer-container">
                <?php if ($showNetworkWidgets) : ?>
                <div class="footer-widgets">
                    <!-- Über uns -->
                    <div class="footer-widget">
                        <h3 class="footer-widget-title"><?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($footerText, ENT_QUOTES, 'UTF-8')); ?></p>
                    </div>
                    <!-- Verzeichnisse -->
                    <div class="footer-widget">
                        <h3 class="footer-widget-title">Verzeichnisse</h3>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/experts">👤 Experten</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/companies">🏢 Firmen</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/events">📅 Events</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/speakers">🎤 Speaker</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/jobs">💼 Stellenmarkt</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/feeds">📰 Feed-Aggregator</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/booking">📆 Buchungsportal</a></li>
                        </ul>
                    </div>
                    <!-- Ressourcen -->
                    <div class="footer-widget">
                        <h3 class="footer-widget-title">Ressourcen</h3>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/whitepapers">Whitepapers</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/webinare">Webinare</a></li>
                            <?php if ($isLoggedIn) : ?>
                                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member">Mein Dashboard</a></li>
                            <?php else : ?>
                                <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/login">Anmelden</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <!-- Rechtliches -->
                    <div class="footer-widget">
                        <h3 class="footer-widget-title">Rechtliches</h3>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/kontakt">Kontakt</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/impressum">Impressum</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/datenschutz">Datenschutz</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/agb">AGB</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/sitemap">Sitemap</a></li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($activeSocials)): ?>
                <div class="footer-social">
                    <div class="footer-social-icons">
                        <?php foreach ($activeSocials as $social): ?>
                            <a href="<?php echo htmlspecialchars($social['url'], ENT_QUOTES, 'UTF-8'); ?>"
                               aria-label="<?php echo htmlspecialchars($social['label'], ENT_QUOTES, 'UTF-8'); ?>"
                               target="_blank" rel="noopener noreferrer">
                                <?php echo $social['svg']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="footer-bottom">
                    <p><?php echo $copyrightText; ?></p>

                    <nav class="footer-legal-nav" aria-label="Rechtliche Links">
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/impressum">Impressum</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/datenschutz">Datenschutz</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/agb">AGB</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/kontakt">Kontakt</a>
                    </nav>

                    <?php if ($isLoggedIn) : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member" class="footer-dashboard-btn">
                            Zum Dashboard
                        </a>
                    <?php else : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/login" class="footer-login-btn">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>

</div><!-- #page .site -->

<?php \CMS\Hooks::doAction('before_footer'); ?>

</body>
</html>
