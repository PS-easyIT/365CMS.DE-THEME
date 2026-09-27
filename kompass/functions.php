<?php
declare(strict_types=1);

/**
 * Kompass – Informations-Theme für 365CMS
 *
 * Barrierearmes Informations- und Service-Portal: Suche im Mittelpunkt, Themenkacheln,
 * Schnellzugriffe, Hinweisbanner, Inhaltsverzeichnis, Themen A–Z, Schriftgrößen- und
 * Kontrastumschaltung.
 *
 * @package Kompass_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('KOMPASS_THEME_VERSION')) {
    define('KOMPASS_THEME_VERSION', '1.0.0');
}

final class Kompass_Theme
{
    public const SLUG = 'kompass';

    /**
     * Schriftwahl → [CSS-Stack, Google-Fonts-Familie (leer = keine Webfont), Font-Manager-Slug].
     *
     * @var array<string, array{0:string,1:string,2:string}>
     */
    private const FONTS = [
        'public-sans' => ['"Public Sans", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Public+Sans:ital,wght@0,400..800;1,400', 'public-sans'],
        'atkinson'    => ['"Atkinson Hyperlegible", "Public Sans", system-ui, -apple-system, "Segoe UI", sans-serif', 'Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700', 'atkinson-hyperlegible'],
        'source-sans' => ['"Source Sans 3", system-ui, -apple-system, "Segoe UI", sans-serif', 'Source+Sans+3:ital,wght@0,400..700;1,400', 'source-sans'],
        'inter'       => ['"Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Inter:wght@400;500;600;700', 'inter'],
        'system'      => ['system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', '', ''],
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
        \CMS\Hooks::addAction('head', [$this, 'outputPreferenceBootstrap'], 2);
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
        $href = $this->themeUrl() . '/style.css?v=' . rawurlencode(KOMPASS_THEME_VERSION);
        echo '<link rel="stylesheet" href="' . kompass_e($href) . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = $this->themeUrl() . '/js/theme.js?v=' . rawurlencode(KOMPASS_THEME_VERSION);
        echo '<script src="' . kompass_e($src) . '" defer></script>' . "\n";
    }

    /**
     * Markiert das Dokument als JS-fähig und setzt gespeicherte Schriftgröße/Kontrast vor dem
     * ersten Rendern (kein Springen der Seite). Inline-Script mit CSP-Nonce, ohne DOM-Sinks.
     */
    public function outputPreferenceBootstrap(): void
    {
        $bar = kompass_flag('header', 'show_a11y_bar', true);
        $size = $bar && kompass_flag('header', 'show_textsize', true);
        $contrast = $bar && kompass_flag('header', 'show_contrast', true);

        $js = 'var d=document.documentElement;d.classList.add("kp-js");';
        if ($size || $contrast) {
            $js .= 'try{';
            if ($size) {
                $js .= 'var s=localStorage.getItem("kompass-fontsize");if(s==="large"||s==="xlarge"){d.setAttribute("data-fontsize",s);}';
            }
            if ($contrast) {
                $js .= 'if(localStorage.getItem("kompass-contrast")==="high"){d.setAttribute("data-contrast","high");}';
            }
            $js .= '}catch(e){}';
        }

        echo '<script' . theme_csp_nonce_attr() . '>(function(){' . $js . '})();</script>' . "\n";
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
        echo '<link rel="stylesheet" href="' . kompass_e($url) . '">' . "\n";
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
            $description = kompass_site_description();
            if ($description !== '') {
                $html = '<meta name="description" content="' . kompass_e($description) . '">' . "\n";
            }
        }

        echo $html;
    }

    public function outputMetaTags(): void
    {
        $brand = kompass_css_color((string) kompass_setting('colors', 'brand_color', '#1a4d8f'), '#1a4d8f');
        echo '<meta name="theme-color" content="' . kompass_e($brand) . '">' . "\n";
    }

    public function outputCustomStyles(): void
    {
        $c = static fn(string $key, string $default): string => kompass_css_color((string) kompass_setting('colors', $key, $default), $default);
        $n = static fn(string $group, string $key, float $default, float $min, float $max): string => kompass_css_number(kompass_setting($group, $key, $default), $default, $min, $max);

        $vars = [
            '--kp-brand'       => $c('brand_color', '#1a4d8f'),
            '--kp-brand-dark'  => $c('brand_dark', '#10345f'),
            '--kp-accent'      => $c('accent_color', '#ffcc33'),
            '--kp-text'        => $c('text_color', '#1b2430'),
            '--kp-muted'       => $c('muted_color', '#4b5868'),
            '--kp-paper'       => $c('paper_color', '#ffffff'),
            '--kp-surface'     => $c('surface_color', '#f2f5f9'),
            '--kp-border'      => $c('border_color', '#d3dbe6'),
            '--kp-font-head'   => $this->fontStack((string) kompass_setting('typography', 'font_heading', 'public-sans'), 'public-sans'),
            '--kp-font-body'   => $this->fontStack((string) kompass_setting('typography', 'font_body', 'atkinson'), 'atkinson'),
            // Basisgröße relativ zur Browser-Einstellung (18 px ≙ 112,5 %), damit Nutzer-Zoom greift.
            '--kp-root-size'   => kompass_css_number((float) $n('typography', 'font_size_base', 18, 16, 21) / 16 * 100, 112.5, 100, 131.25) . '%',
            '--kp-container'   => $n('layout', 'container_width', 1200, 1040, 1400) . 'px',
            '--kp-radius'      => $n('layout', 'border_radius', 8, 0, 20) . 'px',
            // Core-Variablen (editorjs-content.css) nach dem Core-Customizer-Block erneut an die Palette binden.
            '--primary-color'  => 'var(--kp-brand)',
            '--accent'         => 'var(--kp-brand)',
            '--border-color'   => 'var(--kp-border)',
            '--bg-primary'     => 'var(--kp-paper)',
            '--bg-secondary'   => 'var(--kp-surface)',
            '--text-primary'   => 'var(--kp-text)',
            '--text-secondary' => 'var(--kp-muted)',
        ];

        $css = ':root{';
        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }
        $css .= '}';
        $css .= kompass_sanitize_custom_css((string) kompass_setting('advanced', 'custom_css', ''));

        echo '<style id="kompass-custom-vars"' . theme_csp_nonce_attr() . '>' . $css . '</style>' . "\n";
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
            'primary'      => 'Hauptnavigation',
            'service-nav'  => 'Service-Links (Kopfzeile)',
            'footer-nav'   => 'Footer: Service',
            'footer-legal' => 'Rechtliche Links (Footer)',
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
        foreach ([['font_heading', 'public-sans'], ['font_body', 'atkinson']] as [$setting, $default]) {
            $choice = (string) kompass_setting('typography', $setting, $default);
            $keys[] = isset(self::FONTS[$choice]) ? $choice : $default;
        }

        return array_values(array_unique($keys));
    }
}

Kompass_Theme::instance();

// ── Einstellungen & Ausgabe-Helfer ─────────────────────────────────────────────

if (!function_exists('kompass_setting')) {
    function kompass_setting(string $group, string $key, mixed $default = null): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($group, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('kompass_flag')) {
    function kompass_flag(string $group, string $key, bool $default): bool
    {
        $value = kompass_setting($group, $key, $default);
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}

if (!function_exists('kompass_text')) {
    function kompass_text(string $group, string $key, string $default = ''): string
    {
        $value = kompass_setting($group, $key, $default);

        return is_scalar($value) ? trim((string) $value) : $default;
    }
}

if (!function_exists('kompass_int')) {
    function kompass_int(string $group, string $key, int $default, int $min, int $max): int
    {
        $value = kompass_setting($group, $key, $default);
        $number = is_numeric($value) ? (int) round((float) $value) : $default;

        return max($min, min($max, $number));
    }
}

if (!function_exists('kompass_e')) {
    function kompass_e(mixed $value): string
    {
        return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('kompass_lines')) {
    /** @return array<int, string> */
    function kompass_lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', $value) ?: []), static fn(string $line): bool => $line !== ''));
    }
}

if (!function_exists('kompass_css_color')) {
    function kompass_css_color(string $value, string $fallback): string
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

if (!function_exists('kompass_css_number')) {
    function kompass_css_number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;
        $number = max($min, min($max, $number));

        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }
}

if (!function_exists('kompass_sanitize_custom_css')) {
    function kompass_sanitize_custom_css(string $raw): string
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

if (!function_exists('kompass_safe_url')) {
    /** Lässt nur sichere Ziele zu: relative Pfade, Anker, http(s), mailto: und tel:. */
    function kompass_safe_url(string $url, string $fallback = ''): string
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

if (!function_exists('kompass_url')) {
    function kompass_url(string $target = '/'): string
    {
        $base = rtrim((string) SITE_URL, '/');
        $safe = kompass_safe_url($target, '');
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

if (!function_exists('kompass_is_external')) {
    function kompass_is_external(string $url): bool
    {
        return preg_match('#^https?://#i', $url) === 1 && parse_url($url, PHP_URL_HOST) !== parse_url((string) SITE_URL, PHP_URL_HOST);
    }
}

if (!function_exists('kompass_media_url')) {
    function kompass_media_url(mixed $reference): string
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

        $safe = kompass_safe_url($url, '');
        if ($safe === '' || str_starts_with($safe, '#') || str_starts_with($safe, 'mailto:') || str_starts_with($safe, 'tel:')) {
            return '';
        }

        return str_starts_with($safe, '/') ? rtrim((string) SITE_URL, '/') . $safe : $safe;
    }
}

if (!function_exists('kompass_site_title')) {
    function kompass_site_title(): string
    {
        try {
            $title = trim((string) \CMS\ThemeManager::instance()->getSiteTitle());
        } catch (\Throwable) {
            $title = '';
        }

        return $title !== '' ? $title : (defined('SITE_NAME') ? (string) SITE_NAME : '365CMS');
    }
}

if (!function_exists('kompass_site_description')) {
    function kompass_site_description(): string
    {
        try {
            return trim((string) \CMS\ThemeManager::instance()->getSiteDescription());
        } catch (\Throwable) {
            return '';
        }
    }
}

if (!function_exists('kompass_organisation')) {
    function kompass_organisation(): string
    {
        $name = kompass_text('kp_service', 'organisation');

        return $name !== '' ? $name : kompass_site_title();
    }
}

if (!function_exists('kompass_request_locale')) {
    function kompass_request_locale(): string
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

if (!function_exists('kompass_is_home_request')) {
    function kompass_is_home_request(): bool
    {
        $current = rtrim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/');
        $base = rtrim((string) (parse_url((string) SITE_URL, PHP_URL_PATH) ?? ''), '/');

        return $current === $base;
    }
}

if (!function_exists('kompass_service')) {
    /** @return array{phone:string,phone_href:string,email:string,email_href:string,address:array<int,string>,hours:array<int,string>} */
    function kompass_service(): array
    {
        $phone = kompass_text('kp_service', 'phone');
        $email = kompass_text('kp_service', 'email');
        $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

        return [
            'phone' => $phone,
            'phone_href' => $phone !== '' ? kompass_safe_url('tel:' . preg_replace('/[^0-9+]/', '', $phone), '') : '',
            'email' => $emailValid ? $email : '',
            'email_href' => $emailValid ? 'mailto:' . $email : '',
            'address' => kompass_lines(kompass_text('kp_service', 'address')),
            'hours' => kompass_lines(kompass_text('kp_service', 'hours')),
        ];
    }
}

// ── Datum & Text ───────────────────────────────────────────────────────────────

if (!function_exists('kompass_format_date')) {
    function kompass_format_date(mixed $value, string $format = 'long'): string
    {
        $ts = is_string($value) && trim($value) !== '' ? strtotime($value) : false;
        if ($ts === false) {
            return '';
        }

        $months = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];

        return match ($format) {
            'short' => date('d.m.Y', $ts),
            'iso'   => date('c', $ts),
            'day'   => date('d', $ts),
            'month' => mb_substr($months[(int) date('n', $ts) - 1], 0, 3, 'UTF-8'),
            default => date('j', $ts) . '. ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts),
        };
    }
}

if (!function_exists('kompass_plain_text')) {
    /** Klartext aus Editor.js-JSON (Rohdaten in Listen) oder gerendertem HTML. */
    function kompass_plain_text(string $content): string
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

if (!function_exists('kompass_reading_time')) {
    function kompass_reading_time(string $content): int
    {
        $text = kompass_plain_text($content);

        return $text === '' ? 1 : max(1, (int) ceil(count(preg_split('/\s+/u', $text) ?: []) / 200));
    }
}

if (!function_exists('kompass_excerpt')) {
    function kompass_excerpt(object|array $post, int $length = 200, bool $allowContent = true): string
    {
        $data = is_array($post) ? $post : get_object_vars($post);
        $text = trim(strip_tags((string) ($data['excerpt'] ?? '')));
        if ($text === '') {
            $text = trim(strip_tags((string) ($data['meta_description'] ?? '')));
        }
        if ($text === '' && $allowContent) {
            $text = kompass_plain_text((string) ($data['content'] ?? ''));
        }
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen($text, 'UTF-8') > $length) {
            $text = rtrim(mb_substr($text, 0, $length, 'UTF-8'));
            $text = (preg_replace('/\s+\S*$/u', '', $text) ?? $text) . ' …';
        }

        return $text;
    }
}

if (!function_exists('kompass_slugify')) {
    function kompass_slugify(string $text): string
    {
        $text = strtr(mb_strtolower(trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8'), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $text = trim((string) (preg_replace('/[^a-z0-9]+/', '-', $text) ?? ''), '-');

        return $text !== '' ? $text : 'abschnitt';
    }
}

if (!function_exists('kompass_toc')) {
    /**
     * Ergänzt H2/H3 im gerenderten Inhalt um Sprungmarken und liefert das Inhaltsverzeichnis.
     * Vorhandene IDs (z. B. vom Core-Inhaltsverzeichnis) werden übernommen.
     *
     * @return array{html:string, items:array<int, array{id:string,text:string,level:int}>}
     */
    function kompass_toc(string $html, int $minHeadings = 3): array
    {
        if (trim($html) === '' || stripos($html, '<h2') === false) {
            return ['html' => $html, 'items' => []];
        }

        $items = [];
        $used = [];
        preg_match_all('/\bid\s*=\s*["\']([^"\']+)["\']/i', $html, $existing);
        foreach ($existing[1] ?? [] as $id) {
            $used[$id] = true;
        }

        $result = preg_replace_callback('/<(h[23])\b([^>]*)>(.*?)<\/\1>/is', static function (array $match) use (&$items, &$used): string {
            $tag = strtolower($match[1]);
            $attrs = $match[2];
            $inner = $match[3];
            $text = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            if ($text === '') {
                return $match[0];
            }

            if (preg_match('/\bid\s*=\s*["\']([^"\']+)["\']/i', $attrs, $idMatch) === 1) {
                $id = $idMatch[1];
            } else {
                $base = kompass_slugify($text);
                $id = $base;
                $i = 2;
                while (isset($used[$id])) {
                    $id = $base . '-' . $i++;
                }
                $used[$id] = true;
                $attrs = ' id="' . kompass_e($id) . '"' . $attrs;
            }

            $items[] = ['id' => $id, 'text' => $text, 'level' => $tag === 'h2' ? 2 : 3];

            return '<' . $tag . $attrs . '>' . $inner . '</' . $tag . '>';
        }, $html);

        if (!is_string($result) || count(array_filter($items, static fn(array $item): bool => $item['level'] === 2)) === 0 || count($items) < $minHeadings) {
            return ['html' => $html, 'items' => []];
        }

        return ['html' => $result, 'items' => $items];
    }
}

// ── URLs & Daten ───────────────────────────────────────────────────────────────

if (!function_exists('kompass_post_link')) {
    function kompass_post_link(object|array $post): string
    {
        return kompass_safe_url(theme_post_url($post), kompass_url('/blog'));
    }
}

if (!function_exists('kompass_archive_url')) {
    function kompass_archive_url(string $type, string $slug = ''): string
    {
        try {
            if (function_exists('cms_get_archive_url')) {
                return (string) cms_get_archive_url($type, $slug);
            }
        } catch (\Throwable) {
        }

        return kompass_url('/' . ($type === 'category' ? 'kategorie' : 'tag') . ($slug !== '' ? '/' . rawurlencode($slug) : ''));
    }
}

if (!function_exists('kompass_search_url')) {
    function kompass_search_url(string $query = ''): string
    {
        return kompass_url('/search') . ($query !== '' ? '?q=' . rawurlencode($query) : '');
    }
}

if (!function_exists('kompass_get_posts')) {
    /**
     * @param array{limit?:int,offset?:int,exclude?:array<int,int>,category_id?:int} $args
     * @return array<int, object>
     */
    function kompass_get_posts(array $args = []): array
    {
        $limit = max(1, min(30, (int) ($args['limit'] ?? 5)));
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
                "SELECT p.id, p.title, p.slug, p.slug_en, p.excerpt, p.meta_description, p.published_at, p.created_at,
                        p.updated_at, p.category_id, p.content, c.name AS category_name, c.slug AS category_slug
                 FROM {$prefix}posts p
                 LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
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

if (!function_exists('kompass_categories')) {
    /**
     * Kategorien als Themenbaum: Hauptkategorien mit Unterkategorien und kumulierter Beitragszahl.
     *
     * @return array<int, array{id:int,name:string,slug:string,description:string,count:int,url:string,children:array<int, array{name:string,slug:string,url:string,count:int}>}>
     */
    function kompass_categories(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $rows = $db->get_results(
                "SELECT c.id, c.name, c.slug, c.description, c.parent_id,
                        (SELECT COUNT(*) FROM {$prefix}posts p
                          WHERE (p.category_id = c.id OR EXISTS (SELECT 1 FROM {$prefix}post_category_rel pcr WHERE pcr.post_id = p.id AND pcr.category_id = c.id))
                            AND " . cms_post_publication_where('p') . ") AS post_count
                 FROM {$prefix}post_categories c
                 ORDER BY c.sort_order ASC, c.name ASC"
            ) ?: [];
        } catch (\Throwable) {
            return $cache = [];
        }

        $byId = [];
        foreach ($rows as $row) {
            $slug = trim((string) ($row->slug ?? ''));
            if ($slug === '') {
                continue;
            }
            $byId[(int) $row->id] = [
                'id' => (int) $row->id,
                'name' => trim((string) ($row->name ?? '')),
                'slug' => $slug,
                'description' => trim((string) ($row->description ?? '')),
                'parent_id' => (int) ($row->parent_id ?? 0),
                'own' => (int) ($row->post_count ?? 0),
                'url' => kompass_archive_url('category', $slug),
            ];
        }

        $topics = [];
        foreach ($byId as $id => $item) {
            if ($item['parent_id'] > 0 && isset($byId[$item['parent_id']])) {
                continue;
            }
            $children = [];
            $total = $item['own'];
            foreach ($byId as $child) {
                if ($child['parent_id'] === $id) {
                    $total += $child['own'];
                    if ($child['own'] > 0) {
                        $children[] = ['name' => $child['name'], 'slug' => $child['slug'], 'url' => $child['url'], 'count' => $child['own']];
                    }
                }
            }
            if ($total <= 0) {
                continue;
            }
            $topics[] = [
                'id' => $id,
                'name' => $item['name'],
                'slug' => $item['slug'],
                'description' => $item['description'],
                'count' => $total,
                'url' => $item['url'],
                'children' => $children,
            ];
        }

        return $cache = $topics;
    }
}

if (!function_exists('kompass_topic_context')) {
    /**
     * Einordnung einer Kategorie in den Themenbaum: übergeordnetes Thema und Unterthemen.
     *
     * @return array{parent:?array{name:string,url:string}, children:array<int, array{name:string,slug:string,url:string,count:int}>}
     */
    function kompass_topic_context(string $slug): array
    {
        foreach (kompass_categories() as $topic) {
            if ($topic['slug'] === $slug) {
                return ['parent' => null, 'children' => $topic['children']];
            }
            foreach ($topic['children'] as $child) {
                if ($child['slug'] === $slug) {
                    return ['parent' => ['name' => $topic['name'], 'url' => $topic['url']], 'children' => []];
                }
            }
        }

        return ['parent' => null, 'children' => []];
    }
}

if (!function_exists('kompass_topic_icon')) {
    /** Stabiles Symbol je Thema (aus dem Slug abgeleitet). */
    function kompass_topic_icon(string $slug): string
    {
        $icons = ['document', 'people', 'home', 'money', 'shield', 'calendar', 'info', 'leaf', 'briefcase', 'health', 'education', 'globe'];

        return $icons[crc32(strtolower($slug)) % count($icons)];
    }
}

if (!function_exists('kompass_post_tags')) {
    /** @return array<int, array{name:string,url:string}> */
    function kompass_post_tags(object|array $post): array
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
                $slug = kompass_slugify($name);
            }
            $tags[] = ['name' => $name, 'url' => kompass_archive_url('tag', $slug)];
        }

        return $tags;
    }
}

if (!function_exists('kompass_quick_links')) {
    /** @return array<int, array{label:string,url:string,icon:string}> */
    function kompass_quick_links(): array
    {
        $links = [];
        for ($i = 1; $i <= 6; $i++) {
            $label = kompass_text('kp_quick', 'quick_' . $i . '_label');
            $url = kompass_safe_url(kompass_text('kp_quick', 'quick_' . $i . '_url'), '');
            if ($label !== '' && $url !== '') {
                $links[] = ['label' => $label, 'url' => kompass_url($url), 'icon' => kompass_text('kp_quick', 'quick_' . $i . '_icon', 'info')];
            }
        }

        return $links;
    }
}

if (!function_exists('kompass_faqs')) {
    /** @return array<int, array{q:string,a:string}> */
    function kompass_faqs(): array
    {
        $faqs = [];
        for ($i = 1; $i <= 6; $i++) {
            $question = kompass_text('kp_faq', 'faq_' . $i . '_q');
            $answer = kompass_text('kp_faq', 'faq_' . $i . '_a');
            if ($question !== '' && $answer !== '') {
                $faqs[] = ['q' => $question, 'a' => $answer];
            }
        }

        return $faqs;
    }
}

if (!function_exists('kompass_popular_terms')) {
    /** @return array<int, string> */
    function kompass_popular_terms(): array
    {
        return array_slice(array_values(array_filter(array_map('trim', explode(',', kompass_text('kp_hero', 'popular_terms'))))), 0, 8);
    }
}

if (!function_exists('kompass_alpha_groups')) {
    /**
     * Gruppiert Übersichtseinträge alphabetisch (Themen A–Z).
     *
     * @param array<int, array<string, mixed>> $items
     * @return array<string, array<int, array<string, mixed>>>
     */
    function kompass_alpha_groups(array $items): array
    {
        $groups = [];
        foreach ($items as $item) {
            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $first = strtr(mb_strtoupper(mb_substr($title, 0, 1, 'UTF-8'), 'UTF-8'), ['Ä' => 'A', 'Ö' => 'O', 'Ü' => 'U']);
            $letter = preg_match('/^[A-Z]$/', $first) === 1 ? $first : '#';
            $groups[$letter][] = $item;
        }
        ksort($groups);
        foreach ($groups as &$group) {
            usort($group, static fn(array $a, array $b): int => strnatcasecmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? '')));
        }
        unset($group);

        return $groups;
    }
}

// ── Komponenten ────────────────────────────────────────────────────────────────

if (!function_exists('kompass_nav_menu')) {
    /**
     * @param array<int, array{label:string,url:string}> $fallback
     */
    function kompass_nav_menu(string $location, string $class = '', array $fallback = []): void
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

        echo '<ul' . ($class !== '' ? ' class="' . kompass_e($class) . '"' : '') . '>';
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? $item['title'] ?? ''));
            if ($label === '') {
                continue;
            }
            $rawUrl = (string) ($item['url'] ?? '#');
            $href = kompass_url($rawUrl);
            $path = rtrim((string) (parse_url($href, PHP_URL_PATH) ?? ''), '/');
            if ($sitePath !== '' && str_starts_with($path, $sitePath)) {
                $path = rtrim(substr($path, strlen($sitePath)), '/');
            }
            $external = kompass_is_external($href);
            $isActive = !$external && !str_contains($rawUrl, '#')
                && ($path === '' ? $current === '' : ($current === $path || str_starts_with($current . '/', $path . '/')));

            $attrs = $isActive ? ' aria-current="page"' : '';
            if (($item['target'] ?? '') === '_blank' || $external) {
                $attrs .= ' target="_blank" rel="noopener noreferrer"';
            }

            echo '<li><a href="' . kompass_e($href) . '"' . $attrs . '>' . kompass_e($label) . ($external ? '<span class="kp-visually-hidden"> (externer Link)</span>' : '') . '</a></li>';
        }
        echo '</ul>';
    }
}

if (!function_exists('kompass_default_menu')) {
    /** @return array<int, array{label:string,url:string}> */
    function kompass_default_menu(string $location): array
    {
        return match ($location) {
            'primary' => [
                ['label' => 'Start', 'url' => '/'],
                ['label' => 'Themen', 'url' => kompass_archive_url('category')],
                ['label' => 'Aktuelles', 'url' => '/blog'],
                ['label' => 'Themen A–Z', 'url' => kompass_archive_url('tag')],
                ['label' => 'Kontakt', 'url' => '/kontakt'],
            ],
            'footer-nav' => [
                ['label' => 'Alle Themen', 'url' => kompass_archive_url('category')],
                ['label' => 'Themen A–Z', 'url' => kompass_archive_url('tag')],
                ['label' => 'Aktuelles', 'url' => '/blog'],
                ['label' => 'Suche', 'url' => '/search'],
                ['label' => 'Sitemap', 'url' => '/sitemap'],
            ],
            'footer-legal' => [
                ['label' => 'Impressum', 'url' => '/impressum'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz'],
            ],
            default => [],
        };
    }
}

if (!function_exists('kompass_icon')) {
    function kompass_icon(string $name, string $class = 'kp-icon'): string
    {
        $paths = [
            'compass'   => '<circle cx="12" cy="12" r="9.5"/><path d="m15.8 8.2-2.4 5.2-5.2 2.4 2.4-5.2z"/><path d="M12 2.5v2M12 19.5v2M2.5 12h2M19.5 12h2"/>',
            'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
            'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
            'chevron'   => '<path d="m9 6 6 6-6 6"/>',
            'up'        => '<path d="M12 19V5M6 11l6-6 6 6"/>',
            'print'     => '<path d="M7 9V4h10v5M7 17H5a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/><rect x="7" y="14" width="10" height="6"/>',
            'link'      => '<path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1.5 1.5"/><path d="M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1.5-1.5"/>',
            'contrast'  => '<circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor"/>',
            'textsize'  => '<path d="M3 19 8 5l5 14M4.8 14h6.4M14 19l3.5-9 3.5 9M15.2 16h4.6"/>',
            'easy'      => '<path d="M4 5h11a3 3 0 0 1 3 3v11H7a3 3 0 0 1-3-3z"/><path d="M8 9h6M8 13h6"/>',
            'sign'      => '<path d="M7 11V5.5a1.5 1.5 0 0 1 3 0V11M10 10V4.5a1.5 1.5 0 0 1 3 0V10M13 10V5.5a1.5 1.5 0 0 1 3 0V12M16 9.5a1.5 1.5 0 0 1 3 0V14a7 7 0 0 1-7 7h-.5a7 7 0 0 1-5.8-3.1L3.4 14a1.5 1.5 0 0 1 2.5-1.6L7 14"/>',
            'info'      => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.01"/>',
            'warning'   => '<path d="M12 3 2 20h20z"/><path d="M12 10v4M12 17v.01"/>',
            'success'   => '<circle cx="12" cy="12" r="9"/><path d="m8 12.5 2.5 2.5L16 9.5"/>',
            'help'      => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.6.3-1 .8-1 1.5v.7M12 17v.01"/>',
            'document'  => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
            'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
            'people'    => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14a6.5 6.5 0 0 1 3.5 6"/>',
            'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
            'pin'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
            'money'     => '<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.5"/><path d="M6 9.5v.01M18 14.5v.01"/>',
            'home'      => '<path d="m3 11 9-7 9 7"/><path d="M5 10v10h14V10M10 20v-6h4v6"/>',
            'shield'    => '<path d="M12 3 5 6v5c0 4.5 3 8.3 7 10 4-1.7 7-5.5 7-10V6z"/><path d="m9 12 2 2 4-4"/>',
            'download'  => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
            'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'leaf'      => '<path d="M5 19c0-8 5-13 15-14-1 10-6 15-14 15"/><path d="M5 19c3-4 6-6 9-7"/>',
            'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/>',
            'health'    => '<path d="M12 20s-7-4.4-9-9a4.8 4.8 0 0 1 9-3 4.8 4.8 0 0 1 9 3c-2 4.6-9 9-9 9z"/><path d="M12 10v4M10 12h4"/>',
            'education' => '<path d="m2 9 10-5 10 5-10 5z"/><path d="M6 11v5c3 2 9 2 12 0v-5M22 9v6"/>',
            'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
            'thumbs'    => '<path d="M7 11v9H4v-9zM7 11l4-8a2 2 0 0 1 2 2v4h5a2 2 0 0 1 2 2.3l-1.2 7A2 2 0 0 1 16.8 20H7"/>',
            'external'  => '<path d="M14 4h6v6M20 4 10 14M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
        ];
        $body = $paths[$name] ?? $paths['info'];

        return '<svg class="' . kompass_e($class) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $body . '</svg>';
    }
}

if (!function_exists('kompass_news_item')) {
    /** Listeneintrag „Aktuelle Informationen“ mit Datumsblock, Thema und Kurztext. */
    function kompass_news_item(object|array $post, string $headingTag = 'h3', bool $withExcerpt = true, bool $withImage = false): void
    {
        $item = is_array($post) ? (object) $post : $post;
        $title = trim((string) ($item->title ?? ''));
        if ($title === '') {
            return;
        }
        $headingTag = in_array($headingTag, ['h2', 'h3', 'h4'], true) ? $headingTag : 'h3';
        $date = (string) ($item->published_at ?? $item->created_at ?? '');
        $category = trim((string) ($item->category_name ?? ''));
        $categorySlug = trim((string) ($item->category_slug ?? ''));
        $excerpt = $withExcerpt ? kompass_excerpt($item, 190) : '';
        $image = $withImage ? kompass_media_url($item->featured_image ?? '') : '';
        ?>
        <li class="kp-newsitem<?php echo $image !== '' ? ' kp-newsitem--media' : ''; ?>">
            <?php if (kompass_format_date($date) !== '') : ?>
                <time class="kp-newsitem__date" datetime="<?php echo kompass_e(kompass_format_date($date, 'iso')); ?>">
                    <span class="kp-newsitem__day" aria-hidden="true"><?php echo kompass_e(kompass_format_date($date, 'day')); ?></span>
                    <span class="kp-newsitem__month" aria-hidden="true"><?php echo kompass_e(kompass_format_date($date, 'month')); ?></span>
                    <span class="kp-visually-hidden"><?php echo kompass_e(kompass_format_date($date)); ?></span>
                </time>
            <?php endif; ?>
            <div class="kp-newsitem__body">
                <?php if ($category !== '' && $categorySlug !== '') : ?>
                    <p class="kp-newsitem__topic"><a href="<?php echo kompass_e(kompass_archive_url('category', $categorySlug)); ?>"><?php echo kompass_e($category); ?></a></p>
                <?php endif; ?>
                <<?php echo $headingTag; ?> class="kp-newsitem__title"><a href="<?php echo kompass_e(kompass_post_link($item)); ?>"><?php echo kompass_e($title); ?></a></<?php echo $headingTag; ?>>
                <?php if ($excerpt !== '') : ?>
                    <p class="kp-newsitem__excerpt"><?php echo kompass_e($excerpt); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($image !== '') : ?>
                <img class="kp-newsitem__image" src="<?php echo kompass_e($image); ?>" alt="" width="240" height="160" loading="lazy" decoding="async">
            <?php endif; ?>
        </li>
        <?php
    }
}

if (!function_exists('kompass_render_toc')) {
    /**
     * Inhaltsverzeichnis „Auf dieser Seite“ (H3 als Unterpunkte der vorangehenden H2).
     *
     * @param array<int, array{id:string,text:string,level:int}> $items
     */
    function kompass_render_toc(array $items): void
    {
        if ($items === []) {
            return;
        }

        echo '<nav class="kp-toc" aria-labelledby="kp-toc-title" data-kp-toc>';
        echo '<p class="kp-toc__title" id="kp-toc-title">Auf dieser Seite</p>';
        echo '<ol class="kp-toc__list">';
        $open = false;
        $subOpen = false;
        foreach ($items as $item) {
            $link = '<a href="#' . kompass_e($item['id']) . '">' . kompass_e($item['text']) . '</a>';
            if ($item['level'] === 3 && $open) {
                if (!$subOpen) {
                    echo '<ol class="kp-toc__sub">';
                    $subOpen = true;
                }
                echo '<li>' . $link . '</li>';
                continue;
            }
            if ($subOpen) {
                echo '</ol>';
                $subOpen = false;
            }
            if ($open) {
                echo '</li>';
            }
            echo '<li>' . $link;
            $open = true;
        }
        if ($subOpen) {
            echo '</ol>';
        }
        if ($open) {
            echo '</li>';
        }
        echo '</ol></nav>';
    }
}

if (!function_exists('kompass_pdf_available')) {
    /** true, wenn der Core Seiten als PDF ausliefern kann (?pdf=1, Dompdf vorhanden). */
    function kompass_pdf_available(): bool
    {
        try {
            return class_exists('\\CMS\\Services\\PdfService') && \CMS\Services\PdfService::getInstance()->isAvailable();
        } catch (\Throwable) {
            return false;
        }
    }
}

if (!function_exists('kompass_page_tools')) {
    /** Stand-Datum, Drucken, Link kopieren und (für Seiten) PDF-Download. */
    function kompass_page_tools(string $updated, bool $pdf = false): void
    {
        $date = kompass_format_date($updated);
        ?>
        <div class="kp-pagetools">
            <?php if ($date !== '') : ?>
                <p class="kp-pagetools__stand">Stand: <time datetime="<?php echo kompass_e(kompass_format_date($updated, 'iso')); ?>"><?php echo kompass_e($date); ?></time></p>
            <?php endif; ?>
            <ul class="kp-pagetools__list">
                <li class="kp-js-only"><button type="button" class="kp-textbutton" data-kp-print><?php echo kompass_icon('print'); ?><span>Drucken</span></button></li>
                <li class="kp-js-only"><button type="button" class="kp-textbutton" data-kp-copy><?php echo kompass_icon('link'); ?><span>Link kopieren</span></button></li>
                <?php if ($pdf && kompass_pdf_available()) : ?>
                    <li><a class="kp-textbutton" href="?pdf=1" rel="nofollow"><?php echo kompass_icon('download'); ?><span>Als PDF herunterladen</span></a></li>
                <?php endif; ?>
            </ul>
            <p class="kp-pagetools__status" data-kp-copy-status role="status"></p>
        </div>
        <?php
    }
}

if (!function_exists('kompass_pagination')) {
    function kompass_pagination(int $current, int $total, string $param = 'p'): void
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

        echo '<nav class="kp-pagination" aria-label="Seitennavigation">';
        if ($current > 1) {
            echo '<a class="kp-pagination__step" href="' . kompass_e($build($current - 1)) . '" rel="prev">' . kompass_icon('arrow-left') . '<span>Vorherige Seite</span></a>';
        }
        echo '<ol class="kp-pagination__pages">';
        $last = 0;
        foreach ($pages as $number) {
            if ($last > 0 && $number - $last > 1) {
                echo '<li class="kp-pagination__gap" aria-hidden="true">…</li>';
            }
            echo $number === $current
                ? '<li><span aria-current="page"><span class="kp-visually-hidden">Seite </span>' . $number . '</span></li>'
                : '<li><a href="' . kompass_e($build($number)) . '"><span class="kp-visually-hidden">Seite </span>' . $number . '</a></li>';
            $last = $number;
        }
        echo '</ol>';
        if ($current < $total) {
            echo '<a class="kp-pagination__step" href="' . kompass_e($build($current + 1)) . '" rel="next"><span>Nächste Seite</span>' . kompass_icon('arrow') . '</a>';
        }
        echo '</nav>';
    }
}

if (!function_exists('kompass_search_form')) {
    function kompass_search_form(string $id, string $value = '', string $variant = ''): void
    {
        ?>
        <form role="search" method="get" action="<?php echo kompass_e(kompass_search_url()); ?>" class="kp-searchform<?php echo $variant !== '' ? ' kp-searchform--' . kompass_e($variant) : ''; ?>">
            <label for="<?php echo kompass_e($id); ?>" class="<?php echo $variant === 'hero' ? 'kp-searchform__label' : 'kp-visually-hidden'; ?>">Suchbegriff eingeben</label>
            <div class="kp-searchform__row">
                <input id="<?php echo kompass_e($id); ?>" type="search" name="q" value="<?php echo kompass_e($value); ?>" placeholder="z. B. Antrag, Öffnungszeiten, Formular" maxlength="120" autocomplete="off">
                <button type="submit" class="kp-button kp-button--primary"><?php echo kompass_icon('search'); ?><span>Suchen</span></button>
            </div>
        </form>
        <?php
    }
}

if (!function_exists('kompass_notice')) {
    /** Hinweisbanner aus dem Customizer. */
    function kompass_notice(): void
    {
        if (!kompass_flag('kp_notice', 'show_notice', false)) {
            return;
        }
        if (kompass_flag('kp_notice', 'notice_home_only', false) && !kompass_is_home_request()) {
            return;
        }

        $type = kompass_text('kp_notice', 'notice_type', 'info');
        $type = in_array($type, ['info', 'warning', 'success'], true) ? $type : 'info';
        $title = kompass_text('kp_notice', 'notice_title');
        $text = kompass_text('kp_notice', 'notice_text');
        if ($title === '' && $text === '') {
            return;
        }
        $linkLabel = kompass_text('kp_notice', 'notice_link_label');
        $linkUrl = kompass_safe_url(kompass_text('kp_notice', 'notice_link_url'), '');
        ?>
        <aside class="kp-notice kp-notice--<?php echo kompass_e($type); ?>" aria-label="Hinweis">
            <div class="kp-container kp-notice__inner">
                <?php echo kompass_icon($type, 'kp-notice__icon'); ?>
                <p class="kp-notice__text">
                    <?php if ($title !== '') : ?><strong><?php echo kompass_e($title); ?>:</strong> <?php endif; ?>
                    <?php echo kompass_e($text); ?>
                    <?php if ($linkLabel !== '' && $linkUrl !== '') : ?>
                        <a href="<?php echo kompass_e(kompass_url($linkUrl)); ?>"><?php echo kompass_e($linkLabel); ?></a>
                    <?php endif; ?>
                </p>
            </div>
        </aside>
        <?php
    }
}

if (!function_exists('kompass_service_box')) {
    function kompass_service_box(string $headingTag = 'h2'): void
    {
        $service = kompass_service();
        $ctaLabel = kompass_text('kp_service', 'cta_label');
        $ctaUrl = kompass_safe_url(kompass_text('kp_service', 'cta_url'), '');
        $headingTag = in_array($headingTag, ['h2', 'h3'], true) ? $headingTag : 'h2';
        ?>
        <section class="kp-service" aria-labelledby="kp-service-title">
            <div class="kp-service__intro">
                <<?php echo $headingTag; ?> class="kp-service__title" id="kp-service-title"><?php echo kompass_e(kompass_text('kp_service', 'service_heading', 'Service & Kontakt')); ?></<?php echo $headingTag; ?>>
                <?php if (kompass_text('kp_service', 'service_text') !== '') : ?>
                    <p><?php echo kompass_e(kompass_text('kp_service', 'service_text')); ?></p>
                <?php endif; ?>
                <?php if ($ctaLabel !== '' && $ctaUrl !== '') : ?>
                    <a class="kp-button kp-button--accent" href="<?php echo kompass_e(kompass_url($ctaUrl)); ?>"><span><?php echo kompass_e($ctaLabel); ?></span><?php echo kompass_icon('arrow'); ?></a>
                <?php endif; ?>
            </div>
            <dl class="kp-service__facts">
                <?php if ($service['phone_href'] !== '') : ?>
                    <div><dt><?php echo kompass_icon('phone'); ?>Telefon</dt><dd><a href="<?php echo kompass_e($service['phone_href']); ?>"><?php echo kompass_e($service['phone']); ?></a></dd></div>
                <?php endif; ?>
                <?php if ($service['email_href'] !== '') : ?>
                    <div><dt><?php echo kompass_icon('mail'); ?>E-Mail</dt><dd><a href="<?php echo kompass_e($service['email_href']); ?>"><?php echo kompass_e($service['email']); ?></a></dd></div>
                <?php endif; ?>
                <?php if ($service['hours'] !== []) : ?>
                    <div><dt><?php echo kompass_icon('clock'); ?>Sprechzeiten</dt><dd><?php echo implode('<br>', array_map('kompass_e', $service['hours'])); ?></dd></div>
                <?php endif; ?>
                <?php if ($service['address'] !== []) : ?>
                    <div><dt><?php echo kompass_icon('pin'); ?>Anschrift</dt><dd><?php echo kompass_e(kompass_organisation()); ?><br><?php echo implode('<br>', array_map('kompass_e', $service['address'])); ?></dd></div>
                <?php endif; ?>
            </dl>
        </section>
        <?php
    }
}

if (!function_exists('kompass_help_card')) {
    /** Kompakte Kontaktkarte für Seitenleisten. */
    function kompass_help_card(): void
    {
        $service = kompass_service();
        if ($service['phone_href'] === '' && $service['email_href'] === '') {
            return;
        }
        $ctaLabel = kompass_text('kp_service', 'cta_label');
        $ctaUrl = kompass_safe_url(kompass_text('kp_service', 'cta_url'), '');
        ?>
        <section class="kp-helpcard" aria-labelledby="kp-helpcard-title">
            <h2 class="kp-helpcard__title" id="kp-helpcard-title"><?php echo kompass_icon('help'); ?>Fragen? Wir helfen weiter.</h2>
            <ul class="kp-helpcard__list">
                <?php if ($service['phone_href'] !== '') : ?>
                    <li><?php echo kompass_icon('phone'); ?><a href="<?php echo kompass_e($service['phone_href']); ?>"><?php echo kompass_e($service['phone']); ?></a></li>
                <?php endif; ?>
                <?php if ($service['email_href'] !== '') : ?>
                    <li><?php echo kompass_icon('mail'); ?><a href="<?php echo kompass_e($service['email_href']); ?>"><?php echo kompass_e($service['email']); ?></a></li>
                <?php endif; ?>
                <?php if ($service['hours'] !== []) : ?>
                    <li><?php echo kompass_icon('clock'); ?><span><span class="kp-visually-hidden">Sprechzeiten: </span><?php echo implode('<br>', array_map('kompass_e', $service['hours'])); ?></span></li>
                <?php endif; ?>
            </ul>
            <?php if ($ctaLabel !== '' && $ctaUrl !== '') : ?>
                <a class="kp-button kp-button--primary kp-button--block" href="<?php echo kompass_e(kompass_url($ctaUrl)); ?>"><span><?php echo kompass_e($ctaLabel); ?></span></a>
            <?php endif; ?>
        </section>
        <?php
    }
}

if (!function_exists('kompass_topic_nav')) {
    /** Themenliste für Seitenleisten (aktuelles Thema mit aria-current). */
    function kompass_topic_nav(string $activeSlug = ''): void
    {
        $topics = kompass_categories();
        if ($topics === []) {
            return;
        }
        ?>
        <nav class="kp-sidenav" aria-labelledby="kp-sidenav-title">
            <h2 class="kp-sidenav__title" id="kp-sidenav-title">Themenbereiche</h2>
            <ul class="kp-sidenav__list">
                <?php foreach ($topics as $topic) :
                    $active = $topic['slug'] === $activeSlug;
                    ?>
                    <li>
                        <a href="<?php echo kompass_e($topic['url']); ?>"<?php echo $active ? ' aria-current="page"' : ''; ?>><span><?php echo kompass_e($topic['name']); ?></span><span class="kp-sidenav__count"><?php echo (int) $topic['count']; ?><span class="kp-visually-hidden"> Beiträge</span></span></a>
                        <?php if ($topic['children'] !== []) : ?>
                            <ul>
                                <?php foreach ($topic['children'] as $child) : ?>
                                    <li><a href="<?php echo kompass_e($child['url']); ?>"<?php echo $child['slug'] === $activeSlug ? ' aria-current="page"' : ''; ?>><span><?php echo kompass_e($child['name']); ?></span><span class="kp-sidenav__count"><?php echo (int) $child['count']; ?><span class="kp-visually-hidden"> Beiträge</span></span></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    }
}

if (!function_exists('kompass_feedback')) {
    /** „War diese Seite hilfreich?“ – Rückmeldung per E-Mail an die Service-Adresse. */
    function kompass_feedback(string $title): void
    {
        $service = kompass_service();
        if (!kompass_flag('kp_service', 'feedback', true) || $service['email'] === '') {
            return;
        }
        $url = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $page = kompass_url(str_starts_with($url, '/') ? $url : '/');
        $yes = 'mailto:' . $service['email'] . '?subject=' . rawurlencode('Rückmeldung: hilfreich – ' . $title) . '&body=' . rawurlencode("Seite: {$page}\n\nDiese Seite war hilfreich.\n");
        $no = 'mailto:' . $service['email'] . '?subject=' . rawurlencode('Rückmeldung: nicht hilfreich – ' . $title) . '&body=' . rawurlencode("Seite: {$page}\n\nWas hat gefehlt oder war unklar?\n");
        ?>
        <aside class="kp-feedback" aria-labelledby="kp-feedback-title">
            <p class="kp-feedback__title" id="kp-feedback-title"><?php echo kompass_icon('thumbs'); ?>War diese Seite hilfreich?</p>
            <div class="kp-feedback__actions">
                <a class="kp-button kp-button--ghost" href="<?php echo kompass_e($yes); ?>">Ja</a>
                <a class="kp-button kp-button--ghost" href="<?php echo kompass_e($no); ?>">Nein, etwas fehlt</a>
            </div>
        </aside>
        <?php
    }
}

if (!function_exists('kompass_page_title')) {
    function kompass_page_title(array $context): string
    {
        $site = kompass_site_title();
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
            $label = !empty($context['isOverview']) ? 'Themenbereiche' : trim((string) ($context['category']['name'] ?? ''));
        } elseif (isset($context['tag']) && is_array($context['tag'])) {
            $label = !empty($context['isOverview']) ? 'Themen A–Z' : 'Schlagwort: ' . trim((string) ($context['tag']['name'] ?? ''));
        } elseif (array_key_exists('results', $context)) {
            $query = trim((string) ($context['query'] ?? ''));
            $label = $query !== '' ? 'Suche: ' . $query : 'Suche';
        } elseif (isset($context['posts'])) {
            $label = 'Aktuelle Informationen';
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
