<?php
declare(strict_types=1);

/**
 * Rundschau – News-Theme für 365CMS
 *
 * Datengetriebenes Nachrichtenportal: Top-Themen, Ticker, Meistgelesen,
 * automatische Ressort-Blöcke mit Ressortfarben.
 *
 * @package Rundschau_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('RUNDSCHAU_THEME_VERSION')) {
    define('RUNDSCHAU_THEME_VERSION', '1.0.0');
}

final class Rundschau_Theme
{
    public const SLUG = 'rundschau';

    /**
     * Schriftwahl → [CSS-Stack, Google-Fonts-Familie (leer = keine Webfont), Font-Manager-Slug].
     *
     * @var array<string, array{0:string,1:string,2:string}>
     */
    private const FONTS = [
        'archivo'          => ['"Archivo", "Arial Narrow", "Helvetica Neue", Arial, sans-serif', 'Archivo:wdth,wght@75..100,400..800', 'archivo'],
        'roboto-condensed' => ['"Roboto Condensed", "Arial Narrow", Arial, sans-serif', 'Roboto+Condensed:wght@400..800', 'roboto-condensed'],
        'oswald'           => ['"Oswald", "Arial Narrow", Arial, sans-serif', 'Oswald:wght@400..700', 'oswald'],
        'source-serif'     => ['"Source Serif 4", Georgia, "Times New Roman", serif', 'Source+Serif+4:ital,opsz,wght@0,8..60,400..700;1,8..60,400..700', 'source-serif-4'],
        'merriweather'     => ['"Merriweather", Georgia, serif', 'Merriweather:ital,wght@0,400;0,700;0,900;1,400', 'merriweather'],
        'lora'             => ['"Lora", Georgia, serif', 'Lora:ital,wght@0,400..700;1,400..700', 'lora'],
        'source-sans'      => ['"Source Sans 3", system-ui, -apple-system, "Segoe UI", sans-serif', 'Source+Sans+3:ital,wght@0,400..700;1,400..700', 'source-sans'],
        'inter'            => ['"Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Inter:wght@400;500;600;700', 'inter'],
        'system-serif'     => ['ui-serif, Charter, "Bitstream Charter", Cambria, Georgia, serif', '', ''],
        'system'           => ['system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', '', ''],
    ];

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
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'], 1);
        \CMS\Hooks::addAction('head', [$this, 'outputFonts'], 5);
        \CMS\Hooks::addAction('head', [$this, 'outputSeoTags'], 8);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags'], 10);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'], 16);
        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 99);

        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);
        \CMS\Hooks::addFilter('local_font_slugs', [$this, 'localFontSlugs']);
    }

    // ── Assets ───────────────────────────────────────────────────────────────

    public function enqueueStyles(): void
    {
        $href = $this->themeUrl() . '/style.css?v=' . rawurlencode(RUNDSCHAU_THEME_VERSION);
        echo '<link rel="stylesheet" href="' . rundschau_e($href) . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = $this->themeUrl() . '/js/theme.js?v=' . rawurlencode(RUNDSCHAU_THEME_VERSION);
        echo '<script src="' . rundschau_e($src) . '" defer></script>' . "\n";
    }

    public function outputFonts(): void
    {
        if (theme_use_local_fonts()) {
            return; // Core bindet über local_font_slugs lokal gespeicherte Schriften ein (DSGVO).
        }

        $families = [];
        foreach ($this->selectedFontKeys() as $key) {
            $family = self::FONTS[$key][1] ?? '';
            if ($family !== '') {
                $families[$family] = true;
            }
        }
        if ($families === []) {
            return;
        }

        $url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', array_keys($families)) . '&display=swap';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="stylesheet" href="' . rundschau_e($url) . '">' . "\n";
    }

    /**
     * @param mixed $slugs
     * @return array<int, string>
     */
    public function localFontSlugs(mixed $slugs): array
    {
        $slugs = is_array($slugs) ? $slugs : [];
        foreach ($this->selectedFontKeys() as $key) {
            $slug = self::FONTS[$key][2] ?? '';
            if ($slug !== '') {
                $slugs[] = $slug;
            }
        }

        return array_values(array_unique(array_map('strval', $slugs)));
    }

    /** SEO-Metadaten (Description, Canonical, Open Graph, Schema) aus dem Core-SEO-Modul. */
    public function outputSeoTags(): void
    {
        $html = '';
        try {
            if (class_exists('\\CMS\\Services\\SEOService')) {
                $seo = \CMS\Services\SEOService::getInstance();
                $html = (string) $seo->renderCurrentHeadTags();
                $custom = trim((string) $seo->getCustomHeaderCode());
                if ($custom !== '') {
                    $html .= $custom . "\n";
                }
            }
        } catch (\Throwable) {
            $html = '';
        }

        if (trim($html) === '') {
            $description = rundschau_site_description();
            if ($description !== '') {
                $html = '<meta name="description" content="' . rundschau_e($description) . '">' . "\n";
            }
        }

        echo $html;
    }

    public function outputMetaTags(): void
    {
        $brand = rundschau_css_color((string) rundschau_setting('colors', 'brand_color', '#0b3c49'), '#0b3c49');
        echo '<meta name="theme-color" content="' . rundschau_e($brand) . '">' . "\n";
    }

    public function outputCustomStyles(): void
    {
        $c = static fn(string $key, string $default): string => rundschau_css_color((string) rundschau_setting('colors', $key, $default), $default);
        $n = static fn(string $group, string $key, float $default, float $min, float $max): string => rundschau_css_number(rundschau_setting($group, $key, $default), $default, $min, $max);

        $vars = [
            '--rs-brand'        => $c('brand_color', '#0b3c49'),
            '--rs-brand-dark'   => $c('brand_dark', '#072a33'),
            '--rs-accent'       => $c('accent_color', '#e8590c'),
            '--rs-link'         => $c('link_color', '#0b5566'),
            '--rs-text'         => $c('text_color', '#14181c'),
            '--rs-muted'        => $c('muted_color', '#5b6670'),
            '--rs-paper'        => $c('paper_color', '#ffffff'),
            '--rs-surface'      => $c('surface_color', '#f2f4f5'),
            '--rs-border'       => $c('border_color', '#dde2e5'),
            '--rs-font-head'    => $this->fontStack((string) rundschau_setting('typography', 'font_headline', 'archivo'), 'archivo'),
            '--rs-font-body'    => $this->fontStack((string) rundschau_setting('typography', 'font_body', 'source-serif'), 'source-serif'),
            '--rs-size-article' => $n('typography', 'font_size_article', 19, 16, 22) . 'px',
            '--rs-stretch'      => rundschau_flag('typography', 'condensed_headlines', true) ? '88%' : '100%',
            '--rs-container'    => $n('layout', 'container_width', 1280, 1080, 1480) . 'px',
            '--rs-radius'       => $n('layout', 'border_radius', 4, 0, 14) . 'px',
            // Core-Variablen (editorjs-content.css) nach dem Core-Customizer-Block erneut an die Palette binden.
            '--primary-color'   => 'var(--rs-link)',
            '--accent'          => 'var(--rs-link)',
            '--border-color'    => 'var(--rs-border)',
            '--bg-primary'      => 'var(--rs-paper)',
            '--bg-secondary'    => 'var(--rs-surface)',
            '--text-primary'    => 'var(--rs-text)',
            '--text-secondary'  => 'var(--rs-muted)',
        ];

        $css = ':root{';
        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }
        $css .= '}';

        if (!rundschau_flag('header', 'enable_sticky_nav', true)) {
            $css .= '.rs-navbar{position:relative;top:auto;}';
        }
        if (!rundschau_flag('colors', 'use_ressort_colors', true)) {
            $css .= '[class*="rs-c-"]{--rs-ressort:var(--rs-brand);}';
        }

        $css .= rundschau_sanitize_custom_css((string) rundschau_setting('advanced', 'custom_css', ''));

        echo '<style id="rundschau-custom-vars"' . theme_csp_nonce_attr() . '>' . $css . '</style>' . "\n";
    }

    // ── Menüs ────────────────────────────────────────────────────────────────

    /**
     * @param mixed $locations
     * @return array<int, array{slug:string,label:string}>
     */
    public function registerMenuLocations(mixed $locations): array
    {
        $locations = is_array($locations) ? $locations : [];
        $existing = array_column($locations, 'slug');
        foreach ([
            'primary'      => 'Ressort-Navigation (Header)',
            'service-nav'  => 'Service-Leiste (oben)',
            'footer-nav'   => 'Footer-Navigation',
            'footer-legal' => 'Rechtliche Links (Footer)',
        ] as $slug => $label) {
            if (!in_array($slug, $existing, true)) {
                $locations[] = ['slug' => $slug, 'label' => $label];
            }
        }

        return $locations;
    }

    /** Liest Werte aus dem settings-Block der theme.json. */
    public function getConfig(string $key, mixed $default = ''): mixed
    {
        static $config = null;
        if ($config === null) {
            $config = [];
            $file = __DIR__ . '/theme.json';
            if (is_file($file)) {
                $decoded = json_decode((string) file_get_contents($file), true);
                $config = is_array($decoded['settings'] ?? null) ? $decoded['settings'] : [];
            }
        }

        return $config[$key] ?? $default;
    }

    public function themeUrl(): string
    {
        try {
            $url = (string) \CMS\ThemeManager::instance()->getThemeUrl();
        } catch (\Throwable) {
            $url = rtrim((string) SITE_URL, '/') . '/themes/' . self::SLUG;
        }

        return rtrim($url, '/');
    }

    private function fontStack(string $choice, string $fallback): string
    {
        return self::FONTS[$choice][0] ?? self::FONTS[$fallback][0];
    }

    /** @return array<int, string> */
    private function selectedFontKeys(): array
    {
        $keys = [];
        foreach ([['font_headline', 'archivo'], ['font_body', 'source-serif']] as [$setting, $default]) {
            $choice = (string) rundschau_setting('typography', $setting, $default);
            $keys[] = isset(self::FONTS[$choice]) ? $choice : $default;
        }

        return array_values(array_unique($keys));
    }
}

Rundschau_Theme::instance();

// ── Einstellungen & Ausgabe-Helfer ─────────────────────────────────────────────

if (!function_exists('rundschau_setting')) {
    function rundschau_setting(string $group, string $key, mixed $default = null): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($group, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('rundschau_flag')) {
    function rundschau_flag(string $group, string $key, bool $default): bool
    {
        $value = rundschau_setting($group, $key, $default);
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}

if (!function_exists('rundschau_text')) {
    function rundschau_text(string $group, string $key, string $default = ''): string
    {
        $value = rundschau_setting($group, $key, $default);

        return is_scalar($value) ? trim((string) $value) : $default;
    }
}

if (!function_exists('rundschau_int')) {
    function rundschau_int(string $group, string $key, int $default, int $min, int $max): int
    {
        $value = rundschau_setting($group, $key, $default);
        $number = is_numeric($value) ? (int) round((float) $value) : $default;

        return max($min, min($max, $number));
    }
}

if (!function_exists('rundschau_e')) {
    function rundschau_e(mixed $value): string
    {
        return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('rundschau_css_color')) {
    function rundschau_css_color(string $value, string $fallback): string
    {
        $candidate = trim($value);
        if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $candidate) === 1) {
            return $candidate;
        }
        if (preg_match('/^(?:rgb|rgba|hsl|hsla)\(\s*[0-9.,%\s\/]+\)$/i', $candidate) === 1) {
            return $candidate;
        }

        return $fallback;
    }
}

if (!function_exists('rundschau_css_number')) {
    function rundschau_css_number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;
        $number = max($min, min($max, $number));

        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }
}

if (!function_exists('rundschau_sanitize_custom_css')) {
    function rundschau_sanitize_custom_css(string $raw): string
    {
        $css = trim($raw);
        if ($css === '') {
            return '';
        }

        $css = preg_replace('#<\s*/?\s*style\b[^>]*>#i', '', $css) ?? '';
        $css = preg_replace('/@import\b[^;]*;?/i', '', $css) ?? '';
        $css = preg_replace('/expression\s*\([^)]*\)/i', '', $css) ?? '';
        $css = preg_replace('/url\s*\(\s*[\'"]?\s*(?:java|vb)script:[^)]*\)/i', '', $css) ?? '';
        $css = preg_replace('/\b(?:behavior|-moz-binding)\s*:[^;}]+;?/i', '', $css) ?? '';

        return str_replace('</', '<\/', $css);
    }
}

if (!function_exists('rundschau_safe_url')) {
    /** Lässt nur sichere Ziele zu: relative Pfade, Anker, http(s), mailto: und tel:. */
    function rundschau_safe_url(string $url, string $fallback = ''): string
    {
        $candidate = trim($url);
        if ($candidate === '' || preg_match('/[\x00-\x1F\x7F]/', $candidate) === 1 || str_starts_with($candidate, '//')) {
            return $fallback;
        }
        if (str_starts_with($candidate, '/') || str_starts_with($candidate, '?')) {
            return $candidate;
        }
        if (str_starts_with($candidate, '#')) {
            return preg_match('/^#[A-Za-z][A-Za-z0-9_:.-]*$/', $candidate) === 1 ? $candidate : $fallback;
        }
        if (preg_match('/^mailto:[^\s@<>"]+@[^\s@<>"]+\.[^\s@<>"]+$/i', $candidate) === 1) {
            return $candidate;
        }
        if (preg_match('/^tel:\+?[0-9][0-9\s().\/-]{2,31}$/i', $candidate) === 1) {
            return $candidate;
        }

        $scheme = strtolower((string) parse_url($candidate, PHP_URL_SCHEME));
        if (in_array($scheme, ['http', 'https'], true) && filter_var($candidate, FILTER_VALIDATE_URL) !== false) {
            return $candidate;
        }

        return $fallback;
    }
}

if (!function_exists('rundschau_url')) {
    function rundschau_url(string $target = '/'): string
    {
        $base = rtrim((string) SITE_URL, '/');
        $safe = rundschau_safe_url($target, '');
        if ($safe === '') {
            return $base . '/';
        }
        if (str_starts_with($safe, '/')) {
            return $base . $safe;
        }
        if (str_starts_with($safe, '#') || str_starts_with($safe, '?')) {
            return $base . '/' . $safe;
        }

        return $safe;
    }
}

if (!function_exists('rundschau_media_url')) {
    function rundschau_media_url(mixed $reference): string
    {
        $url = is_scalar($reference) ? trim((string) $reference) : '';
        if ($url === '') {
            return '';
        }

        try {
            if (class_exists('\\CMS\\Services\\MediaDeliveryService')) {
                $url = (string) \CMS\Services\MediaDeliveryService::getInstance()->normalizeUrl($url, true);
            }
        } catch (\Throwable) {
        }

        if (!str_starts_with($url, '/') && !preg_match('#^https?://#i', $url)) {
            $url = '/' . ltrim($url, '/');
        }

        $safe = rundschau_safe_url($url, '');
        if ($safe === '' || str_starts_with($safe, '#') || str_starts_with($safe, 'mailto:') || str_starts_with($safe, 'tel:')) {
            return '';
        }

        return str_starts_with($safe, '/') ? rtrim((string) SITE_URL, '/') . $safe : $safe;
    }
}

if (!function_exists('rundschau_site_title')) {
    function rundschau_site_title(): string
    {
        try {
            $title = trim((string) \CMS\ThemeManager::instance()->getSiteTitle());
        } catch (\Throwable) {
            $title = '';
        }

        return $title !== '' ? $title : (defined('SITE_NAME') ? (string) SITE_NAME : '365CMS');
    }
}

if (!function_exists('rundschau_site_description')) {
    function rundschau_site_description(): string
    {
        try {
            return trim((string) \CMS\ThemeManager::instance()->getSiteDescription());
        } catch (\Throwable) {
            return '';
        }
    }
}

if (!function_exists('rundschau_request_locale')) {
    function rundschau_request_locale(): string
    {
        try {
            $path = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
            $context = \CMS\Services\ContentLocalizationService::getInstance()->resolveRequestContext($path);
            $locale = strtolower((string) ($context['locale'] ?? 'de'));

            return preg_match('/^[a-z]{2}(?:-[a-z]{2})?$/', $locale) === 1 ? $locale : 'de';
        } catch (\Throwable) {
            return 'de';
        }
    }
}

// ── Datum & Text ───────────────────────────────────────────────────────────────

if (!function_exists('rundschau_timestamp')) {
    function rundschau_timestamp(mixed $value): ?int
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }
        $ts = strtotime($value);

        return $ts === false ? null : $ts;
    }
}

if (!function_exists('rundschau_format_date')) {
    /**
     * Deutsche Datumsformate ohne intl: 'long' (27. September 2026), 'weekday' (Sonntag, 27. September 2026),
     * 'datetime' (27.09.2026, 14:10 Uhr), 'short' (27.09.2026), 'iso'.
     */
    function rundschau_format_date(mixed $value, string $format = 'long'): string
    {
        $ts = is_int($value) ? $value : rundschau_timestamp($value);
        if ($ts === null) {
            return '';
        }

        $months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
        $days = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];
        $long = date('j', $ts) . '. ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);

        return match ($format) {
            'weekday'  => $days[(int) date('w', $ts)] . ', ' . $long,
            'datetime' => date('d.m.Y, H:i', $ts) . ' Uhr',
            'short'    => date('d.m.Y', $ts),
            'iso'      => date('c', $ts),
            default    => $long,
        };
    }
}

if (!function_exists('rundschau_time_label')) {
    /** Kurzes, relatives Zeitlabel für Nachrichtenlisten: „14:10“, „Gestern“, „25.09.“ oder „25.09.2025“. */
    function rundschau_time_label(mixed $value): string
    {
        $ts = rundschau_timestamp($value);
        if ($ts === null) {
            return '';
        }

        $today = strtotime('today');
        if ($ts >= $today) {
            return date('H:i', $ts);
        }
        if ($ts >= $today - 86400) {
            return 'Gestern';
        }

        return date('Y', $ts) === date('Y') ? date('d.m.', $ts) : date('d.m.Y', $ts);
    }
}

if (!function_exists('rundschau_plain_text')) {
    /** Klartext aus Editor.js-JSON (Rohdaten in Listen) oder gerendertem HTML. */
    function rundschau_plain_text(string $content): string
    {
        $content = trim($content);
        if ($content === '') {
            return '';
        }

        if (str_starts_with($content, '{')) {
            $decoded = json_decode($content, true);
            if (is_array($decoded) && is_array($decoded['blocks'] ?? null)) {
                $parts = [];
                $collect = static function (mixed $value) use (&$collect, &$parts): void {
                    if (is_string($value)) {
                        $parts[] = $value;
                    } elseif (is_array($value)) {
                        foreach ($value as $key => $item) {
                            if (in_array($key, ['id', 'type', 'url', 'file', 'style', 'level', 'alignment', 'tunes'], true)) {
                                continue;
                            }
                            $collect($item);
                        }
                    }
                };
                foreach ($decoded['blocks'] as $block) {
                    if (is_array($block)) {
                        $collect($block['data'] ?? []);
                    }
                }
                $content = implode(' ', $parts);
            }
        }

        // Script-/Style-Blöcke (z. B. Inhaltsverzeichnis des Cores) vor strip_tags() entfernen.
        $content = preg_replace('#<(script|style|template|noscript)\b[^>]*>.*?</\1\s*>#is', ' ', $content) ?? $content;
        $text = html_entity_decode(strip_tags(str_replace('<', ' <', $content)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }
}

if (!function_exists('rundschau_reading_time')) {
    function rundschau_reading_time(string $content): int
    {
        $text = rundschau_plain_text($content);
        if ($text === '') {
            return 1;
        }

        return max(1, (int) ceil(count(preg_split('/\s+/u', $text) ?: []) / 220));
    }
}

if (!function_exists('rundschau_excerpt')) {
    function rundschau_excerpt(object|array $post, int $length = 200, bool $allowContent = true): string
    {
        $data = is_array($post) ? $post : get_object_vars($post);
        $text = trim(strip_tags((string) ($data['excerpt'] ?? '')));
        if ($text === '') {
            $text = trim(strip_tags((string) ($data['meta_description'] ?? '')));
        }
        if ($text === '' && $allowContent) {
            $text = rundschau_plain_text((string) ($data['content'] ?? ''));
        }
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen($text, 'UTF-8') > $length) {
            $text = rtrim(mb_substr($text, 0, $length, 'UTF-8'));
            $text = (preg_replace('/\s+\S*$/u', '', $text) ?? $text) . ' …';
        }

        return $text;
    }
}

if (!function_exists('rundschau_initials')) {
    function rundschau_initials(string $name): string
    {
        $initials = '';
        foreach (array_slice(array_values(array_filter(preg_split('/\s+/u', trim($name)) ?: [])), 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
        }

        return $initials !== '' ? $initials : '•';
    }
}

// ── URLs ───────────────────────────────────────────────────────────────────────

if (!function_exists('rundschau_post_link')) {
    function rundschau_post_link(object|array $post): string
    {
        return rundschau_safe_url(theme_post_url($post), rundschau_url('/blog'));
    }
}

if (!function_exists('rundschau_archive_url')) {
    function rundschau_archive_url(string $type, string $slug = ''): string
    {
        try {
            if (function_exists('cms_get_archive_url')) {
                return (string) cms_get_archive_url($type, $slug);
            }
        } catch (\Throwable) {
        }

        return rundschau_url('/' . ($type === 'category' ? 'kategorie' : 'tag') . ($slug !== '' ? '/' . rawurlencode($slug) : ''));
    }
}

if (!function_exists('rundschau_author_url')) {
    function rundschau_author_url(int $authorId): string
    {
        if ($authorId <= 0) {
            return '';
        }
        try {
            if (class_exists('\\CMS\\Services\\MemberService')) {
                return rundschau_url(\CMS\Services\MemberService::getInstance()->buildPublicAuthorPath($authorId));
            }
        } catch (\Throwable) {
        }

        return rundschau_url('/author/user-' . $authorId);
    }
}

if (!function_exists('rundschau_search_url')) {
    function rundschau_search_url(string $query = ''): string
    {
        return rundschau_url('/search') . ($query !== '' ? '?q=' . rawurlencode($query) : '');
    }
}

// ── Ressorts (Kategorien) ─────────────────────────────────────────────────────

if (!function_exists('rundschau_categories')) {
    /**
     * Alle Kategorien mit direkter und kumulierter Beitragszahl (inkl. Unterkategorien).
     *
     * @return array<int, array{id:int,name:string,slug:string,description:string,parent_id:int,count:int,total:int,url:string}>
     */
    function rundschau_categories(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $rows = $db->get_results(
                "SELECT c.id, c.name, c.slug, c.description, c.parent_id, c.sort_order,
                        (SELECT COUNT(*) FROM {$prefix}posts p WHERE p.category_id = c.id AND " . cms_post_publication_where('p') . ") AS post_count
                 FROM {$prefix}post_categories c
                 ORDER BY c.sort_order ASC, c.name ASC"
            ) ?: [];
        } catch (\Throwable) {
            return $cache = [];
        }

        $items = [];
        foreach ($rows as $row) {
            $id = (int) ($row->id ?? 0);
            $slug = trim((string) ($row->slug ?? ''));
            if ($id <= 0 || $slug === '') {
                continue;
            }
            $items[$id] = [
                'id' => $id,
                'name' => trim((string) ($row->name ?? '')),
                'slug' => $slug,
                'description' => trim((string) ($row->description ?? '')),
                'parent_id' => (int) ($row->parent_id ?? 0),
                'count' => (int) ($row->post_count ?? 0),
                'total' => (int) ($row->post_count ?? 0),
                'url' => rundschau_archive_url('category', $slug),
            ];
        }

        foreach ($items as $id => $item) {
            $parent = $item['parent_id'];
            $guard = 0;
            while ($parent > 0 && isset($items[$parent]) && $guard++ < 10) {
                $items[$parent]['total'] += $item['count'];
                $parent = $items[$parent]['parent_id'];
            }
        }

        return $cache = $items;
    }
}

if (!function_exists('rundschau_category_branch_ids')) {
    /** @return array<int, int> Kategorie plus alle Unterkategorien */
    function rundschau_category_branch_ids(int $categoryId): array
    {
        $all = rundschau_categories();
        $ids = [$categoryId];
        $queue = [$categoryId];
        $guard = 0;
        while ($queue !== [] && $guard++ < 200) {
            $current = array_shift($queue);
            foreach ($all as $item) {
                if ($item['parent_id'] === $current && !in_array($item['id'], $ids, true)) {
                    $ids[] = $item['id'];
                    $queue[] = $item['id'];
                }
            }
        }

        return $ids;
    }
}

if (!function_exists('rundschau_ressorts')) {
    /**
     * Ressorts für die Startseite: explizite Slug-Liste aus dem Customizer oder
     * Hauptkategorien mit den meisten Beiträgen.
     *
     * @return array<int, array<string, mixed>>
     */
    function rundschau_ressorts(int $limit): array
    {
        if ($limit <= 0) {
            return [];
        }

        $all = rundschau_categories();
        $bySlug = [];
        foreach ($all as $item) {
            $bySlug[$item['slug']] = $item;
        }

        $selected = [];
        $slugs = array_filter(array_map(static fn(string $s): string => strtolower(trim($s)), explode(',', rundschau_text('rs_home', 'ressort_slugs'))));
        foreach ($slugs as $slug) {
            if (isset($bySlug[$slug]) && $bySlug[$slug]['total'] > 0) {
                $selected[] = $bySlug[$slug];
            }
        }

        if ($selected === []) {
            $top = array_values(array_filter($all, static fn(array $item): bool => $item['parent_id'] === 0 && $item['total'] > 0));
            usort($top, static fn(array $a, array $b): int => [$b['total'], $a['name']] <=> [$a['total'], $b['name']]);
            $selected = $top;
        }

        return array_slice($selected, 0, $limit);
    }
}

if (!function_exists('rundschau_ressort_class')) {
    /** Stabile Ressortfarbe je Kategorie-Slug (8 Farbstufen, siehe .rs-c-0 … .rs-c-7). */
    function rundschau_ressort_class(string $slug): string
    {
        $slug = trim($slug);
        if ($slug === '') {
            return 'rs-c-none';
        }

        return 'rs-c-' . (crc32(strtolower($slug)) % 8);
    }
}

// ── Beiträge ───────────────────────────────────────────────────────────────────

if (!function_exists('rundschau_get_posts')) {
    /**
     * @param array{limit?:int,offset?:int,exclude?:array<int,int>,category_id?:int,tag?:string,order?:string,days?:int,with_content?:bool} $args
     * @return array<int, object>
     */
    function rundschau_get_posts(array $args = []): array
    {
        $limit = max(1, min(50, (int) ($args['limit'] ?? 6)));
        $offset = max(0, (int) ($args['offset'] ?? 0));

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $where = [cms_post_publication_where('p')];
            $params = [];

            $exclude = array_values(array_filter(array_map('intval', (array) ($args['exclude'] ?? [])), static fn(int $id): bool => $id > 0));
            if ($exclude !== []) {
                $where[] = 'p.id NOT IN (' . implode(',', array_fill(0, count($exclude), '?')) . ')';
                array_push($params, ...$exclude);
            }

            $categoryId = (int) ($args['category_id'] ?? 0);
            if ($categoryId > 0) {
                $ids = rundschau_category_branch_ids($categoryId);
                $ph = implode(',', array_fill(0, count($ids), '?'));
                $where[] = "(p.category_id IN ({$ph}) OR EXISTS (SELECT 1 FROM {$prefix}post_category_rel pcr WHERE pcr.post_id = p.id AND pcr.category_id IN ({$ph})))";
                array_push($params, ...$ids, ...$ids);
            }

            $tag = strtolower(trim((string) ($args['tag'] ?? '')));
            if ($tag !== '') {
                $tagRow = $db->get_row("SELECT id, name FROM {$prefix}post_tags WHERE slug = ? LIMIT 1", [$tag]);
                $tagName = $tagRow !== null ? trim((string) ($tagRow->name ?? '')) : str_replace('-', ' ', $tag);
                $where[] = "(EXISTS (SELECT 1 FROM {$prefix}post_tag_rel ptr WHERE ptr.post_id = p.id AND ptr.tag_id = ?) OR CONCAT(',', LOWER(REPLACE(COALESCE(p.tags, ''), ', ', ',')), ',') LIKE ?)";
                $params[] = $tagRow !== null ? (int) ($tagRow->id ?? 0) : 0;
                $params[] = '%,' . mb_strtolower($tagName, 'UTF-8') . ',%';
            }

            $days = (int) ($args['days'] ?? 0);
            if ($days > 0) {
                $where[] = 'COALESCE(p.published_at, p.created_at) >= ?';
                $params[] = date('Y-m-d H:i:s', time() - $days * 86400);
            }

            $order = match ((string) ($args['order'] ?? 'newest')) {
                'popular' => 'p.views DESC, COALESCE(p.published_at, p.created_at) DESC',
                default   => 'COALESCE(p.published_at, p.created_at) DESC',
            };
            $contentColumn = !empty($args['with_content']) ? 'p.content' : "'' AS content";

            $rows = $db->get_results(
                "SELECT p.id, p.title, p.slug, p.slug_en, p.excerpt, p.meta_description, p.featured_image, p.published_at,
                        p.created_at, p.updated_at, p.category_id, p.author_id, p.views, {$contentColumn},
                        c.name AS category_name, c.slug AS category_slug,
                        COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Redaktion') AS author_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                 LEFT JOIN {$prefix}users u ON u.id = p.author_id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY {$order}
                 LIMIT {$limit} OFFSET {$offset}",
                $params
            );

            return is_array($rows) ? $rows : [];
        } catch (\Throwable) {
            return [];
        }
    }
}

if (!function_exists('rundschau_popular_posts')) {
    /** Meistgelesen im Zeitraum; füllt bei zu wenigen Treffern mit dem Gesamtzeitraum auf. */
    function rundschau_popular_posts(int $limit, int $days = 30, array $exclude = []): array
    {
        $posts = $days > 0 ? rundschau_get_posts(['limit' => $limit, 'order' => 'popular', 'days' => $days, 'exclude' => $exclude]) : [];
        if (count($posts) < $limit) {
            $ids = array_merge($exclude, array_map(static fn(object $p): int => (int) ($p->id ?? 0), $posts));
            $posts = array_merge($posts, rundschau_get_posts(['limit' => $limit - count($posts), 'order' => 'popular', 'exclude' => $ids]));
        }

        return $posts;
    }
}

if (!function_exists('rundschau_ids')) {
    /** @param array<int, object> $posts @return array<int, int> */
    function rundschau_ids(array $posts): array
    {
        return array_values(array_filter(array_map(static fn(mixed $p): int => is_object($p) ? (int) ($p->id ?? 0) : 0, $posts)));
    }
}

if (!function_exists('rundschau_post_tags')) {
    /** @return array<int, array{name:string,url:string}> */
    function rundschau_post_tags(object|array $post): array
    {
        $data = is_array($post) ? $post : get_object_vars($post);
        $items = is_array($data['tag_items'] ?? null) ? $data['tag_items'] : [];
        if ($items === []) {
            foreach (array_filter(array_map('trim', explode(',', (string) ($data['tags'] ?? '')))) as $name) {
                $items[] = ['name' => $name, 'slug' => ''];
            }
        }

        $tags = [];
        foreach ($items as $item) {
            $name = trim((string) (is_array($item) ? ($item['name'] ?? '') : $item));
            if ($name === '') {
                continue;
            }
            $slug = trim((string) (is_array($item) ? ($item['slug'] ?? '') : ''));
            if ($slug === '') {
                $slug = trim((string) (preg_replace('/[^a-z0-9]+/', '-', strtr(mb_strtolower($name, 'UTF-8'), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'])) ?? ''), '-');
            }
            $tags[] = ['name' => $name, 'url' => $slug !== '' ? rundschau_archive_url('tag', $slug) : rundschau_search_url($name)];
        }

        return $tags;
    }
}

// ── Komponenten ────────────────────────────────────────────────────────────────

if (!function_exists('rundschau_nav_menu')) {
    /**
     * @param array<int, array{label:string,url:string}> $fallback
     * @param bool $appendRessorts Hauptkategorien anhängen, die im gepflegten Menü noch fehlen
     */
    function rundschau_nav_menu(string $location, string $class = '', array $fallback = [], bool $appendRessorts = false): void
    {
        try {
            $items = \CMS\ThemeManager::instance()->getMenu($location);
        } catch (\Throwable) {
            $items = [];
        }
        if (!is_array($items) || $items === []) {
            $items = $fallback;
        } elseif ($appendRessorts) {
            $known = [];
            foreach ($items as $item) {
                if (is_array($item)) {
                    $known[rtrim((string) (parse_url(rundschau_url((string) ($item['url'] ?? '')), PHP_URL_PATH) ?? ''), '/')] = true;
                }
            }
            foreach (rundschau_categories() as $category) {
                if ($category['parent_id'] !== 0 || $category['total'] <= 0) {
                    continue;
                }
                $path = rtrim((string) (parse_url($category['url'], PHP_URL_PATH) ?? ''), '/');
                if (!isset($known[$path])) {
                    $items[] = ['label' => $category['name'], 'url' => $category['url'], 'class' => rundschau_ressort_class($category['slug'])];
                    $known[$path] = true;
                }
            }
        }
        if ($items === []) {
            return;
        }

        $current = rtrim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/');
        $sitePath = rtrim((string) (parse_url((string) SITE_URL, PHP_URL_PATH) ?? ''), '/');
        $siteHost = parse_url((string) SITE_URL, PHP_URL_HOST);

        echo '<ul' . ($class !== '' ? ' class="' . rundschau_e($class) . '"' : '') . '>';
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? $item['title'] ?? ''));
            if ($label === '') {
                continue;
            }
            $rawUrl = (string) ($item['url'] ?? '#');
            $href = rundschau_url($rawUrl);
            $path = rtrim((string) (parse_url($href, PHP_URL_PATH) ?? ''), '/');
            if ($sitePath !== '' && str_starts_with($path, $sitePath)) {
                $path = rtrim(substr($path, strlen($sitePath)), '/');
            }
            $isExternal = parse_url($href, PHP_URL_HOST) !== $siteHost;
            $isActive = !$isExternal && !str_contains($rawUrl, '#')
                && ($path === '' ? $current === '' : ($current === $path || str_starts_with($current . '/', $path . '/')));

            $attrs = $isActive ? ' aria-current="page"' : '';
            if (($item['target'] ?? '') === '_blank' || $isExternal) {
                $attrs .= ' target="_blank" rel="noopener noreferrer"';
            }
            $cls = trim(($isActive ? 'is-active ' : '') . (string) ($item['class'] ?? ''));

            echo '<li' . ($cls !== '' ? ' class="' . rundschau_e($cls) . '"' : '') . '><a href="' . rundschau_e($href) . '"' . $attrs . '>' . rundschau_e($label) . '</a></li>';
        }
        echo '</ul>';
    }
}

if (!function_exists('rundschau_default_menu')) {
    /** @return array<int, array{label:string,url:string,class?:string}> */
    function rundschau_default_menu(string $location): array
    {
        if ($location === 'primary') {
            $items = [['label' => 'Startseite', 'url' => '/']];
            $top = array_values(array_filter(rundschau_categories(), static fn(array $c): bool => $c['parent_id'] === 0 && $c['total'] > 0));
            foreach (array_slice($top, 0, 7) as $category) {
                $items[] = ['label' => $category['name'], 'url' => $category['url'], 'class' => rundschau_ressort_class($category['slug'])];
            }

            return $items;
        }

        return match ($location) {
            'service-nav' => [
                ['label' => 'Alle Meldungen', 'url' => '/blog'],
                ['label' => 'Themen A–Z', 'url' => rundschau_archive_url('tag')],
                ['label' => 'RSS', 'url' => '/feed'],
            ],
            'footer-nav' => [
                ['label' => 'Alle Meldungen', 'url' => '/blog'],
                ['label' => 'Ressorts', 'url' => rundschau_archive_url('category')],
                ['label' => 'Themen A–Z', 'url' => rundschau_archive_url('tag')],
                ['label' => 'Suche', 'url' => '/search'],
                ['label' => 'RSS-Feed', 'url' => '/feed'],
            ],
            'footer-legal' => [
                ['label' => 'Impressum', 'url' => '/impressum'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz'],
            ],
            default => [],
        };
    }
}

if (!function_exists('rundschau_icon')) {
    function rundschau_icon(string $name): string
    {
        $paths = [
            'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
            'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
            'up'       => '<path d="M12 19V5M6 11l6-6 6 6"/>',
            'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'pause'    => '<path d="M9 6v12M15 6v12"/>',
            'play'     => '<path d="M8 5.5v13l10-6.5z"/>',
            'link'     => '<path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1.5 1.5"/><path d="M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1.5-1.5"/>',
            'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'whatsapp' => '<path d="M4 20l1.2-3.6A8 8 0 1 1 8 19z"/><path d="M9.5 9.5c.3 2 2 3.7 4 4l1-1 2 1c-.3 1.2-1.3 1.8-2.5 1.5a7 7 0 0 1-5-5c-.3-1.2.3-2.2 1.5-2.5l1 2z"/>',
            'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
            'rss'      => '<path d="M5 5a14 14 0 0 1 14 14M5 11a8 8 0 0 1 8 8"/><circle cx="6" cy="18" r="1.5"/>',
            'bolt'     => '<path d="M13 3 5 13.5h6L10 21l8-10.5h-6z"/>',
            'print'    => '<path d="M7 9V4h10v5M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><rect x="7" y="14" width="10" height="6"/>',
        ];
        $body = $paths[$name] ?? '';
        if ($body === '') {
            return '';
        }

        return '<svg class="rs-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $body . '</svg>';
    }
}

if (!function_exists('rundschau_pagination')) {
    function rundschau_pagination(int $current, int $total, string $param = 'p'): void
    {
        if ($total <= 1) {
            return;
        }

        $path = (string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
        $build = static function (int $number) use ($path, $param): string {
            $query = array_filter($_GET, static fn(mixed $v): bool => is_scalar($v));
            unset($query['p'], $query['page']);
            if ($number > 1) {
                $query[$param] = $number;
            }
            $qs = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

            return $path . ($qs !== '' ? '?' . $qs : '');
        };

        $pages = [1, $total];
        for ($i = max(1, $current - 2); $i <= min($total, $current + 2); $i++) {
            $pages[] = $i;
        }
        $pages = array_values(array_unique($pages));
        sort($pages);

        echo '<nav class="rs-pagination" aria-label="Seitennavigation">';
        if ($current > 1) {
            echo '<a class="rs-pagination__step" href="' . rundschau_e($build($current - 1)) . '" rel="prev">' . rundschau_icon('arrow-left') . '<span>Zurück</span></a>';
        }
        echo '<ol class="rs-pagination__pages">';
        $last = 0;
        foreach ($pages as $number) {
            if ($last > 0 && $number - $last > 1) {
                echo '<li class="rs-pagination__gap" aria-hidden="true">…</li>';
            }
            echo $number === $current
                ? '<li><span aria-current="page">' . $number . '</span></li>'
                : '<li><a href="' . rundschau_e($build($number)) . '">' . $number . '</a></li>';
            $last = $number;
        }
        echo '</ol>';
        if ($current < $total) {
            echo '<a class="rs-pagination__step" href="' . rundschau_e($build($current + 1)) . '" rel="next"><span>Weiter</span>' . rundschau_icon('arrow') . '</a>';
        }
        echo '</nav>';
    }
}

if (!function_exists('rundschau_search_form')) {
    function rundschau_search_form(string $id, string $value = '', string $class = ''): void
    {
        ?>
        <form role="search" method="get" action="<?php echo rundschau_e(rundschau_search_url()); ?>" class="rs-searchform<?php echo $class !== '' ? ' ' . rundschau_e($class) : ''; ?>">
            <label for="<?php echo rundschau_e($id); ?>" class="rs-visually-hidden">Suchbegriff</label>
            <input id="<?php echo rundschau_e($id); ?>" type="search" name="q" value="<?php echo rundschau_e($value); ?>" placeholder="Nachrichten durchsuchen …" maxlength="120" autocomplete="off">
            <button type="submit" class="rs-button"><?php echo rundschau_icon('search'); ?><span>Suchen</span></button>
        </form>
        <?php
    }
}

if (!function_exists('rundschau_kicker')) {
    /** Ressort-Kicker (Kategorie) mit Link und Ressortfarbe. */
    function rundschau_kicker(object $post, string $class = 'rs-kicker'): string
    {
        $name = trim((string) ($post->category_name ?? ''));
        if ($name === '') {
            return '';
        }
        $slug = trim((string) ($post->category_slug ?? ''));

        return '<a class="' . rundschau_e($class . ' ' . rundschau_ressort_class($slug)) . '" href="' . rundschau_e(rundschau_archive_url('category', $slug)) . '">' . rundschau_e($name) . '</a>';
    }
}

if (!function_exists('rundschau_teaser')) {
    /**
     * Einheitliche Teaser-Ausgabe.
     *
     * @param string $variant lead | feature | card | row | compact
     */
    function rundschau_teaser(object $post, string $variant = 'card', string $headingTag = 'h3'): void
    {
        $url = rundschau_post_link($post);
        $image = in_array($variant, ['lead', 'feature', 'card', 'row'], true) ? rundschau_media_url($post->featured_image ?? '') : '';
        $date = (string) ($post->published_at ?? $post->created_at ?? '');
        $excerpt = in_array($variant, ['lead', 'feature', 'row'], true) ? rundschau_excerpt($post, $variant === 'row' ? 170 : 240) : '';
        $headingTag = in_array($headingTag, ['h2', 'h3', 'h4'], true) ? $headingTag : 'h3';
        $size = $variant === 'lead' ? ['1200', '675'] : ['640', '360'];
        ?>
        <article class="rs-teaser rs-teaser--<?php echo rundschau_e($variant); ?><?php echo $image === '' ? ' rs-teaser--noimg' : ''; ?>">
            <?php if ($image !== '') : ?>
                <a class="rs-teaser__media" href="<?php echo rundschau_e($url); ?>" tabindex="-1" aria-hidden="true">
                    <img src="<?php echo rundschau_e($image); ?>" alt="" width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>" loading="<?php echo $variant === 'lead' ? 'eager' : 'lazy'; ?>" decoding="async">
                </a>
            <?php endif; ?>
            <div class="rs-teaser__body">
                <?php echo rundschau_kicker($post); ?>
                <<?php echo $headingTag; ?> class="rs-teaser__title"><a href="<?php echo rundschau_e($url); ?>"><?php echo rundschau_e((string) ($post->title ?? '')); ?></a></<?php echo $headingTag; ?>>
                <?php if ($excerpt !== '') : ?>
                    <p class="rs-teaser__excerpt"><?php echo rundschau_e($excerpt); ?></p>
                <?php endif; ?>
                <p class="rs-meta">
                    <time datetime="<?php echo rundschau_e(rundschau_format_date($date, 'iso')); ?>"><?php echo rundschau_e(rundschau_time_label($date)); ?></time>
                    <?php if ($variant === 'lead' && trim((string) ($post->author_name ?? '')) !== '') : ?>
                        <span class="rs-meta__sep" aria-hidden="true"></span><span><?php echo rundschau_e((string) $post->author_name); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </article>
        <?php
    }
}

if (!function_exists('rundschau_page_title')) {
    function rundschau_page_title(array $context): string
    {
        $site = rundschau_site_title();
        $label = '';

        if (isset($context['post']) || isset($context['page'])) {
            try {
                $payload = \CMS\Services\SEOService::getInstance()->getCurrentSeoPayload();
                $label = trim((string) ($payload['title'] ?? ''));
                if ($label !== '') {
                    return (string) \CMS\Hooks::applyFilters('page_title', $label);
                }
            } catch (\Throwable) {
            }
            $source = $context['post'] ?? $context['page'];
            $label = trim((string) (is_array($source) ? ($source['title'] ?? '') : ($source->title ?? '')));
        } elseif (isset($context['author']) && is_array($context['author'])) {
            $label = 'Beiträge von ' . trim((string) ($context['author']['display_name'] ?? ''));
        } elseif (isset($context['category']) && is_array($context['category'])) {
            $label = trim((string) ($context['category']['name'] ?? ''));
        } elseif (isset($context['tag']) && is_array($context['tag'])) {
            $label = 'Thema: ' . trim((string) ($context['tag']['name'] ?? ''));
        } elseif (array_key_exists('results', $context)) {
            $query = trim((string) ($context['query'] ?? ''));
            $label = $query !== '' ? 'Suche: ' . $query : 'Suche';
        } elseif (isset($context['posts'])) {
            $label = 'Alle Meldungen';
        } elseif (http_response_code() === 404) {
            $label = 'Seite nicht gefunden';
        }

        $separator = '–';
        try {
            $sep = trim((string) \CMS\Services\SEOService::getInstance()->getTitleSeparator());
            if ($sep !== '') {
                $separator = $sep;
            }
        } catch (\Throwable) {
        }

        return (string) \CMS\Hooks::applyFilters('page_title', $label !== '' ? $label . ' ' . $separator . ' ' . $site : $site);
    }
}

// ── 365CMS 3.4 Laufzeit-Helfer (CSP-Nonce, lokale Schriften, Permalinks) ──────

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
            $db = \CMS\Database::instance();
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
        $type = (string) ($data['_type'] ?? '');
        $slug = ltrim((string) ($data['slug'] ?? ''), '/');

        if ($type === 'post') {
            return theme_post_url($data);
        }
        if (($type === 'category' || $type === 'tag') && function_exists('cms_get_archive_url')) {
            $plain = rawurldecode((string) preg_replace('#^(?:category|kategorie|tag)/#', '', $slug));
            try {
                return (string) cms_get_archive_url($type, $plain);
            } catch (\Throwable) {
            }
        }

        return rtrim((string) SITE_URL, '/') . '/' . implode('/', array_map('rawurlencode', array_map('rawurldecode', explode('/', $slug))));
    }
}
