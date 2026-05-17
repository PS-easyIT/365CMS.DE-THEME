<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

final class Academy365_Theme {
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
        $fonts = 'Open+Sans:wght@400;600;700&family=Raleway:wght@700;800;900&family=Plus+Jakarta+Sans:wght@400;500;700';
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        $url = 'https://fonts.googleapis.com/css2?family=' . $fonts . '&display=swap';
        echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    private function mapFontChoice(string $fontChoice, string $fallback): string
    {
        return match ($fontChoice) {
            'open-sans' => '"Open Sans", sans-serif',
            'inter' => 'Inter, sans-serif',
            'lato' => 'Lato, sans-serif',
            'nunito-sans' => '"Nunito Sans", sans-serif',
            'raleway' => 'Raleway, sans-serif',
            'poppins' => 'Poppins, sans-serif',
            'nunito' => 'Nunito, sans-serif',
            'montserrat' => 'Montserrat, sans-serif',
            'plus-jakarta-sans' => '"Plus Jakarta Sans", sans-serif',
            default => $fallback,
        };
    }
    public function outputCustomStyles(): void {
        try { $c = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable $e) { return; }
        $p = fn(string $s, string $k, string $d) => htmlspecialchars((string) $c->get($s, $k, $d), ENT_QUOTES, 'UTF-8');
        $bodyFontChoice = (string) $c->get('typography', 'font_family_base', 'open-sans');
        $headingFontChoice = (string) $c->get('typography', 'font_family_heading', 'raleway');
        $displayFontChoice = (string) $c->get('typography', 'font_family_display', 'plus-jakarta-sans');
        $bodyFont = htmlspecialchars($this->mapFontChoice($bodyFontChoice, '"Open Sans", sans-serif'), ENT_QUOTES, 'UTF-8');
        $headingFont = htmlspecialchars($this->mapFontChoice($headingFontChoice, 'Raleway, sans-serif'), ENT_QUOTES, 'UTF-8');
        $displayFont = htmlspecialchars($this->mapFontChoice($displayFontChoice, '"Plus Jakarta Sans", sans-serif'), ENT_QUOTES, 'UTF-8');
        echo '<style id="ac-custom-vars">:root{';
        echo '--primary-color:'   . $p('colors', 'primary_color',   '#7c3aed') . ';';
        echo '--primary-dark:'    . $p('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--secondary-color:' . $p('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--accent-color:'    . $p('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--bg-primary:'      . $p('colors', 'bg_color',        '#faf5ff') . ';';
        echo '--bg-secondary:'    . $p('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--bg-card:'         . $p('colors', 'card_bg_color',   '#ffffff') . ';';
        echo '--text-primary:'    . $p('colors', 'text_color',      '#1e1b4b') . ';';
        echo '--muted-color:'     . $p('colors', 'muted_color',     '#6b7280') . ';';
        echo '--border-color:'    . $p('colors', 'border_color',    '#e9d5ff') . ';';
        echo '--badge-free:'      . $p('colors', 'success_color',   '#10b981') . ';';
        echo '--badge-cert:'      . $p('colors', 'accent_color',    '#f59e0b') . ';';
        echo '--badge-new:'       . $p('colors', 'progress_color',  '#3b82f6') . ';';
        echo '--badge-best:'      . $p('colors', 'secondary_color', '#5b21b6') . ';';
        echo '--cat-tech:'        . $p('colors', 'category_tech',     '#0ea5e9') . ';';
        echo '--cat-business:'    . $p('colors', 'category_business', '#059669') . ';';
        echo '--cat-design:'      . $p('colors', 'category_design',   '#ec4899') . ';';
        echo '--cat-language:'    . $p('colors', 'category_language', '#f59e0b') . ';';
        echo '--font-body:'       . $bodyFont . ';';
        echo '--font-heading:'    . $headingFont . ';';
        echo '--font-ui:'         . $displayFont . ';';
        echo '--header-height:'   . $p('header', 'header_height', '72') . 'px;';
        echo '--focus-ring:'      . $p('colors', 'accent_color', '#f59e0b') . ';';
        echo '}';
        $custom = $c->get('advanced', 'custom_css', '');
        if ($custom && trim($custom) !== '') echo $custom;
        echo '</style>' . "\n";
    }
    public function outputNavigationScript(): void {
        $url = \CMS\ThemeManager::instance()->getThemeUrl('academy365');
        echo '<script src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '/js/navigation.js" defer></script>' . "\n";
    }
}
Academy365_Theme::instance();
