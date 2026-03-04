<?php
/**
 * Footer Template – CMS Phinit Theme
 * 2-spaltig + schmale Copyright-Leiste + Consent-Banner + Back-to-Top
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl   = SITE_URL;
$siteTitle = \CMS\ThemeManager::instance()->getSiteTitle();
$year      = date('Y');

// Customizer
try {
    $c = \CMS\Services\ThemeCustomizer::instance();

    // Brand
    $_brandName     = $c->get('footer', 'footer_brand_name', 'PHINIT.DE');
    $_footerDesc    = $c->get('footer', 'footer_tagline', 'IT-Profi-Blog für Microsoft 365, PowerShell, Linux und moderne IT-Administration. Praxisnah, fundiert, auf Deutsch.');

    // Spaltentitel
    $_col2Title     = $c->get('footer', 'footer_col2_title', 'Themen');
    $_col3Title     = $c->get('footer', 'footer_col3_title', 'Seiten');
    $_col4Title     = $c->get('footer', 'footer_col4_title', 'Rechtliches');

    // Copyright
    $_copyrightRaw  = $c->get('footer', 'copyright_text', '© {year} {site_title} – Alle Rechte vorbehalten');
    $_copyright     = str_replace(['{year}', '{site_title}'], [$year, $siteTitle], (string)$_copyrightRaw);

    // Toggles
    $_showConsent    = filter_var($c->get('footer', 'show_consent_banner', true), FILTER_VALIDATE_BOOLEAN);
    $_consentText    = $c->get('footer', 'consent_text', 'Diese Website verwendet Cookies für Analyse-Zwecke.');
    $_consentPrivUrl = $c->get('footer', 'consent_privacy_url', '/cookie-policy');
    $_showBackToTop  = filter_var($c->get('layout', 'enable_back_to_top', true), FILTER_VALIDATE_BOOLEAN);
    $_showNetworkBar = filter_var($c->get('footer', 'show_network_bar', false), FILTER_VALIDATE_BOOLEAN);
    $_showSocial     = filter_var($c->get('footer', 'show_footer_social', true), FILTER_VALIDATE_BOOLEAN);

    // Social URLs
    $_liUrl   = $c->get('social', 'social_linkedin', '');
    $_ghUrl   = $c->get('social', 'social_github', '');
    $_twUrl   = $c->get('social', 'social_twitter', '');
    $_maUrl   = $c->get('social', 'social_mastodon', '');
    $_ytUrl   = $c->get('social', 'social_youtube', '');
    $_xiUrl   = $c->get('social', 'social_xing', '');
    $_rssUrl  = $c->get('social', 'social_rss', $siteUrl . '/feed');

    // Social Labels
    $_liLabel  = $c->get('social', 'social_label_linkedin', 'LinkedIn');
    $_ghLabel  = $c->get('social', 'social_label_github', 'GitHub');
    $_rssLabel = $c->get('social', 'social_label_rss', 'RSS Feed');

    // Network-Bar Links (1–5)
    $_networkLinks = [];
    for ($i = 1; $i <= 5; $i++) {
        $label = $c->get('footer', 'network_bar_link' . $i . '_label', '');
        $url   = $c->get('footer', 'network_bar_link' . $i . '_url', '');
        if (!empty($label) && !empty($url)) {
            $_networkLinks[] = ['label' => $label, 'url' => $url];
        }
    }
} catch (\Throwable $e) {
    $_brandName     = 'PHINIT.DE';
    $_footerDesc    = 'IT-Profi-Blog für Microsoft 365, PowerShell & Linux.';
    $_col2Title     = 'Themen';
    $_col3Title     = 'Seiten';
    $_col4Title     = 'Rechtliches';
    $_copyright     = '© ' . $year . ' ' . $siteTitle . ' – Alle Rechte vorbehalten';
    $_showConsent   = true;
    $_consentText   = 'Diese Website verwendet Cookies für Analyse-Zwecke.';
    $_consentPrivUrl = '/cookie-policy';
    $_showBackToTop = true;
    $_showNetworkBar = false;
    $_showSocial    = true;
    $_liUrl = ''; $_ghUrl = ''; $_twUrl = ''; $_maUrl = ''; $_ytUrl = ''; $_xiUrl = '';
    $_rssUrl = $siteUrl . '/feed';
    $_liLabel = 'LinkedIn'; $_ghLabel = 'GitHub'; $_rssLabel = 'RSS Feed';
    $_networkLinks = [];
}
// CMS-Einstellung hat Vorrang: wenn global deaktiviert, Banner unterdrücken
try {
    $_db   = \CMS\Database::instance();
    $_stmt = $_db->prepare('SELECT option_value FROM ' . $_db->prefix() . 'settings WHERE option_name = ?');
    $_stmt->execute(['cookie_consent_enabled']);
    $_row  = $_stmt->fetch(\PDO::FETCH_ASSOC);
    if ($_row !== false && ($_row['option_value'] ?? '1') !== '1') {
        $_showConsent = false;
    }
} catch (\Throwable $_e) {}

// Footer-Menüs aus Menü-Editor laden
$footerTopicsMenu = [];
$footerPagesMenu  = [];
$footerLegalMenu  = [];
try {
    $footerTopicsMenu = \CMS\ThemeManager::instance()->getMenu('footer-topics');
} catch (\Throwable $e) {}
try {
    $footerPagesMenu = \CMS\ThemeManager::instance()->getMenu('footer-pages');
} catch (\Throwable $e) {}
try {
    $footerLegalMenu = \CMS\ThemeManager::instance()->getMenu('footer');
} catch (\Throwable $e) {}
?>

</main><!-- /#main-content -->
</div><!-- /.page-wrap -->

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<footer class="site-footer" role="contentinfo">

    <!-- Haupt-Footer: 3-spaltig (Brand + 2 Nav-Spalten) -->
    <div class="footer-main-wrap">
        <div class="container">
            <div class="footer-main">

                <!-- Marken-Spalte -->
                <div class="footer-brand">
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="logo">
                        <?php echo htmlspecialchars($_brandName, ENT_QUOTES); ?>
                    </a>
                    <p><?php echo htmlspecialchars($_footerDesc, ENT_QUOTES); ?></p>
                    <?php if ($_showSocial): ?>
                    <div class="social-icons">
                        <?php if (!empty($_liUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_liUrl, ENT_QUOTES); ?>" class="li" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($_liLabel, ENT_QUOTES); ?>"><?php echo htmlspecialchars($_liLabel, ENT_QUOTES); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($_ghUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_ghUrl, ENT_QUOTES); ?>" class="gh" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($_ghLabel, ENT_QUOTES); ?>"><?php echo htmlspecialchars($_ghLabel, ENT_QUOTES); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($_twUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_twUrl, ENT_QUOTES); ?>" class="tw" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">𝕏</a>
                        <?php endif; ?>
                        <?php if (!empty($_maUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_maUrl, ENT_QUOTES); ?>" class="ma" target="_blank" rel="noopener noreferrer me" aria-label="Mastodon">🦣</a>
                        <?php endif; ?>
                        <?php if (!empty($_ytUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_ytUrl, ENT_QUOTES); ?>" class="yt" target="_blank" rel="noopener noreferrer" aria-label="YouTube">▶</a>
                        <?php endif; ?>
                        <?php if (!empty($_xiUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_xiUrl, ENT_QUOTES); ?>" class="xi" target="_blank" rel="noopener noreferrer" aria-label="XING">X</a>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($_rssUrl, ENT_QUOTES); ?>" class="rss" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($_rssLabel, ENT_QUOTES); ?>">⊞</a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Navigation 1 (aus Menü-Editor: footer-topics) -->
                <div class="footer-nav">
                    <h4><?php echo htmlspecialchars($_col2Title, ENT_QUOTES); ?></h4>
                    <ul>
                        <?php if (!empty($footerTopicsMenu)): ?>
                            <?php foreach ($footerTopicsMenu as $_fItem): ?>
                            <li><a href="<?php echo htmlspecialchars($_fItem['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($_fItem['label'] ?? '', ENT_QUOTES); ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/linux">Linux &amp; BASH</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell">PowerShell</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/microsoft-365">Microsoft 365</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/intune">Intune &amp; MDM</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutz">Datenschutz &amp; DSGVO</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/news">IT-News</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Navigation 2 (aus Menü-Editor: footer-pages) -->
                <div class="footer-nav">
                    <h4><?php echo htmlspecialchars($_col3Title, ENT_QUOTES); ?></h4>
                    <ul>
                        <?php if (!empty($footerPagesMenu)): ?>
                            <?php foreach ($footerPagesMenu as $_fItem): ?>
                            <li><a href="<?php echo htmlspecialchars($_fItem['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($_fItem['label'] ?? '', ENT_QUOTES); ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/ueber-uns">Über mich</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kontakt">Kontakt</a></li>
                            <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/feed" target="_blank" rel="noopener">RSS-Feed</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Navigation 3 (aus Menü-Editor: footer / Rechtliches) -->
                <?php if (!empty($footerLegalMenu)): ?>
                <div class="footer-nav">
                    <h4><?php echo htmlspecialchars($_col4Title, ENT_QUOTES); ?></h4>
                    <ul>
                        <?php foreach ($footerLegalMenu as $_fItem): ?>
                        <li><a href="<?php echo htmlspecialchars($_fItem['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($_fItem['label'] ?? '', ENT_QUOTES); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div><!-- /.footer-main -->
        </div>
    </div><!-- /.footer-main-wrap -->

    <?php if ($_showNetworkBar && !empty($_networkLinks)): ?>
    <!-- Network Bar (volle Breite) -->
    <div class="network-bar">
        <div class="container">
            <?php foreach ($_networkLinks as $_nl): ?>
            <a href="<?php echo htmlspecialchars($_nl['url'], ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($_nl['label']); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Copyright-Leiste (volle Breite) -->
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <span><?php echo htmlspecialchars($_copyright, ENT_QUOTES); ?></span>
            <div class="footer-bottom-links">
                <?php if (!empty($footerLegalMenu)): ?>
                    <?php foreach ($footerLegalMenu as $_lItem): ?>
                    <a href="<?php echo htmlspecialchars($_lItem['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($_lItem['label'] ?? '', ENT_QUOTES); ?></a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/impressum">Impressum</a>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutzerklaerung">Datenschutz</a>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/disclaimer">Disclaimer</a>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/cookie-policy">Cookie-Policy</a>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/privacy-statement">Privacy Statement</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</footer>
<!-- ═══ FOOTER ENDE ═══════════════════════════════════════════════════════ -->

<!-- Back to Top -->
<?php if ($_showBackToTop): ?>
<button id="back-to-top" style="display:none;position:fixed;bottom:24px;right:24px;width:40px;height:40px;border-radius:50%;background:var(--primary-color);color:#fff;border:none;cursor:pointer;font-size:1.1rem;z-index:900;box-shadow:0 4px 14px rgba(0,0,0,.25);align-items:center;justify-content:center;transition:all .25s;" aria-label="Zum Seitenanfang">↑</button>
<?php endif; ?>

<!-- Einwilligungsbanner (DSGVO) -->
<?php if ($_showConsent): ?>
<div id="consent-banner" role="alert" aria-live="polite">
    <p><?php echo htmlspecialchars($_consentText, ENT_QUOTES); ?> <a href="<?php echo htmlspecialchars($siteUrl . $_consentPrivUrl, ENT_QUOTES); ?>" style="color:var(--accent-teal-light);text-decoration:underline;">Mehr erfahren</a></p>
    <div class="consent-btns">
        <button class="btn btn-sm btn-accent" id="consent-accept">Einwilligen</button>
        <button class="btn btn-sm btn-ghost" id="consent-decline" style="border-color:rgba(255,255,255,.3);color:rgba(255,255,255,.7);">Ablehnen</button>
    </div>
</div>
<?php endif; ?>

<?php \CMS\Hooks::doAction('body_end'); ?>
</body>
</html>
