<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

final class LogiLink_Theme {
    private static ?self $instance = null;
    public static function instance(): self { if (self::$instance === null) self::$instance = new self(); return self::$instance; }
    private function __construct() { $this->registerHooks(); }
    private function registerHooks(): void {
        \CMS\Hooks::addAction('head',          [$this, 'outputGoogleFonts'],      5);
        \CMS\Hooks::addAction('head',          [$this, 'outputCustomStyles'],    15);
        \CMS\Hooks::addAction('before_footer', [$this, 'outputNavigationScript'],99);
        \CMS\Hooks::addAction('cms_init',      [$this, 'registerNavMenus'],      10);
        \CMS\Hooks::addAction('init',          [$this, 'registerNavMenus'],      10);
    }
    public function registerNavMenus(): void {
        \CMS\ThemeManager::instance()->registerMenuLocation('primary-nav', 'Hauptmenü');
        \CMS\ThemeManager::instance()->registerMenuLocation('footer-nav',  'Fußzeilen-Navigation');
        \CMS\ThemeManager::instance()->registerMenuLocation('footer-legal','Rechtliche Links');
    }
    public function outputGoogleFonts(): void {
        $fonts = 'Inter:wght@400;500;600;700;800&family=Roboto+Condensed:wght@600;700&family=JetBrains+Mono:wght@400;600';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        $url = 'https://fonts.googleapis.com/css2?family=' . $fonts . '&display=swap';
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function mapFontChoice(string $fontChoice, string $fallback): string
    {
        return match ($fontChoice) {
            'inter' => 'Inter, sans-serif',
            'roboto' => 'Roboto, sans-serif',
            'open-sans' => '"Open Sans", sans-serif',
            'lato' => 'Lato, sans-serif',
            'roboto-condensed' => '"Roboto Condensed", sans-serif',
            'montserrat' => 'Montserrat, sans-serif',
            'barlow-condensed' => '"Barlow Condensed", sans-serif',
            'jetbrains-mono' => '"JetBrains Mono", monospace',
            'roboto-mono' => '"Roboto Mono", monospace',
            'fira-code' => '"Fira Code", monospace',
            'monospace' => 'monospace',
            default => $fallback,
        };
    }
    public function outputCustomStyles(): void {
        try { $c = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable $e) { return; }
        $p = fn(string $s, string $k, string $d) => htmlspecialchars((string) $c->get($s, $k, $d), ENT_QUOTES, 'UTF-8');
        $baseFontChoice = (string) $c->get('typography', 'font_family_base', 'inter');
        $headingFontChoice = (string) $c->get('typography', 'font_family_heading', 'roboto-condensed');
        $dataFontChoice = (string) $c->get('typography', 'font_family_data', 'jetbrains-mono');
        $baseFont = htmlspecialchars($this->mapFontChoice($baseFontChoice, 'Inter, sans-serif'), ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingFontChoice, '"Roboto Condensed", sans-serif'), ENT_QUOTES, 'UTF-8');
        $dataFont = htmlspecialchars($this->mapFontChoice($dataFontChoice, '"JetBrains Mono", monospace'), ENT_QUOTES, 'UTF-8');
        echo '<style id="ll-custom-vars">:root{';
        echo '--primary-color:'    . $p('colors', 'primary_color',   '#0284c7') . ';';
        echo '--primary-dark:'     . $p('colors', 'secondary_color', '#0369a1') . ';';
        echo '--secondary-color:'  . $p('colors', 'dark_bg_color', '#0c1a2e') . ';';
        echo '--accent-color:'     . $p('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--bg-primary:'       . $p('colors', 'bg_color',      '#f0f9ff') . ';';
        echo '--bg-secondary:'     . $p('colors', 'card_bg_color',    '#ffffff') . ';';
        echo '--text-primary:'     . $p('colors', 'text_color',    '#0f172a') . ';';
        echo '--muted-color:'      . $p('colors', 'muted_color',     '#64748b') . ';';
        echo '--border-color:'     . $p('colors', 'border_color',    '#bae6fd') . ';';
        echo '--status-warehouse:' . $p('colors', 'status_warehouse', '#94a3b8') . ';';
        echo '--status-picked:'    . $p('colors', 'status_picked',    '#f59e0b') . ';';
        echo '--status-transit:'   . $p('colors', 'status_transit',   '#8b5cf6') . ';';
        echo '--status-delivered:' . $p('colors', 'status_delivered', '#16a34a') . ';';
        echo '--status-delayed:'   . $p('colors', 'status_delayed',   '#ef4444') . ';';
        echo '--status-returned:'  . $p('colors', 'status_returned',  '#64748b') . ';';
        echo '--font-body:'        . $baseFont . ';';
        echo '--font-heading:'     . $headingFont . ';';
        echo '--font-mono:'        . $dataFont . ';';
        echo '--header-height:'    . $p('header', 'header_height', '64') . 'px;';
        echo '--focus-ring:'       . $p('colors', 'accent_color', '#f59e0b') . ';';
        echo '}';
        $custom = $c->get('advanced', 'custom_css', '');
        if ($custom && trim($custom) !== '') echo $custom;
        echo '</style>' . "\n";
    }
    public function outputNavigationScript(): void {
        $url = \CMS\ThemeManager::instance()->getThemeUrl('logilink');
        echo '<script src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '/js/navigation.js" defer></script>' . "\n";
    }
}
LogiLink_Theme::instance();
