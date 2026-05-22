<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
?><!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getSiteDescription(), ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getSiteTitle(), ENT_QUOTES, 'UTF-8'); ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="ac-body <?php echo theme_is_logged_in() ? 'is-logged-in' : ''; ?>">
<div id="page" class="ac-page-wrapper">
<a class="ac-skip-link" href="#main">Zum Inhalt springen</a>
<?php
try {
    $c = \CMS\Services\ThemeCustomizer::instance();
    $logoUrl    = $c->get('header', 'logo_url', '');
    $showSearch = filter_var($c->get('header', 'show_search_btn', true), FILTER_VALIDATE_BOOLEAN);
} catch (\Throwable $e) { $logoUrl = ''; $showSearch = true; }
$themeManager = \CMS\ThemeManager::instance();
$siteTitle    = $themeManager->getSiteTitle();
$isLoggedIn   = theme_is_logged_in();
$siteUrl      = SITE_URL;
$safe         = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$homeUrl      = academy365_safe_url(function_exists('theme_route_url') ? theme_route_url('home') : rtrim($siteUrl, '/') . '/', rtrim($siteUrl, '/') . '/');
$searchUrl    = academy365_safe_url(function_exists('theme_route_url') ? theme_route_url('search') : rtrim($siteUrl, '/') . '/search', rtrim($siteUrl, '/') . '/search');
$loginUrl     = academy365_safe_url(function_exists('theme_route_url') ? theme_route_url('login') : rtrim($siteUrl, '/') . '/login', rtrim($siteUrl, '/') . '/login');
$registerUrl  = academy365_safe_url(function_exists('theme_route_url') ? theme_route_url('register') : rtrim($siteUrl, '/') . '/register', rtrim($siteUrl, '/') . '/register');
$memberUrl    = academy365_safe_url(function_exists('theme_route_url') ? theme_route_url('member-dashboard', ['area' => 'member']) : rtrim($siteUrl, '/') . '/member', rtrim($siteUrl, '/') . '/member');
$logoSafeUrl  = academy365_safe_url((string) $logoUrl);
?>
<header id="masthead" class="ac-site-header" role="banner">
    <div class="ac-header-inner">
        <div class="ac-branding">
            <a href="<?php echo $safe($homeUrl); ?>" rel="home">
                <?php if ($logoSafeUrl !== '') : ?>
                    <img src="<?php echo $safe($logoSafeUrl); ?>" alt="<?php echo $safe($siteTitle); ?>" width="150" height="44">
                <?php else : ?>
                    <span class="ac-logo-text"><span aria-hidden="true">🎓</span><?php echo $safe($siteTitle); ?></span>
                <?php endif; ?>
            </a>
        </div>
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Hauptnavigation">
            <?php theme_nav_menu('primary-nav'); ?>
        </nav>
        <div class="ac-header-actions">
            <?php if ($showSearch) : ?>
                <button id="searchToggle" type="button" class="ac-btn ac-btn-ghost ac-search-toggle-btn" aria-label="Suche öffnen" aria-expanded="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                </button>
            <?php endif; ?>
            <?php if ($isLoggedIn) : ?>
                <a href="<?php echo $safe($memberUrl); ?>" class="ac-btn ac-btn-primary">Meine Kurse</a>
            <?php else : ?>
                <a href="<?php echo $safe($loginUrl); ?>"    class="ac-btn ac-btn-ghost">Anmelden</a>
                <a href="<?php echo $safe($registerUrl); ?>" class="ac-btn ac-btn-primary">Kostenlos lernen</a>
            <?php endif; ?>
            <button id="mobileMenuToggle" class="ac-mobile-toggle" type="button" aria-label="Menü öffnen" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
    <?php if ($showSearch) : ?>
    <div id="searchPanel" class="ac-search-panel" hidden aria-hidden="true">
        <div class="ac-header-inner">
            <form role="search" method="get" action="<?php echo $safe($searchUrl); ?>" class="ac-search-form">
                <label for="ac-search" class="ac-visually-hidden">Kurse suchen</label>
                <input id="ac-search" type="search" name="q" placeholder="Kurs, Thema oder Lehrer suchen …" autocomplete="off" class="ac-search-input">
                <button type="submit" class="ac-btn ac-btn-primary" aria-label="Suchen">Suchen</button>
            </form>
            <button id="searchClose" type="button" class="ac-btn ac-btn-ghost" aria-label="Schließen">✕</button>
        </div>
    </div>
    <?php endif; ?>
</header><!-- #masthead -->
<div id="content" class="ac-site-content">
