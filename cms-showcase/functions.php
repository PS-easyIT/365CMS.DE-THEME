<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Produkt-Theme für 365CMS
 *
 * Präsentiert 365CMS selbst: Hero mit Dashboard-Vorschau, Funktionen, Rollen-Tabs,
 * Sicherheitskonzept, Entwickler-Bereich mit hervorgehobenem Code, automatische Galerie
 * der installierten Themes, Release Notes, Installation, FAQ und Dokumentationsseiten.
 *
 * @package Showcase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SHOWCASE_THEME_VERSION')) {
    define('SHOWCASE_THEME_VERSION', '1.0.0');
}

final class Showcase_Theme
{
    public const SLUG = 'cms-showcase';

    /**
     * Schriftwahl → [CSS-Stack, Google-Fonts-Familie (leer = keine Webfont), Font-Manager-Slug].
     *
     * @var array<string, array{0:string,1:string,2:string}>
     */
    private const FONTS = [
        'jakarta'       => ['"Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Plus+Jakarta+Sans:ital,wght@0,400..800;1,400', 'plus-jakarta-sans'],
        'sora'          => ['"Sora", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Sora:wght@400..800', 'sora'],
        'space-grotesk' => ['"Space Grotesk", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Space+Grotesk:wght@400..700', 'space-grotesk'],
        'inter'         => ['"Inter", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'Inter:ital,wght@0,400..700;1,400', 'inter'],
        'dm-sans'       => ['"DM Sans", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif', 'DM+Sans:ital,wght@0,400..700;1,400', 'dm-sans'],
        'system'        => ['system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', '', ''],
    ];

    /** @var array<string, array{0:string,1:string,2:string}> */
    private const MONO_FONTS = [
        'jetbrains' => ['"JetBrains Mono", ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace', 'JetBrains+Mono:wght@400;600', 'jetbrains-mono'],
        'fira-code' => ['"Fira Code", ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace', 'Fira+Code:wght@400;600', 'fira-code'],
        'system'    => ['ui-monospace, "SFMono-Regular", Menlo, Consolas, "Liberation Mono", monospace', '', ''],
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
        \CMS\Hooks::addAction('head', [$this, 'outputScriptFlag'], 2);
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
        $href = $this->themeUrl() . '/style.css?v=' . rawurlencode(SHOWCASE_THEME_VERSION);
        echo '<link rel="stylesheet" href="' . showcase_e($href) . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = $this->themeUrl() . '/js/theme.js?v=' . rawurlencode(SHOWCASE_THEME_VERSION);
        echo '<script src="' . showcase_e($src) . '" defer></script>' . "\n";
    }

    /** Markiert das Dokument als JS-fähig (progressive Verbesserung für Tabs, Menü und Einblenden). */
    public function outputScriptFlag(): void
    {
        $classes = 'sc-js' . (showcase_flag('layout', 'enable_reveal', true) ? ' sc-reveal-on' : '');
        echo '<script' . theme_csp_nonce_attr() . '>document.documentElement.className+=" ' . $classes . '";</script>' . "\n";
    }

    public function outputFonts(): void
    {
        if (theme_use_local_fonts()) {
            return; // Core bindet über local_font_slugs lokal gespeicherte Schriften ein (DSGVO).
        }

        $families = [];
        foreach ($this->selectedFonts() as [$stack, $family, $slug]) {
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
        echo '<link rel="stylesheet" href="' . showcase_e($url) . '">' . "\n";
    }

    /**
     * @param mixed $slugs
     * @return array<int, string>
     */
    public function localFontSlugs(mixed $slugs): array
    {
        $slugs = is_array($slugs) ? $slugs : [];
        foreach ($this->selectedFonts() as [$stack, $family, $slug]) {
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
            $description = showcase_site_description();
            if ($description !== '') {
                $html = '<meta name="description" content="' . showcase_e($description) . '">' . "\n";
            }
        }

        echo $html;
    }

    public function outputMetaTags(): void
    {
        $night = showcase_css_color((string) showcase_setting('colors', 'night_color', '#0b1120'), '#0b1120');
        echo '<meta name="theme-color" content="' . showcase_e($night) . '">' . "\n";
    }

    public function outputCustomStyles(): void
    {
        $c = static fn(string $key, string $default): string => showcase_css_color((string) showcase_setting('colors', $key, $default), $default);
        $n = static fn(string $group, string $key, float $default, float $min, float $max): string => showcase_css_number(showcase_setting($group, $key, $default), $default, $min, $max);
        [$head, $body, $mono] = $this->selectedFonts();

        $vars = [
            '--sc-night'         => $c('night_color', '#0b1120'),
            '--sc-primary'       => $c('primary_color', '#2563eb'),
            '--sc-primary-light' => $c('primary_light', '#60a5fa'),
            '--sc-grad-1'        => $c('gradient_1', '#8b5cf6'),
            '--sc-grad-2'        => $c('gradient_2', '#ec4899'),
            '--sc-grad-3'        => $c('gradient_3', '#f59e0b'),
            '--sc-text'          => $c('text_color', '#0f172a'),
            '--sc-muted'         => $c('muted_color', '#475569'),
            '--sc-paper'         => $c('paper_color', '#ffffff'),
            '--sc-surface'       => $c('surface_color', '#f5f7fb'),
            '--sc-border'        => $c('border_color', '#e2e8f0'),
            '--sc-font-head'     => $head[0],
            '--sc-font-body'     => $body[0],
            '--sc-font-mono'     => $mono[0],
            '--sc-size-base'     => $n('typography', 'font_size_base', 17, 15, 19) . 'px',
            '--sc-container'     => $n('layout', 'container_width', 1200, 1080, 1440) . 'px',
            '--sc-radius'        => $n('layout', 'border_radius', 16, 0, 28) . 'px',
            // Core-Variablen (editorjs-content.css) nach dem Core-Customizer-Block erneut an die Palette binden.
            '--primary-color'    => 'var(--sc-primary)',
            '--accent'           => 'var(--sc-primary)',
            '--border-color'     => 'var(--sc-border)',
            '--bg-primary'       => 'var(--sc-paper)',
            '--bg-secondary'     => 'var(--sc-surface)',
            '--text-primary'     => 'var(--sc-text)',
            '--text-secondary'   => 'var(--sc-muted)',
            '--font-mono'        => 'var(--sc-font-mono)',
        ];

        $css = ':root{';
        foreach ($vars as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }
        $css .= '}';
        $css .= showcase_sanitize_custom_css((string) showcase_setting('advanced', 'custom_css', ''));

        echo '<style id="showcase-custom-vars"' . theme_csp_nonce_attr() . '>' . $css . '</style>' . "\n";
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
            'primary'          => 'Hauptnavigation',
            'footer-product'   => 'Footer: Produkt',
            'footer-resources' => 'Footer: Ressourcen',
            'footer-legal'     => 'Rechtliche Links (Footer)',
        ] as $slug => $label) {
            if (!in_array($slug, $existing, true)) {
                $locations[] = ['slug' => $slug, 'label' => $label];
            }
        }

        return $locations;
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

    /** @return array{0:array{0:string,1:string,2:string},1:array{0:string,1:string,2:string},2:array{0:string,1:string,2:string}} */
    private function selectedFonts(): array
    {
        $head = (string) showcase_setting('typography', 'font_heading', 'jakarta');
        $body = (string) showcase_setting('typography', 'font_body', 'inter');
        $mono = (string) showcase_setting('typography', 'font_mono', 'jetbrains');

        return [
            self::FONTS[$head] ?? self::FONTS['jakarta'],
            self::FONTS[$body] ?? self::FONTS['inter'],
            self::MONO_FONTS[$mono] ?? self::MONO_FONTS['jetbrains'],
        ];
    }
}

Showcase_Theme::instance();

// ── Einstellungen, Ausgabe, Daten (gemeinsame Helfer) ──────────────────────────

if (!function_exists('showcase_setting')) {
    function showcase_setting(string $group, string $key, mixed $default = null): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($group, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('showcase_flag')) {
    function showcase_flag(string $group, string $key, bool $default): bool
    {
        $value = showcase_setting($group, $key, $default);
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }
}

if (!function_exists('showcase_text')) {
    function showcase_text(string $group, string $key, string $default = ''): string
    {
        $value = showcase_setting($group, $key, $default);

        return is_scalar($value) ? trim((string) $value) : $default;
    }
}

if (!function_exists('showcase_int')) {
    function showcase_int(string $group, string $key, int $default, int $min, int $max): int
    {
        $value = showcase_setting($group, $key, $default);
        $number = is_numeric($value) ? (int) round((float) $value) : $default;

        return max($min, min($max, $number));
    }
}

if (!function_exists('showcase_e')) {
    function showcase_e(mixed $value): string
    {
        return htmlspecialchars(is_scalar($value) ? (string) $value : '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('showcase_lines')) {
    /** @return array<int, string> */
    function showcase_lines(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', $value) ?: []), static fn(string $line): bool => $line !== ''));
    }
}

if (!function_exists('showcase_css_color')) {
    function showcase_css_color(string $value, string $fallback): string
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

if (!function_exists('showcase_css_number')) {
    function showcase_css_number(mixed $value, float $default, float $min, float $max): string
    {
        $number = is_numeric($value) ? (float) $value : $default;
        $number = max($min, min($max, $number));

        return rtrim(rtrim(number_format($number, 3, '.', ''), '0'), '.');
    }
}

if (!function_exists('showcase_sanitize_custom_css')) {
    function showcase_sanitize_custom_css(string $raw): string
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

if (!function_exists('showcase_safe_url')) {
    /** Lässt nur sichere Ziele zu: relative Pfade, Anker, http(s), mailto: und tel:. */
    function showcase_safe_url(string $url, string $fallback = ''): string
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

if (!function_exists('showcase_url')) {
    function showcase_url(string $target = '/'): string
    {
        $base = rtrim((string) SITE_URL, '/');
        $safe = showcase_safe_url($target, '');
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

if (!function_exists('showcase_is_external')) {
    function showcase_is_external(string $url): bool
    {
        return preg_match('#^https?://#i', $url) === 1 && parse_url($url, PHP_URL_HOST) !== parse_url((string) SITE_URL, PHP_URL_HOST);
    }
}

if (!function_exists('showcase_media_url')) {
    function showcase_media_url(mixed $reference): string
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

        $safe = showcase_safe_url($url, '');
        if ($safe === '' || str_starts_with($safe, '#') || str_starts_with($safe, 'mailto:') || str_starts_with($safe, 'tel:')) {
            return '';
        }

        return str_starts_with($safe, '/') ? rtrim((string) SITE_URL, '/') . $safe : $safe;
    }
}

if (!function_exists('showcase_site_title')) {
    function showcase_site_title(): string
    {
        try {
            $title = trim((string) \CMS\ThemeManager::instance()->getSiteTitle());
        } catch (\Throwable) {
            $title = '';
        }

        return $title !== '' ? $title : (defined('SITE_NAME') ? (string) SITE_NAME : '365CMS');
    }
}

if (!function_exists('showcase_site_description')) {
    function showcase_site_description(): string
    {
        try {
            return trim((string) \CMS\ThemeManager::instance()->getSiteDescription());
        } catch (\Throwable) {
            return '';
        }
    }
}

if (!function_exists('showcase_request_locale')) {
    function showcase_request_locale(): string
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

if (!function_exists('showcase_is_home_request')) {
    function showcase_is_home_request(): bool
    {
        $current = rtrim((string) (parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'), '/');
        $base = rtrim((string) (parse_url((string) SITE_URL, PHP_URL_PATH) ?? ''), '/');

        return $current === $base;
    }
}

if (!function_exists('showcase_format_date')) {
    function showcase_format_date(mixed $value, string $format = 'long'): string
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

if (!function_exists('showcase_plain_text')) {
    /** Klartext aus Editor.js-JSON (Rohdaten in Listen) oder gerendertem HTML. */
    function showcase_plain_text(string $content): string
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

if (!function_exists('showcase_reading_time')) {
    function showcase_reading_time(string $content): int
    {
        $text = showcase_plain_text($content);

        return $text === '' ? 1 : max(1, (int) ceil(count(preg_split('/\s+/u', $text) ?: []) / 200));
    }
}

if (!function_exists('showcase_excerpt')) {
    function showcase_excerpt(object|array $post, int $length = 200, bool $allowContent = true): string
    {
        $data = is_array($post) ? $post : get_object_vars($post);
        $text = trim(strip_tags((string) ($data['excerpt'] ?? '')));
        if ($text === '') {
            $text = trim(strip_tags((string) ($data['meta_description'] ?? '')));
        }
        if ($text === '' && $allowContent) {
            $text = showcase_plain_text((string) ($data['content'] ?? ''));
        }
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (mb_strlen($text, 'UTF-8') > $length) {
            $text = rtrim(mb_substr($text, 0, $length, 'UTF-8'));
            $text = (preg_replace('/\s+\S*$/u', '', $text) ?? $text) . ' …';
        }

        return $text;
    }
}

if (!function_exists('showcase_slugify')) {
    function showcase_slugify(string $text): string
    {
        $text = strtr(mb_strtolower(trim(html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 'UTF-8'), ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);
        $text = trim((string) (preg_replace('/[^a-z0-9]+/', '-', $text) ?? ''), '-');

        return $text !== '' ? $text : 'abschnitt';
    }
}

if (!function_exists('showcase_toc')) {
    /**
     * Ergänzt H2/H3 im gerenderten Inhalt um Sprungmarken und liefert das Inhaltsverzeichnis.
     * Vorhandene IDs (z. B. vom Core-Inhaltsverzeichnis) werden übernommen.
     *
     * @return array{html:string, items:array<int, array{id:string,text:string,level:int}>}
     */
    function showcase_toc(string $html, int $minHeadings = 3): array
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
                $base = showcase_slugify($text);
                $id = $base;
                $i = 2;
                while (isset($used[$id])) {
                    $id = $base . '-' . $i++;
                }
                $used[$id] = true;
                $attrs = ' id="' . showcase_e($id) . '"' . $attrs;
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

if (!function_exists('showcase_post_link')) {
    function showcase_post_link(object|array $post): string
    {
        return showcase_safe_url(theme_post_url($post), showcase_url('/blog'));
    }
}

if (!function_exists('showcase_archive_url')) {
    function showcase_archive_url(string $type, string $slug = ''): string
    {
        try {
            if (function_exists('cms_get_archive_url')) {
                return (string) cms_get_archive_url($type, $slug);
            }
        } catch (\Throwable) {
        }

        return showcase_url('/' . ($type === 'category' ? 'kategorie' : 'tag') . ($slug !== '' ? '/' . rawurlencode($slug) : ''));
    }
}

if (!function_exists('showcase_search_url')) {
    function showcase_search_url(string $query = ''): string
    {
        return showcase_url('/search') . ($query !== '' ? '?q=' . rawurlencode($query) : '');
    }
}

if (!function_exists('showcase_get_posts')) {
    /**
     * @param array{limit?:int,offset?:int,exclude?:array<int,int>,category_id?:int} $args
     * @return array<int, object>
     */
    function showcase_get_posts(array $args = []): array
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
                        p.updated_at, p.category_id, p.content, p.featured_image, c.name AS category_name, c.slug AS category_slug
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

if (!function_exists('showcase_post_tags')) {
    /** @return array<int, array{name:string,url:string}> */
    function showcase_post_tags(object|array $post): array
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
                $slug = showcase_slugify($name);
            }
            $tags[] = ['name' => $name, 'url' => showcase_archive_url('tag', $slug)];
        }

        return $tags;
    }
}

if (!function_exists('showcase_nav_menu')) {
    /**
     * @param array<int, array{label:string,url:string}> $fallback
     */
    function showcase_nav_menu(string $location, string $class = '', array $fallback = []): void
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

        echo '<ul' . ($class !== '' ? ' class="' . showcase_e($class) . '"' : '') . '>';
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? $item['title'] ?? ''));
            if ($label === '') {
                continue;
            }
            $rawUrl = (string) ($item['url'] ?? '#');
            $href = showcase_url($rawUrl);
            $path = rtrim((string) (parse_url($href, PHP_URL_PATH) ?? ''), '/');
            if ($sitePath !== '' && str_starts_with($path, $sitePath)) {
                $path = rtrim(substr($path, strlen($sitePath)), '/');
            }
            $external = showcase_is_external($href);
            $isActive = !$external && !str_contains($rawUrl, '#')
                && ($path === '' ? $current === '' : ($current === $path || str_starts_with($current . '/', $path . '/')));

            $attrs = $isActive ? ' aria-current="page"' : '';
            if (($item['target'] ?? '') === '_blank' || $external) {
                $attrs .= ' target="_blank" rel="noopener noreferrer"';
            }

            echo '<li><a href="' . showcase_e($href) . '"' . $attrs . '>' . showcase_e($label) . ($external ? '<span class="sc-visually-hidden"> (externer Link)</span>' : '') . '</a></li>';
        }
        echo '</ul>';
    }
}

if (!function_exists('showcase_render_toc')) {
    /**
     * Inhaltsverzeichnis „Auf dieser Seite“ (H3 als Unterpunkte der vorangehenden H2).
     *
     * @param array<int, array{id:string,text:string,level:int}> $items
     */
    function showcase_render_toc(array $items): void
    {
        if ($items === []) {
            return;
        }

        echo '<nav class="sc-toc" aria-labelledby="sc-toc-title" data-sc-toc>';
        echo '<p class="sc-toc__title" id="sc-toc-title">Auf dieser Seite</p>';
        echo '<ol class="sc-toc__list">';
        $open = false;
        $subOpen = false;
        foreach ($items as $item) {
            $link = '<a href="#' . showcase_e($item['id']) . '">' . showcase_e($item['text']) . '</a>';
            if ($item['level'] === 3 && $open) {
                if (!$subOpen) {
                    echo '<ol class="sc-toc__sub">';
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

if (!function_exists('showcase_pagination')) {
    function showcase_pagination(int $current, int $total, string $param = 'p'): void
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

        echo '<nav class="sc-pagination" aria-label="Seitennavigation">';
        if ($current > 1) {
            echo '<a class="sc-pagination__step" href="' . showcase_e($build($current - 1)) . '" rel="prev">' . showcase_icon('arrow-left') . '<span>Vorherige Seite</span></a>';
        }
        echo '<ol class="sc-pagination__pages">';
        $last = 0;
        foreach ($pages as $number) {
            if ($last > 0 && $number - $last > 1) {
                echo '<li class="sc-pagination__gap" aria-hidden="true">…</li>';
            }
            echo $number === $current
                ? '<li><span aria-current="page"><span class="sc-visually-hidden">Seite </span>' . $number . '</span></li>'
                : '<li><a href="' . showcase_e($build($number)) . '"><span class="sc-visually-hidden">Seite </span>' . $number . '</a></li>';
            $last = $number;
        }
        echo '</ol>';
        if ($current < $total) {
            echo '<a class="sc-pagination__step" href="' . showcase_e($build($current + 1)) . '" rel="next"><span>Nächste Seite</span>' . showcase_icon('arrow') . '</a>';
        }
        echo '</nav>';
    }
}

if (!function_exists('showcase_page_title')) {
    function showcase_page_title(array $context): string
    {
        $site = showcase_site_title();
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
            $label = !empty($context['isOverview']) ? 'Kategorien' : trim((string) ($context['category']['name'] ?? ''));
        } elseif (isset($context['tag']) && is_array($context['tag'])) {
            $label = !empty($context['isOverview']) ? 'Schlagwörter' : 'Schlagwort: ' . trim((string) ($context['tag']['name'] ?? ''));
        } elseif (array_key_exists('results', $context)) {
            $query = trim((string) ($context['query'] ?? ''));
            $label = $query !== '' ? 'Suche: ' . $query : 'Suche';
        } elseif (isset($context['posts'])) {
            $label = 'Neuigkeiten';
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

// ── Showcase: Marke, Inhalte, Komponenten ──────────────────────────────────────

if (!function_exists('showcase_highlight')) {
    /** Escaped Text; Wörter in *Sternchen* erhalten den Markenverlauf. */
    function showcase_highlight(string $text): string
    {
        return (string) preg_replace('/\*([^*]+)\*/u', '<span class="sc-grad-text">$1</span>', showcase_e($text));
    }
}

if (!function_exists('showcase_plain')) {
    /** Text ohne Hervorhebungs-Sternchen (für Titel, Labels, aria). */
    function showcase_plain(string $text): string
    {
        return trim(str_replace('*', '', $text));
    }
}

if (!function_exists('showcase_core_version')) {
    /** @return array{version:string,status:string,date:string} */
    function showcase_core_version(): array
    {
        $known = class_exists('CMS\\Version');
        $version = $known && defined('CMS\\Version::CURRENT') ? (string) constant('CMS\\Version::CURRENT') : '';
        $status = $known && defined('CMS\\Version::STATUS') ? (string) constant('CMS\\Version::STATUS') : '';
        $date = $known && defined('CMS\\Version::RELEASE_DATE') ? (string) constant('CMS\\Version::RELEASE_DATE') : '';
        if ($version === '' && defined('CMS_VERSION')) {
            $version = (string) CMS_VERSION;
        }

        return [
            'version' => preg_match('/^[0-9][0-9A-Za-z.\-]{0,20}$/', $version) === 1 ? $version : '',
            'status' => match (strtolower($status)) {
                'stable' => 'stabil',
                'beta' => 'Beta',
                'rc' => 'Release Candidate',
                default => '',
            },
            'date' => $date,
        ];
    }
}

if (!function_exists('showcase_logo')) {
    /** Eigenes Logo aus dem Customizer, sonst das mitgelieferte 365CMS-Logo des Cores. */
    function showcase_logo(): string
    {
        $custom = showcase_media_url(showcase_text('header', 'logo_url'));
        if ($custom !== '') {
            return $custom;
        }

        $file = 'assets/images/LOGO_365CMS-wo_Text-75px.png';
        if (defined('ABSPATH') && is_file(rtrim((string) ABSPATH, '/\\') . '/' . $file)) {
            return rtrim((string) SITE_URL, '/') . '/' . $file;
        }

        return '';
    }
}

if (!function_exists('showcase_brand_name')) {
    function showcase_brand_name(): string
    {
        $name = showcase_text('header', 'brand_text', '365CMS');

        return $name !== '' ? $name : showcase_site_title();
    }
}

if (!function_exists('showcase_default_menu')) {
    /** @return array<int, array{label:string,url:string}> */
    function showcase_default_menu(string $location): array
    {
        $github = showcase_safe_url(showcase_text('header', 'github_url', 'https://github.com/PS-easyIT/365CMS.DE'), '');

        return match ($location) {
            'primary' => [
                ['label' => 'Funktionen', 'url' => '/#funktionen'],
                ['label' => 'Sicherheit', 'url' => '/#sicherheit'],
                ['label' => 'Entwickler', 'url' => '/#entwickler'],
                ['label' => 'Themes', 'url' => '/#themes'],
                ['label' => 'Neuigkeiten', 'url' => '/blog'],
            ],
            'footer-product' => [
                ['label' => 'Funktionen', 'url' => '/#funktionen'],
                ['label' => 'Sicherheit', 'url' => '/#sicherheit'],
                ['label' => 'Themes', 'url' => '/#themes'],
                ['label' => 'Installation', 'url' => '/#installation'],
                ['label' => 'Häufige Fragen', 'url' => '/#faq'],
            ],
            'footer-resources' => array_values(array_filter([
                ['label' => 'Neuigkeiten', 'url' => '/blog'],
                ['label' => 'Release Notes', 'url' => '/#neuigkeiten'],
                $github !== '' ? ['label' => 'GitHub', 'url' => $github] : null,
                ['label' => 'Suche', 'url' => '/search'],
                ['label' => 'Sitemap', 'url' => '/sitemap'],
            ])),
            'footer-legal' => [
                ['label' => 'Impressum', 'url' => '/impressum'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz'],
            ],
            default => [],
        };
    }
}

if (!function_exists('showcase_icon')) {
    function showcase_icon(string $name, string $class = 'sc-icon'): string
    {
        $paths = [
            'blocks'     => '<rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/><rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/>',
            'search'     => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
            'shield'     => '<path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.4 7.5 9.5 4.4-1.1 7.5-4.9 7.5-9.5V6z"/><path d="m8.8 12 2.2 2.2 4.4-4.4"/>',
            'lock'       => '<rect x="4.5" y="10.5" width="15" height="10" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3M12 14.5v2"/>',
            'users'      => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14a6.5 6.5 0 0 1 3.5 6"/>',
            'palette'    => '<path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.8-.8 1.8-1.8 0-.5-.2-.9-.5-1.3-.3-.4-.5-.8-.5-1.3 0-1 .8-1.8 1.8-1.8H16a5 5 0 0 0 5-5C21 6.6 17 3 12 3z"/><circle cx="7.5" cy="11.5" r="1.2"/><circle cx="10.5" cy="7.5" r="1.2"/><circle cx="15" cy="8" r="1.2"/>',
            'puzzle'     => '<path d="M10 4a2 2 0 1 1 4 0v1h3a2 2 0 0 1 2 2v3h-1a2 2 0 1 0 0 4h1v3a2 2 0 0 1-2 2h-3v-1a2 2 0 1 0-4 0v1H7a2 2 0 0 1-2-2v-3h1a2 2 0 1 0 0-4H5V7a2 2 0 0 1 2-2h3z"/>',
            'gauge'      => '<path d="M4.2 17.5a9 9 0 1 1 15.6 0"/><path d="m12 13 4-5"/><circle cx="12" cy="13.5" r="1.5"/>',
            'sparkles'   => '<path d="M12 3.5 13.8 9l5.7 1.8-5.7 1.8L12 18l-1.8-5.4L4.5 10.8 10.2 9z"/><path d="M19 3v3M17.5 4.5h3M5 17v3M3.5 18.5h3"/>',
            'server'     => '<rect x="3.5" y="4" width="17" height="7" rx="2"/><rect x="3.5" y="13" width="17" height="7" rx="2"/><path d="M7.5 7.5h.01M7.5 16.5h.01M11 7.5h6M11 16.5h6"/>',
            'globe'      => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
            'cloud'      => '<path d="M7 18.5h10.5a4 4 0 0 0 .6-8A6 6 0 0 0 6.6 9.3 4.6 4.6 0 0 0 7 18.5z"/>',
            'key'        => '<circle cx="8" cy="15" r="4"/><path d="m11 12 8.5-8.5M16 7l2.5 2.5M14 9l2 2"/>',
            'layers'     => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
            'chart'      => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
            'mail'       => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
            'arrow'      => '<path d="M5 12h14M13 6l6 6-6 6"/>',
            'arrow-left' => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
            'chevron'    => '<path d="m9 6 6 6-6 6"/>',
            'check'      => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
            'close'      => '<path d="M6 6l12 12M18 6 6 18"/>',
            'menu'       => '<path d="M4 7h16M4 12h16M4 17h16"/>',
            'copy'       => '<rect x="8.5" y="8.5" width="12" height="12" rx="2"/><path d="M15.5 8.5V5.5a2 2 0 0 0-2-2h-8a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h3"/>',
            'github'     => '<path d="M9 19c-4.3 1.4-4.3-2.5-6-3m12 5v-3.5c0-1 .1-1.4-.5-2 2.8-.3 5.5-1.4 5.5-6a4.6 4.6 0 0 0-1.3-3.2 4.2 4.2 0 0 0-.1-3.2s-1.1-.3-3.5 1.3a12.3 12.3 0 0 0-6.2 0C6.5 2.8 5.4 3.1 5.4 3.1a4.2 4.2 0 0 0-.1 3.2A4.6 4.6 0 0 0 4 9.5c0 4.6 2.7 5.7 5.5 6-.6.6-.6 1.2-.5 2V21"/>',
            'terminal'   => '<rect x="3" y="4" width="18" height="16" rx="2.5"/><path d="m7 9 3 3-3 3M12.5 15H17"/>',
            'code'       => '<path d="m8 7-5 5 5 5M16 7l5 5-5 5M13.5 4l-3 16"/>',
            'book'       => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5z"/><path d="M4 20.5A2.5 2.5 0 0 0 6.5 23H20v-5"/>',
            'rocket'     => '<path d="M5 15c-1.5 1.5-2 5-2 5s3.5-.5 5-2"/><path d="M9 15 4.5 12.5 8 9h4.5L17 4.5c1.5-1.5 3.5-1.5 3.5-1.5s0 2-1.5 3.5L14.5 11v4.5L11 19l-2.5-4.5z"/>',
            'download'   => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
            'calendar'   => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
            'clock'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'tag'        => '<path d="M3 12V4a1 1 0 0 1 1-1h8l9 9-9 9z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
            'external'   => '<path d="M14 4h6v6M20 4 10 14M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
            'linkedin'   => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10.5V16M8 7.5v.01M12 16v-3.2a2.3 2.3 0 0 1 4.6 0V16M12 10.5V16"/>',
            'x'          => '<path d="M4 4l16 16M20 4 4 20"/>',
            'link'       => '<path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1.5 1.5"/><path d="M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1.5-1.5"/>',
            'star'       => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9z"/>',
            'info'       => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8v.01"/>',
            'up'         => '<path d="M12 19V5M6 11l6-6 6 6"/>',
        ];
        $body = $paths[$name] ?? $paths['info'];

        return '<svg class="' . showcase_e($class) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $body . '</svg>';
    }
}

if (!function_exists('showcase_button')) {
    /** Button-Link; leerer Text oder unsicheres Ziel ergeben keine Ausgabe. */
    function showcase_button(string $label, string $url, string $variant = 'primary', string $icon = 'arrow'): string
    {
        $target = showcase_safe_url($url, '');
        if (trim($label) === '' || $target === '') {
            return '';
        }
        $href = str_starts_with($target, '#') ? $target : showcase_url($target);
        $external = showcase_is_external($href);
        $iconHtml = $icon !== '' ? showcase_icon($external && $icon === 'arrow' ? 'external' : $icon) : '';

        return '<a class="sc-button sc-button--' . showcase_e($variant) . '" href="' . showcase_e($href) . '"'
            . ($external ? ' target="_blank" rel="noopener noreferrer"' : '') . '><span>' . showcase_e($label) . '</span>' . $iconHtml
            . ($external ? '<span class="sc-visually-hidden"> (öffnet in neuem Tab)</span>' : '') . '</a>';
    }
}

if (!function_exists('showcase_section_head')) {
    function showcase_section_head(string $eyebrow, string $heading, string $text, string $id, string $modifier = 'center'): void
    {
        if ($heading === '') {
            return;
        }
        ?>
        <header class="sc-section__head sc-section__head--<?php echo showcase_e($modifier); ?>" data-sc-reveal>
            <?php if ($eyebrow !== '') : ?>
                <p class="sc-eyebrow"><?php echo showcase_e($eyebrow); ?></p>
            <?php endif; ?>
            <h2 class="sc-section__title" id="<?php echo showcase_e($id); ?>"><?php echo showcase_highlight($heading); ?></h2>
            <?php if ($text !== '') : ?>
                <p class="sc-section__text"><?php echo showcase_e($text); ?></p>
            <?php endif; ?>
        </header>
        <?php
    }
}

if (!function_exists('showcase_numbered')) {
    /**
     * Liest nummerierte Customizer-Felder (z. B. feature_1_title … feature_9_title).
     *
     * @param array<string, string> $fields Suffix => Standard
     * @return array<int, array<string, string>>
     */
    function showcase_numbered(string $group, string $prefix, int $max, array $fields, string $required): array
    {
        $items = [];
        for ($i = 1; $i <= $max; $i++) {
            $item = [];
            foreach ($fields as $suffix => $default) {
                $item[$suffix] = showcase_text($group, $prefix . '_' . $i . '_' . $suffix, $default);
            }
            if (($item[$required] ?? '') !== '') {
                $items[] = $item;
            }
        }

        return $items;
    }
}

if (!function_exists('showcase_modules')) {
    /** @return array<int, string> */
    function showcase_modules(): array
    {
        return array_slice(array_values(array_filter(array_map('trim', explode(',', showcase_text('sc_features', 'modules'))))), 0, 60);
    }
}

if (!function_exists('showcase_installed_themes')) {
    /**
     * Installierte Themes für die Galerie (ohne das Showcase-Theme selbst).
     *
     * @return array<int, array{slug:string,name:string,description:string,version:string,tags:array<int,string>,palette:array<int,string>,screenshot:string,active:bool}>
     */
    function showcase_installed_themes(int $limit = 0): array
    {
        try {
            $themes = \CMS\ThemeManager::instance()->getAvailableThemes();
        } catch (\Throwable) {
            return [];
        }

        $items = [];
        foreach ($themes as $folder => $data) {
            $folder = (string) $folder;
            if (!is_array($data) || $folder === Showcase_Theme::SLUG) {
                continue;
            }
            $json = is_array($data['json'] ?? null) ? $data['json'] : [];
            $name = trim((string) ($json['name'] ?? $data['name'] ?? $folder));
            $description = trim(strip_tags((string) ($data['description'] ?? '')));
            if (mb_strlen($description, 'UTF-8') > 170) {
                $description = rtrim((string) (preg_replace('/\s+\S*$/u', '', mb_substr($description, 0, 170, 'UTF-8')) ?? '')) . ' …';
            }

            $palette = [];
            $colors = $json['customization']['colors']['settings'] ?? [];
            if (is_array($colors)) {
                foreach ($colors as $setting) {
                    if (!is_array($setting) || ($setting['type'] ?? '') !== 'color') {
                        continue;
                    }
                    $color = strtolower(showcase_css_color((string) ($setting['default'] ?? ''), ''));
                    if (preg_match('/^#([0-9a-f]{6})$/', $color, $m) === 1) {
                        $r = hexdec(substr($m[1], 0, 2));
                        $g = hexdec(substr($m[1], 2, 2));
                        $b = hexdec(substr($m[1], 4, 2));
                        if (($r * 0.299 + $g * 0.587 + $b * 0.114) > 232) {
                            continue; // sehr helle Flächenfarben liefern keinen Kontrast in der Vorschau
                        }
                        if (!in_array($color, $palette, true)) {
                            $palette[] = $color;
                        }
                    }
                    if (count($palette) >= 4) {
                        break;
                    }
                }
            }
            if (count($palette) < 3) {
                $hue = crc32($folder) % 360;
                $palette = ['hsl(' . $hue . ' 55% 22%)', 'hsl(' . $hue . ' 70% 45%)', 'hsl(' . (($hue + 40) % 360) . ' 80% 60%)', 'hsl(' . (($hue + 180) % 360) . ' 70% 55%)'];
            }
            if (count($palette) >= 3 && preg_match('/^#[0-9a-f]{6}$/', $palette[1]) === 1) {
                // Farbe mit dem größten Helligkeitsabstand zur Hero-Fläche wird Akzent (Button der Vorschau).
                $luma = static fn(string $hex): float => hexdec(substr($hex, 1, 2)) * 0.299 + hexdec(substr($hex, 3, 2)) * 0.587 + hexdec(substr($hex, 5, 2)) * 0.114;
                $base = $luma($palette[1]);
                $rest = array_slice($palette, 2);
                usort($rest, static fn(string $a, string $b): int => abs($luma($b) - $base) <=> abs($luma($a) - $base));
                $palette = array_merge(array_slice($palette, 0, 2), $rest);
            }
            while (count($palette) < 4) {
                $palette[] = $palette[count($palette) % 2];
            }

            $tags = array_slice(array_values(array_filter(array_map(static fn(mixed $tag): string => is_scalar($tag) ? trim((string) $tag) : '', (array) ($json['tags'] ?? [])))), 0, 3);
            $screenshot = isset($data['screenshot']) ? showcase_media_url((string) $data['screenshot']) : '';

            $items[] = [
                'slug' => $folder,
                'name' => $name !== '' ? $name : $folder,
                'description' => $description,
                'version' => trim((string) ($data['version'] ?? '')),
                'tags' => $tags,
                'palette' => $palette,
                'screenshot' => $screenshot,
                'active' => !empty($data['active']),
            ];
        }

        usort($items, static fn(array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));

        return $limit > 0 ? array_slice($items, 0, $limit) : $items;
    }
}

if (!function_exists('showcase_highlight_php')) {
    /** Syntax-Hervorhebung für PHP über den PHP-Tokenizer; jede Ausgabe wird escaped. */
    function showcase_highlight_php(string $code): string
    {
        $code = str_replace(["\r\n", "\r"], "\n", $code);
        $prefixed = preg_match('/^\s*<\?php/', $code) !== 1;
        if ($prefixed) {
            $code = "<?php\n" . $code;
        }

        try {
            $tokens = token_get_all($code);
        } catch (\Throwable) {
            return showcase_e($code);
        }

        $keywords = [T_FUNCTION, T_FN, T_RETURN, T_STATIC, T_PUBLIC, T_PRIVATE, T_PROTECTED, T_CLASS, T_NEW, T_ECHO,
            T_IF, T_ELSE, T_ELSEIF, T_FOREACH, T_FOR, T_WHILE, T_AS, T_USE, T_NAMESPACE, T_ARRAY, T_DECLARE, T_CONST,
            T_FINAL, T_TRY, T_CATCH, T_THROW, T_MATCH, T_READONLY, T_ENUM, T_EXTENDS, T_IMPLEMENTS, T_INTERFACE,
            T_ABSTRACT, T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_ISSET, T_EMPTY, T_INSTANCEOF, T_BREAK, T_CONTINUE];
        $literals = ['true', 'false', 'null', 'self', 'static', 'array', 'string', 'int', 'bool', 'float', 'void', 'mixed', 'never', 'object', 'callable', 'iterable', 'parent'];

        $html = '';
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            if (!is_array($token)) {
                $html .= showcase_e($token);
                continue;
            }
            [$id, $text] = $token;
            if ($i === 0 && $prefixed && $id === T_OPEN_TAG) {
                continue;
            }

            $class = '';
            if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
                $class = 'c';
            } elseif ($id === T_CONSTANT_ENCAPSED_STRING || $id === T_ENCAPSED_AND_WHITESPACE) {
                $class = 's';
            } elseif ($id === T_VARIABLE) {
                $class = 'v';
            } elseif ($id === T_LNUMBER || $id === T_DNUMBER) {
                $class = 'n';
            } elseif ($id === T_OPEN_TAG || $id === T_CLOSE_TAG) {
                $class = 't';
            } elseif (in_array($id, $keywords, true)) {
                $class = 'k';
            } elseif ($id === T_NAME_FULLY_QUALIFIED || $id === T_NAME_QUALIFIED) {
                $class = 'x';
            } elseif ($id === T_STRING) {
                if (in_array(strtolower($text), $literals, true)) {
                    $class = 'k';
                } else {
                    $next = $i + 1;
                    while ($next < $count && is_array($tokens[$next]) && $tokens[$next][0] === T_WHITESPACE) {
                        $next++;
                    }
                    $class = ($next < $count && $tokens[$next] === '(') ? 'f' : '';
                }
            }

            $html .= $class !== '' ? '<span class="sc-tok-' . $class . '">' . showcase_e($text) . '</span>' : showcase_e($text);
        }

        return $html;
    }
}

if (!function_exists('showcase_code_window')) {
    function showcase_code_window(string $code, string $filename, string $id): void
    {
        if (trim($code) === '') {
            return;
        }
        ?>
        <figure class="sc-window sc-window--code">
            <figcaption class="sc-window__bar">
                <span class="sc-window__dots" aria-hidden="true"><span></span><span></span><span></span></span>
                <span class="sc-window__title"><?php echo showcase_icon('code'); ?><?php echo showcase_e($filename !== '' ? $filename : 'Code-Beispiel'); ?></span>
                <button type="button" class="sc-copy sc-js-only" data-sc-copy="<?php echo showcase_e($id); ?>"><?php echo showcase_icon('copy'); ?><span data-sc-copy-label>Kopieren</span></button>
            </figcaption>
            <pre class="sc-window__body" tabindex="0" aria-label="<?php echo showcase_e('Quelltext: ' . ($filename !== '' ? $filename : 'Code-Beispiel')); ?>"><code id="<?php echo showcase_e($id); ?>"><?php echo showcase_highlight_php($code); ?></code></pre>
        </figure>
        <?php
    }
}

if (!function_exists('showcase_terminal')) {
    function showcase_terminal(string $text, string $id): void
    {
        $lines = showcase_lines($text);
        if ($lines === []) {
            return;
        }
        ?>
        <figure class="sc-window sc-window--terminal">
            <figcaption class="sc-window__bar">
                <span class="sc-window__dots" aria-hidden="true"><span></span><span></span><span></span></span>
                <span class="sc-window__title"><?php echo showcase_icon('terminal'); ?>Terminal</span>
                <button type="button" class="sc-copy sc-js-only" data-sc-copy="<?php echo showcase_e($id); ?>"><?php echo showcase_icon('copy'); ?><span data-sc-copy-label>Kopieren</span></button>
            </figcaption>
            <pre class="sc-window__body" tabindex="0" aria-label="Befehle für die Installation"><code id="<?php echo showcase_e($id); ?>"><?php
            foreach ($lines as $index => $line) {
                $class = str_starts_with($line, '#') ? 'sc-term__comment' : (preg_match('#^https?://#i', $line) === 1 ? 'sc-term__url' : 'sc-term__cmd');
                echo ($index > 0 ? "\n" : '') . '<span class="' . $class . '">' . showcase_e($line) . '</span>';
            }
            ?></code></pre>
        </figure>
        <?php
    }
}

if (!function_exists('showcase_get_categories')) {
    /** @return array<int, array{id:int,name:string,slug:string,count:int,url:string}> */
    function showcase_get_categories(): array
    {
        try {
            $db = \CMS\Database::instance();
            $prefix = $db->getPrefix();
            $rows = $db->get_results(
                "SELECT c.id, c.name, c.slug,
                        (SELECT COUNT(*) FROM {$prefix}posts p
                          WHERE (p.category_id = c.id OR EXISTS (SELECT 1 FROM {$prefix}post_category_rel pcr WHERE pcr.post_id = p.id AND pcr.category_id = c.id))
                            AND " . cms_post_publication_where('p') . ") AS post_count
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
                $items[] = ['id' => (int) $row->id, 'name' => trim((string) $row->name), 'slug' => $slug, 'count' => (int) $row->post_count, 'url' => showcase_archive_url('category', $slug)];
            }
        }

        return $items;
    }
}

if (!function_exists('showcase_post_card')) {
    function showcase_post_card(object|array $post, string $headingTag = 'h3'): void
    {
        $item = is_array($post) ? (object) $post : $post;
        $title = trim((string) ($item->title ?? ''));
        if ($title === '') {
            return;
        }
        $headingTag = in_array($headingTag, ['h2', 'h3', 'h4'], true) ? $headingTag : 'h3';
        $url = showcase_post_link($item);
        $image = showcase_media_url($item->featured_image ?? '');
        $date = (string) ($item->published_at ?? $item->created_at ?? '');
        $category = trim((string) ($item->category_name ?? ''));
        $excerpt = showcase_excerpt($item, 150);
        ?>
        <article class="sc-postcard" data-sc-reveal>
            <a class="sc-postcard__media<?php echo $image === '' ? ' sc-postcard__media--empty' : ''; ?>" href="<?php echo showcase_e($url); ?>" tabindex="-1" aria-hidden="true">
                <?php if ($image !== '') : ?>
                    <img src="<?php echo showcase_e($image); ?>" alt="" width="640" height="360" loading="lazy" decoding="async">
                <?php else : ?>
                    <span class="sc-postcard__mark"><?php echo showcase_icon('sparkles'); ?></span>
                <?php endif; ?>
            </a>
            <div class="sc-postcard__body">
                <p class="sc-postcard__meta">
                    <?php if ($category !== '') : ?><span class="sc-pill"><?php echo showcase_e($category); ?></span><?php endif; ?>
                    <?php if (showcase_format_date($date) !== '') : ?>
                        <time datetime="<?php echo showcase_e(showcase_format_date($date, 'iso')); ?>"><?php echo showcase_e(showcase_format_date($date)); ?></time>
                    <?php endif; ?>
                </p>
                <<?php echo $headingTag; ?> class="sc-postcard__title"><a href="<?php echo showcase_e($url); ?>"><?php echo showcase_e($title); ?></a></<?php echo $headingTag; ?>>
                <?php if ($excerpt !== '') : ?>
                    <p class="sc-postcard__excerpt"><?php echo showcase_e($excerpt); ?></p>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}

if (!function_exists('showcase_search_form')) {
    function showcase_search_form(string $id, string $value = '', string $variant = ''): void
    {
        ?>
        <form role="search" method="get" action="<?php echo showcase_e(showcase_search_url()); ?>" class="sc-searchform<?php echo $variant !== '' ? ' sc-searchform--' . showcase_e($variant) : ''; ?>">
            <label for="<?php echo showcase_e($id); ?>" class="sc-visually-hidden">Suchbegriff</label>
            <?php echo showcase_icon('search', 'sc-icon sc-searchform__icon'); ?>
            <input id="<?php echo showcase_e($id); ?>" type="search" name="q" value="<?php echo showcase_e($value); ?>" placeholder="Dokumentation und Neuigkeiten durchsuchen …" maxlength="120" autocomplete="off">
            <button type="submit" class="sc-button sc-button--primary"><span>Suchen</span></button>
        </form>
        <?php
    }
}

if (!function_exists('showcase_share')) {
    function showcase_share(string $permalink, string $title): void
    {
        ?>
        <div class="sc-share">
            <span class="sc-share__label">Teilen</span>
            <button type="button" class="sc-share__link sc-js-only" data-sc-copy-url><?php echo showcase_icon('link'); ?><span class="sc-visually-hidden">Link kopieren</span></button>
            <a class="sc-share__link" href="<?php echo showcase_e('https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($permalink)); ?>" target="_blank" rel="noopener noreferrer"><?php echo showcase_icon('linkedin'); ?><span class="sc-visually-hidden">Auf LinkedIn teilen (öffnet in neuem Tab)</span></a>
            <a class="sc-share__link" href="<?php echo showcase_e('https://x.com/intent/post?url=' . rawurlencode($permalink) . '&text=' . rawurlencode($title)); ?>" target="_blank" rel="noopener noreferrer"><?php echo showcase_icon('x'); ?><span class="sc-visually-hidden">Auf X teilen (öffnet in neuem Tab)</span></a>
            <a class="sc-share__link" href="<?php echo showcase_e('mailto:?subject=' . rawurlencode($title) . '&body=' . rawurlencode($permalink)); ?>"><?php echo showcase_icon('mail'); ?><span class="sc-visually-hidden">Per E-Mail teilen</span></a>
            <span class="sc-share__status" role="status" data-sc-share-status></span>
        </div>
        <?php
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
