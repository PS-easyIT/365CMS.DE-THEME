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

$_requestPath = phinit_current_request_path();
$_requestQuery = phinit_current_request_query();
$_currentLocale = 'de';
$_requestContext = ['base_uri' => $_requestPath, 'locale' => 'de', 'is_localized' => false];
$_contentLocalization = null;

try {
    $_contentLocalization = \CMS\Services\ContentLocalizationService::getInstance();
    $_requestContext = $_contentLocalization->resolveRequestContext($_requestPath);
    $_currentLocale = (string) ($_requestContext['locale'] ?? 'de');
} catch (\Throwable $e) {
}

$_localizedPath = static function (string $path, ?string $locale = null) use ($_contentLocalization, $_currentLocale): string {
    if (function_exists('phinit_localized_path')) {
        return phinit_localized_path($path, $locale ?? $_currentLocale);
    }

    if ($_contentLocalization === null) {
        return $path;
    }

    return $_contentLocalization->buildLocalizedPath($path, $locale ?? $_currentLocale);
};

$_localizedHref = static function (string $url, ?string $locale = null) use ($_contentLocalization, $_currentLocale, $siteUrl): string {
    if (function_exists('phinit_localized_href')) {
        return phinit_localized_href($url, $locale ?? $_currentLocale, (string) $siteUrl);
    }

    $locale = $locale ?? $_currentLocale;
    $trimmedUrl = trim($url);
    if ($trimmedUrl === '' || $trimmedUrl === '#') {
        return $trimmedUrl;
    }

    if ($_contentLocalization === null) {
        return $trimmedUrl;
    }

    $siteUrlBase = rtrim((string) $siteUrl, '/');
    if (preg_match('#^https?://#i', $trimmedUrl) === 1) {
        if (!str_starts_with($trimmedUrl, $siteUrlBase)) {
            return $trimmedUrl;
        }

        $path = (string) (parse_url($trimmedUrl, PHP_URL_PATH) ?? '/');
        $query = (string) (parse_url($trimmedUrl, PHP_URL_QUERY) ?? '');

        return $siteUrlBase . $_contentLocalization->buildLocalizedPath($path, $locale) . ($query !== '' ? '?' . $query : '');
    }

    if (!str_starts_with($trimmedUrl, '/')) {
        return $trimmedUrl;
    }

    return $siteUrlBase . $_contentLocalization->buildLocalizedPath($trimmedUrl, $locale);
};

$_localizedCurrentHomeUrl = rtrim($siteUrl, '/') . $_localizedPath('/', $_currentLocale);
$_baseRequestUri = (string) ($_requestContext['base_uri'] ?? $_requestPath);
$_isHomePage = $_baseRequestUri === '/' || $_baseRequestUri === '';
$_siteTitleTag = $_isHomePage ? 'h1' : 'span';
$_themeInitInlineScript = '';

if (defined('CMS_PHINIT_THEME_DIR') && defined('CMS_PHINIT_THEME_URL')) {
    $_themeInitScriptFile = CMS_PHINIT_THEME_DIR . 'assets/js/theme-init.js';
    if (is_file($_themeInitScriptFile)) {
        $_themeInitInlineScript = trim((string) file_get_contents($_themeInitScriptFile));
        if ($_themeInitInlineScript !== '') {
            $_themeInitInlineScript = str_replace('</script', '<\\/script', $_themeInitInlineScript);
        }
    }
}

if (isset($page) && (is_array($page) || is_object($page))) {
    $GLOBALS['page'] = is_object($page) ? (array) $page : $page;
} else {
    unset($GLOBALS['page']);
}

if (isset($post) && (is_array($post) || is_object($post))) {
    $GLOBALS['post'] = is_object($post) ? (array) $post : $post;
} else {
    unset($GLOBALS['post']);
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($_currentLocale, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(\CMS\Hooks::applyFilters('page_title', $siteTitle), ENT_QUOTES, 'UTF-8'); ?></title>
    <?php if ($_themeInitInlineScript !== ''): ?>
    <script id="cms-phinit-theme-init"><?php echo $_themeInitInlineScript; ?></script>
    <?php endif; ?>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<?php
$isLoggedIn   = function_exists('theme_is_logged_in') ? theme_is_logged_in() : false;
$currentUser  = null;
$isAdminUser  = false;
$memberEditLink = ['show' => false, 'url' => '', 'label' => ''];
$accountPath = $isLoggedIn && function_exists('theme_account_path')
    ? theme_account_path()
    : '/member/dashboard';

try {
    $auth = \CMS\Auth::instance();
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
        $isAdminUser = $auth->isAdmin();
    }
} catch (\Throwable $e) {
    // Auth nicht verfügbar – kein Fehler ausgeben
}

if ($isLoggedIn && $currentUser !== null && $isAdminUser && function_exists('phinit_get_member_edit_link')) {
    $memberEditLink = phinit_get_member_edit_link((string) ($_requestContext['base_uri'] ?? $_requestPath), $_currentLocale);
}

$notifCount = 0;
if ($isLoggedIn && $currentUser !== null) {
    $_userId = is_object($currentUser) ? (int)($currentUser->id ?? 0) : (int)($currentUser['id'] ?? 0);
    if ($_userId > 0) {
        $notifCount = phinit_get_unread_notification_count($_userId);
    }
}

try {
    $customizerInstance = \CMS\Services\ThemeCustomizer::instance();
    $customizer = new class($customizerInstance, $_currentLocale) {
        public function __construct(
            private readonly object $inner,
            private readonly string $locale
        ) {
        }

        public function get(string $category, string $key, mixed $default = ''): mixed
        {
            return phinit_customizer_value($category, $key, $default, $this->locale);
        }

        public function __call(string $method, array $arguments): mixed
        {
            return $this->inner->{$method}(...$arguments);
        }
    };

    $_logoUrl       = $customizer->get('header', 'logo_url', '');
    $_logoPart1     = $customizer->get('header', 'logo_text_part1', 'PHIN');
    $_logoPart2     = $customizer->get('header', 'logo_text_part2', 'IT');
    $_logoSuffix    = $customizer->get('header', 'logo_text_suffix', '.DE');
    $_showLogoText  = filter_var($customizer->get('header', 'show_logo_text_with_image', false), FILTER_VALIDATE_BOOLEAN);
    $_logoMaxH      = (int)$customizer->get('header', 'logo_max_height', 28);

    $_showSearch    = filter_var($customizer->get('header', 'show_search_bar', true), FILTER_VALIDATE_BOOLEAN);
    $_searchPH      = $customizer->get('header', 'search_placeholder', 'Suchen …');
    $_showDarkMode  = filter_var($customizer->get('layout', 'enable_dark_mode_toggle', true), FILTER_VALIDATE_BOOLEAN);
    $_showRss       = filter_var($customizer->get('header', 'show_rss_link', true), FILTER_VALIDATE_BOOLEAN);
    $_showLoginButton = filter_var($customizer->get('header', 'show_login_button', true), FILTER_VALIDATE_BOOLEAN);
    $_showMemberBar = filter_var($customizer->get('header', 'show_member_bar', true), FILTER_VALIDATE_BOOLEAN);
    $_showQuicklinks = filter_var($customizer->get('header', 'show_quicklinks', true), FILTER_VALIDATE_BOOLEAN);
    $_showLanguageSwitch = filter_var($customizer->get('header', 'show_language_switcher', false), FILTER_VALIDATE_BOOLEAN);
    $_languageMode = (string) $customizer->get('header', 'language_switcher_mode', 'text');
    $_languageLabel = trim((string) $customizer->get('header', 'language_switcher_label', 'EN'));
    $_languageSlug = trim((string) $customizer->get('header', 'language_switcher_slug', '/en'));
    $_languageFlag = strtolower(trim((string) $customizer->get('header', 'language_switcher_flag', 'gb')));
    $_languageAriaLabel = trim((string) $customizer->get('header', 'language_switcher_aria_label', 'Zur englischen Version wechseln'));

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
    $_showRss = true; $_showLoginButton = true; $_showMemberBar = true; $_showQuicklinks = true;
    $_showLanguageSwitch = false; $_languageMode = 'text'; $_languageLabel = 'EN'; $_languageSlug = '/en'; $_languageFlag = 'gb'; $_languageAriaLabel = 'Zur englischen Version wechseln';
    $_enableStickyHeader = true; $_enableProgressBar = true;
    $_enableBackToTop = true; $_enableScrollAnimations = true;
    $_showBreadcrumb = true; $_bcOnPosts = true; $_bcOnPages = true;
}

$_headerSearchPlaceholder = trim((string) ($_searchPH ?? ''));
if ($_headerSearchPlaceholder === '' || $_headerSearchPlaceholder === 'Suchen …' || $_headerSearchPlaceholder === 'Suchen…') {
    $_headerSearchPlaceholder = phinit_t('search_posts_placeholder', [], $_currentLocale);
}

$mainMenuItems = function_exists('phinit_get_menu_for_locale')
    ? phinit_get_menu_for_locale('primary', $_currentLocale)
    : [];

$quicklinkItems = function_exists('phinit_get_menu_for_locale')
    ? phinit_get_menu_for_locale('quicklinks', $_currentLocale)
    : [];

$_languageSwitchUrl = '';
$_languageSwitchDisplay = '';
$_languageSwitchHeaderDisplay = '';
if ($_showLanguageSwitch) {
    $normalizeLanguageLabel = static function (string $label, string $targetLocale): string {
        $normalized = strtoupper(trim($label));
        $targetLocale = strtolower(trim($targetLocale));

        if ($targetLocale === 'en' && in_array($normalized, ['GB', 'UK', 'US'], true)) {
            return 'EN';
        }

        if ($targetLocale === 'de' && in_array($normalized, ['DE', 'GER'], true)) {
            return 'DE';
        }

        return $normalized;
    };

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

    $_switchTargetLocale = $_contentLocalization !== null
        ? $_contentLocalization->getAlternateLocale($_currentLocale)
        : ($_currentLocale === 'de' ? 'en' : 'de');
    $_switchBasePath = (string) ($_requestContext['base_uri'] ?? $_requestPath);
    $_languageSwitchUrl = rtrim($siteUrl, '/') . $_localizedPath($_switchBasePath, $_switchTargetLocale) . ($_requestQuery !== '' ? '?' . $_requestQuery : '');

    if ($_switchTargetLocale === 'de') {
        $_languageFlag = 'de';
        $_languageAriaLabel = 'Zur deutschen Version wechseln';
        $_languageSwitchDisplay = $_languageMode === 'flag'
            ? $flagToEmoji('de')
            : $normalizeLanguageLabel('DE', $_switchTargetLocale);
        $_languageSwitchHeaderDisplay = $flagToEmoji('de');
    } else {
        $_languageSwitchDisplay = $_languageMode === 'flag'
            ? $flagToEmoji($_languageFlag)
            : $normalizeLanguageLabel($_languageLabel !== '' ? $_languageLabel : strtoupper(trim($_switchTargetLocale)), $_switchTargetLocale);
        $_languageSwitchHeaderDisplay = $flagToEmoji($_languageFlag);
    }
}
?>
<body<?php
    $bodyClasses = \CMS\Hooks::applyFilters('body_class', '');
    echo $bodyClasses ? ' class="' . htmlspecialchars($bodyClasses, ENT_QUOTES, 'UTF-8') . '"' : '';
    // Layout-Toggles als data-Attribute für JS
    echo ' data-sticky-header="' . ($_enableStickyHeader ? '1' : '0') . '"';
    echo ' data-progress-bar="' . ($_enableProgressBar ? '1' : '0') . '"';
    echo ' data-back-to-top="' . ($_enableBackToTop ? '1' : '0') . '"';
    echo ' data-scroll-anims="' . ($_enableScrollAnimations ? '1' : '0') . '"';
?>>

<?php if ($_enableProgressBar): ?>
<div id="scroll-progress" aria-hidden="true"></div>
<?php endif; ?>
<a href="#main-content" class="skip-link"><?php echo htmlspecialchars(phinit_t('skip_to_content', [], $_currentLocale), ENT_QUOTES); ?></a>

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
                    echo htmlspecialchars((string) $displayName, ENT_QUOTES, 'UTF-8');
                ?></strong>
            </span>

            <nav class="member-bar__nav" aria-label="<?php echo htmlspecialchars(phinit_t('member_navigation', [], $_currentLocale), ENT_QUOTES); ?>">
                <?php if (!empty($memberEditLink['show'])): ?>
                <a href="<?php echo htmlspecialchars((string) ($memberEditLink['url'] ?? '#'), ENT_QUOTES); ?>" class="member-bar__link member-bar__link--edit">
                    <span class="member-bar__icon">✏️</span> <?php echo htmlspecialchars((string) ($memberEditLink['label'] ?? phinit_t('edit', [], $_currentLocale)), ENT_QUOTES); ?>
                </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars(phinit_localized_href($accountPath, $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link">
                    <span class="member-bar__icon">📊</span> <?php echo htmlspecialchars(phinit_t('dashboard', [], $_currentLocale), ENT_QUOTES); ?>
                </a>
                <a href="<?php echo htmlspecialchars(phinit_localized_href('/member/profile', $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link">
                    <span class="member-bar__icon">👤</span> <?php echo htmlspecialchars(phinit_t('profile', [], $_currentLocale), ENT_QUOTES); ?>
                </a>
                <a href="<?php echo htmlspecialchars(phinit_localized_href('/member/notifications', $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link">
                    <span class="member-bar__icon">🔔</span> <?php echo htmlspecialchars(phinit_t('notifications', [], $_currentLocale), ENT_QUOTES); ?>                    <?php if ($notifCount > 0): ?><span class="notif-badge"><?php echo $notifCount > 99 ? '99+' : $notifCount; ?></span><?php endif; ?>                </a>
                <a href="<?php echo htmlspecialchars(phinit_localized_href('/member/favorites', $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link">
                    <span class="member-bar__icon">⭐</span> <?php echo htmlspecialchars(phinit_t('favorites', [], $_currentLocale), ENT_QUOTES); ?>
                </a>
                <a href="<?php echo htmlspecialchars(phinit_localized_href('/member/security', $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link">
                    <span class="member-bar__icon">🔒</span> <?php echo htmlspecialchars(phinit_t('security', [], $_currentLocale), ENT_QUOTES); ?>
                </a>
            </nav>

            <div class="member-bar__actions">
                <?php if ($_showRss): ?>
                <a href="<?php echo htmlspecialchars(phinit_localized_href('/feed', $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link" aria-label="<?php echo htmlspecialchars(phinit_t('rss_subscribe', [], $_currentLocale), ENT_QUOTES); ?>" title="RSS Feed">
                    <svg class="rss-svg-icon" width="14" height="14" viewBox="0 0 14 14" fill="currentColor" aria-hidden="true"><circle cx="2.5" cy="11.5" r="1.5"/><path d="M1 7.5C3.72 7.5 6.07 9.28 6.77 11.5H8.97C8.18 8.17 5.33 5.5 1 5.5V7.5Z"/><path d="M1 3.5C5.97 3.5 10 7.53 10 12.5H12C12 6.43 7.07 1.5 1 1.5V3.5Z"/></svg>
                </a>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars(phinit_localized_href('/logout', $_currentLocale, $siteUrl), ENT_QUOTES); ?>" class="member-bar__link member-bar__logout" title="<?php echo htmlspecialchars(phinit_t('logout_title', [], $_currentLocale), ENT_QUOTES); ?>">
                    <span class="member-bar__icon">🚪</span> <?php echo htmlspecialchars(phinit_t('logout', [], $_currentLocale), ENT_QUOTES); ?>
                </a>
            </div>

        </div>
    </div>
    <?php endif; ?>

    <!-- Ebene 2: Logo + Header-Tools -->
    <div class="hdr-bar hdr-bar-main">
        <div class="hdr-inner">

            <!-- Logo (jetzt in Bar 2, immer sichtbar) -->
            <a href="<?php echo htmlspecialchars($_localizedCurrentHomeUrl, ENT_QUOTES); ?>" class="site-logo" aria-label="<?php echo htmlspecialchars(phinit_t('site_home_aria', ['site' => $siteTitle], $_currentLocale), ENT_QUOTES); ?>">
                <?php if (!empty($_logoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($_logoUrl, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?>" <?php echo phinit_image_loading_attributes(true); ?> <?php echo phinit_image_dimension_attributes($_logoUrl); ?>>
                    <?php if ($_showLogoText): ?>
                    <div class="site-logo__copy">
                        <<?php echo $_siteTitleTag; ?> class="logo-text logo-text-beside"><?php echo htmlspecialchars((string) $_logoPart1, ENT_QUOTES, 'UTF-8'); ?><span class="logo-accent"><?php echo htmlspecialchars((string) $_logoPart2, ENT_QUOTES, 'UTF-8'); ?></span><span class="logo-suffix"><?php echo htmlspecialchars((string) $_logoSuffix, ENT_QUOTES, 'UTF-8'); ?></span></<?php echo $_siteTitleTag; ?>>
                        <span class="logo-tagline">IT-Blog für Microsoft 365, Exchange &amp; PowerShell</span>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="site-logo__copy">
                        <<?php echo $_siteTitleTag; ?> class="logo-text"><?php echo htmlspecialchars((string) $_logoPart1, ENT_QUOTES, 'UTF-8'); ?><span class="logo-accent"><?php echo htmlspecialchars((string) $_logoPart2, ENT_QUOTES, 'UTF-8'); ?></span><span class="logo-suffix"><?php echo htmlspecialchars((string) $_logoSuffix, ENT_QUOTES, 'UTF-8'); ?></span></<?php echo $_siteTitleTag; ?>>
                        <span class="logo-tagline">IT-Blog für Microsoft 365, Exchange &amp; PowerShell</span>
                    </div>
                <?php endif; ?>
            </a>

            <?php
            // Exakter Active-Nav-Abgleich: "/" nur auf Startseite, andere URLs prefix-basiert
            $_navNormalizePath = static function (string $url, bool $localize = false) use ($_localizedPath, $_currentLocale, $siteUrl): string {
                $candidate = trim($url);
                if ($candidate === '' || $candidate === '#') {
                    return '';
                }

                if (preg_match('#^https?://#i', $candidate) === 1) {
                    $siteBase = rtrim((string) $siteUrl, '/');
                    if (!str_starts_with($candidate, $siteBase)) {
                        return '';
                    }

                    $candidate = (string) (parse_url($candidate, PHP_URL_PATH) ?? '/');
                } elseif (str_starts_with($candidate, '/')) {
                    $candidate = (string) (parse_url($candidate, PHP_URL_PATH) ?? $candidate);
                } else {
                    return '';
                }

                if ($localize) {
                    $candidate = $_localizedPath($candidate, $_currentLocale);
                }

                $candidate = '/' . trim($candidate, '/');

                return $candidate === '/' ? '/' : rtrim($candidate, '/');
            };
            $_navUri = $_navNormalizePath($_requestPath);
            $navIsActive = static function (string $url) use ($_navUri, $_navNormalizePath): bool {
                $candidate = $_navNormalizePath($url, true);
                if ($candidate === '' || $_navUri === '') {
                    return false;
                }

                if ($candidate === '/') {
                    return $_navUri === '/';
                }

                return $_navUri === $candidate || str_starts_with($_navUri, $candidate . '/');
            };
            $navItemHasActiveBranch = static function (array $item) use ($navIsActive, &$navItemHasActiveBranch): bool {
                if ($navIsActive((string) ($item['url'] ?? ''))) {
                    return true;
                }

                foreach (($item['children'] ?? []) as $child) {
                    if (is_array($child) && $navItemHasActiveBranch($child)) {
                        return true;
                    }
                }

                return false;
            };
            $normalizeNavLabel = static function (mixed $label): string {
                $decodedLabel = html_entity_decode(trim((string) $label), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                return trim($decodedLabel);
            };
            $dropdownIndex = 0;
            $renderDesktopMenuItems = static function (array $items, int $depth = 0) use (&$renderDesktopMenuItems, $navIsActive, $navItemHasActiveBranch, $normalizeNavLabel, $_localizedHref, $_currentLocale, &$dropdownIndex): void {
                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $itemUrl = (string) ($item['url'] ?? '#');
                    $itemHref = $_localizedHref($itemUrl);
                    $itemLabel = $normalizeNavLabel($item['label'] ?? '');
                    $itemChildren = is_array($item['children'] ?? null) ? $item['children'] : [];
                    $itemIsCurrent = $navIsActive($itemUrl);
                    $itemIsActiveBranch = $navItemHasActiveBranch($item);
                    $linkClass = 'main-nav__link' . ($itemIsActiveBranch ? ' active' : '') . ($depth > 0 ? ' main-nav__link--submenu' : '');
                    $target = ((string) ($item['target'] ?? '_self')) === '_blank' ? '_blank' : '_self';
                    $targetAttributes = $target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';

                    if ($itemLabel === '') {
                        continue;
                    }

                    if ($itemChildren !== []) {
                        $dropdownIndex++;
                        ?>
                        <div class="has-dropdown<?php echo $depth > 0 ? ' has-dropdown--nested' : ''; ?><?php echo $itemIsActiveBranch ? ' is-active-branch' : ''; ?>" data-nav-dropdown data-nav-depth="<?php echo (int) $depth; ?>">
                            <div class="main-nav__item-head">
                                <a href="<?php echo htmlspecialchars($itemHref, ENT_QUOTES); ?>"
                                   class="<?php echo htmlspecialchars($linkClass, ENT_QUOTES); ?>"
                                   <?php echo $itemIsCurrent ? ' aria-current="page"' : ''; ?><?php echo $targetAttributes; ?>>
                                    <?php echo htmlspecialchars($itemLabel, ENT_QUOTES); ?>
                                </a>
                                <button type="button"
                                        class="main-nav__toggle<?php echo $itemIsActiveBranch ? ' active' : ''; ?><?php echo $depth > 0 ? ' main-nav__toggle--submenu' : ''; ?>"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        aria-controls="main-nav-dropdown-<?php echo (int) $dropdownIndex; ?>"
                                        aria-label="<?php echo htmlspecialchars(phinit_t('submenu_open_for', ['label' => $itemLabel], $_currentLocale), ENT_QUOTES); ?>">
                                    <span aria-hidden="true"><?php echo $depth > 0 ? '▸' : '▾'; ?></span>
                                </button>
                            </div>
                            <div class="dropdown dropdown--level-<?php echo (int) ($depth + 1); ?>" id="main-nav-dropdown-<?php echo (int) $dropdownIndex; ?>">
                                <?php $renderDesktopMenuItems($itemChildren, $depth + 1); ?>
                            </div>
                        </div>
                        <?php
                        continue;
                    }
                    ?>
                    <a href="<?php echo htmlspecialchars($itemHref, ENT_QUOTES); ?>"
                       class="<?php echo htmlspecialchars($linkClass, ENT_QUOTES); ?>"
                       <?php echo $itemIsCurrent ? ' aria-current="page"' : ''; ?><?php echo $targetAttributes; ?>>
                        <?php echo htmlspecialchars($itemLabel, ENT_QUOTES); ?>
                    </a>
                    <?php
                }
            };
            $renderMobileMenuItems = static function (array $items, int $depth = 0) use (&$renderMobileMenuItems, $normalizeNavLabel, $_localizedHref): void {
                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $itemLabel = $normalizeNavLabel($item['label'] ?? '');
                    if ($itemLabel === '') {
                        continue;
                    }

                    $target = ((string) ($item['target'] ?? '_self')) === '_blank' ? '_blank' : '_self';
                    $targetAttributes = $target === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '';
                    $depthClass = $depth > 0 ? ' mobile-menu__child mobile-menu__child--depth-' . min($depth, 4) : '';
                    ?>
                    <a href="<?php echo htmlspecialchars($_localizedHref((string) ($item['url'] ?? '#')), ENT_QUOTES); ?>" class="<?php echo htmlspecialchars(trim($depthClass), ENT_QUOTES); ?>"<?php echo $targetAttributes; ?>><?php echo htmlspecialchars($itemLabel, ENT_QUOTES); ?></a>
                    <?php

                    $children = is_array($item['children'] ?? null) ? $item['children'] : [];
                    if ($children !== []) {
                        $renderMobileMenuItems($children, $depth + 1);
                    }
                }
            };
            ?>
            <!-- Header-Tools (rechts, in Bar 2) -->
            <div class="hdr-tools">
                <?php if ($_showDarkMode || ($_showLanguageSwitch && $_languageSwitchUrl !== '' && $_languageSwitchDisplay !== '')): ?>
                <div class="hdr-tools__toggles" aria-label="<?php echo htmlspecialchars(phinit_t('header_controls', [], $_currentLocale), ENT_QUOTES); ?>">
                    <?php if ($_showDarkMode): ?>
                    <button type="button" class="util-link util-dark-toggle" data-dark-toggle aria-label="<?php echo htmlspecialchars(phinit_t('darkmode_toggle', [], $_currentLocale), ENT_QUOTES); ?>" aria-pressed="false" title="<?php echo htmlspecialchars(phinit_t('darkmode_toggle', [], $_currentLocale), ENT_QUOTES); ?>"><span data-dark-toggle-icon aria-hidden="true">☾</span></button>
                    <?php endif; ?>

                    <?php if ($_showLanguageSwitch && $_languageSwitchUrl !== '' && $_languageSwitchHeaderDisplay !== ''): ?>
                    <a href="<?php echo htmlspecialchars($_languageSwitchUrl, ENT_QUOTES); ?>"
                       class="util-link util-language-switch util-language-switch--flag"
                       aria-label="<?php echo htmlspecialchars($_languageAriaLabel, ENT_QUOTES); ?>"
                       title="<?php echo htmlspecialchars($_languageAriaLabel, ENT_QUOTES); ?>">
                        <span class="util-language-switch__value" aria-hidden="true"><?php echo htmlspecialchars($_languageSwitchHeaderDisplay, ENT_QUOTES); ?></span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ($_showSearch): ?>
                <form class="hdr-search" role="search" method="GET" action="<?php echo htmlspecialchars(rtrim($siteUrl, '/') . $_localizedPath('/search', $_currentLocale), ENT_QUOTES); ?>">
                    <input type="search" name="q" placeholder="<?php echo htmlspecialchars($_headerSearchPlaceholder, ENT_QUOTES); ?>" aria-label="<?php echo htmlspecialchars(phinit_t('search_term_input', [], $_currentLocale), ENT_QUOTES); ?>">
                    <button type="submit" aria-label="<?php echo htmlspecialchars(phinit_t('search_start', [], $_currentLocale), ENT_QUOTES); ?>">🔍</button>
                </form>
                <?php endif; ?>

                <?php if ($_showLoginButton): ?>
                <a href="<?php echo htmlspecialchars($isLoggedIn ? rtrim($siteUrl, '/') . $accountPath : theme_login_url(null, $_currentLocale), ENT_QUOTES); ?>"
                   class="util-link util-login-link"
                   aria-label="<?php echo htmlspecialchars($isLoggedIn ? phinit_t('account', [], $_currentLocale) : phinit_t('login', [], $_currentLocale), ENT_QUOTES); ?>"
                   title="<?php echo htmlspecialchars($isLoggedIn ? phinit_t('account', [], $_currentLocale) : phinit_t('login', [], $_currentLocale), ENT_QUOTES); ?>">
                    <span aria-hidden="true"><?php echo $isLoggedIn ? '👤' : '🔑'; ?></span>
                </a>
                <?php endif; ?>

                <button class="burger-btn" id="burger-toggle" aria-label="<?php echo htmlspecialchars(phinit_t('menu_open', [], $_currentLocale), ENT_QUOTES); ?>" aria-expanded="false" aria-controls="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            </div>
        </div>

        <!-- Ebene 3: Hauptmenü-Band -->
        <div class="hdr-bar main-menu-bar">
            <div class="hdr-inner hdr-main-menu">
                <nav class="main-nav" aria-label="<?php echo htmlspecialchars(phinit_t('main_navigation', [], $_currentLocale), ENT_QUOTES); ?>">
                    <?php if (!empty($mainMenuItems)): ?>
                        <?php $renderDesktopMenuItems($mainMenuItems); ?>
                    <?php else: ?>
                        <!-- Fallback-Menü -->
                        <a href="<?php echo htmlspecialchars($_localizedHref('/', $_currentLocale), ENT_QUOTES); ?>" class="main-nav__link<?php echo $navIsActive('/') ? ' active' : ''; ?>"<?php echo $navIsActive('/') ? ' aria-current="page"' : ''; ?>><?php echo htmlspecialchars(phinit_t('home', [], $_currentLocale), ENT_QUOTES); ?></a>
                        <a href="<?php echo htmlspecialchars($_localizedHref('/linux', $_currentLocale), ENT_QUOTES); ?>" class="main-nav__link<?php echo $navIsActive('/linux') ? ' active' : ''; ?>"<?php echo $navIsActive('/linux') ? ' aria-current="page"' : ''; ?>>Linux / BASH</a>
                        <div class="has-dropdown<?php echo $navIsActive('/powershell') ? ' is-active-branch' : ''; ?>" data-nav-dropdown>
                            <div class="main-nav__item-head">
                                <a href="<?php echo htmlspecialchars($_localizedHref('/powershell', $_currentLocale), ENT_QUOTES); ?>" class="main-nav__link<?php echo $navIsActive('/powershell') ? ' active' : ''; ?>"<?php echo $navIsActive('/powershell') ? ' aria-current="page"' : ''; ?>>PowerShell</a>
                                <button type="button"
                                        class="main-nav__toggle<?php echo $navIsActive('/powershell') ? ' active' : ''; ?>"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        aria-controls="main-nav-dropdown-fallback-powershell"
                                        aria-label="<?php echo htmlspecialchars(phinit_t('submenu_open_for', ['label' => 'PowerShell'], $_currentLocale), ENT_QUOTES); ?>">
                                    <span aria-hidden="true">▾</span>
                                </button>
                            </div>
                            <div class="dropdown" id="main-nav-dropdown-fallback-powershell">
                                <a href="<?php echo htmlspecialchars($_localizedHref('/powershell/grundlagen', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Grundlagen', $_currentLocale), ENT_QUOTES); ?></a>
                                <a href="<?php echo htmlspecialchars($_localizedHref('/powershell/glossar', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Glossar', $_currentLocale), ENT_QUOTES); ?></a>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($_localizedHref('/microsoft-365', $_currentLocale), ENT_QUOTES); ?>" class="main-nav__link<?php echo $navIsActive('/microsoft-365') ? ' active' : ''; ?>"<?php echo $navIsActive('/microsoft-365') ? ' aria-current="page"' : ''; ?>>Microsoft 365</a>
                        <a href="<?php echo htmlspecialchars($_localizedHref('/datenschutz', $_currentLocale), ENT_QUOTES); ?>" class="main-nav__link<?php echo $navIsActive('/datenschutz') ? ' active' : ''; ?>"<?php echo $navIsActive('/datenschutz') ? ' aria-current="page"' : ''; ?>><?php echo htmlspecialchars(phinit_localize_menu_label('Datenschutz', $_currentLocale), ENT_QUOTES); ?></a>
                        <a href="<?php echo htmlspecialchars($_localizedHref('/news', $_currentLocale), ENT_QUOTES); ?>" class="main-nav__link<?php echo $navIsActive('/news') ? ' active' : ''; ?>"<?php echo $navIsActive('/news') ? ' aria-current="page"' : ''; ?>>News</a>
                    <?php endif; ?>
                    <?php \CMS\Hooks::doAction('main_nav', 'desktop'); ?>
                </nav>
            </div>
        </div>

        <!-- Mobiles Menü -->
        <nav class="mobile-menu" id="mobile-menu" aria-label="<?php echo htmlspecialchars(phinit_t('mobile_navigation', [], $_currentLocale), ENT_QUOTES); ?>" aria-hidden="true">
            <div class="mob-search">
                <form role="search" method="GET" action="<?php echo htmlspecialchars(rtrim($siteUrl, '/') . $_localizedPath('/search', $_currentLocale), ENT_QUOTES); ?>">
                    <input type="search" name="q" placeholder="<?php echo htmlspecialchars($_headerSearchPlaceholder, ENT_QUOTES); ?>" aria-label="<?php echo htmlspecialchars(phinit_t('mobile_search', [], $_currentLocale), ENT_QUOTES); ?>">
                </form>
            </div>
            <?php if (!empty($mainMenuItems)): ?>
                <?php $renderMobileMenuItems($mainMenuItems); ?>
            <?php else: ?>
                <a href="<?php echo htmlspecialchars($_localizedHref('/', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('home', [], $_currentLocale), ENT_QUOTES); ?></a>
                <a href="<?php echo htmlspecialchars($_localizedHref('/linux', $_currentLocale), ENT_QUOTES); ?>">Linux / BASH</a>
                <a href="<?php echo htmlspecialchars($_localizedHref('/powershell', $_currentLocale), ENT_QUOTES); ?>">PowerShell</a>
                <a href="<?php echo htmlspecialchars($_localizedHref('/microsoft-365', $_currentLocale), ENT_QUOTES); ?>">Microsoft 365</a>
                <a href="<?php echo htmlspecialchars($_localizedHref('/datenschutz', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Datenschutz', $_currentLocale), ENT_QUOTES); ?></a>
                <a href="<?php echo htmlspecialchars($_localizedHref('/news', $_currentLocale), ENT_QUOTES); ?>">News</a>
                <?php if (!$isLoggedIn && $_showLoginButton): ?>
                <a href="<?php echo htmlspecialchars(theme_login_url(null, $_currentLocale), ENT_QUOTES); ?>" class="mobile-menu__login">🔑 <?php echo htmlspecialchars(phinit_t('login', [], $_currentLocale), ENT_QUOTES); ?></a>
                <?php endif; ?>
            <?php endif; ?>
            <?php \CMS\Hooks::doAction('main_nav', 'mobile'); ?>
            <?php if ($_showLanguageSwitch && $_languageSwitchUrl !== '' && $_languageSwitchDisplay !== ''): ?>
            <a href="<?php echo htmlspecialchars($_languageSwitchUrl, ENT_QUOTES); ?>" class="mobile-menu__lang-link" aria-label="<?php echo htmlspecialchars($_languageAriaLabel, ENT_QUOTES); ?>">
                <span class="mobile-menu__lang-icon" aria-hidden="true"><?php echo htmlspecialchars($_languageSwitchDisplay, ENT_QUOTES); ?></span>
                <span><?php echo htmlspecialchars(phinit_t('switch_language', [], $_currentLocale), ENT_QUOTES); ?></span>
            </a>
            <?php endif; ?>
        </nav>

    <!-- Ebene 4: Quicklinks -->
    <?php if ($_showQuicklinks): ?>
    <div class="quicklinks-bar">
        <div class="hdr-inner hdr-sub">
                    <nav class="sub-nav" aria-label="<?php echo htmlspecialchars(phinit_t('quicklinks', [], $_currentLocale), ENT_QUOTES); ?>">
                        <?php if (!empty($quicklinkItems)): ?>
                            <?php foreach ($quicklinkItems as $ql): ?>
                            <a href="<?php echo htmlspecialchars($_localizedHref((string) ($ql['url'] ?? '#')), ENT_QUOTES); ?>"><?php echo htmlspecialchars($normalizeNavLabel($ql['label'] ?? ''), ENT_QUOTES); ?></a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/entra-id', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Entra ID', $_currentLocale), ENT_QUOTES); ?></a>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/intune', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Intune', $_currentLocale), ENT_QUOTES); ?></a>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/compliance', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Compliance', $_currentLocale), ENT_QUOTES); ?></a>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/graph-api', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Graph API', $_currentLocale), ENT_QUOTES); ?></a>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/powershell', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('PowerShell', $_currentLocale), ENT_QUOTES); ?></a>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/security', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Security', $_currentLocale), ENT_QUOTES); ?></a>
                            <a href="<?php echo htmlspecialchars($_localizedHref('/kategorie/exchange', $_currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_localize_menu_label('Exchange', $_currentLocale), ENT_QUOTES); ?></a>
                        <?php endif; ?>
                    </nav>
        </div>
    </div>
    <?php endif; /* $_showQuicklinks */ ?>
</header>
<?php \CMS\Hooks::doAction('after_header'); ?>
<div class="page-wrap">
<main id="main-content">
