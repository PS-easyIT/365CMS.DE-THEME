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
    $_showConsent    = false; // Default: aus – CMS-Admin muss cookie_consent_enabled aktivieren
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
    $_showConsent   = false; // Default: aus
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
// Cookie Consent: Banner nur anzeigen wenn CMS-Admin cookie_consent_enabled = '1' gesetzt hat
try {
    $_db   = \CMS\Database::instance();
    $_stmt = $_db->prepare('SELECT option_value FROM ' . $_db->prefix() . 'settings WHERE option_name = ?');
    $_stmt->execute(['cookie_consent_enabled']);
    $_row  = $_stmt->fetch(\PDO::FETCH_ASSOC);
    if ($_row !== false && ($_row['option_value'] ?? '0') === '1') {
        // CMS aktiviert → Customizer-Toggle zusätzlich auswerten
        try {
            $_showConsent = filter_var(
                \CMS\Services\ThemeCustomizer::instance()->get('footer', 'show_consent_banner', true),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (\Throwable $_e2) {
            $_showConsent = true; // Customizer nicht erreichbar → zeigen wenn DB aktiv
        }
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
<?php \CMS\Hooks::doAction('before_footer'); ?>
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
                        <a href="<?php echo htmlspecialchars($_liUrl, ENT_QUOTES); ?>" class="li" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($_liLabel, ENT_QUOTES); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($_ghUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_ghUrl, ENT_QUOTES); ?>" class="gh" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($_ghLabel, ENT_QUOTES); ?>">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.418 2.865 8.166 6.839 9.489.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.342-3.369-1.342-.454-1.155-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.268 2.75 1.026A9.578 9.578 0 0 1 12 6.836a9.59 9.59 0 0 1 2.504.337c1.909-1.294 2.747-1.026 2.747-1.026.546 1.377.202 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.161 22 16.416 22 12c0-5.523-4.477-10-10-10z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($_twUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_twUrl, ENT_QUOTES); ?>" class="tw" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.736l7.73-8.835L1.254 2.25H8.08l4.259 5.63 5.905-5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($_maUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_maUrl, ENT_QUOTES); ?>" class="ma" target="_blank" rel="noopener noreferrer me" aria-label="Mastodon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.193 7.88c0-5.207-3.411-6.733-3.411-6.733C18.062.357 15.108.025 12.041 0h-.076C8.898.025 5.946.357 4.217 1.147 4.217 1.147.806 2.673.806 7.88c0 1.28-.027 2.809.017 4.224C.992 17.616 3.78 21.512 8.05 22.581c1.73.456 3.213.55 4.41.485 2.165-.12 3.38-.77 3.38-.77l-.073-1.596s-1.548.486-3.286.428c-1.724-.058-3.542-.181-3.82-2.244a4.357 4.357 0 0 1-.04-.598s1.69.413 3.832.51c1.308.06 2.532-.077 3.773-.227 2.385-.284 4.462-1.75 4.726-3.091.41-2.086.375-5.093.375-5.093zm-3.357 5.237H17.44V8.645c0-1.29-.543-1.945-1.628-1.945-1.2 0-1.802.776-1.802 2.31v3.35h-2.38v-3.35c0-1.534-.602-2.31-1.802-2.31-1.085 0-1.628.655-1.628 1.945v4.472H5.806V8.457c0-1.289.328-2.313.988-3.07.68-.758 1.569-1.146 2.673-1.146 1.278 0 2.247.491 2.886 1.474l.622 1.043.623-1.043c.639-.983 1.608-1.474 2.886-1.474 1.104 0 1.993.388 2.673 1.146.66.757.988 1.781.988 3.07v4.66z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($_ytUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_ytUrl, ENT_QUOTES); ?>" class="yt" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($_xiUrl)): ?>
                        <a href="<?php echo htmlspecialchars($_xiUrl, ENT_QUOTES); ?>" class="xi" target="_blank" rel="noopener noreferrer" aria-label="XING">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.188 0c-.517 0-.741.325-.927.66 0 0-7.455 13.224-7.702 13.657.015.024 4.919 9.023 4.919 9.023.17.308.436.66.967.66h3.454c.211 0 .375-.078.463-.22.089-.151.089-.346-.009-.536l-4.879-8.916c-.004-.006-.004-.016 0-.022L22.139.756c.095-.191.097-.387.006-.535C22.056.078 21.894 0 21.686 0h-3.498zM3.648 4.74c-.211 0-.385.074-.473.216-.09.149-.078.339.02.531l2.34 4.05c.004.01.004.016 0 .021L1.86 16.051c-.099.188-.093.381 0 .529.085.142.247.22.455.22h3.51c.519 0 .766-.339.948-.66l3.67-6.516-2.339-4.054c-.176-.301-.44-.83-.979-.83H3.648z"/></svg>
                        </a>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($_rssUrl, ENT_QUOTES); ?>" class="rss" target="_blank" rel="noopener noreferrer" aria-label="<?php echo htmlspecialchars($_rssLabel, ENT_QUOTES); ?>">
                            <svg width="16" height="16" viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><circle cx="2.5" cy="11.5" r="1.5"/><path d="M1 7.5C3.72 7.5 6.07 9.28 6.77 11.5H8.97C8.18 8.17 5.33 5.5 1 5.5V7.5Z"/><path d="M1 3.5C5.97 3.5 10 7.53 10 12.5H12C12 6.43 7.07 1.5 1 1.5V3.5Z"/></svg>
                        </a>
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
<?php \CMS\Hooks::doAction('footer'); ?>

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
