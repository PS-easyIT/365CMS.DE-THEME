<?php
declare(strict_types=1);

/**
 * Kontor – Business-Theme für 365CMS
 *
 * Unternehmenswebsite für Mittelstand, Kanzleien, Beratungen und Dienstleister:
 * pflegbare Startseiten-Sektionen, Kontaktkarte, Kontakt-Band und Blog („Aktuelles“).
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('KONTOR_THEME_VERSION')) {
    define('KONTOR_THEME_VERSION', '1.0.0');
}

final class Kontor_Theme
{
    public const SLUG = 'kontor';

    /**
     * Schriftwahl → [CSS-Stack, Google-Fonts-Familie (leer = keine Webfont), Font-Manager-Slug].
     *
     * @var array<string, array{0:string,1:string,2:string}>
     */
    private const FONTS = [
        'manrope'           => ['"Manrope", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Manrope:wght@400..800', 'manrope'],
        'plus-jakarta-sans' => ['"Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", sans-serif', 'Plus+Jakarta+Sans:wght@400..800', 'plus-jakarta-sans'],
        'sora'              => ['"Sora", system-ui, -apple-system, "Segoe UI", sans-serif', 'Sora:wght@400..800', 'sora'],
        'montserrat'        => ['"Montserrat", system-ui, -apple-system, "Segoe UI", sans-serif', 'Montserrat:wght@400..800', 'montserrat'],
        'source-serif'      => ['"Source Serif 4", Georgia, "Times New Roman", serif', 'Source+Serif+4:ital,opsz,wght@0,8..60,400..700;1,8..60,400..700', 'source-serif-4'],
        'inter'             => ['"Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Inter:wght@400;500;600;700', 'inter'],
        'source-sans'       => ['"Source Sans 3", system-ui, -apple-system, "Segoe UI", sans-serif', 'Source+Sans+3:wght@400;600;700', 'source-sans'],
        'system'            => ['system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', '', ''],
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

    public function enqueueStyles(): void
    {
        $href = $this->themeUrl() . '/style.css?v=' . rawurlencode(KONTOR_THEME_VERSION);
        echo '<link rel="stylesheet" href="' . kontor_e($href) . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = $this->themeUrl() . '/js/theme.js?v=' . rawurlencode(KONTOR_THEME_VERSION);
        echo '<script src="' . kontor_e($src) . '" defer></script>' . "\n";
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
        echo '<link rel="stylesheet" href="' . kontor_e($url) . '">' . "\n";
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
            $description = kontor_site_description();
            if ($description !== '') {
                $html = '<meta name="description" content="' . kontor_e($description) . '">' . "\n";
            }
        }

        echo $html;
    }

    public function outputMetaTags(): void
    {
        $ink = kontor_css_color((string) kontor_setting('colors', 'ink_color', '#0e2f28'), '#0e2f28');
        echo '<meta name="theme-color" content="' . kontor_e($ink) . '">' . "\n";
    }

    public function outputCustomStyles(): void
    {
        $c = static fn(string $key, string $default): string => kontor_css_color((string) kontor_setting('colors', $key, $default), $default);
        $n = static fn(string $group, string $key, float $default, float $min, float $max): string => kontor_css_number(kontor_setting($group, $key, $default), $default, $min, $max);

        $vars = [
            '--kt-ink'           => $c('ink_color', '#0e2f28'),
            '--kt-primary'       => $c('primary_color', '#156b52'),
            '--kt-primary-hover' => $c('primary_hover', '#0f5541'),
            '--kt-accent'        => $c('accent_color', '#62d6a4'),
            '--kt-text'          => $c('text_color', '#1c2b27'),
            '--kt-muted'         => $c('muted_color', '#5a6b65'),
            '--kt-paper'         => $c('paper_color', '#ffffff'),
            '--kt-sand'          => $c('sand_color', '#f5f2eb'),
            '--kt-border'        => $c('border_color', '#e3e6e1'),
            '--kt-font-head'     => $this->fontStack((string) kontor_setting('typography', 'font_heading', 'manrope'), 'manrope'),
            '--kt-font-body'     => $this->fontStack((string) kontor_setting('typography', 'font_body', 'manrope'), 'manrope'),
            '--kt-size-base'     => $n('typography', 'font_size_base', 17, 15, 20) . 'px',
            '--kt-container'     => $n('layout', 'container_width', 1200, 1040, 1440) . 'px',
            '--kt-radius'        => $n('layout', 'border_radius', 14, 0, 28) . 'px',
            // Core-Variablen (editorjs-content.css) nach dem Core-Customizer-Block erneut an die Palette binden.
            '--primary-color'    => 'var(--kt-primary)',
            '--accent'           => 'var(--kt-primary)',
            '--border-color'     => 'var(--kt-border)',
            '--bg-primary'       => 'var(--kt-paper)',
            '--bg-secondary'     => 'var(--kt-sand)',
            '--text-primary'     => 'var(--kt-text)',
            '--text-secondary'   => 'var(--kt-muted)',
        ];

        $css = ':root{';
        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }
        $css .= '}';

        if (!kontor_flag('header', 'enable_sticky_header', true)) {
            $css .= '.kt-header{position:relative;}';
        }

        $css .= kontor_sanitize_custom_css((string) kontor_setting('advanced', 'custom_css', ''));

        echo '<style id="kontor-custom-vars"' . theme_csp_nonce_attr() . '>' . $css . '</style>' . "\n";
    }

    /**
     * @param mixed $locations
     * @return array<int, array{slug:string,label:string}>
     */
    public function registerMenuLocations(mixed $locations): array
    {
        $locations = is_array($locations) ? $locations : [];
        $existing = array_column($locations, 'slug');
        foreach ([
            'primary'         => 'Hauptnavigation (Header)',
            'footer-nav'      => 'Footer: Unternehmen',
            'footer-services' => 'Footer: Leistungen',
            'footer-legal'    => 'Rechtliche Links (Footer)',
        ] as $slug => $label) {
            if (!in_array($slug, $existing, true)) {
                $locations[] = ['slug' => $slug, 'label' => $label];
            }
        }

        return $locations;
    }

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
        foreach ([['font_heading', 'manrope'], ['font_body', 'manrope']] as [$setting, $default]) {
            $choice = (string) kontor_setting('typography', $setting, $default);
            $keys[] = isset(self::FONTS[$choice]) ? $choice : $default;
        }

        return array_values(array_unique($keys));
    }
}

Kontor_Theme::instance();

// ── Einstellungen & Ausgabe-Helfer ─────────────────────────────────────────────

if (!function_exists('kontor_setting')) {
    function kontor_setting(string $group, string $key, mixed $default = null): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($group, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('kontor_flag')) {
    function kontor_flag(string $group, string $key, bool $default): bool
    {
        $value = kontor_setting($group, $key, $default);
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}

if (!function_exists('kontor_text')) {
    function kontor_text(string $group, string $key, string $default = ''): string
    {
        $value = kontor_setting($group, $key, $default);

        return is_scalar($value) ? trim((string) $value) : $default;
    }
}

if (!function_exists('kontor_int')) {
    function kontor_int(string $group, string $key, int $default, int $min, int $max): int
    {
        $value = kontor_setting($group, $key, $default);
        $number = is_numeric($value) ? (int) round((float) $value) : $default;

        return max($min, min($max, $number));
    }
}

if (!function_exists('kontor_e')) {
    function kontor_e(mixed $value): string
    {
        return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('kontor_lines')) {
    /** @return array<int, string> Nicht-leere Zeilen eines Textfelds */
    function kontor_lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', $value) ?: []), static fn(string $line): bool => $line !== ''));
    }
}

if (!function_exists('kontor_highlight')) {
    /** Escaped Text; *Wörter in Sternchen* werden als <em class="kt-hl"> hervorgehoben. */
    function kontor_highlight(string $text): string
    {
        $escaped = kontor_e($text);

        return preg_replace('/\*([^*]{1,80})\*/u', '<em class="kt-hl">$1</em>', $escaped) ?? $escaped;
    }
}

if (!function_exists('kontor_css_color')) {
    function kontor_css_color(string $value, string $fallback): string
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

if (!function_exists('kontor_css_number')) {
    function kontor_css_number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;
        $number = max($min, min($max, $number));

        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }
}

if (!function_exists('kontor_sanitize_custom_css')) {
    function kontor_sanitize_custom_css(string $raw): string
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

if (!function_exists('kontor_safe_url')) {
    /** Lässt nur sichere Ziele zu: relative Pfade, Anker, http(s), mailto: und tel:. */
    function kontor_safe_url(string $url, string $fallback = ''): string
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

if (!function_exists('kontor_url')) {
    /**
     * Absolute Site-URL für relative Pfade. Anker (#leistungen) zeigen auf die Startseite,
     * außer $keepAnchor ist gesetzt (Sprungmarke auf derselben Seite).
     */
    function kontor_url(string $target = '/', bool $keepAnchor = false): string
    {
        $base = rtrim((string) SITE_URL, '/');
        $safe = kontor_safe_url($target, '');
        if ($safe === '') {
            return $base . '/';
        }
        if (str_starts_with($safe, '/')) {
            return $base . $safe;
        }
        if (str_starts_with($safe, '#')) {
            return $keepAnchor ? $safe : $base . '/' . $safe;
        }
        if (str_starts_with($safe, '?')) {
            return $base . '/' . $safe;
        }

        return $safe;
    }
}

if (!function_exists('kontor_media_url')) {
    function kontor_media_url(mixed $reference): string
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

        $safe = kontor_safe_url($url, '');
        if ($safe === '' || str_starts_with($safe, '#') || str_starts_with($safe, 'mailto:') || str_starts_with($safe, 'tel:')) {
            return '';
        }

        return str_starts_with($safe, '/') ? rtrim((string) SITE_URL, '/') . $safe : $safe;
    }
}

if (!function_exists('kontor_site_title')) {
    function kontor_site_title(): string
    {
        try {
            $title = trim((string) \CMS\ThemeManager::instance()->getSiteTitle());
        } catch (\Throwable) {
            $title = '';
        }

        return $title !== '' ? $title : (defined('SITE_NAME') ? (string) SITE_NAME : '365CMS');
    }
}

if (!function_exists('kontor_site_description')) {
    function kontor_site_description(): string
    {
        try {
            return trim((string) \CMS\ThemeManager::instance()->getSiteDescription());
        } catch (\Throwable) {
            return '';
        }
    }
}

if (!function_exists('kontor_request_locale')) {
    function kontor_request_locale(): string
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

if (!function_exists('kontor_company_name')) {
    function kontor_company_name(): string
    {
        $name = kontor_text('kt_contact', 'company_name');

        return $name !== '' ? $name : kontor_site_title();
    }
}

if (!function_exists('kontor_contact')) {
    /** @return array{phone:string,phone_href:string,email:string,email_href:string,address:array<int,string>,hours:array<int,string>} */
    function kontor_contact(): array
    {
        $phone = kontor_text('kt_contact', 'phone');
        $phoneHref = kontor_safe_url('tel:' . preg_replace('/[^0-9+]/', '', $phone), '');
        $email = kontor_text('kt_contact', 'email');
        $emailHref = filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? 'mailto:' . $email : '';

        return [
            'phone' => $phone,
            'phone_href' => $phone !== '' ? $phoneHref : '',
            'email' => $emailHref !== '' ? $email : '',
            'email_href' => $emailHref,
            'address' => kontor_lines(kontor_text('kt_contact', 'address')),
            'hours' => kontor_lines(kontor_text('kt_contact', 'hours')),
        ];
    }
}

if (!function_exists('kontor_social_links')) {
    /** @return array<int, array{label:string,url:string,icon:string}> */
    function kontor_social_links(): array
    {
        $links = [];
        foreach (['linkedin_url' => ['LinkedIn', 'linkedin'], 'xing_url' => ['XING', 'xing'], 'instagram_url' => ['Instagram', 'instagram']] as $key => [$label, $icon]) {
            $url = kontor_safe_url(kontor_text('kt_contact', $key), '');
            if ($url !== '' && preg_match('#^https?://#i', $url) === 1) {
                $links[] = ['label' => $label, 'url' => $url, 'icon' => $icon];
            }
        }

        return $links;
    }
}

// ── Datum & Text ───────────────────────────────────────────────────────────────

if (!function_exists('kontor_format_date')) {
    function kontor_format_date(mixed $value, string $format = 'long'): string
    {
        $ts = is_string($value) && trim($value) !== '' ? strtotime($value) : false;
        if ($ts === false) {
            return '';
        }

        $months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

        return match ($format) {
            'short' => date('d.m.Y', $ts),
            'iso'   => date('c', $ts),
            default => date('j', $ts) . '. ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts),
        };
    }
}

if (!function_exists('kontor_plain_text')) {
    /** Klartext aus Editor.js-JSON (Rohdaten in Listen) oder gerendertem HTML. */
    function kontor_plain_text(string $content): string
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

if (!function_exists('kontor_reading_time')) {
    function kontor_reading_time(string $content): int
    {
        $text = kontor_plain_text($content);

        return $text === '' ? 1 : max(1, (int) ceil(count(preg_split('/\s+/u', $text) ?: []) / 220));
    }
}

if (!function_exists('kontor_excerpt')) {
    function kontor_excerpt(object|array $post, int $length = 180, bool $allowContent = true): string
    {
        $data = is_array($post) ? $post : get_object_vars($post);
        $text = trim(strip_tags((string) ($data['excerpt'] ?? '')));
        if ($text === '') {
            $text = trim(strip_tags((string) ($data['meta_description'] ?? '')));
        }
        if ($text === '' && $allowContent) {
            $text = kontor_plain_text((string) ($data['content'] ?? ''));
        }
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen($text, 'UTF-8') > $length) {
            $text = rtrim(mb_substr($text, 0, $length, 'UTF-8'));
            $text = (preg_replace('/\s+\S*$/u', '', $text) ?? $text) . ' …';
        }

        return $text;
    }
}

if (!function_exists('kontor_initials')) {
    function kontor_initials(string $name): string
    {
        $initials = '';
        foreach (array_slice(array_values(array_filter(preg_split('/[\s&-]+/u', trim($name)) ?: [])), 0, 2) as $part) {
            $initials .= mb_strtoupper(mb_substr($part, 0, 1, 'UTF-8'), 'UTF-8');
        }

        return $initials !== '' ? $initials : 'K';
    }
}

// ── URLs & Daten ───────────────────────────────────────────────────────────────

if (!function_exists('kontor_post_link')) {
    function kontor_post_link(object|array $post): string
    {
        return kontor_safe_url(theme_post_url($post), kontor_url('/blog'));
    }
}

if (!function_exists('kontor_archive_url')) {
    function kontor_archive_url(string $type, string $slug = ''): string
    {
        try {
            if (function_exists('cms_get_archive_url')) {
                return (string) cms_get_archive_url($type, $slug);
            }
        } catch (\Throwable) {
        }

        return kontor_url('/' . ($type === 'category' ? 'kategorie' : 'tag') . ($slug !== '' ? '/' . rawurlencode($slug) : ''));
    }
}

if (!function_exists('kontor_search_url')) {
    function kontor_search_url(string $query = ''): string
    {
        return kontor_url('/search') . ($query !== '' ? '?q=' . rawurlencode($query) : '');
    }
}

if (!function_exists('kontor_get_posts')) {
    /**
     * @param array{limit?:int,offset?:int,exclude?:array<int,int>,category_id?:int} $args
     * @return array<int, object>
     */
    function kontor_get_posts(array $args = []): array
    {
        $limit = max(1, min(30, (int) ($args['limit'] ?? 3)));
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
                $where[] = '(p.category_id = ? OR EXISTS (SELECT 1 FROM ' . $prefix . 'post_category_rel pcr WHERE pcr.post_id = p.id AND pcr.category_id = ?))';
                $params[] = $categoryId;
                $params[] = $categoryId;
            }

            $rows = $db->get_results(
                "SELECT p.id, p.title, p.slug, p.slug_en, p.excerpt, p.meta_description, p.featured_image, p.published_at,
                        p.created_at, p.category_id, p.content, c.name AS category_name, c.slug AS category_slug,
                        COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Redaktion') AS author_name
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
                 LEFT JOIN {$prefix}users u ON u.id = p.author_id
                 WHERE " . implode(' AND ', $where) . "
                 ORDER BY COALESCE(p.published_at, p.created_at) DESC
                 LIMIT {$limit} OFFSET {$offset}",
                $params
            );

            return is_array($rows) ? $rows : [];
        } catch (\Throwable) {
            return [];
        }
    }
}

if (!function_exists('kontor_get_categories')) {
    /** @return array<int, array{id:int,name:string,slug:string,count:int,url:string}> */
    function kontor_get_categories(): array
    {
        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $rows = $db->get_results(
                "SELECT c.id, c.name, c.slug,
                        (SELECT COUNT(*) FROM {$prefix}posts p WHERE p.category_id = c.id AND " . cms_post_publication_where('p') . ") AS post_count
                 FROM {$prefix}post_categories c
                 ORDER BY c.sort_order ASC, c.name ASC"
            ) ?: [];
        } catch (\Throwable) {
            return [];
        }

        $items = [];
        foreach ($rows as $row) {
            $slug = trim((string) ($row->slug ?? ''));
            if ((int) ($row->post_count ?? 0) > 0 && $slug !== '') {
                $items[] = ['id' => (int) $row->id, 'name' => trim((string) $row->name), 'slug' => $slug, 'count' => (int) $row->post_count, 'url' => kontor_archive_url('category', $slug)];
            }
        }

        return $items;
    }
}

if (!function_exists('kontor_post_tags')) {
    /** @return array<int, array{name:string,url:string}> */
    function kontor_post_tags(object|array $post): array
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
            $tags[] = ['name' => $name, 'url' => $slug !== '' ? kontor_archive_url('tag', $slug) : kontor_search_url($name)];
        }

        return $tags;
    }
}

// ── Komponenten ────────────────────────────────────────────────────────────────

if (!function_exists('kontor_nav_menu')) {
    /**
     * @param array<int, array{label:string,url:string}> $fallback
     */
    function kontor_nav_menu(string $location, string $class = '', array $fallback = []): void
    {
        try {
            $items = \CMS\ThemeManager::instance()->getMenu($location);
        } catch (\Throwable) {
            $items = [];
        }
        if (!is_array($items) || $items === []) {
            $items = $fallback;
        }
        if ($items === []) {
            return;
        }

        $current = rtrim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/');
        $sitePath = rtrim((string) (parse_url((string) SITE_URL, PHP_URL_PATH) ?? ''), '/');
        $siteHost = parse_url((string) SITE_URL, PHP_URL_HOST);

        echo '<ul' . ($class !== '' ? ' class="' . kontor_e($class) . '"' : '') . '>';
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? $item['title'] ?? ''));
            if ($label === '') {
                continue;
            }
            $rawUrl = (string) ($item['url'] ?? '#');
            $href = kontor_url($rawUrl);
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

            echo '<li' . ($isActive ? ' class="is-active"' : '') . '><a href="' . kontor_e($href) . '"' . $attrs . '>' . kontor_e($label) . '</a></li>';
        }
        echo '</ul>';
    }
}

if (!function_exists('kontor_default_menu')) {
    /** @return array<int, array{label:string,url:string}> */
    function kontor_default_menu(string $location): array
    {
        return match ($location) {
            'primary' => [
                ['label' => 'Start', 'url' => '/'],
                ['label' => 'Leistungen', 'url' => '/#leistungen'],
                ['label' => 'Über uns', 'url' => '/ueber-uns'],
                ['label' => 'Aktuelles', 'url' => '/blog'],
                ['label' => 'Kontakt', 'url' => '/kontakt'],
            ],
            'footer-nav' => [
                ['label' => 'Über uns', 'url' => '/ueber-uns'],
                ['label' => 'Aktuelles', 'url' => '/blog'],
                ['label' => 'Kontakt', 'url' => '/kontakt'],
                ['label' => 'Suche', 'url' => '/search'],
            ],
            'footer-legal' => [
                ['label' => 'Impressum', 'url' => '/impressum'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz'],
            ],
            default => [],
        };
    }
}

if (!function_exists('kontor_services')) {
    /** @return array<int, array{title:string,text:string,icon:string,url:string}> */
    function kontor_services(): array
    {
        $services = [];
        for ($i = 1; $i <= 6; $i++) {
            $title = kontor_text('kt_services', 'service_' . $i . '_title');
            if ($title === '') {
                continue;
            }
            $services[] = [
                'title' => $title,
                'text' => kontor_text('kt_services', 'service_' . $i . '_text'),
                'icon' => kontor_text('kt_services', 'service_' . $i . '_icon', 'compass'),
                'url' => kontor_safe_url(kontor_text('kt_services', 'service_' . $i . '_url'), ''),
            ];
        }

        return $services;
    }
}

if (!function_exists('kontor_icon')) {
    function kontor_icon(string $name, string $class = 'kt-icon'): string
    {
        $paths = [
            'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
            'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
            'check'     => '<path d="M5 12.5 9.5 17 19 7.5"/>',
            'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
            'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'pin'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'star'      => '<path d="m12 3 2.7 5.6 6.1.8-4.5 4.2 1.1 6-5.4-2.9-5.4 2.9 1.1-6L3.2 9.4l6.1-.8z"/>',
            'quote'     => '<path d="M9 7H5v6h4v-2a4 4 0 0 1-4 4M19 7h-4v6h4v-2a4 4 0 0 1-4 4"/>',
            'plus'      => '<path d="M12 5v14M5 12h14"/>',
            'compass'   => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5-5 2 2-5z"/>',
            'chart'     => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'shield'    => '<path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6z"/><path d="m9 12 2 2 4-4"/>',
            'cloud'     => '<path d="M7 18h10a4 4 0 0 0 .6-8 6 6 0 0 0-11.5 1.5A3.3 3.3 0 0 0 7 18z"/>',
            'people'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14a6.5 6.5 0 0 1 3.5 6"/>',
            'gear'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.6 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.6-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
            'handshake' => '<path d="m11 17 2 2a1.4 1.4 0 0 0 2-2l-3-3M14 16l1 1a1.4 1.4 0 0 0 2-2l-3.5-3.5M8.5 13.5 11 16a1.4 1.4 0 0 1-2 2l-2.5-2.5M2 11l4-4 3 1 3-2 3 1 4-1 3 5-3 3M2 11l3.5 3.5"/>',
            'document'  => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
            'leaf'      => '<path d="M5 19c0-8 5-13 15-14-1 10-6 15-14 15"/><path d="M5 19c3-4 6-6 9-7"/>',
            'bolt'      => '<path d="M13 3 5 13.5h6L10 21l8-10.5h-6z"/>',
            'euro'      => '<path d="M17.5 6.5A7 7 0 1 0 17.5 17.5M4 10h9M4 14h9"/>',
            'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
            'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
            'xing'      => '<path d="m6 7 2.5 4.5L6 16M17 4l-5.5 9.5L15 20"/>',
            'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5v.01"/>',
            'up'        => '<path d="M12 19V5M6 11l6-6 6 6"/>',
        ];
        $body = $paths[$name] ?? $paths['compass'];

        return '<svg class="' . kontor_e($class) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $body . '</svg>';
    }
}

if (!function_exists('kontor_is_home_request')) {
    function kontor_is_home_request(): bool
    {
        $current = rtrim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/');
        $base = rtrim((string) (parse_url((string) SITE_URL, PHP_URL_PATH) ?? ''), '/');

        return $current === $base;
    }
}

if (!function_exists('kontor_button')) {
    function kontor_button(string $label, string $url, string $variant = 'primary', bool $arrow = false): string
    {
        $safe = kontor_safe_url($url, '');
        if ($label === '' || $safe === '') {
            return '';
        }
        $href = kontor_url($safe, kontor_is_home_request());
        $external = preg_match('#^https?://#i', $href) === 1 && parse_url($href, PHP_URL_HOST) !== parse_url((string) SITE_URL, PHP_URL_HOST);

        return '<a class="kt-button kt-button--' . kontor_e($variant) . '" href="' . kontor_e($href) . '"' . ($external ? ' target="_blank" rel="noopener noreferrer"' : '') . '>'
            . '<span>' . kontor_e($label) . '</span>' . ($arrow ? kontor_icon('arrow') : '') . '</a>';
    }
}

if (!function_exists('kontor_pagination')) {
    function kontor_pagination(int $current, int $total, string $param = 'p'): void
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
        for ($i = max(1, $current - 1); $i <= min($total, $current + 1); $i++) {
            $pages[] = $i;
        }
        $pages = array_values(array_unique($pages));
        sort($pages);

        echo '<nav class="kt-pagination" aria-label="Seitennavigation">';
        if ($current > 1) {
            echo '<a class="kt-pagination__step" href="' . kontor_e($build($current - 1)) . '" rel="prev">' . kontor_icon('arrow-left') . '<span>Zurück</span></a>';
        }
        echo '<ol class="kt-pagination__pages">';
        $last = 0;
        foreach ($pages as $number) {
            if ($last > 0 && $number - $last > 1) {
                echo '<li class="kt-pagination__gap" aria-hidden="true">…</li>';
            }
            echo $number === $current
                ? '<li><span aria-current="page">' . $number . '</span></li>'
                : '<li><a href="' . kontor_e($build($number)) . '">' . $number . '</a></li>';
            $last = $number;
        }
        echo '</ol>';
        if ($current < $total) {
            echo '<a class="kt-pagination__step" href="' . kontor_e($build($current + 1)) . '" rel="next"><span>Weiter</span>' . kontor_icon('arrow') . '</a>';
        }
        echo '</nav>';
    }
}

if (!function_exists('kontor_search_form')) {
    function kontor_search_form(string $id, string $value = ''): void
    {
        ?>
        <form role="search" method="get" action="<?php echo kontor_e(kontor_search_url()); ?>" class="kt-searchform">
            <label for="<?php echo kontor_e($id); ?>" class="kt-visually-hidden">Suchbegriff</label>
            <input id="<?php echo kontor_e($id); ?>" type="search" name="q" value="<?php echo kontor_e($value); ?>" placeholder="Website durchsuchen …" maxlength="120" autocomplete="off">
            <button type="submit" class="kt-button kt-button--primary"><?php echo kontor_icon('search'); ?><span>Suchen</span></button>
        </form>
        <?php
    }
}

if (!function_exists('kontor_post_card')) {
    function kontor_post_card(object $post, string $headingTag = 'h3'): void
    {
        $url = kontor_post_link($post);
        $image = kontor_media_url($post->featured_image ?? '');
        $date = (string) ($post->published_at ?? $post->created_at ?? '');
        $category = trim((string) ($post->category_name ?? ''));
        $excerpt = kontor_excerpt($post, 150);
        $headingTag = in_array($headingTag, ['h2', 'h3'], true) ? $headingTag : 'h3';
        ?>
        <article class="kt-postcard">
            <a class="kt-postcard__media<?php echo $image === '' ? ' kt-postcard__media--empty' : ''; ?>" href="<?php echo kontor_e($url); ?>" tabindex="-1" aria-hidden="true">
                <?php if ($image !== '') : ?>
                    <img src="<?php echo kontor_e($image); ?>" alt="" width="640" height="400" loading="lazy" decoding="async">
                <?php else : ?>
                    <?php echo kontor_icon('document', 'kt-postcard__placeholder'); ?>
                <?php endif; ?>
            </a>
            <div class="kt-postcard__body">
                <p class="kt-postcard__meta">
                    <?php if ($category !== '') : ?><span class="kt-tag"><?php echo kontor_e($category); ?></span><?php endif; ?>
                    <time datetime="<?php echo kontor_e(kontor_format_date($date, 'iso')); ?>"><?php echo kontor_e(kontor_format_date($date)); ?></time>
                </p>
                <<?php echo $headingTag; ?> class="kt-postcard__title"><a href="<?php echo kontor_e($url); ?>"><?php echo kontor_e((string) ($post->title ?? '')); ?></a></<?php echo $headingTag; ?>>
                <?php if ($excerpt !== '') : ?>
                    <p class="kt-postcard__excerpt"><?php echo kontor_e($excerpt); ?></p>
                <?php endif; ?>
                <span class="kt-postcard__more" aria-hidden="true">Weiterlesen<?php echo kontor_icon('arrow'); ?></span>
            </div>
        </article>
        <?php
    }
}

if (!function_exists('kontor_contact_card')) {
    /** Kontaktkarte für Seitenleisten (Seiten, Beiträge, Suche). */
    function kontor_contact_card(): void
    {
        $contact = kontor_contact();
        $label = kontor_text('header', 'header_cta_label', 'Beratung anfragen');
        $url = kontor_text('header', 'header_cta_url', '/kontakt');
        ?>
        <aside class="kt-contactcard" aria-labelledby="kt-contactcard-title">
            <p class="kt-eyebrow">Persönlich für Sie da</p>
            <h2 class="kt-contactcard__title" id="kt-contactcard-title">Sie haben Fragen?</h2>
            <p>Wir beraten Sie gern – telefonisch, per E-Mail oder bei uns vor Ort.</p>
            <ul class="kt-contactlist">
                <?php if ($contact['phone_href'] !== '') : ?>
                    <li><?php echo kontor_icon('phone'); ?><a href="<?php echo kontor_e($contact['phone_href']); ?>"><?php echo kontor_e($contact['phone']); ?></a></li>
                <?php endif; ?>
                <?php if ($contact['email_href'] !== '') : ?>
                    <li><?php echo kontor_icon('mail'); ?><a href="<?php echo kontor_e($contact['email_href']); ?>"><?php echo kontor_e($contact['email']); ?></a></li>
                <?php endif; ?>
                <?php if ($contact['hours'] !== []) : ?>
                    <li><?php echo kontor_icon('clock'); ?><span><?php echo implode('<br>', array_map('kontor_e', $contact['hours'])); ?></span></li>
                <?php endif; ?>
            </ul>
            <?php echo kontor_button($label, $url, 'accent', true); ?>
        </aside>
        <?php
    }
}

if (!function_exists('kontor_page_title')) {
    function kontor_page_title(array $context): string
    {
        $site = kontor_site_title();
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
            $label = 'Aktuelles';
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
