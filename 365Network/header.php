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
$siteUrl      = SITE_URL;

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
            <canvas class="network-canvas" id="networkCanvas" aria-hidden="true"></canvas>
        <?php endif; ?>
        <div class="header-container">
            <div class="header-inner">

                <!-- Branding -->
                <div class="site-branding">
                    <div class="site-logo">
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/" aria-label="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php if (!empty($_headerLogoUrl)) : ?>
                                <img src="<?php echo htmlspecialchars($_headerLogoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                     alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>"
                                     loading="eager"
                                     width="120" height="40"
                                     style="max-height:var(--logo-max-height,40px);height:auto;display:block;">
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
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/" rel="home">
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
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member/notifications"
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
                                        <div class="profile-dropdown-role"><?php echo $isAdmin ? 'Administrator' : 'Mitglied'; ?></div>
                                    </div>
                                </div>
                                <div class="profile-dropdown-divider"></div>

                                <?php if ($_profileShowDashboard) : ?>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/<?php echo $isAdmin ? 'admin' : 'member'; ?>"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">📊</span>
                                    Dashboard
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowExpert && $_hasExperts) : ?>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member/expert-profile"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">👤</span>
                                    Experten-Profil
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowCompany && $_hasCompanies) : ?>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member/companies"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">🏢</span>
                                    Firmenprofil
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowEvents && $_hasEvents) : ?>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member/events"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">📅</span>
                                    Meine Events
                                </a>
                                <?php endif; ?>

                                <?php if ($_profileShowSpeaker && $_hasSpeakers) : ?>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member/speaker-profile"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">🎤</span>
                                    Speaker-Profil
                                </a>
                                <?php endif; ?>

                                <div class="profile-dropdown-divider"></div>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/member/settings"
                                   class="profile-dropdown-item" role="menuitem">
                                    <span class="profile-dropdown-icon">⚙️</span>
                                    Einstellungen
                                </a>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/logout"
                                   class="profile-dropdown-item profile-dropdown-item--danger" role="menuitem">
                                    <span class="profile-dropdown-icon">🚪</span>
                                    Abmelden
                                </a>
                            </div>
                        </div>
                    <?php else : ?>
                        <?php if ($_showLoginBtn) : ?>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/login"
                               class="btn btn-sm btn-outline-light<?php echo $_loginIconOnly ? ' btn-icon-only' : ''; ?>"
                               <?php echo $_loginIconOnly ? 'aria-label="Anmelden" title="Anmelden"' : ''; ?>>
                                🔑<?php if (!$_loginIconOnly) : ?> Anmelden<?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($_showRegisterBtn) : ?>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/register"
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
    <nav id="mobileMenuDrawer" class="mobile-menu-drawer" aria-label="Mobile Navigation" aria-hidden="true">
        <?php theme_nav_menu('mobile'); ?>
    </nav>

    <!-- Search Overlay -->
    <div class="search-overlay" id="searchOverlay" role="dialog" aria-label="Schnellsuche" aria-hidden="true">
        <button class="search-overlay-close" id="searchOverlayClose" aria-label="Suche schließen" type="button">&times;</button>
        <div class="search-overlay-inner">
            <form class="search-overlay-form" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/search" method="GET">
                <input class="search-overlay-input" type="search" name="q" placeholder="Suche nach Experten, Firmen, Events…"
                       autocomplete="off" spellcheck="false" aria-label="Suchbegriff eingeben">
                <button type="submit" class="search-overlay-submit">Suchen</button>
            </form>
        </div>
    </div>

    <div id="content" class="site-content">

<?php if ($_showNetworkAnim) : ?>
<script>
/**
 * 365Network – Dezente Header-Netzwerk-Animation
 * Canvas-basierte Partikel mit Verbindungslinien.
 * Respektiert prefers-reduced-motion und ist per Customizer steuerbar.
 */
(function() {
    'use strict';
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var canvas = document.getElementById('networkCanvas');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');
    var nodes = [];
    var nodeCount = <?php echo (int)$_animNodeCount; ?>;
    var speedMap = { slow: 0.15, normal: 0.35, fast: 0.6 };
    var baseSpeed = speedMap[<?php echo json_encode($_animSpeed); ?>] || 0.15;
    var maxDist = 120;
    var raf;

    function resize() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }

    function init() {
        resize();
        nodes = [];
        for (var i = 0; i < nodeCount; i++) {
            nodes.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * baseSpeed,
                vy: (Math.random() - 0.5) * baseSpeed,
                r: Math.random() * 1.5 + 0.8
            });
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        var w = canvas.width, h = canvas.height;

        // Verbindungslinien
        for (var i = 0; i < nodes.length; i++) {
            for (var j = i + 1; j < nodes.length; j++) {
                var dx = nodes[i].x - nodes[j].x;
                var dy = nodes[i].y - nodes[j].y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < maxDist) {
                    var alpha = 1 - dist / maxDist;
                    ctx.strokeStyle = 'rgba(200, 149, 46, ' + (alpha * 0.35) + ')';
                    ctx.lineWidth = 0.5;
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.stroke();
                }
            }
        }

        // Knoten
        for (var k = 0; k < nodes.length; k++) {
            var n = nodes[k];
            ctx.fillStyle = 'rgba(200, 149, 46, 0.6)';
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
            ctx.fill();

            // Bewegen
            n.x += n.vx;
            n.y += n.vy;
            if (n.x < 0 || n.x > w) n.vx *= -1;
            if (n.y < 0 || n.y > h) n.vy *= -1;
        }

        raf = requestAnimationFrame(draw);
    }

    window.addEventListener('resize', function() {
        resize();
    });

    // Nur animieren wenn Header sichtbar ist
    var observer = new IntersectionObserver(function(entries) {
        if (entries[0].isIntersecting) {
            if (!raf) raf = requestAnimationFrame(draw);
        } else {
            if (raf) { cancelAnimationFrame(raf); raf = null; }
        }
    }, { threshold: 0.1 });
    observer.observe(canvas);

    init();
    raf = requestAnimationFrame(draw);
})();
</script>
<?php endif; ?>
