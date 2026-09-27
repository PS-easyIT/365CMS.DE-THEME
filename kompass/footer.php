<?php
declare(strict_types=1);

/**
 * Kompass – Footer
 *
 * Organisation mit Anschrift → Service-Links → Kontakt/Sprechzeiten → Copyright, rechtliche
 * Links und Erklärung zur Barrierefreiheit.
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Der Core rendert den Footer in ThemeManager::render(); doppelte Einbindung verhindern.
if (defined('CMS_THEME_FOOTER_RENDERED')) {
    return;
}
define('CMS_THEME_FOOTER_RENDERED', true);

$organisation = kompass_organisation();
$service = kompass_service();
$tagline = kompass_text('footer', 'footer_tagline');
$a11yUrl = kompass_safe_url(kompass_text('footer', 'accessibility_url'), '');
$copyright = strtr(kompass_text('footer', 'footer_copyright', '© {year} {site_title}'), [
    '{year}' => date('Y'),
    '{site_title}' => kompass_site_title(),
]);
?>
</main>

<footer class="kp-footer">
    <div class="kp-container kp-footer__grid">
        <div class="kp-footer__brand">
            <a class="kp-brand kp-brand--light" href="<?php echo kompass_e(kompass_url('/')); ?>" rel="home">
                <span class="kp-brand__mark"><?php echo kompass_icon('compass'); ?></span>
                <span class="kp-brand__text"><span class="kp-brand__name"><?php echo kompass_e($organisation); ?></span></span>
            </a>
            <?php if ($tagline !== '') : ?>
                <p class="kp-footer__tagline"><?php echo kompass_e($tagline); ?></p>
            <?php endif; ?>
            <?php if ($service['address'] !== []) : ?>
                <address class="kp-footer__address"><?php echo kompass_e($organisation); ?><br><?php echo implode('<br>', array_map('kompass_e', $service['address'])); ?></address>
            <?php endif; ?>
        </div>

        <nav class="kp-footer__col" aria-labelledby="kp-footer-service">
            <h2 class="kp-footer__title" id="kp-footer-service">Service</h2>
            <?php kompass_nav_menu('footer-nav', 'kp-footer__list', kompass_default_menu('footer-nav')); ?>
        </nav>

        <div class="kp-footer__col">
            <h2 class="kp-footer__title">Kontakt</h2>
            <ul class="kp-footer__contact">
                <?php if ($service['phone_href'] !== '') : ?>
                    <li><?php echo kompass_icon('phone'); ?><a href="<?php echo kompass_e($service['phone_href']); ?>"><?php echo kompass_e($service['phone']); ?></a></li>
                <?php endif; ?>
                <?php if ($service['email_href'] !== '') : ?>
                    <li><?php echo kompass_icon('mail'); ?><a href="<?php echo kompass_e($service['email_href']); ?>"><?php echo kompass_e($service['email']); ?></a></li>
                <?php endif; ?>
                <?php if ($service['hours'] !== []) : ?>
                    <li><?php echo kompass_icon('clock'); ?><span><span class="kp-visually-hidden">Sprechzeiten: </span><?php echo implode('<br>', array_map('kompass_e', $service['hours'])); ?></span></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="kp-footer__bottom">
        <div class="kp-container kp-footer__bottom-inner">
            <p class="kp-footer__copy"><?php echo kompass_e($copyright); ?></p>
            <nav class="kp-footer__legalnav" aria-label="Rechtliche Links">
                <?php kompass_nav_menu('footer-legal', 'kp-footer__legal', kompass_default_menu('footer-legal')); ?>
                <?php if ($a11yUrl !== '') : ?>
                    <a class="kp-footer__a11y" href="<?php echo kompass_e(kompass_url($a11yUrl)); ?>">Erklärung zur Barrierefreiheit</a>
                <?php endif; ?>
            </nav>
            <a class="kp-footer__top" href="#top"><?php echo kompass_icon('up'); ?><span>Nach oben</span></a>
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
