<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Header
 *
 * Ankündigungsleiste (optional) → dunkler, fixierter Header mit Marke, Navigation,
 * Suche, GitHub-Link und Call-to-Action.
 *
 * @package Showcase_Theme
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
$documentTitle = showcase_page_title($context);
$locale = isset($contentLocale) && is_string($contentLocale) && preg_match('/^[a-z]{2}$/', $contentLocale) === 1 ? $contentLocale : showcase_request_locale();
$brandName = showcase_brand_name();
$logoUrl = showcase_logo();
$isHome = showcase_is_home_request();
$githubUrl = showcase_safe_url(showcase_text('header', 'github_url', 'https://github.com/PS-easyIT/365CMS.DE'), '');
$ctaLabel = showcase_text('header', 'cta_label', 'Jetzt starten');
$ctaUrl = showcase_text('header', 'cta_url', '/#installation');

$showAnnounce = showcase_flag('sc_announce', 'show_announce', true) && showcase_text('sc_announce', 'announce_text') !== '';
$announceBadge = showcase_text('sc_announce', 'announce_badge', 'Neu');
$announceLabel = showcase_text('sc_announce', 'announce_link_label');
$announceUrl = showcase_safe_url(showcase_text('sc_announce', 'announce_link_url'), '');

$bodyClasses = 'cms-showcase'
    . ($isHome ? ' is-home' : '')
    . (isset($post) && is_object($post) ? ' is-article' : '')
    . (isset($page) && is_array($page) ? ' is-page' : '');
?>
<!DOCTYPE html>
<html lang="<?php echo showcase_e($locale); ?>">
<head>
    <meta charset="UTF-8">
    <?= function_exists('cms_csp_runtime_tags') ? cms_csp_runtime_tags() : '' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo showcase_e($documentTitle); ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo showcase_e($bodyClasses); ?>" id="top">
<?php \CMS\Hooks::doAction('body_start'); ?>
<a class="sc-skip-link" href="#main-content">Zum Inhalt springen</a>

<?php if ($showAnnounce) : ?>
    <div class="sc-announce">
        <div class="sc-container sc-announce__inner">
            <?php if ($announceBadge !== '') : ?>
                <span class="sc-announce__badge"><?php echo showcase_e($announceBadge); ?></span>
            <?php endif; ?>
            <p class="sc-announce__text"><?php echo showcase_e(showcase_text('sc_announce', 'announce_text')); ?></p>
            <?php if ($announceLabel !== '' && $announceUrl !== '') : ?>
                <a class="sc-announce__link" href="<?php echo showcase_e(str_starts_with($announceUrl, '#') ? $announceUrl : showcase_url($announceUrl)); ?>"><span><?php echo showcase_e($announceLabel); ?></span><?php echo showcase_icon('arrow'); ?></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<header class="sc-header" id="sc-header">
    <div class="sc-container sc-header__inner">
        <a class="sc-brand" href="<?php echo showcase_e(showcase_url('/')); ?>" rel="home">
            <?php if ($logoUrl !== '') : ?>
                <img class="sc-brand__logo" src="<?php echo showcase_e($logoUrl); ?>" alt="" width="36" height="36">
            <?php else : ?>
                <span class="sc-brand__mark" aria-hidden="true"><?php echo showcase_icon('layers'); ?></span>
            <?php endif; ?>
            <span class="sc-brand__name"><?php echo showcase_e($brandName); ?></span>
        </a>

        <nav class="sc-nav" id="sc-nav" aria-label="Hauptnavigation">
            <?php showcase_nav_menu('primary', 'sc-nav__list', showcase_default_menu('primary')); ?>
            <div class="sc-nav__cta"><?php echo showcase_button($ctaLabel, $ctaUrl, 'primary'); ?></div>
        </nav>

        <div class="sc-header__actions">
            <?php if (showcase_flag('header', 'show_search', true)) : ?>
                <a class="sc-iconlink" href="<?php echo showcase_e(showcase_search_url()); ?>"><?php echo showcase_icon('search'); ?><span class="sc-visually-hidden">Suche</span></a>
            <?php endif; ?>
            <?php if ($githubUrl !== '') : ?>
                <a class="sc-iconlink" href="<?php echo showcase_e($githubUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo showcase_icon('github'); ?><span class="sc-visually-hidden">365CMS auf GitHub (öffnet in neuem Tab)</span></a>
            <?php endif; ?>
            <span class="sc-header__cta"><?php echo showcase_button($ctaLabel, $ctaUrl, 'primary', ''); ?></span>
            <button type="button" class="sc-menu-toggle" data-sc-menu-toggle aria-controls="sc-nav" aria-expanded="false">
                <span class="sc-menu-toggle__open"><?php echo showcase_icon('menu'); ?></span>
                <span class="sc-menu-toggle__close"><?php echo showcase_icon('close'); ?></span>
                <span class="sc-visually-hidden">Menü</span>
            </button>
        </div>
    </div>
</header>

<main id="main-content" class="sc-main" tabindex="-1">
