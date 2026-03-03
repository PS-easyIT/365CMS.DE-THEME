<?php
/**
 * Header Template – CMS Phinit Theme
 * Sticky 2-Ebenen-Navigation: Logo/Util-Bar + Hauptnavigation + Quicklinks
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$themeManager = \CMS\ThemeManager::instance();
$siteTitle    = $themeManager->getSiteTitle();
$siteDesc     = $themeManager->getSiteDescription();
$themeUrl     = $themeManager->getThemeUrl();
$siteUrl      = SITE_URL;
$isLoggedIn   = function_exists('theme_is_logged_in') ? theme_is_logged_in() : false;
$currentUser  = null;

try {
    $auth = \CMS\Auth::instance();
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
    }
} catch (\Throwable $e) {
    // Auth nicht verfügbar – kein Fehler ausgeben
}

// Customizer-Einstellungen (mit Fallbacks)
try {
    $customizer    = \CMS\Services\ThemeCustomizer::instance();
    $_logoUrl      = $customizer->get('header', 'logo_url', '');
    $_showSearch   = filter_var($customizer->get('header', 'show_search', true), FILTER_VALIDATE_BOOLEAN);
    $_showDarkMode = filter_var($customizer->get('header', 'show_dark_toggle', true), FILTER_VALIDATE_BOOLEAN);
    $_showRss      = filter_var($customizer->get('header', 'show_rss_link', true), FILTER_VALIDATE_BOOLEAN);
    $_qlLinks      = $customizer->get('header', 'quicklinks', []);
} catch (\Throwable $e) {
    $_logoUrl = ''; $_showSearch = true; $_showDarkMode = true;
    $_showRss = true; $_qlLinks = [];
}

// Haupt-Navigation laden
$mainMenuItems = [];
try {
    $menuManager = \CMS\MenuManager::instance();
    $mainMenuItems = $menuManager->getMenuItems('primary') ?? [];
} catch (\Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?> – IT-Blog & Tutorials</title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body<?php echo \CMS\Hooks::applyFilters('body_class', '') ? ' class="' . htmlspecialchars(\CMS\Hooks::applyFilters('body_class', ''), ENT_QUOTES) . '"' : ''; ?>>

<div id="scroll-progress" aria-hidden="true"></div>
<a href="#main-content" class="skip-link">Zum Inhalt springen</a>

<?php \CMS\Hooks::doAction('body_start'); ?>

<!-- ═══ HEADER ═══════════════════════════════════════════════════════════ -->
<header class="site-header" id="site-header">
    <div class="container" style="position:relative;">

        <!-- Ebene 1: Logo + Util -->
        <div class="hdr-bar">
            <div class="hdr-inner hdr-util">

                <!-- Logo -->
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>" class="site-logo" aria-label="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?> – Startseite">
                    <?php if (!empty($_logoUrl)): ?>
                        <img src="<?php echo htmlspecialchars($_logoUrl, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?>" height="36" loading="eager">
                    <?php else: ?>
                        <span class="logo-icon" aria-hidden="true">P</span>
                        <span><?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </a>

                <!-- Util rechts -->
                <div class="hdr-util-right">

                    <!-- Social Links -->
                    <a href="https://linkedin.com" class="util-link" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer" title="LinkedIn">in</a>
                    <a href="https://github.com" class="util-link" aria-label="GitHub" target="_blank" rel="noopener noreferrer" title="GitHub">gh</a>
                    <?php if ($_showRss): ?>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/feed" class="util-link" aria-label="RSS-Feed" title="RSS-Feed">⊞</a>
                    <?php endif; ?>

                    <!-- Dark Mode Toggle -->
                    <?php if ($_showDarkMode): ?>
                    <button class="util-link util-dark-toggle" aria-label="Dark Mode umschalten" aria-pressed="false" title="Dark Mode">🌙</button>
                    <?php endif; ?>

                    <!-- Suche -->
                    <?php if ($_showSearch): ?>
                    <form class="hdr-search" role="search" method="GET" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search">
                        <input type="search" name="q" placeholder="Suchen …" aria-label="Suchbegriff eingeben">
                        <button type="submit" aria-label="Suche starten">🔍</button>
                    </form>
                    <?php endif; ?>

                    <!-- Login/Account -->
                    <?php if ($isLoggedIn && $currentUser): ?>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/dashboard" class="util-link" title="Mein Konto">👤</a>
                    <?php else: ?>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/login" class="btn btn-sm btn-outline" style="margin-left:8px;border-color:rgba(255,255,255,.4);color:#fff;" aria-label="Einloggen">Login</a>
                    <?php endif; ?>

                    <!-- Burger (Mobile) -->
                    <button class="burger-btn" aria-label="Menü öffnen" aria-expanded="false" aria-controls="mobile-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Ebene 2: Hauptnavigation -->
        <div class="hdr-bar hdr-bar-main">
            <div class="hdr-inner">
                <nav class="main-nav" aria-label="Hauptnavigation">
                    <?php if (!empty($mainMenuItems)): ?>
                        <?php foreach ($mainMenuItems as $item): ?>
                            <?php if (!empty($item['children'])): ?>
                            <span class="has-dropdown">
                                <a href="<?php echo htmlspecialchars($item['url'] ?? '#', ENT_QUOTES); ?>"
                                   <?php echo isset($item['label']) && strpos($_SERVER['REQUEST_URI'] ?? '', $item['url'] ?? '') !== false ? ' class="active" aria-current="page"' : ''; ?>>
                                    <?php echo htmlspecialchars($item['label'] ?? '', ENT_QUOTES); ?> ▾
                                </a>
                                <div class="dropdown">
                                    <?php foreach ($item['children'] as $child): ?>
                                    <a href="<?php echo htmlspecialchars($child['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($child['label'] ?? '', ENT_QUOTES); ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </span>
                            <?php else: ?>
                            <a href="<?php echo htmlspecialchars($item['url'] ?? '#', ENT_QUOTES); ?>"
                               <?php echo strpos($_SERVER['REQUEST_URI'] ?? '', $item['url'] ?? 'NOPE') !== false ? ' class="active" aria-current="page"' : ''; ?>>
                                <?php echo htmlspecialchars($item['label'] ?? '', ENT_QUOTES); ?>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback-Menü -->
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/">Startseite</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/linux">Linux / BASH</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell" class="has-dropdown">PowerShell ▾
                            <div class="dropdown">
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell/grundlagen">Grundlagen</a>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell/glossar">Glossar</a>
                            </div>
                        </a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/microsoft-365">Microsoft 365</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutz">Datenschutz</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/news">News</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>

        <!-- Mobiles Menü -->
        <nav class="mobile-menu" id="mobile-menu" aria-label="Mobile Navigation" aria-hidden="true">
            <div class="mob-search">
                <form role="search" method="GET" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search">
                    <input type="search" name="q" placeholder="Suchen …" aria-label="Mobilsuche">
                </form>
            </div>
            <?php if (!empty($mainMenuItems)): ?>
                <?php foreach ($mainMenuItems as $item): ?>
                <a href="<?php echo htmlspecialchars($item['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($item['label'] ?? '', ENT_QUOTES); ?></a>
                    <?php if (!empty($item['children'])): ?>
                        <?php foreach ($item['children'] as $child): ?>
                        <a href="<?php echo htmlspecialchars($child['url'] ?? '#', ENT_QUOTES); ?>" style="padding-left:36px;font-size:.82rem;opacity:.8;"><?php echo htmlspecialchars($child['label'] ?? '', ENT_QUOTES); ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/">Startseite</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/linux">Linux / BASH</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell">PowerShell</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/microsoft-365">Microsoft 365</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutz">Datenschutz</a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/news">News</a>
                <?php if (!$isLoggedIn): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/login" style="color:var(--accent-teal-light);">🔑 Login</a>
                <?php endif; ?>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Ebene 3: Quicklinks -->
    <div class="quicklinks-bar">
        <div class="container">
            <div class="hdr-bar">
                <div class="hdr-inner hdr-sub">
                    <nav class="sub-nav" aria-label="Quicklinks">
                        <?php if (!empty($_qlLinks)): ?>
                            <?php foreach ($_qlLinks as $ql): ?>
                            <a href="<?php echo htmlspecialchars($ql['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($ql['label'] ?? '', ENT_QUOTES); ?></a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/linux/bash">Linux / BASH</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/windows">Windows Shell</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/m365/admin">M365 Admin</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/news/heise-adv">News | Adv/Infra</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/sites-blogs">Sites + Blogs</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/sites/phinit">Sites / 2026</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ═══ HEADER ENDE ═══════════════════════════════════════════════════════ -->

<?php \CMS\Hooks::doAction('after_header'); ?>
<div class="page-wrap">
<main id="main-content">
