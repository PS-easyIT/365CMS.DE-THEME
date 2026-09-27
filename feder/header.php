<?php
declare(strict_types=1);

/**
 * Feder – Header
 *
 * Der Core rendert header.php über ThemeManager::render() und übergibt die
 * Template-Daten ($post, $page, $posts, $category, $tag, $author, $results …).
 *
 * @package Feder_Theme
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
$siteTitle = feder_site_title();
$tagline = feder_site_description();
$documentTitle = feder_page_title($context);
$locale = isset($contentLocale) && is_string($contentLocale) && preg_match('/^[a-z]{2}$/', $contentLocale) === 1 ? $contentLocale : feder_request_locale();
$scheme = feder_color_scheme();

$logoUrl = feder_media_url(feder_text('header', 'logo_url'));
$showTagline = feder_flag('header', 'show_tagline', true) && $tagline !== '';
$showSearch = feder_flag('header', 'show_search', true);
$showToggle = feder_flag('header', 'show_darkmode_toggle', true);
$isArticle = isset($post) && is_object($post);
$showProgress = $isArticle && feder_flag('feder_article', 'show_progress_bar', true);

$bodyClasses = feder_body_class(
    $isArticle ? 'is-article' : '',
    isset($page) && is_array($page) ? 'is-page' : '',
    feder_flag('header', 'enable_sticky_header', false) ? 'has-sticky-header' : ''
);
?>
<!DOCTYPE html>
<html lang="<?php echo feder_e($locale); ?>"<?php echo $scheme !== 'auto' ? ' data-theme="' . feder_e($scheme) . '"' : ''; ?>>
<head>
    <meta charset="UTF-8">
    <?= function_exists('cms_csp_runtime_tags') ? cms_csp_runtime_tags() : '' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo feder_e($documentTitle); ?></title>
    <link rel="alternate" type="application/rss+xml" title="<?php echo feder_e($siteTitle); ?> – RSS" href="<?php echo feder_e(feder_feed_url()); ?>">
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo feder_e($bodyClasses); ?>">
<?php \CMS\Hooks::doAction('body_start'); ?>
<a class="fd-skip-link" href="#main-content">Zum Inhalt springen</a>

<header class="fd-header" id="fd-header">
    <div class="fd-header__inner">
        <a class="fd-brand" href="<?php echo feder_e(feder_url('/')); ?>" rel="home">
            <?php if ($logoUrl !== '') : ?>
                <img class="fd-brand__logo" src="<?php echo feder_e($logoUrl); ?>" alt="<?php echo feder_e($siteTitle); ?>" height="44">
            <?php else : ?>
                <span class="fd-brand__mark" aria-hidden="true"><?php echo feder_e(feder_initials($siteTitle)); ?></span>
                <span class="fd-brand__text">
                    <span class="fd-brand__name"><?php echo feder_e($siteTitle); ?></span>
                    <?php if ($showTagline) : ?>
                        <span class="fd-brand__tagline"><?php echo feder_e($tagline); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </a>

        <nav class="fd-nav" aria-label="Hauptnavigation">
            <?php feder_nav_menu('primary', 'fd-nav__list', feder_default_menu('primary')); ?>
        </nav>

        <div class="fd-header__actions">
            <?php if ($showSearch) : ?>
                <button type="button" class="fd-icon-button" data-fd-toggle="search" aria-controls="fd-search-panel" aria-expanded="false">
                    <?php echo feder_icon('search'); ?><span class="fd-visually-hidden">Suche öffnen</span>
                </button>
            <?php endif; ?>
            <?php if ($showToggle) : ?>
                <button type="button" class="fd-icon-button fd-scheme-toggle" data-fd-scheme-toggle aria-pressed="false">
                    <span class="fd-scheme-toggle__sun"><?php echo feder_icon('sun'); ?></span>
                    <span class="fd-scheme-toggle__moon"><?php echo feder_icon('moon'); ?></span>
                    <span class="fd-visually-hidden">Dunkles Farbschema</span>
                </button>
            <?php endif; ?>
            <button type="button" class="fd-icon-button fd-menu-toggle" data-fd-toggle="menu" aria-controls="fd-menu-panel" aria-expanded="false">
                <span class="fd-menu-toggle__open"><?php echo feder_icon('menu'); ?></span>
                <span class="fd-menu-toggle__close"><?php echo feder_icon('close'); ?></span>
                <span class="fd-visually-hidden">Menü</span>
            </button>
        </div>
    </div>

    <?php if ($showSearch) : ?>
        <div class="fd-panel fd-panel--search" id="fd-search-panel" hidden>
            <div class="fd-panel__inner">
                <?php feder_search_form('fd-header-search'); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="fd-panel fd-panel--menu" id="fd-menu-panel" hidden>
        <nav class="fd-panel__inner" aria-label="Mobile Navigation">
            <?php feder_nav_menu('primary', 'fd-mobile-nav', feder_default_menu('primary')); ?>
        </nav>
    </div>

    <?php if ($showProgress) : ?>
        <div class="fd-progress" aria-hidden="true"><span class="fd-progress__bar" data-fd-progress></span></div>
    <?php endif; ?>
</header>

<main id="main-content" class="fd-main" tabindex="-1">
