<?php
declare(strict_types=1);

/**
 * TechNexus Theme – Bootstrap & Customizer Integration
 *
 * IT & Tech Hub Theme for 365CMS v3.x.x on PHP 8.4.
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('TECHNEXUS_THEME_VERSION')) {
    define('TECHNEXUS_THEME_VERSION', '1.0.1');
}

if (!defined('TECHNEXUS_THEME_SLUG')) {
    define('TECHNEXUS_THEME_SLUG', 'technexus');
}

/**
 * TechNexus Theme Singleton
 */
final class TechNexus_Theme
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

        \CMS\Hooks::addAction('init',     [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'registerNavMenus'], 10);
    }

    public function enqueueStyles(): void
    {
        $href = htmlspecialchars(
            $this->themeBaseUrl() . '/style.css?v=' . rawurlencode(TECHNEXUS_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = htmlspecialchars(
            $this->themeBaseUrl() . '/js/navigation.js?v=' . rawurlencode(TECHNEXUS_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }

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

    public function outputMetaTags(): void
    {
        $tm       = \CMS\ThemeManager::instance();
        $siteDesc = htmlspecialchars((string) $tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl  = htmlspecialchars((string) (defined('SITE_URL') ? SITE_URL : ''), ENT_QUOTES, 'UTF-8');

        $themeColor = htmlspecialchars(
            (string) $this->customizerGet('header', 'header_bg_color', '#0c1220'),
            ENT_QUOTES,
            'UTF-8'
        );

        echo '<meta name="description" content="' . $siteDesc . '">' . "\n";
        echo '<meta property="og:url" content="' . $siteUrl . '">' . "\n";
        echo '<meta name="theme-color" content="' . $themeColor . '">' . "\n";
        echo '<meta name="robots" content="index,follow">' . "\n";
    }

    public function outputCustomStyles(): void
    {
        $p = fn(string $g, string $k, string $d): string =>
            htmlspecialchars((string) $this->customizerGet($g, $k, $d), ENT_QUOTES, 'UTF-8');

        $baseChoice    = (string) $this->customizerGet('typography', 'font_family_base',    'inter');
        $headingChoice = (string) $this->customizerGet('typography', 'font_family_heading', 'plus-jakarta-sans');
        $codeChoice    = (string) $this->customizerGet('typography', 'font_family_code',    'jetbrains-mono');

        $baseFont    = htmlspecialchars($this->mapFontChoice($baseChoice,    'body'),    ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingChoice, 'heading'), ENT_QUOTES, 'UTF-8');
        $codeFont    = htmlspecialchars($this->mapFontChoice($codeChoice,    'mono'),    ENT_QUOTES, 'UTF-8');

        echo '<style id="tn-custom-vars">:root{';

        // Colors
        echo '--tn-primary:'       . $p('colors', 'primary_color',   '#0891b2') . ';';
        echo '--tn-primary-hover:' . $p('colors', 'secondary_color', '#0e7490') . ';';
        echo '--tn-accent:'        . $p('colors', 'accent_color',    '#22d3ee') . ';';
        echo '--tn-code:'          . $p('colors', 'code_color',      '#34d399') . ';';
        echo '--tn-text:'          . $p('colors', 'text_color',      '#0f172a') . ';';
        echo '--tn-bg:'            . $p('colors', 'bg_color',        '#f4f6f8') . ';';
        echo '--tn-card-bg:'       . $p('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--tn-dark-bg:'       . $p('colors', 'dark_bg_color',   '#0c1220') . ';';
        echo '--tn-dark-surface:'  . $p('colors', 'dark_surface_color', '#151d2e') . ';';
        echo '--tn-link:'          . $p('colors', 'link_color',      '#0891b2') . ';';
        echo '--tn-muted:'         . $p('colors', 'muted_color',     '#5c6b7a') . ';';
        echo '--tn-border:'        . $p('colors', 'border_color',    '#d8dee6') . ';';
        echo '--tn-success:'       . $p('colors', 'success_color',   '#22c55e') . ';';
        echo '--tn-error:'         . $p('colors', 'error_color',     '#ef4444') . ';';
        echo '--tn-warning:'       . $p('colors', 'warning_color',   '#f59e0b') . ';';
        echo '--focus-ring:'       . $p('colors', 'accent_color',    '#22d3ee') . ';';

        // Legacy aliases (style.css)
        echo '--primary-color:'     . $p('colors', 'primary_color',   '#0891b2') . ';';
        echo '--primary-hover:'     . $p('colors', 'secondary_color', '#0e7490') . ';';
        echo '--accent-color:'      . $p('colors', 'accent_color',    '#22d3ee') . ';';
        echo '--code-color:'        . $p('colors', 'code_color',      '#34d399') . ';';
        echo '--text-color:'        . $p('colors', 'text_color',      '#0f172a') . ';';
        echo '--text-primary:'      . $p('colors', 'text_color',      '#0f172a') . ';';
        echo '--heading-color:'     . $p('colors', 'text_color',      '#0f172a') . ';';
        echo '--background-color:'  . $p('colors', 'bg_color',        '#f4f6f8') . ';';
        echo '--card-bg:'           . $p('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--muted-color:'       . $p('colors', 'muted_color',     '#5c6b7a') . ';';
        echo '--border-color:'      . $p('colors', 'border_color',    '#d8dee6') . ';';
        echo '--tn-link:'           . $p('colors', 'link_color',      '#0891b2') . ';';
        echo '--link-color:'        . $p('colors', 'link_color',      '#0891b2') . ';';
        echo '--success-color:'     . $p('colors', 'success_color',   '#22c55e') . ';';
        echo '--error-color:'       . $p('colors', 'error_color',     '#ef4444') . ';';
        echo '--warning-color:'     . $p('colors', 'warning_color',   '#f59e0b') . ';';
        echo '--status-online:'     . $p('colors', 'success_color',   '#22c55e') . ';';
        echo '--status-busy:'       . $p('colors', 'warning_color',   '#f59e0b') . ';';

        // Typography
        echo '--tn-font-body:'      . $baseFont    . ';';
        echo '--tn-font-heading:'   . $headingFont . ';';
        echo '--tn-font-code:'      . $codeFont    . ';';
        echo '--font-body:'         . $baseFont    . ';';
        echo '--font-heading:'      . $headingFont . ';';
        echo '--font-code:'         . $codeFont    . ';';
        echo '--tn-font-size-base:' . $p('typography', 'font_size_base',     '16') . 'px;';
        echo '--tn-line-height:'    . $p('typography', 'line_height_base',   '1.6') . ';';
        echo '--tn-heading-weight:' . $p('typography', 'font_weight_heading','700') . ';';

        // Layout
        echo '--tn-max-width:'      . $p('layout', 'container_width',  '1280') . 'px;';
        echo '--container-max-width:' . $p('layout', 'container_width', '1280') . 'px;';
        echo '--tn-content-pad:'    . $p('layout', 'content_padding',  '2')   . 'rem;';
        echo '--container-padding:' . $p('layout', 'content_padding',  '2')   . 'rem;';
        echo '--tn-radius:'         . $p('layout', 'border_radius',    '6')   . 'px;';
        echo '--border-radius:'     . $p('layout', 'border_radius',    '6')   . 'px;';
        echo '--tn-section-pad:'    . $p('layout', 'section_spacing',  '5')   . 'rem;';
        echo '--tn-card-columns:'   . $p('layout', 'card_columns',     '3')   . ';';

        // Header
        echo '--tn-header-bg:'      . $p('header', 'header_bg_color',   '#0c1220') . ';';
        echo '--header-bg:'         . $p('header', 'header_bg_color',   '#0c1220') . ';';
        echo '--tn-header-text:'    . $p('header', 'header_text_color', '#e8edf2') . ';';
        echo '--header-text:'       . $p('header', 'header_text_color', '#e8edf2') . ';';
        echo '--tn-header-height:'  . $p('header', 'header_height',     '72')      . 'px;';
        echo '--tn-logo-max-height:' . $p('header', 'logo_max_height',   '40')      . 'px;';
        echo '--logo-max-height:'    . $p('header', 'logo_max_height',   '40')      . 'px;';

        // Footer
        echo '--tn-footer-bg:'     . $p('footer', 'footer_bg_color',   '#0c1220') . ';';
        echo '--footer-bg:'         . $p('footer', 'footer_bg_color',   '#0c1220') . ';';
        echo '--tn-footer-text:'   . $p('footer', 'footer_text_color', '#94a3b8') . ';';
        echo '--footer-text:'      . $p('footer', 'footer_text_color', '#94a3b8') . ';';
        echo '--tn-footer-link:'   . $p('footer', 'footer_link_color', '#cbd5e1') . ';';

        // Buttons
        echo '--tn-btn-radius:'     . $p('buttons', 'button_border_radius', '6')     . 'px;';
        echo '--tn-btn-padding-x:'  . $p('buttons', 'button_padding_x',     '1.5')   . 'rem;';
        echo '--tn-btn-padding-y:'  . $p('buttons', 'button_padding_y',     '0.625') . 'rem;';
        echo '--tn-btn-weight:'     . $p('buttons', 'button_font_weight',   '600')   . ';';
        echo '--tn-btn-transform:'  . $p('buttons', 'button_transform',     'none')  . ';';

        // Hero
        echo '--hero-grad-start:'   . $p('tech_hero', 'hero_gradient_start', '#0c1220') . ';';
        echo '--hero-grad-end:'     . $p('tech_hero', 'hero_gradient_end',   '#132238') . ';';

        echo '}';

        $headerHeight = (int) $this->customizerGet('header', 'header_height', 72);
        echo '.site-header{height:' . max(48, min(120, $headerHeight)) . 'px;}';

        $stickyHeader = filter_var(
            $this->customizerGet('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.site-header{position:static;}';
        }

        $showShadow = filter_var(
            $this->customizerGet('header', 'show_header_shadow', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$showShadow) {
            echo '.site-header.scrolled{box-shadow:none;}';
        }

        $blurHeader = filter_var(
            $this->customizerGet('header', 'enable_blur_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$blurHeader) {
            echo '.site-header.blur-enabled.scrolled{backdrop-filter:none;}';
        }

        $cardCols = (string) $this->customizerGet('layout', 'card_columns', '3');
        $colMap   = ['2' => '2', '3' => '3', '4' => '4'];
        $cols     = $colMap[$cardCols] ?? '3';
        echo '.tech-grid{grid-template-columns:repeat(' . $cols . ',minmax(0,1fr));}';

        $custom = $this->sanitizeCustomCss((string) $this->customizerGet('advanced', 'custom_css', ''));
        if ($custom !== '') {
            echo $custom;
        }

        echo '</style>' . "\n";
    }

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',      'label' => 'Hauptnavigation'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Footer-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Footer Rechtliche Links'];
        $locations[] = ['slug' => 'tech-cats',    'label' => 'Tech-Kategorien'];
        return $locations;
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (!method_exists($tm, 'registerMenuLocation')) {
            return;
        }
        $tm->registerMenuLocation('primary',      'Hauptnavigation');
        $tm->registerMenuLocation('footer-nav',   'Footer-Navigation');
        $tm->registerMenuLocation('footer-legal', 'Footer Rechtliche Links');
        $tm->registerMenuLocation('tech-cats',    'Tech-Kategorien');
    }

    private function themeBaseUrl(): string
    {
        $tm = \CMS\ThemeManager::instance();
        try {
            $url = (string) $tm->getThemeUrl(TECHNEXUS_THEME_SLUG);
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

    private function mapFontChoice(string $choice, string $slot): string
    {
        $sansStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        $monoStack = 'ui-monospace, SFMono-Regular, "JetBrains Mono", Menlo, Consolas, monospace';

        return match ($choice) {
            'inter'              => 'Inter, ' . $sansStack,
            'roboto'             => 'Roboto, ' . $sansStack,
            'open-sans'          => '"Open Sans", ' . $sansStack,
            'poppins'            => 'Poppins, ' . $sansStack,
            'plus-jakarta-sans'  => '"Plus Jakarta Sans", ' . $sansStack,
            'montserrat'         => 'Montserrat, ' . $sansStack,
            'raleway'            => 'Raleway, ' . $sansStack,
            'system'             => $sansStack,
            'jetbrains-mono'     => '"JetBrains Mono", ' . $monoStack,
            'fira-code'          => '"Fira Code", ' . $monoStack,
            'source-code-pro'    => '"Source Code Pro", ' . $monoStack,
            'monospace'          => $monoStack,
            default              => match ($slot) {
                'heading' => '"Plus Jakarta Sans", ' . $sansStack,
                'mono'    => '"JetBrains Mono", ' . $monoStack,
                default   => 'Inter, ' . $sansStack,
            },
        };
    }

    private function googleFontFamilies(): string
    {
        $base    = (string) $this->customizerGet('typography', 'font_family_base',    'inter');
        $heading = (string) $this->customizerGet('typography', 'font_family_heading', 'plus-jakarta-sans');
        $code    = (string) $this->customizerGet('typography', 'font_family_code',    'jetbrains-mono');

        $needed = [];
        foreach ([$base, $heading, $code] as $choice) {
            switch ($choice) {
                case 'inter':
                    $needed['Inter']               = 'Inter:wght@400;500;600;700';
                    break;
                case 'roboto':
                    $needed['Roboto']              = 'Roboto:wght@400;500;700';
                    break;
                case 'open-sans':
                    $needed['Open Sans']           = 'Open+Sans:wght@400;600;700';
                    break;
                case 'poppins':
                    $needed['Poppins']             = 'Poppins:wght@400;600;700';
                    break;
                case 'plus-jakarta-sans':
                    $needed['Plus Jakarta Sans']   = 'Plus+Jakarta+Sans:wght@600;700;800';
                    break;
                case 'montserrat':
                    $needed['Montserrat']          = 'Montserrat:wght@600;700';
                    break;
                case 'raleway':
                    $needed['Raleway']             = 'Raleway:wght@600;700';
                    break;
                case 'jetbrains-mono':
                    $needed['JetBrains Mono']      = 'JetBrains+Mono:wght@400;500';
                    break;
                case 'fira-code':
                    $needed['Fira Code']           = 'Fira+Code:wght@400;500';
                    break;
                case 'source-code-pro':
                    $needed['Source Code Pro']     = 'Source+Code+Pro:wght@400;600';
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

TechNexus_Theme::instance();

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
            'home'      => $base . '/',
            'search'    => $base . '/search',
            'login'     => $base . '/login',
            'register'  => $base . '/register',
            'member'    => $base . '/member',
            'experts'   => $base . '/it-experts',
            'companies' => $base . '/companies',
            'events'    => $base . '/events',
            'jobs'      => $base . '/jobs',
            default     => $base . '/' . ltrim($name, '/'),
        };
    }
}

if (!function_exists('theme_nav_menu')) {
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(TECHNEXUS_THEME_SLUG) . '/header.php';
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(TECHNEXUS_THEME_SLUG) . '/footer.php';
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

if (!function_exists('tn_get_setting')) {
    function tn_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('technexus_get_setting')) {
    function technexus_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        return tn_get_setting($section, $key, $default);
    }
}

if (!function_exists('tn_get_flash')) {
    /** @return array{type:string,message:string}|null */
    function tn_get_flash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!empty($_SESSION['tn_flash']) && is_array($_SESSION['tn_flash'])) {
            $flash = $_SESSION['tn_flash'];
            unset($_SESSION['tn_flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('tn_set_flash')) {
    function tn_set_flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION['tn_flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('tn_href')) {
    function tn_href(string $target): string
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

if (!function_exists('tn_site_title')) {
    function tn_site_title(): string
    {
        try {
            return (string) \CMS\ThemeManager::instance()->getSiteTitle();
        } catch (\Throwable) {
            return 'TechNexus';
        }
    }
}

if (!function_exists('tn_body_class')) {
    function tn_body_class(string ...$extra): string
    {
        $classes = ['tn-body'];

        $sticky = filter_var(
            tn_get_setting('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if ($sticky) {
            $classes[] = 'has-sticky-header';
        } else {
            $classes[] = 'no-sticky-header';
        }

        if (filter_var(tn_get_setting('layout', 'enable_grid_background', true), FILTER_VALIDATE_BOOLEAN)) {
            $classes[] = 'tn-grid-bg';
        }

        $hover = (string) tn_get_setting('tech_expert_cards', 'card_hover_effect', 'border');
        if (in_array($hover, ['lift', 'glow', 'border', 'none'], true)) {
            $classes[] = 'tn-card-hover-' . $hover;
        }

        if (filter_var(tn_get_setting('advanced', 'prefers_dark_mode', false), FILTER_VALIDATE_BOOLEAN)) {
            $classes[] = 'tn-prefers-dark';
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

if (!function_exists('tn_safe_headline')) {
    function tn_safe_headline(string $raw): string
    {
        $escaped = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['&lt;span class=&quot;tn-hl&quot;&gt;', '&lt;/span&gt;'],
            ['<span class="tn-hl">', '</span>'],
            $escaped
        );
    }
}

if (!function_exists('tn_html_attr')) {
    function tn_html_attr(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
