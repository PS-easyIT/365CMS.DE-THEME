<?php
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('ACADEMY365_THEME_VERSION')) {
    define('ACADEMY365_THEME_VERSION', '3.0.2');
}
if (!defined('ACADEMY365_THEME_SLUG')) {
    define('ACADEMY365_THEME_SLUG', 'academy365');
}
if (!defined('THEME_VERSION')) {
    define('THEME_VERSION', ACADEMY365_THEME_VERSION);
}

final class Academy365_Theme {
    private static ?self $instance = null;
    public static function instance(): self { if (self::$instance === null) self::$instance = new self(); return self::$instance; }
    private function __construct() { $this->registerHooks(); }

    private function registerHooks(): void {
        \CMS\Hooks::addAction('head',          [$this, 'enqueueStyles'],         3);
        \CMS\Hooks::addAction('head',          [$this, 'outputGoogleFonts'],     5);
        \CMS\Hooks::addAction('head',          [$this, 'outputCustomStyles'],   15);
        \CMS\Hooks::addAction('before_footer', [$this, 'outputNavigationScript'],99);
        \CMS\Hooks::addAction('cms_init',      [$this, 'registerNavMenus'],     10);
        \CMS\Hooks::addAction('init',          [$this, 'registerNavMenus'],     10);
    }

    public function registerNavMenus(): void {
        \CMS\ThemeManager::instance()->registerMenuLocation('primary-nav', 'Hauptmenü');
        \CMS\ThemeManager::instance()->registerMenuLocation('footer-nav',  'Fußzeilen-Navigation');
        \CMS\ThemeManager::instance()->registerMenuLocation('footer-legal','Rechtliche Links');
    }

    public function enqueueStyles(): void {
        $baseUrl   = \CMS\ThemeManager::instance()->getThemeUrl('academy365');
        $styleUrl  = htmlspecialchars($baseUrl . '/style.css?v=' . ACADEMY365_THEME_VERSION, ENT_QUOTES, 'UTF-8');
        echo '<link rel="preload" href="' . $styleUrl . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $styleUrl . '">' . "\n";
    }

    public function outputGoogleFonts(): void {
        $fonts = 'Open+Sans:wght@400;600;700&family=Raleway:wght@700;800;900&family=Plus+Jakarta+Sans:wght@400;500;700';
        echo '<link rel="preconnect" href="' . htmlspecialchars('https://fonts.googleapis.com', ENT_QUOTES, 'UTF-8') . '">' . "\n";
        echo '<link rel="preconnect" href="' . htmlspecialchars('https://fonts.gstatic.com', ENT_QUOTES, 'UTF-8') . '" crossorigin>' . "\n";
        $url = 'https://fonts.googleapis.com/css2?family=' . $fonts . '&display=swap';
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function mapFontChoice(string $fontChoice, string $fallback): string
    {
        return match ($fontChoice) {
            'open-sans'         => '"Open Sans", sans-serif',
            'inter'             => 'Inter, sans-serif',
            'lato'              => 'Lato, sans-serif',
            'nunito-sans'       => '"Nunito Sans", sans-serif',
            'raleway'           => 'Raleway, sans-serif',
            'poppins'           => 'Poppins, sans-serif',
            'nunito'            => 'Nunito, sans-serif',
            'montserrat'        => 'Montserrat, sans-serif',
            'plus-jakarta-sans' => '"Plus Jakarta Sans", sans-serif',
            default             => $fallback,
        };
    }

    public function outputCustomStyles(): void {
        try { $c = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable $e) { return; }

        $safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
        $color  = static function (string $section, string $key, string $default) use ($c): string {
            return academy365_css_color((string) $c->get($section, $key, $default), $default);
        };
        $num  = static function (string $section, string $key, string $default, float $min, float $max) use ($c): string {
            return academy365_css_number($c->get($section, $key, $default), (float) $default, $min, $max);
        };
        $choice = static function (string $section, string $key, array $allowed, string $default) use ($c): string {
            return academy365_css_choice((string) $c->get($section, $key, $default), $allowed, $default);
        };
        $bool = static function (string $section, string $key, bool $default) use ($c): bool {
            return filter_var($c->get($section, $key, $default), FILTER_VALIDATE_BOOLEAN);
        };

        // Typography
        $bodyFontChoice    = (string) $c->get('typography', 'font_family_base',    'open-sans');
        $headingFontChoice = (string) $c->get('typography', 'font_family_heading', 'raleway');
        $displayFontChoice = (string) $c->get('typography', 'font_family_display', 'plus-jakarta-sans');
        $bodyFont    = $safe($this->mapFontChoice($bodyFontChoice,    '"Open Sans", sans-serif'));
        $headingFont = $safe($this->mapFontChoice($headingFontChoice, 'Raleway, sans-serif'));
        $displayFont = $safe($this->mapFontChoice($displayFontChoice, '"Plus Jakarta Sans", sans-serif'));

        // Layout switches
        $stickyHeader  = $bool('layout', 'enable_sticky_header',  true);
        $headerShadow  = $bool('header', 'show_header_shadow',    true);
        $courseColumns = (int) $num('layout', 'course_grid_columns', '3', 2, 4);
        if ($courseColumns < 2) { $courseColumns = 2; }
        if ($courseColumns > 4) { $courseColumns = 4; }
        // Card minimum width derived from column setting (visual fallback for narrow viewports)
        $cardMin = match ($courseColumns) { 2 => 320, 4 => 240, default => 280 };

        echo '<style id="ac-custom-vars">:root{';
        // Colors
        echo '--primary-color:'    . $color('colors', 'primary_color',   '#7c3aed') . ';';
        echo '--primary-dark:'     . $color('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--secondary-color:'  . $color('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--accent-color:'     . $color('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--bg-primary:'       . $color('colors', 'bg_color',        '#faf5ff') . ';';
        echo '--bg-secondary:'     . $color('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--bg-card:'          . $color('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--text-primary:'     . $color('colors', 'text_color',      '#1e1b4b') . ';';
        echo '--link-color:'       . $color('colors', 'link_color',      '#7c3aed') . ';';
        echo '--muted-color:'      . $color('colors', 'muted_color',     '#6b7280') . ';';
        echo '--border-color:'     . $color('colors', 'border_color',    '#e9d5ff') . ';';
        echo '--badge-free:'       . $color('colors', 'success_color',   '#10b981') . ';';
        echo '--badge-cert:'       . $color('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--badge-new:'        . $color('colors', 'progress_color',  '#3b82f6') . ';';
        echo '--badge-best:'       . $color('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--progress-color:'   . $color('colors', 'progress_color',  '#3b82f6') . ';';
        echo '--success-color:'    . $color('colors', 'success_color',   '#10b981') . ';';
        echo '--cat-tech:'         . $color('colors', 'category_tech',     '#0ea5e9') . ';';
        echo '--cat-business:'     . $color('colors', 'category_business', '#059669') . ';';
        echo '--cat-design:'       . $color('colors', 'category_design',   '#ec4899') . ';';
        echo '--cat-language:'     . $color('colors', 'category_language', '#f59e0b') . ';';
        // Header & Footer chrome
        echo '--header-bg:'        . $color('header', 'header_bg_color',     '#ffffff') . ';';
        echo '--header-text:'      . $color('header', 'header_text_color',   '#1e1b4b') . ';';
        echo '--header-height:'    . $num('header', 'header_height',       '72', 48, 120)      . 'px;';
        echo '--logo-max-height:'  . $num('header', 'logo_max_height',     '44', 24, 100)      . 'px;';
        echo '--footer-bg:'        . $color('footer', 'footer_bg_color',     '#1e1b4b') . ';';
        echo '--footer-text:'      . $color('footer', 'footer_text_color',   '#c4b5fd') . ';';
        echo '--footer-link:'      . $color('footer', 'footer_link_color',   '#ede9fe') . ';';
        // Typography
        echo '--font-body:'        . $bodyFont    . ';';
        echo '--font-heading:'     . $headingFont . ';';
        echo '--font-ui:'          . $displayFont . ';';
        echo '--font-size-base:'   . $num('typography', 'font_size_base',   '16', 12, 24)   . 'px;';
        echo '--line-height-base:' . $num('typography', 'line_height_base', '1.7', 1.2, 2.5) . ';';
        echo '--font-weight-heading:' . $choice('typography', 'font_weight_heading', ['600', '700', '800'], '700') . ';';
        // Layout
        echo '--container-max:'    . $num('layout', 'container_width',  '1240', 800, 1920) . 'px;';
        echo '--content-padding:'  . $num('layout', 'content_padding',  '2', 0.5, 6)    . 'rem;';
        echo '--radius-md:'        . $num('layout', 'border_radius',    '12', 0, 32)   . 'px;';
        echo '--section-spacing:'  . $num('layout', 'section_spacing',  '4.5', 1, 12)  . 'rem;';
        echo '--course-grid-min:'  . $cardMin . 'px;';
        // Buttons
        echo '--btn-radius:'       . $num('buttons', 'button_border_radius', '8', 0, 50)   . 'px;';
        echo '--btn-padding-x:'    . $num('buttons', 'button_padding_x',    '1.75', 0.5, 4) . 'rem;';
        echo '--btn-padding-y:'    . $num('buttons', 'button_padding_y',    '0.75', 0.25, 2) . 'rem;';
        echo '--btn-weight:'       . $choice('buttons', 'button_font_weight', ['600', '700'], '700') . ';';
        echo '--btn-transform:'    . $choice('buttons', 'button_transform', ['none', 'capitalize'], 'none') . ';';
        // Tokens used by focus rings & sticky header
        echo '--focus-ring:'       . $color('colors', 'accent_color', '#f59e0b') . ';';
        echo '--header-shadow:'    . ($headerShadow ? '0 1px 4px rgba(124,58,237,.08)' : 'none') . ';';
        echo '--header-position:'  . ($stickyHeader ? 'fixed' : 'absolute') . ';';
        echo '}';

        $custom = academy365_sanitize_custom_css((string) $c->get('advanced', 'custom_css', ''));
        if (trim($custom) !== '') {
            echo $custom;
        }
        echo '</style>' . "\n";
    }

    public function outputNavigationScript(): void {
        $baseUrl = \CMS\ThemeManager::instance()->getThemeUrl('academy365');
        $jsUrl   = htmlspecialchars($baseUrl . '/js/navigation.js?v=' . ACADEMY365_THEME_VERSION, ENT_QUOTES, 'UTF-8');
        echo '<script src="' . $jsUrl . '" defer></script>' . "\n";
    }
}
Academy365_Theme::instance();

function academy365_css_color(string $value, string $fallback): string
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

function academy365_css_number(mixed $value, float $default, float $min, float $max): string
{
    $number = is_numeric($value) ? (float) $value : $default;
    $number = max($min, min($max, $number));

    return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
}

function academy365_css_choice(string $value, array $allowed, string $default): string
{
    return in_array($value, $allowed, true) ? $value : $default;
}

function academy365_safe_url(string $url, string $fallback = ''): string
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

function academy365_sanitize_custom_css(string $css): string
{
    $clean = preg_replace('/<\/?style\b[^>]*>/i', '', $css) ?? '';
    $clean = preg_replace('/@import\b[^;]*;?/i', '', $clean) ?? '';
    $clean = preg_replace('/expression\s*\([^)]*\)/i', '', $clean) ?? '';
    $protocolSinkPattern = '/url\s*\(\s*[\'\"]?\s*java' . 'script:[^)]+\)/i';
    $clean = preg_replace($protocolSinkPattern, '', $clean) ?? '';
    $clean = preg_replace('/\b(?:behavior|binding)\s*:[^;]+;?/i', '', $clean) ?? '';

    return str_replace('</', '<\/', $clean);
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
    /**
     * @param array<string, scalar> $params  Query parameters (merged with $query)
     * @param array<string, scalar> $query
     */
    function theme_route_url(string $name, array $params = [], array $query = [], string $fallback = ''): string
    {
        $base = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        $path = match ($name) {
            'home'             => '/',
            'search'           => '/search',
            'login'            => '/login',
            'register'         => '/register',
            'member',
            'member-dashboard' => '/member',
            'courses'          => '/courses',
            'tutors'           => '/tutors',
            'tutor-register'   => '/tutor-register',
            'pro'              => '/pro',
            default            => '/' . ltrim($name, '/'),
        };
        $url = $base . ($path === '/' ? '/' : $path);
        $allQuery = array_merge($params, $query);
        if ($allQuery !== []) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($allQuery, '', '&', PHP_QUERY_RFC3986);
        }

        return academy365_safe_url($url, $fallback !== '' ? $fallback : $url);
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(ACADEMY365_THEME_SLUG) . '/header.php';
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(ACADEMY365_THEME_SLUG) . '/footer.php';
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
