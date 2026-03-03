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
        $version = file_exists($cssFile) ? filemtime($cssFile) : CMS_PHINIT_THEME_VERSION;
        echo '<link rel="stylesheet" href="' . CMS_PHINIT_THEME_URL . 'style.css?v=' . $version . '">' . "\n";

        // Customizer: Dynamisches CSS einbinden
        try {
            $css = \CMS\Services\ThemeCustomizer::instance()->generateCSS();
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
        echo '<meta name="theme-color" content="#1e3a5f">' . "\n";
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
        $locations['primary'] = 'Hauptnavigation';
        $locations['footer']  = 'Footer-Navigation';
        return $locations;
    }

    /** Standard-Navigation beim Erststart anlegen */
    public function seedDefaultMenus(): void
    {
        try {
            $mm = \CMS\MenuManager::instance();
            if (!empty($mm->getMenuItems('primary'))) {
                return; // Bereits vorhanden
            }
            $mm->seedMenu('primary', [
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
        } catch (\Throwable $e) {}
    }

    /* ── Body-Class ────────────────────────────────────────────── */

    public function bodyClass(string $classes): string
    {
        $add = [];
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if ($uri === '/' || $uri === '') $add[] = 'home';
        else $add[] = 'singular';
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
