<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getSiteDescription(), ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getSiteTitle(), ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getThemeUrl('buildbase'), ENT_QUOTES, 'UTF-8'); ?>/style.css">
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="bb-body <?php echo theme_is_logged_in() ? 'is-logged-in' : ''; ?>">
<div id="page" class="bb-page-wrapper">

    <a class="bb-skip-link" href="#main">Zum Inhalt springen</a>

    <?php
    try {
        $c = \CMS\Services\ThemeCustomizer::instance();
        $showEmBanner  = filter_var($c->get('header', 'show_emergency_banner', false), FILTER_VALIDATE_BOOLEAN);
        $emergencyPhone = $c->get('header', 'emergency_phone', '+49 800 000 0000');
        $emergencyLabel = $c->get('header', 'emergency_label', '🔧 Notfall-Service:');
        $logoUrl        = $c->get('header', 'logo_url', '');
        $showSearch     = filter_var($c->get('header', 'show_search_btn', true), FILTER_VALIDATE_BOOLEAN);
    } catch (\Throwable $e) {
        $showEmBanner  = false;
        $emergencyPhone = '';
        $emergencyLabel = '';
        $logoUrl        = '';
        $showSearch     = true;
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

    if ($showEmBanner && !empty($emergencyPhone)) :
        echo '<div class="bb-emergency-banner" role="alert">';
        echo $safe($emergencyLabel) . ' <a class="bb-emergency-link" href="tel:' . $safe($telHref) . '">' . $safe($emergencyPhone) . '</a>';
        echo '</div>';
        echo '<script>document.body.classList.add("has-emergency-banner")</script>';
    endif;
    ?>

    <header id="masthead" class="bb-site-header" role="banner">
        <div class="bb-header-inner">

            <!-- Logo -->
            <div class="bb-branding">
                <a href="<?php echo $safe($homeUrl); ?>" class="bb-site-logo" rel="home">
                    <?php if (!empty($logoUrl)) : ?>
                        <img src="<?php echo $safe($logoUrl); ?>" alt="<?php echo $safe($siteTitle); ?>" width="160" height="44">
                    <?php else : ?>
                        <span class="bb-logo-text">
                            <span class="bb-logo-icon" aria-hidden="true">🔨</span>
                            <?php echo $safe($siteTitle); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Navigation -->
            <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Hauptnavigation">
                <?php theme_nav_menu('primary-nav'); ?>
            </nav>

            <!-- Aktionen -->
            <div class="bb-header-actions">
                <?php if ($showSearch) : ?>
                    <button id="searchToggle" type="button" class="bb-icon-btn" aria-label="Suche öffnen" aria-expanded="false">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                        </svg>
                    </button>
                <?php endif; ?>
                <?php if ($isLoggedIn) : ?>
                    <a href="<?php echo $safe($memberUrl); ?>" class="bb-btn bb-btn-primary">Dashboard</a>
                <?php else : ?>
                    <a href="<?php echo $safe($loginUrl); ?>"    class="bb-btn bb-btn-ghost">Anmelden</a>
                    <a href="<?php echo $safe($registerUrl); ?>" class="bb-btn bb-btn-primary">Profil anlegen</a>
                <?php endif; ?>
                <button id="mobileMenuToggle" type="button" class="bb-mobile-toggle" aria-label="Menü öffnen" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <?php if ($showSearch) : ?>
        <div id="searchPanel" class="bb-search-panel" hidden aria-hidden="true">
            <div class="bb-header-inner">
                <form role="search" method="get" action="<?php echo $safe($searchUrl); ?>" class="bb-search-form">
                    <label for="bb-search" class="bb-visually-hidden">Suche</label>
                    <input id="bb-search" type="search" name="q" placeholder="Handwerker, Baufirmen, Projekte suchen …" autocomplete="off" class="bb-search-input">
                    <button type="submit" class="bb-btn bb-btn-primary" aria-label="Suchen">Suchen</button>
                </form>
                <button id="searchClose" type="button" class="bb-btn bb-btn-ghost" aria-label="Suche schließen">✕</button>
            </div>
        </div>
        <?php endif; ?>
    </header><!-- #masthead -->

<div id="content" class="bb-site-content">
