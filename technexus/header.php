<?php
/**
 * TechNexus Theme – Header Template
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$tnTitle     = tn_site_title();
$tnTitleEsc  = tn_html_attr($tnTitle);
$tnBodyClass = tn_body_class();
$tnHomeUrl   = tn_html_attr(theme_route_url('home'));
$tnLoginUrl  = tn_html_attr(theme_route_url('login'));
$tnRegUrl    = tn_html_attr(theme_route_url('register'));
$tnMemberUrl = tn_html_attr(theme_route_url('member'));
$tnSearchUrl = tn_html_attr(theme_route_url('search'));

$tnLogoUrl        = (string) tn_get_setting('header', 'logo_url', '');
$tnShowStatus     = filter_var(tn_get_setting('header', 'show_status_dot', true), FILTER_VALIDATE_BOOLEAN);
$tnBlurHeader     = filter_var(tn_get_setting('header', 'enable_blur_header', true), FILTER_VALIDATE_BOOLEAN);
$tnShowDarkToggle = filter_var(tn_get_setting('advanced', 'enable_dark_mode_toggle', true), FILTER_VALIDATE_BOOLEAN);
$tnIsLoggedIn     = theme_is_logged_in();

$tnPrefersDark = filter_var(tn_get_setting('advanced', 'prefers_dark_mode', false), FILTER_VALIDATE_BOOLEAN);
$tnHtmlTheme   = $tnPrefersDark ? 'dark' : 'light';
?>
<!DOCTYPE html>
<html lang="de" data-theme="<?php echo tn_html_attr($tnHtmlTheme); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tnTitleEsc; ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>

<body class="<?php echo $tnBodyClass; ?>">
<div id="page" class="site">
    <a class="skip-link tn-focus-shadow" href="#main">Zum Inhalt springen</a>

    <header id="masthead"
            class="site-header<?php echo $tnBlurHeader ? ' blur-enabled' : ''; ?>"
            role="banner">
        <div class="header-container">
            <div class="header-inner">

                <div class="site-branding">
                    <div class="site-logo">
                        <a href="<?php echo $tnHomeUrl; ?>/"
                           class="tn-focus-shadow"
                           aria-label="<?php echo $tnTitleEsc; ?>">
                            <?php if ($tnLogoUrl !== '') : ?>
                                <img src="<?php echo tn_html_attr($tnLogoUrl); ?>"
                                     alt="<?php echo $tnTitleEsc; ?>"
                                     class="site-logo-img">
                            <?php else : ?>
                                <svg class="site-logo-mark" viewBox="0 0 60 60" fill="none"
                                     xmlns="http://www.w3.org/2000/svg"
                                     focusable="false" aria-hidden="true">
                                    <rect x="2" y="2" width="56" height="56" rx="10" fill="currentColor" opacity="0.12"/>
                                    <circle cx="30" cy="30" r="4" fill="currentColor"/>
                                    <circle cx="14" cy="14" r="3.5" fill="currentColor"/>
                                    <circle cx="46" cy="14" r="3.5" fill="currentColor"/>
                                    <circle cx="14" cy="46" r="3.5" fill="currentColor"/>
                                    <circle cx="46" cy="46" r="3.5" fill="currentColor"/>
                                    <line x1="30" y1="30" x2="14" y2="14" stroke="currentColor" stroke-width="1.5" opacity="0.55"/>
                                    <line x1="30" y1="30" x2="46" y2="14" stroke="currentColor" stroke-width="1.5" opacity="0.55"/>
                                    <line x1="30" y1="30" x2="14" y2="46" stroke="currentColor" stroke-width="1.5" opacity="0.55"/>
                                    <line x1="30" y1="30" x2="46" y2="46" stroke="currentColor" stroke-width="1.5" opacity="0.55"/>
                                </svg>
                            <?php endif; ?>
                        </a>
                    </div>
                    <div class="site-identity">
                        <span class="site-title">
                            <a href="<?php echo $tnHomeUrl; ?>/" rel="home" class="tn-focus-shadow">
                                <?php echo $tnTitleEsc; ?>
                            </a>
                        </span>
                        <?php if ($tnShowStatus && $tnIsLoggedIn) : ?>
                            <span class="status-indicator" title="Online" aria-hidden="true"></span>
                        <?php endif; ?>
                    </div>
                </div>

                <nav id="site-navigation"
                     class="main-navigation"
                     role="navigation"
                     aria-label="Hauptmenü">
                    <?php theme_nav_menu('primary'); ?>
                </nav>

                <div class="header-actions">
                    <?php if ($tnShowDarkToggle) : ?>
                        <button class="theme-toggle tn-icon-btn tn-focus-shadow"
                                id="themeToggle"
                                type="button"
                                aria-label="Farbschema umschalten">
                            <span class="theme-toggle-icon dark-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" focusable="false" aria-hidden="true">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                                </svg>
                            </span>
                            <span class="theme-toggle-icon light-icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" focusable="false" aria-hidden="true">
                                    <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                </svg>
                            </span>
                        </button>
                    <?php endif; ?>

                    <button class="search-toggle tn-icon-btn tn-focus-shadow"
                            id="searchToggle"
                            type="button"
                            aria-label="Suche öffnen"
                            aria-expanded="false"
                            aria-controls="searchPanel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             width="20" height="20" focusable="false" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </button>

                    <?php if ($tnIsLoggedIn) : ?>
                        <a href="<?php echo $tnMemberUrl; ?>"
                           class="btn btn-outline btn-header">
                            Dashboard
                        </a>
                    <?php else : ?>
                        <a href="<?php echo $tnLoginUrl; ?>"
                           class="btn btn-outline btn-header">
                            Anmelden
                        </a>
                        <a href="<?php echo $tnRegUrl; ?>"
                           class="btn btn-primary btn-header">
                            Registrieren
                        </a>
                    <?php endif; ?>

                    <button class="mobile-menu-toggle tn-icon-btn tn-focus-shadow"
                            id="mobileMenuToggle"
                            type="button"
                            aria-label="Menü öffnen"
                            aria-expanded="false"
                            aria-controls="site-navigation">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             width="24" height="24" focusable="false" aria-hidden="true">
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </header>

    <div id="searchPanel" class="search-panel" hidden role="search" aria-label="Suche">
        <div class="search-panel-inner">
            <form action="<?php echo $tnSearchUrl; ?>" method="get" class="search-form">
                <label for="tn-search-input" class="tn-visually-hidden">Suche</label>
                <input id="tn-search-input"
                       type="search"
                       name="q"
                       placeholder="IT-Experten, Tech-Skills, Firmen suchen…"
                       class="search-input tn-focus-shadow"
                       autocomplete="off">
                <button type="submit" class="btn btn-primary" aria-label="Suchen">Suchen</button>
            </form>
            <button class="search-close tn-icon-btn tn-focus-shadow"
                    id="searchClose"
                    type="button"
                    aria-label="Suche schließen">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" focusable="false" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="content" class="site-content">
