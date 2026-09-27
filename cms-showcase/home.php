<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Startseite (Produktseite)
 *
 * Hero mit Dashboard-Vorschau und Kennzahlen → Funktionen + Module → Rollen-Tabs
 * → Sicherheit → Entwickler → Theme-Galerie (installierte Themes) → Release Notes + Blog
 * → Installation → FAQ. Das Abschluss-Band folgt im Footer.
 *
 * @package Showcase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$core = showcase_core_version();
$heroImage = showcase_media_url(showcase_text('sc_hero', 'hero_image'));
$trustPoints = showcase_lines(showcase_text('sc_hero', 'trust_points'));
$logoUrl = showcase_logo();

$stats = showcase_numbered('sc_stats', 'stat', 4, ['value' => '', 'label' => ''], 'value');
$features = showcase_numbered('sc_features', 'feature', 9, ['title' => '', 'text' => '', 'icon' => 'blocks'], 'title');
$modules = showcase_modules();
$tabs = showcase_numbered('sc_roles', 'tab', 3, ['label' => '', 'title' => '', 'text' => '', 'points' => ''], 'label');
$tabIcons = ['blocks', 'gauge', 'code'];

$showSecurity = showcase_flag('sc_security', 'show_security', true);
$showDev = showcase_flag('sc_dev', 'show_dev', true);

$showThemes = showcase_flag('sc_themes', 'show_themes', true);
$themes = $showThemes ? showcase_installed_themes(showcase_int('sc_themes', 'themes_count', 8, 0, 24)) : [];

$releases = showcase_numbered('sc_releases', 'release', 3, ['version' => '', 'date' => '', 'items' => ''], 'version');
$newsCount = showcase_flag('sc_releases', 'show_news', true) ? showcase_int('sc_releases', 'news_count', 3, 0, 6) : 0;
$news = $newsCount > 0 ? showcase_get_posts(['limit' => $newsCount]) : [];

$requirements = showcase_lines(showcase_text('sc_install', 'requirements'));
$steps = showcase_lines(showcase_text('sc_install', 'steps'));

$faqs = showcase_flag('sc_faq', 'show_faq', true) ? showcase_numbered('sc_faq', 'faq', 6, ['q' => '', 'a' => ''], 'q') : [];
$faqs = array_values(array_filter($faqs, static fn(array $faq): bool => $faq['a'] !== ''));
?>

<section class="sc-hero" aria-labelledby="sc-hero-title">
    <div class="sc-hero__bg" aria-hidden="true"></div>
    <div class="sc-container sc-hero__inner">
        <div class="sc-hero__content">
            <?php if (showcase_flag('sc_hero', 'show_version', true) && $core['version'] !== '') : ?>
                <p class="sc-hero__badge"><span class="sc-version__dot" aria-hidden="true"></span>Version <?php echo showcase_e($core['version']); ?><?php echo $core['status'] !== '' ? ' · ' . showcase_e($core['status']) : ''; ?></p>
            <?php endif; ?>
            <?php if (showcase_text('sc_hero', 'hero_eyebrow') !== '') : ?>
                <p class="sc-eyebrow sc-eyebrow--light"><?php echo showcase_e(showcase_text('sc_hero', 'hero_eyebrow')); ?></p>
            <?php endif; ?>
            <h1 class="sc-hero__title" id="sc-hero-title"><?php echo showcase_highlight(showcase_text('sc_hero', 'hero_title', 'Das *sichere* CMS für Websites, Portale und Mitglieder.')); ?></h1>
            <?php if (showcase_text('sc_hero', 'hero_text') !== '') : ?>
                <p class="sc-hero__text"><?php echo showcase_e(showcase_text('sc_hero', 'hero_text')); ?></p>
            <?php endif; ?>
            <div class="sc-hero__actions">
                <?php echo showcase_button(showcase_text('sc_hero', 'primary_label'), showcase_text('sc_hero', 'primary_url'), 'primary', 'arrow'); ?>
                <?php echo showcase_button(showcase_text('sc_hero', 'secondary_label'), showcase_text('sc_hero', 'secondary_url'), 'ghost-light', ''); ?>
            </div>
            <?php if ($trustPoints !== []) : ?>
                <ul class="sc-hero__trust">
                    <?php foreach ($trustPoints as $point) : ?>
                        <li><?php echo showcase_icon('check'); ?><span><?php echo showcase_e($point); ?></span></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="sc-hero__visual">
            <?php if ($heroImage !== '') : ?>
                <figure class="sc-shot">
                    <img src="<?php echo showcase_e($heroImage); ?>" alt="Screenshot des 365CMS-Administrationsbereichs" width="1280" height="820" loading="eager" decoding="async" fetchpriority="high">
                </figure>
            <?php else : ?>
                <div class="sc-mock" role="img" aria-label="Vorschau des 365CMS-Dashboards mit Kennzahlen, Systemstatus sowie Sicherheit und Performance">
                    <div class="sc-mock__chrome">
                        <span class="sc-mock__dots"><i></i><i></i><i></i></span>
                        <span class="sc-mock__url">ihre-domain.de/admin</span>
                    </div>
                    <div class="sc-mock__app">
                        <div class="sc-mock__side">
                            <div class="sc-mock__brand">
                                <?php if ($logoUrl !== '') : ?><img src="<?php echo showcase_e($logoUrl); ?>" alt="" width="18" height="18"><?php endif; ?>
                                <b>365CMS</b>
                            </div>
                            <ul class="sc-mock__menu">
                                <li class="is-active">Dashboard</li>
                                <li>Seiten &amp; Beiträge</li>
                                <li>Medien</li>
                                <li>Benutzer &amp; Gruppen</li>
                                <li>Themes &amp; Gestaltung</li>
                                <li>SEO</li>
                                <li>Performance</li>
                                <li>Sicherheit</li>
                                <li>Plugins</li>
                                <li>System</li>
                            </ul>
                        </div>
                        <div class="sc-mock__main">
                            <div class="sc-mock__top">
                                <div><small>Übersicht</small><b>Dashboard</b></div>
                                <span class="sc-mock__btn">+ Neue Seite</span>
                            </div>
                            <div class="sc-mock__kpis">
                                <div><small>Seiten</small><b>48</b></div>
                                <div><small>Beiträge</small><b>312</b></div>
                                <div><small>Medien</small><b>1.204</b></div>
                                <div><small>Mitglieder</small><b>2.310</b></div>
                            </div>
                            <div class="sc-mock__row">
                                <div class="sc-mock__card">
                                    <small>Systemstatus</small>
                                    <ul>
                                        <li><span>PHP</span><b>8.4</b></li>
                                        <li><span>CMS</span><b><?php echo showcase_e($core['version'] !== '' ? $core['version'] : '3.4'); ?></b></li>
                                        <li><span>Datenbank</span><b>MariaDB</b></li>
                                    </ul>
                                </div>
                                <div class="sc-mock__card">
                                    <small>Sicherheit &amp; Performance</small>
                                    <p class="sc-mock__score"><b>90</b>/100</p>
                                    <p class="sc-mock__meter"><i></i></p>
                                    <p class="sc-mock__tags"><span>HTTPS</span><span>CSP</span><span>MFA</span></p>
                                </div>
                            </div>
                            <div class="sc-mock__chart"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                        </div>
                    </div>
                </div>
                <p class="sc-float sc-float--1" aria-hidden="true"><?php echo showcase_icon('shield'); ?>CSP + Trusted Types</p>
                <p class="sc-float sc-float--2" aria-hidden="true"><?php echo showcase_icon('key'); ?>Passkey-Login</p>
                <p class="sc-float sc-float--3" aria-hidden="true"><?php echo showcase_icon('gauge'); ?>Core Web Vitals</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($stats !== []) : ?>
        <div class="sc-container">
            <dl class="sc-stats">
                <?php foreach ($stats as $stat) : ?>
                    <div class="sc-stats__item">
                        <dt><?php echo showcase_e($stat['label']); ?></dt>
                        <dd><?php echo showcase_e($stat['value']); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </div>
    <?php endif; ?>
</section>

<?php if ($features !== []) : ?>
    <section class="sc-section" id="funktionen" aria-labelledby="sc-features-title">
        <div class="sc-container">
            <?php showcase_section_head(showcase_text('sc_features', 'features_eyebrow'), showcase_text('sc_features', 'features_heading', 'Alles an Bord.'), showcase_text('sc_features', 'features_text'), 'sc-features-title'); ?>
            <ul class="sc-features">
                <?php foreach ($features as $feature) : ?>
                    <li class="sc-feature" data-sc-reveal>
                        <span class="sc-feature__icon"><?php echo showcase_icon($feature['icon']); ?></span>
                        <h3 class="sc-feature__title"><?php echo showcase_e($feature['title']); ?></h3>
                        <?php if ($feature['text'] !== '') : ?>
                            <p class="sc-feature__text"><?php echo showcase_e($feature['text']); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($modules !== []) : ?>
                <div class="sc-modules" data-sc-reveal>
                    <p class="sc-modules__title" id="sc-modules-title"><?php echo showcase_e(showcase_text('sc_features', 'modules_heading', 'Und das ist erst der Anfang:')); ?></p>
                    <ul class="sc-modules__list" aria-labelledby="sc-modules-title">
                        <?php foreach ($modules as $module) : ?>
                            <li><?php echo showcase_e($module); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($tabs !== []) : ?>
    <section class="sc-section sc-section--surface" id="rollen" aria-labelledby="sc-roles-title">
        <div class="sc-container">
            <?php showcase_section_head(showcase_text('sc_roles', 'roles_eyebrow'), showcase_text('sc_roles', 'roles_heading', 'Ein System, drei Perspektiven.'), '', 'sc-roles-title'); ?>
            <div class="sc-tabs" data-sc-tabs>
                <div class="sc-tabs__list sc-js-only" role="tablist" aria-label="Perspektiven">
                    <?php foreach ($tabs as $index => $tab) : ?>
                        <button type="button" class="sc-tabs__tab" role="tab" id="sc-tab-<?php echo $index + 1; ?>" aria-controls="sc-panel-<?php echo $index + 1; ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" tabindex="<?php echo $index === 0 ? '0' : '-1'; ?>">
                            <?php echo showcase_icon($tabIcons[$index] ?? 'layers'); ?><span><?php echo showcase_e($tab['label']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php foreach ($tabs as $index => $tab) :
                    $points = showcase_lines($tab['points']);
                    ?>
                    <div class="sc-tabs__panel<?php echo $index === 0 ? ' is-active' : ''; ?>" role="tabpanel" id="sc-panel-<?php echo $index + 1; ?>" aria-labelledby="sc-tab-<?php echo $index + 1; ?>" tabindex="0">
                        <div class="sc-tabs__text">
                            <p class="sc-tabs__kicker"><?php echo showcase_icon($tabIcons[$index] ?? 'layers'); ?><?php echo showcase_e($tab['label']); ?></p>
                            <h3 class="sc-tabs__title"><?php echo showcase_e($tab['title']); ?></h3>
                            <?php if ($tab['text'] !== '') : ?>
                                <p><?php echo showcase_e($tab['text']); ?></p>
                            <?php endif; ?>
                            <?php if ($points !== []) : ?>
                                <ul class="sc-checklist">
                                    <?php foreach ($points as $point) : ?>
                                        <li><?php echo showcase_icon('check'); ?><span><?php echo showcase_e($point); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="sc-tabs__visual sc-tabs__visual--<?php echo $index + 1; ?>" aria-hidden="true">
                            <?php if ($index === 0) : ?>
                                <div class="sc-mini sc-mini--editor">
                                    <span class="sc-mini__h"></span>
                                    <span class="sc-mini__p"></span><span class="sc-mini__p"></span><span class="sc-mini__p sc-mini__p--short"></span>
                                    <span class="sc-mini__img"><?php echo showcase_icon('palette'); ?></span>
                                    <span class="sc-mini__p"></span><span class="sc-mini__p sc-mini__p--short"></span>
                                    <span class="sc-mini__toolbar"><b>+</b><i>Absatz</i><i>Überschrift</i><i>Bild</i><i>Tabelle</i></span>
                                </div>
                            <?php elseif ($index === 1) : ?>
                                <div class="sc-mini sc-mini--rights">
                                    <b class="sc-mini__caption">Rollen &amp; Rechte</b>
                                    <span class="sc-mini__right"><i>Beiträge veröffentlichen</i><em class="is-on"></em></span>
                                    <span class="sc-mini__right"><i>Medien verwalten</i><em class="is-on"></em></span>
                                    <span class="sc-mini__right"><i>Benutzer verwalten</i><em></em></span>
                                    <span class="sc-mini__right"><i>Einstellungen ändern</i><em></em></span>
                                    <span class="sc-mini__right"><i>Protokolle einsehen</i><em class="is-on"></em></span>
                                </div>
                            <?php else : ?>
                                <div class="sc-mini sc-mini--code">
                                    <span class="sc-mini__line"><i class="k"></i><i class="f"></i><i class="s"></i></span>
                                    <span class="sc-mini__line sc-mini__line--in"><i class="v"></i><i class="s"></i></span>
                                    <span class="sc-mini__line sc-mini__line--in"><i class="k"></i><i class="v"></i></span>
                                    <span class="sc-mini__line"><i class="c"></i></span>
                                    <span class="sc-mini__line"><i class="k"></i><i class="f"></i></span>
                                    <span class="sc-mini__line sc-mini__line--in"><i class="s"></i><i class="v"></i><i class="s"></i></span>
                                    <span class="sc-mini__line"><i class="k"></i></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($showSecurity) :
    $securityPoints = showcase_lines(showcase_text('sc_security', 'security_points'));
    ?>
    <section class="sc-section sc-section--dark" id="sicherheit" aria-labelledby="sc-security-title">
        <div class="sc-section__glow" aria-hidden="true"></div>
        <div class="sc-container sc-split">
            <div class="sc-split__text">
                <?php showcase_section_head(showcase_text('sc_security', 'security_eyebrow'), showcase_text('sc_security', 'security_heading', 'Sicherheit ist kein Plugin.'), showcase_text('sc_security', 'security_text'), 'sc-security-title', 'left'); ?>
                <?php if ($securityPoints !== []) : ?>
                    <ul class="sc-checklist sc-checklist--light sc-checklist--grid" data-sc-reveal>
                        <?php foreach ($securityPoints as $point) : ?>
                            <li><?php echo showcase_icon('check'); ?><span><?php echo showcase_e($point); ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="sc-split__visual" data-sc-reveal>
                <figure class="sc-window sc-window--headers">
                    <figcaption class="sc-window__bar">
                        <span class="sc-window__dots" aria-hidden="true"><span></span><span></span><span></span></span>
                        <span class="sc-window__title"><?php echo showcase_icon('shield'); ?>HTTP-Antwort von 365CMS</span>
                    </figcaption>
<pre class="sc-window__body" tabindex="0" aria-label="Beispiel der Sicherheits-Header einer 365CMS-Antwort"><code><span class="sc-tok-k">HTTP/2</span> <span class="sc-tok-n">200</span>
<span class="sc-tok-x">content-security-policy</span>: default-src <span class="sc-tok-s">'self'</span>;
  script-src <span class="sc-tok-s">'self'</span> <span class="sc-tok-v">'nonce-…'</span>;
  style-src <span class="sc-tok-s">'self'</span> <span class="sc-tok-v">'nonce-…'</span>;
  object-src <span class="sc-tok-s">'none'</span>; frame-ancestors <span class="sc-tok-s">'none'</span>;
  trusted-types cms365 default dompurify;
  require-trusted-types-for <span class="sc-tok-s">'script'</span>
<span class="sc-tok-x">x-content-type-options</span>: nosniff
<span class="sc-tok-x">x-frame-options</span>: DENY
<span class="sc-tok-x">referrer-policy</span>: strict-origin-when-cross-origin
<span class="sc-tok-x">cross-origin-opener-policy</span>: same-origin</code></pre>
                </figure>
                <ul class="sc-badges" aria-label="Weitere Schutzmechanismen">
                    <li><?php echo showcase_icon('key'); ?>TOTP &amp; Passkeys</li>
                    <li><?php echo showcase_icon('lock'); ?>CSRF-Tokens</li>
                    <li><?php echo showcase_icon('chart'); ?>Audit-Log</li>
                </ul>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($showDev) :
    $docs = [];
    for ($i = 1; $i <= 3; $i++) {
        $label = showcase_text('sc_dev', 'doc_' . $i . '_label');
        $url = showcase_safe_url(showcase_text('sc_dev', 'doc_' . $i . '_url'), '');
        if ($label !== '' && $url !== '') {
            $docs[] = ['label' => $label, 'url' => str_starts_with($url, '#') ? $url : showcase_url($url)];
        }
    }
    ?>
    <section class="sc-section" id="entwickler" aria-labelledby="sc-dev-title">
        <div class="sc-container sc-split sc-split--dev">
            <div class="sc-split__text">
                <?php showcase_section_head(showcase_text('sc_dev', 'dev_eyebrow'), showcase_text('sc_dev', 'dev_heading', 'Erweitern, ohne den Kern anzufassen.'), showcase_text('sc_dev', 'dev_text'), 'sc-dev-title', 'left'); ?>
                <?php if ($docs !== []) : ?>
                    <ul class="sc-doclinks" data-sc-reveal>
                        <?php foreach ($docs as $doc) :
                            $external = showcase_is_external($doc['url']);
                            ?>
                            <li>
                                <a href="<?php echo showcase_e($doc['url']); ?>"<?php echo $external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                                    <span class="sc-doclinks__icon"><?php echo showcase_icon('book'); ?></span>
                                    <span class="sc-doclinks__label"><?php echo showcase_e($doc['label']); ?><?php echo $external ? '<span class="sc-visually-hidden"> (öffnet in neuem Tab)</span>' : ''; ?></span>
                                    <?php echo showcase_icon($external ? 'external' : 'arrow'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="sc-split__visual" data-sc-reveal>
                <?php showcase_code_window(showcase_text('sc_dev', 'code_sample'), showcase_text('sc_dev', 'code_filename'), 'sc-code-dev'); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if ($themes !== []) : ?>
    <section class="sc-section sc-section--surface" id="themes" aria-labelledby="sc-themes-title">
        <div class="sc-container">
            <?php showcase_section_head(showcase_text('sc_themes', 'themes_eyebrow'), showcase_text('sc_themes', 'themes_heading', 'Ein Kern, viele Gesichter.'), showcase_text('sc_themes', 'themes_text'), 'sc-themes-title'); ?>
            <style<?php echo theme_csp_nonce_attr(); ?>><?php
            foreach ($themes as $index => $theme) {
                echo '.sc-theme-' . ($index + 1) . '{--t1:' . $theme['palette'][0] . ';--t2:' . $theme['palette'][1] . ';--t3:' . $theme['palette'][2] . ';--t4:' . $theme['palette'][3] . '}';
            }
            ?></style>
            <ul class="sc-themes">
                <?php foreach ($themes as $index => $theme) : ?>
                    <li class="sc-themecard sc-theme-<?php echo $index + 1; ?>" data-sc-reveal>
                        <div class="sc-themecard__preview" aria-hidden="true">
                            <?php if ($theme['screenshot'] !== '') : ?>
                                <img src="<?php echo showcase_e($theme['screenshot']); ?>" alt="" width="640" height="400" loading="lazy" decoding="async">
                            <?php else : ?>
                                <span class="sc-themecard__nav"><i></i><i></i><i></i></span>
                                <span class="sc-themecard__hero"><i></i><i></i><i></i></span>
                                <span class="sc-themecard__cards"><i></i><i></i><i></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="sc-themecard__body">
                            <h3 class="sc-themecard__title"><?php echo showcase_e($theme['name']); ?></h3>
                            <?php if ($theme['version'] !== '') : ?>
                                <p class="sc-themecard__version">Version <?php echo showcase_e($theme['version']); ?></p>
                            <?php endif; ?>
                            <?php if ($theme['description'] !== '') : ?>
                                <p class="sc-themecard__text"><?php echo showcase_e($theme['description']); ?></p>
                            <?php endif; ?>
                            <?php if ($theme['tags'] !== []) : ?>
                                <ul class="sc-themecard__tags" aria-label="Schlagwörter">
                                    <?php foreach ($theme['tags'] as $tagName) : ?>
                                        <li><?php echo showcase_e($tagName); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php $themesLink = showcase_button(showcase_text('sc_themes', 'themes_link_label'), showcase_text('sc_themes', 'themes_link_url'), 'ghost', 'arrow'); ?>
            <?php if ($themesLink !== '') : ?>
                <p class="sc-section__more"><?php echo $themesLink; ?></p>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php if ($releases !== [] || $news !== []) : ?>
    <section class="sc-section" id="neuigkeiten" aria-labelledby="sc-releases-title">
        <div class="sc-container">
            <?php showcase_section_head(showcase_text('sc_releases', 'releases_eyebrow'), showcase_text('sc_releases', 'releases_heading', 'Was ist neu?'), '', 'sc-releases-title'); ?>
            <?php if ($releases !== []) : ?>
                <ol class="sc-releases">
                    <?php foreach ($releases as $index => $release) :
                        $items = showcase_lines($release['items']);
                        ?>
                        <li class="sc-release<?php echo $index === 0 ? ' sc-release--latest' : ''; ?>" data-sc-reveal>
                            <p class="sc-release__head">
                                <span class="sc-release__version">v<?php echo showcase_e(ltrim($release['version'], 'vV')); ?></span>
                                <?php if ($release['date'] !== '') : ?><span class="sc-release__date"><?php echo showcase_e($release['date']); ?></span><?php endif; ?>
                                <?php if ($index === 0) : ?><span class="sc-pill sc-pill--accent">Aktuell</span><?php endif; ?>
                            </p>
                            <?php if ($items !== []) : ?>
                                <ul class="sc-checklist sc-checklist--compact">
                                    <?php foreach ($items as $item) : ?>
                                        <li><?php echo showcase_icon('check'); ?><span><?php echo showcase_e($item); ?></span></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

            <?php if ($news !== []) : ?>
                <div class="sc-news">
                    <div class="sc-news__head">
                        <h3 class="sc-news__title"><?php echo showcase_e(showcase_text('sc_releases', 'news_heading', 'Aus dem Blog')); ?></h3>
                        <a class="sc-more" href="<?php echo showcase_e(showcase_url('/blog')); ?>"><span>Alle Beiträge</span><?php echo showcase_icon('arrow'); ?></a>
                    </div>
                    <div class="sc-postgrid">
                        <?php foreach ($news as $item) : ?>
                            <?php showcase_post_card($item, 'h4'); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section class="sc-section sc-section--surface" id="installation" aria-labelledby="sc-install-title">
    <div class="sc-container">
        <?php showcase_section_head(showcase_text('sc_install', 'install_eyebrow'), showcase_text('sc_install', 'install_heading', 'In wenigen Minuten startklar.'), showcase_text('sc_install', 'install_text'), 'sc-install-title'); ?>
        <div class="sc-install">
            <?php if ($steps !== []) : ?>
                <div class="sc-install__steps" data-sc-reveal>
                    <h3 class="sc-install__title">So geht's</h3>
                    <ol class="sc-steps">
                        <?php foreach ($steps as $step) : ?>
                            <li><?php echo showcase_e($step); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            <?php endif; ?>
            <div class="sc-install__side" data-sc-reveal>
                <?php showcase_terminal(showcase_text('sc_install', 'terminal'), 'sc-terminal-install'); ?>
                <?php if ($requirements !== []) : ?>
                    <div class="sc-install__card">
                        <h3 class="sc-install__title">Voraussetzungen</h3>
                        <ul class="sc-checklist sc-checklist--compact">
                            <?php foreach ($requirements as $requirement) : ?>
                                <li><?php echo showcase_icon('check'); ?><span><?php echo showcase_e($requirement); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <p class="sc-install__actions">
                    <?php echo showcase_button(showcase_text('sc_install', 'download_label'), showcase_text('sc_install', 'download_url'), 'primary', 'download'); ?>
                    <?php echo showcase_button(showcase_text('sc_install', 'docs_label'), showcase_text('sc_install', 'docs_url'), 'ghost', 'book'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<?php if ($faqs !== []) : ?>
    <section class="sc-section" id="faq" aria-labelledby="sc-faq-title">
        <div class="sc-container sc-faqwrap">
            <div class="sc-faqwrap__head" data-sc-reveal>
                <p class="sc-eyebrow">FAQ</p>
                <h2 class="sc-section__title" id="sc-faq-title"><?php echo showcase_highlight(showcase_text('sc_faq', 'faq_heading', 'Häufige Fragen')); ?></h2>
            </div>
            <div class="sc-faq" data-sc-reveal>
                <?php foreach ($faqs as $index => $faq) : ?>
                    <details class="sc-faq__item"<?php echo $index === 0 ? ' open' : ''; ?>>
                        <summary class="sc-faq__question"><?php echo showcase_e($faq['q']); ?></summary>
                        <div class="sc-faq__answer"><p><?php echo nl2br(showcase_e($faq['a'])); ?></p></div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
