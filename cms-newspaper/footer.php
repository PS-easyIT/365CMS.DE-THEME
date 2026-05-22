<?php
/**
 * CMS Newspaper Theme – Footer
 *
 * @package CmsNewspaper_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe       = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteTitle  = news_site_title();
$brandRaw   = (string) CmsNewspaper_Theme::instance()->getConfig('masthead_brand', 'PHIN<span>IT</span>.DE');

$tagline    = (string) news_get_setting(
    'footer',
    'footer_tagline',
    (string) CmsNewspaper_Theme::instance()->getConfig('footer_tagline', '')
);

$copyTpl    = (string) news_get_setting(
    'footer',
    'footer_copyright',
    '© {year} {site_title} // designed & developed with 365CMS.DE'
);

$showPartner = filter_var(
    news_get_setting('footer', 'show_partner_strip', true),
    FILTER_VALIDATE_BOOLEAN
);

$copyText = strtr($copyTpl, [
    '{year}'       => gmdate('Y'),
    '{site_title}' => $siteTitle,
]);

// Footer partner strip is purely cosmetic; defaults mirror prototype.
$partners = [
    ['label' => 'Andreas Hepp',     'url' => 'https://andreas-hepp.de'],
    ['label' => '365network.de',    'url' => 'https://365network.de'],
    ['label' => 'phscripts.de',     'url' => 'https://phscripts.de'],
    ['label' => 'ms365insights.de', 'url' => 'https://ms365insights.de'],
    ['label' => 'setupdates.com',   'url' => 'https://setupdates.com'],
    ['label' => 'servertrend.com',  'url' => 'https://servertrend.com'],
    ['label' => 'phun.network',     'url' => 'https://phun.network'],
];
?>
    </main><!-- /#main-content -->

    <footer class="news-footer" id="news-footer" role="contentinfo">

        <div class="news-footer-grid">

            <div class="news-footer-brand">
                <a href="<?php echo $safe(news_href('/')); ?>" class="news-footer-wordmark news-focus-shadow" rel="home" aria-label="<?php echo $safe($siteTitle); ?>">
                    <?php echo news_brand_html($brandRaw); ?>
                </a>
                <?php if (trim($tagline) !== '') : ?>
                    <p><?php echo $safe($tagline); ?></p>
                <?php endif; ?>
            </div>

            <div class="news-footer-col">
                <h4>Themen</h4>
                <?php theme_nav_menu('footer-nav'); ?>
            </div>

            <div class="news-footer-col">
                <h4>Ressourcen</h4>
                <ul>
                    <li><a href="<?php echo $safe(news_href('/script-hub')); ?>">M365 Script Hub</a></li>
                    <li><a href="<?php echo $safe(news_href('/checklisten')); ?>">Admin Checklisten</a></li>
                    <li><a href="<?php echo $safe(news_href('/wissen')); ?>">Knowledge Base</a></li>
                    <li><a href="<?php echo $safe(news_href('/events')); ?>">Event Kalender</a></li>
                </ul>
            </div>

            <div class="news-footer-col">
                <h4>Portal</h4>
                <ul>
                    <li><a href="<?php echo $safe(news_href('/autoren')); ?>">Über uns (Autoren)</a></li>
                    <li><a href="<?php echo $safe(news_href('/impressum')); ?>">Impressum</a></li>
                    <li><a href="<?php echo $safe(news_href('/datenschutz')); ?>">Datenschutz</a></li>
                    <li><a href="<?php echo $safe(news_href('/kontakt')); ?>">Kontakt</a></li>
                </ul>
            </div>

        </div>

        <?php if ($showPartner) : ?>
            <div class="news-partner-strip" aria-label="Partnerseiten">
                <?php foreach ($partners as $p) :
                    $pUrl   = (string) ($p['url']   ?? '#');
                    $pLabel = (string) ($p['label'] ?? '');
                    if ($pLabel === '') { continue; }
                    ?>
                    <a href="<?php echo $safe($pUrl); ?>" target="_blank" rel="noopener noreferrer"><?php echo $safe($pLabel); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="news-footer-copy">
            <span><?php echo $safe($copyText); ?></span>
            <nav class="news-footer-legal" aria-label="Rechtliche Links">
                <?php theme_nav_menu('footer-legal'); ?>
            </nav>
        </div>

    </footer>

<?php \CMS\Hooks::doAction('before_footer'); ?>

</body>
</html>
