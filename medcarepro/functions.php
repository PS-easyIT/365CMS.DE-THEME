<?php
declare(strict_types=1);

/**
 * MedCare Pro Theme – functions.php
 *
 * Medizin- & Gesundheitspraxis-Theme für 365CMS v3.x.x auf PHP 8.4.
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('MEDCAREPRO_THEME_VERSION')) {
    define('MEDCAREPRO_THEME_VERSION', '1.0.3');
}

if (!defined('MEDCAREPRO_THEME_SLUG')) {
    define('MEDCAREPRO_THEME_SLUG', 'medcarepro');
}

/**
 * Theme bootstrap (singleton).
 */
final class MedCarePro_Theme
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

        // Auf BEIDEN Hooks für v3-Bootstrap-Kompatibilität.
        \CMS\Hooks::addAction('init',     [$this, 'registerNavMenus'], 10);
        \CMS\Hooks::addAction('cms_init', [$this, 'registerNavMenus'], 10);
    }

    // ── Assets ───────────────────────────────────────────────────────────────

    public function enqueueStyles(): void
    {
        $href = htmlspecialchars(
            $this->themeBaseUrl() . '/style.css?v=' . rawurlencode(MEDCAREPRO_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = htmlspecialchars(
            $this->themeBaseUrl() . '/js/navigation.js?v=' . rawurlencode(MEDCAREPRO_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }

    /**
     * Google Fonts – preconnect + nur tatsächlich gewählte Familien.
     * Bei reinen System-Stacks (z. B. `georgia`) wird die jeweilige
     * Familie übersprungen und der Request ggf. ganz unterdrückt.
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

    // ── Meta ────────────────────────────────────────────────────────────────

    public function outputMetaTags(): void
    {
        $tm        = \CMS\ThemeManager::instance();
        $siteDesc  = htmlspecialchars((string) $tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl   = htmlspecialchars((string) (defined('SITE_URL') ? SITE_URL : ''), ENT_QUOTES, 'UTF-8');
        $themeColor = htmlspecialchars(
            (string) $this->customizerGet('header', 'header_bg_color', '#ffffff'),
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

        // Fonts
        $baseChoice    = (string) $this->customizerGet('typography', 'font_family_base',         'open-sans');
        $headingChoice = (string) $this->customizerGet('typography', 'font_family_heading',      'source-sans-pro');
        $medicalChoice = (string) $this->customizerGet('typography', 'font_family_medical_text', 'source-serif-pro');

        $baseFont    = htmlspecialchars($this->mapFontChoice($baseChoice,    'body'),    ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingChoice, 'heading'), ENT_QUOTES, 'UTF-8');
        $serifFont   = htmlspecialchars($this->mapFontChoice($medicalChoice, 'serif'),   ENT_QUOTES, 'UTF-8');

        echo '<style id="mc-custom-vars">:root{';

        // Brand & Farben
        echo '--mc-primary:'       . $p('colors', 'primary_color',     '#0e5b6b') . ';';
        echo '--mc-primary-hover:' . $p('colors', 'secondary_color',   '#0c4f5d') . ';';
        echo '--mc-accent:'        . $p('colors', 'accent_color',      '#0f9d6a') . ';';
        echo '--mc-warning:'       . $p('colors', 'warning_color',     '#b45309') . ';';
        echo '--mc-urgent:'        . $p('colors', 'urgent_color',      '#b91c1c') . ';';
        echo '--mc-link:'          . $p('colors', 'link_color',        '#107585') . ';';

        // Spezialgebiete (Fachbereiche)
        echo '--mc-spec-general:'  . $p('colors', 'specialty_general', '#0e5b6b') . ';';
        echo '--mc-spec-dental:'   . $p('colors', 'specialty_dental',  '#b45309') . ';';
        echo '--mc-spec-surgery:'  . $p('colors', 'specialty_surgery', '#7c3aed') . ';';
        echo '--mc-spec-therapy:'  . $p('colors', 'specialty_therapy', '#0f9d6a') . ';';

        // Flächen / Texte
        echo '--mc-paper:'         . $p('colors', 'bg_color',          '#f6f8fa') . ';';
        echo '--mc-surface:'       . $p('colors', 'card_bg_color',     '#ffffff') . ';';
        echo '--mc-ink:'           . $p('colors', 'text_color',        '#0f1f25') . ';';
        echo '--mc-text-mute:'     . $p('colors', 'muted_color',       '#5b6b73') . ';';
        echo '--mc-rule:'          . $p('colors', 'border_color',      '#e1e6ec') . ';';

        // Typografie
        echo '--mc-font-body:'     . $baseFont    . ';';
        echo '--mc-font-heading:'  . $headingFont . ';';
        echo '--mc-font-serif:'    . $serifFont   . ';';
        echo '--mc-font-size-base:'    . $p('typography',    'font_size_base',              '17')  . 'px;';
        echo '--mc-font-size-min:'     . $p('accessibility', 'min_font_size_accessibility', '16')  . 'px;';
        echo '--mc-line-height:'       . $p('typography',    'line_height_base',            '1.7') . ';';
        echo '--mc-heading-weight:'    . $p('typography',    'font_weight_heading',         '700') . ';';

        // Layout
        echo '--mc-max-width:'    . $p('layout', 'container_width', '1100') . 'px;';
        echo '--mc-content-pad:'  . $p('layout', 'content_padding', '2.5')  . 'rem;';
        echo '--mc-radius:'       . $p('layout', 'border_radius',   '10')   . 'px;';
        echo '--mc-section-pad:'  . $p('layout', 'section_spacing', '5')    . 'rem;';

        // Header
        echo '--mc-header-bg:'      . $p('header', 'header_bg_color',     '#ffffff') . ';';
        echo '--mc-header-text:'    . $p('header', 'header_text_color',   '#0f1f25') . ';';
        echo '--mc-header-rule:'    . $p('header', 'header_border_color', '#e1e6ec') . ';';
        echo '--mc-header-height:'  . $p('header', 'header_height',       '76')      . 'px;';
        echo '--mc-logo-max:'       . $p('header', 'logo_max_height',     '52')      . 'px;';

        // Footer
        echo '--mc-footer-bg:'   . $p('footer', 'footer_bg_color',   '#082f38') . ';';
        echo '--mc-footer-text:' . $p('footer', 'footer_text_color', '#b9d3da') . ';';
        echo '--mc-footer-link:' . $p('footer', 'footer_link_color', '#e0f1f5') . ';';

        // Buttons
        echo '--mc-btn-radius:'    . $p('buttons', 'button_border_radius', '8')     . 'px;';
        echo '--mc-btn-padding-x:' . $p('buttons', 'button_padding_x',     '2')     . 'rem;';
        echo '--mc-btn-padding-y:' . $p('buttons', 'button_padding_y',     '0.875') . 'rem;';
        echo '--mc-btn-weight:'    . $p('buttons', 'button_font_weight',   '600')   . ';';
        echo '--mc-btn-transform:' . $p('buttons', 'button_transform',     'none')  . ';';

        // Focus
        echo '--focus-ring:'   . $p('colors', 'primary_color', '#0e5b6b') . ';';

        echo '}';

        // Sticky-Header opt-out – Klasse via body wirkt
        $stickyHeader = filter_var(
            $this->customizerGet('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.mc-site-header{position:static;}body.mc-body{padding-top:0;}.mc-site-content{padding-top:0;}';
        }

        // Custom CSS (defensiv gegen </style>-Injection)
        $custom = $this->sanitizeCustomCss((string) $this->customizerGet('advanced', 'custom_css', ''));
        if ($custom !== '') {
            echo $custom;
        }

        echo '</style>' . "\n";
    }

    // ── Menü-Positionen ──────────────────────────────────────────────────────

    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary-nav',  'label' => 'Hauptmenü'];
        $locations[] = ['slug' => 'footer-nav',   'label' => 'Fußzeilen-Navigation'];
        $locations[] = ['slug' => 'footer-legal', 'label' => 'Rechtliche Links'];
        return $locations;
    }

    public function registerNavMenus(): void
    {
        $tm = \CMS\ThemeManager::instance();
        if (method_exists($tm, 'registerMenuLocation')) {
            $tm->registerMenuLocation('primary-nav',  'Hauptmenü');
            $tm->registerMenuLocation('footer-nav',   'Fußzeilen-Navigation');
            $tm->registerMenuLocation('footer-legal', 'Rechtliche Links');
        }
    }

    // ── Internal helpers ────────────────────────────────────────────────────

    private function themeBaseUrl(): string
    {
        $tm = \CMS\ThemeManager::instance();
        try {
            $url = (string) $tm->getThemeUrl(MEDCAREPRO_THEME_SLUG);
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
     * @param string $slot   'body' | 'heading' | 'serif'
     */
    private function mapFontChoice(string $choice, string $slot): string
    {
        $sansStack  = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        $serifStack = 'Georgia, "Times New Roman", "Liberation Serif", serif';

        return match ($choice) {
            'open-sans'         => '"Open Sans", ' . $sansStack,
            'roboto'            => 'Roboto, ' . $sansStack,
            'lato'              => 'Lato, ' . $sansStack,
            'inter'             => 'Inter, ' . $sansStack,
            'source-sans-pro'   => '"Source Sans 3", ' . $sansStack,
            'libre-baskerville' => '"Libre Baskerville", ' . $serifStack,
            'source-serif-pro'  => '"Source Serif 4", ' . $serifStack,
            'georgia'           => $serifStack,
            default             => match ($slot) {
                'heading' => '"Source Sans 3", ' . $sansStack,
                'serif'   => '"Source Serif 4", ' . $serifStack,
                default   => '"Open Sans", ' . $sansStack,
            },
        };
    }

    /**
     * Liefert die `family=…&family=…`-URL-Stückliste für Google Fonts –
     * nur tatsächlich gewählte Familien werden geladen. System-Fonts
     * (z. B. `georgia`) werden übersprungen.
     */
    private function googleFontFamilies(): string
    {
        $base    = (string) $this->customizerGet('typography', 'font_family_base',         'open-sans');
        $heading = (string) $this->customizerGet('typography', 'font_family_heading',      'source-sans-pro');
        $medical = (string) $this->customizerGet('typography', 'font_family_medical_text', 'source-serif-pro');

        $needed = [];
        foreach ([$base, $heading, $medical] as $choice) {
            switch ($choice) {
                case 'open-sans':
                    $needed['Open Sans']         = 'Open+Sans:wght@400;600;700';
                    break;
                case 'roboto':
                    $needed['Roboto']            = 'Roboto:wght@400;500;700';
                    break;
                case 'lato':
                    $needed['Lato']              = 'Lato:wght@400;700';
                    break;
                case 'inter':
                    $needed['Inter']             = 'Inter:wght@400;500;600;700';
                    break;
                case 'source-sans-pro':
                    $needed['Source Sans 3']     = 'Source+Sans+3:wght@400;600;700';
                    break;
                case 'libre-baskerville':
                    $needed['Libre Baskerville'] = 'Libre+Baskerville:wght@400;700';
                    break;
                case 'source-serif-pro':
                    $needed['Source Serif 4']    = 'Source+Serif+4:wght@400;600;700';
                    break;
                // 'georgia' bleibt System-Schrift.
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

MedCarePro_Theme::instance();

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
    /**
     * Theme-lokaler Routen-Resolver. Kennt die gängigen MedCare-Pro-Pfade,
     * fällt sonst auf SITE_URL + Pfad zurück.
     */
    function theme_route_url(string $name): string
    {
        $base = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        return match ($name) {
            'home'      => $base . '/',
            'search'    => $base . '/search',
            'login'     => $base . '/login',
            'register'  => $base . '/register',
            'member'    => $base . '/member',
            'doctors'   => $base . '/aerzte',
            'booking'   => $base . '/termin',
            'fields'    => $base . '/fachgebiete',
            'blog'      => $base . '/blog',
            'imprint'   => $base . '/impressum',
            'privacy'   => $base . '/datenschutz',
            'terms'     => $base . '/agb',
            'cookies'   => $base . '/cookie-richtlinie',
            default     => $base . '/' . ltrim($name, '/'),
        };
    }
}

if (!function_exists('theme_nav_menu')) {
    /**
     * Render a registered navigation menu as a <ul> list.
     *
     * @param string $location  Menu location slug (e.g. 'primary-nav')
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(MEDCAREPRO_THEME_SLUG) . '/header.php';
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(MEDCAREPRO_THEME_SLUG) . '/footer.php';
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

if (!function_exists('mc_get_setting')) {
    /**
     * Shorthand for ThemeCustomizer::get() with safe fallback.
     */
    function mc_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('mc_get_flash')) {
    /**
     * Return and clear a session flash message array or null.
     *
     * @return array{type:string,message:string}|null
     */
    function mc_get_flash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!empty($_SESSION['mc_flash']) && is_array($_SESSION['mc_flash'])) {
            $flash = $_SESSION['mc_flash'];
            unset($_SESSION['mc_flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('mc_set_flash')) {
    function mc_set_flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION['mc_flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('mc_href')) {
    /**
     * Normalize a link target into an output-safe URL with site host.
     */
    function mc_href(string $target): string
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

if (!function_exists('mc_site_url')) {
    function mc_site_url(): string
    {
        return rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
    }
}

if (!function_exists('mc_site_title')) {
    function mc_site_title(): string
    {
        try {
            return (string) \CMS\ThemeManager::instance()->getSiteTitle();
        } catch (\Throwable) {
            return 'MedCare Pro';
        }
    }
}

if (!function_exists('mc_body_class')) {
    /**
     * Server-render the <body> class string – kein JS-Mutation der Klasse vor Paint.
     */
    function mc_body_class(string ...$extra): string
    {
        $classes = ['mc-body'];

        $sticky = filter_var(
            mc_get_setting('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$sticky) {
            $classes[] = 'no-sticky-header';
        }

        $showEmergency = filter_var(
            mc_get_setting('header', 'show_emergency_banner', false),
            FILTER_VALIDATE_BOOLEAN
        );
        $emergencyPhone = trim((string) mc_get_setting('header', 'emergency_phone', '112'));
        if ($showEmergency && $emergencyPhone !== '') {
            $classes[] = 'has-emergency-banner';
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

if (!function_exists('mc_safe_headline')) {
    /**
     * Erlaubt ausschließlich `<span class="hl">…</span>` als kontrolliertes
     * Inline-Markup in Überschriften. Alles andere wird HTML-escaped.
     */
    function mc_safe_headline(string $raw): string
    {
        $escaped = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['&lt;span class=&quot;hl&quot;&gt;', '&lt;/span&gt;'],
            ['<span class="mc-hl">', '</span>'],
            $escaped
        );
    }
}

if (!function_exists('mc_tel_sanitize')) {
    /**
     * Reduziert eine Eingabe auf `+` und Ziffern für sichere `tel:`-URIs.
     */
    function mc_tel_sanitize(string $raw): string
    {
        $cleaned = (string) preg_replace('/[^0-9+]/', '', $raw);
        if ($cleaned === '' || $cleaned === '+') {
            return '';
        }
        return $cleaned;
    }
}
