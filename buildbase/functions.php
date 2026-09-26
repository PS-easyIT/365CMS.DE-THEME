<?php
declare(strict_types=1);

/**
 * BuildBase Theme – Bootstrap & Customizer Integration
 *
 * Construction / trade / project-delivery theme for 365CMS v3.x.x on PHP 8.4.
 *
 * @package BuildBase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('BUILDBASE_THEME_VERSION')) {
    define('BUILDBASE_THEME_VERSION', '3.1.0');
}
if (!defined('BUILDBASE_THEME_SLUG')) {
    define('BUILDBASE_THEME_SLUG', 'buildbase');
}
if (!defined('THEME_VERSION')) {
    define('THEME_VERSION', BUILDBASE_THEME_VERSION);
}

final class BuildBase_Theme {
    private static ?self $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->registerHooks();
    }

    private function registerHooks(): void {
        \CMS\Hooks::addAction('head',          [$this, 'enqueueStyles'],            1);
        \CMS\Hooks::addAction('head',          [$this, 'outputGoogleFonts'],        5);
        \CMS\Hooks::addAction('head',          [$this, 'outputCustomStyles'],       15);
        \CMS\Hooks::addAction('before_footer', [$this, 'outputNavigationScript'],   99);
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);
    }

    /**
     * Menüpositionen über den Core-Filter register_menu_locations anmelden
     * (ThemeManager::registerMenuLocation() existiert in 365CMS nicht).
     *
     * @param array<int, array{slug:string,label:string}> $locations
     * @return array<int, array{slug:string,label:string}>
     */
    public function registerMenuLocations(array $locations): array {
        $locations[] = ['slug' => 'primary-nav',  'label' => 'Hauptmenü'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Fußzeilen-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Rechtliche Links'];
        return $locations;
    }

    public function enqueueStyles(): void {
        $themeUrl = (string) \CMS\ThemeManager::instance()->getThemeUrl('buildbase');
        $href = htmlspecialchars(
            rtrim($themeUrl, '/') . '/style.css?v=' . BUILDBASE_THEME_VERSION,
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function outputGoogleFonts(): void {
        if (theme_use_local_fonts()) {
            return; // Core bindet lokale Schriften ein (DSGVO)
        }
        $fonts = 'Roboto:wght@400;500;700&family=Roboto+Condensed:wght@600;700'
               . '&family=Oswald:wght@500;700'
               . '&family=Open+Sans:wght@400;600;700'
               . '&family=Lato:wght@400;700'
               . '&family=Inter:wght@400;600;700'
               . '&family=Bebas+Neue';
        $url = 'https://fonts.googleapis.com/css2?family=' . $fonts . '&display=swap';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function mapFontChoice(string $fontChoice, string $fallback): string {
        return match ($fontChoice) {
            'roboto'           => 'Roboto, sans-serif',
            'open-sans'        => '"Open Sans", sans-serif',
            'lato'             => 'Lato, sans-serif',
            'inter'            => 'Inter, sans-serif',
            'roboto-condensed' => '"Roboto Condensed", sans-serif',
            'bebas-neue'       => '"Bebas Neue", sans-serif',
            'oswald'           => 'Oswald, sans-serif',
            default            => $fallback,
        };
    }

    private function sanitizeCustomCss(string $raw): string {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return '';
        }

        $clean = preg_replace('#<\/?\s*style\b[^>]*>#i', '', $trimmed) ?? '';
        $clean = preg_replace('/@import\b[^;]*;?/i', '', $clean) ?? '';
        $clean = preg_replace('/expression\s*\([^)]*\)/i', '', $clean) ?? '';
        $protocolSinkPattern = '/url\s*\(\s*[\'\"]?\s*java' . 'script:[^)]+\)/i';
        $clean = preg_replace($protocolSinkPattern, '', $clean) ?? '';
        $clean = preg_replace('/\b(?:behavior|binding)\s*:[^;]+;?/i', '', $clean) ?? '';

        return str_replace('</', '<\/', $clean);
    }

    public function outputCustomStyles(): void {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
        } catch (\Throwable $e) {
            return;
        }

        $color = fn(string $s, string $k, string $d): string =>
            buildbase_css_color((string) $c->get($s, $k, $d), $d);
        $num = fn(string $s, string $k, string $d, float $min, float $max): string =>
            buildbase_css_number($c->get($s, $k, $d), (float) $d, $min, $max);
        $choice = fn(string $s, string $k, array $allowed, string $d): string =>
            buildbase_css_choice((string) $c->get($s, $k, $d), $allowed, $d);

        $baseFontChoice    = (string) $c->get('typography', 'font_family_base',    'roboto');
        $headingFontChoice = (string) $c->get('typography', 'font_family_heading', 'roboto-condensed');
        $badgeFontChoice   = (string) $c->get('typography', 'font_family_badge',   'oswald');

        $baseFont    = htmlspecialchars($this->mapFontChoice($baseFontChoice,    'Roboto, sans-serif'),               ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingFontChoice, '"Roboto Condensed", sans-serif'),   ENT_QUOTES, 'UTF-8');
        $badgeFont   = htmlspecialchars($this->mapFontChoice($badgeFontChoice,   'Oswald, sans-serif'),               ENT_QUOTES, 'UTF-8');

        echo '<style id="bb-custom-vars"' . theme_csp_nonce_attr() . '>:root{';
        // Colors
        echo '--primary-color:'     . $color('colors', 'primary_color',    '#b45309') . ';';
        echo '--primary-dark:'      . $color('colors', 'secondary_color',  '#92400e') . ';';
        echo '--secondary-color:'   . $color('colors', 'secondary_color',  '#92400e') . ';';
        echo '--accent-color:'      . $color('colors', 'accent_color',     '#d97706') . ';';
        echo '--safety-color:'      . $color('colors', 'safety_color',     '#f59e0b') . ';';
        echo '--bg-primary:'        . $color('colors', 'bg_color',         '#fafaf9') . ';';
        echo '--bg-secondary:'      . $color('colors', 'section_dark_bg',  '#292524') . ';';
        echo '--bg-card:'           . $color('colors', 'card_bg_color',    '#ffffff') . ';';
        echo '--text-primary:'      . $color('colors', 'text_color',       '#1c1917') . ';';
        echo '--text-secondary:'    . $color('colors', 'muted_color',      '#78716c') . ';';
        echo '--muted-color:'       . $color('colors', 'muted_color',      '#78716c') . ';';
        echo '--border-color:'      . $color('colors', 'border_color',     '#d6d3d1') . ';';
        echo '--link-color:'        . $color('colors', 'link_color',       '#b45309') . ';';
        echo '--success-color:'     . $color('colors', 'success_color',    '#16a34a') . ';';
        echo '--error-color:'       . $color('colors', 'error_color',      '#dc2626') . ';';
        echo '--focus-ring:'        . $color('colors', 'accent_color',     '#d97706') . ';';

        // Header
        echo '--header-bg:'         . $color('header', 'header_bg_color',   '#292524') . ';';
        echo '--header-text:'       . $color('header', 'header_text_color', '#fafaf9') . ';';
        echo '--header-height:'     . $num('header', 'header_height',     '68', 48, 120) . 'px;';
        echo '--logo-max-height:'   . $num('header', 'logo_max_height',   '44', 24, 100) . 'px;';

        // Footer
        echo '--footer-bg:'         . $color('footer', 'footer_bg_color',   '#1c1917') . ';';
        echo '--footer-text:'       . $color('footer', 'footer_text_color', '#a8a29e') . ';';
        echo '--footer-link:'       . $color('footer', 'footer_link_color', '#d6d3d1') . ';';

        // Layout
        echo '--container-max:'     . $num('layout', 'container_width',  '1200', 800, 1920) . 'px;';
        echo '--content-padding:'   . $num('layout', 'content_padding',  '2', 0.5, 6)    . 'rem;';
        echo '--radius-sm:'         . $num('layout', 'border_radius',    '4', 0, 16)    . 'px;';
        echo '--section-spacing:'   . $num('layout', 'section_spacing',  '4', 1, 12)    . 'rem;';

        // Hero gradient
        echo '--hero-gradient-start:' . $color('build_hero', 'hero_gradient_start', '#292524') . ';';
        echo '--hero-gradient-end:'   . $color('build_hero', 'hero_gradient_end',   '#44403c') . ';';

        // Buttons
        echo '--btn-radius:'         . $num('buttons', 'button_border_radius', '4', 0, 50)    . 'px;';
        echo '--btn-padding-x:'      . $num('buttons', 'button_padding_x',     '1.75', 0.5, 4) . 'rem;';
        echo '--btn-padding-y:'      . $num('buttons', 'button_padding_y',     '0.75', 0.25, 2) . 'rem;';
        echo '--btn-font-weight:'    . $choice('buttons', 'button_font_weight', ['600', '700'], '700')  . ';';
        echo '--btn-transform:'      . $choice('buttons', 'button_transform', ['none', 'uppercase'], 'uppercase') . ';';

        // Typography
        echo '--font-body:'          . $baseFont    . ';';
        echo '--font-heading:'       . $headingFont . ';';
        echo '--font-condensed:'     . $badgeFont   . ';';
        echo '--font-size-base:'     . $num('typography', 'font_size_base',     '16', 14, 20)  . 'px;';
        echo '--line-height-base:'   . $num('typography', 'line_height_base',   '1.6', 1.2, 2.5) . ';';
        echo '--font-weight-heading:'. $choice('typography', 'font_weight_heading', ['700', '800', '900'], '800') . ';';
        echo '}';

        $custom = (string) $c->get('advanced', 'custom_css', '');
        $safeCustom = $this->sanitizeCustomCss($custom);
        if ($safeCustom !== '') {
            echo $safeCustom;
        }
        echo '</style>' . "\n";
    }

    public function outputNavigationScript(): void {
        $themeUrl = (string) \CMS\ThemeManager::instance()->getThemeUrl('buildbase');
        $src = htmlspecialchars(
            rtrim($themeUrl, '/') . '/js/navigation.js?v=' . BUILDBASE_THEME_VERSION,
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }
}

BuildBase_Theme::instance();

function buildbase_css_color(string $value, string $fallback): string
{
    $candidate = trim($value);
    if (preg_match('/^#[0-9a-f]{3,8}$/i', $candidate) === 1) {
        return $candidate;
    }
    if (preg_match('/^(rgb|rgba|hsl|hsla)\(\s*[0-9.%\s,]+\)$/i', $candidate) === 1) {
        return $candidate;
    }
    if (preg_match('/^var\(--[a-z0-9_-]+\)$/i', $candidate) === 1) {
        return $candidate;
    }

    return $fallback;
}

function buildbase_css_number(mixed $value, float $default, float $min, float $max): string
{
    $number = is_numeric($value) ? (float) $value : $default;
    $number = max($min, min($max, $number));

    return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
}

function buildbase_css_choice(string $value, array $allowed, string $default): string
{
    return in_array($value, $allowed, true) ? $value : $default;
}

function buildbase_safe_url(string $url, string $fallback = ''): string
{
    if (function_exists('theme_safe_url')) {
        return theme_safe_url($url, $fallback);
    }

    $candidate = trim($url);
    if ($candidate === '') {
        return $fallback;
    }
    if (str_starts_with($candidate, '//')) {
        return $fallback;
    }

    if (str_starts_with($candidate, '/')) {
        return preg_replace('/[\x00-\x1F\x7F]+/u', '', $candidate) ?? $fallback;
    }

    $scheme = parse_url($candidate, PHP_URL_SCHEME);
    if (in_array(strtolower((string) $scheme), ['http', 'https'], true) && filter_var($candidate, FILTER_VALIDATE_URL)) {
        return $candidate;
    }

    return $fallback;
}

// ── Theme template helpers ───────────────────────────────────────────────────

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
    function theme_route_url(string $name, array $params = [], array $query = [], string $fallback = ''): string
    {
        $base = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        $path = match ($name) {
            'home'       => '/',
            'search'     => '/search',
            'login'      => '/login',
            'register'   => '/register',
            'member'     => '/member',
            'handwerker' => '/handwerker',
            'projekte'   => '/projekte',
            'angebot'    => '/angebot',
            default      => '/' . ltrim($name, '/'),
        };
        $url = $base . ($path === '/' ? '/' : $path);
        $allQuery = array_merge($params, $query);
        if ($allQuery !== []) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($allQuery, '', '&', PHP_QUERY_RFC3986);
        }

        return buildbase_safe_url($url, $fallback !== '' ? $fallback : $url);
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
        if ($items === []) {
            return;
        }

        $cur = rtrim(parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/', '/');
        echo '<ul>';
        foreach ($items as $item) {
            $rawUrl = (string) ($item['url'] ?? '#');
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(BUILDBASE_THEME_SLUG) . '/header.php';
            if (file_exists($tp)) {
                $path = $tp;
            }
        } catch (\Throwable) {
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(BUILDBASE_THEME_SLUG) . '/footer.php';
            if (file_exists($tp)) {
                $path = $tp;
            }
        } catch (\Throwable) {
        }
        if (file_exists($path)) {
            require $path;
        }
    }
}

// ── 365CMS 3.4 Laufzeit-Helfer (CSP-Nonce, lokale Schriften) ──────────────────

if (!function_exists('theme_csp_nonce_attr')) {
    /** Liefert ` nonce="…"` für Inline-<style>/<script> unter der 365CMS-CSP. */
    function theme_csp_nonce_attr(): string
    {
        try {
            $attr = class_exists('\\CMS\\Security') ? (string) \CMS\Security::instance()->nonceAttr() : '';
        } catch (\Throwable) {
            $attr = '';
        }

        return $attr !== '' ? ' ' . $attr : '';
    }
}

if (!function_exists('theme_use_local_fonts')) {
    /** true, wenn im Core „Schriften lokal einbinden“ (privacy_use_local_fonts) aktiv ist – dann keine Google-Fonts. */
    function theme_use_local_fonts(): bool
    {
        static $useLocal = null;
        if ($useLocal !== null) {
            return $useLocal;
        }

        try {
            $db  = \CMS\Database::instance();
            $row = $db->get_row(
                "SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = 'privacy_use_local_fonts' LIMIT 1"
            );
            $useLocal = $row !== null && (string) ($row->option_value ?? '0') === '1';
        } catch (\Throwable) {
            $useLocal = false;
        }

        return $useLocal;
    }
}

if (!function_exists('theme_post_url')) {
    /** Permalink eines Beitrags (Core-PermalinkService, Fallback /blog/{slug}). */
    function theme_post_url(object|array $post): string
    {
        try {
            if (class_exists('\\CMS\\Services\\PermalinkService')) {
                return \CMS\Services\PermalinkService::getInstance()->buildPostUrl($post);
            }
        } catch (\Throwable) {
        }
        $slug = is_array($post) ? (string) ($post['slug'] ?? '') : (string) ($post->slug ?? '');

        return rtrim((string) SITE_URL, '/') . '/blog/' . rawurlencode($slug);
    }
}

if (!function_exists('theme_search_result_url')) {
    /** URL eines Suchtreffers aus ThemeRouter::renderSearch() (_type + slug). */
    function theme_search_result_url(object|array $result): string
    {
        $data = is_array($result) ? $result : get_object_vars($result);
        if (($data['_type'] ?? '') === 'post') {
            return theme_post_url($data);
        }
        $slug = ltrim((string) ($data['slug'] ?? ''), '/');

        return rtrim((string) SITE_URL, '/') . '/' . implode('/', array_map('rawurlencode', explode('/', $slug)));
    }
}
