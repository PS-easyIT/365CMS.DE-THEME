<?php
/**
 * Theme-Funktionen – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 * @version 1.5.74
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

defined('CMS_PHINIT_THEME_VERSION') || define('CMS_PHINIT_THEME_VERSION', '1.5.74');
defined('CMS_PHINIT_THEME_DIR') || define('CMS_PHINIT_THEME_DIR', THEME_PATH . 'cms-phinit/');
defined('CMS_PHINIT_THEME_URL') || define('CMS_PHINIT_THEME_URL', rtrim(\CMS\ThemeManager::instance()->getThemeUrl(), '/') . '/');

require_once CMS_PHINIT_THEME_DIR . 'includes/theme-template-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-content-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-home-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-media-archive-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-special-pages-helpers.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-services-hub-seed.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-assets-trait.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-head-trait.php';
require_once CMS_PHINIT_THEME_DIR . 'includes/theme-navigation-trait.php';

/**
 * Theme-Hauptklasse (Singleton)
 */
if (!class_exists('CMS_Phinit_Theme', false)) {
final class CMS_Phinit_Theme
{
    use CMS_Phinit_Theme_Assets_Trait;
    use CMS_Phinit_Theme_Head_Trait;
    use CMS_Phinit_Theme_Navigation_Trait;

    private static ?self $instance = null;
    private const LOCAL_FONT_SLUG_ALIASES = [
        'source-sans' => 'source-sans-3',
        'source-code' => 'source-code-pro',
        'exo2' => 'exo-2',
    ];
    private bool $scriptsOutput = false;
    private bool $footerCodeOutput = false;
    private bool $breadcrumbOutput = false;
    private ?array $requestContextCache = null;
    private bool $currentHeadPostResolved = false;
    private ?array $currentHeadPostCache = null;
    private bool $currentHeadPageTitleResolved = false;
    private ?string $currentHeadPageTitleCache = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        \CMS\Hooks::addAction('before_render', [$this, 'handleFavoriteToggleRequest'], 1);

        // Assets
        \CMS\Hooks::addAction('head', [$this, 'outputCustomHeaderCode'], 0);
        \CMS\Hooks::addAction('head', [$this, 'outputPreconnect'],       1);
        \CMS\Hooks::addAction('head', [$this, 'outputGoogleFonts'],      5);
        \CMS\Hooks::addAction('head', [$this, 'outputCriticalResourceHints'], 7);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'],         8);
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],         15);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'],    20);
        \CMS\Hooks::addAction('head', [$this, 'outputSchemaOrg'],       25);

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
        \CMS\Hooks::addAction('cms_init', [$this, 'seedServicesHubSite'], 30);

        // Body-Class für aktuelle Seite anreichern
        \CMS\Hooks::addFilter('body_class', [$this, 'bodyClass']);

        // Dynamischer Seitentitel
        \CMS\Hooks::addFilter('page_title', [$this, 'filterPageTitle']);

        // PHINIT hält öffentliche SiteTables bewusst ruhig: keine Suchleiste über Tabellen.
        \CMS\Hooks::addFilter('site_table_interactive_config', [$this, 'filterSiteTableInteractiveConfig']);
    }

    public function handleFavoriteToggleRequest(): void
    {
        phinit_handle_favorite_toggle_request();
    }

    public function seedServicesHubSite(): void
    {
        phinit_seed_services_hub_site();
    }

    public function filterSiteTableInteractiveConfig(array $config, array $settings = [], int $rowCount = 0): array
    {
        $config['pageSize'] = max(20, (int) ($config['pageSize'] ?? 20));
        $config['searchEnabled'] = false;
        if ($rowCount <= 20) {
            $config['paginationEnabled'] = false;
        }
        $config['interactiveEnabled'] = !empty($config['sortingEnabled']) || !empty($config['paginationEnabled']);

        return $config;
    }
}
}

// Theme initialisieren (nur einmal pro Request)
if (!defined('CMS_PHINIT_THEME_BOOTSTRAPPED')) {
    define('CMS_PHINIT_THEME_BOOTSTRAPPED', true);
    CMS_Phinit_Theme::instance();
}

