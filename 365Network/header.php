<?php
/**
 * Header Template – Dark Navy Dashboard Header
 *
 * @package IT_Expert_Network_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$themeManager = \CMS\ThemeManager::instance();
$siteTitle    = $themeManager->getSiteTitle();
$siteDesc     = $themeManager->getSiteDescription();
$themeUrl     = $themeManager->getThemeUrl();
$isLoggedIn   = theme_is_logged_in();
$isAdmin      = false;
$currentUser  = null;

try {
    $auth = \CMS\Auth::instance();
    $isAdmin = $auth->isAdmin();
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
    }
} catch (\Throwable $e) {
    // Auth nicht verfügbar
}

try {
    $_headerLogoUrl     = \CMS\Services\ThemeCustomizer::instance()->get('header', 'logo_url', '');
    $_showSearchBtn     = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'show_search_btn', true), FILTER_VALIDATE_BOOLEAN);
    $_showLoginBtn      = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'show_login_btn', true), FILTER_VALIDATE_BOOLEAN);
    $_loginIconOnly     = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'login_btn_icon_only', false), FILTER_VALIDATE_BOOLEAN);
    $_showRegisterBtn   = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'show_register_btn', true), FILTER_VALIDATE_BOOLEAN);
    $_registerIconOnly  = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'register_btn_icon_only', false), FILTER_VALIDATE_BOOLEAN);
    $_showNetworkAnim   = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('effects', 'show_network_animation', true), FILTER_VALIDATE_BOOLEAN);
    $_animSpeed         = (string)\CMS\Services\ThemeCustomizer::instance()->get('effects', 'animation_speed', 'slow');
    $_animNodeCount     = (int)\CMS\Services\ThemeCustomizer::instance()->get('effects', 'animation_node_count', 25);

    // Profil-Dropdown-Einstellungen
    $_profileShowDashboard = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_dashboard', true), FILTER_VALIDATE_BOOLEAN);
    $_profileShowExpert    = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_expert', true), FILTER_VALIDATE_BOOLEAN);
    $_profileShowCompany   = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_company', true), FILTER_VALIDATE_BOOLEAN);
    $_profileShowEvents    = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_events', true), FILTER_VALIDATE_BOOLEAN);
    $_profileShowSpeaker   = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_speaker', true), FILTER_VALIDATE_BOOLEAN);
    $_profileShowJobs      = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_jobs', true), FILTER_VALIDATE_BOOLEAN);
    $_profileShowBooking   = filter_var(\CMS\Services\ThemeCustomizer::instance()->get('header', 'profile_show_booking', true), FILTER_VALIDATE_BOOLEAN);
} catch (\Throwable $e) {
    $_headerLogoUrl   = '';
    $_showSearchBtn   = true;
    $_showLoginBtn    = true;
    $_loginIconOnly   = false;
    $_showRegisterBtn = true;
    $_registerIconOnly = false;
    $_showNetworkAnim = true;
    $_animSpeed       = 'slow';
    $_animNodeCount   = 25;
    $_profileShowDashboard = true;
    $_profileShowExpert    = true;
    $_profileShowCompany   = true;
    $_profileShowEvents    = true;
    $_profileShowSpeaker   = true;
    $_profileShowJobs      = true;
    $_profileShowBooking   = true;
}

// User-Initialen für Avatar
$userInitials = 'U';
if ($currentUser) {
    $userName = is_array($currentUser) ? ($currentUser['username'] ?? '') : ($currentUser->username ?? '');
    $userInitials = mb_strtoupper(mb_substr($userName, 0, 2));
}
$displayName = '';
if ($currentUser) {
    $displayName = is_array($currentUser)
        ? ($currentUser['display_name'] ?? $currentUser['username'] ?? 'Benutzer')
        : ($currentUser->display_name ?? $currentUser->username ?? 'Benutzer');
}

// Plugin-Verfügbarkeit für Profil-Dropdown
$_pluginMgr   = \CMS\PluginManager::instance();
$_hasExperts   = $_pluginMgr->isPluginActive('cms-experts');
$_hasCompanies = $_pluginMgr->isPluginActive('cms-companies');
$_hasEvents    = $_pluginMgr->isPluginActive('cms-events');
$_hasSpeakers  = $_pluginMgr->isPluginActive('cms-speakers');
$_hasJobs      = $_pluginMgr->isPluginActive('cms-jobprofile-generator');
$_hasBooking   = $_pluginMgr->isPluginActive('cms-booking');
$siteHomeUrl   = theme_route_url('home');
$headerLogoUrl = theme_safe_url((string) $_headerLogoUrl, '');
$searchUrl     = theme_route_url('search');
$dashboardUrl  = theme_route_url('member-dashboard', ['area' => $isAdmin ? 'admin' : 'member']);
$notificationsUrl = theme_route_url('member-notifications');
$expertProfileUrl = theme_route_url('member-expert-profile');
$memberCompaniesUrl = theme_route_url('member-companies');
$memberEventsUrl = theme_route_url('member-events');
$speakerProfileUrl = theme_route_url('member-speaker-profile');
$jobsUrl = theme_route_url('jobs');
$bookingUrl = theme_route_url('booking');
$memberSettingsUrl = theme_route_url('member-settings');
$logoutUrl = theme_route_url('logout');
$loginUrl = theme_route_url('login');
$registerUrl = theme_route_url('register');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>

<body>
<?php \CMS\Hooks::doAction('body_start'); ?>
<div id="page" class="site">
    <a class="skip-link" href="#content">Zum Inhalt springen</a>

    <header id="masthead" class="site-header" role="banner">
        <?php if ($_showNetworkAnim) : ?>
            <canvas class="network-canvas" id="networkCanvas" aria-hidden="true"
                    data-node-count="<?php echo (int) $_animNodeCount; ?>"
                    data-speed="<?php echo htmlspecialchars($_animSpeed, ENT_QUOTES, 'UTF-8'); ?>"></canvas>
        <?php endif; ?>
        <div class="header-container">
            <div class="header-inner">

                <!-- Branding -->
                <div class="site-branding">
                    <div class="site-logo">
                        <a href="<?php echo htmlspecialchars($siteHomeUrl, ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if ($headerLogoUrl !== '') : ?>
                                <img src="<?php echo htmlspecialchars($headerLogoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>"
                                     loading="eager"
                                     width="120" height="40"
                                     class="site-logo-image">
                            <?php else : ?>
                                <svg class="network-icon" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <circle cx="30" cy="30" r="6" fill="currentColor"/>
                                    <circle cx="15" cy="15" r="5" fill="currentColor"/>
                                    <circle cx="45" cy="15" r="5" fill="currentColor"/>
                                    <circle cx="15" cy="45" r="5" fill="currentColor"/>
                                    <circle cx="45" cy="45" r="5" fill="currentColor"/>
                                    <line x1="30" y1="30" x2="15" y2="15" stroke="currentColor" stroke-width="2"/>
                                    <line x1="30" y1="30" x2="45" y2="15" stroke="currentColor" stroke-width="2"/>
                                    <line x1="30" y1="30" x2="15" y2="45" stroke="currentColor" stroke-width="2"/>
                                    <line x1="30" y1="30" x2="45" y2="45" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="site-identity">
                        <h1 class="site-title">
                            <a href="<?php echo htmlspecialchars($siteHomeUrl, ENT_QUOTES, 'UTF-8'); ?>" rel="home">
                                <?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </h1>
                    </div>
                </div>

                <!-- Hauptmenü (Desktop) -->
                <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Hauptmenü">
                    <?php theme_nav_menu('primary'); ?>
                </nav>

                <!-- Header Actions -->
                <div class="header-actions">
                    <?php if ($isLoggedIn) : ?>
                        <!-- Quick Notifications -->
                                <a href="<?php echo htmlspecialchars($notificationsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                           class="quick-notifications" aria-label="Quick-Notificationen">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                            Quick-Notificationen
                        </a>

                        <!-- Profil-Dropdown -->
                        <div class="profile-dropdown" id="profileDropdown">
                            <button type="button" class="admin-profile" id="profileToggle"
                                    aria-expanded="false" aria-haspopup="true" aria-controls="profileMenu">
                                <span class="admin-profile-avatar"><?php echo htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="admin-profile-name"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></span>
                                <svg class="profile-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                            <div class="profile-dropdown-menu" id="profileMenu" role="menu" aria-hidden="true">
                                <div class="profile-dropdown-header">
                                    <span class="profile-dropdown-avatar"><?php echo htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <div>
                                        <div class="profile-dropdown-name"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="profile-dropdown-role"><?php echo htmlspecialchars($isAdmin ? 'Administrator' : 'Mitglied', ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>
                                <div class="profile-dropdown-divider"></div>

                                <?php if ($_profileShowDashboard) : ?>
                                          <a href="<?php echo htmlspecialchars($dashboardUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">📊</span>
                                    Dashboard
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowExpert && $_hasExperts) : ?>
                                          <a href="<?php echo htmlspecialchars($expertProfileUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">👤</span>
                                    Experten-Profil
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowCompany && $_hasCompanies) : ?>
                                          <a href="<?php echo htmlspecialchars($memberCompaniesUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">🏢</span>
                                    Firmenprofil
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowEvents && $_hasEvents) : ?>
                                          <a href="<?php echo htmlspecialchars($memberEventsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">📅</span>
                                    Meine Events
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowSpeaker && $_hasSpeakers) : ?>
                                          <a href="<?php echo htmlspecialchars($speakerProfileUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">🎤</span>
                                    Speaker-Profil
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowJobs && $_hasJobs) : ?>
                                          <a href="<?php echo htmlspecialchars($jobsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">💼</span>
                                    Stellenmarkt
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowBooking && $_hasBooking) : ?>
                                          <a href="<?php echo htmlspecialchars($bookingUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">📅</span>
                                    Meine Buchungen
                                </a>
                                <?php endif; ?>

                                <div class="profile-dropdown-divider"></div>
                                          <a href="<?php echo htmlspecialchars($memberSettingsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">⚙️</span>
                                    Einstellungen
                                </a>
                                          <a href="<?php echo htmlspecialchars($logoutUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                   class="profile-dropdown-item profile-dropdown-item--danger" role="menuitem">
                                    <span class="profile-dropdown-icon">🚪</span>
                                    Abmelden
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <?php if ($_showLoginBtn) : ?>
                                     <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>"
                               class="btn btn-sm btn-outline-light<?php echo $_loginIconOnly ? ' btn-icon-only' : ''; ?>"
                               <?php echo $_loginIconOnly ? 'aria-label="Anmelden" title="Anmelden"' : ''; ?>>
                                🔑<?php if (!$_loginIconOnly) : ?> Anmelden<?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($_showRegisterBtn) : ?>
                                     <a href="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8'); ?>"
                               class="btn btn-sm btn-accent<?php echo $_registerIconOnly ? ' btn-icon-only' : ''; ?>"
                               <?php echo $_registerIconOnly ? 'aria-label="Registrieren" title="Registrieren"' : ''; ?>>
                                ✏️<?php if (!$_registerIconOnly) : ?> Registrieren<?php endif; ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Dark Mode Toggle -->
                    <button class="theme-toggle" id="themeToggle" aria-label="Dark Mode umschalten" type="button">
                        <span class="theme-toggle-icon dark-icon">🌙</span>
                        <span class="theme-toggle-icon light-icon">☀️</span>
                    </button>

                    <!-- Search Toggle -->
                    <?php if ($_showSearchBtn) : ?>
                    <button class="search-toggle" id="searchToggle" aria-label="Suche öffnen" type="button" aria-expanded="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Menü öffnen"
                            aria-expanded="false" aria-controls="mobileMenuDrawer" type="button">
                        <span class="hamburger" aria-hidden="true">
                            <span class="line"></span>
                            <span class="line"></span>
                            <span class="line"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <?php \CMS\Hooks::doAction('after_header'); ?>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" role="presentation"></div>

    <!-- Mobile Menu Drawer -->
    <nav id="mobileMenuDrawer" class="mobile-menu-drawer" aria-label="Mobile Navigation" aria-hidden="true" tabindex="-1">
        <?php theme_nav_menu('mobile'); ?>
    </nav>

    <!-- Search Overlay -->
    <div class="search-overlay" id="searchOverlay" role="dialog" aria-modal="true" aria-labelledby="searchOverlayTitle" aria-hidden="true" tabindex="-1">
        <button class="search-overlay-close" id="searchOverlayClose" aria-label="Suche schließen" type="button">&times;</button>
        <div class="search-overlay-inner">
            <h2 id="searchOverlayTitle" class="screen-reader-text">Schnellsuche</h2>
            <form class="search-overlay-form" action="<?php echo htmlspecialchars($searchUrl, ENT_QUOTES, 'UTF-8'); ?>" method="GET">
                <input class="search-overlay-input" type="search" name="q" placeholder="Suche nach Experten, Firmen, Events…"
                       autocomplete="off" spellcheck="false" aria-label="Suchbegriff eingeben">
                <button type="submit" class="search-overlay-submit">Suchen</button>
            </form>
        </div>
    </div>

    <div id="content" class="site-content">
