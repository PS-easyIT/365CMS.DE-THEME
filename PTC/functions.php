<?php
declare(strict_types=1);

/**
 * PTC GmbH Theme – functions.php
 *
 * Corporate Theme für Personaldienstleistungen.
 * Marineblau (#002D5D) + Gold (#D4A017) Corporate Identity.
 *
 * @package PTC_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PTC_THEME_VERSION', '1.0.0');
define('PTC_THEME_DIR',     THEME_PATH . 'PTC/');
define('PTC_THEME_URL',     \CMS\ThemeManager::instance()->getThemeUrl());

/**
 * PTC Theme Singleton
 */
final class PTC_Theme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        // Head-Assets
        \CMS\Hooks::addAction('head', [$this, 'outputPreconnect'],    1);
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],      10);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'], 20);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'],     30);

        // Footer-Scripts
        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 10);
        \CMS\Hooks::addAction('body_end', [$this, 'outputCustomFooterCode'], 99);

        // Menüpositionen
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Standard-Menüs
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus']);
    }

    // ── Preconnect ───────────────────────────────────────────────────────────

    public function outputPreconnect(): void
    {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    }

    // ── Assets ───────────────────────────────────────────────────────────────

    public function enqueueStyles(): void
    {
        $css = PTC_THEME_DIR . 'style.css';
        if (file_exists($css)) {
            echo '<link rel="stylesheet" href="' . PTC_THEME_URL . '/style.css?v=' . filemtime($css) . '">' . "\n";
        }
    }

    public function enqueueScripts(): void
    {
        $js = PTC_THEME_DIR . 'js/navigation.js';
        if (file_exists($js)) {
            echo '<script src="' . PTC_THEME_URL . '/js/navigation.js?v=' . filemtime($js) . '" defer></script>' . "\n";
        }
    }

    // ── Custom Styles (Customizer) ───────────────────────────────────────────

    public function outputCustomStyles(): void
    {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();

            // ── Farben ──
            $vars = [
                '--ptc-navy'        => $c->get('colors', 'primary_color',   '#002D5D'),
                '--ptc-navy-dark'   => $c->get('colors', 'primary_hover',   '#001F42'),
                '--ptc-navy-light'  => $c->get('colors', 'primary_light',   '#E8F0FE'),
                '--ptc-gold'        => $c->get('colors', 'accent_color',    '#D4A017'),
                '--ptc-gold-dark'   => $c->get('colors', 'accent_hover',    '#B8860B'),
                '--ptc-gold-light'  => $c->get('colors', 'accent_light',    '#FDF5E6'),
                '--ptc-slate'       => $c->get('colors', 'secondary_color', '#607D8B'),
                '--ptc-text'        => $c->get('colors', 'text_color',      '#334155'),
                '--ptc-heading'     => $c->get('colors', 'heading_color',   '#002D5D'),
                '--ptc-white'       => $c->get('colors', 'text_light',      '#F8F9FA'),
                '--ptc-muted'       => $c->get('colors', 'muted_color',     '#94a3b8'),
                '--ptc-bg'          => $c->get('colors', 'bg_color',        '#F8F9FA'),
                '--ptc-bg-alt'      => $c->get('colors', 'bg_secondary',    '#F1F5F9'),
                '--ptc-link'        => $c->get('colors', 'link_color',      '#002D5D'),
                '--ptc-link-hover'  => $c->get('colors', 'link_hover_color','#D4A017'),
                '--ptc-border'      => $c->get('colors', 'border_color',    '#E2E8F0'),
                '--ptc-success'     => $c->get('colors', 'success_color',   '#22c55e'),
                '--ptc-error'       => $c->get('colors', 'error_color',     '#ef4444'),
            ];

            // ── Typografie ──
            $fontMap = [
                'system'     => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
                'inter'      => "'Inter', sans-serif",
                'roboto'     => "'Roboto', sans-serif",
                'open-sans'  => "'Open Sans', sans-serif",
                'lato'       => "'Lato', sans-serif",
                'montserrat' => "'Montserrat', sans-serif",
                'poppins'    => "'Poppins', sans-serif",
                'raleway'    => "'Raleway', sans-serif",
                'georgia'    => "Georgia, 'Times New Roman', serif",
            ];
            $fontBase    = $c->get('typography', 'font_family_base',    'system');
            $fontHeading = $c->get('typography', 'font_family_heading', 'system');
            $vars['--ptc-font-body']    = $fontMap[$fontBase]    ?? $fontMap['system'];
            $vars['--ptc-font-heading'] = $fontMap[$fontHeading] ?? $fontMap['system'];
            $vars['--ptc-font-size']    = $c->get('typography', 'font_size_base',     '16') . 'px';
            $vars['--ptc-line-height']  = (string)$c->get('typography', 'line_height_base',  '1.6');
            $vars['--ptc-heading-weight'] = (string)$c->get('typography', 'font_weight_heading', '700');

            // ── Layout ──
            $vars['--ptc-container-width']  = $c->get('layout', 'container_width',  '1280') . 'px';
            $vars['--ptc-content-padding']  = $c->get('layout', 'content_padding',  '2') . 'rem';
            $vars['--ptc-radius']           = $c->get('layout', 'border_radius',    '12') . 'px';
            $vars['--ptc-section-spacing']  = $c->get('layout', 'section_spacing',  '5') . 'rem';

            // ── Header ──
            $vars['--ptc-header-bg']      = $c->get('header', 'header_bg_color',     '#002D5D');
            $vars['--ptc-header-text']    = $c->get('header', 'header_text_color',   '#F8F9FA');
            $vars['--ptc-header-accent']  = $c->get('header', 'header_accent_color', '#D4A017');
            $vars['--ptc-header-height']  = $c->get('header', 'header_height',       '72') . 'px';
            $vars['--ptc-logo-height']    = $c->get('header', 'logo_max_height',     '48') . 'px';

            // ── Footer ──
            $vars['--ptc-footer-bg']   = $c->get('footer', 'footer_bg_color',   '#001A33');
            $vars['--ptc-footer-text'] = $c->get('footer', 'footer_text_color', '#94a3b8');
            $vars['--ptc-footer-link'] = $c->get('footer', 'footer_link_color', '#F8F9FA');

            // ── CTA-Sektion ──
            $vars['--ptc-cta-bg']      = $c->get('colors', 'cta_bg_color',   '#002D5D');
            $vars['--ptc-cta-bg-to']   = $c->get('colors', 'cta_bg_to',      '#001F42');
            $vars['--ptc-cta-text']    = $c->get('colors', 'cta_text_color',  '#FFFFFF');

            // ── Seitenränder ──
            $vars['--ptc-side-color']  = $c->get('colors', 'side_margin_color', '#EDF1F5');
            $vars['--ptc-side-accent'] = $c->get('colors', 'side_accent_color', '#002D5D');
            $sideWidth                 = (int) $c->get('colors', 'side_accent_width', 0);
            $vars['--ptc-side-width']  = $sideWidth . 'px';

            // ── Buttons ──
            $vars['--ptc-btn-radius']    = $c->get('buttons', 'button_border_radius', '8') . 'px';
            $vars['--ptc-btn-px']        = $c->get('buttons', 'button_padding_x',     '2') . 'rem';
            $vars['--ptc-btn-py']        = $c->get('buttons', 'button_padding_y',     '0.875') . 'rem';
            $vars['--ptc-btn-weight']    = (string)$c->get('buttons', 'button_font_weight', '600');
            $vars['--ptc-btn-transform'] = (string)$c->get('buttons', 'button_transform',   'none');

            // ── CSS ausgeben ──
            $css = ':root{';
            foreach ($vars as $prop => $val) {
                $css .= htmlspecialchars($prop, ENT_QUOTES, 'UTF-8') . ':' . htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8') . ';';
            }
            $css .= '}';
            echo '<style>' . $css . '</style>' . "\n";

            // ── Eigenes CSS ──
            $customCss = trim((string)$c->get('advanced', 'custom_css', ''));
            if ($customCss !== '') {
                echo '<style>' . $customCss . '</style>' . "\n";
            }

            // ── Custom Head Code ──
            $headCode = trim((string)$c->get('advanced', 'custom_head_code', ''));
            if ($headCode !== '') {
                echo $headCode . "\n";
            }

        } catch (\Throwable $e) {
            // Fallback bei Fehler
            echo '<style>:root{'
                . '--ptc-navy:#002D5D;--ptc-gold:#D4A017;'
                . '}</style>' . "\n";
        }
    }

    /**
     * Custom Footer Code aus dem Customizer ausgeben
     */
    public function outputCustomFooterCode(): void
    {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
            $footerCode = trim((string)$c->get('advanced', 'custom_footer_code', ''));
            if ($footerCode !== '') {
                echo $footerCode . "\n";
            }
        } catch (\Throwable $e) {
            // Stille Fehlerbehandlung
        }
    }

    // ── Meta Tags ────────────────────────────────────────────────────────────

    public function outputMetaTags(): void
    {
        $tm       = \CMS\ThemeManager::instance();
        $siteDesc = htmlspecialchars($tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl  = htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8');
        echo '<meta name="description" content="' . $siteDesc . '">' . "\n";
        echo '<meta property="og:url" content="' . $siteUrl . '">' . "\n";
        echo '<meta name="theme-color" content="#002D5D">' . "\n";
        echo '<meta name="robots" content="index,follow">' . "\n";
    }

    // ── Menü-Positionen ──────────────────────────────────────────────────────

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',      'label' => 'Hauptnavigation (Header)'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Footer-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Footer Rechtliche Links'];
        return $locations;
    }

    /**
     * Standard-Menüeinträge beim ersten Start anlegen
     */
    public function seedDefaultMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();

        $defaults = [
            'primary' => [
                ['label' => 'Home',                   'url' => '/',                       'target' => '_self'],
                ['label' => 'Über Uns',               'url' => '/#ueber-uns',             'target' => '_self'],
                ['label' => 'Akademie & Bildung',     'url' => '/#dienstleistungen',      'target' => '_self'],
                ['label' => 'Logistiklehrwerkstatt',  'url' => '/#dienstleistungen',      'target' => '_self'],
                ['label' => 'Für Unternehmen',        'url' => '/#unternehmen',           'target' => '_self'],
                ['label' => 'Aktuelles',              'url' => '/#termine',               'target' => '_self'],
                ['label' => 'Karriere',               'url' => '/#karriere',              'target' => '_self'],
                ['label' => 'Kontakt',                'url' => '/#kontakt',               'target' => '_self'],
            ],
            'footer-nav' => [
                ['label' => 'Startseite',             'url' => '/',                       'target' => '_self'],
                ['label' => 'Dienstleistungen',       'url' => '/#dienstleistungen',      'target' => '_self'],
                ['label' => 'Über uns',               'url' => '/#ueber-uns',             'target' => '_self'],
                ['label' => 'Kontakt',                'url' => '/#kontakt',               'target' => '_self'],
            ],
            'footer-legal' => [
                ['label' => 'Impressum',              'url' => '/impressum',              'target' => '_self'],
                ['label' => 'Datenschutz',            'url' => '/datenschutz',            'target' => '_self'],
            ],
        ];

        foreach ($defaults as $location => $items) {
            if (empty($tm->getMenu($location))) {
                $tm->saveMenu($location, $items);
            }
        }
    }

    /**
     * Theme-Konfiguration aus theme.json lesen
     */
    public function getConfig(string $key, mixed $default = ''): mixed
    {
        static $config = null;

        if ($config === null) {
            $jsonFile = PTC_THEME_DIR . 'theme.json';
            if (file_exists($jsonFile)) {
                $decoded = json_decode(file_get_contents($jsonFile), true);
                $config  = $decoded['settings'] ?? [];
            } else {
                $config = [];
            }
        }

        return $config[$key] ?? $default;
    }
}

// Theme initialisieren
PTC_Theme::instance();

// ── Global Helper ──────────────────────────────────────────────────────────────

if (!function_exists('ptc_nav_menu')) {
    function ptc_nav_menu(string $location = 'primary'): void
    {
        $tm    = \CMS\ThemeManager::instance();
        $items = $tm->getMenu($location);

        if (empty($items)) {
            return;
        }

        $requestUri = rtrim($_SERVER['REQUEST_URI'] ?? '/', '/');
        echo '<ul>' . "\n";
        foreach ($items as $item) {
            $path     = rtrim(parse_url($item['url'], PHP_URL_PATH) ?? '/', '/');
            $isActive = ($path === '' || $path === '/')
                ? ($requestUri === '' || $requestUri === '/')
                : str_starts_with($requestUri, $path);
            $class  = $isActive ? ' class="active"' : '';
            $target = !empty($item['target']) && $item['target'] === '_blank'
                ? ' target="_blank" rel="noopener noreferrer"'
                : '';
            $url   = htmlspecialchars($item['url'],   ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
            echo '<li' . $class . '><a href="' . $url . '"' . $target . '>' . $label . '</a></li>' . "\n";
        }
        echo '</ul>' . "\n";
    }
}

if (!function_exists('ptc_config')) {
    function ptc_config(string $key, mixed $default = ''): mixed
    {
        return PTC_Theme::instance()->getConfig($key, $default);
    }
}

if (!function_exists('ptc_site_url')) {
    function ptc_site_url(): string
    {
        return rtrim(SITE_URL, '/');
    }
}

if (!function_exists('ptc_site_title')) {
    function ptc_site_title(): string
    {
        return htmlspecialchars(\CMS\ThemeManager::instance()->getSiteTitle(), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Customizer-Wert abrufen (Kurzform für Templates)
 *
 * Verwendung:
 *   $color = ptc_customizer_get('colors', 'primary_color', '#002D5D');
 *   $logo  = ptc_customizer_get('header', 'logo_url');
 *
 * @param string $section  Customizer-Kategorie (colors, typography, layout, header, footer, buttons, homepage, advanced)
 * @param string $key      Einstellungs-Schlüssel innerhalb der Kategorie
 * @param mixed  $default  Fallback-Wert wenn nicht gesetzt
 * @return mixed
 */
if (!function_exists('ptc_customizer_get')) {
    function ptc_customizer_get(string $section, string $key, mixed $default = ''): mixed
    {
        return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
    }
}

/**
 * Gesamte Customizer-Kategorie als Array abrufen
 *
 * Verwendung:
 *   $colors = ptc_customizer_category('colors');
 *   echo $colors['primary_color'] ?? '#002D5D';
 *
 * @param string $section  Customizer-Kategorie
 * @return array<string, mixed>
 */
if (!function_exists('ptc_customizer_category')) {
    function ptc_customizer_category(string $section): array
    {
        return \CMS\Services\ThemeCustomizer::instance()->getCategory($section) ?: [];
    }
}

/**
 * Customizer-Wert HTML-escaped ausgeben (für Template-Attribute/Texte)
 *
 * Verwendung:
 *   <h1><?php ptc_customizer_echo('header', 'site_tagline', 'Ihr Partner für Personal'); ?></h1>
 *   <div style="color: <?php ptc_customizer_echo('colors', 'primary_color', '#002D5D'); ?>">
 *
 * @param string $section  Customizer-Kategorie
 * @param string $key      Einstellungs-Schlüssel
 * @param mixed  $default  Fallback-Wert
 */
if (!function_exists('ptc_customizer_echo')) {
    function ptc_customizer_echo(string $section, string $key, mixed $default = ''): void
    {
        echo htmlspecialchars(
            (string) \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

// ── Auth & Security Helpers ────────────────────────────────────────────────────

/**
 * CSRF-Token generieren
 */
if (!function_exists('theme_csrf_token')) {
    function theme_csrf_token(string $action = 'form'): string
    {
        return \CMS\Security::instance()->generateToken($action);
    }
}

/**
 * CSRF-Token als Hidden-Input ausgeben
 */
if (!function_exists('theme_csrf_field')) {
    function theme_csrf_field(string $action = 'form'): void
    {
        $token = theme_csrf_token($action);
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
}

/**
 * Flash-Message aus Session lesen & löschen
 */
if (!function_exists('theme_get_flash')) {
    function theme_get_flash(string $type = 'error'): string
    {
        $key = ($type === 'success') ? 'success' : 'error';
        $msg = $_SESSION[$key] ?? '';
        unset($_SESSION[$key]);
        return $msg;
    }
}

/**
 * Eingeloggter Benutzer?
 */
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
