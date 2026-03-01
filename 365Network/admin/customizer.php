<?php
/**
 * 365Network Theme – Customizer Settings
 *
 * Stellt die vollständige Admin-Oberfläche für Theme-Einstellungen bereit.
 *
 * @package CMSv2\Themes\365Network\Admin
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Services\ThemeCustomizer;
use CMS\Security;

// Helper für Sidebar laden
$possiblePaths = [
    (defined('ABSPATH') ? rtrim(ABSPATH, '/\\') : '') . '/admin/partials/admin-menu.php',
    dirname(__DIR__, 3) . '/CMS/admin/partials/admin-menu.php',
    dirname(__DIR__, 2) . '/admin/partials/admin-menu.php',
];

$adminMenuLoaded = false;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $adminMenuLoaded = true;
        break;
    }
}

if (!$adminMenuLoaded) {
    if (!function_exists('renderAdminSidebar')) {
        function renderAdminSidebar($slug) { echo "<!-- Sidebar fallback for $slug -->"; }
    }
    if (!function_exists('renderAdminSidebarStyles')) {
        function renderAdminSidebarStyles() { }
    }
}

// ── 1. Konfiguration ─────────────────────────────────────────────────────────
$config = [

    'colors' => [
        'title' => '🎨 Farben',
        'sections' => [
            'primary_color' => [
                'label'       => 'Primärfarbe (Deep Navy)',
                'description' => 'Hauptfarbe – Header, Footer, dunkle UI-Elemente.',
                'type'        => 'color',
                'default'     => '#0c1526',
            ],
            'primary_hover' => [
                'label'       => 'Primärfarbe (Hover)',
                'description' => 'Hover-Zustand der Primärfarbe – dunklere Navy-Variante.',
                'type'        => 'color',
                'default'     => '#162040',
            ],
            'primary_light' => [
                'label'       => 'Primärfarbe (Hell)',
                'description' => 'Helle Variante – Badge-Hintergründe, Borders auf dunklem Grund.',
                'type'        => 'color',
                'default'     => '#1a2a42',
            ],
            'secondary_color' => [
                'label'       => 'Sekundärfarbe (Grau)',
                'description' => 'Graue UI-Farbe – Meta-Texte, Labels, sekundäre Informationen.',
                'type'        => 'color',
                'default'     => '#64748b',
            ],
            'accent_color' => [
                'label'       => 'Akzentfarbe (Gold)',
                'description' => 'Gold-Akzent – CTAs, Tags, Highlights, Navigation.',
                'type'        => 'color',
                'default'     => '#c8952e',
            ],
            'accent_hover' => [
                'label'       => 'Akzentfarbe (Hover)',
                'description' => 'Dunklere Gold-Variante für Hover-Zustände.',
                'type'        => 'color',
                'default'     => '#a67a24',
            ],
            'accent_light' => [
                'label'       => 'Akzentfarbe (Hell)',
                'description' => 'Hellere Gold-Variante für dezente Highlights.',
                'type'        => 'color',
                'default'     => '#d4a84a',
            ],
            'text_color' => [
                'label'       => 'Textfarbe',
                'description' => 'Hauptfarbe für Fließtext und Body-Content.',
                'type'        => 'color',
                'default'     => '#1e293b',
            ],
            'heading_color' => [
                'label'       => 'Überschriftenfarbe',
                'description' => 'Farbe aller Überschriften (h1–h6).',
                'type'        => 'color',
                'default'     => '#0f172a',
            ],
            'text_light' => [
                'label'       => 'Helle Textfarbe',
                'description' => 'Helle Textfarbe für dunkle Hintergründe (Header, Footer, Hero).',
                'type'        => 'color',
                'default'     => '#e2e8f0',
            ],
            'muted_color' => [
                'label'       => 'Gedämpfte Textfarbe',
                'description' => 'Sehr dezenter Text – Timestamps, Platzhalter, Hints.',
                'type'        => 'color',
                'default'     => '#94a3b8',
            ],
            'bg_color' => [
                'label'       => 'Seitenhintergrund',
                'description' => 'Hintergrundfarbe des Content-Bereichs.',
                'type'        => 'color',
                'default'     => '#f8fafc',
            ],
            'bg_secondary' => [
                'label'       => 'Sekundärer Hintergrund',
                'description' => 'Hintergrund für Body, Cards, Sidebar-Bereiche.',
                'type'        => 'color',
                'default'     => '#f1f5f9',
            ],
            'link_color' => [
                'label'       => 'Linkfarbe',
                'description' => 'Standard-Linkfarbe im Content-Bereich.',
                'type'        => 'color',
                'default'     => '#c8952e',
            ],
            'link_hover_color' => [
                'label'       => 'Link Hover-Farbe',
                'description' => 'Linkfarbe beim Hover-Zustand.',
                'type'        => 'color',
                'default'     => '#a67a24',
            ],
            'border_color' => [
                'label'       => 'Rahmenfarbe',
                'description' => 'Standard-Rahmenfarbe für Trennlinien, Cards und Inputs.',
                'type'        => 'color',
                'default'     => '#e2e8f0',
            ],
            'success_color' => [
                'label'       => 'Erfolgsfarbe',
                'description' => 'Positivmeldungen und Status-Badges (Grün).',
                'type'        => 'color',
                'default'     => '#22c55e',
            ],
            'error_color' => [
                'label'       => 'Fehlerfarbe',
                'description' => 'Fehlermeldungen und Warnungen (Rot).',
                'type'        => 'color',
                'default'     => '#ef4444',
            ],
        ],
    ],

    'typography' => [
        'title' => '🔤 Typografie',
        'sections' => [
            'font_family_base' => [
                'label'       => 'Basis-Schriftart',
                'description' => 'Schriftart für Fließtext – System-Standard für optimale Performance.',
                'type'        => 'select',
                'options'     => [
                    'system'      => 'System-Standard',
                    'inter'       => 'Inter',
                    'roboto'      => 'Roboto',
                    'open-sans'   => 'Open Sans',
                    'lato'        => 'Lato',
                    'montserrat'  => 'Montserrat',
                    'poppins'     => 'Poppins',
                    'raleway'     => 'Raleway',
                ],
                'default'     => 'system',
            ],
            'font_family_heading' => [
                'label'       => 'Überschriften-Schriftart',
                'description' => 'Schriftart für alle Überschriften (sans-serif passend zum Dashboard-Design).',
                'type'        => 'select',
                'options'     => [
                    'system'      => 'System-Standard',
                    'georgia'     => 'Georgia (Serif)',
                    'inter'       => 'Inter',
                    'roboto'      => 'Roboto',
                    'open-sans'   => 'Open Sans',
                    'lato'        => 'Lato',
                    'montserrat'  => 'Montserrat',
                    'poppins'     => 'Poppins',
                    'raleway'     => 'Raleway',
                ],
                'default'     => 'system',
            ],
            'font_size_base' => [
                'label'       => 'Basis-Schriftgröße (px)',
                'description' => 'Schriftgröße des Fließtexts in Pixeln.',
                'type'        => 'number',
                'default'     => 16,
            ],
            'line_height_base' => [
                'label'       => 'Zeilenhöhe',
                'description' => 'Zeilenhöhe des Fließtexts (Faktor, z. B. 1.6).',
                'type'        => 'number',
                'default'     => 1.6,
            ],
            'font_weight_heading' => [
                'label'       => 'Überschriften-Gewicht',
                'description' => 'Font-Weight für alle Überschriften.',
                'type'        => 'select',
                'options'     => [
                    '400' => 'Regular (400)',
                    '500' => 'Medium (500)',
                    '600' => 'Semi-Bold (600)',
                    '700' => 'Bold (700)',
                    '800' => 'Extra-Bold (800)',
                    '900' => 'Black (900)',
                ],
                'default'     => '700',
            ],
        ],
    ],

    'layout' => [
        'title' => '📐 Layout',
        'sections' => [
            'container_width' => [
                'label'       => 'Container-Breite (px)',
                'description' => 'Maximale Breite des Inhaltsbereichs in Pixeln.',
                'type'        => 'number',
                'default'     => 1400,
            ],
            'content_padding' => [
                'label'       => 'Content-Padding (rem)',
                'description' => 'Horizontaler Innenabstand des Containers.',
                'type'        => 'number',
                'default'     => 2,
            ],
            'border_radius' => [
                'label'       => 'Eckenradius (px)',
                'description' => 'Standard-Rundung für Cards, Buttons und Felder.',
                'type'        => 'number',
                'default'     => 8,
            ],
            'section_spacing' => [
                'label'       => 'Sektionsabstand (rem)',
                'description' => 'Vertikaler Abstand zwischen Seitenbereichen.',
                'type'        => 'number',
                'default'     => 4,
            ],
            'enable_sticky_header' => [
                'label'       => 'Sticky Header aktivieren',
                'description' => 'Header bleibt beim Scrollen oben fixiert.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    'header' => [
        'title' => '🖼️ Header & Logo',
        'sections' => [
            'logo_url' => [
                'label'       => 'Header Logo',
                'description' => 'Logo-Bild im Header (JPG, PNG, WebP, SVG – max. 2 MB). Leer = Netzwerk-Icon.',
                'type'        => 'image_upload',
                'default'     => '',
            ],
            'header_bg_color' => [
                'label'       => 'Header-Hintergrundfarbe',
                'description' => 'Hintergrundfarbe der Navigationsleiste (Deep Navy).',
                'type'        => 'color',
                'default'     => '#0c1526',
            ],
            'header_text_color' => [
                'label'       => 'Header-Textfarbe',
                'description' => 'Farbe von Navigationslinks und Logo-Text (hell auf dunkel).',
                'type'        => 'color',
                'default'     => '#e2e8f0',
            ],
            'header_accent_color' => [
                'label'       => 'Header-Akzentfarbe',
                'description' => 'Akzentfarbe im Header – aktive Links, Badges, Highlights.',
                'type'        => 'color',
                'default'     => '#c8952e',
            ],
            'header_height' => [
                'label'       => 'Header-Höhe (px)',
                'description' => 'Höhe der Navigationsleiste.',
                'type'        => 'number',
                'default'     => 72,
            ],
            'logo_max_height' => [
                'label'       => 'Logo-Maximalhöhe (px)',
                'description' => 'Maximale Höhe des Site-Logos.',
                'type'        => 'number',
                'default'     => 48,
            ],
            'show_header_shadow' => [
                'label'       => 'Header-Schatten anzeigen',
                'description' => 'Subtiler Schatten unterhalb der Navigationsleiste.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_search_btn' => [
                'label'       => 'Such-Button im Header anzeigen',
                'description' => 'Zeigt den Such-Button für die Schnellsuche.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_login_btn' => [
                'label'       => 'Anmelden-Button anzeigen',
                'description' => 'Zeigt den Login-Button für nicht angemeldete Besucher.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_register_btn' => [
                'label'       => 'Registrieren-Button anzeigen',
                'description' => 'Zeigt den Registrieren-Button für nicht angemeldete Besucher.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'profile_show_dashboard' => [
                'label'       => 'Profil-Menü: Dashboard-Link',
                'description' => 'Zeigt den Link zum Member-/Admin-Dashboard im Profil-Dropdown.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'profile_show_expert' => [
                'label'       => 'Profil-Menü: Experten-Profil',
                'description' => 'Zeigt den Link zum eigenen Experten-Profil (benötigt cms-experts).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'profile_show_company' => [
                'label'       => 'Profil-Menü: Firmenprofil',
                'description' => 'Zeigt den Link zur Firmenübersicht (benötigt cms-companies).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'profile_show_events' => [
                'label'       => 'Profil-Menü: Meine Events',
                'description' => 'Zeigt den Link zu eigenen Events (benötigt cms-events).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'profile_show_speaker' => [
                'label'       => 'Profil-Menü: Speaker-Profil',
                'description' => 'Zeigt den Link zum Speaker-Profil (benötigt cms-speakers).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    'footer' => [
        'title' => '🔻 Footer',
        'sections' => [
            'footer_bg_color' => [
                'label'       => 'Footer-Hintergrundfarbe',
                'description' => 'Hintergrundfarbe des Footer-Bereichs (Deep Navy).',
                'type'        => 'color',
                'default'     => '#0c1526',
            ],
            'footer_text_color' => [
                'label'       => 'Footer-Textfarbe',
                'description' => 'Textfarbe im Footer-Bereich.',
                'type'        => 'color',
                'default'     => '#94a3b8',
            ],
            'footer_link_color' => [
                'label'       => 'Footer-Linkfarbe',
                'description' => 'Farbe der Links im Footer.',
                'type'        => 'color',
                'default'     => '#e2e8f0',
            ],
            'footer_text' => [
                'label'       => 'Footer-Beschreibungstext',
                'description' => 'Text im ersten Widget-Bereich (z. B. Firmenbeschreibung).',
                'type'        => 'textarea',
                'default'     => 'Die IT-Networking-Plattform für Experten, Unternehmen und Events. Vernetze dich mit der IT-Community.',
            ],
            'show_network_widgets' => [
                'label'       => 'Netzwerk-Widgets anzeigen',
                'description' => 'Aktiviert den oberen Footer-Bereich mit Experten/Unternehmen-Links.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'copyright_text' => [
                'label'       => 'Copyright-Text',
                'description' => 'Copyright-Zeile unten. Platzhalter: {year}, {site_title}.',
                'type'        => 'text',
                'default'     => '© {year} {site_title}. Alle Rechte vorbehalten.',
            ],
            'social_twitter' => [
                'label'       => 'Twitter / X URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_instagram' => [
                'label'       => 'Instagram URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_linkedin' => [
                'label'       => 'LinkedIn URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_youtube' => [
                'label'       => 'YouTube URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
        ],
    ],

    'buttons' => [
        'title' => '🔘 Buttons',
        'sections' => [
            'button_border_radius' => [
                'label'       => 'Button-Eckenradius (px)',
                'description' => 'Eckenrundung aller regulären Buttons.',
                'type'        => 'number',
                'default'     => 6,
            ],
            'button_padding_x' => [
                'label'       => 'Button-Padding horizontal (rem)',
                'description' => 'Horizontaler Innenabstand der Buttons.',
                'type'        => 'number',
                'default'     => 1.5,
            ],
            'button_padding_y' => [
                'label'       => 'Button-Padding vertikal (rem)',
                'description' => 'Vertikaler Innenabstand der Buttons.',
                'type'        => 'number',
                'default'     => 0.625,
            ],
            'button_font_weight' => [
                'label'       => 'Button-Schriftgewicht',
                'description' => 'Schriftgewicht aller Buttons.',
                'type'        => 'select',
                'options'     => [
                    '400' => 'Regular (400)',
                    '500' => 'Medium (500)',
                    '600' => 'Semi-Bold (600)',
                    '700' => 'Bold (700)',
                ],
                'default'     => '600',
            ],
            'button_transform' => [
                'label'       => 'Button-Textumwandlung',
                'description' => 'Textformatierung für Button-Beschriftungen.',
                'type'        => 'select',
                'options'     => [
                    'none'       => 'Normal',
                    'uppercase'  => 'GROSSBUCHSTABEN',
                    'capitalize' => 'Erster Buchstabe groß',
                ],
                'default'     => 'none',
            ],
        ],
    ],

    'homepage' => [
        'title' => '🏠 Startseite',
        'sections' => [
            'show_hero' => [
                'label'       => 'Hero-Sektion anzeigen',
                'description' => 'Zeigt die große Hero-Sektion mit Suchleiste und Statistiken.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'hero_title' => [
                'label'       => 'Hero-Überschrift',
                'description' => 'Hauptüberschrift in der Hero-Sektion. Wird per LandingPage oder Plugin-Filter überschrieben falls gesetzt.',
                'type'        => 'text',
                'default'     => 'Führendes Verzeichnis für IT-Experten & Unternehmen',
            ],
            'hero_subtitle' => [
                'label'       => 'Hero-Untertitel',
                'description' => 'Optionaler Untertitel (leer = ausgeblendet).',
                'type'        => 'text',
                'default'     => '',
            ],
            'show_hero_search' => [
                'label'       => 'Suchformular anzeigen',
                'description' => 'Zeigt die erweiterte Suche in der Hero-Sektion.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_stats_bar' => [
                'label'       => 'Statistik-Leiste anzeigen',
                'description' => 'Experten/Firmen/Events/Speaker-Zähler. Plugins können per Filter \'home_stats\' eigene hinzufügen.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_experts_section' => [
                'label'       => 'Experten-Sektion anzeigen',
                'description' => 'Neueste Experten-Profile (benötigt cms-experts Plugin).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'experts_section_title' => [
                'label'       => 'Experten-Überschrift',
                'description' => 'Titel über der Experten-Sektion.',
                'type'        => 'text',
                'default'     => 'Aktuelle Experten',
            ],
            'experts_limit' => [
                'label'       => 'Anzahl Experten',
                'description' => 'Maximale Anzahl angezeigter Experten-Cards (1–12).',
                'type'        => 'number',
                'default'     => 3,
            ],
            'show_events_section' => [
                'label'       => 'Events-Sektion anzeigen',
                'description' => 'Kommende Events und Konferenzen (benötigt cms-events Plugin).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'events_section_title' => [
                'label'       => 'Events-Überschrift',
                'description' => 'Titel über der Events-Sektion.',
                'type'        => 'text',
                'default'     => 'Kommende Events & Konferenzen',
            ],
            'events_limit' => [
                'label'       => 'Anzahl Events',
                'description' => 'Maximale Anzahl angezeigter Events (1–12).',
                'type'        => 'number',
                'default'     => 4,
            ],
            'show_companies_section' => [
                'label'       => 'Firmen-Sektion anzeigen',
                'description' => 'Prominenteste Firmenprofile (benötigt cms-companies Plugin).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'companies_section_title' => [
                'label'       => 'Firmen-Überschrift',
                'description' => 'Titel über der Firmen-Sektion.',
                'type'        => 'text',
                'default'     => 'Top Firmen im Fokus',
            ],
            'companies_limit' => [
                'label'       => 'Anzahl Firmen',
                'description' => 'Maximale Anzahl angezeigter Firmen-Cards (1–12).',
                'type'        => 'number',
                'default'     => 4,
            ],
            'show_sidebar' => [
                'label'       => 'Sidebar anzeigen',
                'description' => 'Plugin-Widgets registrieren sich über Hook \'home_sidebar_widget\'.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'homepage_layout' => [
                'label'       => 'Seitenlayout',
                'description' => 'Layout der Startseite. Volle Breite deaktiviert die Sidebar.',
                'type'        => 'select',
                'options'     => [
                    'sidebar-right' => 'Sidebar rechts (Standard)',
                    'sidebar-left'  => 'Sidebar links',
                    'full-width'    => 'Volle Breite (ohne Sidebar)',
                ],
                'default'     => 'sidebar-right',
            ],
            'show_events_strip' => [
                'label'       => 'Events-Fussleiste anzeigen',
                'description' => 'Horizontale Events-Vorschau am Ende der Seite.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    'effects' => [
        'title' => '✨ Effekte & Animationen',
        'sections' => [
            'show_network_animation' => [
                'label'       => 'Netzwerk-Animation im Header',
                'description' => 'Zeigt eine dezente Partikel-/Netzwerk-Animation im Header-Hintergrund.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'animation_speed' => [
                'label'       => 'Animations-Geschwindigkeit',
                'description' => 'Bewegungsgeschwindigkeit der Netzwerk-Partikel.',
                'type'        => 'select',
                'options'     => [
                    'slow'   => 'Langsam (entspannt)',
                    'normal' => 'Normal',
                    'fast'   => 'Schnell (dynamisch)',
                ],
                'default'     => 'slow',
            ],
            'animation_opacity' => [
                'label'       => 'Animations-Deckkraft (%)',
                'description' => 'Sichtbarkeit der Animation (niedrig = dezenter).',
                'type'        => 'number',
                'default'     => 15,
            ],
            'animation_node_count' => [
                'label'       => 'Anzahl Netzwerk-Knoten',
                'description' => 'Partikelanzahl der Netzwerk-Animation.',
                'type'        => 'number',
                'default'     => 25,
            ],
        ],
    ],

    'advanced' => [
        'title' => '🔧 Erweitert',
        'sections' => [
            'custom_css' => [
                'label'       => 'Eigenes CSS',
                'description' => 'Zusätzliches CSS, das am Ende aller Theme-Styles angehängt wird.',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'custom_head_code' => [
                'label'       => 'Custom Head Code (Tracking, Meta)',
                'description' => 'Wird im &lt;head&gt; ausgegeben. Nur vertrauenswürdigen Code einfügen!',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'custom_footer_code' => [
                'label'       => 'Custom Footer Code (Analytics, Widgets)',
                'description' => 'Wird vor &lt;/body&gt; ausgegeben.',
                'type'        => 'textarea',
                'default'     => '',
            ],
        ],
    ],

];

// ── 2. Customizer-Instanz ────────────────────────────────────────────────────
$customizer = ThemeCustomizer::instance();
if (class_exists('\CMS\ThemeManager')) {
    $customizer->setTheme(\CMS\ThemeManager::instance()->getActiveThemeSlug());
}

$activeTab = $_GET['tab'] ?? 'colors';
if (!isset($config[$activeTab])) {
    $activeTab = 'colors';
}

// ── 3. Speichern & Zurücksetzen ──────────────────────────────────────────────
$success = null;
$error   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_theme_tab') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'theme_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        $resetTab   = $_POST['active_section'] ?? $activeTab;
        if (!isset($config[$resetTab])) {
            $resetTab = $activeTab;
        }
        $resetFailed = false;
        foreach ($config[$resetTab]['sections'] as $fieldKey => $fieldConfig) {
            $default = $fieldConfig['default'] ?? '';
            if (is_bool($default)) {
                $default = $default ? '1' : '0';
            }
            if (!$customizer->set($resetTab, $fieldKey, (string)$default)) {
                $resetFailed = true;
            }
        }
        if ($resetFailed) {
            $error = 'Einstellungen konnten nicht zurückgesetzt werden. Bitte Fehler-Log prüfen.';
        } else {
            $success = 'Einstellungen für &bdquo;' . htmlspecialchars($config[$resetTab]['title']) . '&ldquo; auf Standardwerte zurückgesetzt.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_theme_options') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'theme_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        // Logo-Datei-Upload verarbeiten
        if (!empty($_FILES['logo_upload_file']['tmp_name'])) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            $fileExt     = strtolower(pathinfo($_FILES['logo_upload_file']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowedExts, true)) {
                $uploadDir = UPLOAD_PATH . 'theme-logos';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName = 'logo-' . time() . '.' . $fileExt;
                $destPath    = $uploadDir . '/' . $newFileName;
                if (move_uploaded_file($_FILES['logo_upload_file']['tmp_name'], $destPath)) {
                    $customizer->set('header', 'logo_url', UPLOAD_URL . '/theme-logos/' . $newFileName);
                } else {
                    $error = 'Logo-Upload fehlgeschlagen. Bitte prüfen Sie die Schreibrechte auf uploads/theme-logos/';
                }
            } else {
                $error = 'Ungültiges Dateiformat. Erlaubt: JPG, PNG, GIF, SVG, WebP';
            }
        }

        if (!$error) {
            $saveTab = $_POST['active_section'] ?? $activeTab;
            if (!isset($config[$saveTab])) {
                $saveTab = $activeTab;
            }
            $saveFailed = false;
            foreach ($config[$saveTab]['sections'] as $fieldKey => $fieldConfig) {
                $inputName = "{$saveTab}_{$fieldKey}";
                // logo_url: nur speichern wenn explizit befüllt (Datei-Upload hat Vorrang)
                if ($saveTab === 'header' && $fieldKey === 'logo_url') {
                    $postVal = $_POST[$inputName] ?? '';
                    if ($postVal !== '') {
                        if (!$customizer->set($saveTab, $fieldKey, $postVal)) {
                            $saveFailed = true;
                        }
                    }
                    continue;
                }
                if ($fieldConfig['type'] === 'checkbox') {
                    $value = isset($_POST[$inputName]) ? '1' : '0';
                } elseif ($fieldConfig['type'] === 'image_upload') {
                    $value = $_POST[$inputName] ?? null;
                    if ($value === null) { continue; }
                } else {
                    $value = $_POST[$inputName] ?? '';
                }
                if (!$customizer->set($saveTab, $fieldKey, $value)) {
                    $saveFailed = true;
                }
            }
            if ($saveFailed) {
                $error = 'Einstellungen konnten nicht gespeichert werden. Bitte Fehler-Log prüfen (evtl. fehlt die DB-Tabelle oder es liegt ein Datenbank-Fehler vor).';
            } else {
                $success = 'Einstellungen für &bdquo;' . htmlspecialchars($config[$saveTab]['title']) . '&ldquo; gespeichert.';
            }
        }
    }
}

// Token EINMAL generieren – mehrfaches generateToken() für dieselbe Action
// überschreibt den Session-Eintrag und macht zuvor gesendete Tokens ungültig.
$csrfToken = Security::instance()->generateToken('theme_customizer');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Customizer – <?php echo defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : '365Network'; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css?v=20260222b">
    <?php renderAdminSidebarStyles(); ?>
    <style>
        .customizer-layout { display: flex; gap: 2rem; align-items: flex-start; }
        .customizer-nav { width: 240px; flex-shrink: 0; background: #fff; border-radius: var(--card-radius, 10px); border: var(--card-border, 1px solid #e2e8f0); overflow: hidden; }
        .customizer-nav a { display: block; padding: 1rem 1.5rem; color: #64748b; text-decoration: none; border-left: 3px solid transparent; transition: all .2s; font-size: .9rem; }
        .customizer-nav a:hover { background: #f8fafc; color: var(--admin-primary, #3b82f6); }
        .customizer-nav a.active { background: #eff6ff; color: var(--admin-primary, #3b82f6); border-left-color: var(--admin-primary, #3b82f6); font-weight: 600; }
        .customizer-content { flex: 1; }
        .form-actions-card { position: sticky; bottom: 1rem; z-index: 10; }

        /* ── Farben-Tab: 3-Spalten-Karten-Layout ── */
        .color-cards-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        .color-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; }
        .color-card h4 { margin: 0 0 1rem 0; font-size: .95rem; font-weight: 700; color: #1e293b; padding-bottom: .75rem; border-bottom: 1px solid #f1f5f9; }
        .color-card .form-group { margin-bottom: 1rem; }
        .color-card .form-group:last-child { margin-bottom: 0; }
        .color-card .form-label { font-size: .82rem; margin-bottom: .25rem; }
        .color-card .form-text { font-size: .75rem; }
        @media (max-width: 1200px) { .color-cards-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 800px) { .color-cards-grid { grid-template-columns: 1fr; } }

        /* ── Startseite-Tab: 2-Spalten-Karten-Layout ── */
        .homepage-cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .homepage-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; }
        .homepage-card h4 { margin: 0 0 1rem 0; font-size: .95rem; font-weight: 700; color: #1e293b; padding-bottom: .75rem; border-bottom: 1px solid #f1f5f9; }
        .homepage-card .form-group { margin-bottom: 1rem; }
        .homepage-card .form-group:last-child { margin-bottom: 0; }
        .homepage-card .form-label { font-size: .85rem; margin-bottom: .25rem; }
        .homepage-card .form-text { font-size: .78rem; }
        @media (max-width: 900px) { .homepage-cards-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="admin-body">

    <?php renderAdminSidebar('theme-customizer'); ?>

    <div class="admin-content">

        <div class="admin-page-header">
            <div>
                <h2>🎨 Theme Customizer</h2>
                <p>Passe das Aussehen des 365Network-Themes an.</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo SITE_URL; ?>/" target="_blank" class="btn btn-secondary">🌐 Seite ansehen</a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="?tab=<?php echo htmlspecialchars($activeTab); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_theme_options">
            <input type="hidden" name="active_section" value="<?php echo htmlspecialchars($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">

            <div class="customizer-layout">

                <!-- Sidebar-Navigation -->
                <nav class="customizer-nav">
                    <?php foreach ($config as $key => $tab): ?>
                        <a href="?tab=<?php echo $key; ?>"
                           class="<?php echo $activeTab === $key ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($tab['title']); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Inhaltsbereich -->
                <div class="customizer-content">
                    <?php if (isset($config[$activeTab])): $currentSection = $config[$activeTab]; ?>
                    <?php if ($activeTab === 'colors'):
                        // ── Farben: 3-Spalten-Karten-Layout ──────────────────────
                        $colorGroups = [
                            '🎨 Markenfarben' => ['primary_color', 'primary_hover', 'primary_light', 'secondary_color', 'accent_color', 'accent_hover', 'accent_light'],
                            '📝 Text & Links' => ['text_color', 'heading_color', 'text_light', 'muted_color', 'link_color', 'link_hover_color'],
                            '🖼️ Hintergrund & Status' => ['bg_color', 'bg_secondary', 'border_color', 'success_color', 'error_color'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo htmlspecialchars($currentSection['title']); ?></h3>
                        <div class="color-cards-grid">
                            <?php foreach ($colorGroups as $groupTitle => $groupKeys): ?>
                            <div class="color-card">
                                <h4><?php echo $groupTitle; ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $inputId; ?>" class="form-label">
                                        <?php echo htmlspecialchars($field['label']); ?>
                                    </label>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <input type="color" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                               value="<?php echo htmlspecialchars((string)$val); ?>"
                                               style="height:38px;padding:2px;width:60px;border:1px solid #ddd;border-radius:4px;">
                                        <input type="text" value="<?php echo htmlspecialchars((string)$val); ?>"
                                               class="form-control" style="width:120px;"
                                               onchange="document.getElementById('<?php echo $inputId; ?>').value = this.value; updateLivePreview();">
                                    </div>
                                    <?php if (!empty($field['description'])): ?>
                                        <small class="form-text"><?php echo $field['description']; ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'homepage'):
                        // ── Startseite: 2-Spalten-Karten-Layout ──────────────────────
                        $homepageGroups = [
                            '🎬 Hero-Sektion' => ['show_hero', 'hero_title', 'hero_subtitle', 'show_hero_search', 'show_stats_bar'],
                            '👨‍💻 Experten-Bereich' => ['show_experts_section', 'experts_section_title', 'experts_limit'],
                            '📅 Events-Bereich' => ['show_events_section', 'events_section_title', 'events_limit'],
                            '🏢 Firmen-Bereich' => ['show_companies_section', 'companies_section_title', 'companies_limit'],
                            '📐 Layout & Sidebar' => ['show_sidebar', 'homepage_layout'],
                            '📌 Zusätzliche Bereiche' => ['show_events_strip'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo htmlspecialchars($currentSection['title']); ?></h3>
                        <div class="homepage-cards-grid">
                            <?php foreach ($homepageGroups as $groupTitle => $groupKeys): ?>
                            <div class="homepage-card">
                                <h4><?php echo $groupTitle; ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $inputId; ?>" class="form-label">
                                        <?php echo htmlspecialchars($field['label']); ?>
                                    </label>

                                    <?php if ($field['type'] === 'checkbox'): ?>
                                        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                                            <input type="checkbox" id="<?php echo $inputId; ?>"
                                                   name="<?php echo $inputName; ?>" value="1"
                                                   <?php echo $val ? 'checked' : ''; ?>>
                                            <label for="<?php echo $inputId; ?>" style="cursor:pointer;">Aktivieren</label>
                                        </div>

                                    <?php elseif ($field['type'] === 'select'): ?>
                                        <select id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                                class="form-control">
                                            <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                                            <option value="<?php echo htmlspecialchars((string)$optVal); ?>"
                                                <?php echo (string)$val === (string)$optVal ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($optLabel); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>

                                    <?php elseif ($field['type'] === 'number'): ?>
                                        <input type="number" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                               value="<?php echo htmlspecialchars((string)$val); ?>"
                                               class="form-control" style="width:120px;" min="1" max="12">

                                    <?php else: ?>
                                        <input type="<?php echo htmlspecialchars($field['type']); ?>"
                                               id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                               value="<?php echo htmlspecialchars((string)$val); ?>"
                                               class="form-control">
                                    <?php endif; ?>

                                    <?php if (!empty($field['description'])): ?>
                                        <small class="form-text"><?php echo $field['description']; ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php else: ?>
                    <!-- Alle anderen Tabs: Standard-Rendering -->
                    <div class="admin-card">
                        <h3><?php echo htmlspecialchars($currentSection['title']); ?></h3>

                        <?php foreach ($currentSection['sections'] as $fieldKey => $field):
                            $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                            $inputId   = "field_{$activeTab}_{$fieldKey}";
                            $inputName = "{$activeTab}_{$fieldKey}";
                        ?>
                        <div class="form-group">
                            <label for="<?php echo $inputId; ?>" class="form-label">
                                <?php echo htmlspecialchars($field['label']); ?>
                            </label>

                            <?php if ($field['type'] === 'textarea'): ?>
                                <textarea id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                          class="form-control" rows="4"
                                ><?php echo htmlspecialchars((string)$val); ?></textarea>

                            <?php elseif ($field['type'] === 'checkbox'): ?>
                                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                                    <input type="checkbox" id="<?php echo $inputId; ?>"
                                           name="<?php echo $inputName; ?>" value="1"
                                           <?php echo $val ? 'checked' : ''; ?>>
                                    <label for="<?php echo $inputId; ?>" style="cursor:pointer;">Aktivieren</label>
                                </div>

                            <?php elseif ($field['type'] === 'select'): ?>
                                <select id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                        class="form-control">
                                    <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                                    <option value="<?php echo htmlspecialchars((string)$optVal); ?>"
                                        <?php echo (string)$val === (string)$optVal ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($optLabel); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                            <?php elseif ($field['type'] === 'image_upload'): ?>
                                <?php $previewUrl = $val ? htmlspecialchars((string)$val) : ''; ?>
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    <div id="logo-preview-wrap" style="background:#f8fafc;border:1px dashed #cbd5e1;border-radius:6px;padding:12px;display:flex;align-items:center;gap:12px;min-height:60px;">
                                        <?php if ($previewUrl): ?>
                                            <img id="logo-preview-img" src="<?php echo $previewUrl; ?>" alt="Logo" style="max-height:48px;max-width:200px;">
                                        <?php else: ?>
                                            <span id="logo-preview-img" style="color:#94a3b8;font-size:.85rem;">🖼️ Noch kein Logo ausgewählt</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <label style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;padding:.45rem .9rem;background:#3b82f6;color:#fff;border-radius:5px;font-size:.85rem;font-weight:600;">
                                            📁 Bild hochladen
                                            <input type="file" name="logo_upload_file" accept="image/*"
                                                   style="display:none;" onchange="previewLogoUpload(this)">
                                        </label>
                                        <span style="color:#64748b;font-size:.8rem;">oder URL eingeben:</span>
                                    </div>
                                    <input type="text" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                           value="<?php echo $previewUrl; ?>" class="form-control"
                                           placeholder="https://..." oninput="syncLogoUrlPreview(this.value)">
                                </div>

                            <?php elseif ($field['type'] === 'color'): ?>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <input type="color" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                           value="<?php echo htmlspecialchars((string)$val); ?>"
                                           style="height:38px;padding:2px;width:60px;border:1px solid #ddd;border-radius:4px;">
                                    <input type="text" value="<?php echo htmlspecialchars((string)$val); ?>"
                                           class="form-control" style="width:120px;"
                                           onchange="document.getElementById('<?php echo $inputId; ?>').value = this.value; updateLivePreview();">
                                </div>

                            <?php else: ?>
                                <input type="<?php echo htmlspecialchars($field['type']); ?>"
                                       id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                       value="<?php echo htmlspecialchars((string)$val); ?>"
                                       class="form-control">
                            <?php endif; ?>

                            <?php if (!empty($field['description'])): ?>
                                <small class="form-text"><?php echo $field['description']; ?></small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; /* colors / homepage / standard tab */ ?>

                    <!-- Sticky Speichern-Leiste -->
                    <div class="admin-card form-actions-card">
                        <div class="form-actions" style="justify-content:space-between;">
                            <button type="submit" class="btn btn-primary">💾 Einstellungen speichern</button>
                            <button type="button" class="btn btn-secondary"
                                    onclick="showResetConfirm()"
                                    title="Alle Einstellungen dieses Tabs auf Standardwerte zurücksetzen">
                                ↺ Auf Standardwerte zurücksetzen
                            </button>
                        </div>
                    </div>

                    <?php endif; ?>
                </div>
            </div>
        </form>

    <!-- Verstecktes Reset-Formular (außerhalb des Haupt-Forms, verschachtelte Forms sind invalid HTML) -->
    <?php if (isset($config[$activeTab])): ?>
    <form id="reset-form" method="POST" action="?tab=<?php echo htmlspecialchars($activeTab); ?>" style="display:none;">
        <input type="hidden" name="action" value="reset_theme_tab">
        <input type="hidden" name="active_section" value="<?php echo htmlspecialchars($activeTab); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    </form>
    <?php endif; ?>

    </div><!-- /.admin-content -->

    <!-- Reset-Bestätigungsmodal -->
    <div id="confirm-reset-modal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:480px;">
            <div class="modal-header">
                <h3>⚠️ Einstellungen zurücksetzen?</h3>
                <button class="modal-close" onclick="closeResetModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Alle Einstellungen dieses Tabs werden auf die <strong>Standard-Designwerte</strong> des 365Network-Themes zurückgesetzt.</p>
                <p style="color:#64748b;font-size:.875rem;">Bereits gespeicherte Anpassungen gehen für diesen Bereich verloren.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeResetModal()">Abbrechen</button>
                <button type="button" class="btn btn-danger" onclick="confirmReset()">↺ Zurücksetzen</button>
            </div>
        </div>
    </div>

    <script src="<?php echo SITE_URL; ?>/assets/js/admin.js"></script>
    <script>
    // ── Farb-Picker ↔ Text-Input + Live-Vorschau ─────────────────────────────
    (function () {

        var liveStyle = document.createElement('style');
        liveStyle.id  = 'customizer-live-preview';
        document.head.appendChild(liveStyle);

        // Mapping: input-name → CSS-Variable im 365Network-Theme
        var cssVarMap = {
            'colors_primary_color':     '--primary-color',
            'colors_primary_hover':     '--primary-hover',
            'colors_primary_light':     '--primary-light',
            'colors_secondary_color':   '--secondary-color',
            'colors_accent_color':      '--accent-color',
            'colors_accent_hover':      '--accent-hover',
            'colors_accent_light':      '--accent-light',
            'colors_text_color':        '--text-color',
            'colors_heading_color':     '--heading-color',
            'colors_text_light':        '--text-light',
            'colors_muted_color':       '--muted-color',
            'colors_bg_color':          '--background-color',
            'colors_bg_secondary':      '--bg-secondary',
            'colors_link_color':        '--link-color',
            'colors_link_hover_color':  '--link-hover-color',
            'colors_border_color':      '--border-color',
            'colors_success_color':     '--success-color',
            'colors_error_color':       '--error-color',
            'header_header_bg_color':   '--header-bg',
            'header_header_text_color': '--header-text',
            'header_header_accent_color': '--header-text-secondary',
            'footer_footer_bg_color':   '--footer-bg',
            'footer_footer_text_color': '--footer-text',
            'footer_footer_link_color': '--footer-link-color',
        };

        function updateLivePreview() {
            var rules = ':root {\n';
            Object.keys(cssVarMap).forEach(function (name) {
                var inp = document.querySelector('input[name="' + name + '"][type="color"]');
                if (inp) {
                    rules += '  ' + cssVarMap[name] + ': ' + inp.value + ';\n';
                }
            });
            rules += '}';
            liveStyle.textContent = rules;
        }

        // Farb-Picker ↔ Text-Input synchronisieren
        document.querySelectorAll('input[type="color"]').forEach(function (picker) {
            var textInput = picker.nextElementSibling;
            if (textInput && textInput.tagName === 'INPUT' && textInput.type === 'text') {
                picker.addEventListener('input', function () {
                    textInput.value = this.value;
                    updateLivePreview();
                });
                textInput.addEventListener('input', function () {
                    var v = this.value.trim();
                    if (/^#[0-9a-fA-F]{6}$/.test(v)) {
                        picker.value = v;
                        updateLivePreview();
                    }
                });
            }
        });

        // Farbpaletten-Vorschau einblenden (nur im Farben-Tab)
        if (document.querySelector('input[name="colors_primary_color"]')) {
            var paletteFields = [
                { name: 'colors_primary_color',    label: 'Primär' },
                { name: 'colors_secondary_color',  label: 'Sekundär' },
                { name: 'colors_accent_color',     label: 'Akzent' },
                { name: 'colors_text_color',       label: 'Text' },
                { name: 'colors_bg_color',         label: 'Hintergrund' },
                { name: 'colors_bg_secondary',     label: 'Surface' },
                { name: 'colors_border_color',     label: 'Rahmen' },
            ];
            var palette = document.createElement('div');
            palette.style.cssText = 'display:flex;gap:6px;flex-wrap:wrap;';

            paletteFields.forEach(function (cf) {
                var inp = document.querySelector('input[name="' + cf.name + '"][type="color"]');
                if (!inp) { return; }
                var swatch = document.createElement('div');
                swatch.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:2px;';
                var dot = document.createElement('div');
                dot.style.cssText = 'width:32px;height:32px;border-radius:50%;border:2px solid rgba(0,0,0,.1);background:' + inp.value + ';';
                var lbl = document.createElement('span');
                lbl.style.cssText = 'font-size:.68rem;color:#64748b;max-width:48px;text-align:center;line-height:1.2;';
                lbl.textContent = cf.label;
                swatch.appendChild(dot);
                swatch.appendChild(lbl);
                palette.appendChild(swatch);
                inp.addEventListener('input', function () { dot.style.background = this.value; });
            });

            var firstCard = document.querySelector('.customizer-content .admin-card');
            if (firstCard) {
                var previewWrap = document.createElement('div');
                previewWrap.style.cssText = 'padding:1rem;border-bottom:1px solid #f1f5f9;background:#fafafa;';
                var title = document.createElement('div');
                title.style.cssText = 'font-size:.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;';
                title.textContent = 'Farb-Vorschau';
                previewWrap.appendChild(title);
                previewWrap.appendChild(palette);
                firstCard.insertBefore(previewWrap, firstCard.firstChild);
            }
        }

        // Strg+S → Speichern
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                var btn = document.querySelector('button[type="submit"].btn-primary');
                if (btn) { btn.click(); }
            }
        });

    })();

    // globale Funktion für Text-Input-onchange
    function updateLivePreview() {
        var evt = new Event('input', { bubbles: true });
        document.querySelectorAll('input[type="color"]').forEach(function (p) { p.dispatchEvent(evt); });
    }

    // ── Logo-Upload Vorschau ─────────────────────────────────────────────────
    function previewLogoUpload(input) {
        if (!input.files || !input.files[0]) { return; }
        var reader = new FileReader();
        reader.onload = function (e) {
            var wrap = document.getElementById('logo-preview-wrap');
            var img  = document.getElementById('logo-preview-img');
            if (img && img.tagName === 'IMG') {
                img.src = e.target.result;
            } else if (wrap) {
                wrap.innerHTML = '<img id="logo-preview-img" src="' + e.target.result + '" style="max-height:48px;max-width:200px;">';
            }
            var urlField = document.querySelector('input[name="header_logo_url"]');
            if (urlField) { urlField.value = ''; }
        };
        reader.readAsDataURL(input.files[0]);
    }

    function syncLogoUrlPreview(url) {
        var wrap = document.getElementById('logo-preview-wrap');
        if (!wrap) { return; }
        if (url && url.match(/^https?:\/\//)) {
            wrap.innerHTML = '<img id="logo-preview-img" src="' + url + '" alt="Logo" style="max-height:48px;max-width:200px;" onerror="this.parentElement.innerHTML=\'<span style=color:#ef4444>Bild konnte nicht geladen werden</span>\'">';
        }
    }

    // ── Reset-Modal ──────────────────────────────────────────────────────────
    function showResetConfirm() {
        var m = document.getElementById('confirm-reset-modal');
        if (m) { m.style.display = 'flex'; }
    }
    function closeResetModal() {
        var m = document.getElementById('confirm-reset-modal');
        if (m) { m.style.display = 'none'; }
    }
    function confirmReset() {
        closeResetModal();
        document.getElementById('reset-form').submit();
    }
    window.addEventListener('click', function (e) {
        var m = document.getElementById('confirm-reset-modal');
        if (m && e.target === m) { closeResetModal(); }
    });
    </script>
</body>
</html>
