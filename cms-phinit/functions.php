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

defined('CMS_PHINIT_THEME_VERSION') || define('CMS_PHINIT_THEME_VERSION', '1.4.1');
defined('CMS_PHINIT_THEME_DIR') || define('CMS_PHINIT_THEME_DIR', THEME_PATH . 'cms-phinit/');
defined('CMS_PHINIT_THEME_URL') || define('CMS_PHINIT_THEME_URL', rtrim(\CMS\ThemeManager::instance()->getThemeUrl(), '/') . '/');

require_once CMS_PHINIT_THEME_DIR . 'includes/theme-template-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-content-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-assets-trait.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-head-trait.php';

/**
 * Theme-Hauptklasse (Singleton)
 */
if (!class_exists('CMS_Phinit_Theme', false)) {
final class CMS_Phinit_Theme
{
    use CMS_Phinit_Theme_Assets_Trait;
    use CMS_Phinit_Theme_Head_Trait;

    private static ?self $instance = null;
    private const LOCAL_FONT_SLUG_ALIASES = [
        'source-sans' => 'source-sans-3',
        'source-code' => 'source-code-pro',
        'exo2' => 'exo-2',
    ];
    private bool $scriptsOutput = false;
    private bool $footerCodeOutput = false;
    private bool $breadcrumbOutput = false;

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
        \CMS\Hooks::addAction('head', [$this, 'outputSchemaOrg'],       25);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomHeaderCode'], 99);

        // Scripts ans Ende des Body
        \CMS\Hooks::addAction('body_end', [$this, 'enqueueScripts'],        10);
        \CMS\Hooks::addAction('body_end', [$this, 'outputCustomFooterCode'], 99);

        // Breadcrumb (nach dem Header)
        \CMS\Hooks::addAction('after_header', [$this, 'outputBreadcrumb'], 5);

        // Menüpositionen
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);
        \CMS\Hooks::addFilter('local_font_slugs', [$this, 'registerRequiredLocalFonts']);

        // Standardmenüs beim ersten Start anlegen
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus'], 20);

        // Body-Class für aktuelle Seite anreichern
        \CMS\Hooks::addFilter('body_class', [$this, 'bodyClass']);

        // Dynamischer Seitentitel
        \CMS\Hooks::addFilter('page_title', [$this, 'filterPageTitle']);
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
                    ['label' => 'Über mich', 'url' => '/ueber-uns'],
                    ['label' => 'Kontakt',   'url' => '/kontakt'],
                    ['label' => 'RSS-Feed',  'url' => '/feed'],
                ]);
            }

            // Footer – Rechtliches
            if (empty($tm->getMenu('footer'))) {
                $tm->saveMenu('footer', [
                    ['label' => 'Impressum',            'url' => '/impressum'],
                    ['label' => 'Datenschutzerklärung', 'url' => '/datenschutzerklaerung'],
                    ['label' => 'Disclaimer',           'url' => '/disclaimer'],
                    ['label' => 'Cookie-Policy',        'url' => '/cookie-policy'],
                ]);
            }

        } catch (\Throwable $e) {}
    }
}
}

// Theme initialisieren (nur einmal pro Request)
if (!defined('CMS_PHINIT_THEME_BOOTSTRAPPED')) {
    define('CMS_PHINIT_THEME_BOOTSTRAPPED', true);
    CMS_Phinit_Theme::instance();
}

