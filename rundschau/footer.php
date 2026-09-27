<?php
declare(strict_types=1);

/**
 * Rundschau – Footer
 *
 * @package Rundschau_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Der Core rendert den Footer in ThemeManager::render(); doppelte Einbindung verhindern.
if (defined('CMS_THEME_FOOTER_RENDERED')) {
    return;
}
define('CMS_THEME_FOOTER_RENDERED', true);

$siteTitle = rundschau_site_title();
$tagline = rundschau_text('footer', 'footer_tagline');
$copyright = strtr(rundschau_text('footer', 'footer_copyright', '© {year} {site_title}'), [
    '{year}' => date('Y'),
    '{site_title}' => $siteTitle,
]);
$ressorts = rundschau_flag('footer', 'show_ressorts', true)
    ? array_values(array_filter(rundschau_categories(), static fn(array $c): bool => $c['parent_id'] === 0 && $c['total'] > 0))
    : [];
?>
</main>

<footer class="rs-footer">
    <div class="rs-container rs-footer__grid">
        <div class="rs-footer__brand">
            <a class="rs-footer__name" href="<?php echo rundschau_e(rundschau_url('/')); ?>" rel="home"><?php echo rundschau_e($siteTitle); ?></a>
            <?php if ($tagline !== '') : ?>
                <p><?php echo rundschau_e($tagline); ?></p>
            <?php endif; ?>
            <a class="rs-footer__rss" href="<?php echo rundschau_e(rundschau_url('/feed')); ?>"><?php echo rundschau_icon('rss'); ?><span>RSS-Feed abonnieren</span></a>
        </div>

        <?php if ($ressorts !== []) : ?>
            <nav class="rs-footer__col" aria-label="Ressorts">
                <h2 class="rs-footer__title">Ressorts</h2>
                <ul class="rs-footer__list">
                    <?php foreach (array_slice($ressorts, 0, 10) as $ressort) : ?>
                        <li><a href="<?php echo rundschau_e($ressort['url']); ?>"><?php echo rundschau_e($ressort['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>

        <nav class="rs-footer__col" aria-label="Footer-Navigation">
            <h2 class="rs-footer__title">Service</h2>
            <?php rundschau_nav_menu('footer-nav', 'rs-footer__list', rundschau_default_menu('footer-nav')); ?>
        </nav>
    </div>

    <div class="rs-footer__bottom">
        <div class="rs-container rs-footer__bottom-inner">
            <p><?php echo rundschau_e($copyright); ?></p>
            <nav aria-label="Rechtliche Links">
                <?php rundschau_nav_menu('footer-legal', 'rs-footer__legal', rundschau_default_menu('footer-legal')); ?>
            </nav>
            <a class="rs-footer__top" href="#main-content"><?php echo rundschau_icon('up'); ?><span>Nach oben</span></a>
        </div>
    </div>
</footer>

<?php
// before_footer (Theme-Scripts) und body_end (Cookie-Consent, Web-Vitals, PhotoSwipe)
// laufen pro Request nur einmal – der Core löst sie ebenfalls aus.
\CMS\Hooks::doAction('before_footer');
\CMS\Hooks::doAction('body_end');
?>
</body>
</html>
