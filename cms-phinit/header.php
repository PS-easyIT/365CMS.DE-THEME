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

    // Logo
    $_logoUrl       = $customizer->get('header', 'logo_url', '');
    $_logoPart1     = $customizer->get('header', 'logo_text_part1', 'PHIN');
    $_logoPart2     = $customizer->get('header', 'logo_text_part2', 'IT');
    $_logoSuffix    = $customizer->get('header', 'logo_text_suffix', '.DE');
    $_showLogoText  = filter_var($customizer->get('header', 'show_logo_text_with_image', false), FILTER_VALIDATE_BOOLEAN);
    $_logoMaxH      = (int)$customizer->get('header', 'logo_max_height', 28);

    // Toggles
    $_showSearch    = filter_var($customizer->get('header', 'show_search_bar', true), FILTER_VALIDATE_BOOLEAN);
    $_searchPH      = $customizer->get('header', 'search_placeholder', 'Suchen …');
    $_showDarkMode  = filter_var($customizer->get('layout', 'enable_dark_mode_toggle', true), FILTER_VALIDATE_BOOLEAN);
    $_showRss       = filter_var($customizer->get('header', 'show_rss_link', true), FILTER_VALIDATE_BOOLEAN);
    $_showUtilLinks = filter_var($customizer->get('header', 'show_util_links', true), FILTER_VALIDATE_BOOLEAN);
    $_showQuicklinks = filter_var($customizer->get('header', 'show_quicklinks', true), FILTER_VALIDATE_BOOLEAN);

    // Layout-Toggles für JS
    $_enableStickyHeader    = filter_var($customizer->get('layout', 'enable_sticky_header', true), FILTER_VALIDATE_BOOLEAN);
    $_enableProgressBar     = filter_var($customizer->get('layout', 'enable_progress_bar', true), FILTER_VALIDATE_BOOLEAN);
    $_enableBackToTop       = filter_var($customizer->get('layout', 'enable_back_to_top', true), FILTER_VALIDATE_BOOLEAN);
    $_enableScrollAnimations = filter_var($customizer->get('layout', 'enable_scroll_animations', true), FILTER_VALIDATE_BOOLEAN);

    // Util Links (1–3)
    $_utilLinks = [];
    for ($i = 1; $i <= 3; $i++) {
        $text = $customizer->get('header', 'util_link' . $i . '_text', '');
        $url  = $customizer->get('header', 'util_link' . $i . '_url', '');
        if (!empty($text) && !empty($url)) {
            $_utilLinks[] = ['text' => $text, 'url' => $url];
        }
    }

    // Quicklinks (1–8)
    $_qlLinks = [];
    for ($i = 1; $i <= 8; $i++) {
        $text = $customizer->get('header', 'quicklink' . $i . '_text', '');
        $url  = $customizer->get('header', 'quicklink' . $i . '_url', '');
        if (!empty($text) && !empty($url)) {
            $_qlLinks[] = ['label' => $text, 'url' => $url];
        }
    }

    // Social (aus social-Kategorie)
    $_socialLinkedIn = $customizer->get('social', 'social_linkedin', '');
    $_socialGithub   = $customizer->get('social', 'social_github', '');
} catch (\Throwable $e) {
    $_logoUrl = ''; $_logoPart1 = 'PHIN'; $_logoPart2 = 'IT'; $_logoSuffix = '.DE';
    $_showLogoText = false; $_logoMaxH = 28;
    $_showSearch = true; $_searchPH = 'Suchen …'; $_showDarkMode = true;
    $_showRss = true; $_showUtilLinks = true; $_showQuicklinks = true;
    $_utilLinks = []; $_qlLinks = [];
    $_socialLinkedIn = ''; $_socialGithub = '';
    $_enableStickyHeader = true; $_enableProgressBar = true;
    $_enableBackToTop = true; $_enableScrollAnimations = true;
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
<body<?php
    $bodyClasses = \CMS\Hooks::applyFilters('body_class', '');
    echo $bodyClasses ? ' class="' . htmlspecialchars($bodyClasses, ENT_QUOTES) . '"' : '';
    // Layout-Toggles als data-Attribute für JS
    echo ' data-sticky-header="' . ($_enableStickyHeader ? '1' : '0') . '"';
    echo ' data-progress-bar="' . ($_enableProgressBar ? '1' : '0') . '"';
    echo ' data-back-to-top="' . ($_enableBackToTop ? '1' : '0') . '"';
    echo ' data-scroll-anims="' . ($_enableScrollAnimations ? '1' : '0') . '"';
?>>

<?php if ($_enableProgressBar): ?>
<div id="scroll-progress" aria-hidden="true"></div>
<?php endif; ?>
<a href="#main-content" class="skip-link">Zum Inhalt springen</a>

<?php \CMS\Hooks::doAction('body_start'); ?>

<!-- ═══ HEADER ═══════════════════════════════════════════════════════════ -->
<header class="site-header" id="site-header">

    <!-- Ebene 1: Logo + Util -->
    <div class="hdr-bar">
            <div class="hdr-inner hdr-util">

                <!-- Logo -->
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>" class="site-logo" aria-label="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?> – Startseite">
                    <?php if (!empty($_logoUrl)): ?>
                        <img src="<?php echo htmlspecialchars($_logoUrl, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?>" height="<?php echo $_logoMaxH; ?>" loading="eager">
                        <?php if ($_showLogoText): ?>
                        <span class="logo-text-beside"><?php echo htmlspecialchars($_logoPart1); ?><span class="logo-accent"><?php echo htmlspecialchars($_logoPart2); ?></span><?php echo htmlspecialchars($_logoSuffix); ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="logo-icon" aria-hidden="true"><?php echo htmlspecialchars(mb_substr($_logoPart1, 0, 1)); ?></span>
                        <span><?php echo htmlspecialchars($_logoPart1); ?><span class="logo-accent"><?php echo htmlspecialchars($_logoPart2); ?></span><?php echo htmlspecialchars($_logoSuffix); ?></span>
                    <?php endif; ?>
                </a>

                <!-- Util rechts -->
                <div class="hdr-util-right">

                    <!-- Util / Social Links -->
                    <?php if ($_showUtilLinks): ?>
                        <?php if (!empty($_socialLinkedIn)): ?>
                        <a href="<?php echo htmlspecialchars($_socialLinkedIn, ENT_QUOTES); ?>" class="util-link" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer" title="LinkedIn">in</a>
                        <?php endif; ?>
                        <?php if (!empty($_socialGithub)): ?>
                        <a href="<?php echo htmlspecialchars($_socialGithub, ENT_QUOTES); ?>" class="util-link" aria-label="GitHub" target="_blank" rel="noopener noreferrer" title="GitHub">gh</a>
                        <?php endif; ?>
                        <?php foreach ($_utilLinks as $_ul): ?>
                        <a href="<?php echo htmlspecialchars($_ul['url'], ENT_QUOTES); ?>" class="util-link" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($_ul['text']); ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                        <input type="search" name="q" placeholder="<?php echo htmlspecialchars($_searchPH, ENT_QUOTES); ?>" aria-label="Suchbegriff eingeben">
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

    <!-- Ebene 3: Quicklinks -->
    <?php if ($_showQuicklinks): ?>
    <div class="quicklinks-bar">
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
    <?php endif; /* $_showQuicklinks */ ?>
</header>
<!-- ═══ HEADER ENDE ═══════════════════════════════════════════════════════ -->

<?php \CMS\Hooks::doAction('after_header'); ?>
<div class="page-wrap">
<main id="main-content">
