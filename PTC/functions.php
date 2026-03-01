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
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $primary = $customizer->get('colors', 'primary_color', '#002D5D');
            $accent  = $customizer->get('colors', 'accent_color', '#D4A017');
        } catch (\Throwable $e) {
            $primary = '#002D5D';
            $accent  = '#D4A017';
        }

        echo '<style>:root{'
            . '--ptc-navy:' . htmlspecialchars($primary, ENT_QUOTES, 'UTF-8') . ';'
            . '--ptc-gold:' . htmlspecialchars($accent, ENT_QUOTES, 'UTF-8') . ';'
            . '}</style>' . "\n";
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
