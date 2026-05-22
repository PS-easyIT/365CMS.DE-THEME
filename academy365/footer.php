<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$safe = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
try { $c = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable $e) { $c = null; }
$copyrightText = $c ? $c->get('footer', 'copyright_text', '') : '';
$footerText = $c ? $c->get('footer', 'footer_text', 'Lernen ohne Grenzen. Starte noch heute.') : 'Lernen ohne Grenzen. Starte noch heute.';
$siteTitle     = \CMS\ThemeManager::instance()->getSiteTitle();
$siteUrl       = rtrim(academy365_safe_url((string) SITE_URL, '/'), '/');
$siteUrl       = $siteUrl !== '' ? $siteUrl : '/';
$year          = date('Y');
$copyrightLine = str_replace(
    ['{year}', '{site_title}'],
    [$year, $siteTitle],
    (string) $copyrightText
);
?>
</div><!-- #content .ac-site-content -->
<footer id="colophon" class="ac-site-footer" role="contentinfo">
    <div class="ac-footer-inner ac-container">
        <div class="ac-footer-brand">
            <a href="<?php echo $safe($siteUrl); ?>" class="ac-footer-logo">
                <span aria-hidden="true">🎓</span><?php echo $safe($siteTitle); ?>
            </a>
            <p class="ac-footer-tagline"><?php echo $safe((string) $footerText); ?></p>
        </div>
        <nav class="ac-footer-nav" aria-label="Fußzeilen-Navigation">
            <div class="ac-footer-col">
                <h4>Für Lernende</h4>
                <ul>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/courses', $siteUrl)); ?>">Alle Kurse</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/categories', $siteUrl)); ?>">Kategorien</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/certificates', $siteUrl)); ?>">Zertifikate</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/pro', $siteUrl)); ?>">Academy365 Pro</a></li>
                </ul>
            </div>
            <div class="ac-footer-col">
                <h4>Für Tutoren</h4>
                <ul>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/tutor-register', $siteUrl)); ?>">Tutor werden</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/tutor-guide', $siteUrl)); ?>">Tutor-Leitfaden</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/earnings', $siteUrl)); ?>">Verdienst-Rechner</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/tutor-faq', $siteUrl)); ?>">FAQ Tutoren</a></li>
                </ul>
            </div>
            <div class="ac-footer-col">
                <h4>Unternehmen</h4>
                <ul>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/business', $siteUrl)); ?>">Business-Plan</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/enterprise', $siteUrl)); ?>">Enterprise</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/partner', $siteUrl)); ?>">Partner werden</a></li>
                </ul>
            </div>
            <div class="ac-footer-col">
                <h4>Plattform</h4>
                <ul>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/about', $siteUrl)); ?>">Über uns</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/blog', $siteUrl)); ?>">Blog</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/contact', $siteUrl)); ?>">Kontakt</a></li>
                    <li><a href="<?php echo $safe(academy365_safe_url($siteUrl . '/help', $siteUrl)); ?>">Hilfe</a></li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="ac-footer-bottom">
        <div class="ac-container">
            <p>
                <?php if ($copyrightText && trim($copyrightText) !== '') : ?>
                    <?php echo $safe($copyrightLine); ?>
                <?php else : ?>
                    &copy; <?php echo $year; ?> <?php echo $safe($siteTitle); ?>. Alle Rechte vorbehalten.
                <?php endif; ?>
            </p>
            <?php theme_nav_menu('footer-legal'); ?>
        </div>
    </div>
</footer><!-- #colophon -->
</div><!-- #page .ac-page-wrapper -->
<?php \CMS\Hooks::doAction('before_footer'); ?>
</body>
</html>
