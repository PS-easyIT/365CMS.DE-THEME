<?php
/**
 * PersonalFlow Theme – Header Template
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$pfTitle       = pf_site_title();
$pfTitleEsc    = htmlspecialchars($pfTitle, ENT_QUOTES, 'UTF-8');
$pfBodyCls     = pf_body_class();
$pfIsLoggedIn  = theme_is_logged_in();

$pfHomeUrl     = htmlspecialchars(theme_route_url('home'),     ENT_QUOTES, 'UTF-8');
$pfSearchUrl   = htmlspecialchars(theme_route_url('search'),   ENT_QUOTES, 'UTF-8');
$pfLoginUrl    = htmlspecialchars(theme_route_url('login'),    ENT_QUOTES, 'UTF-8');
$pfRegisterUrl = htmlspecialchars(theme_route_url('register'), ENT_QUOTES, 'UTF-8');
$pfMemberUrl   = htmlspecialchars(theme_route_url('member'),   ENT_QUOTES, 'UTF-8');

$pfLogoUrl     = (string) pf_get_setting('header', 'logo_url', '');
$pfShowSearch  = filter_var(pf_get_setting('header', 'show_search', true), FILTER_VALIDATE_BOOLEAN);
$pfSearchPh    = (string) pf_get_setting('header', 'search_placeholder', 'Kandidaten, Jobs oder Unternehmen suchen …');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pfTitleEsc; ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo $pfBodyCls; ?>">
<a class="pf-skip-link" href="#main">Zum Inhalt springen</a>
<div id="page" class="pf-page-wrapper">

<header id="masthead" class="pf-site-header" role="banner">
    <div class="pf-header-inner">

        <div class="pf-site-branding">
            <a href="<?php echo $pfHomeUrl; ?>" class="pf-site-logo pf-focus-shadow" rel="home"
               aria-label="<?php echo $pfTitleEsc; ?> – Startseite">
                <?php if ($pfLogoUrl !== '') : ?>
                    <img src="<?php echo htmlspecialchars($pfLogoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo $pfTitleEsc; ?>" width="160" height="44" loading="eager">
                <?php else : ?>
                    <span class="pf-logo-text">
                        <span class="pf-logo-mark" aria-hidden="true">P</span>
                        <?php echo $pfTitleEsc; ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>

        <nav id="site-navigation" class="main-navigation" aria-label="Hauptnavigation">
            <?php theme_nav_menu('primary-nav'); ?>
        </nav>

        <div class="pf-header-actions">
            <?php if ($pfShowSearch) : ?>
                <button id="pfSearchToggle"
                        type="button"
                        class="pf-icon-btn pf-focus-shadow"
                        aria-label="Suche öffnen"
                        aria-expanded="false"
                        aria-controls="pfSearchPanel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" focusable="false" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>
            <?php endif; ?>

            <?php if ($pfIsLoggedIn) : ?>
                <a href="<?php echo $pfMemberUrl; ?>" class="pf-btn pf-btn-primary pf-focus-shadow">Mein Bereich</a>
            <?php else : ?>
                <a href="<?php echo $pfLoginUrl; ?>"    class="pf-btn pf-btn-ghost pf-focus-shadow">Anmelden</a>
                <a href="<?php echo $pfRegisterUrl; ?>" class="pf-btn pf-btn-primary pf-focus-shadow">Kostenlos starten</a>
            <?php endif; ?>

            <button id="pfMobileToggle"
                    type="button"
                    class="pf-mobile-toggle pf-focus-shadow"
                    aria-label="Menü öffnen"
                    aria-expanded="false"
                    aria-controls="pfMobileDrawer">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <?php if ($pfShowSearch) : ?>
    <div id="pfSearchPanel" class="pf-search-panel" hidden aria-hidden="true">
        <div class="pf-header-inner pf-search-panel-inner">
            <form role="search" method="get" action="<?php echo $pfSearchUrl; ?>" class="pf-search-form">
                <label for="pf-search" class="pf-visually-hidden">Suche</label>
                <input id="pf-search"
                       type="search"
                       name="q"
                       placeholder="<?php echo htmlspecialchars($pfSearchPh, ENT_QUOTES, 'UTF-8'); ?>"
                       autocomplete="off">
                <button type="submit" aria-label="Suchen">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" focusable="false" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </button>
            </form>
            <button id="pfSearchClose"
                    type="button"
                    class="pf-icon-btn pf-focus-shadow"
                    aria-label="Suche schließen">✕</button>
        </div>
    </div>
    <?php endif; ?>
</header><!-- #masthead -->

<div id="pfMobileOverlay" class="pf-mobile-overlay" aria-hidden="true"></div>
<nav id="pfMobileDrawer"
     class="pf-mobile-drawer"
     aria-label="Mobile Navigation"
     aria-hidden="true">
    <?php theme_nav_menu('primary-nav'); ?>
    <?php if (!$pfIsLoggedIn) : ?>
        <div class="pf-cta-group pf-mobile-drawer-cta">
            <a href="<?php echo $pfLoginUrl; ?>"    class="pf-btn pf-btn-ghost">Anmelden</a>
            <a href="<?php echo $pfRegisterUrl; ?>" class="pf-btn pf-btn-primary">Kostenlos starten</a>
        </div>
    <?php endif; ?>
</nav>

<div id="content" class="pf-site-content">
