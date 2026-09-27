<?php
declare(strict_types=1);

/**
 * Kompass – Header
 *
 * Barrierefreiheits-Leiste (Leichte Sprache, Gebärdensprache, Service-Links, Schriftgröße,
 * Kontrast) → Kopfbereich mit Marke und Suche → Hauptnavigation → optionaler Hinweisbanner.
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Der Core rendert den Header in ThemeManager::render(); doppelte Einbindung verhindern.
if (defined('CMS_THEME_HEADER_RENDERED')) {
    return;
}
define('CMS_THEME_HEADER_RENDERED', true);

// Inhalte für das Core-SEO-Modul bereitstellen (Description, Canonical, Open Graph).
if (isset($post) && (is_object($post) || is_array($post))) {
    $GLOBALS['post'] = $post;
} elseif (isset($page) && is_array($page)) {
    $GLOBALS['page'] = $page;
}

$context = get_defined_vars();
$documentTitle = kompass_page_title($context);
$locale = isset($contentLocale) && is_string($contentLocale) && preg_match('/^[a-z]{2}$/', $contentLocale) === 1 ? $contentLocale : kompass_request_locale();
$siteTitle = kompass_site_title();
$siteDescription = kompass_site_description();
$logoUrl = kompass_media_url(kompass_text('header', 'logo_url'));
$isHome = kompass_is_home_request();

$showBar = kompass_flag('header', 'show_a11y_bar', true);
$showTextsize = $showBar && kompass_flag('header', 'show_textsize', true);
$showContrast = $showBar && kompass_flag('header', 'show_contrast', true);
$easyUrl = kompass_safe_url(kompass_text('header', 'easy_language_url'), '');
$signUrl = kompass_safe_url(kompass_text('header', 'sign_language_url'), '');
$showSearch = kompass_flag('header', 'show_search', true) && !$isHome;

ob_start();
kompass_nav_menu('service-nav', 'kp-a11ybar__list');
$serviceNav = (string) ob_get_clean();
$hasServiceLinks = $easyUrl !== '' || $signUrl !== '' || $serviceNav !== '';

$bodyClasses = 'kompass'
    . ($isHome ? ' is-home' : '')
    . (isset($post) && is_object($post) ? ' is-article' : '')
    . (isset($page) && is_array($page) ? ' is-page' : '');
?>
<!DOCTYPE html>
<html lang="<?php echo kompass_e($locale); ?>">
<head>
    <meta charset="UTF-8">
    <?= function_exists('cms_csp_runtime_tags') ? cms_csp_runtime_tags() : '' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo kompass_e($documentTitle); ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo kompass_e($bodyClasses); ?>" id="top">
<?php \CMS\Hooks::doAction('body_start'); ?>
<a class="kp-skip-link" href="#main-content">Zum Inhalt springen</a>

<?php if ($showBar && ($hasServiceLinks || $showTextsize || $showContrast)) : ?>
    <div class="kp-a11ybar">
        <div class="kp-container kp-a11ybar__inner">
            <?php if ($hasServiceLinks) : ?>
            <nav class="kp-a11ybar__nav" aria-label="Service">
                <?php if ($easyUrl !== '' || $signUrl !== '') : ?>
                    <ul class="kp-a11ybar__list">
                        <?php if ($easyUrl !== '') : ?>
                            <li><a href="<?php echo kompass_e(kompass_url($easyUrl)); ?>"><?php echo kompass_icon('easy'); ?><span>Leichte Sprache</span></a></li>
                        <?php endif; ?>
                        <?php if ($signUrl !== '') : ?>
                            <li><a href="<?php echo kompass_e(kompass_url($signUrl)); ?>"><?php echo kompass_icon('sign'); ?><span>Gebärdensprache</span></a></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
                <?php echo $serviceNav; ?>
            </nav>
            <?php endif; ?>

            <?php if ($showTextsize || $showContrast) : ?>
                <div class="kp-a11ybar__tools">
                    <?php if ($showTextsize) : ?>
                        <div class="kp-textsize" role="group" aria-labelledby="kp-textsize-label">
                            <span class="kp-a11ybar__label" id="kp-textsize-label"><?php echo kompass_icon('textsize'); ?>Schriftgröße</span>
                            <button type="button" class="kp-textsize__button" data-kp-fontsize="normal" aria-pressed="true">A<span class="kp-visually-hidden"> (normal)</span></button>
                            <button type="button" class="kp-textsize__button" data-kp-fontsize="large" aria-pressed="false">A+<span class="kp-visually-hidden"> (groß)</span></button>
                            <button type="button" class="kp-textsize__button" data-kp-fontsize="xlarge" aria-pressed="false">A++<span class="kp-visually-hidden"> (sehr groß)</span></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($showContrast) : ?>
                        <button type="button" class="kp-contrast-toggle" data-kp-contrast aria-pressed="false"><?php echo kompass_icon('contrast'); ?><span>Kontrast</span></button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<header class="kp-header" id="kp-header">
    <div class="kp-container kp-header__inner">
        <a class="kp-brand" href="<?php echo kompass_e(kompass_url('/')); ?>" rel="home">
            <?php if ($logoUrl !== '') : ?>
                <img class="kp-brand__logo" src="<?php echo kompass_e($logoUrl); ?>" alt="<?php echo kompass_e($siteTitle); ?>" height="56">
            <?php else : ?>
                <span class="kp-brand__mark"><?php echo kompass_icon('compass'); ?></span>
                <span class="kp-brand__text">
                    <span class="kp-brand__name"><?php echo kompass_e($siteTitle); ?></span>
                    <?php if ($siteDescription !== '') : ?>
                        <span class="kp-brand__tagline"><?php echo kompass_e($siteDescription); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </a>

        <?php if ($showSearch) : ?>
            <div class="kp-header__search">
                <?php kompass_search_form('kp-header-search', '', 'header'); ?>
            </div>
        <?php endif; ?>

        <button type="button" class="kp-menu-toggle" data-kp-menu-toggle aria-controls="kp-nav" aria-expanded="false">
            <span class="kp-menu-toggle__open"><?php echo kompass_icon('menu'); ?></span>
            <span class="kp-menu-toggle__close"><?php echo kompass_icon('close'); ?></span>
            <span>Menü</span>
        </button>
    </div>
</header>

<nav class="kp-nav" id="kp-nav" aria-label="Hauptnavigation">
    <div class="kp-container">
        <?php kompass_nav_menu('primary', 'kp-nav__list', kompass_default_menu('primary')); ?>
    </div>
</nav>

<?php kompass_notice(); ?>

<main id="main-content" class="kp-main" tabindex="-1">
