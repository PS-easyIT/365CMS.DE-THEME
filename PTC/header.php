<?php
/**
 * PTC Theme – Header
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tm       = \CMS\ThemeManager::instance();
$title    = ptc_site_title();
$siteUrl  = ptc_site_url();

// Auth-Status
$isLoggedIn  = false;
$isAdmin     = false;
$currentUser = null;
try {
    $auth        = \CMS\Auth::instance();
    $isLoggedIn  = $auth->isLoggedIn();
    $isAdmin     = $auth->isAdmin();
    if ($isLoggedIn) {
        $currentUser = $auth->getCurrentUser();
    }
} catch (\Throwable $e) {
    // Auth nicht verfügbar
}

// User-Initialen
$userInitials = 'U';
$displayName  = 'Benutzer';
if ($currentUser) {
    $userName     = is_array($currentUser) ? ($currentUser['username'] ?? '') : ($currentUser->username ?? '');
    $userInitials = mb_strtoupper(mb_substr($userName, 0, 2));
    $displayName  = is_array($currentUser)
        ? ($currentUser['display_name'] ?? $currentUser['username'] ?? 'Benutzer')
        : ($currentUser->display_name ?? $currentUser->username ?? 'Benutzer');
}

// Customizer-Einstellungen
$_logoUrl       = ptc_customizer_get('header', 'logo_url', '');
$_showLoginBtn  = filter_var(ptc_customizer_get('header', 'show_login_btn', false), FILTER_VALIDATE_BOOLEAN);
$_loginIconOnly = filter_var(ptc_customizer_get('header', 'login_btn_icon_only', false), FILTER_VALIDATE_BOOLEAN);
$_showRegBtn    = filter_var(ptc_customizer_get('header', 'show_register_btn', false), FILTER_VALIDATE_BOOLEAN);
$_regIconOnly   = filter_var(ptc_customizer_get('header', 'register_btn_icon_only', false), FILTER_VALIDATE_BOOLEAN);
$_headerCtaText = (string) ptc_customizer_get('header', 'header_cta_text', 'Kontakt');
$_headerCtaUrl  = (string) ptc_customizer_get('header', 'header_cta_url', '/#kontakt');
$_headerAnim    = (string) ptc_customizer_get('header', 'header_animation', 'none');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>

<body>
<?php \CMS\Hooks::doAction('body_start'); ?>
<a class="skip-link" href="#main-content">Zum Inhalt springen</a>

<div class="ptc-site">

    <header class="ptc-header" id="ptc-masthead"<?php echo $_headerAnim !== 'none' ? ' data-header-anim="' . htmlspecialchars($_headerAnim, ENT_QUOTES, 'UTF-8') . '"' : ''; ?>>
        <?php if ($_headerAnim !== 'none'): ?>
            <div class="ptc-header-anim" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="ptc-container">
            <div class="ptc-header-inner">

                <!-- Logo -->
                <a href="<?php echo $siteUrl; ?>/" class="ptc-logo" aria-label="<?php echo $title; ?>">
                    <?php if (!empty($_logoUrl)) : ?>
                        <img src="<?php echo htmlspecialchars($_logoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                             alt="<?php echo $title; ?>" class="ptc-logo-img">
                    <?php else : ?>
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="18" cy="18" r="18" fill="var(--ptc-navy, #002D5D)"/>
                            <path d="M10 12h5v3h-5zM10 17h5v3h-5zM10 22h5v3h-5zM17 12h9v3h-9zM17 17h7v3h-7zM17 22h5v3h-5z" fill="var(--ptc-gold, #D4A017)"/>
                        </svg>
                        <span class="ptc-logo-text"><?php echo $title; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Desktop Navigation -->
                <nav class="ptc-nav" id="ptc-main-nav" aria-label="Hauptnavigation">
                    <?php ptc_nav_menu('primary'); ?>
                </nav>

                <!-- Header Actions -->
                <div class="ptc-header-actions">
                    <?php if ($isLoggedIn): ?>
                        <!-- Profil-Dropdown -->
                        <div class="ptc-profile-dropdown" id="ptcProfileDropdown">
                            <button class="ptc-profile-toggle" type="button"
                                    aria-expanded="false" aria-haspopup="true"
                                    aria-label="Benutzerprofil">
                                <span class="ptc-avatar"><?php echo htmlspecialchars($userInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="ptc-profile-name"><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></span>
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 5l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </button>
                            <div class="ptc-profile-menu" role="menu">
                                <a href="<?php echo $siteUrl; ?>/member" role="menuitem">📊 Dashboard</a>
                                <a href="<?php echo $siteUrl; ?>/member/profile" role="menuitem">👤 Mein Profil</a>
                                <?php if ($isAdmin): ?>
                                    <div class="ptc-profile-divider"></div>
                                    <a href="<?php echo $siteUrl; ?>/admin/" role="menuitem">⚙️ Administration</a>
                                <?php endif; ?>
                                <div class="ptc-profile-divider"></div>
                                <a href="<?php echo $siteUrl; ?>/logout" role="menuitem">🚪 Abmelden</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php if ($_showLoginBtn): ?>
                            <a href="<?php echo $siteUrl; ?>/login"
                               class="btn-ptc btn-ptc-ghost btn-ptc-sm<?php echo $_loginIconOnly ? ' btn-ptc-icon-only' : ''; ?>"
                               <?php echo $_loginIconOnly ? 'aria-label="Anmelden" title="Anmelden"' : ''; ?>>
                                🔑<?php if (!$_loginIconOnly): ?> Anmelden<?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($_showRegBtn): ?>
                            <a href="<?php echo $siteUrl; ?>/register"
                               class="btn-ptc btn-ptc-accent btn-ptc-sm<?php echo $_regIconOnly ? ' btn-ptc-icon-only' : ''; ?>"
                               <?php echo $_regIconOnly ? 'aria-label="Registrieren" title="Registrieren"' : ''; ?>>
                                ✏️<?php if (!$_regIconOnly): ?> Registrieren<?php endif; ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($_headerCtaText !== ''): ?>
                            <a href="<?php echo htmlspecialchars($_headerCtaUrl, ENT_QUOTES, 'UTF-8'); ?>"
                               class="btn-ptc btn-ptc-accent btn-ptc-sm">
                                <?php echo htmlspecialchars($_headerCtaText, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Mobile Toggle -->
                <button class="ptc-mobile-toggle" id="ptcMobileToggle"
                        aria-label="Menü öffnen" aria-expanded="false" aria-controls="ptcMobileDrawer"
                        type="button">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </button>

            </div>
        </div>
    </header>

    <?php \CMS\Hooks::doAction('after_header'); ?>

    <!-- Mobile Overlay & Drawer -->
    <div class="ptc-mobile-overlay" id="ptcMobileOverlay" role="presentation"></div>
    <nav id="ptcMobileDrawer" class="ptc-mobile-drawer" aria-label="Mobile Navigation" aria-hidden="true">
        <?php ptc_nav_menu('primary'); ?>

        <div class="ptc-drawer-actions">
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo $siteUrl; ?>/member" class="btn-ptc btn-ptc-accent ptc-drawer-btn">
                    📊 Dashboard
                </a>
                <a href="<?php echo $siteUrl; ?>/logout" class="btn-ptc btn-ptc-ghost ptc-drawer-btn">
                    🚪 Abmelden
                </a>
            <?php else: ?>
                <?php if ($_showLoginBtn): ?>
                    <a href="<?php echo $siteUrl; ?>/login" class="btn-ptc btn-ptc-ghost ptc-drawer-btn">
                        🔑 Anmelden
                    </a>
                <?php endif; ?>
                <?php if ($_showRegBtn): ?>
                    <a href="<?php echo $siteUrl; ?>/register" class="btn-ptc btn-ptc-accent ptc-drawer-btn">
                        ✏️ Registrieren
                    </a>
                <?php endif; ?>
                <?php if ($_headerCtaText !== ''): ?>
                    <a href="<?php echo htmlspecialchars($_headerCtaUrl, ENT_QUOTES, 'UTF-8'); ?>"
                       class="btn-ptc btn-ptc-accent ptc-drawer-btn">
                        <?php echo htmlspecialchars($_headerCtaText, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </nav>

    <main id="main-content" class="ptc-content">
