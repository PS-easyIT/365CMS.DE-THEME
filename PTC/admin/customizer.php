<?php
/**
 * PTC GmbH Theme – Customizer (Admin)
 *
 * Umfangreiche Anpassungsmöglichkeiten für Farben, Typografie, Layout,
 * Header, Footer, Buttons, Startseite (Hero, Dienstleistungen, Termine,
 * FAQ, CTA) und erweiterte Einstellungen.
 *
 * @package PTC_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Auth;
use CMS\Security;
use CMS\Services\ThemeCustomizer;

if (!Auth::instance()->isAdmin()) {
    header('Location: ' . SITE_URL);
    exit;
}

// ── Admin-Sidebar laden ──────────────────────────────────────────────────────
$sidebarPaths = [
    dirname(__DIR__, 2) . '/../CMS/admin/partials/admin-menu.php',
    ABSPATH . 'admin/partials/admin-menu.php',
    dirname(ABSPATH) . '/admin/partials/admin-menu.php',
];
foreach ($sidebarPaths as $sp) {
    if (file_exists($sp)) {
        require_once $sp;
        break;
    }
}

// ── 1. Konfiguration aller Tabs & Felder ─────────────────────────────────────
$config = [

    // ─────────────────────────────────────────────────────────────
    // FARBEN
    // ─────────────────────────────────────────────────────────────
    'colors' => [
        'title' => '🎨 Farben',
        'sections' => [
            'primary_color' => [
                'label'       => 'Primärfarbe (Navy)',
                'description' => 'Hauptfarbe – Marineblau der PTC Corporate Identity.',
                'type'        => 'color',
                'default'     => '#002D5D',
            ],
            'primary_hover' => [
                'label'       => 'Primärfarbe Hover',
                'description' => 'Dunklerer Ton für Hover-Zustände auf Navy-Elementen.',
                'type'        => 'color',
                'default'     => '#001F42',
            ],
            'primary_light' => [
                'label'       => 'Primärfarbe Hell',
                'description' => 'Aufgehellter Ton für Hintergründe und Badges.',
                'type'        => 'color',
                'default'     => '#E8F0FE',
            ],
            'accent_color' => [
                'label'       => 'Akzentfarbe (Gold)',
                'description' => 'Goldton der PTC Corporate Identity – CTAs, Highlights.',
                'type'        => 'color',
                'default'     => '#D4A017',
            ],
            'accent_hover' => [
                'label'       => 'Akzentfarbe Hover',
                'description' => 'Dunkleres Gold für Hover-Zustände.',
                'type'        => 'color',
                'default'     => '#B8860B',
            ],
            'accent_light' => [
                'label'       => 'Akzentfarbe Hell',
                'description' => 'Heller Goldton für dezente Markierungen.',
                'type'        => 'color',
                'default'     => '#FDF5E6',
            ],
            'secondary_color' => [
                'label'       => 'Sekundärfarbe (Slate)',
                'description' => 'Schiefergrau für Nebenelemente und Labels.',
                'type'        => 'color',
                'default'     => '#607D8B',
            ],
            'text_color' => [
                'label'       => 'Textfarbe',
                'description' => 'Standard-Fließtextfarbe auf hellem Hintergrund.',
                'type'        => 'color',
                'default'     => '#334155',
            ],
            'heading_color' => [
                'label'       => 'Überschriftenfarbe',
                'description' => 'Farbe aller Überschriften (h1–h6).',
                'type'        => 'color',
                'default'     => '#002D5D',
            ],
            'text_light' => [
                'label'       => 'Helle Textfarbe',
                'description' => 'Helle Textfarbe für dunkle Hintergründe (Header, Footer, Hero).',
                'type'        => 'color',
                'default'     => '#F8F9FA',
            ],
            'muted_color' => [
                'label'       => 'Gedämpfte Textfarbe',
                'description' => 'Sehr dezenter Text – Timestamps, Platzhalter, Hints.',
                'type'        => 'color',
                'default'     => '#94a3b8',
            ],
            'bg_color' => [
                'label'       => 'Seitenhintergrund',
                'description' => 'Hintergrundfarbe des Content-Bereichs (Off-White).',
                'type'        => 'color',
                'default'     => '#F8F9FA',
            ],
            'bg_secondary' => [
                'label'       => 'Sekundärer Hintergrund',
                'description' => 'Hintergrund für Cards und alternierende Sektionen.',
                'type'        => 'color',
                'default'     => '#F1F5F9',
            ],
            'link_color' => [
                'label'       => 'Linkfarbe',
                'description' => 'Standard-Linkfarbe im Content-Bereich.',
                'type'        => 'color',
                'default'     => '#002D5D',
            ],
            'link_hover_color' => [
                'label'       => 'Link Hover-Farbe',
                'description' => 'Linkfarbe beim Hover-Zustand.',
                'type'        => 'color',
                'default'     => '#D4A017',
            ],
            'border_color' => [
                'label'       => 'Rahmenfarbe',
                'description' => 'Standard-Rahmenfarbe für Trennlinien, Cards und Inputs.',
                'type'        => 'color',
                'default'     => '#E2E8F0',
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

    // ─────────────────────────────────────────────────────────────
    // TYPOGRAFIE
    // ─────────────────────────────────────────────────────────────
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
                'description' => 'Schriftart für alle Überschriften.',
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

    // ─────────────────────────────────────────────────────────────
    // LAYOUT
    // ─────────────────────────────────────────────────────────────
    'layout' => [
        'title' => '📐 Layout',
        'sections' => [
            'container_width' => [
                'label'       => 'Container-Breite (px)',
                'description' => 'Maximale Breite des Inhaltsbereichs.',
                'type'        => 'number',
                'default'     => 1280,
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
                'default'     => 12,
            ],
            'section_spacing' => [
                'label'       => 'Sektionsabstand (rem)',
                'description' => 'Vertikaler Abstand zwischen Seitenbereichen.',
                'type'        => 'number',
                'default'     => 5,
            ],
            'enable_sticky_header' => [
                'label'       => 'Sticky Header aktivieren',
                'description' => 'Header bleibt beim Scrollen oben fixiert.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // HEADER & LOGO
    // ─────────────────────────────────────────────────────────────
    'header' => [
        'title' => '🖼️ Header & Logo',
        'sections' => [
            'logo_url' => [
                'label'       => 'Header Logo',
                'description' => 'Logo-Bild im Header (JPG, PNG, WebP, SVG – max. 2 MB). Leer = Text-Logo.',
                'type'        => 'image_upload',
                'default'     => '',
            ],
            'logo_text' => [
                'label'       => 'Logo-Text (Wordmark)',
                'description' => 'Text-Logo als Fallback wenn kein Bild vorhanden.',
                'type'        => 'text',
                'default'     => 'PTC GmbH',
            ],
            'header_bg_color' => [
                'label'       => 'Header-Hintergrundfarbe',
                'description' => 'Hintergrundfarbe der Navigationsleiste (Navy).',
                'type'        => 'color',
                'default'     => '#002D5D',
            ],
            'header_text_color' => [
                'label'       => 'Header-Textfarbe',
                'description' => 'Farbe von Navigationslinks und Logo-Text.',
                'type'        => 'color',
                'default'     => '#F8F9FA',
            ],
            'header_accent_color' => [
                'label'       => 'Header-Akzentfarbe',
                'description' => 'Akzentfarbe im Header – aktive Links, Hover-Zustand.',
                'type'        => 'color',
                'default'     => '#D4A017',
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
            'show_login_btn' => [
                'label'       => 'Anmelden-Button anzeigen',
                'description' => 'Zeigt den Login-Button für nicht angemeldete Besucher.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'show_register_btn' => [
                'label'       => 'Registrieren-Button anzeigen',
                'description' => 'Zeigt den Registrieren-Button für nicht angemeldete Besucher.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'header_cta_text' => [
                'label'       => 'Header CTA-Button Text',
                'description' => 'Text des Kontakt-/CTA-Buttons im Header. Leer = ausgeblendet.',
                'type'        => 'text',
                'default'     => 'Kontakt',
            ],
            'header_cta_url' => [
                'label'       => 'Header CTA-Button URL',
                'description' => 'Ziel-URL des CTA-Buttons im Header.',
                'type'        => 'text',
                'default'     => '/#kontakt',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // FOOTER
    // ─────────────────────────────────────────────────────────────
    'footer' => [
        'title' => '🔻 Footer',
        'sections' => [
            'footer_bg_color' => [
                'label'       => 'Footer-Hintergrundfarbe',
                'description' => 'Hintergrundfarbe des Footer-Bereichs.',
                'type'        => 'color',
                'default'     => '#001A33',
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
                'default'     => '#F8F9FA',
            ],
            'footer_tagline' => [
                'label'       => 'Footer-Beschreibungstext',
                'description' => 'Text im ersten Footer-Widget (z. B. Firmenbeschreibung).',
                'type'        => 'textarea',
                'default'     => 'PTC GmbH – Ihr Partner für Bildung, Karriere und Zukunft.',
            ],
            'footer_address' => [
                'label'       => 'Adresse / Kontakt',
                'description' => 'Adressdaten im Kontakt-Widget des Footers.',
                'type'        => 'textarea',
                'default'     => "PTC GmbH\nMusterstraße 1\n12345 Musterstadt",
            ],
            'footer_phone' => [
                'label'       => 'Telefon',
                'description' => 'Telefonnummer im Footer-Kontaktbereich.',
                'type'        => 'text',
                'default'     => '',
            ],
            'footer_email' => [
                'label'       => 'E-Mail',
                'description' => 'E-Mail-Adresse im Footer-Kontaktbereich.',
                'type'        => 'text',
                'default'     => '',
            ],
            'copyright_text' => [
                'label'       => 'Copyright-Text',
                'description' => 'Copyright-Zeile unten. Platzhalter: {year}, {site_title}.',
                'type'        => 'text',
                'default'     => '© {year} {site_title}. Alle Rechte vorbehalten.',
            ],
            'social_facebook' => [
                'label'       => 'Facebook URL',
                'description' => 'Vollständige URL zum Facebook-Profil (leer = ausgeblendet).',
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
            'social_xing' => [
                'label'       => 'Xing URL',
                'description' => 'Vollständige URL zum Xing-Profil (leer = ausgeblendet).',
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

    // ─────────────────────────────────────────────────────────────
    // BUTTONS
    // ─────────────────────────────────────────────────────────────
    'buttons' => [
        'title' => '🔘 Buttons',
        'sections' => [
            'button_border_radius' => [
                'label'       => 'Button-Eckenradius (px)',
                'description' => 'Eckenrundung aller regulären Buttons.',
                'type'        => 'number',
                'default'     => 8,
            ],
            'button_padding_x' => [
                'label'       => 'Button-Padding horizontal (rem)',
                'description' => 'Horizontaler Innenabstand der Buttons.',
                'type'        => 'number',
                'default'     => 2,
            ],
            'button_padding_y' => [
                'label'       => 'Button-Padding vertikal (rem)',
                'description' => 'Vertikaler Innenabstand der Buttons.',
                'type'        => 'number',
                'default'     => 0.875,
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

    // ─────────────────────────────────────────────────────────────
    // STARTSEITE
    // ─────────────────────────────────────────────────────────────
    'homepage' => [
        'title' => '🏠 Startseite',
        'sections' => [
            // ── Hero ──
            'show_hero' => [
                'label'       => 'Hero-Sektion anzeigen',
                'description' => 'Zeigt die große Hero-Sektion mit Badge, Überschrift und CTAs.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'hero_badge' => [
                'label'       => 'Hero-Badge Text',
                'description' => 'Kleiner Badge-Text über der Überschrift.',
                'type'        => 'text',
                'default'     => 'Ihr Partner für Bildung, Karriere und Zukunft',
            ],
            'hero_title' => [
                'label'       => 'Hero-Überschrift',
                'description' => 'Hauptüberschrift – HTML-Tags wie &lt;span class="highlight"&gt; erlaubt.',
                'type'        => 'text',
                'default'     => 'Willkommen bei <span class="highlight">PTC GmbH</span> – Ihr Partner für Personaldienstleistungen.',
            ],
            'hero_text' => [
                'label'       => 'Hero-Beschreibungstext',
                'description' => 'Einleitungstext unter der Überschrift.',
                'type'        => 'textarea',
                'default'     => 'Wir verbinden Menschen mit Chancen: Personalvermittlung, Arbeitnehmerüberlassung, Akademie & Bildung und Logistiklehrwerkstatt – alles aus einer Hand.',
            ],
            'hero_cta_primary_label' => [
                'label'       => 'Primärer CTA-Button Text',
                'description' => 'Beschriftung des Haupt-Buttons in der Hero-Sektion.',
                'type'        => 'text',
                'default'     => 'Entdecken Sie Ihre Möglichkeiten',
            ],
            'hero_cta_primary_url' => [
                'label'       => 'Primärer CTA-Button URL',
                'description' => 'Ziel-URL des Haupt-Buttons (z. B. #dienstleistungen).',
                'type'        => 'text',
                'default'     => '#dienstleistungen',
            ],
            'hero_cta_secondary_label' => [
                'label'       => 'Sekundärer CTA-Button Text',
                'description' => 'Beschriftung des zweiten Buttons. Leer = ausgeblendet.',
                'type'        => 'text',
                'default'     => 'Kontakt aufnehmen',
            ],
            'hero_cta_secondary_url' => [
                'label'       => 'Sekundärer CTA-Button URL',
                'description' => 'Ziel-URL des zweiten Buttons.',
                'type'        => 'text',
                'default'     => '#kontakt',
            ],
            'hero_bg_image' => [
                'label'       => 'Hero-Hintergrundbild',
                'description' => 'Optionales Hintergrundbild für den Hero-Bereich.',
                'type'        => 'image_upload',
                'default'     => '',
            ],

            // ── Dienstleistungen ──
            'show_services' => [
                'label'       => 'Dienstleistungen-Sektion anzeigen',
                'description' => 'Zeigt den Dienstleistungs-Bereich mit Service-Cards.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'services_title' => [
                'label'       => 'Dienstleistungen-Überschrift',
                'description' => 'Titel über der Dienstleistungs-Sektion.',
                'type'        => 'text',
                'default'     => 'Unsere Dienstleistungen',
            ],
            'services_subtitle' => [
                'label'       => 'Dienstleistungen-Untertitel',
                'description' => 'Kurze Beschreibung unter dem Titel.',
                'type'        => 'text',
                'default'     => 'Von Aktivierung über Logistik bis hin zur Personalvermittlung – wir bieten maßgeschneiderte Lösungen.',
            ],
            'services_columns' => [
                'label'       => 'Spaltenanzahl (Cards)',
                'description' => 'Wie viele Service-Cards pro Zeile (2 oder 3).',
                'type'        => 'select',
                'options'     => [
                    '2' => '2 Spalten',
                    '3' => '3 Spalten (Standard)',
                ],
                'default'     => '3',
            ],

            // ── Termine ──
            'show_events' => [
                'label'       => 'Termine-Sektion anzeigen',
                'description' => 'Zeigt den "Aktuelle Termine"-Bereich.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'events_title' => [
                'label'       => 'Termine-Überschrift',
                'description' => 'Titel über der Termine-Sektion.',
                'type'        => 'text',
                'default'     => 'Aktuelle Termine & Angebote',
            ],
            'events_subtitle' => [
                'label'       => 'Termine-Untertitel',
                'description' => 'Beschreibung unter dem Titel.',
                'type'        => 'text',
                'default'     => 'Entdecken Sie unsere aktuellen Kursangebote, Workshops und Veranstaltungen.',
            ],

            // ── FAQ ──
            'show_faq' => [
                'label'       => 'FAQ-Sektion anzeigen',
                'description' => 'Zeigt den Bereich mit häufig gestellten Fragen.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'faq_title' => [
                'label'       => 'FAQ-Überschrift',
                'description' => 'Titel über dem FAQ-Bereich.',
                'type'        => 'text',
                'default'     => 'Häufig gestellte Fragen',
            ],
            'faq_subtitle' => [
                'label'       => 'FAQ-Untertitel',
                'description' => 'Beschreibung unter dem FAQ-Titel.',
                'type'        => 'text',
                'default'     => 'Hier finden Sie Antworten auf die wichtigsten Fragen zu unseren Dienstleistungen.',
            ],

            // ── CTA ──
            'show_cta' => [
                'label'       => 'CTA-Sektion anzeigen',
                'description' => 'Zeigt den Call-to-Action-Bereich am Seitenende.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'cta_title' => [
                'label'       => 'CTA-Überschrift',
                'description' => 'Überschrift des Call-to-Action-Bereichs.',
                'type'        => 'text',
                'default'     => 'Bereit für den nächsten Karriereschritt?',
            ],
            'cta_text' => [
                'label'       => 'CTA-Beschreibungstext',
                'description' => 'Text im Call-to-Action-Bereich.',
                'type'        => 'textarea',
                'default'     => 'Ob Arbeitnehmer auf Jobsuche oder Unternehmen mit Personalbedarf – sprechen Sie uns an. Wir finden die passende Lösung für Sie.',
            ],
            'cta_button_label' => [
                'label'       => 'CTA-Button Text',
                'description' => 'Beschriftung des CTA-Buttons.',
                'type'        => 'text',
                'default'     => 'Jetzt Kontakt aufnehmen',
            ],
            'cta_button_url' => [
                'label'       => 'CTA-Button URL',
                'description' => 'Ziel-URL des CTA-Buttons.',
                'type'        => 'text',
                'default'     => '/#kontakt',
            ],
            'cta_secondary_label' => [
                'label'       => 'CTA zweiter Button Text',
                'description' => 'Beschriftung des zweiten CTA-Buttons. Leer = ausgeblendet.',
                'type'        => 'text',
                'default'     => 'Unsere Leistungen entdecken',
            ],
            'cta_secondary_url' => [
                'label'       => 'CTA zweiter Button URL',
                'description' => 'Ziel-URL des zweiten CTA-Buttons.',
                'type'        => 'text',
                'default'     => '/#dienstleistungen',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // ERWEITERT
    // ─────────────────────────────────────────────────────────────
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

// -- Reset --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_theme_tab') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'ptc_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        $resetTab = $_POST['active_section'] ?? $activeTab;
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

// -- Save --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_theme_options') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'ptc_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        // Logo-Upload
        if (!empty($_FILES['logo_upload_file']['tmp_name'])) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            $fileExt     = strtolower(pathinfo($_FILES['logo_upload_file']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowedExts, true)) {
                $uploadDir = UPLOAD_PATH . 'theme-logos';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName = 'ptc-logo-' . time() . '.' . $fileExt;
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

        // Hero-Hintergrundbild-Upload
        if (!empty($_FILES['hero_bg_upload_file']['tmp_name'])) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExt     = strtolower(pathinfo($_FILES['hero_bg_upload_file']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowedExts, true)) {
                $uploadDir = UPLOAD_PATH . 'theme-images';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName = 'ptc-hero-' . time() . '.' . $fileExt;
                $destPath    = $uploadDir . '/' . $newFileName;
                if (move_uploaded_file($_FILES['hero_bg_upload_file']['tmp_name'], $destPath)) {
                    $customizer->set('homepage', 'hero_bg_image', UPLOAD_URL . '/theme-images/' . $newFileName);
                } else {
                    $error = 'Bild-Upload fehlgeschlagen. Bitte prüfen Sie die Schreibrechte.';
                }
            } else {
                $error = 'Ungültiges Dateiformat. Erlaubt: JPG, PNG, GIF, WebP';
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

                // Bild-Uploads: nur speichern wenn explizit befüllt (Datei-Upload hat Vorrang)
                if ($fieldConfig['type'] === 'image_upload') {
                    $postVal = $_POST[$inputName] ?? '';
                    if ($postVal !== '' || $fieldKey === 'logo_url' || $fieldKey === 'hero_bg_image') {
                        // File upload already handled above
                        if ($postVal !== '') {
                            if (!$customizer->set($saveTab, $fieldKey, $postVal)) {
                                $saveFailed = true;
                            }
                        }
                    }
                    continue;
                }

                if ($fieldConfig['type'] === 'checkbox') {
                    $value = isset($_POST[$inputName]) ? '1' : '0';
                } else {
                    $value = $_POST[$inputName] ?? '';
                }
                if (!$customizer->set($saveTab, $fieldKey, $value)) {
                    $saveFailed = true;
                }
            }
            if ($saveFailed) {
                $error = 'Einstellungen konnten nicht gespeichert werden. Bitte Fehler-Log prüfen.';
            } else {
                $success = 'Einstellungen für &bdquo;' . htmlspecialchars($config[$saveTab]['title']) . '&ldquo; gespeichert.';
            }
        }
    }
}

// Token EINMAL generieren
$csrfToken = Security::instance()->generateToken('ptc_customizer');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Customizer – <?php echo defined('SITE_NAME') ? htmlspecialchars(SITE_NAME) : 'PTC GmbH'; ?></title>
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

        /* Farben-Tab: 3-Spalten */
        .color-cards-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
        .color-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; }
        .color-card h4 { margin: 0 0 1rem 0; font-size: .95rem; font-weight: 700; color: #1e293b; padding-bottom: .75rem; border-bottom: 1px solid #f1f5f9; }
        .color-card .form-group { margin-bottom: 1rem; }
        .color-card .form-group:last-child { margin-bottom: 0; }
        .color-card .form-label { font-size: .82rem; margin-bottom: .25rem; }
        .color-card .form-text { font-size: .75rem; }
        @media (max-width: 1200px) { .color-cards-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 800px) { .color-cards-grid { grid-template-columns: 1fr; } }

        /* Startseite-Tab: 2-Spalten */
        .homepage-cards-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .homepage-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; }
        .homepage-card h4 { margin: 0 0 1rem 0; font-size: .95rem; font-weight: 700; color: #1e293b; padding-bottom: .75rem; border-bottom: 1px solid #f1f5f9; }
        .homepage-card .form-group { margin-bottom: 1rem; }
        .homepage-card .form-group:last-child { margin-bottom: 0; }
        .homepage-card .form-label { font-size: .85rem; margin-bottom: .25rem; }
        .homepage-card .form-text { font-size: .78rem; }
        @media (max-width: 900px) { .homepage-cards-grid { grid-template-columns: 1fr; } }

        @media (max-width: 960px) {
            .customizer-layout { flex-direction: column; }
            .customizer-nav { width: 100%; display: flex; flex-wrap: wrap; gap: 0; }
            .customizer-nav a { border-left: none; border-bottom: 3px solid transparent; padding: .75rem 1rem; font-size: .8rem; }
            .customizer-nav a.active { border-bottom-color: var(--admin-primary, #3b82f6); }
        }
    </style>
</head>
<body class="admin-body">

    <?php renderAdminSidebar('theme-customizer'); ?>

    <div class="admin-content">

        <div class="admin-page-header">
            <div>
                <h2>🎨 Theme Customizer – PTC</h2>
                <p>Passe das Aussehen des PTC Corporate-Themes an.</p>
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

                <!-- Tab-Navigation -->
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
                            '🎨 Markenfarben' => ['primary_color', 'primary_hover', 'primary_light', 'accent_color', 'accent_hover', 'accent_light', 'secondary_color'],
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
                        // ── Startseite: 2-Spalten-Karten-Layout ──────────────────
                        $homepageGroups = [
                            '🎬 Hero-Sektion'            => ['show_hero', 'hero_badge', 'hero_title', 'hero_text', 'hero_bg_image'],
                            '🔗 Hero-Buttons'            => ['hero_cta_primary_label', 'hero_cta_primary_url', 'hero_cta_secondary_label', 'hero_cta_secondary_url'],
                            '🛠️ Dienstleistungen'       => ['show_services', 'services_title', 'services_subtitle', 'services_columns'],
                            '📅 Termine'                 => ['show_events', 'events_title', 'events_subtitle'],
                            '❓ FAQ'                      => ['show_faq', 'faq_title', 'faq_subtitle'],
                            '📢 Call-to-Action'           => ['show_cta', 'cta_title', 'cta_text', 'cta_button_label', 'cta_button_url', 'cta_secondary_label', 'cta_secondary_url'],
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

                                    <?php elseif ($field['type'] === 'textarea'): ?>
                                        <textarea id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                                  class="form-control" rows="3"
                                        ><?php echo htmlspecialchars((string)$val); ?></textarea>

                                    <?php elseif ($field['type'] === 'image_upload'): ?>
                                        <?php $previewUrl = $val ? htmlspecialchars((string)$val) : ''; ?>
                                        <div style="display:flex;flex-direction:column;gap:10px;">
                                            <div id="hero-bg-preview-wrap" style="background:#f8fafc;border:1px dashed #cbd5e1;border-radius:6px;padding:12px;display:flex;align-items:center;gap:12px;min-height:60px;">
                                                <?php if ($previewUrl): ?>
                                                    <img id="hero-bg-preview-img" src="<?php echo $previewUrl; ?>" alt="Hero BG" style="max-height:80px;max-width:200px;border-radius:4px;">
                                                <?php else: ?>
                                                    <span id="hero-bg-preview-img" style="color:#94a3b8;font-size:.85rem;">🖼️ Kein Bild ausgewählt</span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <label style="cursor:pointer;display:inline-flex;align-items:center;gap:6px;padding:.45rem .9rem;background:#3b82f6;color:#fff;border-radius:5px;font-size:.85rem;font-weight:600;">
                                                    📁 Bild hochladen
                                                    <input type="file" name="hero_bg_upload_file" accept="image/*"
                                                           style="display:none;" onchange="previewImageUpload(this, 'hero-bg-preview')">
                                                </label>
                                                <span style="color:#64748b;font-size:.8rem;">oder URL:</span>
                                            </div>
                                            <input type="text" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                                   value="<?php echo $previewUrl; ?>" class="form-control"
                                                   placeholder="https://...">
                                        </div>

                                    <?php elseif ($field['type'] === 'number'): ?>
                                        <input type="number" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                               value="<?php echo htmlspecialchars((string)$val); ?>"
                                               class="form-control" style="width:120px;">

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

                    <?php elseif ($activeTab === 'header' || $activeTab === 'footer' || $activeTab === 'buttons'):
                        // ── Header / Footer / Buttons: 2-Spalten-Karten-Layout ──
                        $tabCardGroups = [
                            'header' => [
                                '🖼️ Logo'              => ['logo_url', 'logo_text', 'logo_max_height'],
                                '🎨 Header-Farben'      => ['header_bg_color', 'header_text_color', 'header_accent_color'],
                                '📐 Header-Layout'      => ['header_height', 'show_header_shadow'],
                                '🔗 Header-Aktionen'    => ['show_login_btn', 'show_register_btn', 'header_cta_text', 'header_cta_url'],
                            ],
                            'footer' => [
                                '🎨 Footer-Farben'      => ['footer_bg_color', 'footer_text_color', 'footer_link_color'],
                                '📝 Footer-Inhalte'     => ['footer_tagline', 'footer_address', 'footer_phone', 'footer_email', 'copyright_text'],
                                '🌐 Social Media'       => ['social_facebook', 'social_instagram', 'social_linkedin', 'social_xing', 'social_youtube'],
                            ],
                            'buttons' => [
                                '📐 Button-Form'        => ['button_border_radius', 'button_padding_x', 'button_padding_y'],
                                '🔤 Button-Text'        => ['button_font_weight', 'button_transform'],
                            ],
                        ];
                        $cardGroups = $tabCardGroups[$activeTab] ?? [];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo htmlspecialchars($currentSection['title']); ?></h3>
                        <div class="homepage-cards-grid">
                            <?php foreach ($cardGroups as $groupTitle => $groupKeys): ?>
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

                                    <?php if ($field['type'] === 'image_upload'): ?>
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

                                    <?php elseif ($field['type'] === 'checkbox'): ?>
                                        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                                            <input type="checkbox" id="<?php echo $inputId; ?>"
                                                   name="<?php echo $inputName; ?>" value="1"
                                                   <?php echo $val ? 'checked' : ''; ?>>
                                            <label for="<?php echo $inputId; ?>" style="cursor:pointer;">Aktivieren</label>
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
                                               class="form-control" style="width:120px;">

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
                    <!-- Standard-Rendering (Typografie, Layout, Erweitert) -->
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

                            <?php elseif ($field['type'] === 'color'): ?>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <input type="color" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                           value="<?php echo htmlspecialchars((string)$val); ?>"
                                           style="height:38px;padding:2px;width:60px;border:1px solid #ddd;border-radius:4px;">
                                    <input type="text" value="<?php echo htmlspecialchars((string)$val); ?>"
                                           class="form-control" style="width:120px;"
                                           onchange="document.getElementById('<?php echo $inputId; ?>').value = this.value; updateLivePreview();">
                                </div>

                            <?php elseif ($field['type'] === 'number'): ?>
                                <input type="number" id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>"
                                       value="<?php echo htmlspecialchars((string)$val); ?>"
                                       class="form-control" style="width:120px;">

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
                    <?php endif; ?>

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

    <!-- Reset-Formular (außerhalb des Haupt-Forms) -->
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
                <p>Alle Einstellungen dieses Tabs werden auf die <strong>Standard-Designwerte</strong> des PTC-Themes zurückgesetzt.</p>
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

        // PTC CSS-Variable-Mapping
        var cssVarMap = {
            'colors_primary_color':     '--ptc-navy',
            'colors_primary_hover':     '--ptc-navy-dark',
            'colors_primary_light':     '--ptc-navy-light',
            'colors_accent_color':      '--ptc-gold',
            'colors_accent_hover':      '--ptc-gold-dark',
            'colors_accent_light':      '--ptc-gold-light',
            'colors_secondary_color':   '--ptc-slate',
            'colors_text_color':        '--ptc-text',
            'colors_heading_color':     '--ptc-heading',
            'colors_text_light':        '--ptc-white',
            'colors_muted_color':       '--ptc-muted',
            'colors_bg_color':          '--ptc-bg',
            'colors_bg_secondary':      '--ptc-bg-alt',
            'colors_link_color':        '--ptc-link',
            'colors_link_hover_color':  '--ptc-link-hover',
            'colors_border_color':      '--ptc-border',
            'colors_success_color':     '--ptc-success',
            'colors_error_color':       '--ptc-error',
            'header_header_bg_color':   '--ptc-navy',
            'header_header_text_color': '--ptc-white',
            'header_header_accent_color': '--ptc-gold',
            'footer_footer_bg_color':   '--ptc-footer-bg',
            'footer_footer_text_color': '--ptc-footer-text',
            'footer_footer_link_color': '--ptc-footer-link',
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

        // Farb-Picker synchronisieren
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

        // Farbpaletten-Vorschau
        if (document.querySelector('input[name="colors_primary_color"]')) {
            var paletteFields = [
                { name: 'colors_primary_color',   label: 'Navy' },
                { name: 'colors_accent_color',    label: 'Gold' },
                { name: 'colors_secondary_color', label: 'Slate' },
                { name: 'colors_text_color',      label: 'Text' },
                { name: 'colors_bg_color',        label: 'Hintergrund' },
                { name: 'colors_bg_secondary',    label: 'Surface' },
                { name: 'colors_border_color',    label: 'Rahmen' },
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
                title.textContent = 'PTC Farb-Vorschau';
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

    // Globale Funktion für Text-Input-onchange
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

    // ── Generischer Bild-Upload-Preview ──────────────────────────────────────
    function previewImageUpload(input, prefix) {
        if (!input.files || !input.files[0]) { return; }
        var reader = new FileReader();
        reader.onload = function (e) {
            var wrap = document.getElementById(prefix + '-wrap');
            if (wrap) {
                wrap.innerHTML = '<img id="' + prefix + '-img" src="' + e.target.result + '" style="max-height:80px;max-width:200px;border-radius:4px;">';
            }
        };
        reader.readAsDataURL(input.files[0]);
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
