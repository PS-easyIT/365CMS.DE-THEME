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

// ── Customizer-Werte ─────────────────────────────────────────────────────────
$tagline   = (string) ptc_customizer_get('footer', 'footer_tagline', 'PTC GmbH – Ihr Partner für Bildung, Karriere und Zukunft.');
$address   = (string) ptc_customizer_get('footer', 'footer_address', '');
$phone     = (string) ptc_customizer_get('footer', 'footer_phone', '');
$email     = (string) ptc_customizer_get('footer', 'footer_email', '');

// Copyright mit Platzhaltern
$copyrightTpl  = (string) ptc_customizer_get('footer', 'copyright_text', '© {year} {site_title}. Alle Rechte vorbehalten.');
$copyrightText = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), $siteTitle],
    $copyrightTpl
);

// Social-Links
$socialLinks = [
    'facebook'  => ['url' => (string) ptc_customizer_get('footer', 'social_facebook', ''),  'label' => 'Facebook',  'icon' => 'f'],
    'instagram' => ['url' => (string) ptc_customizer_get('footer', 'social_instagram', ''), 'label' => 'Instagram', 'icon' => '📷'],
    'linkedin'  => ['url' => (string) ptc_customizer_get('footer', 'social_linkedin', ''),  'label' => 'LinkedIn',  'icon' => 'in'],
    'xing'      => ['url' => (string) ptc_customizer_get('footer', 'social_xing', ''),      'label' => 'Xing',      'icon' => 'X'],
    'youtube'   => ['url' => (string) ptc_customizer_get('footer', 'social_youtube', ''),    'label' => 'YouTube',   'icon' => '▶'],
];
$activeSocials = array_filter($socialLinks, fn($s) => $s['url'] !== '');
?>
    </main><!-- /#main-content -->

    <?php \CMS\Hooks::doAction('before_footer'); ?>

    <footer class="ptc-footer" id="ptc-footer" role="contentinfo">
        <div class="ptc-container">
            <div class="ptc-footer-grid">

                <!-- Brand -->
                <div class="ptc-footer-brand">
                    <?php
                    $footerLogo = ptc_customizer_get('header', 'logo_url', '');
                    ?>
                    <a href="<?php echo $siteUrl; ?>/" class="ptc-logo ptc-logo--footer" aria-label="<?php echo $siteTitle; ?>">
                        <?php if (!empty($footerLogo)): ?>
                            <img src="<?php echo htmlspecialchars($footerLogo, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo $siteTitle; ?>" class="ptc-logo-img" style="max-height:40px;" loading="lazy">
                        <?php else: ?>
                            <svg width="28" height="28" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <circle cx="18" cy="18" r="18" fill="var(--ptc-gold, #D4A017)"/>
                                <path d="M10 12h5v3h-5zM10 17h5v3h-5zM10 22h5v3h-5zM17 12h9v3h-9zM17 17h7v3h-7zM17 22h5v3h-5z" fill="var(--ptc-navy, #002D5D)"/>
                            </svg>
                            <span class="ptc-logo-text"><?php echo $siteTitle; ?></span>
                        <?php endif; ?>
                    </a>
                    <?php if ($tagline !== ''): ?>
                        <p><?php echo htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($activeSocials)): ?>
                        <div class="ptc-social">
                            <?php foreach ($activeSocials as $social): ?>
                                <a href="<?php echo htmlspecialchars($social['url'], ENT_QUOTES, 'UTF-8'); ?>"
                                   aria-label="<?php echo htmlspecialchars($social['label'], ENT_QUOTES, 'UTF-8'); ?>"
                                   target="_blank" rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($social['icon'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
                    <ul class="ptc-footer-contact">
                        <?php if ($address !== ''): ?>
                            <li class="ptc-footer-address"><?php echo nl2br(htmlspecialchars($address, ENT_QUOTES, 'UTF-8')); ?></li>
                        <?php endif; ?>
                        <?php if ($phone !== ''): ?>
                            <li>📞 <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^+0-9]/', '', $phone), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>
                            </a></li>
                        <?php endif; ?>
                        <?php if ($email !== ''): ?>
                            <li>✉️ <a href="mailto:<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                            </a></li>
                        <?php endif; ?>
                    </ul>
                </div>

            </div>

            <!-- Bottom Bar -->
            <div class="ptc-footer-bottom">
                <span><?php echo $copyrightText; ?></span>
                <nav class="ptc-footer-legal" aria-label="Rechtliche Links">
                    <?php ptc_nav_menu('footer-legal'); ?>
                </nav>
            </div>
        </div>
    </footer>

    <?php \CMS\Hooks::doAction('footer'); ?>

    <?php
    // ── Network Bar ──────────────────────────────────────────────────────────
    $showNetworkBar = filter_var(ptc_customizer_get('footer', 'show_network_bar', true), FILTER_VALIDATE_BOOLEAN);
    if ($showNetworkBar):
        $nbName = trim((string) ptc_customizer_get('footer', 'network_bar_name', 'Andreas Hepp'));
        $nbLinks = [];
        for ($i = 1; $i <= 3; $i++) {
            $lbl = trim((string) ptc_customizer_get('footer', "network_bar_link{$i}_label", ''));
            $url = trim((string) ptc_customizer_get('footer', "network_bar_link{$i}_url", ''));
            if ($lbl !== '' && $url !== '') {
                $nbLinks[] = ['label' => $lbl, 'url' => $url];
            }
        }
    ?>
    <div class="ptc-network-bar">
        <div class="ptc-container">
            <div class="ptc-network-inner">
                <?php if ($nbName !== ''): ?>
                    <span class="ptc-network-label"><?php echo htmlspecialchars($nbName, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
                <?php if (!empty($nbLinks)): ?>
                    <div class="ptc-network-links">
                        <?php foreach ($nbLinks as $link): ?>
                            <a href="<?php echo htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>"
                               target="_blank" rel="noopener noreferrer">
                                <?php echo htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.ptc-site -->

<?php \CMS\Hooks::doAction('body_end'); ?>

</body>
</html>
