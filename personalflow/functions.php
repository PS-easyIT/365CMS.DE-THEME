<?php
declare(strict_types=1);

/**
 * PersonalFlow Theme – functions.php
 *
 * HR / Personalvermittlungs-Theme für 365CMS v3.x.x auf PHP 8.4.
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('PERSONALFLOW_THEME_VERSION')) {
    define('PERSONALFLOW_THEME_VERSION', '1.0.1');
}

if (!defined('PERSONALFLOW_THEME_SLUG')) {
    define('PERSONALFLOW_THEME_SLUG', 'personalflow');
}

/**
 * Theme bootstrap (singleton).
 */
final class PersonalFlow_Theme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function version(): string
    {
        return PERSONALFLOW_THEME_VERSION;
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
            $this->themeBaseUrl() . '/style.css?v=' . rawurlencode(PERSONALFLOW_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function enqueueScripts(): void
    {
        $src = htmlspecialchars(
            $this->themeBaseUrl() . '/js/navigation.js?v=' . rawurlencode(PERSONALFLOW_THEME_VERSION),
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }

    /**
     * Google Fonts – preconnect + nur tatsächlich gewählte Familien.
     * Bei reinen System-Stacks wird die jeweilige Familie übersprungen und
     * der Request ggf. ganz unterdrückt.
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
        $tm       = \CMS\ThemeManager::instance();
        $siteDesc = htmlspecialchars((string) $tm->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteUrl  = htmlspecialchars((string) (defined('SITE_URL') ? SITE_URL : ''), ENT_QUOTES, 'UTF-8');

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
        $baseChoice    = (string) $this->customizerGet('typography', 'font_family_base',    'nunito-sans');
        $headingChoice = (string) $this->customizerGet('typography', 'font_family_heading', 'nunito');
        $statsChoice   = (string) $this->customizerGet('typography', 'font_family_stats',   'jetbrains-mono');

        $baseFont    = htmlspecialchars($this->mapFontChoice($baseChoice,    'body'),    ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingChoice, 'heading'), ENT_QUOTES, 'UTF-8');
        $statsFont   = htmlspecialchars($this->mapFontChoice($statsChoice,   'stats'),   ENT_QUOTES, 'UTF-8');

        echo '<style id="pf-custom-vars">:root{';

        // Brand
        echo '--pf-primary:'       . $p('colors', 'primary_color',     '#b45309') . ';';
        echo '--pf-primary-hover:' . $p('colors', 'secondary_color',   '#92400e') . ';';
        echo '--pf-primary-soft:'  . $p('colors', 'primary_soft',      '#fef3c7') . ';';
        echo '--pf-primary-ink:'   . $p('colors', 'primary_ink',       '#451a03') . ';';
        echo '--pf-accent:'        . $p('colors', 'accent_color',      '#0f766e') . ';';
        echo '--pf-accent-soft:'   . $p('colors', 'accent_soft',       '#ccfbf1') . ';';

        // Pipeline-Status (Beworben → Screening → Interview → Angebot → Eingestellt → Archiv)
        echo '--pf-stage-applied-fg:'     . $p('colors', 'stage_applied_fg',     '#475569') . ';';
        echo '--pf-stage-applied-bg:'     . $p('colors', 'stage_applied_bg',     '#e2e8f0') . ';';
        echo '--pf-stage-screened-fg:'    . $p('colors', 'stage_screened_fg',    '#b45309') . ';';
        echo '--pf-stage-screened-bg:'    . $p('colors', 'stage_screened_bg',    '#fef3c7') . ';';
        echo '--pf-stage-interview-fg:'   . $p('colors', 'stage_interview_fg',   '#1d4ed8') . ';';
        echo '--pf-stage-interview-bg:'   . $p('colors', 'stage_interview_bg',   '#dbeafe') . ';';
        echo '--pf-stage-offer-fg:'       . $p('colors', 'stage_offer_fg',       '#6d28d9') . ';';
        echo '--pf-stage-offer-bg:'       . $p('colors', 'stage_offer_bg',       '#ede9fe') . ';';
        echo '--pf-stage-hired-fg:'       . $p('colors', 'stage_hired_fg',       '#15803d') . ';';
        echo '--pf-stage-hired-bg:'       . $p('colors', 'stage_hired_bg',       '#dcfce7') . ';';
        echo '--pf-stage-archived-fg:'    . $p('colors', 'stage_archived_fg',    '#57534e') . ';';
        echo '--pf-stage-archived-bg:'    . $p('colors', 'stage_archived_bg',    '#f5f5f4') . ';';

        // Flächen / Text
        echo '--pf-paper:'      . $p('colors', 'bg_color',        '#fffbf5') . ';';
        echo '--pf-paper-alt:'  . $p('colors', 'bg_secondary',    '#fef6e7') . ';';
        echo '--pf-surface:'    . $p('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--pf-ink:'        . $p('colors', 'text_color',      '#1c1917') . ';';
        echo '--pf-text-mute:'  . $p('colors', 'muted_color',     '#78716c') . ';';
        echo '--pf-rule:'       . $p('colors', 'border_color',    '#f5e7c4') . ';';
        echo '--pf-rule-strong:' . $p('colors', 'border_strong_color', '#e7d39a') . ';';
        echo '--pf-link:'       . $p('colors', 'link_color',      '#92400e') . ';';

        // Status (Banner / Forms)
        echo '--pf-success:'    . $p('colors', 'success_color',   '#15803d') . ';';
        echo '--pf-warning:'    . $p('colors', 'warning_color',   '#b45309') . ';';
        echo '--pf-error:'      . $p('colors', 'error_color',     '#b91c1c') . ';';

        // Typografie
        echo '--pf-font-body:'      . $baseFont    . ';';
        echo '--pf-font-heading:'   . $headingFont . ';';
        echo '--pf-font-stats:'     . $statsFont   . ';';
        echo '--pf-font-size-base:' . $p('typography', 'font_size_base',      '16')  . 'px;';
        echo '--pf-line-height:'    . $p('typography', 'line_height_base',    '1.6') . ';';
        echo '--pf-heading-weight:' . $p('typography', 'font_weight_heading', '700') . ';';

        // Layout
        echo '--pf-max-width:'    . $p('layout', 'container_width', '1200') . 'px;';
        echo '--pf-content-pad:'  . $p('layout', 'content_padding', '2')    . 'rem;';
        echo '--pf-radius:'       . $p('layout', 'border_radius',   '12')   . 'px;';
        echo '--pf-section-pad:'  . $p('layout', 'section_spacing', '4.5')  . 'rem;';

        // Header
        echo '--pf-header-bg:'      . $p('header', 'header_bg_color',     '#ffffff') . ';';
        echo '--pf-header-text:'    . $p('header', 'header_text_color',   '#1c1917') . ';';
        echo '--pf-header-rule:'    . $p('header', 'header_border_color', '#f5e7c4') . ';';
        echo '--pf-header-height:'  . $p('header', 'header_height',       '72')      . 'px;';
        echo '--pf-logo-max:'       . $p('header', 'logo_max_height',     '44')      . 'px;';

        // Footer
        echo '--pf-footer-bg:'   . $p('footer', 'footer_bg_color',   '#1c1917') . ';';
        echo '--pf-footer-text:' . $p('footer', 'footer_text_color', '#a8a29e') . ';';
        echo '--pf-footer-link:' . $p('footer', 'footer_link_color', '#fde68a') . ';';

        // Buttons
        echo '--pf-btn-radius:'    . $p('buttons', 'button_border_radius', '10')    . 'px;';
        echo '--pf-btn-padding-x:' . $p('buttons', 'button_padding_x',     '1.5')   . 'rem;';
        echo '--pf-btn-padding-y:' . $p('buttons', 'button_padding_y',     '0.75')  . 'rem;';
        echo '--pf-btn-weight:'    . $p('buttons', 'button_font_weight',   '700')   . ';';
        echo '--pf-btn-transform:' . $p('buttons', 'button_transform',     'none')  . ';';

        // Focus
        echo '--focus-ring:'        . $p('colors', 'primary_color', '#b45309') . ';';
        echo '--focus-ring-shadow:' . '0 0 0 .2rem rgba(180, 83, 9, .22);';

        echo '}';

        // Sticky-Header opt-out – body-Klasse wirkt
        $stickyHeader = filter_var(
            $this->customizerGet('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$stickyHeader) {
            echo '.pf-site-header{position:static;}body.pf-body{padding-top:0;}.pf-site-content{padding-top:0;}';
        }

        // Header-Schatten Opt-out
        $headerShadow = filter_var(
            $this->customizerGet('header', 'show_header_shadow', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$headerShadow) {
            echo '.pf-site-header.scrolled{box-shadow:none;}';
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
            $url = (string) $tm->getThemeUrl(PERSONALFLOW_THEME_SLUG);
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
     * @param string $slot   'body' | 'heading' | 'stats'
     */
    private function mapFontChoice(string $choice, string $slot): string
    {
        $sansStack = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
        $monoStack = 'ui-monospace, SFMono-Regular, "JetBrains Mono", "IBM Plex Mono", Menlo, Consolas, monospace';

        return match ($choice) {
            // Body / Heading
            'nunito-sans'  => '"Nunito Sans", ' . $sansStack,
            'nunito'       => 'Nunito, ' . $sansStack,
            'inter'        => 'Inter, ' . $sansStack,
            'open-sans'    => '"Open Sans", ' . $sansStack,
            'lato'         => 'Lato, ' . $sansStack,
            'roboto'       => 'Roboto, ' . $sansStack,
            'poppins'      => 'Poppins, ' . $sansStack,
            'manrope'      => 'Manrope, ' . $sansStack,
            'raleway'      => 'Raleway, ' . $sansStack,
            'montserrat'   => 'Montserrat, ' . $sansStack,
            'playfair-display' => '"Playfair Display", Georgia, "Times New Roman", serif',

            // Stats / KPI
            'jetbrains-mono'   => '"JetBrains Mono", ' . $monoStack,
            'roboto-mono'      => '"Roboto Mono", ' . $monoStack,
            'roboto-condensed' => '"Roboto Condensed", ' . $sansStack,
            'oswald'           => 'Oswald, ' . $sansStack,
            'system-mono'      => $monoStack,

            default => match ($slot) {
                'heading' => 'Nunito, ' . $sansStack,
                'stats'   => '"JetBrains Mono", ' . $monoStack,
                default   => '"Nunito Sans", ' . $sansStack,
            },
        };
    }

    /**
     * Liefert die `family=…&family=…`-URL-Stückliste für Google Fonts –
     * nur tatsächlich gewählte Familien werden geladen. System-Stacks
     * (z. B. `system-mono`) werden übersprungen.
     */
    private function googleFontFamilies(): string
    {
        $base    = (string) $this->customizerGet('typography', 'font_family_base',    'nunito-sans');
        $heading = (string) $this->customizerGet('typography', 'font_family_heading', 'nunito');
        $stats   = (string) $this->customizerGet('typography', 'font_family_stats',   'jetbrains-mono');

        $needed = [];
        foreach ([$base, $heading, $stats] as $choice) {
            switch ($choice) {
                case 'nunito-sans':
                    $needed['Nunito Sans']      = 'Nunito+Sans:wght@400;600;700';
                    break;
                case 'nunito':
                    $needed['Nunito']           = 'Nunito:wght@400;600;700;800';
                    break;
                case 'inter':
                    $needed['Inter']            = 'Inter:wght@400;500;600;700';
                    break;
                case 'open-sans':
                    $needed['Open Sans']        = 'Open+Sans:wght@400;600;700';
                    break;
                case 'lato':
                    $needed['Lato']             = 'Lato:wght@400;700';
                    break;
                case 'roboto':
                    $needed['Roboto']           = 'Roboto:wght@400;500;700';
                    break;
                case 'poppins':
                    $needed['Poppins']          = 'Poppins:wght@500;600;700';
                    break;
                case 'manrope':
                    $needed['Manrope']          = 'Manrope:wght@500;600;700';
                    break;
                case 'raleway':
                    $needed['Raleway']          = 'Raleway:wght@500;600;700';
                    break;
                case 'montserrat':
                    $needed['Montserrat']       = 'Montserrat:wght@500;600;700';
                    break;
                case 'playfair-display':
                    $needed['Playfair Display'] = 'Playfair+Display:wght@600;700';
                    break;
                case 'jetbrains-mono':
                    $needed['JetBrains Mono']   = 'JetBrains+Mono:wght@500;700';
                    break;
                case 'roboto-mono':
                    $needed['Roboto Mono']      = 'Roboto+Mono:wght@500;700';
                    break;
                case 'roboto-condensed':
                    $needed['Roboto Condensed'] = 'Roboto+Condensed:wght@500;700';
                    break;
                case 'oswald':
                    $needed['Oswald']           = 'Oswald:wght@500;700';
                    break;
                // 'system-mono' bleibt System-Schrift.
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

PersonalFlow_Theme::instance();

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
     * Theme-lokaler Routen-Resolver. Kennt die gängigen PersonalFlow-Pfade,
     * fällt sonst auf SITE_URL + Pfad zurück.
     */
    function theme_route_url(string $name): string
    {
        $base = rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
        return match ($name) {
            'home'        => $base . '/',
            'search'      => $base . '/search',
            'login'       => $base . '/login',
            'register'    => $base . '/register',
            'member'      => $base . '/member',
            'jobs'        => $base . '/jobs',
            'candidates'  => $base . '/kandidaten',
            'employers'   => $base . '/arbeitgeber',
            'pipeline'    => $base . '/pipeline',
            'pricing'     => $base . '/preise',
            'imprint'     => $base . '/impressum',
            'privacy'     => $base . '/datenschutz',
            'terms'       => $base . '/agb',
            default       => $base . '/' . ltrim($name, '/'),
        };
    }
}

if (!function_exists('theme_nav_menu')) {
    /**
     * Render a registered navigation menu as a <ul> list.
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(PERSONALFLOW_THEME_SLUG) . '/header.php';
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
            $tp = \CMS\ThemeManager::instance()->getThemePath(PERSONALFLOW_THEME_SLUG) . '/footer.php';
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

if (!function_exists('pf_get_setting')) {
    /**
     * Shorthand for ThemeCustomizer::get() with safe fallback.
     */
    function pf_get_setting(string $section, string $key, mixed $default = ''): mixed
    {
        try {
            return \CMS\Services\ThemeCustomizer::instance()->get($section, $key, $default);
        } catch (\Throwable) {
            return $default;
        }
    }
}

if (!function_exists('pf_get_flash')) {
    /**
     * Return and clear a session flash message array or null.
     *
     * @return array{type:string,message:string}|null
     */
    function pf_get_flash(): ?array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!empty($_SESSION['pf_flash']) && is_array($_SESSION['pf_flash'])) {
            $flash = $_SESSION['pf_flash'];
            unset($_SESSION['pf_flash']);
            return $flash;
        }
        return null;
    }
}

if (!function_exists('pf_set_flash')) {
    function pf_set_flash(string $type, string $message): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $_SESSION['pf_flash'] = ['type' => $type, 'message' => $message];
    }
}

if (!function_exists('pf_href')) {
    /**
     * Normalize a link target into an output-safe URL with site host.
     */
    function pf_href(string $target): string
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

if (!function_exists('pf_site_url')) {
    function pf_site_url(): string
    {
        return rtrim((string) (defined('SITE_URL') ? SITE_URL : ''), '/');
    }
}

if (!function_exists('pf_site_title')) {
    function pf_site_title(): string
    {
        try {
            return (string) \CMS\ThemeManager::instance()->getSiteTitle();
        } catch (\Throwable) {
            return 'PersonalFlow';
        }
    }
}

if (!function_exists('pf_body_class')) {
    /**
     * Server-render the <body> class string – kein JS-Mutation der Klasse vor Paint.
     */
    function pf_body_class(string ...$extra): string
    {
        $classes = ['pf-body'];

        $sticky = filter_var(
            pf_get_setting('layout', 'enable_sticky_header', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$sticky) {
            $classes[] = 'no-sticky-header';
        }

        $shadow = filter_var(
            pf_get_setting('header', 'show_header_shadow', true),
            FILTER_VALIDATE_BOOLEAN
        );
        if (!$shadow) {
            $classes[] = 'no-header-shadow';
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

if (!function_exists('pf_safe_headline')) {
    /**
     * Erlaubt ausschliesslich `<span class="hl">…</span>` als kontrolliertes
     * Inline-Markup in Überschriften. Alles andere wird HTML-escaped.
     */
    function pf_safe_headline(string $raw): string
    {
        $escaped = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
        return str_replace(
            ['&lt;span class=&quot;hl&quot;&gt;', '&lt;/span&gt;'],
            ['<span class="pf-hl">', '</span>'],
            $escaped
        );
    }
}

if (!function_exists('pf_stage_label')) {
    /**
     * Resolve a pipeline-stage key to its Customizer-driven German label.
     */
    function pf_stage_label(string $stage): string
    {
        $map = [
            'applied'   => (string) pf_get_setting('pipeline', 'stage_applied_label',   'Beworben'),
            'screened'  => (string) pf_get_setting('pipeline', 'stage_screened_label',  'Screening'),
            'interview' => (string) pf_get_setting('pipeline', 'stage_interview_label', 'Interview'),
            'offer'     => (string) pf_get_setting('pipeline', 'stage_offer_label',     'Angebot'),
            'hired'     => (string) pf_get_setting('pipeline', 'stage_hired_label',     'Eingestellt'),
            'archived'  => (string) pf_get_setting('pipeline', 'stage_archived_label',  'Archiviert'),
        ];
        return $map[$stage] ?? ucfirst($stage);
    }
}
