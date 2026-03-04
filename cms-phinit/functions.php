<?php
/**
 * Theme-Funktionen – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 * @version 1.0.0
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('CMS_PHINIT_THEME_VERSION', '1.0.0');
define('CMS_PHINIT_THEME_DIR',     THEME_PATH . 'cms-phinit/');
define('CMS_PHINIT_THEME_URL',     rtrim(\CMS\ThemeManager::instance()->getThemeUrl(), '/') . '/');

/**
 * Theme-Hauptklasse (Singleton)
 */
final class CMS_Phinit_Theme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        // Assets
        \CMS\Hooks::addAction('head', [$this, 'outputPreconnect'],       1);
        \CMS\Hooks::addAction('head', [$this, 'outputGoogleFonts'],      5);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'],         8);
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],         15);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'],    20);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomHeaderCode'], 99);

        // Scripts ans Ende des Body
        \CMS\Hooks::addAction('body_end', [$this, 'enqueueScripts'],        10);
        \CMS\Hooks::addAction('body_end', [$this, 'outputCustomFooterCode'], 99);

        // Menüpositionen
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Standardmenüs beim ersten Start anlegen
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus'], 20);

        // Body-Class für aktuelle Seite anreichern
        \CMS\Hooks::addFilter('body_class', [$this, 'bodyClass']);
    }

    /* ── Assets ─────────────────────────────────────────────────── */

    public function enqueueStyles(): void
    {
        $cssFile = CMS_PHINIT_THEME_DIR . 'style.css';
        // Cache-Buster aus Customizer oder Datei-Timestamp
        $cbVersion = '';
        try {
            $cbVersion = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'cache_buster_css', '');
        } catch (\Throwable $e) {}
        $version = !empty(trim((string)$cbVersion)) ? $cbVersion : (file_exists($cssFile) ? filemtime($cssFile) : CMS_PHINIT_THEME_VERSION);
        echo '<link rel="stylesheet" href="' . CMS_PHINIT_THEME_URL . 'style.css?v=' . $version . '">' . "\n";

        // Phinit-spezifisches CSS aus Customizer generieren
        // (überschreibt die generische generateCSS()-Methode, die andere Key-Namen erwartet)
        try {
            $css = $this->generatePhinitCSS();
            if (!empty(trim($css))) {
                echo '<style id="cms-phinit-customizer-css">' . "\n" . $css . "\n" . '</style>' . "\n";
            }
        } catch (\Throwable $e) {
            // Kein Customizer-CSS – kein Fehler
        }
    }

    public function enqueueScripts(): void
    {
        $jsFile  = CMS_PHINIT_THEME_DIR . 'assets/js/navigation.js';
        $version = file_exists($jsFile) ? filemtime($jsFile) : CMS_PHINIT_THEME_VERSION;
        echo '<script src="' . CMS_PHINIT_THEME_URL . 'assets/js/navigation.js?v=' . $version . '" defer></script>' . "\n";
    }

    /* ── Meta Tags ──────────────────────────────────────────────── */

    public function outputMetaTags(): void
    {
        $tm = \CMS\ThemeManager::instance();
        echo '<meta name="description" content="' . htmlspecialchars($tm->getSiteDescription() ?? '', ENT_QUOTES) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . htmlspecialchars($tm->getSiteTitle() ?? '', ENT_QUOTES) . '">' . "\n";
        // theme-color dynamisch aus Customizer
        $themeColor = '#1e3a5f';
        try {
            $tc = \CMS\Services\ThemeCustomizer::instance()->get('colors', 'primary_color', '#1e3a5f');
            if (!empty($tc)) { $themeColor = $tc; }
        } catch (\Throwable $e) {}
        echo '<meta name="theme-color" content="' . htmlspecialchars($themeColor, ENT_QUOTES) . '">' . "\n";
    }

    /* ── Google Fonts Preconnect ────────────────────────────────── */

    public function outputPreconnect(): void
    {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }

    /* ── Google Fonts dynamisch laden ──────────────────────────── */

    public function outputGoogleFonts(): void
    {
        try {
            $c       = \CMS\Services\ThemeCustomizer::instance();
            $ui      = $c->get('typography', 'font_family_ui',    'barlow');
            $brand   = $c->get('typography', 'font_family_brand', 'barlow-condensed');
            $code    = $c->get('typography', 'font_family_code',  'jetbrains-mono');
            $fontMap = [
                'barlow'           => 'Barlow:wght@400;500;600;700',
                'barlow-condensed' => 'Barlow+Condensed:wght@500;600;700;800',
                'inter'            => 'Inter:wght@400;500;600;700',
                'roboto'           => 'Roboto:wght@400;500;700',
                'open-sans'        => 'Open+Sans:wght@400;600;700',
                'lato'             => 'Lato:wght@400;700',
                'montserrat'       => 'Montserrat:wght@400;600;700',
                'poppins'          => 'Poppins:wght@400;500;600;700',
                'source-sans'      => 'Source+Sans+3:wght@400;600;700',
                'nunito'           => 'Nunito:wght@400;600;700',
                'roboto-condensed' => 'Roboto+Condensed:wght@400;700',
                'oswald'           => 'Oswald:wght@500;700',
                'rajdhani'         => 'Rajdhani:wght@500;600;700',
                'exo2'             => 'Exo+2:wght@500;700',
                'jetbrains-mono'   => 'JetBrains+Mono:wght@400;600',
                'fira-code'        => 'Fira+Code:wght@400;600',
                'source-code'      => 'Source+Code+Pro:wght@400;600',
            ];
            $families = [];
            foreach (array_unique([$ui, $brand, $code]) as $slug) {
                if (isset($fontMap[$slug])) {
                    $families[] = $fontMap[$slug];
                }
            }
            if (empty($families)) { return; }
            $url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $families) . '&display=swap';
            echo '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . "\n";
        } catch (\Throwable $e) {}
    }

    /* ── Custom Head Code aus Customizer ───────────────────────── */

    public function outputCustomHeaderCode(): void
    {
        try {
            $code = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'custom_head_code', '');
            if (!empty(trim((string)$code))) {
                echo "\n" . (string)$code . "\n";
            }
        } catch (\Throwable $e) {}
    }

    /* ── Custom Footer Code aus Customizer ─────────────────────── */

    public function outputCustomFooterCode(): void
    {
        try {
            $code = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'custom_footer_code', '');
            if (!empty(trim((string)$code))) {
                echo "\n" . (string)$code . "\n";
            }
            // Google Analytics (separat, sicher)
            $gaId = \CMS\Services\ThemeCustomizer::instance()->get('advanced', 'google_analytics_id', '');
            if (!empty(trim((string)$gaId)) && preg_match('/^G-[A-Z0-9]{6,}$/', trim((string)$gaId))) {
                $gaId = htmlspecialchars(trim((string)$gaId), ENT_QUOTES);
                echo "<script async src=\"https://www.googletagmanager.com/gtag/js?id={$gaId}\"></script>\n";
                echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);} gtag('js',new Date()); gtag('config','{$gaId}',{anonymize_ip:true});</script>\n";
            }
        } catch (\Throwable $e) {}
    }

    /* ── Custom Styles aus Customizer ──────────────────────────── */

    /**
     * Phinit-spezifisches CSS aus Customizer generieren.
     * Mappt die Customizer-Keys (colors.primary_color, typography.font_family_ui, etc.)
     * auf die CSS Custom Properties in style.css.
     */
    private function generatePhinitCSS(): string
    {
        $c = \CMS\Services\ThemeCustomizer::instance();
        $css = "/* CMS Phinit – Customizer CSS */\n:root {\n";

        // ── Farben → CSS Custom Properties ──
        $colorMap = [
            'primary_color'      => '--primary-color',
            'primary_dark'       => '--primary-dark',
            'primary_mid'        => '--primary-mid',
            'primary_light'      => '--primary-light',
            'accent_color'       => '--accent-color',
            'accent_hover'       => '--accent-hover',
            'accent_blue'        => '--accent-blue',
            'accent_blue2'       => '--accent-blue2',
            'accent_teal'        => '--accent-teal',
            'accent_teal_light'  => '--accent-teal-light',
            'bg_header1'         => '--bg-header1',
            'bg_header2'         => '--bg-header2',
            'bg_header3'         => '--bg-header3',
            'bg_primary'         => '--bg-primary',
            'bg_secondary'       => '--bg-secondary',
            'bg_dark'            => '--bg-dark',
            'text_primary'       => '--text-primary',
            'text_secondary'     => '--text-secondary',
            'text_muted'         => '--text-muted',
            'text_nav'           => '--text-nav',
            'text_nav_member'    => '--text-nav-member',
            'text_nav_main'      => '--text-nav-main',
            'text_nav_quicklinks'=> '--text-nav-quicklinks',
            'text_nav_dropdown'  => '--text-nav-dropdown',
            'logo_suffix_color'  => '--logo-suffix-color',
            'border_light'       => '--border-color',
            'footer_bg'          => '--footer-bg',
            'footer_bottom_bg'   => '--footer-bottom-bg',
            'footer_border'      => '--footer-border',
            'success_color'      => '--success-color',
            'error_color'        => '--error-color',
            'progress_bar_start' => '--progress-bar-start',
            'progress_bar_end'   => '--progress-bar-end',
        ];
        foreach ($colorMap as $key => $var) {
            $val = $c->get('colors', $key, '');
            if (!empty($val) && $val !== '') {
                $css .= "    {$var}: {$val};\n";
            }
        }

        // ── Typografie ──
        $fontMapSlug = [
            'barlow'           => "'Barlow', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'barlow-condensed' => "'Barlow Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'inter'            => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'roboto'           => "'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'open-sans'        => "'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'lato'             => "'Lato', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'montserrat'       => "'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'poppins'          => "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'source-sans'      => "'Source Sans 3', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'nunito'           => "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'roboto-condensed' => "'Roboto Condensed', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'oswald'           => "'Oswald', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'rajdhani'         => "'Rajdhani', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'exo2'             => "'Exo 2', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
            'jetbrains-mono'   => "'JetBrains Mono', 'Fira Code', 'Cascadia Code', monospace",
            'fira-code'        => "'Fira Code', 'JetBrains Mono', monospace",
            'source-code'      => "'Source Code Pro', 'Fira Code', monospace",
            'cascadia'         => "'Cascadia Code', 'JetBrains Mono', monospace",
            'system'           => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'system-mono'      => "'Cascadia Code', 'Consolas', 'Courier New', monospace",
        ];

        $uiFont = $c->get('typography', 'font_family_ui', 'barlow');
        if (!empty($uiFont) && isset($fontMapSlug[$uiFont])) {
            $css .= "    --font-ui: {$fontMapSlug[$uiFont]};\n";
        }
        $brandFont = $c->get('typography', 'font_family_brand', 'barlow-condensed');
        if (!empty($brandFont) && isset($fontMapSlug[$brandFont])) {
            $css .= "    --font-brand: {$fontMapSlug[$brandFont]};\n";
        }
        $codeFont = $c->get('typography', 'font_family_code', 'jetbrains-mono');
        if (!empty($codeFont) && isset($fontMapSlug[$codeFont])) {
            $css .= "    --font-code: {$fontMapSlug[$codeFont]};\n";
        }

        $typoNumMap = [
            'font_size_base'      => ['--fs-base', 'px'],
            'font_size_post'      => ['--fs-post', 'px'],
            'line_height_base'    => ['--lh-base', ''],
            'line_height_post'    => ['--lh-post', ''],
        ];
        foreach ($typoNumMap as $key => $info) {
            $val = $c->get('typography', $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$info[0]}: {$val}{$info[1]};\n";
            }
        }
        $fwHead = $c->get('typography', 'font_weight_heading', '');
        if (!empty($fwHead)) { $css .= "    --fw-heading: {$fwHead};\n"; }
        $fwNav = $c->get('typography', 'font_weight_nav', '');
        if (!empty($fwNav)) { $css .= "    --fw-nav: {$fwNav};\n"; }

        // ── Layout ──
        $layoutMap = [
            'container_width'  => ['--container-max', 'px'],
            'sidebar_width'    => ['--sidebar-width', 'px'],
            'border_radius'    => ['--radius-sm', 'px'],
            'border_radius_md' => ['--radius', 'px'],
            'spacing_header_content' => ['--spacing-header-content', 'px'],
            'spacing_content_footer' => ['--spacing-content-footer', 'px'],
            'content_gap'            => ['--content-gap', 'px'],
            'spacing_sections'       => ['--spacing-sections', 'px'],
        ];
        foreach ($layoutMap as $key => $info) {
            $val = $c->get('layout', $key, '');
            if ($val !== '' && $val !== null) {
                $css .= "    {$info[0]}: {$val}{$info[1]};\n";
            }
        }

        // ── Header ──
        $logoAccent = $c->get('header', 'logo_accent_color', '');
        if (!empty($logoAccent)) { $css .= "    --logo-accent: {$logoAccent};\n"; }
        $logoHeight = $c->get('header', 'logo_max_height', '');
        if (!empty($logoHeight)) { $css .= "    --logo-max-height: {$logoHeight}px;\n"; }
        $memberBarH = $c->get('header', 'member_bar_height', '');
        if (!empty($memberBarH)) { $css .= "    --member-bar-h: {$memberBarH}px;\n"; }
        $mainNavH = $c->get('header', 'main_nav_height', '');
        if (!empty($mainNavH)) {
            $css .= "    --main-nav-height: {$mainNavH}px;\n";
            $css .= "    --header-h: {$mainNavH}px;\n";
        }
        $subBarH = $c->get('header', 'sub_bar_height', '');
        if (!empty($subBarH)) {
            $css .= "    --sub-bar-height: {$subBarH}px;\n";
            $css .= "    --quicklinks-h: {$subBarH}px;\n";
        }

        // ── Posts ──
        $heroH = $c->get('posts', 'post_hero_height', '');
        if (!empty($heroH)) { $css .= "    --post-hero-height: {$heroH}px;\n"; }

        $css .= "}\n";

        // ── Element-spezifische Regeln ──
        $css .= "\nbody {\n";
        $css .= "    font-family: var(--font-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);\n";
        $css .= "    font-size: var(--fs-base, 14.5px);\n";
        $css .= "    line-height: var(--lh-base, 1.55);\n";
        $css .= "    background: var(--bg-secondary);\n";
        $css .= "    color: var(--text-primary);\n";
        $css .= "}\n";

        $css .= "h1, h2, h3, h4, h5, h6, .site-logo, .main-nav a, .sub-nav a {\n";
        $css .= "    font-family: var(--font-brand);\n";
        $css .= "}\n";
        $css .= "h1, h2, h3 { font-weight: var(--fw-heading, 700); }\n";
        $css .= ".main-nav a, .sub-nav a { font-weight: var(--fw-nav, 600); }\n";

        $css .= "code, pre, .inline-code, .code-block {\n";
        $css .= "    font-family: var(--font-code);\n";
        $css .= "}\n";

        $css .= ".post-body {\n";
        $css .= "    font-size: var(--fs-post, 15.5px);\n";
        $css .= "    line-height: var(--lh-post, 1.8);\n";
        $css .= "}\n";

        $css .= ".container { max-width: var(--container-max, 1060px); }\n";

        $css .= ".member-bar { background: var(--bg-header1); min-height: var(--member-bar-h, 36px); }\n";
        $css .= ".hdr-bar-main { background: var(--bg-header2); min-height: var(--main-nav-height, 48px); }\n";
        $css .= ".quicklinks-bar { background: var(--bg-header3); min-height: var(--sub-bar-height, 30px); }\n";

        $css .= ".main-nav a { color: var(--text-nav-main, var(--text-nav, rgba(255,255,255,.82))); }\n";
        $css .= ".member-bar__link { color: var(--text-nav-member, rgba(255,255,255,.72)); }\n";
        $css .= ".member-bar__greeting { color: var(--text-nav-member, rgba(255,255,255,.7)); }\n";
        $css .= ".sub-nav a { color: var(--text-nav-quicklinks, var(--text-secondary)); }\n";
        $css .= ".main-nav .dropdown a { color: var(--text-nav-dropdown, rgba(255,255,255,.82)); }\n";

        $css .= ".site-footer { background: var(--footer-bg); border-top: 3px solid var(--footer-border); }\n";
        $css .= ".footer-bottom { background: var(--footer-bottom-bg); }\n";

        $css .= ".site-logo .logo-accent { color: var(--logo-accent, var(--accent-teal-light)); }\n";
        $css .= ".site-logo .logo-icon { background: var(--logo-accent, var(--accent-teal)); }\n";
        $css .= ".site-logo .logo-suffix { color: var(--logo-suffix-color, var(--accent-color)); }\n";
        $css .= ".site-logo img { max-height: var(--logo-max-height, 28px); }\n";

        $css .= "#scroll-progress { background: linear-gradient(90deg, var(--progress-bar-start, #2d7dd2), var(--progress-bar-end, #e8a838)); }\n";

        $css .= ".post-hero-img { max-height: var(--post-hero-height, 340px); }\n";

        // Artikel-Thumbnail Dimensionen als CSS-Variablen
        $thumbW = $c->get('homepage', 'article_thumb_width', '');
        $thumbH = $c->get('homepage', 'article_thumb_height', '');
        if (!empty($thumbW) || !empty($thumbH)) {
            $css .= ":root {\n";
            if (!empty($thumbW)) { $css .= "    --article-thumb-w: {$thumbW}px;\n"; }
            if (!empty($thumbH)) { $css .= "    --article-thumb-h: {$thumbH}px;\n"; }
            $css .= "}\n";
        }

        return $css;
    }

    public function outputCustomStyles(): void
    {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $custom = $c->get('advanced', 'custom_css', '');
            if (!empty(trim((string)$custom))) {
                echo '<style id="cms-phinit-custom-css">' . "\n";
                // Nur sicheres CSS ausgeben (kein Inline-Event-Handler möglich)
                echo strip_tags((string)$custom);
                echo "\n</style>\n";
            }
        } catch (\Throwable $e) {}
    }

    /* ── Menü-Positionen ────────────────────────────────────────── */

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',       'label' => 'Hauptnavigation'];
        $locations[] = ['slug' => 'quicklinks',    'label' => 'Quicklinks (Sub-Navigation)'];
        $locations[] = ['slug' => 'footer-topics', 'label' => 'Footer – Themen'];
        $locations[] = ['slug' => 'footer-pages',  'label' => 'Footer – Seiten'];
        $locations[] = ['slug' => 'footer',        'label' => 'Footer – Rechtliches'];
        return $locations;
    }

    /** Standard-Navigation beim Erststart anlegen */
    public function seedDefaultMenus(): void
    {
        try {
            $tm = \CMS\ThemeManager::instance();

            // Hauptnavigation
            if (empty($tm->getMenu('primary'))) {
                $tm->saveMenu('primary', [
                    ['label' => 'Startseite',    'url' => '/'],
                    ['label' => 'Linux / BASH',  'url' => '/linux'],
                    ['label' => 'PowerShell',    'url' => '/powershell', 'children' => [
                        ['label' => 'Grundlagen',   'url' => '/powershell/grundlagen'],
                        ['label' => 'Glossar',      'url' => '/powershell/glossar'],
                    ]],
                    ['label' => 'Microsoft 365', 'url' => '/microsoft-365', 'children' => [
                        ['label' => 'Microsoft 365 Admin', 'url' => '/microsoft-365/admin'],
                        ['label' => 'Exchange Online',     'url' => '/microsoft-365/exchange'],
                        ['label' => 'Teams & SharePoint',  'url' => '/microsoft-365/teams'],
                    ]],
                    ['label' => 'Datenschutz',   'url' => '/datenschutz'],
                    ['label' => 'News',          'url' => '/news'],
                ]);
            }

            // Quicklinks (Sub-Navigation)
            if (empty($tm->getMenu('quicklinks'))) {
                $tm->saveMenu('quicklinks', [
                    ['label' => 'Entra ID',    'url' => '/kategorie/entra-id'],
                    ['label' => 'Intune',      'url' => '/kategorie/intune'],
                    ['label' => 'Compliance',  'url' => '/kategorie/compliance'],
                    ['label' => 'Graph API',   'url' => '/kategorie/graph-api'],
                    ['label' => 'PowerShell',  'url' => '/kategorie/powershell'],
                    ['label' => 'Security',    'url' => '/kategorie/security'],
                    ['label' => 'Exchange',    'url' => '/kategorie/exchange'],
                ]);
            }

            // Footer – Themen
            if (empty($tm->getMenu('footer-topics'))) {
                $tm->saveMenu('footer-topics', [
                    ['label' => 'Linux & BASH',          'url' => '/linux'],
                    ['label' => 'PowerShell',            'url' => '/powershell'],
                    ['label' => 'Microsoft 365',         'url' => '/microsoft-365'],
                    ['label' => 'Intune & MDM',          'url' => '/intune'],
                    ['label' => 'Datenschutz & DSGVO',   'url' => '/datenschutz'],
                    ['label' => 'IT-News',               'url' => '/news'],
                ]);
            }

            // Footer – Seiten
            if (empty($tm->getMenu('footer-pages'))) {
                $tm->saveMenu('footer-pages', [
                    ['label' => 'Über mich',             'url' => '/ueber-uns'],
                    ['label' => 'Kontakt',               'url' => '/kontakt'],
                    ['label' => 'RSS-Feed',              'url' => '/feed'],
                    ['label' => 'Impressum',             'url' => '/impressum'],
                    ['label' => 'Datenschutzerklärung',  'url' => '/datenschutzerklaerung'],
                    ['label' => 'Disclaimer',            'url' => '/disclaimer'],
                ]);
            }

        } catch (\Throwable $e) {}
    }

    /* ── Body-Class ────────────────────────────────────────────── */

    public function bodyClass(string $classes): string
    {
        $add = [];
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        if ($uri === '/' || $uri === '') {
            $add[] = 'home';
        } else {
            $add[] = 'singular';
        }
        // Blog-Einzelbeitrag erkennen
        if (preg_match('#^/blog/.+#', $uri)) {
            $add[] = 'is-post';
        }
        // Member-Bereich
        if (str_starts_with($uri, '/member') || str_starts_with($uri, '/dashboard')) {
            $add[] = 'is-member';
        }
        return trim($classes . ' ' . implode(' ', $add));
    }
}

// Theme initialisieren
CMS_Phinit_Theme::instance();

/* ── Template-Helper ─────────────────────────────────────────── */

if (!function_exists('get_theme_part')) {
    /**
     * Theme-Partial laden (header.php, footer.php, …)
     */
    function get_theme_part(string $part, array $vars = []): void
    {
        $file = CMS_PHINIT_THEME_DIR . $part . '.php';
        if (!file_exists($file)) {
            return;
        }
        if (!empty($vars)) {
            extract($vars, EXTR_SKIP);
        }
        include $file;
    }
}

if (!function_exists('theme_is_logged_in')) {
    function theme_is_logged_in(): bool
    {
        try {
            return \CMS\Auth::instance()->isLoggedIn();
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('phinit_reading_time')) {
    /**
     * Lesezeit in Minuten schätzen
     */
    function phinit_reading_time(string $content, int $wpm = 200): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int)round($wordCount / $wpm));
    }
}
