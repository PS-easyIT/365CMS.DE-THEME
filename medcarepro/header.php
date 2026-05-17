<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getSiteDescription(), ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getSiteTitle(), ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getThemeUrl('medcarepro'), ENT_QUOTES, 'UTF-8'); ?>/style.css">
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="mc-body <?php echo theme_is_logged_in() ? 'is-logged-in' : ''; ?>">
<div id="page" class="mc-page-wrapper">
<a class="mc-skip-link" href="#main">Zum Inhalt springen</a>
<?php
try {
    $c = \CMS\Services\ThemeCustomizer::instance();
    $logoUrl         = $c->get('header', 'logo_url', '');
    $showSearch      = true;
    $showFontSize    = filter_var($c->get('accessibility', 'enable_font_size_toggle', true), FILTER_VALIDATE_BOOLEAN);
    $showContrast    = filter_var($c->get('accessibility', 'enable_high_contrast_toggle', true), FILTER_VALIDATE_BOOLEAN);
    $showEmergency   = filter_var($c->get('header', 'show_emergency_banner', false), FILTER_VALIDATE_BOOLEAN);
    $emergencyPhone  = $c->get('header', 'emergency_phone', '112');
    $emergencyBanner = $c->get('header', 'emergency_banner_text', 'Notfall? Bitte rufen Sie sofort an:');
} catch (\Throwable $e) {
    $logoUrl = ''; $showSearch = true; $showFontSize = true; $showContrast = true;
    $showEmergency = false; $emergencyPhone = '112'; $emergencyBanner = '';
}
$themeManager = \CMS\ThemeManager::instance();
$siteTitle    = $themeManager->getSiteTitle();
$isLoggedIn   = theme_is_logged_in();
$siteUrl      = SITE_URL;
$safe         = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$homeUrl      = function_exists('theme_route_url') ? theme_route_url('home') : rtrim($siteUrl, '/') . '/';
$searchUrl    = function_exists('theme_route_url') ? theme_route_url('search') : rtrim($siteUrl, '/') . '/search';
$loginUrl     = function_exists('theme_route_url') ? theme_route_url('login') : rtrim($siteUrl, '/') . '/login';
$registerUrl  = function_exists('theme_route_url') ? theme_route_url('register') : rtrim($siteUrl, '/') . '/register';
$memberUrl    = rtrim($siteUrl, '/') . '/member';
$telHref      = preg_replace('/[^0-9+]/', '', (string) $emergencyPhone) ?? '';
?>
<?php if ($showEmergency && !empty($emergencyPhone)) : ?>
<style>:root{--header-height:calc(72px + 2.375rem);}</style>
<div class="mc-emergency-banner" role="alert" aria-live="polite">
    <div class="mc-container mc-emergency-banner-row">
        <span><?php echo $safe($emergencyBanner); ?></span>
        <a href="tel:<?php echo $safe($telHref); ?>" class="mc-emergency-phone"><?php echo $safe($emergencyPhone); ?></a>
    </div>
</div>
<?php endif; ?>
<header id="masthead" class="mc-site-header" role="banner">
    <div class="mc-header-inner">
        <div class="mc-branding">
            <a href="<?php echo $safe($homeUrl); ?>" rel="home">
                <?php if (!empty($logoUrl)) : ?>
                    <img src="<?php echo $safe($logoUrl); ?>" alt="<?php echo $safe($siteTitle); ?>" width="150" height="44">
                <?php else : ?>
                    <span class="mc-logo-text"><span class="mc-logo-cross" aria-hidden="true">✚</span><?php echo $safe($siteTitle); ?></span>
                <?php endif; ?>
            </a>
        </div>
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Hauptnavigation">
            <?php theme_nav_menu('primary-nav'); ?>
        </nav>
        <div class="mc-header-actions">
            <?php if ($showFontSize) : ?>
                <button id="fontSizeToggle" class="mc-btn mc-btn-ghost mc-font-size-toggle" aria-label="Schriftgröße ändern" title="Schriftgröße">A±</button>
            <?php endif; ?>
            <?php if ($showContrast) : ?>
                <button id="contrastToggle" class="mc-btn mc-btn-ghost mc-font-size-toggle" aria-label="Kontrast wechseln" title="Hoher Kontrast">◑</button>
            <?php endif; ?>
            <?php if ($showSearch) : ?>
                <button id="searchToggle" type="button" class="mc-btn mc-btn-ghost mc-search-toggle-btn" aria-label="Suche öffnen" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                </button>
            <?php endif; ?>
            <?php if ($isLoggedIn) : ?>
                <a href="<?php echo $safe($memberUrl); ?>" class="mc-btn mc-btn-primary">Mein Bereich</a>
            <?php else : ?>
                <a href="<?php echo $safe($loginUrl); ?>"    class="mc-btn mc-btn-ghost">Anmelden</a>
                <a href="<?php echo $safe($registerUrl); ?>" class="mc-btn mc-btn-primary">Arzt registrieren</a>
            <?php endif; ?>
            <button id="mobileMenuToggle" type="button" class="mc-mobile-toggle" aria-label="Menü öffnen" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
    <?php if ($showSearch) : ?>
    <div id="searchPanel" class="mc-search-panel" hidden aria-hidden="true">
        <div class="mc-header-inner">
            <form role="search" method="get" action="<?php echo $safe($searchUrl); ?>" class="mc-header-search-form">
                <label for="mc-search" class="mc-visually-hidden">Arzt/Fachgebiet suchen</label>
                <input id="mc-search" type="search" name="q" placeholder="Arzt, Fachgebiet, PLZ suchen …" autocomplete="off" class="mc-header-search-input">
                <button type="submit" class="mc-btn mc-btn-primary" aria-label="Suchen">Suchen</button>
            </form>
            <button id="searchClose" type="button" class="mc-btn mc-btn-ghost" aria-label="Schließen">✕</button>
        </div>
    </div>
    <?php endif; ?>
</header><!-- #masthead -->
<div id="content" class="mc-site-content">
