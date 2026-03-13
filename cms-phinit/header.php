<?php
/**
 * Header Template – CMS Phinit Theme
 * Sticky 2-Ebenen-Navigation: Member-Bar + Hauptnavigation + Quicklinks
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

// Ungelesene Benachrichtigungen zählen (für Badge in Member-Bar)
$notifCount = 0;
if ($isLoggedIn && $currentUser !== null) {
    try {
        $_userId = is_object($currentUser) ? (int)($currentUser->id ?? 0) : (int)($currentUser['id'] ?? 0);
        if ($_userId > 0) {
            $_ndb = \CMS\Database::instance();
            $notifCount = (int)($_ndb->get_var(
                "SELECT COUNT(*) FROM {$_ndb->prefix()}notifications WHERE user_id = ? AND is_read = 0",
                [$_userId]
            ) ?? 0);
        }
    } catch (\Throwable) {}
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
    $_showMemberBar = filter_var($customizer->get('header', 'show_member_bar', true), FILTER_VALIDATE_BOOLEAN);
    $_showQuicklinks = filter_var($customizer->get('header', 'show_quicklinks', true), FILTER_VALIDATE_BOOLEAN);
    $_showLanguageSwitch = filter_var($customizer->get('header', 'show_language_switcher', false), FILTER_VALIDATE_BOOLEAN);
    $_languageMode = (string) $customizer->get('header', 'language_switcher_mode', 'text');
    $_languageLabel = trim((string) $customizer->get('header', 'language_switcher_label', 'EN'));
    $_languageSlug = trim((string) $customizer->get('header', 'language_switcher_slug', '/en'));
    $_languageFlag = strtolower(trim((string) $customizer->get('header', 'language_switcher_flag', 'gb')));
    $_languageAriaLabel = trim((string) $customizer->get('header', 'language_switcher_aria_label', 'Zur englischen Version wechseln'));

    // Layout-Toggles für JS
    $_enableStickyHeader    = filter_var($customizer->get('layout', 'enable_sticky_header', true), FILTER_VALIDATE_BOOLEAN);
    $_enableProgressBar     = filter_var($customizer->get('layout', 'enable_progress_bar', true), FILTER_VALIDATE_BOOLEAN);
    $_enableBackToTop       = filter_var($customizer->get('layout', 'enable_back_to_top', true), FILTER_VALIDATE_BOOLEAN);
    $_enableScrollAnimations = filter_var($customizer->get('layout', 'enable_scroll_animations', true), FILTER_VALIDATE_BOOLEAN);
    $_showBreadcrumb = filter_var($customizer->get('layout', 'show_breadcrumb', true), FILTER_VALIDATE_BOOLEAN);
    $_bcOnPosts      = filter_var($customizer->get('layout', 'breadcrumb_on_posts', true), FILTER_VALIDATE_BOOLEAN);
    $_bcOnPages      = filter_var($customizer->get('layout', 'breadcrumb_on_pages', true), FILTER_VALIDATE_BOOLEAN);

} catch (\Throwable $e) {
    $_logoUrl = ''; $_logoPart1 = 'PHIN'; $_logoPart2 = 'IT'; $_logoSuffix = '.DE';
    $_showLogoText = false; $_logoMaxH = 28;
    $_showSearch = true; $_searchPH = 'Suchen …'; $_showDarkMode = true;
    $_showRss = true; $_showMemberBar = true; $_showQuicklinks = true;
    $_showLanguageSwitch = false; $_languageMode = 'text'; $_languageLabel = 'EN'; $_languageSlug = '/en'; $_languageFlag = 'gb'; $_languageAriaLabel = 'Zur englischen Version wechseln';
    $_enableStickyHeader = true; $_enableProgressBar = true;
    $_enableBackToTop = true; $_enableScrollAnimations = true;
    $_showBreadcrumb = true; $_bcOnPosts = true; $_bcOnPages = true;
}

// Haupt-Navigation laden
$mainMenuItems = [];
try {
    $mainMenuItems = \CMS\ThemeManager::instance()->getMenu('primary');
} catch (\Throwable $e) {}

// Quicklinks laden (Sub-Navigation)
$quicklinkItems = [];
try {
    $quicklinkItems = \CMS\ThemeManager::instance()->getMenu('quicklinks');
} catch (\Throwable $e) {}

$_languageSwitchUrl = '';
$_languageSwitchDisplay = '';
if ($_showLanguageSwitch) {
    $_languageSlug = '/' . trim($_languageSlug, " \t\n\r\0\x0B/");
    $_languageSlug = $_languageSlug === '/' ? '' : rtrim($_languageSlug, '/');

    $flagToEmoji = static function (string $countryCode): string {
        $countryCode = strtoupper(preg_replace('/[^A-Z]/i', '', $countryCode) ?? '');
        if (strlen($countryCode) !== 2) {
            return '🌐';
        }

        $emoji = '';
        foreach (str_split($countryCode) as $letter) {
            $emoji .= mb_chr(127397 + ord($letter), 'UTF-8');
        }

        return $emoji !== '' ? $emoji : '🌐';
    };

    $_requestPath = (string) (strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/');
    $_requestQuery = trim((string) ($_SERVER['QUERY_STRING'] ?? ''));
    if ($_languageSlug !== '') {
        if ($_requestPath === $_languageSlug || str_starts_with($_requestPath, $_languageSlug . '/')) {
            $_languageTargetPath = $_requestPath;
        } else {
            $_languageTargetPath = $_languageSlug . ($_requestPath === '/' ? '/' : $_requestPath);
        }

        $_languageSwitchUrl = rtrim($siteUrl, '/') . $_languageTargetPath . ($_requestQuery !== '' ? '?' . $_requestQuery : '');
    }

    $_languageSwitchDisplay = $_languageMode === 'flag'
        ? $flagToEmoji($_languageFlag)
        : ($_languageLabel !== '' ? $_languageLabel : strtoupper(trim($_languageFlag)));
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(\CMS\Hooks::applyFilters('page_title', $siteTitle), ENT_QUOTES, 'UTF-8'); ?></title>
    <script>
    (function () {
        try {
            var storedTheme = localStorage.getItem('cms365-theme');
            if (storedTheme === null) {
                storedTheme = localStorage.getItem('cms-phinit-theme');
                if (storedTheme !== null) {
                    localStorage.setItem('cms365-theme', storedTheme);
                }
            }

            if (storedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
                document.addEventListener('DOMContentLoaded', function () {
                    if (document.body) {
                        document.body.classList.add('dark-mode');
                    }
                }, { once: true });
            }
        } catch (error) {
            console.warn('Dark-Mode konnte vorab nicht initialisiert werden.', error);
        }
    })();
    </script>
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

    <!-- Ebene 1: Member-Bar (nur für eingeloggte User, wenn aktiviert) -->
    <?php if ($_showMemberBar && $isLoggedIn && $currentUser): ?>
    <div class="hdr-bar member-bar">
        <div class="hdr-inner hdr-member">

            <span class="member-bar__greeting">
                👋 Hallo, <strong><?php
                    $displayName = 'User';
                    if (is_object($currentUser)) {
                        $displayName = $currentUser->display_name ?? $currentUser->username ?? 'User';
                    } elseif (is_array($currentUser)) {
                        $displayName = $currentUser['display_name'] ?? $currentUser['username'] ?? 'User';
                    }
                    echo htmlspecialchars($displayName);
                ?></strong>
            </span>

            <nav class="member-bar__nav" aria-label="Member-Navigation">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member" class="member-bar__link">
                    <span class="member-bar__icon">📊</span> Dashboard
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/profile" class="member-bar__link">
                    <span class="member-bar__icon">👤</span> Profil
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/messages" class="member-bar__link">
                    <span class="member-bar__icon">✉️</span> Nachrichten
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/notifications" class="member-bar__link">
                    <span class="member-bar__icon">🔔</span> Benachrichtigungen                    <?php if ($notifCount > 0): ?><span class="notif-badge"><?php echo $notifCount > 99 ? '99+' : $notifCount; ?></span><?php endif; ?>                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/favorites" class="member-bar__link">
                    <span class="member-bar__icon">⭐</span> Favoriten
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security" class="member-bar__link">
                    <span class="member-bar__icon">🔒</span> Sicherheit
                </a>
            </nav>

            <div class="member-bar__actions">
                <?php if ($_showRss): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/feed" class="member-bar__link" aria-label="RSS-Feed abonnieren" title="RSS Feed">
                    <svg class="rss-svg-icon" width="14" height="14" viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><circle cx="2.5" cy="11.5" r="1.5"/><path d="M1 7.5C3.72 7.5 6.07 9.28 6.77 11.5H8.97C8.18 8.17 5.33 5.5 1 5.5V7.5Z"/><path d="M1 3.5C5.97 3.5 10 7.53 10 12.5H12C12 6.43 7.07 1.5 1 1.5V3.5Z"/></svg>
                </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/logout" class="member-bar__link member-bar__logout" title="Abmelden">
                    <span class="member-bar__icon">🚪</span> Logout
                </a>
            </div>

        </div>
    </div>
    <?php endif; ?>

    <!-- Ebene 2: Logo + Hauptnavigation + Tools -->
    <div class="hdr-bar hdr-bar-main">
        <div class="hdr-inner">

            <!-- Logo (jetzt in Bar 2, immer sichtbar) -->
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>" class="site-logo" aria-label="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?> – Startseite">
                <?php if (!empty($_logoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($_logoUrl, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?>" height="<?php echo $_logoMaxH; ?>" loading="eager">
                    <?php if ($_showLogoText): ?>
                    <span class="logo-text-beside"><?php echo htmlspecialchars($_logoPart1); ?><span class="logo-accent"><?php echo htmlspecialchars($_logoPart2); ?></span><?php echo htmlspecialchars($_logoSuffix); ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="logo-icon" aria-hidden="true"><?php echo htmlspecialchars(mb_substr($_logoPart1, 0, 1)); ?></span>
                    <span class="logo-text"><?php echo htmlspecialchars($_logoPart1); ?><span class="logo-accent"><?php echo htmlspecialchars($_logoPart2); ?></span><span class="logo-suffix"><?php echo htmlspecialchars($_logoSuffix); ?></span></span>
                <?php endif; ?>
            </a>

            <?php
            // Exakter Active-Nav-Abgleich: "/" nur auf Startseite, andere URLs prefix-basiert
            $_navUri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
            $navIsActive = static function (string $url) use ($_navUri): bool {
                if ($url === '' || $url === '#') { return false; }
                if ($url === '/') { return $_navUri === '/'; }
                return $_navUri === $url || str_starts_with($_navUri, rtrim($url, '/') . '/');
            };
            $navItemHasActiveBranch = static function (array $item) use ($navIsActive): bool {
                if ($navIsActive((string) ($item['url'] ?? ''))) {
                    return true;
                }

                foreach (($item['children'] ?? []) as $child) {
                    if (is_array($child) && $navIsActive((string) ($child['url'] ?? ''))) {
                        return true;
                    }
                }

                return false;
            };
            ?>
            <nav class="main-nav" aria-label="Hauptnavigation">
                    <?php if (!empty($mainMenuItems)): ?>
                        <?php foreach ($mainMenuItems as $index => $item): ?>
                            <?php
                            $itemUrl = (string) ($item['url'] ?? '#');
                            $itemLabel = (string) ($item['label'] ?? '');
                            $itemChildren = is_array($item['children'] ?? null) ? $item['children'] : [];
                            $itemIsCurrent = $navIsActive($itemUrl);
                            $itemIsActiveBranch = $navItemHasActiveBranch($item);
                            ?>
                            <?php if (!empty($item['children'])): ?>
                            <div class="has-dropdown<?php echo $itemIsActiveBranch ? ' is-active-branch' : ''; ?>" data-nav-dropdown>
                                <div class="main-nav__item-head">
                                <a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES); ?>"
                                   class="main-nav__link<?php echo $itemIsActiveBranch ? ' active' : ''; ?>"
                                   <?php echo $itemIsCurrent ? ' aria-current="page"' : ''; ?>>
                                    <?php echo htmlspecialchars($itemLabel, ENT_QUOTES); ?>
                                </a>
                                <button type="button"
                                        class="main-nav__toggle<?php echo $itemIsActiveBranch ? ' active' : ''; ?>"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        aria-controls="main-nav-dropdown-<?php echo (int) $index; ?>"
                                        aria-label="Untermenü für <?php echo htmlspecialchars($itemLabel, ENT_QUOTES); ?> öffnen">
                                    <span aria-hidden="true">▾</span>
                                </button>
                                </div>
                                <div class="dropdown" id="main-nav-dropdown-<?php echo (int) $index; ?>">
                                    <?php foreach ($itemChildren as $child): ?>
                                    <a href="<?php echo htmlspecialchars($child['url'] ?? '#', ENT_QUOTES); ?>"
                                       <?php echo $navIsActive($child['url'] ?? '') ? ' class="active" aria-current="page"' : ''; ?>><?php echo htmlspecialchars($child['label'] ?? '', ENT_QUOTES); ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <a href="<?php echo htmlspecialchars($itemUrl, ENT_QUOTES); ?>"
                               class="main-nav__link<?php echo $itemIsCurrent ? ' active' : ''; ?>"
                               <?php echo $itemIsCurrent ? ' aria-current="page"' : ''; ?>>
                                <?php echo htmlspecialchars($itemLabel, ENT_QUOTES); ?>
                            </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback-Menü -->
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="main-nav__link">Startseite</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/linux" class="main-nav__link">Linux / BASH</a>
                        <div class="has-dropdown" data-nav-dropdown>
                            <div class="main-nav__item-head">
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell" class="main-nav__link">PowerShell</a>
                            <button type="button"
                                    class="main-nav__toggle"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                    aria-controls="main-nav-dropdown-fallback-powershell"
                                    aria-label="Untermenü für PowerShell öffnen">
                                <span aria-hidden="true">▾</span>
                            </button>
                            </div>
                            <div class="dropdown" id="main-nav-dropdown-fallback-powershell">
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell/grundlagen">Grundlagen</a>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/powershell/glossar">Glossar</a>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/microsoft-365" class="main-nav__link">Microsoft 365</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/datenschutz" class="main-nav__link">Datenschutz</a>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/news" class="main-nav__link">News</a>
                    <?php endif; ?>
                </nav>

            <!-- Header-Tools (rechts, in Bar 2) -->
            <div class="hdr-tools">
                <?php if ($_showDarkMode): ?>
                <button class="util-link util-dark-toggle" aria-label="Dark Mode umschalten" aria-pressed="false" title="Dark Mode">🌙</button>
                <?php endif; ?>

                <?php if ($_showLanguageSwitch && $_languageSwitchUrl !== '' && $_languageSwitchDisplay !== ''): ?>
                <a href="<?php echo htmlspecialchars($_languageSwitchUrl, ENT_QUOTES); ?>"
                   class="util-link util-language-switch util-language-switch--<?php echo $_languageMode === 'flag' ? 'flag' : 'text'; ?>"
                   aria-label="<?php echo htmlspecialchars($_languageAriaLabel, ENT_QUOTES); ?>"
                   title="<?php echo htmlspecialchars($_languageAriaLabel, ENT_QUOTES); ?>">
                    <span class="util-language-switch__value" aria-hidden="true"><?php echo htmlspecialchars($_languageSwitchDisplay, ENT_QUOTES); ?></span>
                </a>
                <?php endif; ?>

                <?php if ($_showSearch): ?>
                <form class="hdr-search" role="search" method="GET" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/search">
                    <input type="search" name="q" placeholder="<?php echo htmlspecialchars($_searchPH, ENT_QUOTES); ?>" aria-label="Suchbegriff eingeben">
                    <button type="submit" aria-label="Suche starten">🔍</button>
                </form>
                <?php endif; ?>

                <?php if ($isLoggedIn && $currentUser): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/dashboard" class="util-link" title="Mein Konto">👤</a>
                <?php else: ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/login" class="btn btn-sm btn-outline util-login-link" aria-label="Einloggen">Login</a>
                <?php endif; ?>

                <button class="burger-btn" id="burger-toggle" aria-label="Menü öffnen" aria-expanded="false" aria-controls="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

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
                        <a href="<?php echo htmlspecialchars($child['url'] ?? '#', ENT_QUOTES); ?>" class="mobile-menu__child"><?php echo htmlspecialchars($child['label'] ?? '', ENT_QUOTES); ?></a>
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
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/login" class="mobile-menu__login">🔑 Login</a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($_showLanguageSwitch && $_languageSwitchUrl !== '' && $_languageSwitchDisplay !== ''): ?>
            <a href="<?php echo htmlspecialchars($_languageSwitchUrl, ENT_QUOTES); ?>" class="mobile-menu__lang-link" aria-label="<?php echo htmlspecialchars($_languageAriaLabel, ENT_QUOTES); ?>">
                <span class="mobile-menu__lang-icon" aria-hidden="true"><?php echo htmlspecialchars($_languageSwitchDisplay, ENT_QUOTES); ?></span>
                <span>Sprache wechseln</span>
            </a>
            <?php endif; ?>
        </nav>

    <!-- Ebene 3: Quicklinks -->
    <?php if ($_showQuicklinks): ?>
    <div class="quicklinks-bar">
        <div class="hdr-inner hdr-sub">
                    <nav class="sub-nav" aria-label="Quicklinks">
                        <?php if (!empty($quicklinkItems)): ?>
                            <?php foreach ($quicklinkItems as $ql): ?>
                            <a href="<?php echo htmlspecialchars($ql['url'] ?? '#', ENT_QUOTES); ?>"><?php echo htmlspecialchars($ql['label'] ?? '', ENT_QUOTES); ?></a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/entra-id">Entra ID</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/intune">Intune</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/compliance">Compliance</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/graph-api">Graph API</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/powershell">PowerShell</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/security">Security</a>
                            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/kategorie/exchange">Exchange</a>
                        <?php endif; ?>
                    </nav>
        </div>
    </div>
    <?php endif; /* $_showQuicklinks */ ?>
</header>
<?php \CMS\Hooks::doAction('after_header'); ?>
<div class="page-wrap">
<main id="main-content">
