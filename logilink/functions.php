<?php
declare(strict_types=1);

/**
 * LogiLink Theme – functions.php
 *
 * Logistik & Transport Theme für 365CMS v3.x.x auf PHP 8.4.
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('LOGILINK_THEME_VERSION')) {
    define('LOGILINK_THEME_VERSION', '1.0.2');
}

if (!defined('LOGILINK_THEME_SLUG')) {
    define('LOGILINK_THEME_SLUG', 'logilink');
}

/**
 * Theme bootstrap (singleton).
 */
final class LogiLink_Theme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->registerHooks();
    }

    private function registerHooks(): void
    {
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],      1);
        \CMS\Hooks::addAction('head', [$this, 'outputGoogleFonts'],  5);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'],     10);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'], 15);

        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 99);

        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Auf BEIDEN Hooks für v3-Bootstrap-Kompatibilität.
        \CMS\Hooks::addAction('init',     [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'registerNavMenus'], 10);
    }

    // ── Assets ───────────────────────────────────────────────────────────────

    public function enqueueStyles(): void
    {
        $href = htmlspecialchars(
            $this->themeBaseUrl() . '/style.css?v=' . rawurlencode(LOGILINK_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = htmlspecialchars(
            $this->themeBaseUrl() . '/js/navigation.js?v=' . rawurlencode(LOGILINK_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }

    /**
     * Google Fonts – preconnect + nur tatsächlich gewählte Familien.
     * Bei reinen System-Stacks (z. B. `monospace`) wird nichts geladen.
     */
    public function outputGoogleFonts(): void
    {
        $families = $this->googleFontFamilies();
        if ($families === '') {
            return;
        }
        $url = 'https://fonts.googleapis.com/css2?family=' . $families . '&display=swap';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    // ── Meta ────────────────────────────────────────────────────────────────

    public function outputMetaTags(): void
    {
        $tm       = \CMS\ThemeManager::instance();
        $siteDesc = htmlspecialchars((string) $tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl  = htmlspecialchars((string) (defined('SITE_URL') ? SITE_URL : ''), ENT_QUOTES, 'UTF-8');

        $themeColor = htmlspecialchars(
            (string) $this->customizerGet('header', 'header_bg_color', '#0c1a2e'),
            ENT_QUOTES,
            'UTF-8'
        );

        echo '<meta name="description" content="' . $siteDesc . '">' . "\n";
        echo '<meta property="og:url" content="' . $siteUrl . '">' . "\n";
        echo '<meta name="theme-color" content="' . $themeColor . '">' . "\n";
        echo '<meta name="robots" content="index,follow">' . "\n";
    }

    // ── Customizer-getriebene CSS-Variablen + custom_css ─────────────────────

    public function outputCustomStyles(): void
    {
        $p = fn(string $g, string $k, string $d): string =>
            htmlspecialchars((string) $this->customizerGet($g, $k, $d), ENT_QUOTES, 'UTF-8');

        // Fonts
        $baseChoice    = (string) $this->customizerGet('typography', 'font_family_base',    'inter');
        $headingChoice = (string) $this->customizerGet('typography', 'font_family_heading', 'roboto-condensed');
        $dataChoice    = (string) $this->customizerGet('typography', 'font_family_data',    'jetbrains-mono');

        $baseFont    = htmlspecialchars($this->mapFontChoice($baseChoice,    'body'),    ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingChoice, 'heading'), ENT_QUOTES, 'UTF-8');
        $monoFont    = htmlspecialchars($this->mapFontChoice($dataChoice,    'mono'),    ENT_QUOTES, 'UTF-8');

        echo '<style id="ll-custom-vars">:root{';

        // Brand & colors
        echo '--ll-primary:'       . $p('colors', 'primary_color',   '#0e6ba8') . ';';
        echo '--ll-primary-hover:' . $p('colors', 'secondary_color', '#0a558a') . ';';
        echo '--ll-accent:'        . $p('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--ll-ink:'           . $p('colors', 'dark_bg_color',   '#0c1a2e') . ';';
        echo '--ll-paper:'         . $p('colors', 'bg_color',        '#f3f5f8') . ';';
        echo '--ll-surface:'       . $p('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--ll-text:'          . $p('colors', 'text_color',      '#0f172a') . ';';
        echo '--ll-text-mute:'     . $p('colors', 'muted_color',     '#5b6b80') . ';';
        echo '--ll-rule:'          . $p('colors', 'border_color',    '#d4dbe3') . ';';

        // Status
        echo '--status-warehouse:' . $p('colors', 'status_warehouse', '#475569') . ';';
        echo '--status-picked:'    . $p('colors', 'status_picked',    '#b45309') . ';';
        echo '--status-transit:'   . $p('colors', 'status_transit',   '#1d4ed8') . ';';
        echo '--status-delivered:' . $p('colors', 'status_delivered', '#15803d') . ';';
        echo '--status-delayed:'   . $p('colors', 'status_delayed',   '#b91c1c') . ';';
        echo '--status-returned:'  . $p('colors', 'status_returned',  '#6d28d9') . ';';

        // Typography
        echo '--ll-font-body:'      . $baseFont    . ';';
        echo '--ll-font-heading:'   . $headingFont . ';';
        echo '--ll-font-mono:'      . $monoFont    . ';';
        echo '--ll-font-size-base:' . $p('typography', 'font_size_base',     '15') . 'px;';
        echo '--ll-line-height:'    . $p('typography', 'line_height_base',   '1.5') . ';';
        echo '--ll-heading-weight:' . $p('typography', 'font_weight_heading','700') . ';';

        // Layout
        echo '--ll-max-width:'   . $p('layout', 'container_width', '1280') . 'px;';
        echo '--ll-content-pad:' . $p('layout', 'content_padding', '1.5')  . 'rem;';
        echo '--ll-radius:'      . $p('layout', 'border_radius',   '6')    . 'px;';
        echo '--ll-section-pad:' . $p('layout', 'section_spacing', '3')    . 'rem;';
        echo '--ll-kpi-columns:' . $p('layout', 'kpi_columns',     '4')    . ';';

        // Header
        echo '--ll-header-bg:'      . $p('header', 'header_bg_color',   '#0c1a2e') . ';';
        echo '--ll-header-text:'    . $p('header', 'header_text_color', '#e0f2fe') . ';';
        echo '--ll-header-height:'  . $p('header', 'header_height',     '64')      . 'px;';
        echo '--ll-logo-max-height:'. $p('header', 'logo_max_height',   '38')      . 'px;';

        // Footer
        echo '--ll-footer-bg:'   . $p('footer', 'footer_bg_color',   '#0c1a2e') . ';';
        echo '--ll-footer-text:' . $p('footer', 'footer_text_color', '#94a3b8') . ';';
        echo '--ll-footer-link:' . $p('footer', 'footer_link_color', '#bae6fd') . ';';

        // Buttons
        echo '--ll-btn-radius:'    . $p('buttons', 'button_border_radius', '5')     . 'px;';
        echo '--ll-btn-padding-x:' . $p('buttons', 'button_padding_x',     '1.5')   . 'rem;';
        echo '--ll-btn-padding-y:' . $p('buttons', 'button_padding_y',     '0.625') . 'rem;';
        echo '--ll-btn-weight:'    . $p('buttons', 'button_font_weight',   '600')   . ';';
        echo '--ll-btn-transform:' . $p('buttons', 'button_transform',     'none')  . ';';

        echo '--focus-ring:'   . $p('colors', 'accent_color', '#f59e0b') . ';';

        echo '}';

        // Sticky-Header opt-out – Klasse via body wirkt
        $stickyHeader = filter_var(
            $this->customizerGet('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.ll-site-header{position:static;}.ll-site-content{padding-top:0;}';
        }

        // Custom CSS (defensiv gegen </style>-Injection)
        $custom = $this->sanitizeCustomCss((string) $this->customizerGet('advanced', 'custom_css', ''));
        if ($custom !== '') {
            echo $custom;
        }

        echo '</style>' . "\n";
    }

    // ── Menü-Positionen ──────────────────────────────────────────────────────

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary-nav',  'label' => 'Hauptmenü'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Fußzeilen-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Rechtliche Links'];
        return $locations;
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (method_exists($tm, 'registerMenuLocation')) {
            $tm->registerMenuLocation('primary-nav',  'Hauptmenü');
            $tm->registerMenuLocation('footer-nav',   'Fußzeilen-Navigation');
            $tm->registerMenuLocation('footer-legal', 'Rechtliche Links');
        }
    }

    /**
     * Theme-Konfiguration aus theme.json (settings-Block) lesen.
     */
    public function getConfig(string $key, mixed $default = ''): mixed
    {
        static $config = null;

        if ($config === null) {
            $jsonFile = __DIR__ . '/theme.json';
            if (file_exists($jsonFile)) {
                $decoded = json_decode((string) file_get_contents($jsonFile), true);
                $config  = is_array($decoded) ? ($decoded['settings'] ?? []) : [];
            } else {
                $config = [];
            }
        }

        return $config[$key] ?? $default;
    }

    // ── Internal helpers ────────────────────────────────────────────────────

    private function themeBaseUrl(): string
    {
        $tm = \CMS\ThemeManager::instance();
        try {
            $url = (string) $tm->getThemeUrl(LOGILINK_THEME_SLUG);
        } catch (\Throwable) {
            $url = (string) $tm->getThemeUrl();
        }
        return rtrim($url, '/');
    }

    private function customizerGet(string $group, string $key, mixed $default = null): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($group, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }

    /**
     * Mapped Customizer-Auswahl auf konkreten Font-Stack inkl. Fallback-Kette.
     *
     * @param string $choice Wert aus theme.json customization.typography
     * @param string $slot   'body' | 'heading' | 'mono'
     */
    private function mapFontChoice(string $choice, string $slot): string
    {
        $sansStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        $monoStack = 'ui-monospace, SFMono-Regular, "JetBrains Mono", "IBM Plex Mono", Menlo, Consolas, monospace';

        return match ($choice) {
            // Body / heading
            'inter'            => 'Inter, ' . $sansStack,
            'roboto'           => 'Roboto, ' . $sansStack,
            'open-sans'        => '"Open Sans", ' . $sansStack,
            'lato'             => 'Lato, ' . $sansStack,
            'roboto-condensed' => '"Roboto Condensed", ' . $sansStack,
            'montserrat'       => 'Montserrat, ' . $sansStack,
            'barlow-condensed' => '"Barlow Condensed", ' . $sansStack,

            // Mono / data
            'jetbrains-mono'   => '"JetBrains Mono", ' . $monoStack,
            'roboto-mono'      => '"Roboto Mono", ' . $monoStack,
            'fira-code'        => '"Fira Code", ' . $monoStack,
            'monospace'        => $monoStack,

            default            => match ($slot) {
                'heading' => '"Roboto Condensed", ' . $sansStack,
                'mono'    => '"JetBrains Mono", ' . $monoStack,
                default   => 'Inter, ' . $sansStack,
            },
        };
    }

    /**
     * Liefert die `family=…&family=…`-URL-Stückliste für Google Fonts –
     * nur tatsächlich gewählte Familien werden geladen.
     */
    private function googleFontFamilies(): string
    {
        $base    = (string) $this->customizerGet('typography', 'font_family_base',    'inter');
        $heading = (string) $this->customizerGet('typography', 'font_family_heading', 'roboto-condensed');
        $data    = (string) $this->customizerGet('typography', 'font_family_data',    'jetbrains-mono');

        $needed = [];
        foreach ([$base, $heading, $data] as $choice) {
            switch ($choice) {
                case 'inter':
                    $needed['Inter']             = 'Inter:wght@400;500;600;700;800';
                    break;
                case 'roboto':
                    $needed['Roboto']            = 'Roboto:wght@400;500;700';
                    break;
                case 'open-sans':
                    $needed['Open Sans']         = 'Open+Sans:wght@400;600;700';
                    break;
                case 'lato':
                    $needed['Lato']              = 'Lato:wght@400;700';
                    break;
                case 'roboto-condensed':
                    $needed['Roboto Condensed']  = 'Roboto+Condensed:wght@500;700';
                    break;
                case 'montserrat':
                    $needed['Montserrat']        = 'Montserrat:wght@500;700';
                    break;
                case 'barlow-condensed':
                    $needed['Barlow Condensed']  = 'Barlow+Condensed:wght@500;700';
                    break;
                case 'jetbrains-mono':
                    $needed['JetBrains Mono']    = 'JetBrains+Mono:wght@400;600;700';
                    break;
                case 'roboto-mono':
                    $needed['Roboto Mono']       = 'Roboto+Mono:wght@400;600;700';
                    break;
                case 'fira-code':
                    $needed['Fira Code']         = 'Fira+Code:wght@400;600';
                    break;
            }
        }

        if ($needed === []) {
            return '';
        }
        return implode('&family=', $needed);
    }

    private function sanitizeCustomCss(string $raw): string
    {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return '';
        }
        return (string) preg_replace('#</\s*style\s*>#i', '', $trimmed);
    }
}

LogiLink_Theme::instance();

// ── Theme Helper Functions ──────────────────────────────────────────────────

if (!function_exists('theme_is_logged_in')) {
    function theme_is_logged_in(): bool
    {
        try {
            return \CMS\Auth::instance()->isLoggedIn();
        } catch (\Throwable) {
            return false;
        }
    }
}

if (!function_exists('theme_route_url')) {
    /**
     * Theme-lokaler Routen-Resolver. Kennt die gängigen LogiLink-Pfade,
     * fällt sonst auf SITE_URL + Pfad zurück.
     */
    function theme_route_url(string $name): string
    {
        $base = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        return match ($name) {
            'home'     => $base . '/',
            'tracking' => $base . '/tracking',
            'login'    => $base . '/login',
            'register' => $base . '/register',
            'member'   => $base . '/member',
            'partners' => $base . '/partners',
            'routes'   => $base . '/routes',
            'search'   => $base . '/search',
            default    => $base . '/' . ltrim($name, '/'),
        };
    }
}

if (!function_exists('theme_nav_menu')) {
    /**
     * Render a registered navigation menu as a <ul> list.
     */
    function theme_nav_menu(string $location): void
    {
        try {
            $items = \CMS\ThemeManager::instance()->getMenu($location);
        } catch (\Throwable) {
            $items = [];
        }
        if (empty($items)) {
            return;
        }
        $cur = rtrim(parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/', '/');
        echo '<ul>';
        foreach ($items as $item) {
            $rawUrl = (string) ($item['url']   ?? '#');
            $label  = (string) ($item['label'] ?? '');
            $path   = rtrim(parse_url($rawUrl, PHP_URL_PATH) ?? '/', '/');

            $isActive = ($path === '' || $path === '/')
                ? ($cur === '' || $cur === '/')
                : str_starts_with($cur, $path);

            $sanitizedUrl = filter_var($rawUrl, FILTER_VALIDATE_URL)
                ? $rawUrl
                : (str_starts_with($rawUrl, '/') || str_starts_with($rawUrl, '#')
                    ? $rawUrl
                    : '#');

            $target = (!empty($item['target']) && $item['target'] === '_blank')
                ? ' target="_blank" rel="noopener noreferrer"'
                : '';

            $cls = $isActive ? ' class="current-menu-item"' : '';
            echo '<li' . $cls . '><a href="'
                . htmlspecialchars($sanitizedUrl, ENT_QUOTES, 'UTF-8')
                . '"' . $target . '>'
                . htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
                . '</a></li>';
        }
        echo '</ul>';
    }
}

if (!function_exists('get_header')) {
    function get_header(): void
    {
        $path = __DIR__ . '/header.php';
        try {
            $tp = \CMS\ThemeManager::instance()->getThemePath(LOGILINK_THEME_SLUG) . '/header.php';
            if (file_exists($tp)) {
                $path = $tp;
            }
        } catch (\Throwable) {
            // bleibt bei __DIR__
        }
        if (file_exists($path)) {
            require $path;
        }
    }
}

if (!function_exists('get_footer')) {
    function get_footer(): void
    {
        $path = __DIR__ . '/footer.php';
        try {
            $tp = \CMS\ThemeManager::instance()->getThemePath(LOGILINK_THEME_SLUG) . '/footer.php';
            if (file_exists($tp)) {
                $path = $tp;
            }
        } catch (\Throwable) {
            // bleibt bei __DIR__
        }
        if (file_exists($path)) {
            require $path;
        }
    }
}

if (!function_exists('ll_get_setting')) {
    /**
     * Shorthand for ThemeCustomizer::get() with safe fallback.
     */
    function ll_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('ll_get_flash')) {
    /**
     * Return and clear a session flash message array or null.
     *
     * @return array{type:string,message:string}|null
     */
    function ll_get_flash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!empty($_SESSION['ll_flash']) && is_array($_SESSION['ll_flash'])) {
            $flash = $_SESSION['ll_flash'];
            unset($_SESSION['ll_flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('ll_set_flash')) {
    function ll_set_flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION['ll_flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('ll_href')) {
    /**
     * Normalize a link target into an output-safe URL with site host.
     */
    function ll_href(string $target): string
    {
        $trimmed = trim($target);
        $site    = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');

        if ($trimmed === '') {
            return $site . '/';
        }
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }
        if (str_starts_with($trimmed, 'mailto:') || str_starts_with($trimmed, 'tel:')) {
            return $trimmed;
        }
        if (str_starts_with($trimmed, '#')) {
            return $site . '/' . $trimmed;
        }
        return $site . '/' . ltrim($trimmed, '/');
    }
}

if (!function_exists('ll_site_url')) {
    function ll_site_url(): string
    {
        return rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
    }
}

if (!function_exists('ll_site_title')) {
    function ll_site_title(): string
    {
        try {
            return (string) \CMS\ThemeManager::instance()->getSiteTitle();
        } catch (\Throwable) {
            return 'LogiLink';
        }
    }
}

if (!function_exists('ll_body_class')) {
    /**
     * Server-render the <body> class string – kein JS-Mutation der Klasse.
     */
    function ll_body_class(string ...$extra): string
    {
        $classes = ['ll-body'];

        $sticky = filter_var(
            ll_get_setting('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$sticky) {
            $classes[] = 'no-sticky-header';
        }

        $dashboard = filter_var(
            ll_get_setting('layout', 'enable_dashboard_mode', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if ($dashboard) {
            $classes[] = 'has-dashboard-mode';
        }

        if (theme_is_logged_in()) {
            $classes[] = 'is-logged-in';
        }

        foreach ($extra as $c) {
            $c = trim($c);
            if ($c !== '') {
                $classes[] = $c;
            }
        }

        return htmlspecialchars(implode(' ', $classes), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('ll_safe_headline')) {
    /**
     * Erlaubt ausschliesslich `<span class="hl">…</span>` als kontrolliertes
     * Inline-Markup in Überschriften. Alles andere wird HTML-escaped.
     */
    function ll_safe_headline(string $raw): string
    {
        $escaped = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['&lt;span class=&quot;hl&quot;&gt;', '&lt;/span&gt;'],
            ['<span class="ll-hl">', '</span>'],
            $escaped
        );
    }
}
