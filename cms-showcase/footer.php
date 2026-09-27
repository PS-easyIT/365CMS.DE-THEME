<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Footer mit Abschluss-Band
 *
 * @package Showcase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Der Core rendert den Footer in ThemeManager::render(); doppelte Einbindung verhindern.
if (defined('CMS_THEME_FOOTER_RENDERED')) {
    return;
}
define('CMS_THEME_FOOTER_RENDERED', true);

$brandName = showcase_brand_name();
$logoUrl = showcase_logo();
$core = showcase_core_version();
$githubUrl = showcase_safe_url(showcase_text('header', 'github_url', 'https://github.com/PS-easyIT/365CMS.DE'), '');
$tagline = showcase_text('footer', 'footer_tagline');
$copyright = strtr(showcase_text('footer', 'footer_copyright', '© {year} {site_title}'), [
    '{year}' => date('Y'),
    '{site_title}' => showcase_site_title(),
]);
// Auf Fehlerseiten bleibt der Fokus auf Suche und Startseite – dort kein Abschluss-Band.
$showCta = showcase_flag('sc_cta', 'show_cta', true) && showcase_text('sc_cta', 'cta_heading') !== '' && http_response_code() < 400;
?>
</main>

<?php if ($showCta) : ?>
    <section class="sc-ctaband" aria-labelledby="sc-ctaband-title">
        <div class="sc-container sc-ctaband__inner" data-sc-reveal>
            <div class="sc-ctaband__text">
                <h2 class="sc-ctaband__title" id="sc-ctaband-title"><?php echo showcase_highlight(showcase_text('sc_cta', 'cta_heading')); ?></h2>
                <?php if (showcase_text('sc_cta', 'cta_text') !== '') : ?>
                    <p><?php echo showcase_e(showcase_text('sc_cta', 'cta_text')); ?></p>
                <?php endif; ?>
            </div>
            <div class="sc-ctaband__actions">
                <?php echo showcase_button(showcase_text('sc_cta', 'cta_primary_label'), showcase_text('sc_cta', 'cta_primary_url'), 'light', 'rocket'); ?>
                <?php echo showcase_button(showcase_text('sc_cta', 'cta_secondary_label'), showcase_text('sc_cta', 'cta_secondary_url'), 'outline-light', 'github'); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<footer class="sc-footer">
    <div class="sc-container sc-footer__grid">
        <div class="sc-footer__brand">
            <a class="sc-brand" href="<?php echo showcase_e(showcase_url('/')); ?>" rel="home">
                <?php if ($logoUrl !== '') : ?>
                    <img class="sc-brand__logo" src="<?php echo showcase_e($logoUrl); ?>" alt="" width="36" height="36" loading="lazy">
                <?php else : ?>
                    <span class="sc-brand__mark" aria-hidden="true"><?php echo showcase_icon('layers'); ?></span>
                <?php endif; ?>
                <span class="sc-brand__name"><?php echo showcase_e($brandName); ?></span>
            </a>
            <?php if ($tagline !== '') : ?>
                <p class="sc-footer__tagline"><?php echo showcase_e($tagline); ?></p>
            <?php endif; ?>
            <p class="sc-footer__meta">
                <?php if (showcase_flag('footer', 'show_footer_version', true) && $core['version'] !== '') : ?>
                    <span class="sc-version"><span class="sc-version__dot" aria-hidden="true"></span>Version <?php echo showcase_e($core['version']); ?><?php echo $core['status'] !== '' ? ' · ' . showcase_e($core['status']) : ''; ?></span>
                <?php endif; ?>
                <?php if ($githubUrl !== '') : ?>
                    <a class="sc-iconlink sc-iconlink--footer" href="<?php echo showcase_e($githubUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo showcase_icon('github'); ?><span class="sc-visually-hidden">365CMS auf GitHub (öffnet in neuem Tab)</span></a>
                <?php endif; ?>
            </p>
        </div>

        <nav class="sc-footer__col" aria-labelledby="sc-footer-product">
            <h2 class="sc-footer__title" id="sc-footer-product">Produkt</h2>
            <?php showcase_nav_menu('footer-product', 'sc-footer__list', showcase_default_menu('footer-product')); ?>
        </nav>

        <nav class="sc-footer__col" aria-labelledby="sc-footer-resources">
            <h2 class="sc-footer__title" id="sc-footer-resources">Ressourcen</h2>
            <?php showcase_nav_menu('footer-resources', 'sc-footer__list', showcase_default_menu('footer-resources')); ?>
        </nav>

        <nav class="sc-footer__col" aria-labelledby="sc-footer-legal">
            <h2 class="sc-footer__title" id="sc-footer-legal">Rechtliches</h2>
            <?php showcase_nav_menu('footer-legal', 'sc-footer__list', showcase_default_menu('footer-legal')); ?>
        </nav>
    </div>

    <div class="sc-container sc-footer__bottom">
        <p><?php echo showcase_e($copyright); ?></p>
        <p class="sc-footer__made">Mit <span class="sc-grad-text">365CMS</span> gebaut – sicher, schnell und selbst gehostet.</p>
        <a class="sc-footer__top" href="#top"><?php echo showcase_icon('up'); ?><span>Nach oben</span></a>
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
