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
use CMS\Auth;
use CMS\Security;

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

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
            'login_btn_icon_only' => [
                'label'       => 'Anmelden-Button: Nur Icon',
                'description' => 'Zeigt nur das 🔑-Icon ohne Text – macht den Button schmaler.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'show_register_btn' => [
                'label'       => 'Registrieren-Button anzeigen',
                'description' => 'Zeigt den Registrieren-Button für nicht angemeldete Besucher.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'register_btn_icon_only' => [
                'label'       => 'Registrieren-Button: Nur Icon',
                'description' => 'Zeigt nur das ✏️-Icon ohne Text – macht den Button schmaler.',
                'type'        => 'checkbox',
                'default'     => false,
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
            'profile_show_jobs' => [
                'label'       => 'Profil-Menü: Stellenmarkt',
                'description' => 'Zeigt den Link zum Stellenmarkt im Profil-Dropdown (benötigt cms-jobprofile-generator).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'profile_show_booking' => [
                'label'       => 'Profil-Menü: Buchungsportal',
                'description' => 'Zeigt den Link zum Buchungsportal im Profil-Dropdown (benötigt cms-booking).',
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
            'homepage_mode' => [
                'label'       => 'Startseiten-Modus',
                'description' => 'Theme-Startseite: Hero/Sektionen aus dem Customizer. Landing Page: Daten aus dem CMS Landing-Page-Editor.',
                'type'        => 'select',
                'options'     => [
                    'theme'        => 'Theme-Startseite (Customizer)',
                    'landing_page' => 'CMS Landing Page',
                ],
                'default'     => 'theme',
            ],
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
            'show_speakers_section' => [
                'label'       => 'Speaker-Sektion anzeigen',
                'description' => 'Featured Speaker auf der Startseite (benötigt cms-speakers Plugin).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'speakers_section_title' => [
                'label'       => 'Speaker-Überschrift',
                'description' => 'Titel über der Speaker-Sektion.',
                'type'        => 'text',
                'default'     => 'Featured Speaker',
            ],
            'speakers_limit' => [
                'label'       => 'Anzahl Speaker',
                'description' => 'Maximale Anzahl angezeigter Speaker-Cards (1–12).',
                'type'        => 'number',
                'default'     => 4,
            ],
            'show_jobs_section' => [
                'label'       => 'Stellen-Sektion anzeigen',
                'description' => 'Aktuelle Stellenanzeigen auf der Startseite (benötigt cms-jobprofile-generator Plugin).',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'jobs_section_title' => [
                'label'       => 'Stellen-Überschrift',
                'description' => 'Titel über der Stellen-Sektion.',
                'type'        => 'text',
                'default'     => 'Aktuelle Stellen',
            ],
            'jobs_limit' => [
                'label'       => 'Anzahl Stellen',
                'description' => 'Maximale Anzahl angezeigter Stellen (1–15).',
                'type'        => 'number',
                'default'     => 5,
            ],
        ],
    ],

    'homepage_cta' => [
        'title' => '📣 Startseite – Call-to-Action',
        'sections' => [
            'cta_enabled' => [
                'label'       => 'CTA-Sektion anzeigen',
                'description' => 'Aktiviert den Call-to-Action-Banner auf der Startseite.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'cta_title' => [
                'label'       => 'CTA-Überschrift',
                'description' => 'Hauptüberschrift des CTA-Banners.',
                'type'        => 'text',
                'default'     => 'Bereit, Teil unseres Netzwerks zu werden?',
            ],
            'cta_text' => [
                'label'       => 'CTA-Beschreibung',
                'description' => 'Beschreibungstext unter der Überschrift (leer = ausgeblendet).',
                'type'        => 'textarea',
                'default'     => 'Registriere dich jetzt und vernetze dich mit IT-Experten, Unternehmen und Events.',
            ],
            'cta_button_text' => [
                'label'       => 'Button-Text',
                'description' => 'Beschriftung des CTA-Buttons.',
                'type'        => 'text',
                'default'     => 'Jetzt registrieren',
            ],
            'cta_button_url' => [
                'label'       => 'Button-URL',
                'description' => 'Ziel-URL des CTA-Buttons (relativ oder absolut).',
                'type'        => 'text',
                'default'     => '/register',
            ],
            'cta_button_secondary_text' => [
                'label'       => 'Sekundärer Button-Text',
                'description' => 'Optionaler zweiter Button (leer = ausgeblendet).',
                'type'        => 'text',
                'default'     => '',
            ],
            'cta_button_secondary_url' => [
                'label'       => 'Sekundärer Button-URL',
                'description' => 'Ziel-URL des zweiten Buttons.',
                'type'        => 'text',
                'default'     => '',
            ],
            'cta_style' => [
                'label'       => 'CTA-Stil',
                'description' => 'Farbschema des CTA-Banners.',
                'type'        => 'select',
                'options'     => [
                    'dark'     => 'Dunkel (Navy)',
                    'accent'   => 'Akzentfarbe (Gold)',
                    'gradient' => 'Gradient (Navy → Blau)',
                    'light'    => 'Hell (Weiß)',
                ],
                'default'     => 'dark',
            ],
            'cta_alignment' => [
                'label'       => 'Textausrichtung',
                'description' => 'Ausrichtung von Text und Buttons.',
                'type'        => 'select',
                'options'     => [
                    'center' => 'Zentriert',
                    'left'   => 'Links',
                ],
                'default'     => 'center',
            ],
            'cta_size' => [
                'label'       => 'CTA-Größe',
                'description' => 'Vertikales Padding des CTA-Banners.',
                'type'        => 'select',
                'options'     => [
                    'compact' => 'Kompakt',
                    'normal'  => 'Normal',
                    'large'   => 'Groß',
                ],
                'default'     => 'normal',
            ],
            'cta_show_logged_in' => [
                'label'       => 'Auch für eingeloggte User anzeigen',
                'description' => 'Wenn deaktiviert, wird der CTA nur für Gäste angezeigt.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    'sidebar' => [
        'title' => '📌 Sidebar (Startseite)',
        'sections' => [
            'sidebar_enabled' => [
                'label'       => 'Sidebar auf der Startseite anzeigen',
                'description' => 'Aktiviert die Sidebar im Dashboard-Layout der Startseite.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_position' => [
                'label'       => 'Sidebar-Position',
                'description' => 'Auf welcher Seite die Sidebar angezeigt wird.',
                'type'        => 'select',
                'options'     => [
                    'right' => 'Rechts (Standard)',
                    'left'  => 'Links',
                ],
                'default'     => 'right',
            ],
            'sidebar_width' => [
                'label'       => 'Sidebar-Breite (px)',
                'description' => 'Breite der Sidebar in Pixeln (Standard: 340).',
                'type'        => 'number',
                'default'     => 340,
            ],
            'sidebar_bg_color' => [
                'label'       => 'Sidebar-Hintergrundfarbe',
                'description' => 'Hintergrundfarbe der Sidebar-Panels.',
                'type'        => 'color',
                'default'     => '#ffffff',
            ],
            'sidebar_border_color' => [
                'label'       => 'Sidebar-Rahmenfarbe',
                'description' => 'Rahmenfarbe der Sidebar-Panels.',
                'type'        => 'color',
                'default'     => '#e2e8f0',
            ],
            'sidebar_border_radius' => [
                'label'       => 'Sidebar-Eckenradius (px)',
                'description' => 'Rundung der Sidebar-Panels.',
                'type'        => 'number',
                'default'     => 12,
            ],
            'sidebar_padding' => [
                'label'       => 'Sidebar-Innenabstand (rem)',
                'description' => 'Innenabstand der Sidebar-Panels.',
                'type'        => 'number',
                'default'     => 1.25,
            ],
            'sidebar_title_size' => [
                'label'       => 'Widget-Titel-Größe (rem)',
                'description' => 'Schriftgröße der Sidebar-Widget-Überschriften.',
                'type'        => 'number',
                'default'     => 1.0,
            ],
            'sidebar_title_color' => [
                'label'       => 'Widget-Titel-Farbe',
                'description' => 'Textfarbe der Widget-Überschriften.',
                'type'        => 'color',
                'default'     => '#1e293b',
            ],
            'sidebar_text_color' => [
                'label'       => 'Sidebar-Textfarbe',
                'description' => 'Standard-Textfarbe in der Sidebar.',
                'type'        => 'color',
                'default'     => '#475569',
            ],
            'sidebar_gap' => [
                'label'       => 'Abstand zwischen Widgets (rem)',
                'description' => 'Vertikaler Abstand zwischen Sidebar-Widgets.',
                'type'        => 'number',
                'default'     => 1.25,
            ],
            'sidebar_shadow' => [
                'label'       => 'Widget-Schatten anzeigen',
                'description' => 'Subtiler Schatten um Sidebar-Panels.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_booking_widget' => [
                'label'       => 'Buchungsportal-Widget anzeigen',
                'description' => 'Zeigt das Buchungsportal-Placeholder-Widget (bis ein Plugin es ersetzt).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'booking_widget_title' => [
                'label'       => 'Buchungsportal-Überschrift',
                'description' => 'Titel des Buchungsportal-Widgets.',
                'type'        => 'text',
                'default'     => '📅 Buchungsportal',
            ],
            'show_feed_widget' => [
                'label'       => 'Feed/News-Widget anzeigen',
                'description' => 'Zeigt das Feed-Aggregator-Widget (cms-feed Plugin oder Placeholder).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'feed_widget_title' => [
                'label'       => 'Feed-Widget-Überschrift',
                'description' => 'Titel des Feed-Widgets.',
                'type'        => 'text',
                'default'     => '📰 Feed-Aggregator',
            ],
            'feed_widget_count' => [
                'label'       => 'Anzahl Feed-Beiträge',
                'description' => 'Maximale Anzahl angezeigter Beiträge im Feed-Widget (1–10).',
                'type'        => 'number',
                'default'     => 5,
            ],
            'show_jobs_widget' => [
                'label'       => 'Job-Anzeigen-Widget anzeigen',
                'description' => 'Zeigt das Job-Anzeigen-Widget (cms-jobprofile-generator Plugin oder Placeholder).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'jobs_widget_title' => [
                'label'       => 'Job-Widget-Überschrift',
                'description' => 'Titel des Job-Anzeigen-Widgets.',
                'type'        => 'text',
                'default'     => '💼 Job-Anzeigen',
            ],
            'show_blog_widget' => [
                'label'       => 'Blog-Beiträge-Widget anzeigen',
                'description' => 'Zeigt die neuesten Blog-Beiträge in der Sidebar.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'blog_widget_title' => [
                'label'       => 'Blog-Widget-Überschrift',
                'description' => 'Titel des Blog-Widgets.',
                'type'        => 'text',
                'default'     => '📝 Aktuelle Beiträge',
            ],
            'blog_widget_count' => [
                'label'       => 'Anzahl Blog-Beiträge',
                'description' => 'Maximale Anzahl angezeigter Beiträge (1–10).',
                'type'        => 'number',
                'default'     => 5,
            ],
            'sidebar_custom_html' => [
                'label'       => 'Eigenes HTML-Widget',
                'description' => 'Zusätzliches HTML in der Sidebar (z. B. Banner, Partnerlogos). Wird nach allen Widgets angezeigt.',
                'type'        => 'textarea',
                'default'     => '',
            ],
        ],
    ],

    'speakers' => [
        'title' => '🎤 Speaker-Verzeichnis',
        'sections' => [
            'speakers_per_page' => [
                'label'       => 'Speaker pro Seite',
                'description' => 'Anzahl der Speaker-Karten pro Seite im Verzeichnis.',
                'type'        => 'number',
                'default'     => 12,
            ],
            'speakers_default_sort' => [
                'label'       => 'Standard-Sortierung',
                'description' => 'Voreingestellte Sortierung im Speaker-Verzeichnis.',
                'type'        => 'select',
                'options'     => [
                    'latest' => 'Neueste zuerst',
                    'name'   => 'Name (A–Z)',
                    'events' => 'Meiste Events',
                ],
                'default'     => 'latest',
            ],
            'speakers_show_event_count' => [
                'label'       => 'Event-Zähler anzeigen',
                'description' => 'Zeigt die Anzahl der Event-Teilnahmen auf der Speaker-Card.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'speakers_show_topics' => [
                'label'       => 'Themen-Tags anzeigen',
                'description' => 'Zeigt Topic-Tags auf der Speaker-Card.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'speakers_hero_title' => [
                'label'       => 'Verzeichnis-Titel',
                'description' => 'Überschrift der Speaker-Verzeichnis-Seite.',
                'type'        => 'text',
                'default'     => 'Speaker & Referenten',
            ],
            'speakers_hero_subtitle' => [
                'label'       => 'Verzeichnis-Untertitel',
                'description' => 'Beschreibungstext unter dem Titel (leer = ausgeblendet).',
                'type'        => 'text',
                'default'     => 'Entdecke erfahrene Speaker und Referenten aus der IT-Branche.',
            ],
        ],
    ],

    'jobs' => [
        'title' => '💼 Stellenmarkt',
        'sections' => [
            'jobs_per_page' => [
                'label'       => 'Stellen pro Seite',
                'description' => 'Anzahl der Stellenanzeigen pro Seite.',
                'type'        => 'number',
                'default'     => 15,
            ],
            'jobs_default_sort' => [
                'label'       => 'Standard-Sortierung',
                'description' => 'Voreingestellte Sortierung im Stellenmarkt.',
                'type'        => 'select',
                'options'     => [
                    'latest'   => 'Neueste zuerst',
                    'salary'   => 'Gehalt absteigend',
                    'title'    => 'Titel (A–Z)',
                ],
                'default'     => 'latest',
            ],
            'jobs_show_salary' => [
                'label'       => 'Gehalt anzeigen',
                'description' => 'Gehaltsangaben auf Job-Cards sichtbar machen.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'jobs_show_remote_badge' => [
                'label'       => 'Remote-Badge anzeigen',
                'description' => 'Markiert Remote-Jobs mit einem Badge.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'jobs_enable_alert' => [
                'label'       => 'Job-Alert-Funktion aktivieren',
                'description' => 'Zeigt den "Job Alert erstellen"-Button für eingeloggte Nutzer.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'jobs_hero_title' => [
                'label'       => 'Stellenmarkt-Titel',
                'description' => 'Überschrift der Stellenmarkt-Seite.',
                'type'        => 'text',
                'default'     => 'IT-Stellenmarkt',
            ],
            'jobs_hero_subtitle' => [
                'label'       => 'Stellenmarkt-Untertitel',
                'description' => 'Beschreibungstext unter dem Titel.',
                'type'        => 'text',
                'default'     => 'Finde deinen nächsten Job in der IT-Branche.',
            ],
        ],
    ],

    'feeds' => [
        'title' => '📰 Feed-Aggregator',
        'sections' => [
            'feeds_per_page' => [
                'label'       => 'Beiträge pro Seite',
                'description' => 'Anzahl der Feed-Beiträge pro Seite im Aggregator.',
                'type'        => 'number',
                'default'     => 18,
            ],
            'feeds_default_sort' => [
                'label'       => 'Standard-Sortierung',
                'description' => 'Voreingestellte Sortierung im Feed-Aggregator.',
                'type'        => 'select',
                'options'     => [
                    'latest' => 'Neueste zuerst',
                    'title'  => 'Titel (A–Z)',
                ],
                'default'     => 'latest',
            ],
            'feeds_show_source' => [
                'label'       => 'Quellenangabe anzeigen',
                'description' => 'Zeigt den Feed-Namen als Quellenangabe auf der Karte.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'feeds_open_external' => [
                'label'       => 'Links in neuem Tab öffnen',
                'description' => 'Öffnet Feed-Artikel in einem neuen Browser-Tab.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'feeds_hero_title' => [
                'label'       => 'Aggregator-Titel',
                'description' => 'Überschrift der Feed-Aggregator-Seite.',
                'type'        => 'text',
                'default'     => 'IT-News & Feeds',
            ],
            'feeds_hero_subtitle' => [
                'label'       => 'Aggregator-Untertitel',
                'description' => 'Beschreibungstext unter dem Titel.',
                'type'        => 'text',
                'default'     => 'Aktuelle News und Beiträge aus der IT-Welt.',
            ],
        ],
    ],

    'booking' => [
        'title' => '📅 Buchungsportal',
        'sections' => [
            'booking_enabled' => [
                'label'       => 'Buchungsportal aktivieren',
                'description' => 'Schaltet das Buchungsportal-Frontend frei.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'booking_require_login' => [
                'label'       => 'Anmeldung erforderlich',
                'description' => 'Buchen ist nur für eingeloggte Mitglieder möglich.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'booking_show_history' => [
                'label'       => 'Buchungsverlauf anzeigen',
                'description' => 'Zeigt dem Nutzer seine eigenen Buchungen in der Sidebar.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'booking_history_count' => [
                'label'       => 'Buchungsverlauf-Anzahl',
                'description' => 'Maximale Anzahl angezeigter eigener Buchungen (1–20).',
                'type'        => 'number',
                'default'     => 10,
            ],
            'booking_show_expert_sidebar' => [
                'label'       => 'Experten-Sidebar anzeigen',
                'description' => 'Zeigt verfügbare Experten in der Booking-Sidebar.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'booking_hero_title' => [
                'label'       => 'Buchungsportal-Titel',
                'description' => 'Überschrift der Buchungsportal-Seite.',
                'type'        => 'text',
                'default'     => 'Buchungsportal',
            ],
            'booking_hero_subtitle' => [
                'label'       => 'Buchungsportal-Untertitel',
                'description' => 'Beschreibungstext unter dem Titel.',
                'type'        => 'text',
                'default'     => 'Buche jetzt einen Termin mit unserem Team oder IT-Experten.',
            ],
            'booking_intro_text' => [
                'label'       => 'Einführungstext',
                'description' => 'Text über dem Buchungsformular (leer = ausgeblendet).',
                'type'        => 'textarea',
                'default'     => '',
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

$coreMainCssUrl = function_exists('cms_asset_url')
    ? cms_asset_url('css/main.css')
    : SITE_URL . '/assets/css/main.css';
$coreAdminCssUrl = function_exists('cms_asset_url')
    ? cms_asset_url('css/admin.css')
    : SITE_URL . '/assets/css/admin.css?v=20260222b';
$coreAdminJsUrl = function_exists('cms_asset_url')
    ? cms_asset_url('js/admin.js')
    : SITE_URL . '/assets/js/admin.js';

$themeUrl = class_exists('\CMS\ThemeManager')
    ? rtrim((string) \CMS\ThemeManager::instance()->getThemeUrl(), '/')
    : rtrim(SITE_URL, '/') . '/themes/365Network';

$customizerCssFile = dirname(__DIR__) . '/css/customizer-admin.css';
$customizerJsFile  = dirname(__DIR__) . '/js/customizer-admin.js';
$customizerCssUrl  = is_file($customizerCssFile)
    ? $themeUrl . '/css/customizer-admin.css?v=' . rawurlencode((string) filemtime($customizerCssFile))
    : '';
$customizerJsUrl = is_file($customizerJsFile)
    ? $themeUrl . '/js/customizer-admin.js?v=' . rawurlencode((string) filemtime($customizerJsFile))
    : '';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Customizer – <?php echo defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : '365Network'; ?></title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($coreMainCssUrl, ENT_QUOTES); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($coreAdminCssUrl, ENT_QUOTES); ?>">
    <?php if ($customizerCssUrl !== ''): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($customizerCssUrl, ENT_QUOTES); ?>">
    <?php endif; ?>
    <?php renderAdminSidebarStyles(); ?>
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

        <form id="customizer-form" method="POST" action="?tab=<?php echo htmlspecialchars($activeTab); ?>" enctype="multipart/form-data">
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
                        <div class="customizer-color-grid">
                            <?php foreach ($colorGroups as $groupTitle => $groupKeys): ?>
                            <div class="customizer-color-card">
                                <h4><?php echo $groupTitle; ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default'] ?? '');
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $textInputId = $inputId . '_text';
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $inputId; ?>" class="form-label">
                                        <?php echo htmlspecialchars($field['label']); ?>
                                    </label>
                                    <div class="customizer-control-row">
                                        <input type="color" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                               value="<?php echo htmlspecialchars((string)$val); ?>"
                                               class="customizer-color-picker"
                                               data-customizer-color-picker
                                               data-sync-text="<?php echo $textInputId; ?>">
                                        <input type="text" id="<?php echo $textInputId; ?>" value="<?php echo htmlspecialchars((string)$val); ?>"
                                               class="form-control customizer-color-text"
                                               data-customizer-color-text
                                               data-sync-picker="<?php echo $inputId; ?>">
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
                            '⚙️ Startseiten-Modus' => ['homepage_mode'],
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
                        <div class="customizer-section-grid">
                            <?php foreach ($homepageGroups as $groupTitle => $groupKeys): ?>
                            <div class="customizer-section-card">
                                <h4><?php echo $groupTitle; ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default'] ?? '');
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $inputId; ?>" class="form-label">
                                        <?php echo htmlspecialchars($field['label']); ?>
                                    </label>

                                    <?php if ($field['type'] === 'checkbox'): ?>
                                        <div class="customizer-checkbox-row">
                                            <input type="checkbox" id="<?php echo $inputId; ?>"
                                                   name="<?php echo $inputName; ?>" value="1"
                                                   <?php echo $val ? 'checked' : ''; ?>>
                                            <label for="<?php echo $inputId; ?>" class="customizer-checkbox-label">Aktivieren</label>
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
                                                 class="form-control customizer-number-input" min="1" max="12">

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

                    <?php elseif ($activeTab === 'header' || $activeTab === 'footer' || $activeTab === 'buttons' || $activeTab === 'sidebar'):
                        // ── Header / Footer / Buttons: 2-Spalten-Karten-Layout ──────
                        $tabCardGroups = [
                            'header' => [
                                '🖼️ Logo' => ['logo_url', 'logo_max_height'],
                                '🎨 Header-Farben' => ['header_bg_color', 'header_text_color', 'header_accent_color'],
                                '📐 Header-Layout' => ['header_height', 'show_header_shadow'],
                                '🔍 Header-Buttons' => ['show_search_btn', 'show_login_btn', 'login_btn_icon_only', 'show_register_btn', 'register_btn_icon_only'],
                                '👤 Profil-Dropdown' => ['profile_show_dashboard', 'profile_show_expert', 'profile_show_company', 'profile_show_events', 'profile_show_speaker'],
                            ],
                            'footer' => [
                                '🎨 Footer-Farben' => ['footer_bg_color', 'footer_text_color', 'footer_link_color'],
                                '📝 Footer-Inhalte' => ['footer_text', 'copyright_text', 'show_network_widgets'],
                                '🌐 Social Media' => ['social_twitter', 'social_instagram', 'social_linkedin', 'social_youtube'],
                            ],
                            'buttons' => [
                                '📐 Button-Form' => ['button_border_radius', 'button_padding_x', 'button_padding_y'],
                                '🔤 Button-Text' => ['button_font_weight', 'button_transform'],
                            ],
                            'sidebar' => [
                                '⚙️ Sidebar-Grundeinstellungen' => ['sidebar_enabled', 'sidebar_position', 'sidebar_width', 'sidebar_gap'],
                                '🎨 Sidebar-Design' => ['sidebar_bg_color', 'sidebar_border_color', 'sidebar_border_radius', 'sidebar_padding', 'sidebar_shadow'],
                                '🔤 Sidebar-Typografie' => ['sidebar_title_size', 'sidebar_title_color', 'sidebar_text_color'],
                                '📅 Buchungsportal-Widget' => ['show_booking_widget', 'booking_widget_title'],
                                '📰 Feed-Widget' => ['show_feed_widget', 'feed_widget_title', 'feed_widget_count'],
                                '💼 Job-Widget' => ['show_jobs_widget', 'jobs_widget_title'],
                                '📝 Blog-Widget' => ['show_blog_widget', 'blog_widget_title', 'blog_widget_count'],
                                '🧩 Eigenes HTML' => ['sidebar_custom_html'],
                            ],
                        ];
                        $cardGroups = $tabCardGroups[$activeTab] ?? [];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo htmlspecialchars($currentSection['title']); ?></h3>
                        <div class="customizer-section-grid">
                            <?php foreach ($cardGroups as $groupTitle => $groupKeys): ?>
                            <div class="customizer-section-card">
                                <h4><?php echo $groupTitle; ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default'] ?? '');
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $textInputId = $inputId . '_text';
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $inputId; ?>" class="form-label">
                                        <?php echo htmlspecialchars($field['label']); ?>
                                    </label>

                                    <?php if ($field['type'] === 'image_upload'): ?>
                                        <?php $previewUrl = $val ? htmlspecialchars((string)$val) : ''; ?>
                                        <div class="customizer-control-row-stack" data-customizer-logo-group>
                                            <div class="customizer-logo-preview" data-customizer-logo-preview>
                                                <?php if ($previewUrl): ?>
                                                    <img src="<?php echo $previewUrl; ?>" alt="Logo" class="customizer-logo-preview-image">
                                                <?php else: ?>
                                                    <span class="customizer-logo-preview-placeholder">🖼️ Noch kein Logo ausgewählt</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="customizer-control-row-inline">
                                                <label class="customizer-upload-button">
                                                    📁 Bild hochladen
                                                    <input type="file" name="logo_upload_file" accept="image/*"
                                                           class="customizer-file-input"
                                                           data-customizer-logo-upload>
                                                </label>
                                                <span class="customizer-upload-hint">oder URL eingeben:</span>
                                            </div>
                                            <input type="text" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                                   value="<?php echo $previewUrl; ?>" class="form-control"
                                                   placeholder="https://..." data-customizer-logo-url>
                                        </div>

                                    <?php elseif ($field['type'] === 'color'): ?>
                                        <div class="customizer-control-row">
                                            <input type="color" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                                   value="<?php echo htmlspecialchars((string)$val); ?>"
                                                   class="customizer-color-picker"
                                                   data-customizer-color-picker
                                                   data-sync-text="<?php echo $textInputId; ?>">
                                            <input type="text" id="<?php echo $textInputId; ?>" value="<?php echo htmlspecialchars((string)$val); ?>"
                                                   class="form-control customizer-color-text"
                                                   data-customizer-color-text
                                                   data-sync-picker="<?php echo $inputId; ?>">
                                        </div>

                                    <?php elseif ($field['type'] === 'checkbox'): ?>
                                        <div class="customizer-checkbox-row">
                                            <input type="checkbox" id="<?php echo $inputId; ?>"
                                                   name="<?php echo $inputName; ?>" value="1"
                                                   <?php echo $val ? 'checked' : ''; ?>>
                                            <label for="<?php echo $inputId; ?>" class="customizer-checkbox-label">Aktivieren</label>
                                        </div>

                                    <?php elseif ($field['type'] === 'textarea'): ?>
                                        <textarea id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                                  class="form-control" rows="3"
                                        ><?php echo htmlspecialchars((string)$val); ?></textarea>

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
                                               class="form-control customizer-number-input">

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
                            $val       = $customizer->get($activeTab, $fieldKey, $field['default'] ?? '');
                            $inputId   = "field_{$activeTab}_{$fieldKey}";
                            $textInputId = $inputId . '_text';
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
                                <div class="customizer-checkbox-row">
                                    <input type="checkbox" id="<?php echo $inputId; ?>"
                                           name="<?php echo $inputName; ?>" value="1"
                                           <?php echo $val ? 'checked' : ''; ?>>
                                    <label for="<?php echo $inputId; ?>" class="customizer-checkbox-label">Aktivieren</label>
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
                                <div class="customizer-control-row-stack" data-customizer-logo-group>
                                    <div class="customizer-logo-preview" data-customizer-logo-preview>
                                        <?php if ($previewUrl): ?>
                                            <img src="<?php echo $previewUrl; ?>" alt="Logo" class="customizer-logo-preview-image">
                                        <?php else: ?>
                                            <span class="customizer-logo-preview-placeholder">🖼️ Noch kein Logo ausgewählt</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="customizer-control-row-inline">
                                        <label class="customizer-upload-button">
                                            📁 Bild hochladen
                                            <input type="file" name="logo_upload_file" accept="image/*"
                                                   class="customizer-file-input"
                                                   data-customizer-logo-upload>
                                        </label>
                                        <span class="customizer-upload-hint">oder URL eingeben:</span>
                                    </div>
                                    <input type="text" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                           value="<?php echo $previewUrl; ?>" class="form-control"
                                           placeholder="https://..." data-customizer-logo-url>
                                </div>

                            <?php elseif ($field['type'] === 'color'): ?>
                                <div class="customizer-control-row">
                                    <input type="color" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                           value="<?php echo htmlspecialchars((string)$val); ?>"
                                           class="customizer-color-picker"
                                           data-customizer-color-picker
                                           data-sync-text="<?php echo $textInputId; ?>">
                                    <input type="text" id="<?php echo $textInputId; ?>" value="<?php echo htmlspecialchars((string)$val); ?>"
                                           class="form-control customizer-color-text"
                                           data-customizer-color-text
                                           data-sync-picker="<?php echo $inputId; ?>">
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
                    <?php endif; /* colors / homepage / header / footer / buttons / standard tab */ ?>

                    <!-- Sticky Speichern-Leiste -->
                    <div class="admin-card customizer-sticky-card">
                        <div class="form-actions customizer-form-actions">
                            <button type="submit" class="btn btn-primary">💾 Einstellungen speichern</button>
                            <button type="button" class="btn btn-secondary"
                                    data-customizer-reset-open
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
    <form id="reset-form" method="POST" action="?tab=<?php echo htmlspecialchars($activeTab); ?>" class="customizer-hidden">
        <input type="hidden" name="action" value="reset_theme_tab">
        <input type="hidden" name="active_section" value="<?php echo htmlspecialchars($activeTab); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    </form>
    <?php endif; ?>

    </div><!-- /.admin-content -->

    <!-- Reset-Bestätigungsmodal -->
    <div id="confirm-reset-modal" class="modal customizer-reset-modal" hidden aria-hidden="true">
        <div class="modal-content customizer-reset-dialog">
            <div class="modal-header">
                <h3>⚠️ Einstellungen zurücksetzen?</h3>
                <button class="modal-close" type="button" data-customizer-reset-close>&times;</button>
            </div>
            <div class="modal-body">
                <p>Alle Einstellungen dieses Tabs werden auf die <strong>Standard-Designwerte</strong> des 365Network-Themes zurückgesetzt.</p>
                <p class="customizer-reset-note">Bereits gespeicherte Anpassungen gehen für diesen Bereich verloren.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-customizer-reset-close>Abbrechen</button>
                <button type="button" class="btn btn-danger" data-customizer-reset-confirm>↺ Zurücksetzen</button>
            </div>
        </div>
    </div>

    <script src="<?php echo htmlspecialchars($coreAdminJsUrl, ENT_QUOTES); ?>"></script>
    <?php if ($customizerJsUrl !== ''): ?>
        <script src="<?php echo htmlspecialchars($customizerJsUrl, ENT_QUOTES); ?>"></script>
    <?php endif; ?>
</body>
</html>
