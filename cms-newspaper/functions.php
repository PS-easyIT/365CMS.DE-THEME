<?php
declare(strict_types=1);

/**
 * CMS Newspaper Theme – Bootstrap & Customizer Integration
 *
 * Editorial news / magazine theme for 365CMS v3.x.x on PHP 8.4.
 *
 * @package CmsNewspaper_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('CMSNEWSPAPER_THEME_VERSION')) {
    define('CMSNEWSPAPER_THEME_VERSION', '1.0.0');
}

if (!defined('CMSNEWSPAPER_THEME_SLUG')) {
    define('CMSNEWSPAPER_THEME_SLUG', 'cms-newspaper');
}

/**
 * Newspaper Theme Singleton
 */
final class CmsNewspaper_Theme
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
        // Assets in <head>
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'],      1);
        \CMS\Hooks::addAction('head', [$this, 'outputGoogleFonts'],  5);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'],     10);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'], 15);

        // Deferred navigation script
        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 99);

        // Menü-Locations via Filter
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Menüs registrieren – auf BEIDEN Hooks für v3-Bootstrap-Kompatibilität
        \CMS\Hooks::addAction('init',     [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'registerNavMenus'], 10);
    }

    // ── Assets ───────────────────────────────────────────────────────────────

    public function enqueueStyles(): void
    {
        $themeUrl = $this->themeBaseUrl();
        $href     = htmlspecialchars(
            $themeUrl . '/style.css?v=' . rawurlencode(CMSNEWSPAPER_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $themeUrl = $this->themeBaseUrl();
        $src      = htmlspecialchars(
            $themeUrl . '/js/navigation.js?v=' . rawurlencode(CMSNEWSPAPER_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }

    /**
     * Google Fonts – preconnect + nur benötigte Familien.
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

    // ── Meta ─────────────────────────────────────────────────────────────────

    public function outputMetaTags(): void
    {
        $tm       = \CMS\ThemeManager::instance();
        $siteDesc = htmlspecialchars((string) $tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl  = htmlspecialchars((string) (defined('SITE_URL') ? SITE_URL : ''), ENT_QUOTES, 'UTF-8');

        $themeColor = htmlspecialchars(
            (string) $this->customizerGet('colors', 'ink_color', '#0a0b0c'),
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

        $displayChoice = (string) $this->customizerGet('typography', 'font_family_display', 'playfair');
        $baseChoice    = (string) $this->customizerGet('typography', 'font_family_base',    'source-sans');
        $monoChoice    = (string) $this->customizerGet('typography', 'font_family_mono',    'plex-mono');

        $displayFont = htmlspecialchars($this->mapFontChoice($displayChoice, 'display'), ENT_QUOTES, 'UTF-8');
        $baseFont    = htmlspecialchars($this->mapFontChoice($baseChoice,    'body'),    ENT_QUOTES, 'UTF-8');
        $monoFont    = htmlspecialchars($this->mapFontChoice($monoChoice,    'mono'),    ENT_QUOTES, 'UTF-8');

        echo '<style id="news-custom-vars">:root{';

        // Colors – editoriale Restpalette
        echo '--news-ink:'         . $p('colors', 'ink_color',     '#0a0b0c') . ';';
        echo '--news-ink-mid:'     . $p('colors', 'ink_mid',       '#3a3b3c') . ';';
        echo '--news-ink-dim:'     . $p('colors', 'ink_dim',       '#6b6c6d') . ';';
        echo '--news-ink-ghost:'   . $p('colors', 'ink_ghost',     '#9a9b9c') . ';';
        echo '--news-paper:'       . $p('colors', 'paper_color',   '#f8f9fa') . ';';
        echo '--news-surface:'     . $p('colors', 'surface_color', '#ffffff') . ';';
        echo '--news-accent:'      . $p('colors', 'accent_color',  '#e32d12') . ';';
        echo '--news-accent-hover:'. $p('colors', 'accent_hover',  '#c22510') . ';';
        echo '--news-rule:'        . $p('colors', 'border_color',  '#e4e5e7') . ';';
        echo '--news-live:'        . $p('colors', 'live_color',    '#22c55e') . ';';
        echo '--focus-ring:'       . $p('colors', 'accent_color',  '#e32d12') . ';';

        // Typography
        echo '--news-font-display:'. $displayFont . ';';
        echo '--news-font-body:'   . $baseFont    . ';';
        echo '--news-font-mono:'   . $monoFont    . ';';
        echo '--news-font-size-base:' . $p('typography', 'font_size_base',   '16')   . 'px;';
        echo '--news-line-height:'    . $p('typography', 'line_height_base', '1.6')  . ';';

        // Layout
        echo '--news-max-width:'   . $p('layout', 'container_width', '1040') . 'px;';
        echo '--news-section-pad:' . $p('layout', 'section_spacing', '4')    . 'rem;';
        echo '--news-radius:'      . $p('layout', 'border_radius',   '6')    . 'px;';

        // Buttons
        echo '--news-btn-radius:'    . $p('buttons', 'button_border_radius', '4')   . 'px;';
        echo '--news-btn-padding-x:' . $p('buttons', 'button_padding_x',     '1.5') . 'rem;';
        echo '--news-btn-padding-y:' . $p('buttons', 'button_padding_y',     '0.75'). 'rem;';
        echo '--news-btn-transform:' . $p('buttons', 'button_transform',     'uppercase') . ';';

        echo '}';

        // Sticky-Header opt-out
        $stickyHeader = filter_var(
            $this->customizerGet('header', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.news-header{position:static;box-shadow:none;}';
        }

        // Custom CSS (defensiv gegen </style>-Injection)
        $custom = $this->sanitizeCustomCss((string) $this->customizerGet('advanced', 'custom_css', ''));
        if ($custom !== '') {
            echo $custom;
        }

        echo '</style>' . "\n";
    }

    // ── Menü-Positionen ───────────────────────────────────────────────────────

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary-nav',  'label' => 'Hauptnavigation (Ressorts)'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Footer-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Footer Rechtliche Links'];
        $locations[] = ['slug' => 'topics-nav',   'label' => 'Themen-Schnellnavigation (Sub-Bar)'];
        return $locations;
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (method_exists($tm, 'registerMenuLocation')) {
            $tm->registerMenuLocation('primary-nav',  'Hauptnavigation (Ressorts)');
            $tm->registerMenuLocation('footer-nav',   'Footer-Navigation');
            $tm->registerMenuLocation('footer-legal', 'Footer Rechtliche Links');
            $tm->registerMenuLocation('topics-nav',   'Themen-Schnellnavigation (Sub-Bar)');
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

    // ── Internal helpers ─────────────────────────────────────────────────────

    private function themeBaseUrl(): string
    {
        $tm = \CMS\ThemeManager::instance();
        try {
            $url = (string) $tm->getThemeUrl(CMSNEWSPAPER_THEME_SLUG);
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
     * @param string $slot   'display' | 'body' | 'mono'
     */
    private function mapFontChoice(string $choice, string $slot): string
    {
        $sansStack  = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        $serifStack = 'Georgia, "Times New Roman", "Source Serif 4", serif';
        $monoStack  = 'ui-monospace, SFMono-Regular, "JetBrains Mono", "IBM Plex Mono", Menlo, Consolas, monospace';

        return match ($choice) {
            // Display / Headlines
            'playfair'     => '"Playfair Display", ' . $serifStack,
            'source-serif' => '"Source Serif 4", ' . $serifStack,
            'eb-garamond'  => '"EB Garamond", ' . $serifStack,
            'lora'         => 'Lora, ' . $serifStack,
            'syne'         => 'Syne, ' . $sansStack,

            // Body
            'source-sans'  => '"Source Sans 3", ' . $sansStack,
            'inter'        => 'Inter, ' . $sansStack,
            'barlow'       => 'Barlow, ' . $sansStack,
            'system'       => $sansStack,

            // Mono
            'plex-mono'    => '"IBM Plex Mono", ' . $monoStack,
            'jetbrains'    => '"JetBrains Mono", ' . $monoStack,
            'system-mono'  => $monoStack,

            default        => match ($slot) {
                'display' => '"Playfair Display", ' . $serifStack,
                'mono'    => '"IBM Plex Mono", ' . $monoStack,
                default   => '"Source Sans 3", ' . $sansStack,
            },
        };
    }

    private function googleFontFamilies(): string
    {
        $display = (string) $this->customizerGet('typography', 'font_family_display', 'playfair');
        $base    = (string) $this->customizerGet('typography', 'font_family_base',    'source-sans');
        $mono    = (string) $this->customizerGet('typography', 'font_family_mono',    'plex-mono');

        $needed = [];
        foreach ([$display, $base, $mono] as $choice) {
            switch ($choice) {
                case 'playfair':
                    $needed['Playfair Display'] = 'Playfair+Display:wght@600;700;800';
                    break;
                case 'source-serif':
                    $needed['Source Serif 4']   = 'Source+Serif+4:wght@500;700';
                    break;
                case 'eb-garamond':
                    $needed['EB Garamond']      = 'EB+Garamond:wght@500;700';
                    break;
                case 'lora':
                    $needed['Lora']             = 'Lora:wght@500;700';
                    break;
                case 'syne':
                    $needed['Syne']             = 'Syne:wght@700;800';
                    break;
                case 'source-sans':
                    $needed['Source Sans 3']    = 'Source+Sans+3:wght@400;600;700';
                    break;
                case 'inter':
                    $needed['Inter']            = 'Inter:wght@400;500;700';
                    break;
                case 'barlow':
                    $needed['Barlow']           = 'Barlow:wght@400;500;700';
                    break;
                case 'plex-mono':
                    $needed['IBM Plex Mono']    = 'IBM+Plex+Mono:wght@400;600';
                    break;
                case 'jetbrains':
                    $needed['JetBrains Mono']   = 'JetBrains+Mono:wght@400;600';
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

CmsNewspaper_Theme::instance();

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
    function theme_route_url(string $name): string
    {
        $base = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        return match ($name) {
            'home'     => $base . '/',
            'search'   => $base . '/search',
            'login'    => $base . '/login',
            'register' => $base . '/register',
            'archive'  => $base . '/archiv',
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(CMSNEWSPAPER_THEME_SLUG) . '/header.php';
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(CMSNEWSPAPER_THEME_SLUG) . '/footer.php';
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

if (!function_exists('news_get_setting')) {
    /**
     * Shorthand for ThemeCustomizer::get() with safe fallback.
     */
    function news_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('news_get_flash')) {
    /**
     * Return and clear a session flash message array or null.
     *
     * @return array{type:string,message:string}|null
     */
    function news_get_flash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!empty($_SESSION['news_flash']) && is_array($_SESSION['news_flash'])) {
            $flash = $_SESSION['news_flash'];
            unset($_SESSION['news_flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('news_set_flash')) {
    function news_set_flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION['news_flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('news_config')) {
    /**
     * Read a piece of theme content – first from Customizer (relevant group),
     * then from theme.json `settings` block, then static default.
     */
    function news_config(string $key, mixed $default = ''): mixed
    {
        static $heroKeys = [
            'hero_kicker'     => true,
            'hero_headline'   => true,
            'hero_lead'       => true,
            'hero_cta_label'  => true,
            'hero_cta_url'    => true,
        ];
        $group = isset($heroKeys[$key]) ? 'news_hero' : 'news_content';

        $val = news_get_setting($group, $key, null);
        if (is_string($val)) {
            if ($val !== '') {
                return $val;
            }
        } elseif ($val !== null) {
            return $val;
        }

        return CmsNewspaper_Theme::instance()->getConfig($key, $default);
    }
}

if (!function_exists('news_href')) {
    /**
     * Normalize a link target (absolute URL, mailto:, tel:, anchor or relative)
     * into an output-safe URL with leading host where appropriate.
     */
    function news_href(string $target): string
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

if (!function_exists('news_site_title')) {
    function news_site_title(): string
    {
        try {
            return (string) \CMS\ThemeManager::instance()->getSiteTitle();
        } catch (\Throwable) {
            return 'PHINIT.DE';
        }
    }
}

if (!function_exists('news_body_class')) {
    /**
     * Server-render the <body> class string – no JS-mutation of classes.
     */
    function news_body_class(string ...$extra): string
    {
        $classes = ['news-body'];

        $sticky = filter_var(
            news_get_setting('header', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if ($sticky) {
            $classes[] = 'has-sticky-header';
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

if (!function_exists('news_brand_html')) {
    /**
     * Render the wordmark allowing only the inner `<span>` highlight
     * (e.g. PHIN<span>IT</span>.DE). Everything else is escaped.
     */
    function news_brand_html(string $raw): string
    {
        $escaped = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['&lt;span&gt;', '&lt;/span&gt;'],
            ['<span class="news-wordmark-accent">', '</span>'],
            $escaped
        );
    }
}
