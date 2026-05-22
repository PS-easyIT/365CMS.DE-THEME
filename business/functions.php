<?php
declare(strict_types=1);

/**
 * Business Presentation Theme – functions.php
 *
 * Editorial B2B / Corporate Landing Theme for 365CMS v3.x.x on PHP 8.4.
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('BUSINESS_THEME_VERSION')) {
    define('BUSINESS_THEME_VERSION', '3.0.2');
}

/**
 * Business Theme Singleton
 */
final class IT_Business_Theme
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
        \CMS\Hooks::addAction('head',          [$this, 'enqueueStyles'],      1);
        \CMS\Hooks::addAction('head',          [$this, 'outputGoogleFonts'],  5);
        \CMS\Hooks::addAction('head',          [$this, 'outputMetaTags'],     10);
        \CMS\Hooks::addAction('head',          [$this, 'outputCustomStyles'], 15);

        // Deferred script before </body>
        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'],     99);

        // Menü-Positionen via Filter registrieren
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Menüs registrieren – auf BEIDEN Hooks für v3-Bootstrap-Kompatibilität
        \CMS\Hooks::addAction('init',     [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'registerNavMenus'], 10);

        // Standard-Menüs beim ersten Start anlegen
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus'], 20);
    }

    // ── Assets ───────────────────────────────────────────────────────────────

    public function enqueueStyles(): void
    {
        $themeUrl = $this->themeBaseUrl();
        $href     = htmlspecialchars(
            $themeUrl . '/style.css?v=' . rawurlencode(BUSINESS_THEME_VERSION),
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
            $themeUrl . '/js/navigation.js?v=' . rawurlencode(BUSINESS_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }

    /**
     * Optional Google Fonts – nur wenn Customizer das nicht-Default-Family wählt.
     * Bei "system" (Standard) wird nichts geladen, der OS-Stack reicht.
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
        $siteDesc = htmlspecialchars($tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl  = htmlspecialchars(biz_safe_url((string) SITE_URL, '/'), ENT_QUOTES, 'UTF-8');

        $themeColor = biz_css_color((string) $this->customizerGet('colors', 'ink_color', '#0c1320'), '#0c1320');

        echo '<meta name="description" content="' . $siteDesc . '">' . "\n";
        echo '<meta property="og:url" content="' . $siteUrl . '">' . "\n";
        echo '<meta name="theme-color" content="' . $themeColor . '">' . "\n";
        echo '<meta name="robots" content="index,follow">' . "\n";
    }

    // ── Customizer-getriebene CSS-Variablen + custom_css ─────────────────────

    public function outputCustomStyles(): void
    {
        $color = fn(string $g, string $k, string $d): string =>
            biz_css_color((string) $this->customizerGet($g, $k, $d), $d);
        $num = fn(string $g, string $k, string $d, float $min, float $max): string =>
            biz_css_number($this->customizerGet($g, $k, $d), (float) $d, $min, $max);

        $baseFontChoice    = (string) $this->customizerGet('typography', 'font_family_base',    'system');
        $headingFontChoice = (string) $this->customizerGet('typography', 'font_family_heading', 'system');

        $baseFont    = htmlspecialchars(
            $this->mapFontChoice($baseFontChoice, 'system'),
            ENT_QUOTES,
            'UTF-8'
        );
        $headingFont = htmlspecialchars(
            $this->mapFontChoice($headingFontChoice, 'system'),
            ENT_QUOTES,
            'UTF-8'
        );

        echo '<style id="biz-custom-vars">:root{';

        // Colors
        echo '--biz-accent:'         . $color('colors', 'primary_color', '#c08a2e') . ';';
        echo '--biz-accent-hover:'   . $color('colors', 'primary_hover', '#a16f1e') . ';';
        echo '--biz-ink:'            . $color('colors', 'ink_color',     '#0c1320') . ';';
        echo '--biz-ink-soft:'       . $color('colors', 'ink_soft',      '#18243a') . ';';
        echo '--biz-paper:'          . $color('colors', 'paper_color',   '#f7f5f0') . ';';
        echo '--biz-text:'           . $color('colors', 'text_color',    '#1a2030') . ';';
        echo '--biz-text-mute:'      . $color('colors', 'muted_color',   '#5a6479') . ';';
        echo '--biz-rule:'           . $color('colors', 'border_color',  '#dcd9d1') . ';';
        echo '--focus-ring:'         . $color('colors', 'primary_color', '#c08a2e') . ';';

        // Typography
        echo '--biz-font-body:'      . $baseFont    . ';';
        echo '--biz-font-heading:'   . $headingFont . ';';
        echo '--biz-font-size-base:' . $num('typography', 'font_size_base',   '16', 14, 20)   . 'px;';
        echo '--biz-line-height:'    . $num('typography', 'line_height_base', '1.65', 1.3, 2.2) . ';';

        // Layout
        echo '--biz-max-width:'      . $num('layout', 'container_width', '1200', 960, 1440) . 'px;';
        echo '--biz-section-pad:'    . $num('layout', 'section_spacing', '5', 2, 9)    . 'rem;';
        echo '--biz-radius:'         . $num('layout', 'border_radius',   '6', 0, 16)    . 'px;';

        echo '}';

        // Sticky header opt-out
        $stickyHeader = filter_var(
            $this->customizerGet('header', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.biz-header{position:static;}';
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
        $locations[] = ['slug' => 'primary',      'label' => 'Hauptnavigation (Header)'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Footer-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Footer Rechtliche Links'];
        return $locations;
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (method_exists($tm, 'registerMenuLocation')) {
            $tm->registerMenuLocation('primary',      'Hauptnavigation (Header)');
            $tm->registerMenuLocation('footer-nav',   'Footer-Navigation');
            $tm->registerMenuLocation('footer-legal', 'Footer Rechtliche Links');
        }
    }

    /**
     * Standard-Menüeinträge beim ersten Start anlegen (nur wenn leer)
     */
    public function seedDefaultMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();

        $defaults = [
            'primary' => [
                ['label' => 'Start',       'url' => '/',            'target' => '_self'],
                ['label' => 'Über uns',    'url' => '/#ueber-uns',  'target' => '_self'],
                ['label' => 'Leistungen',  'url' => '/#leistungen', 'target' => '_self'],
                ['label' => 'Kontakt',     'url' => '/#kontakt',    'target' => '_self'],
            ],
            'footer-nav' => [
                ['label' => 'Startseite',  'url' => '/',            'target' => '_self'],
                ['label' => 'Über uns',    'url' => '/#ueber-uns',  'target' => '_self'],
                ['label' => 'Leistungen',  'url' => '/#leistungen', 'target' => '_self'],
                ['label' => 'Kontakt',     'url' => '/#kontakt',    'target' => '_self'],
            ],
            'footer-legal' => [
                ['label' => 'Impressum',   'url' => '/impressum',   'target' => '_self'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz', 'target' => '_self'],
                ['label' => 'AGB',         'url' => '/agb',         'target' => '_self'],
            ],
        ];

        foreach ($defaults as $location => $items) {
            if (empty($tm->getMenu($location))) {
                $tm->saveMenu($location, $items);
            }
        }
    }

    /**
     * Theme-Konfiguration aus theme.json (settings-Block) lesen
     */
    public function getConfig(string $key, mixed $default = ''): mixed
    {
        static $config = null;

        if ($config === null) {
            $jsonFile = THEME_PATH . 'business/theme.json';
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
        try {
            $url = (string) \CMS\ThemeManager::instance()->getThemeUrl('business');
        } catch (\Throwable) {
            $url = (string) \CMS\ThemeManager::instance()->getThemeUrl();
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

    private function mapFontChoice(string $choice, string $fallback): string
    {
        $systemStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        return match ($choice) {
            'system'       => $systemStack,
            'inter'        => 'Inter, ' . $systemStack,
            'source-sans'  => '"Source Sans 3", ' . $systemStack,
            'source-serif' => '"Source Serif 4", Georgia, "Times New Roman", serif',
            default        => $fallback === 'system' ? $systemStack : $fallback,
        };
    }

    private function googleFontFamilies(): string
    {
        $base    = (string) $this->customizerGet('typography', 'font_family_base',    'system');
        $heading = (string) $this->customizerGet('typography', 'font_family_heading', 'system');

        $needed = [];
        foreach ([$base, $heading] as $choice) {
            switch ($choice) {
                case 'inter':
                    $needed['Inter']             = 'Inter:wght@400;500;700';
                    break;
                case 'source-sans':
                    $needed['Source Sans 3']     = 'Source+Sans+3:wght@400;600;700';
                    break;
                case 'source-serif':
                    $needed['Source Serif 4']    = 'Source+Serif+4:wght@500;700';
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

        $clean = preg_replace('#<\/?\s*style\b[^>]*>#i', '', $trimmed) ?? '';
        $clean = preg_replace('/@import\b[^;]*;?/i', '', $clean) ?? '';
        $clean = preg_replace('/expression\s*\([^)]*\)/i', '', $clean) ?? '';
        $protocolSinkPattern = '/url\s*\(\s*[\'\"]?\s*java' . 'script:[^)]+\)/i';
        $clean = preg_replace($protocolSinkPattern, '', $clean) ?? '';
        $clean = preg_replace('/\b(?:behavior|binding)\s*:[^;]+;?/i', '', $clean) ?? '';

        return $clean;
    }
}

IT_Business_Theme::instance();

// ── Global Helper ──────────────────────────────────────────────────────────────

if (!function_exists('biz_css_color')) {
    function biz_css_color(string $value, string $fallback): string
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
}

if (!function_exists('biz_css_number')) {
    function biz_css_number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;
        $number = max($min, min($max, $number));

        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}

if (!function_exists('biz_safe_url')) {
    function biz_safe_url(string $url, string $fallback = ''): string
    {
        if (function_exists('theme_safe_url')) {
            return theme_safe_url($url, $fallback);
        }

        $candidate = trim($url);
        if ($candidate === '' || str_starts_with($candidate, '//')) {
            return $fallback;
        }
        if (str_starts_with($candidate, '/')) {
            return preg_replace('/[\x00-\x1F\x7F]+/u', '', $candidate) ?? $fallback;
        }
        if (str_starts_with($candidate, '#')) {
            return preg_match('/^#[A-Za-z][A-Za-z0-9_-]*$/', $candidate) === 1 ? $candidate : $fallback;
        }
        if (preg_match('/^mailto:[^\s@]+@[^\s@]+\.[^\s@]+$/i', $candidate) === 1) {
            return $candidate;
        }
        if (preg_match('/^tel:\+?[0-9][0-9\s().-]{2,31}$/i', $candidate) === 1) {
            return $candidate;
        }

        $scheme = parse_url($candidate, PHP_URL_SCHEME);
        if (in_array(strtolower((string) $scheme), ['http', 'https'], true) && filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $candidate;
        }

        return $fallback;
    }
}

if (!function_exists('biz_sanitize_content_html')) {
    function biz_sanitize_content_html(string $html): string
    {
        $allowedTags = '<p><br><strong><b><em><i><u><s>'
            . '<h1><h2><h3><h4><h5><h6>'
            . '<ul><ol><li><dl><dt><dd>'
            . '<a><img>'
            . '<blockquote><pre><code>'
            . '<table><thead><tbody><tr><th><td>'
            . '<div><span><section><article><aside>'
            . '<hr><figure><figcaption>';

        $clean = strip_tags($html, $allowedTags);
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? '';
        $clean = preg_replace('/\s+style\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean) ?? '';
        $clean = preg_replace_callback('/\s+(href|src)\s*=\s*(["\'])(.*?)\2/i', static function (array $match): string {
            $attr = strtolower((string) $match[1]);
            $value = html_entity_decode((string) $match[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $fallback = $attr === 'href' ? '#' : '';
            $safe = biz_safe_url($value, $fallback);

            if ($safe === '') {
                return '';
            }

            return ' ' . $attr . '="' . htmlspecialchars($safe, ENT_QUOTES, 'UTF-8') . '"';
        }, $clean) ?? '';

        return str_replace('</', '<\/', $clean);
    }
}

if (!function_exists('biz_nav_menu')) {
    function biz_nav_menu(string $location = 'primary'): void
    {
        $tm    = \CMS\ThemeManager::instance();
        $items = $tm->getMenu($location);

        if (empty($items)) {
            return;
        }

        $requestUri = rtrim($_SERVER['REQUEST_URI'] ?? '/', '/');
        echo '<ul>' . "\n";
        foreach ($items as $item) {
            $rawUrl = (string) ($item['url'] ?? '#');
            $path   = rtrim(parse_url($rawUrl, PHP_URL_PATH) ?? '/', '/');
            $isActive = ($path === '' || $path === '/')
                ? ($requestUri === '' || $requestUri === '/')
                : str_starts_with($requestUri, $path);
            $class   = $isActive ? ' class="active"' : '';
            $target  = !empty($item['target']) && $item['target'] === '_blank'
                ? ' target="_blank" rel="noopener noreferrer"'
                : '';
            $sanitizedUrl = biz_safe_url($rawUrl, '#');
            $url   = htmlspecialchars($sanitizedUrl,           ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            echo '<li' . $class . '><a href="' . $url . '"' . $target . '>' . $label . '</a></li>' . "\n";
        }
        echo '</ul>' . "\n";
    }
}

if (!function_exists('biz_href')) {
    function biz_href(string $target): string
    {
        $trimmed = trim($target);
        if ($trimmed === '') {
            return biz_site_url() . '/';
        }
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return biz_safe_url($trimmed, biz_site_url() . '/');
        }
        if (str_starts_with($trimmed, 'mailto:') || str_starts_with($trimmed, 'tel:')) {
            return biz_safe_url($trimmed, biz_site_url() . '/');
        }
        if (str_starts_with($trimmed, '#')) {
            $anchor = biz_safe_url($trimmed, '');
            return $anchor !== '' ? biz_site_url() . '/' . $anchor : biz_site_url() . '/';
        }
        return biz_safe_url(biz_site_url() . '/' . ltrim($trimmed, '/'), biz_site_url() . '/');
    }
}

if (!function_exists('biz_safe_headline')) {
    function biz_safe_headline(string $headline): string
    {
        $escaped = htmlspecialchars($headline, ENT_QUOTES, 'UTF-8');
        $escaped = str_replace(
            ['&lt;span class=&quot;highlight&quot;&gt;', '&lt;/span&gt;'],
            ['<span class="highlight">', '</span>'],
            $escaped
        );
        return $escaped;
    }
}

if (!function_exists('biz_config')) {
    /**
     * Liest Theme-Inhalte – zuerst aus dem Customizer (Gruppe `biz_content`
     * bzw. `biz_hero`), fällt sonst auf theme.json `settings` zurück.
     */
    function biz_config(string $key, mixed $default = ''): mixed
    {
        static $heroKeys = [
            'hero_badge'                 => true,
            'hero_headline'              => true,
            'hero_text'                  => true,
            'hero_cta_primary_label'     => true,
            'hero_cta_primary_url'       => true,
            'hero_cta_secondary_label'   => true,
            'hero_cta_secondary_url'     => true,
        ];
        $group = isset($heroKeys[$key]) ? 'biz_hero' : 'biz_content';

        try {
            $val = \CMS\Services\ThemeCustomizer::instance()->get($group, $key, null);
            if (is_string($val)) {
                if ($val !== '') {
                    return $val;
                }
            } elseif ($val !== null) {
                return $val;
            }
        } catch (\Throwable) {
            // Customizer nicht verfügbar – fällt unten auf theme.json zurück.
        }

        return IT_Business_Theme::instance()->getConfig($key, $default);
    }
}

if (!function_exists('biz_site_url')) {
    function biz_site_url(): string
    {
        $siteUrl = rtrim(biz_safe_url((string) SITE_URL, '/'), '/');

        return $siteUrl !== '' ? $siteUrl : '/';
    }
}

if (!function_exists('biz_site_title')) {
    function biz_site_title(): string
    {
        return htmlspecialchars(\CMS\ThemeManager::instance()->getSiteTitle(), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('biz_body_class')) {
    /**
     * Gibt den serverseitig komponierten <body>-Class-String zurück
     * (statt inline JS, das später die Klasse setzt).
     */
    function biz_body_class(string ...$extra): string
    {
        $classes = ['biz-body'];

        try {
            $sticky = filter_var(
                \CMS\Services\ThemeCustomizer::instance()->get('header', 'enable_sticky_header', true),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (\Throwable) {
            $sticky = true;
        }
        if ($sticky) {
            $classes[] = 'has-sticky-header';
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
