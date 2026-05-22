<?php
/**
 * PTC Theme – Customizer (Admin)
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

$esc = static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');

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
                'description' => 'Hauptfarbe – Primärfarbe – Vertrauen und Seriosität.',
                'type'        => 'color',
                'default'     => '#1a4d5c',
            ],
            'primary_hover' => [
                'label'       => 'Primärfarbe Hover',
                'description' => 'Dunklerer Ton für Hover-Zustände auf Navy-Elementen.',
                'type'        => 'color',
                'default'     => '#123a47',
            ],
            'primary_light' => [
                'label'       => 'Primärfarbe Hell',
                'description' => 'Aufgehellter Ton für Hintergründe und Badges.',
                'type'        => 'color',
                'default'     => '#E8F0FE',
            ],
            'accent_color' => [
                'label'       => 'Akzentfarbe (Gold)',
                'description' => 'Akzentfarbe – Entwicklung und CTAs – CTAs, Highlights.',
                'type'        => 'color',
                'default'     => '#2d7a5f',
            ],
            'accent_hover' => [
                'label'       => 'Akzentfarbe Hover',
                'description' => 'Dunkleres Gold für Hover-Zustände.',
                'type'        => 'color',
                'default'     => '#236349',
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
                'default'     => '#1a4d5c',
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
                'default'     => '#1a4d5c',
            ],
            'link_hover_color' => [
                'label'       => 'Link Hover-Farbe',
                'description' => 'Linkfarbe beim Hover-Zustand.',
                'type'        => 'color',
                'default'     => '#2d7a5f',
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

            // ── CTA-Sektion ──
            'cta_bg_color' => [
                'label'       => 'CTA-Hintergrund (Verlauf Start)',
                'description' => 'Startfarbe des CTA-Sektions-Hintergrund-Verlaufs.',
                'type'        => 'color',
                'default'     => '#1a4d5c',
            ],
            'cta_bg_to' => [
                'label'       => 'CTA-Hintergrund (Verlauf Ende)',
                'description' => 'Endfarbe des CTA-Sektions-Verlaufs.',
                'type'        => 'color',
                'default'     => '#123a47',
            ],
            'cta_text_color' => [
                'label'       => 'CTA-Textfarbe',
                'description' => 'Textfarbe innerhalb der CTA-Sektion.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],

            // ── Seitenränder ──
            'side_margin_color' => [
                'label'       => 'Seitenrand-Hintergrund',
                'description' => 'Hintergrundfarbe der Seitenränder (links/rechts). Dezent anders als Content.',
                'type'        => 'color',
                'default'     => '#EDF1F5',
            ],
            'side_accent_color' => [
                'label'       => 'Seitenrand-Akzent',
                'description' => 'Optionale farbige Akzentlinie am linken und rechten Seitenrand. Leer/transparent = deaktiviert.',
                'type'        => 'color',
                'default'     => '#1a4d5c',
            ],
            'side_accent_width' => [
                'label'       => 'Seitenrand-Akzent Breite (px)',
                'description' => 'Breite der Seitenrand-Akzentlinie (0 = keine Linie).',
                'type'        => 'number',
                'default'     => 0,
            ],

            // ── Sektions-Hintergrundfarben ──
            'hero_bg_color' => [
                'label'       => 'Hero-Hintergrund (Start)',
                'description' => 'Startfarbe des Hero-Verlaufs auf der Startseite.',
                'type'        => 'color',
                'default'     => '#F8F9FA',
            ],
            'hero_bg_to' => [
                'label'       => 'Hero-Hintergrund (Ende)',
                'description' => 'Endfarbe des Hero-Verlaufs.',
                'type'        => 'color',
                'default'     => '#F1F5F9',
            ],
            'services_bg_color' => [
                'label'       => 'Dienstleistungen Hintergrund',
                'description' => 'Hintergrundfarbe der Dienstleistungen-Sektion.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'services_card_bg' => [
                'label'       => 'Dienstleistungen Karte',
                'description' => 'Hintergrundfarbe der Service-Karten.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'events_bg_color' => [
                'label'       => 'Termine Hintergrund',
                'description' => 'Hintergrundfarbe der Termine-Sektion.',
                'type'        => 'color',
                'default'     => '#F0F4F8',
            ],
            'events_card_bg' => [
                'label'       => 'Termine Karte',
                'description' => 'Hintergrundfarbe der Event-Karten.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'faq_bg_color' => [
                'label'       => 'FAQ Hintergrund',
                'description' => 'Hintergrundfarbe der FAQ-Sektion.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'faq_item_bg' => [
                'label'       => 'FAQ Akkordeon',
                'description' => 'Hintergrundfarbe der einzelnen FAQ-Einträge.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'network_bar_bg' => [
                'label'       => 'Network-Bar Hintergrund',
                'description' => 'Hintergrundfarbe der Network-Bar am Seitenende.',
                'type'        => 'color',
                'default'     => '#000D1A',
            ],
            'network_bar_text' => [
                'label'       => 'Network-Bar Text',
                'description' => 'Textfarbe der Network-Bar.',
                'type'        => 'color',
                'default'     => '#FFFFFF59',
            ],

            // ── Blog-Farben ─────────────────────────────────────
            'blog_hero_bg' => [
                'label'       => 'Blog Hero Hintergrund',
                'description' => 'Hintergrundfarbe des Blog-Hero-Bereichs.',
                'type'        => 'color',
                'default'     => '#1a4d5c',
            ],
            'blog_hero_text' => [
                'label'       => 'Blog Hero Text',
                'description' => 'Textfarbe im Blog-Hero-Bereich.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'blog_content_bg' => [
                'label'       => 'Blog Inhalt Hintergrund',
                'description' => 'Hintergrundfarbe des Blog-Inhaltsbereichs.',
                'type'        => 'color',
                'default'     => '#F8F9FA',
            ],
            'blog_card_bg' => [
                'label'       => 'Blog Karte Hintergrund',
                'description' => 'Hintergrundfarbe der Blog-Karten.',
                'type'        => 'color',
                'default'     => '#FFFFFF',
            ],
            'blog_card_title' => [
                'label'       => 'Blog Karte Titel',
                'description' => 'Titelfarbe auf Blog-Karten.',
                'type'        => 'color',
                'default'     => '#1a4d5c',
            ],
            'blog_category_bg' => [
                'label'       => 'Kategorie-Badge Hintergrund',
                'description' => 'Hintergrundfarbe der Kategorie-Badges.',
                'type'        => 'color',
                'default'     => '#FDF5E6',
            ],
            'blog_category_text' => [
                'label'       => 'Kategorie-Badge Text',
                'description' => 'Textfarbe der Kategorie-Badges.',
                'type'        => 'color',
                'default'     => '#236349',
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
                'default'     => '',
            ],
            'header_bg_color' => [
                'label'       => 'Header-Hintergrundfarbe',
                'description' => 'Hintergrundfarbe der Navigationsleiste (Navy).',
                'type'        => 'color',
                'default'     => '#1a4d5c',
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
                'default'     => '#2d7a5f',
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
            'nav_font_size' => [
                'label'       => 'Menü-Schriftgröße (px)',
                'description' => 'Schriftgröße der Hauptmenüpunkte (Standard: 14).',
                'type'        => 'number',
                'default'     => '14',
                'min'         => 10,
                'max'         => 30,
                'step'        => 2,
            ],
            'nav_sub_font_size' => [
                'label'       => 'Untermenü-Schriftgröße (px)',
                'description' => 'Schriftgröße der Untermenüpunkte (Standard: 13).',
                'type'        => 'number',
                'default'     => '13',
                'min'         => 10,
                'max'         => 26,
                'step'        => 2,
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
                'default'     => false,
            ],
            'register_btn_icon_only' => [
                'label'       => 'Registrieren-Button: Nur Icon',
                'description' => 'Zeigt nur das ✏️-Icon ohne Text – macht den Button schmaler.',
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
            'header_animation' => [
                'label'       => 'Header Hintergrund-Animation',
                'description' => 'Dezente Animation im Header-Hintergrund (optional).',
                'type'        => 'select',
                'options'     => [
                    'none'      => 'Keine Animation',
                    'gradient'  => 'Sanfter Farbverlauf',
                    'particles' => 'Schwebende Partikel',
                    'pulse'     => 'Goldener Lichtstreifen',
                    'wave'      => 'Wellenlinie (unterer Rand)',
                ],
                'default'     => 'none',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // FOOTER
    // ─────────────────────────────────────────────────────────────
    'footer' => [
        'title' => '🔻 Footer',
        'sections' => [
            'footer_width' => [
                'label'       => 'Footer-Breite',
                'description' => 'Volle Breite (wie Header) oder gleiche Breite wie Content-Bereich.',
                'type'        => 'select',
                'options'     => [
                    'full'    => 'Volle Breite',
                    'content' => 'Content-Breite',
                ],
                'default'     => 'full',
            ],
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
                'default'     => ' – Ihr Partner für Bildung, Karriere und Zukunft.',
            ],
            'footer_address' => [
                'label'       => 'Adresse / Kontakt',
                'description' => 'Adressdaten im Kontakt-Widget des Footers.',
                'type'        => 'textarea',
                'default'     => "\nMusterstraße 1\n12345 Musterstadt",
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

            // ── Network Bar ──
            'show_network_bar' => [
                'label'       => 'Network Bar anzeigen',
                'description' => 'Zeigt die Netzwerk-Leiste unter dem Footer.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'network_bar_name' => [
                'label'       => 'Network Bar – Name',
                'description' => 'Name/Label links in der Network Bar.',
                'type'        => 'text',
                'default'     => '',
            ],
            'network_bar_link1_label' => [
                'label'       => 'Link 1 – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'network_bar_link1_url' => [
                'label'       => 'Link 1 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'network_bar_link2_label' => [
                'label'       => 'Link 2 – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => '365CMS',
            ],
            'network_bar_link2_url' => [
                'label'       => 'Link 2 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://365cms.de',
            ],
            'network_bar_link3_label' => [
                'label'       => 'Link 3 – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => '365 Network',
            ],
            'network_bar_link3_url' => [
                'label'       => 'Link 3 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://365network.de',
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
    // DIENSTLEISTUNGEN
    // ─────────────────────────────────────────────────────────────
    'services' => [
        'title' => '🛠️ Dienstleistungen',
        'sections' => [
            // ── Allgemein ──
            'show_services' => [
                'label'       => 'Dienstleistungen-Sektion anzeigen',
                'description' => 'Zeigt den Dienstleistungs-Bereich auf der Startseite.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'services_tag' => [
                'label'       => 'Label / Tag-Text',
                'description' => 'Kleiner Text über der Überschrift (z. B. „Unsere Leistungen").',
                'type'        => 'text',
                'default'     => 'Unsere Leistungen',
            ],
            'services_title' => [
                'label'       => 'Sektions-Überschrift',
                'description' => 'Haupttitel der Dienstleistungen-Sektion.',
                'type'        => 'text',
                'default'     => 'Unsere Dienstleistungen',
            ],
            'services_subtitle' => [
                'label'       => 'Sektions-Untertitel',
                'description' => 'Beschreibungstext unterhalb der Überschrift.',
                'type'        => 'textarea',
                'default'     => 'Von Aktivierung über Logistik bis hin zur Personalvermittlung – wir bieten maßgeschneiderte Lösungen.',
            ],
            'services_columns' => [
                'label'       => 'Spaltenanzahl',
                'description' => 'Anzahl der Service-Cards pro Zeile.',
                'type'        => 'select',
                'options'     => [
                    '2' => '2 Spalten',
                    '3' => '3 Spalten (Standard)',
                    '4' => '4 Spalten',
                ],
                'default'     => '3',
            ],
            'services_bg_style' => [
                'label'       => 'Hintergrund-Stil',
                'description' => 'Hintergrundfarbe der Dienstleistungen-Sektion.',
                'type'        => 'select',
                'options'     => [
                    'default' => 'Transparent (Standard)',
                    'alt'     => 'Weiß (alternierend)',
                    'navy'    => 'Navy (dunkel)',
                ],
                'default'     => 'default',
            ],
            'services_card_style' => [
                'label'       => 'Card-Design',
                'description' => 'Visuelles Design der einzelnen Dienstleistungs-Karten.',
                'type'        => 'select',
                'options'     => [
                    'bordered' => 'Rahmen + Schatten (Standard)',
                    'shadow'   => 'Nur Schatten',
                    'flat'     => 'Flach / ohne Rahmen',
                    'filled'   => 'Gefüllt (leicht getönt)',
                ],
                'default'     => 'bordered',
            ],
            'services_icon_style' => [
                'label'       => 'Icon-Stil',
                'description' => 'Darstellung der Emoji-Icons auf den Cards.',
                'type'        => 'select',
                'options'     => [
                    'circle'  => 'Kreisförmiger Hintergrund (Standard)',
                    'square'  => 'Quadratischer Hintergrund',
                    'plain'   => 'Nur Emoji (ohne Hintergrund)',
                    'large'   => 'Groß (ohne Hintergrund)',
                ],
                'default'     => 'circle',
            ],
            'services_show_hover' => [
                'label'       => 'Hover-Effekt',
                'description' => 'Goldlinie und Anheben beim Darüberfahren.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'services_show_icons' => [
                'label'       => 'Icons anzeigen',
                'description' => 'Zeigt die Emoji-Icons auf den Service-Karten. Deaktiviert = nur Titel und Text.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'services_max_items' => [
                'label'       => 'Maximale Anzahl sichtbarer Karten',
                'description' => 'Wie viele Service-Karten maximal angezeigt werden (1–12).',
                'type'        => 'number',
                'default'     => 6,
            ],
            'services_show_cta' => [
                'label'       => 'CTA-Button unter Karten anzeigen',
                'description' => 'Zeigt einen zusätzlichen Button unter dem Service-Grid.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'services_cta_label' => [
                'label'       => 'CTA-Button Text',
                'description' => 'Beschriftung des optionalen Buttons.',
                'type'        => 'text',
                'default'     => 'Alle Leistungen entdecken',
            ],
            'services_cta_url' => [
                'label'       => 'CTA-Button URL',
                'description' => 'Ziel-URL des Buttons.',
                'type'        => 'text',
                'default'     => '/leistungen',
            ],

            // ── Service 1 ──
            'service_1_icon' => [
                'label'       => '🔶 Service 1 – Icon (Emoji)',
                'description' => 'Emoji-Icon für die erste Karte.',
                'type'        => 'text',
                'default'     => '🎯',
            ],
            'service_1_image' => [
                'label'       => 'Service 1 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_1_title' => [
                'label'       => 'Service 1 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Aktivierung und Vermittlung',
            ],
            'service_1_text' => [
                'label'       => 'Service 1 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Förderung für Ihren Erfolg – individuelle Aktivierungsmaßnahmen und passgenaue Vermittlung in den Arbeitsmarkt.',
            ],
            'service_1_url' => [
                'label'       => 'Service 1 – Link (optional)',
                'description' => 'Ziel-URL der „Mehr erfahren"-Verknüpfung. Leer = kein Link.',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 2 ──
            'service_2_icon' => [
                'label'       => '🔶 Service 2 – Icon (Emoji)',
                'description' => '',
                'type'        => 'text',
                'default'     => '🏗️',
            ],
            'service_2_image' => [
                'label'       => 'Service 2 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_2_title' => [
                'label'       => 'Service 2 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Weiterbildung & Qualifizierung',
            ],
            'service_2_text' => [
                'label'       => 'Service 2 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Fachpraxis auf höchstem Niveau – praxisnahe Qualifizierung in Logistik, Lagerwirtschaft und Gabelstaplerführung.',
            ],
            'service_2_url' => [
                'label'       => 'Service 2 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 3 ──
            'service_3_icon' => [
                'label'       => '🔶 Service 3 – Icon (Emoji)',
                'description' => '',
                'type'        => 'text',
                'default'     => '🎓',
            ],
            'service_3_image' => [
                'label'       => 'Service 3 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_3_title' => [
                'label'       => 'Service 3 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Akademie und Bildung',
            ],
            'service_3_text' => [
                'label'       => 'Service 3 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Individuelle Coachings, Führungskräfte-Workshops und zertifizierte Weiterbildungsprogramme für Ihre Karriere.',
            ],
            'service_3_url' => [
                'label'       => 'Service 3 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 4 ──
            'service_4_icon' => [
                'label'       => '🔶 Service 4 – Icon (Emoji)',
                'description' => '',
                'type'        => 'text',
                'default'     => '🤝',
            ],
            'service_4_image' => [
                'label'       => 'Service 4 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_4_title' => [
                'label'       => 'Service 4 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Personalvermittlung',
            ],
            'service_4_text' => [
                'label'       => 'Service 4 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Den perfekten Job finden – wir bringen qualifizierte Fachkräfte und Unternehmen zusammen.',
            ],
            'service_4_url' => [
                'label'       => 'Service 4 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 5 ──
            'service_5_icon' => [
                'label'       => '🔶 Service 5 – Icon (Emoji)',
                'description' => '',
                'type'        => 'text',
                'default'     => '🔄',
            ],
            'service_5_image' => [
                'label'       => 'Service 5 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_5_title' => [
                'label'       => 'Service 5 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Arbeitnehmerüberlassung',
            ],
            'service_5_text' => [
                'label'       => 'Service 5 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Flexibilität für Ihr Unternehmen – temporäre Fachkräfte genau dann, wenn Sie sie brauchen.',
            ],
            'service_5_url' => [
                'label'       => 'Service 5 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 6 ──
            'service_6_icon' => [
                'label'       => '🔶 Service 6 – Icon (Emoji)',
                'description' => '',
                'type'        => 'text',
                'default'     => '🚀',
            ],
            'service_6_image' => [
                'label'       => 'Service 6 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_6_title' => [
                'label'       => 'Service 6 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Ausbildung',
            ],
            'service_6_text' => [
                'label'       => 'Service 6 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Start in Ihre berufliche Zukunft – Ausbildungsplätze und Einstiegsprogramme für junge Talente.',
            ],
            'service_6_url' => [
                'label'       => 'Service 6 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 7 ──
            'service_7_icon' => [
                'label'       => '🔶 Service 7 – Icon (Emoji)',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_7_image' => [
                'label'       => 'Service 7 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_7_title' => [
                'label'       => 'Service 7 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_7_text' => [
                'label'       => 'Service 7 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'service_7_url' => [
                'label'       => 'Service 7 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 8 ──
            'service_8_icon' => [
                'label'       => '🔶 Service 8 – Icon (Emoji)',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_8_image' => [
                'label'       => 'Service 8 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_8_title' => [
                'label'       => 'Service 8 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_8_text' => [
                'label'       => 'Service 8 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'service_8_url' => [
                'label'       => 'Service 8 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 9 ──
            'service_9_icon' => [
                'label'       => '🔶 Service 9 – Icon (Emoji)',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_9_image' => [
                'label'       => 'Service 9 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_9_title' => [
                'label'       => 'Service 9 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_9_text' => [
                'label'       => 'Service 9 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'service_9_url' => [
                'label'       => 'Service 9 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 10 ──
            'service_10_icon' => [
                'label'       => '🔶 Service 10 – Icon (Emoji)',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_10_image' => [
                'label'       => 'Service 10 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_10_title' => [
                'label'       => 'Service 10 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_10_text' => [
                'label'       => 'Service 10 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'service_10_url' => [
                'label'       => 'Service 10 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 11 ──
            'service_11_icon' => [
                'label'       => '🔶 Service 11 – Icon (Emoji)',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_11_image' => [
                'label'       => 'Service 11 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_11_title' => [
                'label'       => 'Service 11 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_11_text' => [
                'label'       => 'Service 11 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'service_11_url' => [
                'label'       => 'Service 11 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Service 12 ──
            'service_12_icon' => [
                'label'       => '🔶 Service 12 – Icon (Emoji)',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_12_image' => [
                'label'       => 'Service 12 – Bild (URL)',
                'description' => 'Bild-URL. Wird anstelle des Icons links neben dem Titel angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_12_title' => [
                'label'       => 'Service 12 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'service_12_text' => [
                'label'       => 'Service 12 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'service_12_url' => [
                'label'       => 'Service 12 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // TERMINE / EVENTS
    // ─────────────────────────────────────────────────────────────
    'events' => [
        'title' => '📅 Termine',
        'sections' => [
            // ── Allgemein ──
            'show_events' => [
                'label'       => 'Termine-Sektion anzeigen',
                'description' => 'Zeigt den „Aktuelle Termine"-Bereich auf der Startseite.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'events_tag' => [
                'label'       => 'Label / Tag-Text',
                'description' => 'Kleiner Text über der Überschrift.',
                'type'        => 'text',
                'default'     => 'Veranstaltungen',
            ],
            'events_title' => [
                'label'       => 'Sektions-Überschrift',
                'description' => 'Haupttitel der Termine-Sektion.',
                'type'        => 'text',
                'default'     => 'Aktuelle Termine & Angebote',
            ],
            'events_subtitle' => [
                'label'       => 'Sektions-Untertitel',
                'description' => 'Beschreibungstext unterhalb der Überschrift.',
                'type'        => 'textarea',
                'default'     => 'Entdecken Sie unsere aktuellen Kursangebote, Workshops und Veranstaltungen.',
            ],
            'events_source' => [
                'label'       => 'Datenquelle',
                'description' => 'Woher Termine geladen werden.',
                'type'        => 'select',
                'options'     => [
                    'auto'   => 'Automatisch (Plugin → Fallback manuell)',
                    'plugin' => 'Nur cms-events Plugin',
                    'manual' => 'Nur manuelle Einträge',
                ],
                'default'     => 'auto',
            ],
            'events_max_items' => [
                'label'       => 'Maximale Anzahl',
                'description' => 'Wie viele Termine maximal angezeigt werden.',
                'type'        => 'number',
                'default'     => 6,
            ],
            'events_columns' => [
                'label'       => 'Spaltenanzahl',
                'description' => 'Anzahl der Event-Karten pro Zeile.',
                'type'        => 'select',
                'options'     => [
                    '2' => '2 Spalten',
                    '3' => '3 Spalten (Standard)',
                    '4' => '4 Spalten',
                ],
                'default'     => '3',
            ],
            'events_bg_style' => [
                'label'       => 'Hintergrund-Stil',
                'description' => 'Hintergrundfarbe der Termine-Sektion.',
                'type'        => 'select',
                'options'     => [
                    'alt'     => 'Weiß / alternierend (Standard)',
                    'default' => 'Transparent',
                    'navy'    => 'Navy (dunkel)',
                ],
                'default'     => 'alt',
            ],
            'events_card_style' => [
                'label'       => 'Card-Design',
                'description' => 'Visuelles Design der Termin-Karten.',
                'type'        => 'select',
                'options'     => [
                    'bordered' => 'Rahmen (Standard)',
                    'shadow'   => 'Schatten',
                    'flat'     => 'Flach',
                ],
                'default'     => 'bordered',
            ],
            'events_show_date_badge' => [
                'label'       => 'Datums-Badge anzeigen',
                'description' => 'Zeigt das Datum mit Kalender-Emoji.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'events_link_text' => [
                'label'       => 'Link-Text',
                'description' => 'Text der „Mehr erfahren"-Links auf den Karten.',
                'type'        => 'text',
                'default'     => 'Mehr erfahren →',
            ],
            'events_show_empty' => [
                'label'       => 'Platzhalter bei 0 Terminen',
                'description' => 'Zeigt eine Info-Karte wenn momentan keine Termine vorhanden sind.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'events_empty_text' => [
                'label'       => 'Platzhaltertext',
                'description' => 'Text der erscheint, wenn keine Termine vorhanden sind.',
                'type'        => 'text',
                'default'     => 'Neue Termine werden in Kürze veröffentlicht',
            ],
            'events_empty_hint' => [
                'label'       => 'Platzhalter-Hinweis',
                'description' => 'Zweite Zeile unter dem Platzhaltertext.',
                'type'        => 'text',
                'default'     => 'Schauen Sie bald wieder vorbei oder kontaktieren Sie uns direkt.',
            ],
            'events_show_cta' => [
                'label'       => 'CTA-Button anzeigen',
                'description' => 'Zeigt einen Button unter den Termin-Karten.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'events_cta_label' => [
                'label'       => 'CTA-Button Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Alle Termine ansehen',
            ],
            'events_cta_url' => [
                'label'       => 'CTA-Button URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '/termine',
            ],

            // ── Manuelle Termine ──
            'event_1_title' => [
                'label'       => '📌 Termin 1 – Titel',
                'description' => 'Leer = Karte nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_1_date' => [
                'label'       => 'Termin 1 – Datum',
                'description' => 'z. B. "15. Apr." oder "2026-04-15".',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_1_text' => [
                'label'       => 'Termin 1 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'event_1_url' => [
                'label'       => 'Termin 1 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            'event_2_title' => [
                'label'       => '📌 Termin 2 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_2_date' => [
                'label'       => 'Termin 2 – Datum',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_2_text' => [
                'label'       => 'Termin 2 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'event_2_url' => [
                'label'       => 'Termin 2 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            'event_3_title' => [
                'label'       => '📌 Termin 3 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_3_date' => [
                'label'       => 'Termin 3 – Datum',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_3_text' => [
                'label'       => 'Termin 3 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'event_3_url' => [
                'label'       => 'Termin 3 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            'event_4_title' => [
                'label'       => '📌 Termin 4 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_4_date' => [
                'label'       => 'Termin 4 – Datum',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_4_text' => [
                'label'       => 'Termin 4 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'event_4_url' => [
                'label'       => 'Termin 4 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            'event_5_title' => [
                'label'       => '📌 Termin 5 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_5_date' => [
                'label'       => 'Termin 5 – Datum',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_5_text' => [
                'label'       => 'Termin 5 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'event_5_url' => [
                'label'       => 'Termin 5 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            'event_6_title' => [
                'label'       => '📌 Termin 6 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_6_date' => [
                'label'       => 'Termin 6 – Datum',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'event_6_text' => [
                'label'       => 'Termin 6 – Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'event_6_url' => [
                'label'       => 'Termin 6 – Link (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],

            // ── Microsoft Booking Integration ──
            'events_booking_url' => [
                'label'       => '📅 MS Booking – Embed-URL',
                'description' => 'Vollständige Microsoft Booking URL für das Inline-Buchungsformular. Leer = keine Anzeige.',
                'type'        => 'text',
                'default'     => '',
            ],
            'events_booking_title' => [
                'label'       => 'MS Booking – Überschrift',
                'description' => 'Text über dem Buchungsformular.',
                'type'        => 'text',
                'default'     => 'Online-Termin buchen',
            ],
            'events_booking_height' => [
                'label'       => 'MS Booking – Höhe (px)',
                'description' => 'Höhe des Booking-Iframes in Pixeln.',
                'type'        => 'number',
                'default'     => 600,
            ],
        ],
    ],

    // ─────────────────────────────────────────────────────────────
    // FAQ
    // ─────────────────────────────────────────────────────────────
    'faq' => [
        'title' => '❓ FAQ',
        'sections' => [
            // ── Allgemein ──
            'show_faq' => [
                'label'       => 'FAQ-Sektion anzeigen',
                'description' => 'Zeigt den FAQ-Bereich auf der Startseite.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'faq_tag' => [
                'label'       => 'Label / Tag-Text',
                'description' => 'Kleiner Text über der Überschrift.',
                'type'        => 'text',
                'default'     => 'Wissenswertes',
            ],
            'faq_title' => [
                'label'       => 'Sektions-Überschrift',
                'description' => 'Haupttitel des FAQ-Bereichs.',
                'type'        => 'text',
                'default'     => 'Häufig gestellte Fragen',
            ],
            'faq_subtitle' => [
                'label'       => 'Sektions-Untertitel',
                'description' => 'Beschreibungstext unterhalb der Überschrift.',
                'type'        => 'textarea',
                'default'     => 'Hier finden Sie Antworten auf die wichtigsten Fragen zu unseren Dienstleistungen.',
            ],
            'faq_style' => [
                'label'       => 'Darstellungs-Stil',
                'description' => 'Wie die FAQ-Items dargestellt werden.',
                'type'        => 'select',
                'options'     => [
                    'accordion' => 'Akkordeon / aufklappbar (Standard)',
                    'open'      => 'Alle geöffnet',
                ],
                'default'     => 'accordion',
            ],
            'faq_max_width' => [
                'label'       => 'Maximale Breite (px)',
                'description' => 'Maximale Breite der FAQ-Liste (0 = volle Breite).',
                'type'        => 'number',
                'default'     => 720,
            ],
            'faq_bg_style' => [
                'label'       => 'Hintergrund-Stil',
                'description' => 'Hintergrundfarbe der FAQ-Sektion.',
                'type'        => 'select',
                'options'     => [
                    'default' => 'Transparent (Standard)',
                    'alt'     => 'Weiß (alternierend)',
                ],
                'default'     => 'default',
            ],
            'faq_show_cta' => [
                'label'       => 'CTA-Bereich unter FAQ anzeigen',
                'description' => 'Zeigt einen Hinweistext + Button unter den FAQ-Items.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'faq_cta_text' => [
                'label'       => 'CTA-Hinweistext',
                'description' => 'Text oberhalb des CTA-Buttons.',
                'type'        => 'text',
                'default'     => 'Ihre Frage war nicht dabei?',
            ],
            'faq_cta_label' => [
                'label'       => 'CTA-Button Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Kontaktieren Sie uns',
            ],
            'faq_cta_url' => [
                'label'       => 'CTA-Button URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '/#kontakt',
            ],

            // ── FAQ 1 ──
            'faq_1_question' => [
                'label'       => '💬 Frage 1',
                'description' => 'Leer = wird nicht angezeigt.',
                'type'        => 'text',
                'default'     => 'Was sind Ihre Personaldienstleistungen?',
            ],
            'faq_1_answer' => [
                'label'       => 'Antwort 1',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Wir bieten ein breites Spektrum an Personaldienstleistungen: Von der klassischen Personalvermittlung über Arbeitnehmerüberlassung bis hin zu individuellen Bildungs- und Qualifizierungsangeboten in unserer Akademie und Weiterbildung & Qualifizierung.',
            ],

            // ── FAQ 2 ──
            'faq_2_question' => [
                'label'       => '💬 Frage 2',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Was ist Arbeitnehmerüberlassung?',
            ],
            'faq_2_answer' => [
                'label'       => 'Antwort 2',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Bei der Arbeitnehmerüberlassung stellen wir Ihnen qualifizierte Mitarbeiter temporär zur Verfügung. Sie profitieren von Flexibilität, während die Fachkräfte bei uns angestellt bleiben. So können Sie schnell auf Personalbedarfe reagieren.',
            ],

            // ── FAQ 3 ──
            'faq_3_question' => [
                'label'       => '💬 Frage 3',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Welche Weiterbildungen bieten Sie an?',
            ],
            'faq_3_answer' => [
                'label'       => 'Antwort 3',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Unsere Akademie bietet Führungskräfte-Workshops, Gabelstaplerschulungen, individuelle Coachings und zertifizierte Weiterbildungsprogramme. Alle Angebote werden praxisnah durchgeführt und können auf Ihre Bedürfnisse angepasst werden.',
            ],

            // ── FAQ 4 ──
            'faq_4_question' => [
                'label'       => '💬 Frage 4',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Wie finde ich bei uns den passenden Job?',
            ],
            'faq_4_answer' => [
                'label'       => 'Antwort 4',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Kontaktieren Sie uns per Telefon, E-Mail oder über unser Kontaktformular. In einem persönlichen Gespräch analysieren wir Ihre Stärken und Wünsche und vermitteln Sie passgenau an Unternehmen in der Region.',
            ],

            // ── FAQ 5 ──
            'faq_5_question' => [
                'label'       => '💬 Frage 5',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Welche Vorteile hat Arbeitnehmerüberlassung für Unternehmen?',
            ],
            'faq_5_answer' => [
                'label'       => 'Antwort 5',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Unternehmen profitieren von maximaler Flexibilität, reduziertem Verwaltungsaufwand und schneller Verfügbarkeit von qualifiziertem Personal – ohne langfristige Bindung.',
            ],

            // ── FAQ 6 ──
            'faq_6_question' => [
                'label'       => '💬 Frage 6',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Gibt es finanzielle Fördermöglichkeiten?',
            ],
            'faq_6_answer' => [
                'label'       => 'Antwort 6',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Ja, für viele unserer Bildungsangebote und Aktivierungsmaßnahmen gibt es Förderungsmöglichkeiten über die Agentur für Arbeit oder das Jobcenter. Wir beraten Sie gerne zu Ihren individuellen Möglichkeiten.',
            ],

            // ── FAQ 7 ──
            'faq_7_question' => [
                'label'       => '💬 Frage 7',
                'description' => 'Leer = wird nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'faq_7_answer' => [
                'label'       => 'Antwort 7',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],

            // ── FAQ 8 ──
            'faq_8_question' => [
                'label'       => '💬 Frage 8',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'faq_8_answer' => [
                'label'       => 'Antwort 8',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
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
                'default'     => 'Willkommen bei <span class="highlight"></span> – Ihr Partner für Personaldienstleistungen.',
            ],
            'hero_text' => [
                'label'       => 'Hero-Beschreibungstext',
                'description' => 'Einleitungstext unter der Überschrift.',
                'type'        => 'textarea',
                'default'     => 'Wir verbinden Menschen mit Chancen: Personalvermittlung, Arbeitnehmerüberlassung, Akademie & Bildung und Weiterbildung & Qualifizierung – alles aus einer Hand.',
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
    // BLOG & SEITEN
    // ─────────────────────────────────────────────────────────────
    'blog' => [
        'title' => '📰 Blog & Seiten',
        'sections' => [

            // ── Blog-Übersicht ─────────────────────────────
            'blog_layout' => [
                'label'       => 'Blog-Layout',
                'description' => 'Darstellung der Beitragsübersicht.',
                'type'        => 'select',
                'options'     => [
                    'grid' => 'Kacheln (Grid)',
                    'list' => 'Liste',
                ],
                'default'     => 'grid',
            ],
            'blog_columns' => [
                'label'       => 'Spaltenanzahl (Grid)',
                'description' => 'Anzahl der Spalten im Grid-Layout.',
                'type'        => 'select',
                'options'     => [
                    '2' => '2 Spalten',
                    '3' => '3 Spalten',
                ],
                'default'     => '3',
            ],
            'blog_show_sidebar' => [
                'label'       => 'Sidebar anzeigen',
                'description' => 'Seitenleiste mit Kategorien, Tags und Suche.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_show_featured_image' => [
                'label'       => 'Beitragsbild anzeigen',
                'description' => 'Beitragsbild in der Übersicht anzeigen.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_show_excerpt' => [
                'label'       => 'Textauszug anzeigen',
                'description' => 'Kurzbeschreibung unter dem Titel anzeigen.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_show_date' => [
                'label'       => 'Datum anzeigen',
                'description' => 'Veröffentlichungsdatum in der Übersicht.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_show_author' => [
                'label'       => 'Autor anzeigen',
                'description' => 'Autorname unter den Beiträgen.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_show_category' => [
                'label'       => 'Kategorie-Badge anzeigen',
                'description' => 'Kategorie als farbiges Badge auf der Karte.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_hero_title' => [
                'label'       => 'Blog-Überschrift',
                'description' => 'Titel im Hero-Bereich der Blog-Seite.',
                'type'        => 'text',
                'default'     => 'Unser Blog',
            ],
            'blog_hero_subtitle' => [
                'label'       => 'Blog-Untertitel',
                'description' => 'Kurzer Text unter der Blog-Überschrift.',
                'type'        => 'text',
                'default'     => 'Aktuelle Beiträge, Einblicke und Neuigkeiten',
            ],

            // ── Einzelbeitrag (Single) ─────────────────────
            'blog_single_show_featured' => [
                'label'       => 'Beitragsbild auf Einzelseite',
                'description' => 'Großes Beitragsbild am Anfang des Artikels.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_single_show_author' => [
                'label'       => 'Autor auf Einzelseite',
                'description' => 'Autorname und -info im Artikel anzeigen.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_single_show_date' => [
                'label'       => 'Datum auf Einzelseite',
                'description' => 'Veröffentlichungsdatum im Artikel.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_single_show_tags' => [
                'label'       => 'Tags auf Einzelseite',
                'description' => 'Schlagwörter am Ende des Artikels.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_single_show_related' => [
                'label'       => 'Ähnliche Beiträge anzeigen',
                'description' => 'Verwandte Beiträge unter dem Artikel.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_single_show_reading_time' => [
                'label'       => 'Lesezeit anzeigen',
                'description' => 'Geschätzte Lesezeit im Artikelkopf.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'blog_single_max_width' => [
                'label'       => 'Maximale Artikelbreite (px)',
                'description' => 'Max. Breite des Artikelinhalts für bessere Lesbarkeit.',
                'type'        => 'number',
                'default'     => '820',
            ],

            // ── Seiten-Einstellungen ───────────────────────
            'page_show_title' => [
                'label'       => 'Seitentitel anzeigen',
                'description' => 'Seitentitel als Hero / Seitenüberschrift anzeigen.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'page_show_updated' => [
                'label'       => '„Zuletzt aktualisiert“ anzeigen',
                'description' => 'Datum der letzten Aktualisierung unter dem Seitentitel.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'page_show_featured_image' => [
                'label'       => 'Beitragsbild auf Seiten',
                'description' => 'Beitragsbild auf statischen Seiten anzeigen.',
                'type'        => 'checkbox',
                'default'     => '1',
            ],
            'page_max_width' => [
                'label'       => 'Maximale Seitenbreite (px)',
                'description' => 'Max. Breite des Seiteninhalts.',
                'type'        => 'number',
                'default'     => '960',
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

$activeTab = preg_replace('/[^a-z0-9_-]/i', '', (string)($_GET['tab'] ?? 'colors')) ?: 'colors';
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
            $success = 'Einstellungen für „' . (string)($config[$resetTab]['title'] ?? '') . '“ auf Standardwerte zurückgesetzt.';
        }
    }
}

// -- Save --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_theme_options') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'ptc_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        // Track uploaded file fields to avoid overwriting with stale POST values
        $uploadedFields = [];

        // Logo-Upload
        if (!empty($_FILES['logo_upload_file']['tmp_name'])) {
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            $fileExt     = strtolower(pathinfo($_FILES['logo_upload_file']['name'], PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowedExts, true)) {
                $uploadDir = UPLOAD_PATH . 'theme-logos';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $newFileName = 'theme-logo-' . time() . '.' . $fileExt;
                $destPath    = $uploadDir . '/' . $newFileName;
                if (move_uploaded_file($_FILES['logo_upload_file']['tmp_name'], $destPath)) {
                    $customizer->set('header', 'logo_url', UPLOAD_URL . '/theme-logos/' . $newFileName);
                    $uploadedFields['header:logo_url'] = true;
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
                $newFileName = 'theme-hero-' . time() . '.' . $fileExt;
                $destPath    = $uploadDir . '/' . $newFileName;
                if (move_uploaded_file($_FILES['hero_bg_upload_file']['tmp_name'], $destPath)) {
                    $customizer->set('homepage', 'hero_bg_image', UPLOAD_URL . '/theme-images/' . $newFileName);
                    $uploadedFields['homepage:hero_bg_image'] = true;
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

                // Bild-Uploads: Skip wenn bereits per Datei-Upload gespeichert
                if ($fieldConfig['type'] === 'image_upload') {
                    if (isset($uploadedFields["{$saveTab}:{$fieldKey}"])) {
                        continue; // Datei-Upload hat Vorrang
                    }
                    $postVal = trim($_POST[$inputName] ?? '');
                    if ($postVal !== '') {
                        if (!$customizer->set($saveTab, $fieldKey, $postVal)) {
                            $saveFailed = true;
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
                $success = 'Einstellungen für „' . (string)($config[$saveTab]['title'] ?? '') . '“ gespeichert.';
            }
        }
    }
}

// Token EINMAL generieren
$csrfToken = Security::instance()->generateToken('ptc_customizer');

$coreMainCssUrl = function_exists('cms_asset_url')
    ? cms_asset_url('css/main.css')
    : SITE_URL . '/assets/css/main.css';
$coreAdminCssUrl = function_exists('cms_asset_url')
    ? cms_asset_url('css/admin.css')
    : SITE_URL . '/assets/css/admin.css?v=20260222b';
$coreAdminJsUrl = function_exists('cms_asset_url')
    ? cms_asset_url('js/admin.js')
    : SITE_URL . '/assets/js/admin.js';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Customizer – <?php echo defined('SITE_NAME') ? $esc(SITE_NAME) : ''; ?></title>
    <link rel="stylesheet" href="<?php echo $esc($coreMainCssUrl); ?>">
    <link rel="stylesheet" href="<?php echo $esc($coreAdminCssUrl); ?>">
    <?php renderAdminSidebarStyles(); ?>
    <style>
        .customizer-layout { display: flex; gap: 2rem; align-items: flex-start; }
        .customizer-nav { width: 240px; flex-shrink: 0; background: #fff; border-radius: var(--card-radius, 10px); border: var(--card-border, 1px solid #e2e8f0); overflow: hidden; }
        .customizer-nav a { display: block; padding: 1rem 1.5rem; color: #64748b; text-decoration: none; border-left: 3px solid transparent; transition: all .2s; font-size: .9rem; }
        .customizer-nav a:hover { background: #f8fafc; color: var(--admin-primary, #3b82f6); }
        .customizer-nav a.active { background: #eff6ff; color: var(--admin-primary, #3b82f6); border-left-color: var(--admin-primary, #3b82f6); font-weight: 600; }
        .customizer-nav-group-label { display: block; padding: .6rem 1.5rem .3rem; font-size: .72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; border-top: 1px solid #f1f5f9; margin-top: .25rem; }
        .customizer-nav a.customizer-nav-sub { padding-left: 2.25rem; font-size: .85rem; }
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
            .customizer-nav a.customizer-nav-sub { padding-left: 1rem; }
            .customizer-nav-group-label { padding: .4rem .75rem .15rem; border-top: none; margin-top: 0; }
        }
    </style>
</head>
<body class="admin-body">

    <?php renderAdminSidebar('theme-customizer'); ?>

    <div class="admin-content">

        <div class="admin-page-header">
            <div>
                <h2>🎨 Theme Customizer – PTC</h2>
                <p>Passe das Aussehen des des Themes an.</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo $esc(SITE_URL); ?>/" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">🌐 Seite ansehen</a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $esc($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $esc($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="?tab=<?php echo $esc($activeTab); ?>" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_theme_options">
            <input type="hidden" name="active_section" value="<?php echo $esc($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $esc($csrfToken); ?>">

            <div class="customizer-layout">

                <!-- Tab-Navigation -->
                <nav class="customizer-nav">
                    <?php
                    // Navigationsstruktur mit Gruppierung
                    $navStructure = [
                        ['key' => 'colors'],
                        ['key' => 'typography'],
                        ['key' => 'layout'],
                        ['key' => 'header'],
                        ['group' => '🏠 Startseite', 'items' => [
                            'homepage' => '🎬 Hero & CTA',
                            'services' => '🛠️ Dienstleistungen',
                            'events'   => '📅 Termine',
                            'faq'      => '❓ FAQ',
                        ]],
                        ['key' => 'footer'],
                        ['key' => 'blog'],
                        ['key' => 'buttons'],
                        ['key' => 'advanced'],
                    ];
                    $homepageSubTabs = ['homepage', 'services', 'events', 'faq'];
                    foreach ($navStructure as $navItem):
                        if (isset($navItem['group'])):
                    ?>
                        <span class="customizer-nav-group-label"><?php echo $esc($navItem['group']); ?></span>
                        <?php foreach ($navItem['items'] as $subKey => $subLabel): ?>
                            <a href="?tab=<?php echo $esc($subKey); ?>"
                               class="customizer-nav-sub<?php echo $activeTab === $subKey ? ' active' : ''; ?>">
                                <?php echo $esc($subLabel); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="?tab=<?php echo $esc($navItem['key']); ?>"
                           class="<?php echo $activeTab === $navItem['key'] ? 'active' : ''; ?>">
                            <?php echo $esc($config[$navItem['key']]['title'] ?? ''); ?>
                        </a>
                    <?php endif; endforeach; ?>
                </nav>

                <!-- Inhaltsbereich -->
                <div class="customizer-content">
                    <?php if (isset($config[$activeTab])): $currentSection = $config[$activeTab]; ?>

                    <?php if ($activeTab === 'colors'):
                        // ── Farben: 3-Spalten-Karten-Layout ──────────────────────
                        $colorGroups = [
                            '🎨 Markenfarben'          => ['primary_color', 'primary_hover', 'primary_light', 'accent_color', 'accent_hover', 'accent_light', 'secondary_color'],
                            '📝 Text & Links'           => ['text_color', 'heading_color', 'text_light', 'muted_color', 'link_color', 'link_hover_color'],
                            '🖼️ Hintergrund & Status'  => ['bg_color', 'bg_secondary', 'border_color', 'success_color', 'error_color'],
                            '📢 CTA-Sektion'            => ['cta_bg_color', 'cta_bg_to', 'cta_text_color'],
                            '🖼️ Seitenränder'          => ['side_margin_color', 'side_accent_color', 'side_accent_width'],
                            '🎬 Hero-Sektion'           => ['hero_bg_color', 'hero_bg_to'],
                            '🛠️ Dienstleistungen'      => ['services_bg_color', 'services_card_bg'],
                            '📅 Termine'                => ['events_bg_color', 'events_card_bg'],
                            '❓ FAQ'                     => ['faq_bg_color', 'faq_item_bg'],
                            '🔗 Network-Bar'            => ['network_bar_bg', 'network_bar_text'],
                            '📰 Blog'                   => ['blog_hero_bg', 'blog_hero_text', 'blog_content_bg', 'blog_card_bg', 'blog_card_title', 'blog_category_bg', 'blog_category_text'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo $esc($currentSection['title']); ?></h3>
                        <div class="color-cards-grid">
                            <?php foreach ($colorGroups as $groupTitle => $groupKeys): ?>
                            <div class="color-card">
                                <h4><?php echo $esc($groupTitle); ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $esc($inputId); ?>" class="form-label">
                                        <?php echo $esc($field['label']); ?>
                                    </label>
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        <input type="color" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                               value="<?php echo $esc($val); ?>"
                                               style="height:38px;padding:2px;width:60px;border:1px solid #ddd;border-radius:4px;">
                                        <input type="text" value="<?php echo $esc($val); ?>"
                                               class="form-control" style="width:120px;"
                                               onchange="document.getElementById('<?php echo $esc($inputId); ?>').value = this.value; updateLivePreview();">
                                    </div>
                                    <?php if (!empty($field['description'])): ?>
                                        <small class="form-text"><?php echo $esc($field['description']); ?></small>
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
                            ' Call-to-Action'           => ['show_cta', 'cta_title', 'cta_text', 'cta_button_label', 'cta_button_url', 'cta_secondary_label', 'cta_secondary_url'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo $esc($currentSection['title']); ?></h3>
                        <div class="homepage-cards-grid">
                            <?php foreach ($homepageGroups as $groupTitle => $groupKeys): ?>
                            <div class="homepage-card">
                                <h4><?php echo $esc($groupTitle); ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $esc($inputId); ?>" class="form-label">
                                        <?php echo $esc($field['label']); ?>
                                    </label>

                                    <?php if ($field['type'] === 'checkbox'): ?>
                                        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                                            <input type="checkbox" id="<?php echo $esc($inputId); ?>"
                                                   name="<?php echo $esc($inputName); ?>" value="1"
                                                   <?php echo $val ? 'checked' : ''; ?>>
                                            <label for="<?php echo $esc($inputId); ?>" style="cursor:pointer;">Aktivieren</label>
                                        </div>

                                    <?php elseif ($field['type'] === 'select'): ?>
                                        <select id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                class="form-control">
                                            <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                                            <option value="<?php echo $esc($optVal); ?>"
                                                <?php echo (string)$val === (string)$optVal ? 'selected' : ''; ?>>
                                                <?php echo $esc($optLabel); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>

                                    <?php elseif ($field['type'] === 'textarea'): ?>
                                        <textarea id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                  class="form-control" rows="3"
                                        ><?php echo $esc($val); ?></textarea>

                                    <?php elseif ($field['type'] === 'image_upload'): ?>
                                        <?php $previewUrl = $val ? $esc($val) : ''; ?>
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
                                              <input type="text" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                   value="<?php echo $previewUrl; ?>" class="form-control"
                                                   placeholder="https://...">
                                        </div>

                                    <?php elseif ($field['type'] === 'number'): ?>
                                             <input type="number" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                 value="<?php echo $esc($val); ?>"
                                               class="form-control" style="width:120px;">

                                    <?php else: ?>
                                             <input type="<?php echo $esc($field['type']); ?>"
                                                 id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                 value="<?php echo $esc($val); ?>"
                                               class="form-control">
                                    <?php endif; ?>

                                    <?php if (!empty($field['description'])): ?>
                                             <small class="form-text"><?php echo $esc($field['description']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'header' || $activeTab === 'footer' || $activeTab === 'buttons' || $activeTab === 'services' || $activeTab === 'events' || $activeTab === 'faq' || $activeTab === 'blog'):
                        // ── Header / Footer / Buttons / Services / Events / FAQ: 2-Spalten-Karten-Layout ──
                        $tabCardGroups = [
                            'header' => [
                                '🖼️ Logo'              => ['logo_url', 'logo_text', 'logo_max_height'],
                                '🎨 Header-Farben'      => ['header_bg_color', 'header_text_color', 'header_accent_color'],
                                '📐 Header-Layout'      => ['header_height', 'show_header_shadow', 'nav_font_size', 'nav_sub_font_size'],
                                '🔗 Header-Aktionen'    => ['show_login_btn', 'login_btn_icon_only', 'show_register_btn', 'register_btn_icon_only', 'header_cta_text', 'header_cta_url'],
                                '✨ Animation'           => ['header_animation'],
                            ],
                            'footer' => [
                                '📐 Footer-Layout'       => ['footer_width'],
                                '🎨 Footer-Farben'      => ['footer_bg_color', 'footer_text_color', 'footer_link_color'],
                                '📝 Footer-Inhalte'     => ['footer_tagline', 'footer_address', 'footer_phone', 'footer_email', 'copyright_text'],
                                '🌐 Social Media'       => ['social_facebook', 'social_instagram', 'social_linkedin', 'social_xing', 'social_youtube'],
                                '🔗 Network Bar'        => ['show_network_bar', 'network_bar_name', 'network_bar_link1_label', 'network_bar_link1_url', 'network_bar_link2_label', 'network_bar_link2_url', 'network_bar_link3_label', 'network_bar_link3_url'],
                            ],
                            'buttons' => [
                                '📐 Button-Form'        => ['button_border_radius', 'button_padding_x', 'button_padding_y'],
                                '🔤 Button-Text'        => ['button_font_weight', 'button_transform'],
                            ],
                            'services' => [
                                '⚙️ Allgemeine Einstellungen' => ['show_services', 'services_tag', 'services_title', 'services_subtitle'],
                                '📐 Layout & Design'          => ['services_columns', 'services_bg_style', 'services_card_style', 'services_icon_style', 'services_show_hover', 'services_show_icons', 'services_max_items'],
                                '🔗 CTA-Button'               => ['services_show_cta', 'services_cta_label', 'services_cta_url'],
                                '🔶 Service 1'                => ['service_1_icon', 'service_1_image', 'service_1_title', 'service_1_text', 'service_1_url'],
                                '🔶 Service 2'                => ['service_2_icon', 'service_2_image', 'service_2_title', 'service_2_text', 'service_2_url'],
                                '🔶 Service 3'                => ['service_3_icon', 'service_3_image', 'service_3_title', 'service_3_text', 'service_3_url'],
                                '🔶 Service 4'                => ['service_4_icon', 'service_4_image', 'service_4_title', 'service_4_text', 'service_4_url'],
                                '🔶 Service 5'                => ['service_5_icon', 'service_5_image', 'service_5_title', 'service_5_text', 'service_5_url'],
                                '🔶 Service 6'                => ['service_6_icon', 'service_6_image', 'service_6_title', 'service_6_text', 'service_6_url'],
                                '🔶 Service 7 (optional)'     => ['service_7_icon', 'service_7_image', 'service_7_title', 'service_7_text', 'service_7_url'],
                                '🔶 Service 8 (optional)'     => ['service_8_icon', 'service_8_image', 'service_8_title', 'service_8_text', 'service_8_url'],
                                '🔶 Service 9 (optional)'     => ['service_9_icon', 'service_9_image', 'service_9_title', 'service_9_text', 'service_9_url'],
                                '🔶 Service 10 (optional)'    => ['service_10_icon', 'service_10_image', 'service_10_title', 'service_10_text', 'service_10_url'],
                                '🔶 Service 11 (optional)'    => ['service_11_icon', 'service_11_image', 'service_11_title', 'service_11_text', 'service_11_url'],
                                '🔶 Service 12 (optional)'    => ['service_12_icon', 'service_12_image', 'service_12_title', 'service_12_text', 'service_12_url'],
                            ],
                            'events' => [
                                '⚙️ Allgemeine Einstellungen' => ['show_events', 'events_tag', 'events_title', 'events_subtitle'],
                                '📐 Layout & Design'          => ['events_source', 'events_max_items', 'events_columns', 'events_bg_style', 'events_card_style', 'events_show_date_badge', 'events_link_text'],
                                '📭 Platzhalter'              => ['events_show_empty', 'events_empty_text', 'events_empty_hint'],
                                '🔗 CTA-Button'               => ['events_show_cta', 'events_cta_label', 'events_cta_url'],
                                '📅 MS Booking'               => ['events_booking_url', 'events_booking_title', 'events_booking_height'],
                                '📌 Termin 1'                 => ['event_1_title', 'event_1_date', 'event_1_text', 'event_1_url'],
                                '📌 Termin 2'                 => ['event_2_title', 'event_2_date', 'event_2_text', 'event_2_url'],
                                '📌 Termin 3'                 => ['event_3_title', 'event_3_date', 'event_3_text', 'event_3_url'],
                                '📌 Termin 4'                 => ['event_4_title', 'event_4_date', 'event_4_text', 'event_4_url'],
                                '📌 Termin 5'                 => ['event_5_title', 'event_5_date', 'event_5_text', 'event_5_url'],
                                '📌 Termin 6'                 => ['event_6_title', 'event_6_date', 'event_6_text', 'event_6_url'],
                            ],
                            'faq' => [
                                '⚙️ Allgemeine Einstellungen' => ['show_faq', 'faq_tag', 'faq_title', 'faq_subtitle'],
                                '📐 Layout & Design'          => ['faq_style', 'faq_max_width', 'faq_bg_style'],
                                '🔗 CTA-Bereich'              => ['faq_show_cta', 'faq_cta_text', 'faq_cta_label', 'faq_cta_url'],
                                '💬 Frage 1'                  => ['faq_1_question', 'faq_1_answer'],
                                '💬 Frage 2'                  => ['faq_2_question', 'faq_2_answer'],
                                '💬 Frage 3'                  => ['faq_3_question', 'faq_3_answer'],
                                '💬 Frage 4'                  => ['faq_4_question', 'faq_4_answer'],
                                '💬 Frage 5'                  => ['faq_5_question', 'faq_5_answer'],
                                '💬 Frage 6'                  => ['faq_6_question', 'faq_6_answer'],
                                '💬 Frage 7 (optional)'       => ['faq_7_question', 'faq_7_answer'],
                                '💬 Frage 8 (optional)'       => ['faq_8_question', 'faq_8_answer'],
                            ],
                            'blog' => [
                                '📰 Blog-Übersicht'       => ['blog_layout', 'blog_columns', 'blog_show_sidebar', 'blog_hero_title', 'blog_hero_subtitle'],
                                '🖼️ Blog-Karten'          => ['blog_show_featured_image', 'blog_show_excerpt', 'blog_show_date', 'blog_show_author', 'blog_show_category'],
                                '📄 Einzelbeitrag'        => ['blog_single_show_featured', 'blog_single_show_author', 'blog_single_show_date', 'blog_single_show_tags', 'blog_single_show_reading_time', 'blog_single_show_related', 'blog_single_max_width'],
                                '📃 Seiten-Einstellungen' => ['page_show_title', 'page_show_updated', 'page_show_featured_image', 'page_max_width'],
                            ],
                        ];
                        $cardGroups = $tabCardGroups[$activeTab] ?? [];
                    ?>
                    <div class="admin-card">
                        <h3><?php echo $esc($currentSection['title']); ?></h3>
                        <div class="homepage-cards-grid">
                            <?php foreach ($cardGroups as $groupTitle => $groupKeys): ?>
                            <div class="homepage-card">
                                <h4><?php echo $esc($groupTitle); ?></h4>
                                <?php foreach ($groupKeys as $fieldKey):
                                    if (!isset($currentSection['sections'][$fieldKey])) { continue; }
                                    $field     = $currentSection['sections'][$fieldKey];
                                    $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                                    $inputId   = "field_{$activeTab}_{$fieldKey}";
                                    $inputName = "{$activeTab}_{$fieldKey}";
                                ?>
                                <div class="form-group">
                                    <label for="<?php echo $esc($inputId); ?>" class="form-label">
                                        <?php echo $esc($field['label']); ?>
                                    </label>

                                    <?php if ($field['type'] === 'image_upload'): ?>
                                        <?php $previewUrl = $val ? $esc($val) : ''; ?>
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
                                            <input type="text" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                   value="<?php echo $previewUrl; ?>" class="form-control"
                                                   placeholder="https://..." oninput="syncLogoUrlPreview(this.value)">
                                        </div>

                                    <?php elseif ($field['type'] === 'color'): ?>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <input type="color" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                   value="<?php echo $esc($val); ?>"
                                                   style="height:38px;padding:2px;width:60px;border:1px solid #ddd;border-radius:4px;">
                                            <input type="text" value="<?php echo $esc($val); ?>"
                                                   class="form-control" style="width:120px;"
                                                   onchange="document.getElementById('<?php echo $esc($inputId); ?>').value = this.value; updateLivePreview();">
                                        </div>

                                    <?php elseif ($field['type'] === 'checkbox'): ?>
                                        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                                            <input type="checkbox" id="<?php echo $esc($inputId); ?>"
                                                   name="<?php echo $esc($inputName); ?>" value="1"
                                                   <?php echo $val ? 'checked' : ''; ?>>
                                            <label for="<?php echo $esc($inputId); ?>" style="cursor:pointer;">Aktivieren</label>
                                        </div>

                                    <?php elseif ($field['type'] === 'textarea'): ?>
                                        <textarea id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                  class="form-control" rows="3"
                                        ><?php echo $esc($val); ?></textarea>

                                    <?php elseif ($field['type'] === 'select'): ?>
                                        <select id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                                class="form-control">
                                            <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                                            <option value="<?php echo $esc($optVal); ?>"
                                                <?php echo (string)$val === (string)$optVal ? 'selected' : ''; ?>>
                                                <?php echo $esc($optLabel); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>

                                    <?php elseif ($field['type'] === 'number'): ?>
                                        <input type="number" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                               value="<?php echo $esc($val); ?>"
                                               class="form-control" style="width:120px;">

                                    <?php else: ?>
                                        <input type="<?php echo $esc($field['type']); ?>"
                                               id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                               value="<?php echo $esc($val); ?>"
                                               class="form-control">
                                    <?php endif; ?>

                                    <?php if (!empty($field['description'])): ?>
                                        <small class="form-text"><?php echo $esc($field['description']); ?></small>
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
                        <h3><?php echo $esc($currentSection['title']); ?></h3>

                        <?php foreach ($currentSection['sections'] as $fieldKey => $field):
                            $val       = $customizer->get($activeTab, $fieldKey, $field['default']);
                            $inputId   = "field_{$activeTab}_{$fieldKey}";
                            $inputName = "{$activeTab}_{$fieldKey}";
                        ?>
                        <div class="form-group">
                            <label for="<?php echo $esc($inputId); ?>" class="form-label">
                                <?php echo $esc($field['label']); ?>
                            </label>

                            <?php if ($field['type'] === 'textarea'): ?>
                                <textarea id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                          class="form-control" rows="4"
                                ><?php echo $esc($val); ?></textarea>

                            <?php elseif ($field['type'] === 'checkbox'): ?>
                                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                                    <input type="checkbox" id="<?php echo $esc($inputId); ?>"
                                           name="<?php echo $esc($inputName); ?>" value="1"
                                           <?php echo $val ? 'checked' : ''; ?>>
                                    <label for="<?php echo $esc($inputId); ?>" style="cursor:pointer;">Aktivieren</label>
                                </div>

                            <?php elseif ($field['type'] === 'select'): ?>
                                <select id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                        class="form-control">
                                    <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                                    <option value="<?php echo $esc($optVal); ?>"
                                        <?php echo (string)$val === (string)$optVal ? 'selected' : ''; ?>>
                                        <?php echo $esc($optLabel); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                            <?php elseif ($field['type'] === 'color'): ?>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <input type="color" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                           value="<?php echo $esc($val); ?>"
                                           style="height:38px;padding:2px;width:60px;border:1px solid #ddd;border-radius:4px;">
                                    <input type="text" value="<?php echo $esc($val); ?>"
                                           class="form-control" style="width:120px;"
                                           onchange="document.getElementById('<?php echo $esc($inputId); ?>').value = this.value; updateLivePreview();">
                                </div>

                            <?php elseif ($field['type'] === 'number'): ?>
                                <input type="number" id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                       value="<?php echo $esc($val); ?>"
                                       class="form-control" style="width:120px;">

                            <?php else: ?>
                                <input type="<?php echo $esc($field['type']); ?>"
                                       id="<?php echo $esc($inputId); ?>" name="<?php echo $esc($inputName); ?>"
                                       value="<?php echo $esc($val); ?>"
                                       class="form-control">
                            <?php endif; ?>

                            <?php if (!empty($field['description'])): ?>
                                <small class="form-text"><?php echo $esc($field['description']); ?></small>
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
    <form id="reset-form" method="POST" action="?tab=<?php echo $esc($activeTab); ?>" style="display:none;">
        <input type="hidden" name="action" value="reset_theme_tab">
        <input type="hidden" name="active_section" value="<?php echo $esc($activeTab); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $esc($csrfToken); ?>">
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
                <p>Alle Einstellungen dieses Tabs werden auf die <strong>Standard-Designwerte</strong> des Themes zurückgesetzt.</p>
                <p style="color:#64748b;font-size:.875rem;">Bereits gespeicherte Anpassungen gehen für diesen Bereich verloren.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeResetModal()">Abbrechen</button>
                <button type="button" class="btn btn-danger" onclick="confirmReset()">↺ Zurücksetzen</button>
            </div>
        </div>
    </div>

    <script src="<?php echo $esc($coreAdminJsUrl); ?>"></script>
    <script>
    // ── Farb-Picker ↔ Text-Input + Live-Vorschau ─────────────────────────────
    (function () {

        var liveStyle = document.createElement('style');
        liveStyle.id  = 'customizer-live-preview';
        document.head.appendChild(liveStyle);

        // PTC CSS-Variable-Mapping
        var cssVarMap = {
            'colors_primary_color':     '--color-primary',
            'colors_primary_hover':     '--color-primary-hover',
            'colors_primary_light':     '--color-primary-light',
            'colors_accent_color':      '--color-accent',
            'colors_accent_hover':      '--color-accent-hover',
            'colors_accent_light':      '--color-accent-light',
            'colors_secondary_color':   '--color-secondary',
            'colors_text_color':        '--color-text',
            'colors_heading_color':     '--color-heading',
            'colors_text_light':        '--color-on-dark',
            'colors_muted_color':       '--color-muted',
            'colors_bg_color':          '--color-bg',
            'colors_bg_secondary':      '--color-bg-alt',
            'colors_link_color':        '--color-link',
            'colors_link_hover_color':  '--color-link-hover',
            'colors_border_color':      '--color-border',
            'colors_success_color':     '--color-success',
            'colors_error_color':       '--color-error',
            'header_header_bg_color':   '--color-primary',
            'header_header_text_color': '--color-on-dark',
            'header_header_accent_color': '--color-accent',
            'footer_footer_bg_color':   '--footer-bg',
            'footer_footer_text_color': '--footer-text',
            'footer_footer_link_color': '--footer-link',
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
