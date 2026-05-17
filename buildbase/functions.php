<?php
declare(strict_types=1);

/**
 * BuildBase Theme – Bootstrap & Customizer Integration
 *
 * Construction / trade / project-delivery theme for 365CMS v3.x.x on PHP 8.4.
 *
 * @package BuildBase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('BUILDBASE_THEME_VERSION')) {
    define('BUILDBASE_THEME_VERSION', '3.0.1');
}

final class BuildBase_Theme {
    private static ?self $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->registerHooks();
    }

    private function registerHooks(): void {
        \CMS\Hooks::addAction('head',          [$this, 'enqueueStyles'],            1);
        \CMS\Hooks::addAction('head',          [$this, 'outputGoogleFonts'],        5);
        \CMS\Hooks::addAction('head',          [$this, 'outputCustomStyles'],       15);
        \CMS\Hooks::addAction('before_footer', [$this, 'outputNavigationScript'],   99);
        \CMS\Hooks::addAction('cms_init',      [$this, 'registerNavMenus'],         10);
        \CMS\Hooks::addAction('init',          [$this, 'registerNavMenus'],         10);
    }

    public function registerNavMenus(): void {
        \CMS\ThemeManager::instance()->registerMenuLocation('primary-nav', 'Hauptmenü');
        \CMS\ThemeManager::instance()->registerMenuLocation('footer-nav',  'Fußzeilen-Navigation');
        \CMS\ThemeManager::instance()->registerMenuLocation('footer-legal','Rechtliche Links');
    }

    public function enqueueStyles(): void {
        $themeUrl = (string) \CMS\ThemeManager::instance()->getThemeUrl('buildbase');
        $href = htmlspecialchars(
            rtrim($themeUrl, '/') . '/style.css?v=' . BUILDBASE_THEME_VERSION,
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<link rel="preload" href="' . $href . '" as="style">' . "\n";
        echo '<link rel="stylesheet" href="' . $href . '">' . "\n";
    }

    public function outputGoogleFonts(): void {
        $fonts = 'Roboto:wght@400;500;700&family=Roboto+Condensed:wght@600;700'
               . '&family=Oswald:wght@500;700'
               . '&family=Open+Sans:wght@400;600;700'
               . '&family=Lato:wght@400;700'
               . '&family=Inter:wght@400;600;700'
               . '&family=Bebas+Neue';
        $url = 'https://fonts.googleapis.com/css2?family=' . $fonts . '&display=swap';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function mapFontChoice(string $fontChoice, string $fallback): string {
        return match ($fontChoice) {
            'roboto'           => 'Roboto, sans-serif',
            'open-sans'        => '"Open Sans", sans-serif',
            'lato'             => 'Lato, sans-serif',
            'inter'            => 'Inter, sans-serif',
            'roboto-condensed' => '"Roboto Condensed", sans-serif',
            'bebas-neue'       => '"Bebas Neue", sans-serif',
            'oswald'           => 'Oswald, sans-serif',
            default            => $fallback,
        };
    }

    private function sanitizeCustomCss(string $raw): string {
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return '';
        }
        // Protect against `</style>` injection inside the inline tag.
        return (string) preg_replace('#</\s*style\s*>#i', '', $trimmed);
    }

    public function outputCustomStyles(): void {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
        } catch (\Throwable $e) {
            return;
        }

        $p = fn(string $s, string $k, string $d): string =>
            htmlspecialchars((string) $c->get($s, $k, $d), ENT_QUOTES, 'UTF-8');

        $baseFontChoice    = (string) $c->get('typography', 'font_family_base',    'roboto');
        $headingFontChoice = (string) $c->get('typography', 'font_family_heading', 'roboto-condensed');
        $badgeFontChoice   = (string) $c->get('typography', 'font_family_badge',   'oswald');

        $baseFont    = htmlspecialchars($this->mapFontChoice($baseFontChoice,    'Roboto, sans-serif'),               ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingFontChoice, '"Roboto Condensed", sans-serif'),   ENT_QUOTES, 'UTF-8');
        $badgeFont   = htmlspecialchars($this->mapFontChoice($badgeFontChoice,   'Oswald, sans-serif'),               ENT_QUOTES, 'UTF-8');

        echo '<style id="bb-custom-vars">:root{';
        // Colors
        echo '--primary-color:'     . $p('colors', 'primary_color',    '#b45309') . ';';
        echo '--primary-dark:'      . $p('colors', 'secondary_color',  '#92400e') . ';';
        echo '--secondary-color:'   . $p('colors', 'secondary_color',  '#92400e') . ';';
        echo '--accent-color:'      . $p('colors', 'accent_color',     '#d97706') . ';';
        echo '--safety-color:'      . $p('colors', 'safety_color',     '#f59e0b') . ';';
        echo '--bg-primary:'        . $p('colors', 'bg_color',         '#fafaf9') . ';';
        echo '--bg-secondary:'      . $p('colors', 'section_dark_bg',  '#292524') . ';';
        echo '--bg-card:'           . $p('colors', 'card_bg_color',    '#ffffff') . ';';
        echo '--text-primary:'      . $p('colors', 'text_color',       '#1c1917') . ';';
        echo '--text-secondary:'    . $p('colors', 'muted_color',      '#78716c') . ';';
        echo '--muted-color:'       . $p('colors', 'muted_color',      '#78716c') . ';';
        echo '--border-color:'      . $p('colors', 'border_color',     '#d6d3d1') . ';';
        echo '--link-color:'        . $p('colors', 'link_color',       '#b45309') . ';';
        echo '--success-color:'     . $p('colors', 'success_color',    '#16a34a') . ';';
        echo '--error-color:'       . $p('colors', 'error_color',      '#dc2626') . ';';
        echo '--focus-ring:'        . $p('colors', 'accent_color',     '#d97706') . ';';

        // Header
        echo '--header-bg:'         . $p('header', 'header_bg_color',   '#292524') . ';';
        echo '--header-text:'       . $p('header', 'header_text_color', '#fafaf9') . ';';
        echo '--header-height:'     . $p('header', 'header_height',     '68') . 'px;';
        echo '--logo-max-height:'   . $p('header', 'logo_max_height',   '44') . 'px;';

        // Footer
        echo '--footer-bg:'         . $p('footer', 'footer_bg_color',   '#1c1917') . ';';
        echo '--footer-text:'       . $p('footer', 'footer_text_color', '#a8a29e') . ';';
        echo '--footer-link:'       . $p('footer', 'footer_link_color', '#d6d3d1') . ';';

        // Layout
        echo '--container-max:'     . $p('layout', 'container_width',  '1200') . 'px;';
        echo '--content-padding:'   . $p('layout', 'content_padding',  '2')    . 'rem;';
        echo '--radius-sm:'         . $p('layout', 'border_radius',    '4')    . 'px;';
        echo '--section-spacing:'   . $p('layout', 'section_spacing',  '4')    . 'rem;';

        // Hero gradient
        echo '--hero-gradient-start:' . $p('build_hero', 'hero_gradient_start', '#292524') . ';';
        echo '--hero-gradient-end:'   . $p('build_hero', 'hero_gradient_end',   '#44403c') . ';';

        // Buttons
        echo '--btn-radius:'         . $p('buttons', 'button_border_radius', '4')    . 'px;';
        echo '--btn-padding-x:'      . $p('buttons', 'button_padding_x',     '1.75') . 'rem;';
        echo '--btn-padding-y:'      . $p('buttons', 'button_padding_y',     '0.75') . 'rem;';
        echo '--btn-font-weight:'    . $p('buttons', 'button_font_weight',   '700')  . ';';
        echo '--btn-transform:'      . $p('buttons', 'button_transform',     'uppercase') . ';';

        // Typography
        echo '--font-body:'          . $baseFont    . ';';
        echo '--font-heading:'       . $headingFont . ';';
        echo '--font-condensed:'     . $badgeFont   . ';';
        echo '--font-size-base:'     . $p('typography', 'font_size_base',     '16')  . 'px;';
        echo '--line-height-base:'   . $p('typography', 'line_height_base',   '1.6') . ';';
        echo '--font-weight-heading:'. $p('typography', 'font_weight_heading','800') . ';';
        echo '}';

        $custom = (string) $c->get('advanced', 'custom_css', '');
        $safeCustom = $this->sanitizeCustomCss($custom);
        if ($safeCustom !== '') {
            echo $safeCustom;
        }
        echo '</style>' . "\n";
    }

    public function outputNavigationScript(): void {
        $themeUrl = (string) \CMS\ThemeManager::instance()->getThemeUrl('buildbase');
        $src = htmlspecialchars(
            rtrim($themeUrl, '/') . '/js/navigation.js?v=' . BUILDBASE_THEME_VERSION,
            ENT_QUOTES,
            'UTF-8'
        );
        echo '<script src="' . $src . '" defer></script>' . "\n";
    }
}

BuildBase_Theme::instance();
