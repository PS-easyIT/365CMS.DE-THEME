<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$_pgNavItems = isset($_pgNavItems) && is_array($_pgNavItems) ? $_pgNavItems : [];
$siteUrl = defined('SITE_URL') ? SITE_URL : '';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
?>
<div class="sidebar-widget page-sidebar-nav">
    <h4 class="page-sidebar-nav__title"><?php echo htmlspecialchars(phinit_t('page_navigation', [], $currentLocale), ENT_QUOTES); ?></h4>
    <nav aria-label="<?php echo htmlspecialchars(phinit_t('page_navigation', [], $currentLocale), ENT_QUOTES); ?>">
        <?php foreach ($_pgNavItems as $_ni): ?>
        <?php $navHref = function_exists('phinit_safe_public_url') ? phinit_safe_public_url((string) ($_ni['url'] ?? ''), $siteUrl, ['http', 'https']) : (string) ($_ni['url'] ?? ''); ?>
        <?php if ($navHref === ''): continue; endif; ?>
        <a href="<?php echo htmlspecialchars($navHref, ENT_QUOTES); ?>"
           class="page-sidebar-nav__link"><?php echo htmlspecialchars(phinit_localize_menu_label((string) ($_ni['label'] ?? ''), $currentLocale), ENT_QUOTES); ?></a>
        <?php endforeach; ?>
    </nav>
</div>
