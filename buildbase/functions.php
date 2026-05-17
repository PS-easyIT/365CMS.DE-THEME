<?php
declare(strict_types=1);

/**
 * BuildBase Theme – Bootstrap & Customizer Integration
 *
 * @package BuildBase_Theme
 */

if (!defined('ABSPATH')) {
    exit;
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

    public function outputGoogleFonts(): void {
        $fonts = 'Roboto:wght@400;500;700&family=Roboto+Condensed:wght@600;700&family=Oswald:wght@500;700';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        $url = 'https://fonts.googleapis.com/css2?family=' . $fonts . '&display=swap';
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function mapFontChoice(string $fontChoice, string $fallback): string
    {
        return match ($fontChoice) {
            'roboto' => 'Roboto, sans-serif',
            'open-sans' => '"Open Sans", sans-serif',
            'lato' => 'Lato, sans-serif',
            'inter' => 'Inter, sans-serif',
            'roboto-condensed' => '"Roboto Condensed", sans-serif',
            'bebas-neue' => '"Bebas Neue", sans-serif',
            'oswald' => 'Oswald, sans-serif',
            default => $fallback,
        };
    }

    public function outputCustomStyles(): void {
        try {
            $c = \CMS\Services\ThemeCustomizer::instance();
        } catch (\Throwable $e) {
            return;
        }
        $p = fn(string $s, string $k, string $d) => htmlspecialchars((string) $c->get($s, $k, $d), ENT_QUOTES, 'UTF-8');
        $baseFontChoice = (string) $c->get('typography', 'font_family_base', 'roboto');
        $headingFontChoice = (string) $c->get('typography', 'font_family_heading', 'roboto-condensed');
        $badgeFontChoice = (string) $c->get('typography', 'font_family_badge', 'oswald');
        $baseFont = htmlspecialchars($this->mapFontChoice($baseFontChoice, 'Roboto, sans-serif'), ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingFontChoice, '"Roboto Condensed", sans-serif'), ENT_QUOTES, 'UTF-8');
        $badgeFont = htmlspecialchars($this->mapFontChoice($badgeFontChoice, 'Oswald, sans-serif'), ENT_QUOTES, 'UTF-8');

        echo '<style id="bb-custom-vars">:root{';
        echo '--primary-color:'   . $p('colors', 'primary_color',    '#b45309') . ';';
        echo '--primary-dark:'    . $p('colors', 'secondary_color',  '#92400e') . ';';
        echo '--secondary-color:' . $p('colors', 'secondary_color',  '#92400e') . ';';
        echo '--accent-color:'    . $p('colors', 'accent_color',     '#d97706') . ';';
        echo '--bg-primary:'      . $p('colors', 'bg_color',         '#fafaf9') . ';';
        echo '--bg-secondary:'    . $p('colors', 'section_dark_bg',  '#292524') . ';';
        echo '--bg-card:'         . $p('colors', 'card_bg_color',    '#ffffff') . ';';
        echo '--text-primary:'    . $p('colors', 'text_color',       '#1c1917') . ';';
        echo '--text-secondary:'  . $p('colors', 'muted_color',      '#78716c') . ';';
        echo '--muted-color:'     . $p('colors', 'muted_color',      '#78716c') . ';';
        echo '--border-color:'    . $p('colors', 'border_color',     '#d6d3d1') . ';';
        echo '--font-body:'       . $baseFont . ';';
        echo '--font-heading:'    . $headingFont . ';';
        echo '--font-condensed:'  . $badgeFont . ';';
        echo '--header-height:'   . $p('header', 'header_height', '68') . 'px;';
        echo '--focus-ring:'      . $p('colors', 'accent_color', '#d97706') . ';';
        echo '}';
        $custom = $c->get('advanced', 'custom_css', '');
        if ($custom && trim($custom) !== '') echo $custom;
        echo '</style>' . "\n";
    }

    public function outputNavigationScript(): void {
        $themeUrl = \CMS\ThemeManager::instance()->getThemeUrl('buildbase');
        echo '<script src="' . htmlspecialchars($themeUrl, ENT_QUOTES, 'UTF-8') . '/js/navigation.js" defer></script>' . "\n";
    }
}

BuildBase_Theme::instance();
