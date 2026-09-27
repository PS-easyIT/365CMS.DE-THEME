<?php
declare(strict_types=1);

/**
 * Kontor – Footer mit Kontakt-Band
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Der Core rendert den Footer in ThemeManager::render(); doppelte Einbindung verhindern.
if (defined('CMS_THEME_FOOTER_RENDERED')) {
    return;
}
define('CMS_THEME_FOOTER_RENDERED', true);

$company = kontor_company_name();
$contact = kontor_contact();
$socials = kontor_social_links();
$copyright = strtr(kontor_text('footer', 'footer_copyright', '© {year} {site_title}'), [
    '{year}' => date('Y'),
    '{site_title}' => $company,
]);
$services = kontor_services();
?>
</main>

<?php if (kontor_flag('kt_cta', 'show_cta', true)) :
    $ctaHeading = kontor_text('kt_cta', 'cta_heading', 'Lassen Sie uns über Ihr Vorhaben sprechen.');
    ?>
    <section class="kt-ctaband" aria-labelledby="kt-ctaband-title">
        <div class="kt-container kt-ctaband__inner">
            <div class="kt-ctaband__text">
                <h2 class="kt-ctaband__title" id="kt-ctaband-title"><?php echo kontor_highlight($ctaHeading); ?></h2>
                <p><?php echo kontor_e(kontor_text('kt_cta', 'cta_text')); ?></p>
            </div>
            <div class="kt-ctaband__actions">
                <?php echo kontor_button(kontor_text('kt_cta', 'cta_label', 'Termin vereinbaren'), kontor_text('kt_cta', 'cta_url', '/kontakt'), 'accent', true); ?>
                <?php if ($contact['phone_href'] !== '') : ?>
                    <a class="kt-ctaband__phone" href="<?php echo kontor_e($contact['phone_href']); ?>"><?php echo kontor_icon('phone'); ?><span><?php echo kontor_e($contact['phone']); ?></span></a>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<footer class="kt-footer">
    <div class="kt-container kt-footer__grid">
        <div class="kt-footer__brand">
            <a class="kt-brand kt-brand--light" href="<?php echo kontor_e(kontor_url('/')); ?>" rel="home">
                <span class="kt-brand__mark" aria-hidden="true"><?php echo kontor_e(kontor_initials($company)); ?></span>
                <span class="kt-brand__name"><?php echo kontor_e($company); ?></span>
            </a>
            <?php if (kontor_text('footer', 'footer_tagline') !== '') : ?>
                <p class="kt-footer__tagline"><?php echo kontor_e(kontor_text('footer', 'footer_tagline')); ?></p>
            <?php endif; ?>
            <?php if ($socials !== []) : ?>
                <ul class="kt-footer__social" aria-label="Soziale Netzwerke">
                    <?php foreach ($socials as $social) : ?>
                        <li><a href="<?php echo kontor_e($social['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo kontor_icon($social['icon']); ?><span class="kt-visually-hidden"><?php echo kontor_e($social['label']); ?></span></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <nav class="kt-footer__col" aria-label="Unternehmen">
            <h2 class="kt-footer__title">Unternehmen</h2>
            <?php kontor_nav_menu('footer-nav', 'kt-footer__list', kontor_default_menu('footer-nav')); ?>
        </nav>

        <nav class="kt-footer__col" aria-label="Leistungen">
            <h2 class="kt-footer__title">Leistungen</h2>
            <?php
            $serviceFallback = array_map(static fn(array $s): array => ['label' => $s['title'], 'url' => $s['url'] !== '' ? $s['url'] : '/#leistungen'], array_slice($services, 0, 6));
            kontor_nav_menu('footer-services', 'kt-footer__list', $serviceFallback);
            ?>
        </nav>

        <div class="kt-footer__col">
            <h2 class="kt-footer__title">Kontakt</h2>
            <ul class="kt-contactlist kt-contactlist--footer">
                <?php if ($contact['address'] !== []) : ?>
                    <li><?php echo kontor_icon('pin'); ?><address><?php echo kontor_e($company); ?><br><?php echo implode('<br>', array_map('kontor_e', $contact['address'])); ?></address></li>
                <?php endif; ?>
                <?php if ($contact['phone_href'] !== '') : ?>
                    <li><?php echo kontor_icon('phone'); ?><a href="<?php echo kontor_e($contact['phone_href']); ?>"><?php echo kontor_e($contact['phone']); ?></a></li>
                <?php endif; ?>
                <?php if ($contact['email_href'] !== '') : ?>
                    <li><?php echo kontor_icon('mail'); ?><a href="<?php echo kontor_e($contact['email_href']); ?>"><?php echo kontor_e($contact['email']); ?></a></li>
                <?php endif; ?>
                <?php if ($contact['hours'] !== []) : ?>
                    <li><?php echo kontor_icon('clock'); ?><span><?php echo implode('<br>', array_map('kontor_e', $contact['hours'])); ?></span></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="kt-footer__bottom">
        <div class="kt-container kt-footer__bottom-inner">
            <p><?php echo kontor_e($copyright); ?></p>
            <nav aria-label="Rechtliche Links">
                <?php kontor_nav_menu('footer-legal', 'kt-footer__legal', kontor_default_menu('footer-legal')); ?>
            </nav>
            <a class="kt-footer__top" href="#main-content"><?php echo kontor_icon('up'); ?><span>Nach oben</span></a>
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
