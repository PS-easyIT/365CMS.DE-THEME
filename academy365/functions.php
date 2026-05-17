<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

if (!defined('ACADEMY365_THEME_VERSION')) {
    define('ACADEMY365_THEME_VERSION', '3.0.1');
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
        $str  = static function (string $section, string $key, string $default) use ($c, $safe): string {
            return $safe((string) $c->get($section, $key, $default));
        };
        $num  = static function (string $section, string $key, string $default) use ($c): string {
            $v = $c->get($section, $key, $default);
            if (is_numeric($v)) {
                return (string) $v;
            }
            $n = preg_replace('/[^0-9.\-]/', '', (string) $v) ?? '';
            return $n !== '' ? $n : $default;
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
        $courseColumns = (int) $num('layout', 'course_grid_columns', '3');
        if ($courseColumns < 2) { $courseColumns = 2; }
        if ($courseColumns > 4) { $courseColumns = 4; }
        // Card minimum width derived from column setting (visual fallback for narrow viewports)
        $cardMin = match ($courseColumns) { 2 => 320, 4 => 240, default => 280 };

        echo '<style id="ac-custom-vars">:root{';
        // Colors
        echo '--primary-color:'    . $str('colors', 'primary_color',   '#7c3aed') . ';';
        echo '--primary-dark:'     . $str('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--secondary-color:'  . $str('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--accent-color:'     . $str('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--bg-primary:'       . $str('colors', 'bg_color',        '#faf5ff') . ';';
        echo '--bg-secondary:'     . $str('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--bg-card:'          . $str('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--text-primary:'     . $str('colors', 'text_color',      '#1e1b4b') . ';';
        echo '--link-color:'       . $str('colors', 'link_color',      '#7c3aed') . ';';
        echo '--muted-color:'      . $str('colors', 'muted_color',     '#6b7280') . ';';
        echo '--border-color:'     . $str('colors', 'border_color',    '#e9d5ff') . ';';
        echo '--badge-free:'       . $str('colors', 'success_color',   '#10b981') . ';';
        echo '--badge-cert:'       . $str('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--badge-new:'        . $str('colors', 'progress_color',  '#3b82f6') . ';';
        echo '--badge-best:'       . $str('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--progress-color:'   . $str('colors', 'progress_color',  '#3b82f6') . ';';
        echo '--success-color:'    . $str('colors', 'success_color',   '#10b981') . ';';
        echo '--cat-tech:'         . $str('colors', 'category_tech',     '#0ea5e9') . ';';
        echo '--cat-business:'     . $str('colors', 'category_business', '#059669') . ';';
        echo '--cat-design:'       . $str('colors', 'category_design',   '#ec4899') . ';';
        echo '--cat-language:'     . $str('colors', 'category_language', '#f59e0b') . ';';
        // Header & Footer chrome
        echo '--header-bg:'        . $str('header', 'header_bg_color',     '#ffffff') . ';';
        echo '--header-text:'      . $str('header', 'header_text_color',   '#1e1b4b') . ';';
        echo '--header-height:'    . $num('header', 'header_height',       '72')      . 'px;';
        echo '--logo-max-height:'  . $num('header', 'logo_max_height',     '44')      . 'px;';
        echo '--footer-bg:'        . $str('footer', 'footer_bg_color',     '#1e1b4b') . ';';
        echo '--footer-text:'      . $str('footer', 'footer_text_color',   '#c4b5fd') . ';';
        echo '--footer-link:'      . $str('footer', 'footer_link_color',   '#ede9fe') . ';';
        // Typography
        echo '--font-body:'        . $bodyFont    . ';';
        echo '--font-heading:'     . $headingFont . ';';
        echo '--font-ui:'          . $displayFont . ';';
        echo '--font-size-base:'   . $num('typography', 'font_size_base',   '16')   . 'px;';
        echo '--line-height-base:' . $num('typography', 'line_height_base', '1.7') . ';';
        echo '--font-weight-heading:' . $num('typography', 'font_weight_heading', '700') . ';';
        // Layout
        echo '--container-max:'    . $num('layout', 'container_width',  '1240') . 'px;';
        echo '--content-padding:'  . $num('layout', 'content_padding',  '2')    . 'rem;';
        echo '--radius-md:'        . $num('layout', 'border_radius',    '12')   . 'px;';
        echo '--section-spacing:'  . $num('layout', 'section_spacing',  '4.5')  . 'rem;';
        echo '--course-grid-min:'  . $cardMin . 'px;';
        // Buttons
        echo '--btn-radius:'       . $num('buttons', 'button_border_radius', '8')   . 'px;';
        echo '--btn-padding-x:'    . $num('buttons', 'button_padding_x',    '1.75') . 'rem;';
        echo '--btn-padding-y:'    . $num('buttons', 'button_padding_y',    '0.75') . 'rem;';
        echo '--btn-weight:'       . $num('buttons', 'button_font_weight', '700') . ';';
        echo '--btn-transform:'    . $safe((string) $c->get('buttons', 'button_transform', 'none')) . ';';
        // Tokens used by focus rings & sticky header
        echo '--focus-ring:'       . $str('colors', 'accent_color', '#f59e0b') . ';';
        echo '--header-shadow:'    . ($headerShadow ? '0 1px 4px rgba(124,58,237,.08)' : 'none') . ';';
        echo '--header-position:'  . ($stickyHeader ? 'fixed' : 'absolute') . ';';
        echo '}';

        $custom = (string) $c->get('advanced', 'custom_css', '');
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
