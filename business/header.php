<?php
declare(strict_types=1);

/**
 * Business Theme – Header
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$title    = biz_site_title();
$siteUrl  = biz_site_url();
$bodyCls  = biz_body_class();

try {
    $_logoUrl = (string) \CMS\Services\ThemeCustomizer::instance()->get('header', 'logo_url', '');
} catch (\Throwable) {
    $_logoUrl = '';
}
$logoUrl = biz_safe_url($_logoUrl);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>
    <?php \CMS\Hooks::doAction('head'); ?>
</head>

<body class="<?php echo $bodyCls; ?>">
<a class="skip-link" href="#main-content">Zum Inhalt springen</a>

<div class="biz-site">

    <header class="biz-header" id="biz-masthead">
        <div class="biz-container">
            <div class="biz-header-inner">

                <!-- Logo -->
                <a href="<?php echo htmlspecialchars(biz_href('/'), ENT_QUOTES, 'UTF-8'); ?>" class="biz-logo biz-focus-shadow" aria-label="<?php echo $title; ?>">
                    <?php if ($logoUrl !== '') : ?>
                        <img src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                             alt="<?php echo $title; ?>" class="biz-logo-img">
                    <?php else : ?>
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                             aria-hidden="true" focusable="false">
                            <rect width="32" height="32" rx="6" fill="#c08a2e"/>
                            <path d="M8 10h4v12H8V10zm6 0h4v12h-4V10zm6 4h4v8h-4v-8z" fill="#0c1320"/>
                        </svg>
                        <span class="biz-logo-text"><?php echo $title; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Desktop Navigation -->
                <nav class="biz-nav" id="biz-main-nav" aria-label="Hauptnavigation">
                    <?php biz_nav_menu('primary'); ?>
                </nav>

                <!-- Header CTA -->
                <div class="biz-header-cta">
                    <a href="<?php echo htmlspecialchars(biz_href('#kontakt'), ENT_QUOTES, 'UTF-8'); ?>"
                       class="btn-biz btn-biz-primary">
                        Kontakt
                    </a>
                </div>

                <!-- Mobile Toggle -->
                <button class="biz-mobile-toggle" id="bizMobileToggle"
                        aria-label="Menü öffnen" aria-expanded="false" aria-controls="bizMobileDrawer"
                        type="button">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </button>

            </div>
        </div>
    </header>

    <!-- Mobile Overlay & Drawer -->
    <div class="biz-mobile-overlay" id="bizMobileOverlay" role="presentation"></div>
    <nav id="bizMobileDrawer" class="biz-mobile-drawer" aria-label="Mobile Navigation" aria-hidden="true">
        <?php biz_nav_menu('primary'); ?>
        <div class="biz-drawer-cta">
            <a href="<?php echo htmlspecialchars(biz_href('#kontakt'), ENT_QUOTES, 'UTF-8'); ?>"
               class="btn-biz btn-biz-primary biz-drawer-cta-link">
                Kontakt aufnehmen
            </a>
        </div>
    </nav>

    <main id="main-content" class="biz-content">
