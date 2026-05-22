<?php
declare(strict_types=1);

/**
 * PTC Theme – Personalvermittlung & Schulungen (365CMS v3.x.x, PHP 8.4)
 *
 * @package PTC_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('PTC_THEME_VERSION')) {
    define('PTC_THEME_VERSION', '3.0.0');
}

if (!defined('PTC_THEME_SLUG')) {
    define('PTC_THEME_SLUG', 'PTC');
}

/**
 * Theme bootstrap (singleton).
 */
final class PTC_Theme
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
        \CMS\Hooks::addAction('body_end', [$this, 'outputCustomFooterCode'], 99);

        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        \CMS\Hooks::addAction('init',     [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus'], 20);
    }

    public function enqueueStyles(): void
    {
        $href = htmlspecialchars(
            $this->themeBaseUrl() . '/style.css?v=' . rawurlencode(PTC_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = htmlspecialchars(
            $this->themeBaseUrl() . '/js/navigation.js?v=' . rawurlencode(PTC_THEME_VERSION),
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
        $tm         = \CMS\ThemeManager::instance();
        $siteDesc   = htmlspecialchars((string) $tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl    = htmlspecialchars(ptc_safe_url((string) (defined('SITE_URL') ? SITE_URL : '/'), '/'), ENT_QUOTES, 'UTF-8');
        $themeColor = htmlspecialchars(
            ptc_css_color((string) $this->customizerGet('colors', 'primary_color', '#1a4d5c'), '#1a4d5c'),
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
            htmlspecialchars(
                ptc_css_color((string) $this->customizerGet($g, $k, $d), $d),
                ENT_QUOTES,
                'UTF-8'
            );
        $num = fn(string $g, string $k, string $d, float $min, float $max): string =>
            ptc_css_number($this->customizerGet($g, $k, $d), (float) $d, $min, $max);

        $baseChoice    = (string) $this->customizerGet('typography', 'font_family_base',    'system');
        $headingChoice = (string) $this->customizerGet('typography', 'font_family_heading', 'system');
        $baseFont      = htmlspecialchars($this->mapFontChoice($baseChoice, 'system'), ENT_QUOTES, 'UTF-8');
        $headingFont   = htmlspecialchars($this->mapFontChoice($headingChoice, 'system'), ENT_QUOTES, 'UTF-8');

        echo '<style id="ptc-custom-vars">:root{';

        echo '--color-primary:'         . $p('colors', 'primary_color', '#1a4d5c') . ';';
        echo '--color-primary-hover:'   . $p('colors', 'primary_hover', '#123a47') . ';';
        echo '--color-primary-light:'   . $p('colors', 'primary_light', '#e8f2f4') . ';';
        echo '--color-accent:'          . $p('colors', 'accent_color', '#2d7a5f') . ';';
        echo '--color-accent-hover:'    . $p('colors', 'accent_hover', '#236349') . ';';
        echo '--color-accent-light:'    . $p('colors', 'accent_light', '#e6f4ef') . ';';
        echo '--color-secondary:'       . $p('colors', 'secondary_color', '#5c6f7a') . ';';
        echo '--color-text:'            . $p('colors', 'text_color', '#24303a') . ';';
        echo '--color-heading:'         . $p('colors', 'heading_color', '#1a4d5c') . ';';
        echo '--color-on-dark:'         . $p('colors', 'text_light', '#f4f7f8') . ';';
        echo '--color-muted:'           . $p('colors', 'muted_color', '#6b7c88') . ';';
        echo '--color-bg:'              . $p('colors', 'bg_color', '#f6f8f9') . ';';
        echo '--color-bg-alt:'          . $p('colors', 'bg_secondary', '#eef2f4') . ';';
        echo '--color-link:'            . $p('colors', 'link_color', '#1a4d5c') . ';';
        echo '--color-link-hover:'      . $p('colors', 'link_hover_color', '#2d7a5f') . ';';
        echo '--color-border:'          . $p('colors', 'border_color', '#d8e0e5') . ';';
        echo '--color-success:'         . $p('colors', 'success_color', '#2e7d4f') . ';';
        echo '--color-error:'           . $p('colors', 'error_color', '#b42318') . ';';

        echo '--color-cta-bg:'           . $p('colors', 'cta_bg_color', '#1a4d5c') . ';';
        echo '--color-cta-bg-end:'       . $p('colors', 'cta_bg_to', '#123a47') . ';';
        echo '--color-cta-text:'         . $p('colors', 'cta_text_color', '#ffffff') . ';';
        echo '--color-side-margin:'      . $p('colors', 'side_margin_color', '#edf1f3') . ';';
        echo '--color-side-accent:'      . $p('colors', 'side_accent_color', '#1a4d5c') . ';';
        echo '--color-side-width:'       . $num('colors', 'side_accent_width', '0', 0, 12) . 'px;';

        echo '--color-hero-bg:'          . $p('colors', 'hero_bg_color', '#f6f8f9') . ';';
        echo '--color-hero-bg-end:'      . $p('colors', 'hero_bg_to', '#eef2f4') . ';';
        echo '--color-services-bg:'      . $p('colors', 'services_bg_color', '#ffffff') . ';';
        echo '--color-service-card-bg:'  . $p('colors', 'services_card_bg', '#ffffff') . ';';
        echo '--color-events-bg:'        . $p('colors', 'events_bg_color', '#eef2f4') . ';';
        echo '--color-event-card-bg:'    . $p('colors', 'events_card_bg', '#ffffff') . ';';
        echo '--color-faq-bg:'           . $p('colors', 'faq_bg_color', '#ffffff') . ';';
        echo '--color-faq-item-bg:'      . $p('colors', 'faq_item_bg', '#ffffff') . ';';
        echo '--color-network-bar-bg:'   . $p('colors', 'network_bar_bg', '#0f2d38') . ';';
        echo '--color-network-bar-text:' . $p('colors', 'network_bar_text', 'rgba(255,255,255,0.45)') . ';';

        echo '--color-blog-hero-bg:'     . $p('colors', 'blog_hero_bg', '#1a4d5c') . ';';
        echo '--color-blog-hero-text:'   . $p('colors', 'blog_hero_text', '#ffffff') . ';';
        echo '--color-blog-content-bg:'  . $p('colors', 'blog_content_bg', '#f6f8f9') . ';';
        echo '--color-blog-card-bg:'     . $p('colors', 'blog_card_bg', '#ffffff') . ';';
        echo '--color-blog-card-title:'  . $p('colors', 'blog_card_title', '#1a4d5c') . ';';
        echo '--color-blog-cat-bg:'      . $p('colors', 'blog_category_bg', '#e6f4ef') . ';';
        echo '--color-blog-cat-text:'    . $p('colors', 'blog_category_text', '#236349') . ';';

        echo '--font-body:'              . $baseFont . ';';
        echo '--font-heading:'           . $headingFont . ';';
        echo '--font-size-base:'          . $num('typography', 'font_size_base', '16', 12, 24) . 'px;';
        echo '--line-height-base:'        . $num('typography', 'line_height_base', '1.6', 1.2, 2.5) . ';';
        echo '--font-weight-heading:'     . htmlspecialchars((string) $this->customizerGet('typography', 'font_weight_heading', '700'), ENT_QUOTES, 'UTF-8') . ';';

        echo '--layout-container:'        . $num('layout', 'container_width', '1280', 800, 1920) . 'px;';
        echo '--layout-content-pad:'      . $num('layout', 'content_padding', '2', 0.5, 6) . 'rem;';
        echo '--layout-radius:'           . $num('layout', 'border_radius', '10', 0, 32) . 'px;';
        echo '--layout-section-gap:'      . $num('layout', 'section_spacing', '5', 1, 10) . 'rem;';

        echo '--header-bg:'               . $p('header', 'header_bg_color', '#1a4d5c') . ';';
        echo '--header-text:'             . $p('header', 'header_text_color', '#f4f7f8') . ';';
        echo '--header-accent:'           . $p('header', 'header_accent_color', '#2d7a5f') . ';';
        echo '--header-height:'           . $num('header', 'header_height', '72', 48, 120) . 'px;';
        echo '--logo-max-height:'         . $num('header', 'logo_max_height', '48', 24, 100) . 'px;';
        echo '--nav-font-size:'           . $num('header', 'nav_font_size', '14', 11, 20) . 'px;';
        echo '--nav-sub-font-size:'       . $num('header', 'nav_sub_font_size', '13', 10, 18) . 'px;';

        echo '--footer-bg:'               . $p('footer', 'footer_bg_color', '#0f2d38') . ';';
        echo '--footer-text:'             . $p('footer', 'footer_text_color', '#94a8b3') . ';';
        echo '--footer-link:'             . $p('footer', 'footer_link_color', '#f4f7f8') . ';';

        echo '--btn-radius:'              . $num('buttons', 'button_border_radius', '8', 0, 50) . 'px;';
        echo '--btn-padding-x:'           . $num('buttons', 'button_padding_x', '2', 0.5, 4) . 'rem;';
        echo '--btn-padding-y:'           . $num('buttons', 'button_padding_y', '0.875', 0.25, 2) . 'rem;';
        echo '--btn-font-weight:'         . htmlspecialchars((string) $this->customizerGet('buttons', 'button_font_weight', '600'), ENT_QUOTES, 'UTF-8') . ';';
        echo '--btn-text-transform:'      . htmlspecialchars((string) $this->customizerGet('buttons', 'button_transform', 'none'), ENT_QUOTES, 'UTF-8') . ';';

        echo '--focus-ring:'              . $p('colors', 'accent_color', '#2d7a5f') . ';';

        echo '}';

        $stickyHeader = filter_var(
            $this->customizerGet('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.ptc-header{position:static;}.ptc-content{padding-top:0;}';
        }

        $custom = $this->sanitizeCustomCss((string) $this->customizerGet('advanced', 'custom_css', ''));
        if ($custom !== '') {
            echo $custom;
        }

        echo '</style>' . "\n";

        $headCode = trim((string) $this->customizerGet('advanced', 'custom_head_code', ''));
        if ($headCode !== '') {
            echo $headCode . "\n";
        }
    }

    public function outputCustomFooterCode(): void
    {
        $footerCode = trim((string) $this->customizerGet('advanced', 'custom_footer_code', ''));
        if ($footerCode !== '') {
            echo $footerCode . "\n";
        }
    }

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary',         'label' => 'Hauptnavigation (Header)'];
        $locations[] = ['slug' => 'footer-nav',      'label' => 'Footer-Navigation'];
        $locations[] = ['slug' => 'footer-services', 'label' => 'Footer Leistungen'];
        $locations[] = ['slug' => 'footer-legal',    'label' => 'Footer Rechtliche Links'];
        return $locations;
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (method_exists($tm, 'registerMenuLocation')) {
            $tm->registerMenuLocation('primary',         'Hauptnavigation (Header)');
            $tm->registerMenuLocation('footer-nav',      'Footer-Navigation');
            $tm->registerMenuLocation('footer-services', 'Footer Leistungen');
            $tm->registerMenuLocation('footer-legal',    'Footer Rechtliche Links');
        }
    }

    public function seedDefaultMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();

        $defaults = [
            'primary' => [
                ['label' => 'Start',              'url' => '/',                  'target' => '_self'],
                ['label' => 'Für Kandidaten',     'url' => '/#kandidaten',       'target' => '_self'],
                ['label' => 'Für Arbeitgeber',  'url' => '/#arbeitgeber',      'target' => '_self'],
                ['label' => 'Weiterbildung',     'url' => '/#dienstleistungen', 'target' => '_self'],
                ['label' => 'Termine',            'url' => '/#termine',          'target' => '_self'],
                ['label' => 'Kontakt',            'url' => '/#kontakt',          'target' => '_self'],
            ],
            'footer-nav' => [
                ['label' => 'Startseite',         'url' => '/',                  'target' => '_self'],
                ['label' => 'Leistungen',         'url' => '/#dienstleistungen', 'target' => '_self'],
                ['label' => 'FAQ',                'url' => '/#faq',              'target' => '_self'],
                ['label' => 'Kontakt',            'url' => '/#kontakt',          'target' => '_self'],
            ],
            'footer-services' => [
                ['label' => 'Personalvermittlung',     'url' => '/#dienstleistungen', 'target' => '_self'],
                ['label' => 'Arbeitnehmerüberlassung', 'url' => '/#dienstleistungen', 'target' => '_self'],
                ['label' => 'Weiterbildung',            'url' => '/#dienstleistungen', 'target' => '_self'],
                ['label' => 'Qualifizierung',            'url' => '/#dienstleistungen', 'target' => '_self'],
            ],
            'footer-legal' => [
                ['label' => 'Impressum',   'url' => '/impressum',   'target' => '_self'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz', 'target' => '_self'],
            ],
        ];

        foreach ($defaults as $location => $items) {
            if (empty($tm->getMenu($location))) {
                $tm->saveMenu($location, $items);
            }
        }
    }

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

    private function themeBaseUrl(): string
    {
        $tm = \CMS\ThemeManager::instance();
        try {
            $url = (string) $tm->getThemeUrl(PTC_THEME_SLUG);
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

    private function mapFontChoice(string $choice, string $fallback): string
    {
        $systemStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        $serifStack  = 'Georgia, "Times New Roman", serif';

        return match ($choice) {
            'system'     => $systemStack,
            'inter'      => 'Inter, ' . $systemStack,
            'roboto'     => 'Roboto, ' . $systemStack,
            'open-sans'  => '"Open Sans", ' . $systemStack,
            'lato'       => 'Lato, ' . $systemStack,
            'montserrat' => 'Montserrat, ' . $systemStack,
            'poppins'    => 'Poppins, ' . $systemStack,
            'raleway'    => 'Raleway, ' . $systemStack,
            'georgia'    => $serifStack,
            default      => $fallback === 'system' ? $systemStack : $fallback,
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
                    $needed['Inter'] = 'Inter:wght@400;500;600;700';
                    break;
                case 'roboto':
                    $needed['Roboto'] = 'Roboto:wght@400;500;700';
                    break;
                case 'open-sans':
                    $needed['Open Sans'] = 'Open+Sans:wght@400;600;700';
                    break;
                case 'lato':
                    $needed['Lato'] = 'Lato:wght@400;700';
                    break;
                case 'montserrat':
                    $needed['Montserrat'] = 'Montserrat:wght@500;600;700';
                    break;
                case 'poppins':
                    $needed['Poppins'] = 'Poppins:wght@500;600;700';
                    break;
                case 'raleway':
                    $needed['Raleway'] = 'Raleway:wght@500;600;700';
                    break;
            }
        }

        return $needed === [] ? '' : implode('&family=', $needed);
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

PTC_Theme::instance();

// ── Theme helpers ─────────────────────────────────────────────────────────────

if (!function_exists('ptc_css_color')) {
    function ptc_css_color(string $value, string $fallback): string
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

if (!function_exists('ptc_css_number')) {
    function ptc_css_number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;
        $number = max($min, min($max, $number));

        return rtrim(rtrim(number_format($number, 4, '.', ''), '0'), '.');
    }
}

if (!function_exists('ptc_safe_url')) {
    function ptc_safe_url(string $url, string $fallback = ''): string
    {
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

if (!function_exists('ptc_get_setting')) {
    function ptc_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('ptc_customizer_get')) {
    function ptc_customizer_get(string $section, string $key, mixed $default = ''): mixed
    {
        return ptc_get_setting($section, $key, $default);
    }
}

if (!function_exists('ptc_customizer_category')) {
    function ptc_customizer_category(string $section): array
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->getCategory($section) ?: [];
        } catch (\Throwable) {
            return [];
        }
    }
}

if (!function_exists('ptc_customizer_echo')) {
    function ptc_customizer_echo(string $section, string $key, mixed $default = ''): void
    {
        echo htmlspecialchars((string) ptc_get_setting($section, $key, $default), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('ptc_nav_menu')) {
    function ptc_nav_menu(string $location = 'primary'): void
    {
        theme_nav_menu($location);
    }
}

if (!function_exists('theme_nav_menu')) {
    function theme_nav_menu(string $location = 'primary'): void
    {
        try {
            $items = \CMS\ThemeManager::instance()->getMenu($location);
        } catch (\Throwable) {
            $items = [];
        }
        if (empty($items)) {
            return;
        }

        $requestUri = rtrim((string) ($_SERVER['REQUEST_URI'] ?? '/'), '/');
        echo '<ul>' . "\n";
        foreach ($items as $item) {
            $rawUrl = (string) ($item['url'] ?? '#');
            $path   = rtrim(parse_url($rawUrl, PHP_URL_PATH) ?? '/', '/');
            $isActive = ($path === '' || $path === '/')
                ? ($requestUri === '' || $requestUri === '/')
                : str_starts_with($requestUri, $path);
            $hasChildren = !empty($item['children']) && is_array($item['children']);
            $classes = [];
            if ($isActive) {
                $classes[] = 'active';
            }
            if ($hasChildren) {
                $classes[] = 'has-children';
            }
            $classAttr = $classes !== [] ? ' class="' . implode(' ', $classes) . '"' : '';
            $target = (!empty($item['target']) && $item['target'] === '_blank')
                ? ' target="_blank" rel="noopener noreferrer"'
                : '';
            $url   = htmlspecialchars(ptc_safe_url($rawUrl, '#'), ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            echo '<li' . $classAttr . '><a href="' . $url . '"' . $target . '>' . $label . '</a>' . "\n";

            if ($hasChildren) {
                echo '<ul class="ptc-submenu">' . "\n";
                foreach ($item['children'] as $child) {
                    $cUrl   = htmlspecialchars(ptc_safe_url((string) ($child['url'] ?? '#'), '#'), ENT_QUOTES, 'UTF-8');
                    $cLabel = htmlspecialchars((string) ($child['label'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $cTarget = (!empty($child['target']) && $child['target'] === '_blank')
                        ? ' target="_blank" rel="noopener noreferrer"'
                        : '';
                    echo '<li><a href="' . $cUrl . '"' . $cTarget . '>' . $cLabel . '</a></li>' . "\n";
                }
                echo '</ul>' . "\n";
            }

            echo '</li>' . "\n";
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
        return rtrim(ptc_safe_url((string) (defined('SITE_URL') ? SITE_URL : '/'), '/'), '/');
    }
}

if (!function_exists('ptc_site_title')) {
    function ptc_site_title(): string
    {
        return htmlspecialchars(\CMS\ThemeManager::instance()->getSiteTitle(), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('ptc_href')) {
    function ptc_href(string $target): string
    {
        $trimmed = trim($target);
        $site    = ptc_site_url();
        if ($trimmed === '') {
            return $site . '/';
        }
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return ptc_safe_url($trimmed, $site . '/');
        }
        if (str_starts_with($trimmed, 'mailto:') || str_starts_with($trimmed, 'tel:')) {
            return ptc_safe_url($trimmed, $site . '/');
        }
        if (str_starts_with($trimmed, '#')) {
            return $site . '/' . ltrim($trimmed, '#');
        }
        return ptc_safe_url($site . '/' . ltrim($trimmed, '/'), $site . '/');
    }
}

if (!function_exists('theme_route_url')) {
    function theme_route_url(string $name): string
    {
        return match ($name) {
            'home'     => ptc_site_url() . '/',
            'login'    => ptc_site_url() . '/login',
            'register' => ptc_site_url() . '/register',
            'member'   => ptc_site_url() . '/member',
            'blog'     => ptc_site_url() . '/blog',
            'search'   => ptc_site_url() . '/search',
            default    => ptc_site_url() . '/' . ltrim($name, '/'),
        };
    }
}

if (!function_exists('ptc_safe_headline')) {
    function ptc_safe_headline(string $raw): string
    {
        $escaped = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['&lt;span class=&quot;highlight&quot;&gt;', '&lt;/span&gt;'],
            ['<span class="highlight">', '</span>'],
            $escaped
        );
    }
}

if (!function_exists('ptc_body_class')) {
    function ptc_body_class(string ...$extra): string
    {
        $classes = ['ptc-body'];
        $sticky = filter_var(
            ptc_get_setting('layout', 'enable_sticky_header', true),
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

if (!function_exists('get_header')) {
    function get_header(): void
    {
        $path = __DIR__ . '/header.php';
        try {
            $tp = \CMS\ThemeManager::instance()->getThemePath(PTC_THEME_SLUG) . '/header.php';
            if (file_exists($tp)) {
                $path = $tp;
            }
        } catch (\Throwable) {
            // __DIR__ fallback
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(PTC_THEME_SLUG) . '/footer.php';
            if (file_exists($tp)) {
                $path = $tp;
            }
        } catch (\Throwable) {
            // __DIR__ fallback
        }
        if (file_exists($path)) {
            require $path;
        }
    }
}

if (!function_exists('theme_csrf_token')) {
    function theme_csrf_token(string $action = 'form'): string
    {
        return \CMS\Security::instance()->generateToken($action);
    }
}

if (!function_exists('theme_csrf_field')) {
    function theme_csrf_field(string $action = 'form'): void
    {
        $token = theme_csrf_token($action);
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
}

if (!function_exists('theme_get_flash')) {
    function theme_get_flash(string $type = 'error'): string
    {
        $key = ($type === 'success') ? 'success' : 'error';
        $msg = $_SESSION[$key] ?? '';
        unset($_SESSION[$key]);
        return is_string($msg) ? $msg : '';
    }
}

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
