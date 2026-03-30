<?php
declare(strict_types=1);

/**
 * IT Expert Network Theme - Functions
 *
 * Theme-spezifische Funktionen, Hooks und Helper-Klassen für das CMSv2-System.
 *
 * @package IT_Expert_Network_Theme
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('THEME_VERSION', '3.4.11');
define('THEME_DIR', THEME_PATH . '365Network/');
define('THEME_URL_BASE', \CMS\ThemeManager::instance()->getThemeUrl());

/**
 * Theme Bootstrap - Hooks & Assets registrieren
 */
class IT_Expert_Network_Theme
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
        // Assets im <head> einbinden – Priority 15: NACH Plugin-CSS (10) laden,
        // damit Theme-Overrides für Plugin-Elemente (Experts, Events, …) greifen.
        \CMS\Hooks::addAction('head', [$this, 'enqueueStyles'], 15);
        \CMS\Hooks::addAction('head', [$this, 'outputMetaTags']);
        \CMS\Hooks::addAction('head', [$this, 'outputGoogleFonts'], 5);
        \CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'], 20);
        \CMS\Hooks::addAction('head', [$this, 'outputDerivedStyles'], 25);

        // Performance Hints
        \CMS\Hooks::addAction('head', [$this, 'outputPreconnect'], 1);
        
        // Custom Header Code (SEO/Tracking)
        \CMS\Hooks::addAction('head', [$this, 'outputCustomHeaderCode'], 99);
        
        // Custom Head Code from Customizer (Tracking etc.)
        \CMS\Hooks::addAction('head', [$this, 'outputCustomizerHeadCode'], 98);

        // Footer Scripts
        \CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts']);
        
        // Cookie Banner
        \CMS\Hooks::addAction('before_footer', [$this, 'outputCookieBanner'], 10);
        
        // Custom Footer Code from Customizer
        \CMS\Hooks::addAction('body_end', [$this, 'outputCustomizerFooterCode'], 50);

        // Theme-Menüpositionen registrieren
        \CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

        // Standard-Menüeinträge beim ersten Start automatisch anlegen
        \CMS\Hooks::addAction('cms_init', [$this, 'seedDefaultMenus']);

        // ── Homepage: Default-Sidebar-Widgets registrieren ──────────
        // Widgets lesen Customizer-Einstellungen aus der Kategorie 'sidebar'.
        // Plugins können diese per removeAction() ersetzen oder eigene
        // per addAction('home_sidebar_widget', ...) mit passender Priorität einhängen.
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderSidebarBookingWidget'],  10);
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderSidebarFeedWidget'],     20);
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderSidebarSpeakersWidget'], 25);
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderSidebarJobWidget'],      30);
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderSidebarBlogWidget'],     40);
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderSidebarCustomHtml'],     90);

        // Sidebar CSS-Variablen aus Customizer injizieren
        \CMS\Hooks::addAction('head', [$this, 'outputSidebarStyles'], 26);
    }
    
    /**
     * Output Custom Header Code from SEO Settings
     */
    public function outputCustomHeaderCode(): void
    {
        $code = \CMS\Services\SEOService::getInstance()->getCustomHeaderCode();
        if (!empty($code)) {
            echo "\n<!-- Custom Header Code -->\n";
            echo $code . "\n";
            echo "<!-- /Custom Header Code -->\n";
        }
    }

    /**
     * Cookie Banner einbinden (wenn aktiviert in Admin > Recht & Sicherheit > Cookie Managed)
     */
    public function outputCookieBanner(): void
    {
        $db = \CMS\Database::instance();
        // Check if cookie banner is enabled
        $enabled = $db->execute("SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = 'cookie_consent_enabled'")->fetch();
        if (!$enabled || $enabled->option_value !== '1') {
            return;
        }

        // Load settings
        $settings = [];
        $keys = ['cookie_banner_position', 'cookie_banner_text', 'cookie_accept_text', 'cookie_essential_text', 'cookie_policy_url', 'cookie_primary_color'];
        foreach ($keys as $k) {
            $row = $db->execute("SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = ?", [$k])->fetch();
            $settings[$k] = $row ? $row->option_value : '';
        }

        // Defaults
        $pos = $settings['cookie_banner_position'] === 'modal' ? 'modal' : 'bottom';
        $text = htmlspecialchars($settings['cookie_banner_text'] ?: 'Wir verwenden Cookies.', ENT_QUOTES, 'UTF-8');
        $btnAccept = htmlspecialchars($settings['cookie_accept_text'] ?: 'Akzeptieren', ENT_QUOTES, 'UTF-8');
        $btnEssential = htmlspecialchars($settings['cookie_essential_text'] ?: 'Nur Essenzielle', ENT_QUOTES, 'UTF-8');
        $linkPolicy = htmlspecialchars(theme_safe_url((string)($settings['cookie_policy_url'] ?: '#'), '#'), ENT_QUOTES, 'UTF-8');
        $color = htmlspecialchars($settings['cookie_primary_color'] ?: '#3b82f6', ENT_QUOTES, 'UTF-8');

        echo <<<HTML
        <div id="cms-cookie-banner"
             class="cms-cookie-banner cms-cookie-banner--{$pos}"
             data-cookie-banner
             data-cookie-accent="{$color}">
            <p class="cms-cookie-banner__text">{$text} <a href="{$linkPolicy}" class="cms-cookie-banner__link">Mehr erfahren</a></p>
            <div class="cms-cookie-banner__actions">
                <button class="cms-cookie-btn cms-cookie-btn--secondary" type="button" data-cookie-action="essential">{$btnEssential}</button>
                <button class="cms-cookie-btn cms-cookie-btn--primary" type="button" data-cookie-action="all">{$btnAccept}</button>
            </div>
        </div>
HTML;
    }

    /**
     * Theme-Menüpositionen registrieren
     */
    public function registerMenuLocations(array $locations): array
    {
        $locations[] = ['slug' => 'primary', 'label' => 'Hauptmenü (Header)'];
        $locations[] = ['slug' => 'mobile',  'label' => 'Mobiles Menü'];
        $locations[] = ['slug' => 'footer',  'label' => 'Footer-Navigation'];
        $locations[] = ['slug' => 'speaker', 'label' => 'Speaker-Menü'];
        return $locations;
    }

    /**
     * Standard-Menüeinträge beim ersten Start anlegen (nur wenn leer)
     * Andere Themes können dasselbe Muster verwenden.
     */
    public function seedDefaultMenus(): void
    {
        $themeManager = \CMS\ThemeManager::instance();

        $defaults = [
            'primary' => [
                ['label' => 'Startseite',  'url' => '/',          'target' => '_self'],
                ['label' => 'Experten',    'url' => '/experts',   'target' => '_self'],
                ['label' => 'Firmen',      'url' => '/companies', 'target' => '_self'],
                ['label' => 'Events',      'url' => '/events',    'target' => '_self'],
                ['label' => 'Speaker',     'url' => '/speakers',  'target' => '_self'],
                ['label' => 'Kontakt',     'url' => '/kontakt',   'target' => '_self'],
            ],
            'mobile'  => [
                ['label' => 'Startseite',  'url' => '/',          'target' => '_self'],
                ['label' => 'Experten',    'url' => '/experts',   'target' => '_self'],
                ['label' => 'Firmen',      'url' => '/companies', 'target' => '_self'],
                ['label' => 'Events',      'url' => '/events',    'target' => '_self'],
                ['label' => 'Speaker',     'url' => '/speakers',  'target' => '_self'],
                ['label' => 'Jobs',        'url' => '/jobs',      'target' => '_self'],
                ['label' => 'Feeds',       'url' => '/feeds',     'target' => '_self'],
                ['label' => 'Login',       'url' => '/login',     'target' => '_self'],
            ],
            'footer'  => [
                ['label' => 'Impressum',   'url' => '/impressum',   'target' => '_self'],
                ['label' => 'Datenschutz', 'url' => '/datenschutz', 'target' => '_self'],
                ['label' => 'Kontakt',     'url' => '/kontakt',     'target' => '_self'],
            ],
        ];

        foreach ($defaults as $location => $items) {
            // Nur anlegen wenn noch keine Einträge vorhanden
            if (empty($themeManager->getMenu($location))) {
                $themeManager->saveMenu($location, $items);
            }
        }
    }

    /**
     * Stylesheets einbinden
     */
    public function enqueueStyles(): void
    {
        $v = THEME_VERSION;
        $styleUrl = htmlspecialchars((string) (THEME_URL_BASE . '/style.css?v=' . $v), ENT_QUOTES, 'UTF-8');
        echo '<link rel="stylesheet" href="' . $styleUrl . '">' . "\n";
    }

    /**
     * JavaScript einbinden
     */
    public function enqueueScripts(): void
    {
        $v = THEME_VERSION;
        // Kein defer: Scripts stehen bereits am Ende des Body
        $navigationScriptUrl = htmlspecialchars((string) (THEME_URL_BASE . '/js/navigation.js?v=' . $v), ENT_QUOTES, 'UTF-8');
        $themeScriptUrl = htmlspecialchars((string) (THEME_URL_BASE . '/js/theme.js?v=' . $v), ENT_QUOTES, 'UTF-8');
        echo '<script src="' . $navigationScriptUrl . '"></script>' . "\n";
        echo '<script src="' . $themeScriptUrl . '"></script>' . "\n";
    }

    /**
     * Meta-Tags ausgeben
     */
    public function outputMetaTags(): void
    {
        $themeManager = \CMS\ThemeManager::instance();
        $siteDesc = htmlspecialchars($themeManager->getSiteDescription(), ENT_QUOTES, 'UTF-8');
        $siteTitle = htmlspecialchars($themeManager->getSiteTitle(), ENT_QUOTES, 'UTF-8');
        $siteUrl = htmlspecialchars(theme_safe_external_url((string) SITE_URL) ?: rtrim((string) SITE_URL, '/') . '/', ENT_QUOTES, 'UTF-8');

        echo '<meta name="description" content="' . $siteDesc . '">' . "\n";
        echo '<meta property="og:site_name" content="' . $siteTitle . '">' . "\n";
        echo '<meta property="og:url" content="' . $siteUrl . '">' . "\n";
        echo '<meta name="theme-color" content="#0c1526">' . "\n";
        echo '<meta name="robots" content="index,follow">' . "\n";
    }

    /**
     * Preconnect Hints für Performance
     */
    public function outputPreconnect(): void
    {
        // Don't preconnect to Google if we use local fonts
        $db = \CMS\Database::instance();
        $useLocal = $db->execute("SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = 'privacy_use_local_fonts'")->fetch();
        if ($useLocal && $useLocal->option_value === '1') {
            return;
        }

        echo '<link rel="preconnect" href="' . htmlspecialchars('https://fonts.googleapis.com', ENT_QUOTES, 'UTF-8') . '">' . "\n";
        echo '<link rel="dns-prefetch" href="' . htmlspecialchars('//fonts.googleapis.com', ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }

    /**
     * Google Fonts einbinden (basierend auf Typography-Einstellungen)
     */
    public function outputGoogleFonts(): void
    {
        // Check if local fonts are enabled
        $db = \CMS\Database::instance();
        $useLocal = $db->execute("SELECT option_value FROM {$db->getPrefix()}settings WHERE option_name = 'privacy_use_local_fonts'")->fetch();
        
        if ($useLocal && $useLocal->option_value === '1') {
            // Use local CSS if available
            if (file_exists(ASSETS_PATH . 'css/local-fonts.css')) {
                $localFontsUrl = function_exists('cms_asset_url')
                    ? cms_asset_url('css/local-fonts.css')
                    : SITE_URL . '/assets/css/local-fonts.css';
                echo '<link rel="stylesheet" href="' . htmlspecialchars($localFontsUrl, ENT_QUOTES, 'UTF-8') . '">' . "\n";
                // Preconnect not needed for local, but maybe keep for other external?
                // For now, we return, to avoid Google request
                return;
            }
        }

        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $typo = $customizer->getCategory('typography');

            $googleFonts = ['inter', 'roboto', 'open-sans', 'lato', 'montserrat', 'poppins', 'raleway'];
            $fontMap = [
                'inter'       => 'Inter:wght@400;500;600;700;800',
                'roboto'      => 'Roboto:wght@400;500;700',
                'open-sans'   => 'Open+Sans:wght@400;600;700',
                'lato'        => 'Lato:wght@400;700',
                'montserrat'  => 'Montserrat:wght@400;500;600;700',
                'poppins'     => 'Poppins:wght@400;500;600;700',
                'raleway'     => 'Raleway:wght@400;600;700',
            ];

            $fontsToLoad = [];
            $baseFont    = $typo['font_family_base']    ?? 'inter';
            $headingFont = $typo['font_family_heading'] ?? 'inter';

            if (in_array($baseFont, $googleFonts, true) && isset($fontMap[$baseFont])) {
                $fontsToLoad[$baseFont] = $fontMap[$baseFont];
            }
            if ($headingFont !== $baseFont && in_array($headingFont, $googleFonts, true) && isset($fontMap[$headingFont])) {
                $fontsToLoad[$headingFont] = $fontMap[$headingFont];
            }

            if (!empty($fontsToLoad)) {
                $families = implode('&family=', array_values($fontsToLoad));
                $url = 'https://fonts.googleapis.com/css2?family=' . $families . '&display=swap';
                echo '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . "\n";
            }
        } catch (\Throwable $e) {
            // Google Fonts nicht verfügbar – System-Fonts greifen
        }
    }

    /**
     * Custom CSS-Variablen aus DB-Einstellungen (ThemeCustomizer)
     */
    public function outputCustomStyles(): void
    {
        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $css = $customizer->generateCSS();

            if (trim($css) !== '') {
                echo '<style id="cms-theme-customizer">' . "\n" . $css . '</style>' . "\n";
            }
        } catch (\Throwable $e) {
            // ThemeCustomizer nicht verfügbar – Default CSS aus style.css greift
        }
    }

    /**
     * Derived CSS: Korrigiert Core-Mapping-Konflikte und erweitert um theme-spezifische Variablen.
     *
     * Problem: Core-ThemeCustomizer mappt:
     *   secondary_color → --secondary-color AND --primary-hover (Konflikt: Grau vs Navy)
     *   bg_color        → --background-color, --bg-secondary, --light-bg (Triple-Map, sollten verschieden sein)
     *
     * Lösung: Diese Methode läuft NACH generateCSS() und überschreibt die fehlerhaften Mappings
     * mit den korrekten Werten aus den theme-spezifischen Customizer-Keys.
     */
    public function outputDerivedStyles(): void
    {
        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $colors  = $customizer->getCategory('colors');
            $header  = $customizer->getCategory('header');
            $effects = $customizer->getCategory('effects');

            $css = "/* 365Network – Derived Overrides */\n:root {\n";

            // ── Fix 1: secondary_color → --primary-hover Korrektur ──
            // Core setzt --primary-hover auf den secondary_color Wert (Grau).
            // Wir überschreiben --primary-hover mit dem dedizierten primary_hover Key.
            $primaryHover = $colors['primary_hover'] ?? '#162040';
            $css .= "    --primary-hover: {$primaryHover};\n";

            // ── Fix 2: bg_color Triple-Mapping Korrektur ──
            // Core setzt --bg-secondary und --light-bg auf denselben Wert wie --background-color.
            // Wir überschreiben sie mit dem dedizierten bg_secondary Key.
            $bgSecondary = $colors['bg_secondary'] ?? '#f1f5f9';
            $css .= "    --bg-secondary: {$bgSecondary};\n";
            $css .= "    --light-bg: {$bgSecondary};\n";

            // ── Theme-spezifische Farb-Variablen (nicht im Core-Mapping) ──
            $primaryLight = $colors['primary_light'] ?? '#1a2a42';
            $css .= "    --primary-light: {$primaryLight};\n";

            $accentHover = $colors['accent_hover'] ?? '#a67a24';
            $css .= "    --accent-hover: {$accentHover};\n";

            $accentLight = $colors['accent_light'] ?? '#d4a84a';
            $css .= "    --accent-light: {$accentLight};\n";

            $headingColor = $colors['heading_color'] ?? '#0f172a';
            $css .= "    --heading-color: {$headingColor};\n";

            $textLight = $colors['text_light'] ?? '#e2e8f0';
            $css .= "    --text-light: {$textLight};\n";

            // ── Header-Akzentfarbe & Border ──
            $headerAccent = $header['header_accent_color'] ?? '#c8952e';
            $css .= "    --header-text-secondary: {$headerAccent};\n";
            $css .= "    --header-border: {$primaryLight};\n";

            // ── Netzwerk-Animation ──
            $animOpacity = (int)($effects['animation_opacity'] ?? 15);
            $css .= "    --network-animation-opacity: " . ($animOpacity / 100) . ";\n";

            $css .= "}\n";

            echo '<style id="cms-theme-derived">' . "\n" . $css . '</style>' . "\n";
        } catch (\Throwable $e) {
            // Fallback: style.css Defaults bleiben erhalten
        }
    }

    /**
     * Custom Head Code aus Customizer (Tracking-Scripts, zusätzliche Meta-Tags)
     */
    public function outputCustomizerHeadCode(): void
    {
        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $code = $customizer->get('advanced', 'custom_head_code', '');
            if (!empty(trim((string)$code))) {
                echo "\n<!-- Customizer Head Code -->\n";
                echo (string)$code . "\n";
            }
        } catch (\Throwable $e) {
            // Keine Ausgabe
        }
    }

    /**
     * Custom Footer Code aus Customizer (Analytics, Widgets)
     */
    public function outputCustomizerFooterCode(): void
    {
        try {
            $customizer = \CMS\Services\ThemeCustomizer::instance();
            $code = $customizer->get('advanced', 'custom_footer_code', '');
            if (!empty(trim((string)$code))) {
                echo "\n<!-- Customizer Footer Code -->\n";
                echo (string)$code . "\n";
            }
        } catch (\Throwable $e) {
            // Keine Ausgabe
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    // Homepage-Sidebar: Default-Widgets
    // Jedes Widget liest Customizer-Einstellungen (Kategorie: sidebar).
    // Plugins können diese Defaults per removeAction() entfernen
    // und ihre eigenen per addAction('home_sidebar_widget', ...) registrieren.
    // ═══════════════════════════════════════════════════════════════════

    /** Sidebar-Customizer-Setting lesen */
    private function getSidebarSetting(string $key, $default = '')
    {
        static $cache = null;
        if ($cache === null) {
            try {
                $cache = \CMS\Services\ThemeCustomizer::instance()->getCategory('sidebar');
            } catch (\Throwable $e) {
                $cache = [];
            }
        }
        return $cache[$key] ?? $default;
    }

    private function sidebarBool(string $key, bool $default = true): bool
    {
        return filter_var($this->getSidebarSetting($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Sidebar CSS-Variablen aus Customizer
     */
    public function outputSidebarStyles(): void
    {
        $bg      = $this->getSidebarSetting('sidebar_bg_color', '#ffffff');
        $border  = $this->getSidebarSetting('sidebar_border_color', '#e2e8f0');
        $radius  = $this->getSidebarSetting('sidebar_border_radius', 12);
        $padding = $this->getSidebarSetting('sidebar_padding', 1.25);
        $tSize   = $this->getSidebarSetting('sidebar_title_size', 1.0);
        $tColor  = $this->getSidebarSetting('sidebar_title_color', '#1e293b');
        $txtColor = $this->getSidebarSetting('sidebar_text_color', '#475569');
        $gap     = $this->getSidebarSetting('sidebar_gap', 1.25);
        $shadow  = $this->sidebarBool('sidebar_shadow', true);
        $width   = (int)$this->getSidebarSetting('sidebar_width', 340);

        $css = "/* 365Network – Sidebar Customizer */\n";
        $css .= ".dashboard-sidebar {\n";
        $css .= "    --sidebar-widget-bg: {$bg};\n";
        $css .= "    --sidebar-widget-border: {$border};\n";
        $css .= "    --sidebar-widget-radius: {$radius}px;\n";
        $css .= "    --sidebar-widget-padding: {$padding}rem;\n";
        $css .= "    --sidebar-title-size: {$tSize}rem;\n";
        $css .= "    --sidebar-title-color: {$tColor};\n";
        $css .= "    --sidebar-text-color: {$txtColor};\n";
        $css .= "    --sidebar-gap: {$gap}rem;\n";
        $css .= "    gap: var(--sidebar-gap);\n";
        if ($width !== 340) {
            $css .= "    width: {$width}px;\n";
            $css .= "    min-width: {$width}px;\n";
        }
        $css .= "}\n";
        $css .= ".dashboard-sidebar .sidebar-panel {\n";
        $css .= "    background: var(--sidebar-widget-bg);\n";
        $css .= "    border: 1px solid var(--sidebar-widget-border);\n";
        $css .= "    border-radius: var(--sidebar-widget-radius);\n";
        $css .= "    padding: var(--sidebar-widget-padding);\n";
        $css .= "    color: var(--sidebar-text-color);\n";
        if ($shadow) {
            $css .= "    box-shadow: 0 1px 4px rgba(0,0,0,.06);\n";
        }
        $css .= "}\n";
        $css .= ".dashboard-sidebar .sidebar-panel h3 {\n";
        $css .= "    font-size: var(--sidebar-title-size);\n";
        $css .= "    color: var(--sidebar-title-color);\n";
        $css .= "}\n";

        echo '<style id="cms-sidebar-customizer">' . "\n" . $css . '</style>' . "\n";
    }

    /**
     * Default-Widget: Buchungsportal (Placeholder)
     */
    public function renderSidebarBookingWidget(): void
    {
        if (!$this->sidebarBool('show_booking_widget', true)) {
            return;
        }
        $title = htmlspecialchars((string)$this->getSidebarSetting('booking_widget_title', '📅 Buchungsportal'), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="sidebar-panel widget-zone" data-widget="booking">
            <h3><?php echo $title; ?></h3>
            <p class="widget-zone__hint">
                Hier können Buchungs-Plugins Termine und Verfügbarkeiten anzeigen.
            </p>
            <div class="widget-zone__placeholder">
                <span class="widget-zone__icon">📅</span>
                <span class="widget-zone__text">Plugin-Slot: Buchung</span>
            </div>
        </div>
        <?php
    }

    /**
     * Default-Widget: Feed-Aggregator
     * Zeigt echte Feed-Beiträge aus dem cms-feed Plugin (oder Placeholder wenn nicht aktiv).
     */
    public function renderSidebarFeedWidget(): void
    {
        if (!$this->sidebarBool('show_feed_widget', true)) {
            return;
        }
        $title = htmlspecialchars((string)$this->getSidebarSetting('feed_widget_title', '📰 Feed-Aggregator'), ENT_QUOTES, 'UTF-8');
        $count = max(1, min(10, (int)$this->getSidebarSetting('feed_widget_count', 5)));

        $hasFeed = \CMS\PluginManager::instance()->isPluginActive('cms-feed');
        $items = [];

        if ($hasFeed) {
            try {
                $db = \CMS\Database::instance();
                $prefix = $db->prefix();
                $stmt = $db->execute(
                    "SELECT fi.id, fi.title, fi.link, fi.description, fi.image_url, fi.author, fi.pub_date
                     FROM {$prefix}feed_items fi
                     WHERE fi.is_hidden = 0
                     ORDER BY fi.pub_date DESC
                     LIMIT " . $count
                );
                $items = $stmt->fetchAll() ?: [];
            } catch (\Throwable $e) { /* feed_items ggf. nicht vorhanden */ }
        }
        ?>
        <div class="sidebar-panel" data-widget="feed">
            <h3><?php echo $title; ?></h3>
            <?php if (!empty($items)) : ?>
                <ul class="sidebar-feed-list">
                    <?php foreach ($items as $item) :
                        $iTitle = htmlspecialchars(_field($item, 'title', 'Beitrag'), ENT_QUOTES, 'UTF-8');
                        $iLink  = htmlspecialchars(theme_safe_external_url(_field($item, 'link', '#')) ?: '#', ENT_QUOTES, 'UTF-8');
                        $iDate  = _field($item, 'pub_date', '');
                        $iDateF = $iDate ? date('d.m.Y', strtotime($iDate)) : '';
                    ?>
                    <li class="sidebar-feed-list__item">
                        <a href="<?php echo $iLink; ?>" target="_blank" rel="noopener noreferrer"
                           class="sidebar-feed-list__link">
                            <?php echo $iTitle; ?>
                        </a>
                        <?php if ($iDateF) : ?>
                            <span class="sidebar-feed-list__meta">📅 <?php echo $iDateF; ?></span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <div class="widget-zone__placeholder">
                    <span class="widget-zone__icon">📰</span>
                    <span class="widget-zone__text"><?php echo $hasFeed ? 'Noch keine Feed-Beiträge vorhanden.' : 'Plugin-Slot: Feed'; ?></span>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Default-Widget: Featured Speaker
     * Zeigt aktuelle Speaker aus dem cms-speakers Plugin (oder Placeholder wenn nicht aktiv).
     */
    public function renderSidebarSpeakersWidget(): void
    {
        $hasSpeakers = \CMS\PluginManager::instance()->isPluginActive('cms-speakers');
        $items = [];

        if ($hasSpeakers) {
            try {
                $db = \CMS\Database::instance();
                $prefix = $db->prefix();
                $stmt = $db->execute(
                    "SELECT id, name, topics, avatar_url, total_events
                     FROM {$prefix}event_speakers
                     WHERE status = 'active'
                     ORDER BY total_events DESC, id DESC
                     LIMIT 4"
                );
                $items = $stmt->fetchAll() ?: [];
            } catch (\Throwable $e) { /* event_speakers ggf. nicht vorhanden */ }
        }

        if (!$hasSpeakers && empty($items)) {
            return; // Kein Widget wenn Plugin nicht aktiv
        }
        $speakersUrl = theme_route_url('speakers');
        ?>
        <div class="sidebar-panel" data-widget="speakers">
            <h3>🎤 Featured Speaker</h3>
            <?php if (!empty($items)) : ?>
                <ul class="sidebar-speaker-list">
                    <?php foreach ($items as $sp) :
                        $spName   = htmlspecialchars(_field($sp, 'name', 'Speaker'), ENT_QUOTES, 'UTF-8');
                        $spTopics = _field($sp, 'topics', '');
                        $spFirst  = $spTopics ? htmlspecialchars(explode(',', $spTopics)[0], ENT_QUOTES, 'UTF-8') : '';
                        $spEvents = (int)_field($sp, 'total_events', 0);
                        $spId     = (int)_field($sp, 'id', 0);
                        $spUrl    = htmlspecialchars(theme_route_url('speaker', ['id' => (string) $spId], [], $speakersUrl), ENT_QUOTES, 'UTF-8');
                    ?>
                    <li class="sidebar-speaker-list__item">
                        <div class="sidebar-speaker-list__avatar">
                            <?php echo mb_strtoupper(mb_substr($spName, 0, 1)); ?>
                        </div>
                        <div class="sidebar-speaker-list__content">
                            <a href="<?php echo $spUrl; ?>"
                               class="sidebar-speaker-list__link">
                                <?php echo $spName; ?>
                            </a>
                            <?php if ($spFirst) : ?>
                                <span class="sidebar-speaker-list__topic"><?php echo $spFirst; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($spEvents > 0) : ?>
                            <span class="sidebar-speaker-list__count"><?php echo $spEvents; ?> Events</span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo htmlspecialchars($speakersUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="sidebar-panel__footer-link">
                    Alle Speaker →
                </a>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Default-Widget: Job-Anzeigen
     */
    public function renderSidebarJobWidget(): void
    {
        if (!$this->sidebarBool('show_jobs_widget', true)) {
            return;
        }
        $title = htmlspecialchars((string)$this->getSidebarSetting('jobs_widget_title', '💼 Job-Anzeigen'), ENT_QUOTES, 'UTF-8');
        ?>
        <div class="sidebar-panel widget-zone" data-widget="jobs">
            <h3><?php echo $title; ?></h3>
            <p class="widget-zone__hint">
                Das cms-jobprofile-generator Plugin zeigt hier aktuelle Stellenprofile an.
            </p>
            <div class="widget-zone__placeholder">
                <span class="widget-zone__icon">💼</span>
                <span class="widget-zone__text">Plugin-Slot: Jobs</span>
            </div>
        </div>
        <?php
    }

    /**
     * Sidebar-Widget: Aktuelle Blog-Beiträge
     * Zeigt die neuesten Beiträge aus der CMS posts-Tabelle.
     */
    public function renderSidebarBlogWidget(): void
    {
        if (!$this->sidebarBool('show_blog_widget', true)) {
            return;
        }
        $title = htmlspecialchars((string)$this->getSidebarSetting('blog_widget_title', '📝 Aktuelle Beiträge'), ENT_QUOTES, 'UTF-8');
        $count = max(1, min(10, (int)$this->getSidebarSetting('blog_widget_count', 5)));

        $posts = [];
        try {
            $db = \CMS\Database::instance();
            $prefix = $db->prefix();
            $stmt = $db->execute(
                "SELECT id, title, slug, excerpt, published_at
                 FROM {$prefix}posts
                 WHERE status = 'published'
                 ORDER BY published_at DESC
                 LIMIT " . $count
            );
            $posts = $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) { /* posts-Tabelle ggf. nicht vorhanden */ }

        $blogUrl = theme_route_url('blog');
        ?>
        <div class="sidebar-panel" data-widget="blog">
            <h3><?php echo $title; ?></h3>
            <?php if (!empty($posts)) : ?>
                <ul class="sidebar-blog-list">
                    <?php foreach ($posts as $post) :
                        $pTitle = htmlspecialchars(_field($post, 'title', 'Beitrag'), ENT_QUOTES, 'UTF-8');
                        $pSlug  = rawurlencode(_field($post, 'slug', ''));
                        $pDate  = _field($post, 'published_at', '');
                        $pDateF = $pDate ? date('d.m.Y', strtotime($pDate)) : '';
                        $pUrl   = htmlspecialchars(theme_route_url('blog-post', ['slug' => $pSlug], [], $blogUrl), ENT_QUOTES, 'UTF-8');
                    ?>
                    <li class="sidebar-blog-list__item">
                        <a href="<?php echo $pUrl; ?>"
                           class="sidebar-blog-list__link">
                            <?php echo $pTitle; ?>
                        </a>
                        <?php if ($pDateF) : ?>
                            <span class="sidebar-blog-list__meta">📅 <?php echo $pDateF; ?></span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="sidebar-panel__footer-link">
                    Alle Beiträge →
                </a>
            <?php else : ?>
                <p class="sidebar-panel__empty-text">Noch keine Beiträge vorhanden.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Sidebar-Widget: Eigenes HTML aus Customizer
     */
    public function renderSidebarCustomHtml(): void
    {
        $html = trim((string)$this->getSidebarSetting('sidebar_custom_html', ''));
        if (empty($html)) {
            return;
        }
        ?>
        <div class="sidebar-panel" data-widget="custom-html">
            <?php echo theme_sanitize_html($html, 'default'); ?>
        </div>
        <?php
    }
}

// Theme initialisieren
IT_Expert_Network_Theme::instance();

if (!function_exists('_field')) {
    function _field($row, string $key, string $default = ''): string
    {
        if (is_array($row)) {
            return (string)($row[$key] ?? $default);
        }
        if (is_object($row)) {
            return (string)($row->{$key} ?? $default);
        }

        return $default;
    }
}

function theme_sanitize_html(string $html, string $profile = 'default'): string
{
    if ($html === '') {
        return '';
    }

    if (function_exists('sanitize_html')) {
        return (string) sanitize_html($html, $profile);
    }

    return strip_tags($html);
}

function theme_has_relative_reference_prefix(string $value): bool
{
    return str_starts_with($value, '/')
        || str_starts_with($value, './')
        || str_starts_with($value, '../')
        || str_starts_with($value, '?')
        || str_starts_with($value, '#');
}

function theme_safe_url(string $url, string $fallback = ''): string
{
    $candidate = trim($url);
    if ($candidate === '') {
        return $fallback !== '' ? theme_safe_url($fallback) : '';
    }

    if (preg_match('/[\x00-\x1F\x7F]/', $candidate) === 1) {
        return $fallback !== '' ? theme_safe_url($fallback) : '';
    }

    if (theme_has_relative_reference_prefix($candidate)) {
        return $candidate;
    }

    if (str_starts_with($candidate, '//')) {
        return $fallback !== '' ? theme_safe_url($fallback) : '';
    }

    $scheme = parse_url($candidate, PHP_URL_SCHEME);
    if ($scheme !== null && $scheme !== false) {
        $normalizedScheme = strtolower((string) $scheme);
        if (!in_array($normalizedScheme, ['http', 'https'], true)) {
            return $fallback !== '' ? theme_safe_url($fallback) : '';
        }
    }

    $sanitized = function_exists('esc_url') ? esc_url($candidate) : (filter_var($candidate, FILTER_VALIDATE_URL) ?: '');
    if (is_string($sanitized) && $sanitized !== '') {
        return $sanitized;
    }

    return $fallback !== '' ? theme_safe_url($fallback) : '';
}

function theme_safe_external_url(string $url): string
{
    $sanitized = theme_safe_url($url);
    if ($sanitized === '' || !preg_match('#^https?://#i', $sanitized)) {
        return '';
    }

    return $sanitized;
}

function theme_build_query_url(string $basePath, array $params = [], array $overrides = []): string
{
    $path = $basePath !== '' ? $basePath : (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
    $path = theme_has_relative_reference_prefix($path) ? $path : '/' . ltrim($path, '/');
    $query = array_merge($params, $overrides);
    $normalized = [];

    foreach ($query as $key => $value) {
        $normalizedKey = function_exists('sanitize_key') ? sanitize_key((string) $key) : preg_replace('/[^a-z0-9_\-]/i', '', (string) $key);
        if ($normalizedKey === '') {
            continue;
        }

        if ($value === null || $value === '' || $value === false) {
            continue;
        }

        if (is_bool($value)) {
            $normalized[$normalizedKey] = $value ? '1' : '0';
            continue;
        }

        if (is_scalar($value)) {
            $normalized[$normalizedKey] = (string) $value;
        }
    }

    $queryString = $normalized ? ('?' . http_build_query($normalized)) : '';

    return theme_safe_url($path . $queryString, $path);
}

function theme_route_path(string $route, array $params = []): string
{
    $routes = [
        'home' => '/',
        'search' => '/search',
        'login' => '/login',
        'register' => '/register',
        'logout' => '/logout',
        'blog' => '/blog',
        'blog-post' => '/blog/{slug}',
        'experts' => '/experts',
        'expert' => '/experts/{id}',
        'companies' => '/companies',
        'company' => '/companies/{id}',
        'events' => '/events',
        'event' => '/events/{id}',
        'speakers' => '/speakers',
        'speaker' => '/speakers/{id}',
        'jobs' => '/jobs',
        'job' => '/jobs/{id}',
        'booking' => '/booking',
        'member-dashboard' => '/{area}',
        'member-notifications' => '/member/notifications',
        'member-expert-profile' => '/member/expert-profile',
        'member-companies' => '/member/companies',
        'member-events' => '/member/events',
        'member-speaker-profile' => '/member/speaker-profile',
        'member-settings' => '/member/settings',
    ];

    $template = $routes[$route] ?? $route;
    $path = theme_has_relative_reference_prefix($template) ? $template : '/' . ltrim($template, '/');

    $resolved = preg_replace_callback('/\{([a-z0-9_]+)\}/i', static function (array $matches) use ($params): string {
        $key = $matches[1];
        if (!array_key_exists($key, $params) || !is_scalar($params[$key])) {
            return '';
        }

        $value = trim(strip_tags((string) $params[$key]));
        if ($value === '') {
            return '';
        }

        $segments = array_filter(
            explode('/', str_replace('\\', '/', $value)),
            static fn(string $segment): bool => $segment !== ''
        );

        if ($segments === []) {
            return '';
        }

        return implode('/', array_map('rawurlencode', $segments));
    }, $path);

    $normalized = preg_replace('#/+#', '/', $resolved ?? '/') ?? '/';

    return $normalized !== '' ? $normalized : '/';
}

function theme_route_url(string $route, array $params = [], array $query = [], string $fallback = ''): string
{
    $path = theme_route_path($route, $params);
    $relativeUrl = $query !== [] ? theme_build_query_url($path, $query) : $path;
    $absoluteUrl = rtrim(SITE_URL, '/') . ($relativeUrl === '/' ? '/' : $relativeUrl);

    return theme_safe_url($absoluteUrl, $fallback !== '' ? $fallback : (rtrim(SITE_URL, '/') . '/'));
}

/**
 * Helper: Aktuelle URL bestimmen
 */
function theme_current_url(): string
{
    $url = SITE_URL;
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return rtrim($url, '/') . $uri;
}

/**
 * Helper: URL aktiv prüfen (für Navigations-Highlighting)
 */
function theme_is_active_url(string $url): bool
{
    $requestUri = rtrim($_SERVER['REQUEST_URI'] ?? '/', '/');
    $path = rtrim(parse_url($url, PHP_URL_PATH) ?? '/', '/');

    if ($path === '' || $path === '/') {
        return $requestUri === '' || $requestUri === '/';
    }

    return str_starts_with($requestUri, $path);
}

/**
 * Helper: CSRF-Token generieren
 */
function theme_csrf_token(string $action = 'form'): string
{
    return \CMS\Security::instance()->generateToken($action);
}

/**
 * Helper: CSRF-Token als Hidden-Field
 */
function theme_csrf_field(string $action = 'form'): void
{
    $token = theme_csrf_token($action);
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">' . "\n";
}

/**
 * Helper: Flash-Message aus Session lesen & löschen
 */
function theme_get_flash(string $type = 'error'): string
{
    $key = ($type === 'success') ? 'success' : 'error';
    $msg = $_SESSION[$key] ?? '';
    unset($_SESSION[$key]);
    return $msg;
}

/**
 * Helper: Eingeloggter Benutzer?
 */
function theme_is_logged_in(): bool
{
    return \CMS\Auth::instance()->isLoggedIn();
}

/**
 * Helper: Navigationsmenü für eine Menü-Position rendern
 */
function theme_nav_menu(string $location = 'primary'): void
{
    $themeManager = \CMS\ThemeManager::instance();
    $menuItems    = $themeManager->getMenu($location);

    // FIX: Wenn explizit ein leeres Array gespeichert wurde (z.B. User hat alle Items gelöscht),
    // darf NICHT das Default-Menü angezeigt werden.
    // Wir prüfen daher, ob wir überhaupt schon mal was gespeichert haben (Seed-Logic).
    // Da seedDefaultMenus() beim Init läuft, sollte die DB eigentlich gefüllt sein.
    // Wenn hier leer zurückkommt, dann ist es entweder leer gespeichert oder DB-Fehler.
    // Wir zeigen das Fallback NUR, wenn wir wirklich gar nichts haben UND es keine gespeicherte Einstellung gibt.
    
    // Aber für den Moment: Wenn $menuItems leer ist, gehen wir davon aus, dass der User es geleert hat
    // oder etwas schief ging. Das Hardcoded-Fallback überschreibt sonst Benutzereingaben.
    
    // Besserer Ansatz: seedDefaultMenus füllt die DB. Wenn User löscht, ist die Liste leer.
    // Wenn Liste leer, soll nichts angezeigt werden.
    // DAS HIER WAR DER FEHLER: Das hardcoded Fallback hat immer gegriffen, wenn (aus irgendeinem Grund)
    // das Array leer war oder vielleicht nicht geladen werden konnte.
    
    // Wir deaktivieren das hardcoded Fallback hier, da seedDefaultMenus() das übernimmt via DB.
    if (empty($menuItems) && !in_array($location, ['primary', 'mobile', 'footer'])) {
       // nur returnen bei unbekannten locations
       return; 
    }
    
    // Wenn leer, dann leer lassen (User hat vielleicht alles gelöscht)
    if (empty($menuItems)) {
        // Optional: Debug-Ausgabe oder Admin-Hinweis
        // Wir returnen NICHTS, damit nichts gerendert wird (nur leeres UL evtl. vermeiden?)
        return; 
    }

    echo '<ul class="nav-menu">' . "\n";
    foreach ($menuItems as $item) {
        $safeUrl = theme_safe_url((string)($item['url'] ?? ''), '#');
        $isActive = $safeUrl !== '#' && theme_is_active_url($safeUrl);
        $class    = $isActive ? ' class="active"' : '';
        $target   = !empty($item['target']) && $item['target'] === '_blank'
            ? ' target="_blank" rel="noopener noreferrer"' : '';
        $url   = htmlspecialchars($safeUrl, ENT_QUOTES, 'UTF-8');
        $label = htmlspecialchars($item['label'] ?? '', ENT_QUOTES, 'UTF-8');
        echo '<li' . $class . '><a href="' . $url . '"' . $target . '>' . $label . '</a></li>' . "\n";
    }
    echo '</ul>' . "\n";
}
