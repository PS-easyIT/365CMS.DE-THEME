<?php
declare(strict_types=1);

/**
 * Kontor – Header
 *
 * Kontaktleiste (optional) → Header mit Marke, Navigation und Beratungs-Button.
 *
 * @package Kontor_Theme
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
$documentTitle = kontor_page_title($context);
$locale = isset($contentLocale) && is_string($contentLocale) && preg_match('/^[a-z]{2}$/', $contentLocale) === 1 ? $contentLocale : kontor_request_locale();
$company = kontor_company_name();
$logoUrl = kontor_media_url(kontor_text('header', 'logo_url'));
$contact = kontor_contact();
$socials = kontor_social_links();
$showContactBar = kontor_flag('header', 'show_contact_bar', true);
$ctaLabel = kontor_text('header', 'header_cta_label', 'Beratung anfragen');
$ctaUrl = kontor_text('header', 'header_cta_url', '/kontakt');
$isHome = kontor_is_home_request();
$bodyClasses = 'kontor' . ($isHome ? ' is-home' : '') . (isset($post) && is_object($post) ? ' is-article' : '');
?>
<!DOCTYPE html>
<html lang="<?php echo kontor_e($locale); ?>">
<head>
    <meta charset="UTF-8">
    <?= function_exists('cms_csp_runtime_tags') ? cms_csp_runtime_tags() : '' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo kontor_e($documentTitle); ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo kontor_e($bodyClasses); ?>">
<?php \CMS\Hooks::doAction('body_start'); ?>
<a class="kt-skip-link" href="#main-content">Zum Inhalt springen</a>

<?php if ($showContactBar) : ?>
    <div class="kt-contactbar">
        <div class="kt-container kt-contactbar__inner">
            <p class="kt-contactbar__text"><?php echo kontor_e(kontor_text('header', 'contact_bar_text')); ?></p>
            <ul class="kt-contactbar__list">
                <?php if ($contact['phone_href'] !== '') : ?>
                    <li><a href="<?php echo kontor_e($contact['phone_href']); ?>"><?php echo kontor_icon('phone'); ?><span><?php echo kontor_e($contact['phone']); ?></span></a></li>
                <?php endif; ?>
                <?php if ($contact['email_href'] !== '') : ?>
                    <li><a href="<?php echo kontor_e($contact['email_href']); ?>"><?php echo kontor_icon('mail'); ?><span><?php echo kontor_e($contact['email']); ?></span></a></li>
                <?php endif; ?>
                <?php foreach ($socials as $social) : ?>
                    <li><a href="<?php echo kontor_e($social['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo kontor_icon($social['icon']); ?><span class="kt-visually-hidden"><?php echo kontor_e($social['label']); ?></span></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<header class="kt-header" id="kt-header">
    <div class="kt-container kt-header__inner">
        <a class="kt-brand" href="<?php echo kontor_e(kontor_url('/')); ?>" rel="home">
            <?php if ($logoUrl !== '') : ?>
                <img class="kt-brand__logo" src="<?php echo kontor_e($logoUrl); ?>" alt="<?php echo kontor_e($company); ?>" height="48">
            <?php else : ?>
                <span class="kt-brand__mark" aria-hidden="true"><?php echo kontor_e(kontor_initials($company)); ?></span>
                <span class="kt-brand__name"><?php echo kontor_e($company); ?></span>
            <?php endif; ?>
        </a>

        <nav class="kt-nav" aria-label="Hauptnavigation">
            <?php kontor_nav_menu('primary', 'kt-nav__list', kontor_default_menu('primary')); ?>
        </nav>

        <div class="kt-header__actions">
            <a class="kt-icon-link" href="<?php echo kontor_e(kontor_search_url()); ?>"><?php echo kontor_icon('search'); ?><span class="kt-visually-hidden">Suche</span></a>
            <span class="kt-header__cta"><?php echo kontor_button($ctaLabel, $ctaUrl, 'primary'); ?></span>
            <button type="button" class="kt-menu-toggle" data-kt-menu-toggle aria-controls="kt-mobile-menu" aria-expanded="false">
                <span class="kt-menu-toggle__open"><?php echo kontor_icon('menu'); ?></span>
                <span class="kt-menu-toggle__close"><?php echo kontor_icon('close'); ?></span>
                <span class="kt-visually-hidden">Menü</span>
            </button>
        </div>
    </div>

    <div class="kt-mobile" id="kt-mobile-menu" hidden>
        <nav class="kt-container" aria-label="Mobile Navigation">
            <?php kontor_nav_menu('primary', 'kt-mobile__list', kontor_default_menu('primary')); ?>
            <div class="kt-mobile__cta"><?php echo kontor_button($ctaLabel, $ctaUrl, 'primary', true); ?></div>
            <?php if ($contact['phone_href'] !== '' || $contact['email_href'] !== '') : ?>
                <ul class="kt-contactlist kt-contactlist--mobile">
                    <?php if ($contact['phone_href'] !== '') : ?>
                        <li><?php echo kontor_icon('phone'); ?><a href="<?php echo kontor_e($contact['phone_href']); ?>"><?php echo kontor_e($contact['phone']); ?></a></li>
                    <?php endif; ?>
                    <?php if ($contact['email_href'] !== '') : ?>
                        <li><?php echo kontor_icon('mail'); ?><a href="<?php echo kontor_e($contact['email_href']); ?>"><?php echo kontor_e($contact['email']); ?></a></li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main id="main-content" class="kt-main" tabindex="-1">
