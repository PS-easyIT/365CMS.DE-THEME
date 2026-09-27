<?php
declare(strict_types=1);

/**
 * Feder – Footer
 *
 * @package Feder_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Der Core rendert den Footer in ThemeManager::render(); doppelte Einbindung verhindern.
if (defined('CMS_THEME_FOOTER_RENDERED')) {
    return;
}
define('CMS_THEME_FOOTER_RENDERED', true);

$siteTitle = feder_site_title();
$tagline = feder_text('footer', 'footer_tagline', 'Ein persönliches Blog über Technik, Arbeit und Alltag.');
$copyright = strtr(feder_text('footer', 'footer_copyright', '© {year} {site_title}'), [
    '{year}' => date('Y'),
    '{site_title}' => $siteTitle,
]);
$socialLinks = feder_social_links();
?>
</main>

<footer class="fd-footer">
    <div class="fd-footer__inner">
        <div class="fd-footer__brand">
            <a class="fd-footer__name" href="<?php echo feder_e(feder_url('/')); ?>" rel="home"><?php echo feder_e($siteTitle); ?></a>
            <?php if ($tagline !== '') : ?>
                <p class="fd-footer__tagline"><?php echo feder_e($tagline); ?></p>
            <?php endif; ?>
            <?php if ($socialLinks !== []) : ?>
                <ul class="fd-social" aria-label="Profile und Feed">
                    <?php foreach ($socialLinks as $link) :
                        $external = preg_match('#^https?://#i', $link['url']) === 1 && parse_url($link['url'], PHP_URL_HOST) !== parse_url((string) SITE_URL, PHP_URL_HOST);
                        ?>
                        <li>
                            <a href="<?php echo feder_e($link['url']); ?>"<?php echo $external ? ' target="_blank" rel="me noopener noreferrer"' : ''; ?>>
                                <?php echo feder_icon($link['icon']); ?><span class="fd-visually-hidden"><?php echo feder_e($link['label']); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <nav class="fd-footer__nav" aria-label="Footer-Navigation">
            <?php feder_nav_menu('footer-nav', 'fd-footer__list', feder_default_menu('footer-nav')); ?>
        </nav>
    </div>

    <div class="fd-footer__bottom">
        <p class="fd-footer__copy"><?php echo feder_e($copyright); ?></p>
        <nav class="fd-footer__legal" aria-label="Rechtliche Links">
            <?php feder_nav_menu('footer-legal', 'fd-footer__legal-list', feder_default_menu('footer-legal')); ?>
        </nav>
        <?php if (feder_flag('footer', 'show_back_to_top', true)) : ?>
            <a class="fd-to-top" href="#main-content"><?php echo feder_icon('up'); ?><span>Nach oben</span></a>
        <?php endif; ?>
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
