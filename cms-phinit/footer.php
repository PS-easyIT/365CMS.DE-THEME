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
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$isLoggedIn = function_exists('theme_is_logged_in') ? theme_is_logged_in() : false;

// Customizer
try {
    $c = \CMS\Services\ThemeCustomizer::instance();

    // Brand
    $_brandName     = $c->get('footer', 'footer_brand_name', 'PHINIT.DE');
    $_footerDesc    = $c->get('footer', 'footer_tagline', phinit_is_english_locale($currentLocale) ? 'IT pro blog for Microsoft 365, PowerShell, Linux, and modern IT administration. Practical, well-founded, and concise.' : 'IT-Profi-Blog für Microsoft 365, PowerShell, Linux und moderne IT-Administration. Praxisnah, fundiert, auf Deutsch.');
    $_footerAboutTitle = trim((string) $c->get('homepage', 'sidebar_about_title', phinit_is_english_locale($currentLocale) ? 'About Me' : 'Über mich'));
    $_footerAboutName = trim((string) $c->get('homepage', 'sidebar_about_name', ''));
    $_footerAboutText = trim((string) $c->get('homepage', 'sidebar_about_text', ''));
    $_footerAboutImageUrl = function_exists('phinit_normalize_public_media_url')
        ? phinit_normalize_public_media_url((string) $c->get('homepage', 'sidebar_about_image_url', ''), true, $siteUrl)
        : (function_exists('phinit_safe_public_media_url')
            ? phinit_safe_public_media_url((string) $c->get('homepage', 'sidebar_about_image_url', ''), $siteUrl)
            : (string) $c->get('homepage', 'sidebar_about_image_url', ''));

    if ($_footerAboutTitle === '') {
        $_footerAboutTitle = phinit_is_english_locale($currentLocale) ? 'About Me' : 'Über mich';
    }

    if ($_footerAboutText === '') {
        $_footerAboutText = (string) $_footerDesc;
    }

    $_footerAboutText = trim((string) (preg_replace('/\s+/u', ' ', (string) $_footerAboutText) ?? $_footerAboutText));

    // Spaltentitel
    $_col2Title     = $c->get('footer', 'footer_col2_title', phinit_is_english_locale($currentLocale) ? 'Topics' : 'Themen');
    $_col3Title     = $c->get('footer', 'footer_col3_title', phinit_is_english_locale($currentLocale) ? 'Pages' : 'Seiten');
    $_col4Title     = $c->get('footer', 'footer_col4_title', phinit_is_english_locale($currentLocale) ? 'Legal' : 'Rechtliches');

    // Copyright
    $_copyrightRaw  = $c->get('footer', 'copyright_text', phinit_is_english_locale($currentLocale) ? '© {year} {site_title} – All rights reserved' : '© {year} {site_title} – Alle Rechte vorbehalten');
    $_copyright     = str_replace(['{year}', '{site_title}'], [$year, $siteTitle], (string)$_copyrightRaw);

    // Toggles
    $_showConsent    = false; // Default: aus – CMS-Admin muss cookie_consent_enabled aktivieren
    $_consentText    = $c->get('footer', 'consent_text', phinit_is_english_locale($currentLocale) ? 'This website uses cookies for analytics purposes.' : 'Diese Website verwendet Cookies für Analyse-Zwecke.');
    $_consentPrivUrl = $c->get('footer', 'consent_privacy_url', '/cookie-policy');
    $_showBackToTop  = filter_var($c->get('layout', 'enable_back_to_top', true), FILTER_VALIDATE_BOOLEAN);
    $_showNetworkBar = filter_var($c->get('footer', 'show_network_bar', false), FILTER_VALIDATE_BOOLEAN);
    $_showSocial     = filter_var($c->get('footer', 'show_footer_social', true), FILTER_VALIDATE_BOOLEAN);

    // Social URLs
    $_liUrl   = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $c->get('social', 'social_linkedin', ''), $siteUrl, ['http', 'https']) : (string) $c->get('social', 'social_linkedin', '');
    $_ghUrl   = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $c->get('social', 'social_github', ''), $siteUrl, ['http', 'https']) : (string) $c->get('social', 'social_github', '');
    $_twUrl   = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $c->get('social', 'social_twitter', ''), $siteUrl, ['http', 'https']) : (string) $c->get('social', 'social_twitter', '');
    $_maUrl   = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $c->get('social', 'social_mastodon', ''), $siteUrl, ['http', 'https']) : (string) $c->get('social', 'social_mastodon', '');
    $_ytUrl   = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $c->get('social', 'social_youtube', ''), $siteUrl, ['http', 'https']) : (string) $c->get('social', 'social_youtube', '');
    $_xiUrl   = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) $c->get('social', 'social_xing', ''), $siteUrl, ['http', 'https']) : (string) $c->get('social', 'social_xing', '');
    $_rssUrl  = function_exists('phinit_safe_public_url') ? (phinit_safe_public_url((string) $c->get('social', 'social_rss', $siteUrl . '/feed'), $siteUrl, ['http', 'https']) ?: ($siteUrl . '/feed')) : (string) $c->get('social', 'social_rss', $siteUrl . '/feed');

    // Social Labels
    $_liLabel  = $c->get('social', 'social_label_linkedin', 'LinkedIn');
    $_ghLabel  = $c->get('social', 'social_label_github', 'GitHub');
    $_rssLabel = $c->get('social', 'social_label_rss', 'RSS Feed');

    // Network-Bar Links (1–5)
    $_networkLinks = [];
    for ($i = 1; $i <= 5; $i++) {
        $label = $c->get('footer', 'network_bar_link' . $i . '_label', '');
        $url   = $c->get('footer', 'network_bar_link' . $i . '_url', '');
        $safeUrl = function_exists('phinit_safe_public_url')
            ? phinit_safe_public_url((string) $url, $siteUrl, ['http', 'https'])
            : (string) $url;
        if (!empty($label) && $safeUrl !== '') {
            $_networkLinks[] = ['label' => $label, 'url' => $safeUrl];
        }
    }
} catch (\Throwable $e) {
    $_brandName     = 'PHINIT.DE';
    $_footerDesc    = phinit_is_english_locale($currentLocale) ? 'IT pro blog for Microsoft 365, PowerShell & Linux.' : 'IT-Profi-Blog für Microsoft 365, PowerShell & Linux.';
    $_footerAboutTitle = phinit_is_english_locale($currentLocale) ? 'About Me' : 'Über mich';
    $_footerAboutName = '';
    $_footerAboutText = $_footerDesc;
    $_footerAboutImageUrl = '';
    $_col2Title     = phinit_is_english_locale($currentLocale) ? 'Topics' : 'Themen';
    $_col3Title     = phinit_is_english_locale($currentLocale) ? 'Pages' : 'Seiten';
    $_col4Title     = phinit_is_english_locale($currentLocale) ? 'Legal' : 'Rechtliches';
    $_copyright     = phinit_is_english_locale($currentLocale) ? '© ' . $year . ' ' . $siteTitle . ' – All rights reserved' : '© ' . $year . ' ' . $siteTitle . ' – Alle Rechte vorbehalten';
    $_showConsent   = false; // Default: aus
    $_consentText   = phinit_is_english_locale($currentLocale) ? 'This website uses cookies for analytics purposes.' : 'Diese Website verwendet Cookies für Analyse-Zwecke.';
    $_consentPrivUrl = '/cookie-policy';
    $_showBackToTop = true;
    $_showNetworkBar = false;
    $_showSocial    = true;
    $_liUrl = ''; $_ghUrl = ''; $_twUrl = ''; $_maUrl = ''; $_ytUrl = ''; $_xiUrl = '';
    $_rssUrl = $siteUrl . '/feed';
    $_liLabel = 'LinkedIn'; $_ghLabel = 'GitHub'; $_rssLabel = 'RSS Feed';
    $_networkLinks = [];
}

$_footerAccountPath = $isLoggedIn && function_exists('theme_account_path')
    ? theme_account_path()
    : '/member/dashboard';
$_footerAccountUrl = $isLoggedIn ? rtrim($siteUrl, '/') . $_footerAccountPath : theme_login_url(null, $currentLocale);
$_footerAccountLabel = $isLoggedIn
    ? phinit_t('account', [], $currentLocale)
    : phinit_t('login', [], $currentLocale);
$_footerAccountTitle = $isLoggedIn
    ? phinit_t('account', [], $currentLocale)
    : phinit_t('login', [], $currentLocale);
// Cookie Consent: Banner nur anzeigen wenn CMS-Admin cookie_consent_enabled = '1' gesetzt hat
try {
    if (class_exists('\\CMS\\Services\\CookieConsentService')
        && \CMS\Services\CookieConsentService::getInstance()->isManagedExternally()
    ) {
        $_showConsent = false;
    } else {
    if (phinit_is_cookie_consent_enabled_by_cms()) {
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
    }
} catch (\Throwable $_e) {}

// Footer-Menüs aus Menü-Editor laden
$footerTopicsMenu = [];
$footerPagesMenu  = [];
$footerLegalMenu  = [];
$privacyUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/datenschutz', $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/datenschutz';
$imprintUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/impressum', $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/impressum';
$termsUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/agb', $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/agb';
$_footerContactUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/contact', $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/contact';
$_footerContactLabel = phinit_is_english_locale($currentLocale) ? 'Contact' : 'Kontakt';
try {
    $footerTopicsMenu = \CMS\ThemeManager::instance()->getMenu('footer-topics');
} catch (\Throwable $e) {}
try {
    $footerPagesMenu = \CMS\ThemeManager::instance()->getMenu('footer-pages');
} catch (\Throwable $e) {}
try {
    $footerLegalMenu = \CMS\ThemeManager::instance()->getMenu('footer');
} catch (\Throwable $e) {}

$_footerRepoViewModel = [];
try {
    $_footerRequestPath = function_exists('phinit_current_request_path') ? phinit_current_request_path() : '/';
    $_footerBaseUri = $_footerRequestPath;

    if (class_exists('CMS\\Services\\ContentLocalizationService')) {
        $_footerRequestContext = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($_footerRequestPath);
        $_footerBaseUri = (string) ($_footerRequestContext['base_uri'] ?? $_footerRequestPath);
    }

    if ($_footerBaseUri === '' || $_footerBaseUri === '/') {
        require_once __DIR__ . '/includes/theme-home-helpers.php';

        if (function_exists('phinit_get_homepage_view_model')) {
            $_footerRepoViewModel = phinit_get_homepage_view_model();
        }
    }
} catch (\Throwable $_footerRepoError) {
    $_footerRepoViewModel = [];
}

$_footerShowRepoBanner = !empty($_footerRepoViewModel['_showRepo']) && !empty($_footerRepoViewModel['_repoTitle']);
?>

</main><!-- /#main-content -->
</div><!-- /.page-wrap -->

<!-- ═══ FOOTER ═══════════════════════════════════════════════════════════ -->
<?php \CMS\Hooks::doAction('before_footer'); ?>
<?php if ($_footerShowRepoBanner): ?>
<section class="footer-repo-banner" aria-label="<?php echo htmlspecialchars((string) ($_footerRepoViewModel['_repoTitle'] ?? 'GitHub Repository'), ENT_QUOTES); ?>">
    <?php get_theme_part('partials/home-repo-card', array_merge($_footerRepoViewModel, [
        '_repoWrapSection' => false,
        '_repoFooterBanner' => true,
    ])); ?>
</section>
<?php endif; ?>
<footer class="site-footer" role="contentinfo">

    <!-- Haupt-Footer: 3-spaltig (Brand + 2 Nav-Spalten) -->
    <div class="footer-main-wrap">
        <div class="container">
            <div class="footer-main">

                <!-- Marken-Spalte -->
                <section class="footer-brand footer-about<?php echo $_footerAboutImageUrl === '' ? ' footer-about--no-image' : ''; ?>" aria-labelledby="footer-about-title">
                    <?php if ($_footerAboutImageUrl !== ''): ?>
                    <div class="footer-about__media">
                        <img src="<?php echo htmlspecialchars($_footerAboutImageUrl, ENT_QUOTES); ?>"
                             alt="<?php echo htmlspecialchars($_footerAboutName !== '' ? $_footerAboutName : $_footerAboutTitle, ENT_QUOTES); ?>"
                             class="footer-about__avatar"
                             <?php echo phinit_image_loading_attributes(false, false); ?>
                             <?php echo phinit_image_dimension_attributes($_footerAboutImageUrl, 56, 56); ?>>
                    </div>
                    <?php endif; ?>
                    <div class="footer-about__content">
                        <div class="footer-about__heading">
                            <h4 id="footer-about-title" class="footer-about__title"><?php echo htmlspecialchars($_footerAboutTitle, ENT_QUOTES); ?></h4>
                            <?php if ($_footerAboutName !== ''): ?>
                            <p class="footer-about__name"><?php echo htmlspecialchars($_footerAboutName, ENT_QUOTES); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php if ($_footerAboutText !== ''): ?>
                        <p class="footer-about__text"><?php echo htmlspecialchars($_footerAboutText, ENT_QUOTES); ?></p>
                        <?php endif; ?>
                        <div class="footer-about__actions">
                        <?php if ($_showSocial): ?>
                        <nav class="footer-about__social social-icons" aria-label="<?php echo htmlspecialchars(phinit_is_english_locale($currentLocale) ? 'Social media' : 'Social Media', ENT_QUOTES); ?>">
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
                        </nav>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($_footerContactUrl, ENT_QUOTES); ?>" class="footer-about__contact" aria-label="<?php echo htmlspecialchars($_footerContactLabel, ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($_footerContactLabel, ENT_QUOTES); ?>
                        </a>
                        </div>
                    </div>
                </section>

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
                            <li><a href="<?php echo htmlspecialchars(phinit_localized_href('/contact', $currentLocale, $siteUrl), ENT_QUOTES); ?>">Kontakt</a></li>
                            <li><a href="<?php echo htmlspecialchars(phinit_localized_href('/feed', $currentLocale, $siteUrl), ENT_QUOTES); ?>" target="_blank" rel="noopener">RSS-Feed</a></li>
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
                    <a href="<?php echo htmlspecialchars($imprintUrl, ENT_QUOTES); ?>">Impressum</a>
                    <a href="<?php echo htmlspecialchars($privacyUrl, ENT_QUOTES); ?>">Datenschutz</a>
                    <a href="<?php echo htmlspecialchars($termsUrl, ENT_QUOTES); ?>">AGB</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</footer>
<!-- ═══ FOOTER ENDE ═══════════════════════════════════════════════════════ -->
<?php \CMS\Hooks::doAction('footer'); ?>

<!-- Back to Top -->
<?php if ($_showBackToTop): ?>
<button id="back-to-top" class="back-to-top-btn" aria-label="<?php echo htmlspecialchars(phinit_t('back_to_top', [], $currentLocale), ENT_QUOTES); ?>">↑</button>
<?php endif; ?>

<!-- Einwilligungsbanner (DSGVO) -->
<?php if ($_showConsent): ?>
<div id="consent-banner" role="alert" aria-live="polite">
    <p><?php echo htmlspecialchars($_consentText, ENT_QUOTES); ?> <a href="<?php echo htmlspecialchars(phinit_localized_href((string) $_consentPrivUrl, $currentLocale, $siteUrl), ENT_QUOTES); ?>" class="consent-link"><?php echo htmlspecialchars(phinit_t('learn_more', [], $currentLocale), ENT_QUOTES); ?></a></p>
    <div class="consent-btns">
        <button class="btn btn-sm btn-accent" id="consent-accept"><?php echo htmlspecialchars(phinit_t('consent_accept', [], $currentLocale), ENT_QUOTES); ?></button>
        <button class="btn btn-sm btn-ghost btn-consent-decline" id="consent-decline"><?php echo htmlspecialchars(phinit_t('consent_decline', [], $currentLocale), ENT_QUOTES); ?></button>
    </div>
</div>
<?php endif; ?>

<?php \CMS\Hooks::doAction('body_end'); ?>
</body>
</html>
