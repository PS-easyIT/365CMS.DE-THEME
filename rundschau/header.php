<?php
declare(strict_types=1);

/**
 * Rundschau – Header
 *
 * Service-Leiste (Datum, Claim, Service-Menü) → Masthead (Wortmarke, Suche)
 * → Ressort-Navigation (sticky) → Nachrichten-Ticker.
 *
 * @package Rundschau_Theme
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
$siteTitle = rundschau_site_title();
$documentTitle = rundschau_page_title($context);
$locale = isset($contentLocale) && is_string($contentLocale) && preg_match('/^[a-z]{2}$/', $contentLocale) === 1 ? $contentLocale : rundschau_request_locale();

$logoUrl = rundschau_media_url(rundschau_text('header', 'logo_url'));
$claim = rundschau_text('header', 'brand_claim', (string) Rundschau_Theme::instance()->getConfig('brand_claim', ''));
$showTopbar = rundschau_flag('header', 'show_topbar', true);
$showDate = rundschau_flag('header', 'show_date', true);
$showSearch = rundschau_flag('header', 'show_search', true);
$topbarLabel = rundschau_text('header', 'topbar_label', (string) Rundschau_Theme::instance()->getConfig('topbar_label', ''));

// Ticker
$tickerItems = [];
if (rundschau_flag('rs_ticker', 'show_ticker', true)) {
    $tickerCount = rundschau_int('rs_ticker', 'ticker_count', 6, 3, 12);
    if (rundschau_text('rs_ticker', 'ticker_source', 'latest') === 'tag' && rundschau_text('rs_ticker', 'ticker_tag') !== '') {
        $tickerItems = rundschau_get_posts(['limit' => $tickerCount, 'tag' => rundschau_text('rs_ticker', 'ticker_tag')]);
    }
    if ($tickerItems === []) {
        $tickerItems = rundschau_get_posts(['limit' => $tickerCount]);
    }
}
$tickerSpeed = rundschau_text('rs_ticker', 'ticker_speed', 'normal');
$tickerSpeed = in_array($tickerSpeed, ['slow', 'normal', 'fast'], true) ? $tickerSpeed : 'normal';

$appendRessorts = rundschau_flag('header', 'nav_append_ressorts', true);
$isArticle = isset($post) && is_object($post);
$bodyClasses = 'rundschau' . ($isArticle ? ' is-article' : '') . (isset($page) && is_array($page) ? ' is-page' : '');
?>
<!DOCTYPE html>
<html lang="<?php echo rundschau_e($locale); ?>">
<head>
    <meta charset="UTF-8">
    <?= function_exists('cms_csp_runtime_tags') ? cms_csp_runtime_tags() : '' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo rundschau_e($documentTitle); ?></title>
    <link rel="alternate" type="application/rss+xml" title="<?php echo rundschau_e($siteTitle); ?> – RSS" href="<?php echo rundschau_e(rundschau_url('/feed')); ?>">
    <?php \CMS\Hooks::doAction('head'); ?>
</head>
<body class="<?php echo rundschau_e($bodyClasses); ?>">
<?php \CMS\Hooks::doAction('body_start'); ?>
<a class="rs-skip-link" href="#main-content">Zum Inhalt springen</a>

<header class="rs-header">
    <?php if ($showTopbar) : ?>
        <div class="rs-topbar">
            <div class="rs-container rs-topbar__inner">
                <p class="rs-topbar__info">
                    <?php if ($showDate) : ?>
                        <time datetime="<?php echo rundschau_e(date('Y-m-d')); ?>"><?php echo rundschau_e(rundschau_format_date(time(), 'weekday')); ?></time>
                    <?php endif; ?>
                    <?php if ($topbarLabel !== '') : ?>
                        <span class="rs-topbar__label"><?php echo rundschau_e($topbarLabel); ?></span>
                    <?php endif; ?>
                </p>
                <nav class="rs-topbar__nav" aria-label="Service">
                    <?php rundschau_nav_menu('service-nav', 'rs-topbar__list', rundschau_default_menu('service-nav')); ?>
                </nav>
            </div>
        </div>
    <?php endif; ?>

    <div class="rs-masthead">
        <div class="rs-container rs-masthead__inner">
            <button type="button" class="rs-icon-button rs-menu-toggle" data-rs-toggle="menu" aria-controls="rs-menu-panel" aria-expanded="false">
                <span class="rs-menu-toggle__open"><?php echo rundschau_icon('menu'); ?></span>
                <span class="rs-menu-toggle__close"><?php echo rundschau_icon('close'); ?></span>
                <span class="rs-visually-hidden">Ressorts und Menü</span>
            </button>

            <a class="rs-brand" href="<?php echo rundschau_e(rundschau_url('/')); ?>" rel="home">
                <?php if ($logoUrl !== '') : ?>
                    <img class="rs-brand__logo" src="<?php echo rundschau_e($logoUrl); ?>" alt="<?php echo rundschau_e($siteTitle); ?>" height="56">
                <?php else : ?>
                    <span class="rs-brand__name"><?php echo rundschau_e($siteTitle); ?></span>
                <?php endif; ?>
                <?php if ($claim !== '') : ?>
                    <span class="rs-brand__claim"><?php echo rundschau_e($claim); ?></span>
                <?php endif; ?>
            </a>

            <?php if ($showSearch) : ?>
                <div class="rs-masthead__search">
                    <?php rundschau_search_form('rs-header-search', '', 'rs-searchform--header'); ?>
                </div>
                <button type="button" class="rs-icon-button rs-search-toggle" data-rs-toggle="search" aria-controls="rs-search-panel" aria-expanded="false">
                    <?php echo rundschau_icon('search'); ?><span class="rs-visually-hidden">Suche öffnen</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($showSearch) : ?>
        <div class="rs-panel rs-panel--search" id="rs-search-panel" hidden>
            <div class="rs-container">
                <?php rundschau_search_form('rs-mobile-search'); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="rs-panel rs-panel--menu" id="rs-menu-panel" hidden>
        <nav class="rs-container" aria-label="Ressorts (mobil)">
            <?php rundschau_nav_menu('primary', 'rs-mobile-nav', rundschau_default_menu('primary'), $appendRessorts); ?>
            <?php rundschau_nav_menu('service-nav', 'rs-mobile-nav rs-mobile-nav--service', rundschau_default_menu('service-nav')); ?>
        </nav>
    </div>
</header>

<nav class="rs-navbar" aria-label="Ressorts">
    <div class="rs-container rs-navbar__inner" data-rs-scroll-shadow>
        <?php rundschau_nav_menu('primary', 'rs-navbar__list', rundschau_default_menu('primary'), $appendRessorts); ?>
    </div>
</nav>

<?php if ($tickerItems !== []) : ?>
    <div class="rs-ticker rs-ticker--<?php echo rundschau_e($tickerSpeed); ?>" data-rs-ticker>
        <div class="rs-container rs-ticker__inner">
            <p class="rs-ticker__label"><?php echo rundschau_icon('bolt'); ?><span><?php echo rundschau_e(rundschau_text('rs_ticker', 'ticker_label', 'Aktuell')); ?></span></p>
            <div class="rs-ticker__viewport">
                <ul class="rs-ticker__track" data-rs-ticker-track>
                    <?php foreach ([false, true] as $isClone) : ?>
                        <?php foreach ($tickerItems as $item) :
                            $date = (string) ($item->published_at ?? $item->created_at ?? '');
                            ?>
                            <li class="rs-ticker__item"<?php echo $isClone ? ' aria-hidden="true" data-rs-ticker-clone' : ''; ?>>
                                <time datetime="<?php echo rundschau_e(rundschau_format_date($date, 'iso')); ?>"><?php echo rundschau_e(rundschau_time_label($date)); ?></time>
                                <a href="<?php echo rundschau_e(rundschau_post_link($item)); ?>"<?php echo $isClone ? ' tabindex="-1"' : ''; ?>><?php echo rundschau_e((string) ($item->title ?? '')); ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button type="button" class="rs-ticker__toggle" data-rs-ticker-toggle aria-pressed="false">
                <span class="rs-ticker__pause"><?php echo rundschau_icon('pause'); ?></span>
                <span class="rs-ticker__play"><?php echo rundschau_icon('play'); ?></span>
                <span class="rs-visually-hidden">Ticker anhalten</span>
            </button>
        </div>
    </div>
<?php endif; ?>

<main id="main-content" class="rs-main" tabindex="-1">
