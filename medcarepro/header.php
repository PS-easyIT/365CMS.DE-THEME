<?php
/**
 * MedCare Pro Theme – Header Template
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$mcTitle    = mc_site_title();
$mcTitleEsc = htmlspecialchars($mcTitle, ENT_QUOTES, 'UTF-8');
$mcBodyCls  = mc_body_class();

$mcHomeUrl     = htmlspecialchars(theme_route_url('home'),     ENT_QUOTES, 'UTF-8');
$mcSearchUrl   = htmlspecialchars(theme_route_url('search'),   ENT_QUOTES, 'UTF-8');
$mcLoginUrl    = htmlspecialchars(theme_route_url('login'),    ENT_QUOTES, 'UTF-8');
$mcRegisterUrl = htmlspecialchars(theme_route_url('register'), ENT_QUOTES, 'UTF-8');
$mcMemberUrl   = htmlspecialchars(theme_route_url('member'),   ENT_QUOTES, 'UTF-8');

$mcLogoUrl       = (string) mc_get_setting('header', 'logo_url',              '');
$mcShowSearch    = true;
$mcShowFontSize  = filter_var(mc_get_setting('accessibility', 'enable_font_size_toggle',    true),  FILTER_VALIDATE_BOOLEAN);
$mcShowContrast  = filter_var(mc_get_setting('accessibility', 'enable_high_contrast_toggle', true), FILTER_VALIDATE_BOOLEAN);
$mcShowEmergency = filter_var(mc_get_setting('header',        'show_emergency_banner',      false), FILTER_VALIDATE_BOOLEAN);

$mcEmergencyPhone  = (string) mc_get_setting('header', 'emergency_phone',       '112');
$mcEmergencyBanner = (string) mc_get_setting('header', 'emergency_banner_text', 'Notfall? Bitte rufen Sie sofort an:');
$mcTelHref         = mc_tel_sanitize($mcEmergencyPhone);
$mcIsLoggedIn      = theme_is_logged_in();

$mcEmergencyActive = $mcShowEmergency && $mcTelHref !== '' && $mcEmergencyPhone !== '';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $mcTitleEsc; ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo $mcBodyCls; ?>">
<a class="mc-skip-link" href="#main">Zum Inhalt springen</a>
<div id="page" class="mc-page-wrapper">

<?php if ($mcEmergencyActive) : ?>
<div class="mc-emergency-banner" role="alert" aria-live="polite">
    <div class="mc-container mc-emergency-banner-row">
        <span class="mc-emergency-banner-label">
            <span class="mc-emergency-banner-icon" aria-hidden="true">⚠</span>
            <?php echo htmlspecialchars($mcEmergencyBanner, ENT_QUOTES, 'UTF-8'); ?>
        </span>
        <a href="tel:<?php echo htmlspecialchars($mcTelHref, ENT_QUOTES, 'UTF-8'); ?>" class="mc-emergency-phone">
            <?php echo htmlspecialchars($mcEmergencyPhone, ENT_QUOTES, 'UTF-8'); ?>
        </a>
    </div>
</div>
<?php endif; ?>

<header id="masthead" class="mc-site-header" role="banner">
    <div class="mc-header-inner">

        <div class="mc-branding">
            <a href="<?php echo $mcHomeUrl; ?>" rel="home" class="mc-branding-link" aria-label="<?php echo $mcTitleEsc; ?>">
                <?php if ($mcLogoUrl !== '') : ?>
                    <img src="<?php echo htmlspecialchars($mcLogoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo $mcTitleEsc; ?>" class="mc-logo-img">
                <?php else : ?>
                    <span class="mc-logo-text">
                        <span class="mc-logo-mark" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg"
                                 focusable="false" aria-hidden="true">
                                <rect x="2" y="2" width="20" height="20" rx="4" fill="currentColor" opacity=".12"/>
                                <path d="M10 5h4v5h5v4h-5v5h-4v-5H5v-4h5z" fill="currentColor"/>
                            </svg>
                        </span>
                        <?php echo $mcTitleEsc; ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>

        <nav id="site-navigation" class="main-navigation" aria-label="Hauptnavigation">
            <?php theme_nav_menu('primary-nav'); ?>
        </nav>

        <div class="mc-header-actions">
            <?php if ($mcShowFontSize) : ?>
                <button id="fontSizeToggle"
                        type="button"
                        class="mc-icon-btn mc-font-size-toggle"
                        aria-label="Schriftgröße ändern"
                        aria-pressed="false"
                        title="Schriftgröße">A±</button>
            <?php endif; ?>
            <?php if ($mcShowContrast) : ?>
                <button id="contrastToggle"
                        type="button"
                        class="mc-icon-btn mc-contrast-toggle"
                        aria-label="Kontrast wechseln"
                        aria-pressed="false"
                        title="Hoher Kontrast">◐</button>
            <?php endif; ?>
            <?php if ($mcShowSearch) : ?>
                <button id="searchToggle"
                        type="button"
                        class="mc-icon-btn mc-search-toggle-btn"
                        aria-label="Suche öffnen"
                        aria-expanded="false"
                        aria-controls="searchPanel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         focusable="false" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>
            <?php endif; ?>
            <?php if ($mcIsLoggedIn) : ?>
                <a href="<?php echo $mcMemberUrl; ?>" class="mc-btn mc-btn-primary">Mein Bereich</a>
            <?php else : ?>
                <a href="<?php echo $mcLoginUrl; ?>"    class="mc-btn mc-btn-ghost">Anmelden</a>
                <a href="<?php echo $mcRegisterUrl; ?>" class="mc-btn mc-btn-primary">Arzt registrieren</a>
            <?php endif; ?>
            <button id="mobileMenuToggle"
                    type="button"
                    class="mc-mobile-toggle"
                    aria-label="Menü öffnen"
                    aria-expanded="false"
                    aria-controls="site-navigation">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <?php if ($mcShowSearch) : ?>
    <div id="searchPanel" class="mc-search-panel" hidden aria-hidden="true">
        <div class="mc-header-inner mc-search-panel-inner">
            <form role="search" method="get" action="<?php echo $mcSearchUrl; ?>" class="mc-header-search-form">
                <label for="mc-search" class="mc-visually-hidden">Arzt, Fachgebiet oder PLZ suchen</label>
                <input id="mc-search"
                       type="search"
                       name="q"
                       placeholder="Arzt, Fachgebiet, PLZ suchen …"
                       autocomplete="off"
                       class="mc-header-search-input">
                <button type="submit" class="mc-btn mc-btn-primary" aria-label="Suchen">Suchen</button>
            </form>
            <button id="searchClose"
                    type="button"
                    class="mc-icon-btn mc-search-close"
                    aria-label="Suche schließen">✕</button>
        </div>
    </div>
    <?php endif; ?>
</header><!-- #masthead -->
<div id="content" class="mc-site-content">
