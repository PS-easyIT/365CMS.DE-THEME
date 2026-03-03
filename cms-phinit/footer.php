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
    $_footerDesc    = $c->get('footer', 'description', 'IT-Profi-Blog für Microsoft 365, PowerShell, Linux und moderne IT-Administration. Praxisnah, fundiert, auf Deutsch.');
    $_showConsent   = filter_var($c->get('footer', 'show_consent_banner', true), FILTER_VALIDATE_BOOLEAN);
    $_showBackToTop = filter_var($c->get('footer', 'show_back_to_top', true), FILTER_VALIDATE_BOOLEAN);
    $_liUrl         = $c->get('social', 'linkedin_url', '#');
    $_ghUrl         = $c->get('social', 'github_url', '#');
    $_rssUrl        = $c->get('social', 'rss_url', $siteUrl . '/feed');
} catch (\Throwable $e) {
    $_footerDesc    = 'IT-Profi-Blog für Microsoft 365, PowerShell & Linux.';
    $_showConsent   = true;
    $_showBackToTop = true;
    $_liUrl = '#'; $_ghUrl = '#';
    $_rssUrl = $siteUrl . '/feed';
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
?>

</main><!-- /#main-content -->
</div><!-- /.page-wrap -->

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<footer class="site-footer" role="contentinfo">
    <div class="container">

        <!-- Haupt-Footer: 3-spaltig (Brand + 2 Nav-Spalten) -->
        <div class="footer-main">

            <!-- Marken-Spalte -->
            <div class="footer-brand">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="logo">
                    PHIN<span>IT</span>.DE
                </a>
                <p><?php echo htmlspecialchars($_footerDesc, ENT_QUOTES); ?></p>
                <div class="social-icons">
                    <a href="<?php echo htmlspecialchars($_liUrl, ENT_QUOTES); ?>" class="li" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in</a>
                    <a href="<?php echo htmlspecialchars($_ghUrl, ENT_QUOTES); ?>" class="gh" target="_blank" rel="noopener noreferrer" aria-label="GitHub">gh</a>
                    <a href="<?php echo htmlspecialchars($_rssUrl, ENT_QUOTES); ?>" class="rss" target="_blank" rel="noopener noreferrer" aria-label="RSS-Feed">⊞</a>
                </div>
            </div>

            <!-- Navigation 1 -->
            <div class="footer-nav">
                <h4>Themen</h4>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/linux">Linux & BASH</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell">PowerShell</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/microsoft-365">Microsoft 365</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/intune">Intune & MDM</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutz">Datenschutz & DSGVO</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/news">IT-News</a></li>
                </ul>
            </div>

            <!-- Navigation 2 -->
            <div class="footer-nav">
                <h4>Seiten</h4>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/ueber-uns">Über mich</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kontakt">Kontakt</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/feed" target="_blank" rel="noopener">RSS-Feed</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/impressum">Impressum</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutzerklaerung">Datenschutzerklärung</a></li>
                    <li><a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/disclaimer">Disclaimer</a></li>
                </ul>
            </div>

        </div><!-- /.footer-main -->

        <!-- Copyright-Leiste -->
        <div class="footer-bottom">
            <span>© <?php echo $year; ?> <?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?> – Alle Rechte vorbehalten</span>
            <div class="footer-bottom-links">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/impressum">Impressum</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutzerklaerung">Datenschutz</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/disclaimer">Disclaimer</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/cookie-policy">Cookie-Policy</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/privacy-statement">Privacy Statement</a>
            </div>
        </div>

    </div><!-- /.container -->
</footer>
<!-- ═══ FOOTER ENDE ═══════════════════════════════════════════════════════ -->

<!-- Back to Top -->
<?php if ($_showBackToTop): ?>
<button id="back-to-top" style="display:none;position:fixed;bottom:24px;right:24px;width:40px;height:40px;border-radius:50%;background:var(--primary-color);color:#fff;border:none;cursor:pointer;font-size:1.1rem;z-index:900;box-shadow:0 4px 14px rgba(0,0,0,.25);align-items:center;justify-content:center;transition:all .25s;" aria-label="Zum Seitenanfang">↑</button>
<?php endif; ?>

<!-- Einwilligungsbanner (DSGVO) -->
<?php if ($_showConsent): ?>
<div id="consent-banner" role="alert" aria-live="polite">
    <p>Diese Website verwendet Cookies für Analyse-Zwecke. <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/cookie-policy" style="color:var(--accent-teal-light);text-decoration:underline;">Mehr erfahren</a></p>
    <div class="consent-btns">
        <button class="btn btn-sm btn-accent" id="consent-accept">Einwilligen</button>
        <button class="btn btn-sm btn-ghost" id="consent-decline" style="border-color:rgba(255,255,255,.3);color:rgba(255,255,255,.7);">Ablehnen</button>
    </div>
</div>
<?php endif; ?>

<?php \CMS\Hooks::doAction('body_end'); ?>
</body>
</html>
