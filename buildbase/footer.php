<?php
if (!defined('ABSPATH')) {
    exit;
}

$themeManager = \CMS\ThemeManager::instance();
$siteTitle    = (string) $themeManager->getSiteTitle();
$isLoggedIn   = theme_is_logged_in();
$siteUrl      = SITE_URL;

try {
    $c            = \CMS\Services\ThemeCustomizer::instance();
    $footerText   = (string) $c->get('footer', 'footer_text', 'Qualität aus Handwerkerhand.');
    $showLinks    = filter_var($c->get('footer', 'footer_show_crafts', true), FILTER_VALIDATE_BOOLEAN);
    $copyrightTpl = (string) $c->get('footer', 'copyright_text', '&copy; {year} {site_title}. Alle Rechte vorbehalten.');
} catch (\Throwable $e) {
    $footerText   = '';
    $showLinks    = true;
    $copyrightTpl = '&copy; {year} {site_title}.';
}

$safe = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

$copyright = str_replace(
    ['{year}', '{site_title}'],
    [gmdate('Y'), $safe($siteTitle)],
    $copyrightTpl
);

$baseUrl = rtrim($siteUrl, '/');
?>
    </div><!-- #content -->

    <footer id="bb-footer" class="bb-footer" role="contentinfo">
        <div class="bb-container">
            <div class="bb-footer-widgets">
                <div>
                    <h3><?php echo $safe($siteTitle); ?></h3>
                    <?php if ($footerText !== '') : ?>
                        <p><?php echo nl2br($safe($footerText)); ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($showLinks) : ?>
                    <div>
                        <h3>Handwerk</h3>
                        <ul>
                            <li><a href="<?php echo $safe($baseUrl . '/handwerker'); ?>">Handwerker finden</a></li>
                            <li><a href="<?php echo $safe($baseUrl . '/baufirmen'); ?>">Baufirmen</a></li>
                            <li><a href="<?php echo $safe($baseUrl . '/angebot'); ?>">Angebot anfragen</a></li>
                            <li><a href="<?php echo $safe($baseUrl . '/projekte'); ?>">Referenzprojekte</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3>Für Fachbetriebe</h3>
                        <ul>
                            <li><a href="<?php echo $safe($baseUrl . '/register'); ?>">Profil anlegen</a></li>
                            <li><a href="<?php echo $safe($baseUrl . '/pricing'); ?>">Pakete &amp; Preise</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="bb-footer-bottom">
            <div class="bb-container"><?php echo $copyright; ?></div>
        </div>
    </footer>

</div><!-- #page -->
<?php \CMS\Hooks::doAction('before_footer'); ?>
</body>
</html>
