<?php
/**
 * CMS Phinit Theme – Customizer (Admin)
 *
 * Umfangreiche Anpassungsmöglichkeiten für das technische Tech-Blog-Theme
 * inspiriert von phinit.de – IT-Profi, Deep Navy, Gold, Code-Ästhetik.
 *
 * @package CMS_Phinit_Theme
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

    // ═══════════════════════════════════════════════════════════════════════
    // FARBEN
    // ═══════════════════════════════════════════════════════════════════════
    'colors' => [
        'title' => '🎨 Farben',
        'sections' => [

            // ── Markenfarben ──
            'primary_color' => [
                'label'       => 'Primärfarbe (Navy)',
                'description' => 'Hauptfarbe – Dunkelblau für Header, Links und Akzente.',
                'type'        => 'color',
                'default'     => '#1e3a5f',
            ],
            'primary_dark' => [
                'label'       => 'Primärfarbe Dunkel',
                'description' => 'Noch dunkleres Navy für Hover-Zustände.',
                'type'        => 'color',
                'default'     => '#0f2340',
            ],
            'primary_mid' => [
                'label'       => 'Primärfarbe Mittel',
                'description' => 'Mittleres Navy – Header Bar 2.',
                'type'        => 'color',
                'default'     => '#1a3255',
            ],
            'primary_light' => [
                'label'       => 'Primärfarbe Hell',
                'description' => 'Aufgehelltes Navy für Badge-Hintergründe.',
                'type'        => 'color',
                'default'     => '#2a4f7c',
            ],
            'accent_color' => [
                'label'       => 'Akzentfarbe (Gold/Amber)',
                'description' => 'Goldton für CTAs, Highlights und spezielle Badges.',
                'type'        => 'color',
                'default'     => '#e8a838',
            ],
            'accent_hover' => [
                'label'       => 'Akzentfarbe Hover',
                'description' => 'Dunkleres Gold für Hover-Zustände.',
                'type'        => 'color',
                'default'     => '#d4922a',
            ],
            'accent_blue' => [
                'label'       => 'Akzentfarbe Blau (IT)',
                'description' => 'Technik-Blau für Links, TOC, Code-Hervorhebungen.',
                'type'        => 'color',
                'default'     => '#4a9eff',
            ],
            'accent_blue2' => [
                'label'       => 'Akzentfarbe Blau 2 (satt)',
                'description' => 'Satteres Blau für Buttons und aktive Badges.',
                'type'        => 'color',
                'default'     => '#2d7dd2',
            ],
            'accent_teal' => [
                'label'       => 'Akzentfarbe Teal',
                'description' => 'Teal-Ton für Navigations-Hover, Links, Badges, Karten-Akzente.',
                'type'        => 'color',
                'default'     => '#0d9488',
            ],
            'accent_teal_light' => [
                'label'       => 'Akzentfarbe Teal Hell',
                'description' => 'Helleres Teal für Logo-Akzent, Hervorhebungen, aktive Elemente.',
                'type'        => 'color',
                'default'     => '#14b8a6',
            ],

            // ── Header-Hintergründe ──
            'bg_header1' => [
                'label'       => 'Header Bar 1 Hintergrund',
                'description' => 'Member-Bar Hintergrundfarbe (oberste Header-Leiste).',
                'type'        => 'color',
                'default'     => '#111827',
            ],
            'bg_header2' => [
                'label'       => 'Header Bar 2 Hintergrund',
                'description' => 'Haupt-Navigation Hintergrundfarbe.',
                'type'        => 'color',
                'default'     => '#162030',
            ],
            'bg_header3' => [
                'label'       => 'Header Bar 3 Hintergrund',
                'description' => 'Quicklinks-Subbar Hintergrundfarbe.',
                'type'        => 'color',
                'default'     => '#0e1a28',
            ],

            // ── Seite & Inhalt ──
            'bg_primary' => [
                'label'       => 'Content-Hintergrund',
                'description' => 'Hintergrundfarbe von Cards, Post-Body, Sidebar-Widgets.',
                'type'        => 'color',
                'default'     => '#ffffff',
            ],
            'bg_secondary' => [
                'label'       => 'Seitenhintergrund',
                'description' => 'Hintergrundfarbe der gesamten Seite (body).',
                'type'        => 'color',
                'default'     => '#f1f5f9',
            ],
            'bg_dark' => [
                'label'       => 'Dunkelbereich-Hintergrund',
                'description' => 'Sehr dunkler Hintergrund für spezielle Dark-Sektionen.',
                'type'        => 'color',
                'default'     => '#0a0f1a',
            ],

            // ── Text ──
            'text_primary' => [
                'label'       => 'Primäre Textfarbe',
                'description' => 'Standard-Fließtextfarbe.',
                'type'        => 'color',
                'default'     => '#1e293b',
            ],
            'text_secondary' => [
                'label'       => 'Sekundäre Textfarbe',
                'description' => 'Beschreibungstext, Subtitles.',
                'type'        => 'color',
                'default'     => '#4a5568',
            ],
            'text_muted' => [
                'label'       => 'Gedämpfte Textfarbe',
                'description' => 'Timestamps, Meta-Infos, Platzhalter.',
                'type'        => 'color',
                'default'     => '#7a8898',
            ],
            'text_nav' => [
                'label'       => 'Navigationstext (Fallback)',
                'description' => 'Allgemeine Textfarbe für alle Navigationslinks – wird von den einzelnen Bereichen überschrieben.',
                'type'        => 'color',
                'default'     => '#c8d4e4',
            ],
            'text_nav_member' => [
                'label'       => 'Member-Bar Textfarbe',
                'description' => 'Textfarbe der Links in der obersten Member-Leiste.',
                'type'        => 'color',
                'default'     => 'rgba(255,255,255,.72)',
            ],
            'text_nav_main' => [
                'label'       => 'Hauptnavigation Textfarbe',
                'description' => 'Textfarbe der Links in der Hauptnavigation (Bar 2).',
                'type'        => 'color',
                'default'     => 'rgba(255,255,255,.82)',
            ],
            'text_nav_quicklinks' => [
                'label'       => 'Quicklinks Textfarbe',
                'description' => 'Textfarbe der Links in der Quicklinks-Subnavigation.',
                'type'        => 'color',
                'default'     => '#334155',
            ],
            'text_nav_dropdown' => [
                'label'       => 'Dropdown Textfarbe',
                'description' => 'Textfarbe der Links in ausgeklappten Dropdown-Menüs.',
                'type'        => 'color',
                'default'     => 'rgba(255,255,255,.82)',
            ],
            'logo_suffix_color' => [
                'label'       => 'Logo-Suffix Farbe (.DE)',
                'description' => 'Farbe des Suffix-Teils im Text-Logo (z. B. ".DE").',
                'type'        => 'color',
                'default'     => '#e8a838',
            ],

            // ── Rahmen ──
            'border_light' => [
                'label'       => 'Rahmen (hell)',
                'description' => 'Standard-Rahmenfarbe für Cards und Elemente.',
                'type'        => 'color',
                'default'     => '#dde3ea',
            ],

            // ── Footer ──
            'footer_bg' => [
                'label'       => 'Footer Hintergrundfarbe',
                'description' => 'Haupthintergrund des Footers.',
                'type'        => 'color',
                'default'     => '#0d1828',
            ],
            'footer_bottom_bg' => [
                'label'       => 'Footer Bottom-Bar Hintergrundfarbe',
                'description' => 'Partner-Bar und Copyright-Zeile.',
                'type'        => 'color',
                'default'     => '#080d15',
            ],
            'footer_border' => [
                'label'       => 'Footer-Oberrand (Akzentlinie)',
                'description' => 'Farbe der Trennlinie am oberen Footer-Rand.',
                'type'        => 'color',
                'default'     => '#2d7dd2',
            ],

            // ── Status-Farben ──
            'success_color' => [
                'label'       => 'Erfolgsfarbe',
                'description' => 'Grün für Erfolgsmeldungen und Status-OK.',
                'type'        => 'color',
                'default'     => '#16a34a',
            ],
            'error_color' => [
                'label'       => 'Fehlerfarbe',
                'description' => 'Rot für Fehlermeldungen und Warnungen.',
                'type'        => 'color',
                'default'     => '#dc2626',
            ],

            // ── Reading Progress Bar ──
            'progress_bar_start' => [
                'label'       => 'Lese-Fortschrittsbalken (Farbe Start)',
                'description' => 'Startfarbe des Lese-Fortschrittsbalkens ganz oben.',
                'type'        => 'color',
                'default'     => '#2d7dd2',
            ],
            'progress_bar_end' => [
                'label'       => 'Lese-Fortschrittsbalken (Farbe Ende)',
                'description' => 'Endfarbe des Lese-Fortschrittsbalkens.',
                'type'        => 'color',
                'default'     => '#e8a838',
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // TYPOGRAFIE
    // ═══════════════════════════════════════════════════════════════════════
    'typography' => [
        'title' => '🔤 Typografie',
        'sections' => [
            'font_family_ui' => [
                'label'       => 'UI-Schriftart (Fließtext)',
                'description' => 'Schriftart für allgemeinen Text, Navigation, Forms.',
                'type'        => 'select',
                'options'     => [
                    'barlow'      => 'Barlow (Standard IT-Tech)',
                    'system'      => 'System-Standard',
                    'inter'       => 'Inter',
                    'roboto'      => 'Roboto',
                    'open-sans'   => 'Open Sans',
                    'lato'        => 'Lato',
                    'montserrat'  => 'Montserrat',
                    'poppins'     => 'Poppins',
                    'source-sans' => 'Source Sans 3',
                    'nunito'      => 'Nunito',
                ],
                'default'     => 'barlow',
            ],
            'font_family_brand' => [
                'label'       => 'Brand-Schriftart (Überschriften & Navigation)',
                'description' => 'Schriftart für Seitenname, Überschriften, Navigationsitems.',
                'type'        => 'select',
                'options'     => [
                    'barlow-condensed' => 'Barlow Condensed (Standard)',
                    'barlow'           => 'Barlow',
                    'system'           => 'System-Standard',
                    'roboto-condensed' => 'Roboto Condensed',
                    'oswald'           => 'Oswald',
                    'montserrat'       => 'Montserrat',
                    'rajdhani'         => 'Rajdhani',
                    'exo2'             => 'Exo 2',
                ],
                'default'     => 'barlow-condensed',
            ],
            'font_family_code' => [
                'label'       => 'Code-Schriftart',
                'description' => 'Monospace-Schriftart für Code-Blöcke und inline-Code.',
                'type'        => 'select',
                'options'     => [
                    'jetbrains-mono' => 'JetBrains Mono (Standard)',
                    'fira-code'      => 'Fira Code',
                    'source-code'    => 'Source Code Pro',
                    'cascadia'       => 'Cascadia Code',
                    'system-mono'    => 'System Monospace',
                ],
                'default'     => 'jetbrains-mono',
            ],
            'font_size_base' => [
                'label'       => 'Basis-Schriftgröße (px)',
                'description' => 'Schriftgröße des Fließtexts.',
                'type'        => 'number',
                'default'     => 14.5,
            ],
            'font_size_post' => [
                'label'       => 'Artikel-Schriftgröße (px)',
                'description' => 'Schriftgröße im Post-Body für bessere Lesbarkeit.',
                'type'        => 'number',
                'default'     => 15.5,
            ],
            'line_height_base' => [
                'label'       => 'Zeilenhöhe',
                'description' => 'Zeilenhöhe des Fließtexts (Faktor, z. B. 1.55).',
                'type'        => 'number',
                'default'     => 1.55,
            ],
            'line_height_post' => [
                'label'       => 'Zeilenhöhe Artikel',
                'description' => 'Zeilenhöhe im Post-Body (Standard: 1.8 für bessere Lesbarkeit).',
                'type'        => 'number',
                'default'     => 1.8,
            ],
            'font_weight_heading' => [
                'label'       => 'Überschriften-Gewicht',
                'description' => 'Font-Weight für h1–h3.',
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
            'font_weight_nav' => [
                'label'       => 'Navigation-Gewicht',
                'description' => 'Font-Weight für Navigationslinks.',
                'type'        => 'select',
                'options'     => [
                    '400' => 'Regular (400)',
                    '500' => 'Medium (500)',
                    '600' => 'Semi-Bold (600)',
                    '700' => 'Bold (700)',
                    '800' => 'Extra-Bold (800)',
                ],
                'default'     => '600',
            ],
            // ── Teaser-Texte (List & Grid Cards) ──
            'article_title_fontsize' => [
                'label'       => 'Listen-Card: Titel Schriftgröße (px)',
                'description' => 'Schriftgröße des Artikeltitels in der Artikel-Liste.',
                'type'        => 'number',
                'default'     => 16,
            ],
            'tile_title_fontsize' => [
                'label'       => 'Grid-Card: Titel Schriftgröße (px)',
                'description' => 'Schriftgröße des Artikeltitels in den Kachel-Cards.',
                'type'        => 'number',
                'default'     => 15,
            ],
            'article_excerpt_fontsize' => [
                'label'       => 'Listen-Card: Teaser-Text Schriftgröße (px)',
                'description' => 'Schriftgröße des Auszug-Textes in der Artikel-Liste (Startseite & Archiv).',
                'type'        => 'number',
                'default'     => 13,
            ],
            'article_excerpt_length' => [
                'label'       => 'Listen-Card: Teaser-Text Länge (Zeichen)',
                'description' => 'Maximale Zeichenanzahl des Teaser-Textes in Artikel-Listcards.',
                'type'        => 'number',
                'default'     => 180,
            ],
            'tile_excerpt_fontsize' => [
                'label'       => 'Grid-Card: Teaser-Text Schriftgröße (px)',
                'description' => 'Schriftgröße des Auszug-Textes in den Kachel-Cards.',
                'type'        => 'number',
                'default'     => 12,
            ],
            'tile_excerpt_length' => [
                'label'       => 'Grid-Card: Teaser-Text Länge (Zeichen)',
                'description' => 'Maximale Zeichenanzahl des Teaser-Textes in Grid-Kachel-Cards.',
                'type'        => 'number',
                'default'     => 160,
            ],
        ],
    ],
    // ═══════════════════════════════════════════════════════════════════════
    'layout' => [
        'title' => '📐 Layout',
        'sections' => [
            'container_width' => [
                'label'       => 'Container-Breite (px)',
                'description' => 'Maximale Breite des Inhaltsbereichs.',
                'type'        => 'number',
                'default'     => 1060,
            ],
            'sidebar_width' => [
                'label'       => 'Sidebar-Breite (px)',
                'description' => 'Breite der Artikel-Sidebar (rechts, Desktop).',
                'type'        => 'number',
                'default'     => 280,
            ],
            'border_radius' => [
                'label'       => 'Eckenradius Standard (px)',
                'description' => 'Rundung für Cards, Felder und kleinere Elemente.',
                'type'        => 'number',
                'default'     => 4,
            ],
            'border_radius_md' => [
                'label'       => 'Eckenradius Mittel (px)',
                'description' => 'Rundung für Cards, Bilder und Widgets.',
                'type'        => 'number',
                'default'     => 6,
            ],
            'enable_sticky_header' => [
                'label'       => 'Sticky Header aktivieren',
                'description' => 'Header fixiert beim Scrollen oben (scrolled-Klasse).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'enable_progress_bar' => [
                'label'       => 'Lese-Fortschrittsbalken anzeigen',
                'description' => 'Schmaler Balken ganz oben zeigt Leseprogress im Artikel.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'enable_back_to_top' => [
                'label'       => '„Zurück nach oben"-Button anzeigen',
                'description' => 'Button erscheint nach 400px Scrollen unten rechts.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'enable_dark_mode_toggle' => [
                'label'       => 'Dark Mode Toggle anzeigen',
                'description' => 'Schalter (☀/🌙) im Header für Besucher.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'enable_scroll_animations' => [
                'label'       => 'Scroll-Animationen aktivieren',
                'description' => 'Elemente mit [data-anim] faden beim Einrollen ein.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_position' => [
                'label'       => 'Sidebar-Position (Artikel)',
                'description' => 'Gibt an, auf welcher Seite die Sidebar beim Artikel liegt.',
                'type'        => 'select',
                'options'     => [
                    'right' => 'Rechts (Standard)',
                    'left'  => 'Links',
                    'none'  => 'Keine Sidebar (Standard: post-wide)',
                ],
                'default'     => 'right',
            ],
            'spacing_header_content' => [
                'label'       => 'Abstand Header → Content (px)',
                'description' => 'Vertikaler Abstand zwischen Header und dem Seiteninhalt.',
                'type'        => 'number',
                'default'     => 25,
            ],
            'spacing_content_footer' => [
                'label'       => 'Abstand Content → Footer (px)',
                'description' => 'Vertikaler Abstand zwischen dem Seiteninhalt und dem Footer.',
                'type'        => 'number',
                'default'     => 25,
            ],
            'content_gap' => [
                'label'       => 'Grid-Gap Content/Sidebar (px)',
                'description' => 'Horizontaler Abstand zwischen Hauptinhalt und Sidebar.',
                'type'        => 'number',
                'default'     => 28,
            ],
            'spacing_sections' => [
                'label'       => 'Abstand Sektionen untereinander (px)',
                'description' => 'Vertikaler Abstand zwischen den einzelnen Homepage-Sektionen (Repo-Card, Artikelliste, Kacheln, RSS …).',
                'type'        => 'number',
                'default'     => 40,
            ],

            // ── Breadcrumb ──
            'show_breadcrumb' => [
                'label'       => 'Breadcrumb anzeigen',
                'description' => 'Zeigt den Breadcrumb-Navigationspfad direkt unterhalb des Headers.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'breadcrumb_on_posts' => [
                'label'       => 'Breadcrumb auf Beitrags-Seiten',
                'description' => 'Breadcrumb auf einzelnen Blog-Posts anzeigen.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'breadcrumb_on_pages' => [
                'label'       => 'Breadcrumb auf Seiten (Pages)',
                'description' => 'Breadcrumb auf statischen Seiten anzeigen.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // HEADER & NAVIGATION
    // ═══════════════════════════════════════════════════════════════════════
    'header' => [
        'title' => '🖥️ Header & Navigation',
        'sections' => [

            // ── Logo & Marke ──
            'logo_text_part1' => [
                'label'       => 'Logo-Text (Teil 1)',
                'description' => 'Erster Teil des Text-Logos (z. B. "365" oder "PHINIT").',
                'type'        => 'text',
                'default'     => '365',
            ],
            'logo_text_part2' => [
                'label'       => 'Logo-Text (Teil 2, farbig)',
                'description' => 'Zweiter Teil des Logos – wird farbig hervorgehoben.',
                'type'        => 'text',
                'default'     => 'CMS',
            ],
            'logo_text_suffix' => [
                'label'       => 'Logo-Text Suffix',
                'description' => 'Optional: Suffix hinter dem Logo (z. B. ".DE").',
                'type'        => 'text',
                'default'     => '.DE',
            ],
            'logo_url' => [
                'label'       => 'Logo-Bild URL',
                'description' => 'Bild-Logo (PNG/SVG). Leer = Text-Logo.',
                'type'        => 'text',
                'default'     => '',
            ],
            'show_logo_text_with_image' => [
                'label'       => 'Logo-Text neben Bild anzeigen',
                'description' => 'Wenn ein Bild-Logo hochgeladen ist, den Text trotzdem rechts daneben anzeigen.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'logo_max_height' => [
                'label'       => 'Logo-Maximalhöhe (px)',
                'description' => 'Maximale Höhe des Bild-Logos.',
                'type'        => 'number',
                'default'     => 28,
            ],
            'logo_accent_color' => [
                'label'       => 'Logo Akzent-Farbe',
                'description' => 'Farbe des farbigen Logo-Teils (Teil 2).',
                'type'        => 'color',
                'default'     => '#4a9eff',
            ],

            // ── Bar 1 (Member-Bar) ──
            'show_member_bar' => [
                'label'       => 'Member-Bar anzeigen',
                'description' => 'Zeigt eine persönliche Member-Leiste (Dashboard, Profil, Nachrichten …) für eingeloggte Benutzer.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'member_bar_height' => [
                'label'       => 'Member-Bar Höhe (px)',
                'description' => 'Höhe der obersten Header-Bar für eingeloggte Benutzer.',
                'type'        => 'number',
                'default'     => 36,
            ],

            // ── Bar 2 (Haupt-Navigation) ──
            'main_nav_height' => [
                'label'       => 'Hauptnavigation Höhe (px)',
                'description' => 'Höhe der mittleren Header-Bar mit Hauptmenü.',
                'type'        => 'number',
                'default'     => 48,
            ],
            'show_search_bar' => [
                'label'       => 'Suchleiste im Header anzeigen',
                'description' => 'Zeigt das Suchfeld rechts in der Hauptnavigation.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'search_placeholder' => [
                'label'       => 'Suchfeld Platzhaltertext',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Artikel suchen…',
            ],
            'show_rss_link' => [
                'label'       => 'RSS-Link im Header anzeigen',
                'description' => 'Zeigt den RSS-Feed-Link im Header.',
                'type'        => 'checkbox',
                'default'     => true,
            ],

            // ── Bar 3 (Quicklinks) ──
            'show_quicklinks' => [
                'label'       => 'Quicklinks-Bar anzeigen',
                'description' => 'Zeigt die untere Subnavigation (z. B. Entra ID, Intune, …).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sub_bar_height' => [
                'label'       => 'Quicklinks-Bar Höhe (px)',
                'description' => 'Höhe der Quicklinks-Subnavigation.',
                'type'        => 'number',
                'default'     => 30,
            ],


        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // FOOTER
    // ═══════════════════════════════════════════════════════════════════════
    'footer' => [
        'title' => '🔻 Footer',
        'sections' => [
            'footer_brand_name' => [
                'label'       => 'Markenname im Footer',
                'description' => 'Seitenname im Footer-Logo (kann Platzhalter {year} nutzen).',
                'type'        => 'text',
                'default'     => '365CMS.DE',
            ],
            'footer_tagline' => [
                'label'       => 'Footer-Beschreibungstext',
                'description' => 'Kurzer Text unter dem Footer-Logo.',
                'type'        => 'textarea',
                'default'     => 'Professionelle Microsoft 365 Administration und Cloud-Security. Von einem erfahrenen IT-Profi für IT-Profis.',
            ],
            'footer_col2_title' => [
                'label'       => 'Spalte 2 Titel',
                'description' => 'Überschrift der zweiten Footer-Spalte.',
                'type'        => 'text',
                'default'     => 'Themen',
            ],
            'footer_col3_title' => [
                'label'       => 'Spalte 3 Titel',
                'description' => 'Überschrift der dritten Footer-Spalte.',
                'type'        => 'text',
                'default'     => 'Ressourcen',
            ],
            'footer_col4_title' => [
                'label'       => 'Spalte 4 Titel',
                'description' => 'Überschrift der vierten Footer-Spalte.',
                'type'        => 'text',
                'default'     => 'Rechtliches',
            ],
            'copyright_text' => [
                'label'       => 'Copyright-Text',
                'description' => 'Copyright-Zeile unten. Platzhalter: {year}, {site_title}.',
                'type'        => 'text',
                'default'     => '© {year} {site_title} – Powered by 365CMS – Cloud Intelligence for Professionals',
            ],
            'show_footer_social' => [
                'label'       => 'Social Icons im Footer anzeigen',
                'description' => 'Zeigt Social-Media-Icons unten links im Footer.',
                'type'        => 'checkbox',
                'default'     => true,
            ],

            // ── Cookie-Consent-Banner ──
            'show_consent_banner' => [
                'label'       => 'Cookie-Consent-Banner anzeigen',
                'description' => 'Zeigt den DSGVO-Einwilligungsbanner am unteren Seitenrand.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'consent_text' => [
                'label'       => 'Consent-Banner-Text',
                'description' => 'Text des Cookie-/Datenschutz-Banners. Platzhalter: {privacy_url} für den Link zur Datenschutzseite.',
                'type'        => 'textarea',
                'default'     => 'Diese Website verwendet Cookies für Analyse-Zwecke.',
            ],
            'consent_privacy_url' => [
                'label'       => 'Datenschutz-Link-URL im Banner',
                'description' => 'URL zur „Mehr erfahren"-Seite im Consent-Banner.',
                'type'        => 'text',
                'default'     => '/cookie-policy',
            ],

            // ── Partner/Network Bar ──
            'show_network_bar' => [
                'label'       => 'Partner/Network-Bar anzeigen',
                'description' => 'Zeigt die Partnernetzwerk-Leiste unter dem Footer.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'network_bar_link1_label' => [
                'label'       => '🔗 Network-Bar Link 1 – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'phinit.de',
            ],
            'network_bar_link1_url' => [
                'label'       => 'Network-Bar Link 1 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://phinit.de',
            ],
            'network_bar_link2_label' => [
                'label'       => '🔗 Network-Bar Link 2 – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'phscripts.de',
            ],
            'network_bar_link2_url' => [
                'label'       => 'Network-Bar Link 2 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://phscripts.de',
            ],
            'network_bar_link3_label' => [
                'label'       => '🔗 Network-Bar Link 3 – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => '365network.de',
            ],
            'network_bar_link3_url' => [
                'label'       => 'Network-Bar Link 3 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://365network.de',
            ],
            'network_bar_link4_label' => [
                'label'       => '🔗 Network-Bar Link 4 – Label',
                'description' => 'Leer = nicht angezeigt.',
                'type'        => 'text',
                'default'     => 'ms365insights.de',
            ],
            'network_bar_link4_url' => [
                'label'       => 'Network-Bar Link 4 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://ms365insights.de',
            ],
            'network_bar_link5_label' => [
                'label'       => '🔗 Network-Bar Link 5 – Label',
                'description' => 'Leer = nicht angezeigt.',
                'type'        => 'text',
                'default'     => '',
            ],
            'network_bar_link5_url' => [
                'label'       => 'Network-Bar Link 5 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // STARTSEITE
    // ═══════════════════════════════════════════════════════════════════════
    'homepage' => [
        'title' => '🏠 Startseite',
        'sections' => [

            // ── GitHub / Repo-Card ──
            'show_repo_card' => [
                'label'       => 'Repo-Card anzeigen',
                'description' => 'Hebt GitHub-Repository oder Hauptprojekt prominent hervor.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'repo_card_title' => [
                'label'       => 'Repo-Card Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'PS-easyIT Script-Repository',
            ],
            'repo_card_description' => [
                'label'       => 'Repo-Card Beschreibung',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '25+ Code-signierte PowerShell-Module für M365 Administration. Enterprise-ready & Open Source.',
            ],
            'repo_card_badge' => [
                'label'       => 'Repo-Card Badge-Text',
                'description' => 'Kleiner farbiger Badge rechts (z. B. "25+ Repos").',
                'type'        => 'text',
                'default'     => '25+ Repos',
            ],
            'repo_card_btn_text' => [
                'label'       => 'Repo-Card Button Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Zum GitHub →',
            ],
            'repo_card_btn_url' => [
                'label'       => 'Repo-Card Button URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://github.com/phinit/',
            ],

            // ── Artikel-Liste ──
            'show_article_list' => [
                'label'       => 'Aktuelle Artikel-Liste anzeigen',
                'description' => 'Horizontale Artikel-Karten als Hauptinhalt.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'article_list_label' => [
                'label'       => 'Artikel-Liste Sektion-Label',
                'description' => 'Kleines Label über der Artikel-Liste.',
                'type'        => 'text',
                'default'     => 'Aktuell',
            ],
            'article_list_count' => [
                'label'       => 'Anzahl Artikel in der Hauptliste',
                'description' => 'Wie viele Artikel in der horizontalen Liste angezeigt werden.',
                'type'        => 'number',
                'default'     => 4,
            ],
            'article_thumb_width' => [
                'label'       => 'Artikel-Thumbnail Breite (px)',
                'description' => 'Breite der Vorschaubilder in der Artikelliste.',
                'type'        => 'number',
                'default'     => 190,
            ],
            'article_thumb_height' => [
                'label'       => 'Artikel-Thumbnail Höhe (px)',
                'description' => 'Höhe der Vorschaubilder in der Artikelliste.',
                'type'        => 'number',
                'default'     => 115,
            ],
            'show_article_excerpt' => [
                'label'       => 'Artikel-Auszug anzeigen',
                'description' => 'Zeigt eine kurze Zusammenfassung unter dem Titel.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_article_meta' => [
                'label'       => 'Artikel-Meta anzeigen',
                'description' => 'Zeigt Kategorie, Datum und Lesezeit in der Liste.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_article_badge' => [
                'label'       => 'Kategorie-Badge auf Thumbnail',
                'description' => 'Kleines farbiges Badge mit Kategoriename oben links.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_meta_category' => [
                'label'       => 'Meta: Kategorie anzeigen',
                'description' => 'Kategorie-Name in der Metazeile der Artikel-Liste.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_meta_date' => [
                'label'       => 'Meta: Datum anzeigen',
                'description' => 'Veröffentlichungsdatum in der Metazeile.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_meta_readtime' => [
                'label'       => 'Meta: Lesezeit anzeigen',
                'description' => 'Geschätzte Lesezeit (z. B. „5 Min.“) in der Metazeile.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'article_list_link_url' => [
                'label'       => 'Artikel-Liste – „Alle Beiträge“ URL',
                'description' => 'Ziel des „Alle Beiträge →“-Links rechts im Sektions-Header.',
                'type'        => 'text',
                'default'     => '/blog',
            ],
            // ── Sidebar neben Artikel-Liste ──
            'show_list_sidebar' => [
                'label'       => 'Sidebar neben „Aktuell“ anzeigen',
                'description' => 'Zeigt eine konfigurierbare Sidebar rechts neben der Artikel-Liste an.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'list_sidebar_width' => [
                'label'       => 'Sidebar Breite (px)',
                'description' => 'Breite der Sidebar neben der Artikel-Liste.',
                'type'        => 'number',
                'default'     => 260,
            ],
            'list_sidebar_title' => [
                'label'       => 'Sidebar Titel',
                'description' => 'Optional: Überschrift der Sidebar.',
                'type'        => 'text',
                'default'     => '',
            ],
            'list_sidebar_content' => [
                'label'       => 'Sidebar Inhalt (HTML erlaubt)',
                'description' => 'Freier HTML-Inhalt für die Sidebar, z. B. Links, Hinweise, Bilder.',
                'type'        => 'textarea',
                'default'     => '',
            ],

            // ── Sidebar Widgets ────────────────────────────────────────────
            'sidebar_show_projects' => [
                'label'       => 'Widget: Projekt-Hinweise anzeigen',
                'description' => 'Zeigt Links zu eigenen Projekten (z. B. 365CMS.DE und 365NETWORK.DE).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_project1_name' => [
                'label'       => 'Projekt 1 – Name',
                'description' => '',
                'type'        => 'text',
                'default'     => '365CMS.DE',
            ],
            'sidebar_project1_desc' => [
                'label'       => 'Projekt 1 – Kurzbeschreibung',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Das eigene CMS – modular & flexibel',
            ],
            'sidebar_project1_url' => [
                'label'       => 'Projekt 1 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://365cms.de',
            ],
            'sidebar_project2_name' => [
                'label'       => 'Projekt 2 – Name',
                'description' => '',
                'type'        => 'text',
                'default'     => '365NETWORK.DE',
            ],
            'sidebar_project2_desc' => [
                'label'       => 'Projekt 2 – Kurzbeschreibung',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Business-Netzwerk-Plattform',
            ],
            'sidebar_project2_url' => [
                'label'       => 'Projekt 2 – URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://365network.de',
            ],
            'sidebar_show_status' => [
                'label'       => 'Widget: Dienst-Status anzeigen',
                'description' => 'Platzhalter – wird spaeter via Plugin (cms-status) befuellt.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_status_label' => [
                'label'       => 'Dienst-Status Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Dienst-Status',
            ],
            'sidebar_show_downloads' => [
                'label'       => 'Widget: Download-Bereich anzeigen',
                'description' => 'Zeigt eine Liste konfigurierbarer Download-Links.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'sidebar_downloads_label' => [
                'label'       => 'Downloads – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Downloads & Checklisten',
            ],
            'sidebar_downloads_items' => [
                'label'       => 'Download-Links (ein Eintrag pro Zeile)',
                'description' => 'Format pro Zeile: Bezeichnung|https://url.de/datei.pdf',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'sidebar_show_social' => [
                'label'       => 'Widget: Social-Media-Links anzeigen',
                'description' => 'URLs werden aus dem Social-Tab uebernommen.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_social_label' => [
                'label'       => 'Social Media – Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Folge uns',
            ],
            // ── Widget: Site-Identity (Logo oben) ────────────────────────
            'sidebar_show_identity' => [
                'label'       => 'Widget: Site-Identity (Logo) oben anzeigen',
                'description' => 'Zeigt ein Logo + Tagline ganz oben in der Sidebar.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_identity_logo_url' => [
                'label'       => 'Identity – Logo-URL',
                'description' => 'Pfad oder URL zum Logo-Bild, z. B. /assets/images/logo.png',
                'type'        => 'text',
                'default'     => '',
            ],
            'sidebar_identity_tagline' => [
                'label'       => 'Identity – Tagline',
                'description' => 'Kurzer Satz unter dem Logo.',
                'type'        => 'text',
                'default'     => '',
            ],
            'sidebar_identity_link_url' => [
                'label'       => 'Identity – Link (Klick auf Logo)',
                'description' => '',
                'type'        => 'text',
                'default'     => '/',
            ],
            // ── Widget: Projekt-Logos ─────────────────────────────────────
            'sidebar_project1_logo_url' => [
                'label'       => 'Projekt 1 – Logo-URL',
                'description' => 'URL oder Pfad zum Logo-Bild des ersten Projekts.',
                'type'        => 'text',
                'default'     => '',
            ],
            'sidebar_project2_logo_url' => [
                'label'       => 'Projekt 2 – Logo-URL',
                'description' => 'URL oder Pfad zum Logo-Bild des zweiten Projekts.',
                'type'        => 'text',
                'default'     => '',
            ],
            // ── Widget: Status-Dienste ────────────────────────────────────
            'sidebar_status_services' => [
                'label'       => 'Dienst-Status – Dienste (ein Eintrag pro Zeile)',
                'description' => 'Format: Dienstname|Status-URL|Kürzel (max. 4 Zeichen)',
                'type'        => 'textarea',
                'default'     => "Microsoft 365|https://status.office365.com|M365\nAzure|https://status.azure.com|AZ\nStarface|https://www.starface.com/support/|SF\nAnyDesk|https://status.anydesk.com|AD\nGitHub|https://githubstatus.com|GH\nCloudflare|https://www.cloudflarestatus.com|CF",
            ],
            // ── Widget: Ankündigung / Hinweis ──────────────────────────────
            'sidebar_show_notice' => [
                'label'       => 'Widget: Anküdigung/Hinweis anzeigen',
                'description' => 'Konfigurierbarer Banner, z. B. für Wartungsfenster oder Neuigkeiten.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'sidebar_notice_title' => [
                'label'       => 'Hinweis – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '💡 Aktueller Hinweis',
            ],
            'sidebar_notice_text' => [
                'label'       => 'Hinweis – Text',
                'description' => '',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'sidebar_notice_url' => [
                'label'       => 'Hinweis – Link URL (optional)',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'sidebar_notice_url_text' => [
                'label'       => 'Hinweis – Link Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Mehr erfahren →',
            ],
            // ── Widget: Empfohlene Artikel (Featured Posts) ─────────────────
            'sidebar_show_featured_posts' => [
                'label'       => 'Widget: Empfohlene Artikel anzeigen',
                'description' => 'Zeigt bis zu 3 ausgewählte Beiträge in der Sidebar. Wird anstelle der anderen Widgets angezeigt, wenn aktiviert.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'sidebar_featured_posts_label' => [
                'label'       => 'Empfohlene Artikel – Label',
                'description' => 'Überschrift des Featured-Posts-Widgets.',
                'type'        => 'text',
                'default'     => '📌 Empfohlene Artikel',
            ],
            'sidebar_featured_post_1' => [
                'label'       => 'Empfohlener Beitrag 1',
                'description' => 'Wähle den ersten anzuzeigenden Beitrag.',
                'type'        => 'post_picker',
                'default'     => '',
            ],
            'sidebar_featured_post_2' => [
                'label'       => 'Empfohlener Beitrag 2',
                'description' => 'Wähle den zweiten anzuzeigenden Beitrag.',
                'type'        => 'post_picker',
                'default'     => '',
            ],
            'sidebar_featured_post_3' => [
                'label'       => 'Empfohlener Beitrag 3',
                'description' => 'Wähle den dritten anzuzeigenden Beitrag.',
                'type'        => 'post_picker',
                'default'     => '',
            ],

            'show_info_grid' => [
                'label'       => 'Kategorie-Cards anzeigen',
                'description' => '2er-Grid mit Kategorie-Übersichts-Cards.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'info_card1_title' => [
                'label'       => 'Info-Card 1 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '🖥️ Admin Anleitungen',
            ],
            'info_card1_text' => [
                'label'       => 'Info-Card 1 – Text',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Schritt-für-Schritt-Tutorials für Microsoft 365 Administration.',
            ],
            'info_card1_link_text' => [
                'label'       => 'Info-Card 1 – Link-Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Alle Anleitungen ansehen →',
            ],
            'info_card1_link_url' => [
                'label'       => 'Info-Card 1 – Link URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '/kategorie/anleitungen',
            ],
            'info_card1_style' => [
                'label'       => 'Info-Card 1 – Stil',
                'description' => '',
                'type'        => 'select',
                'options'     => ['default' => 'Standard (Blau)', 'gold' => 'Gold-Akzent'],
                'default'     => 'default',
            ],
            'info_card2_title' => [
                'label'       => 'Info-Card 2 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => '🔒 DSGVO & Compliance',
            ],
            'info_card2_text' => [
                'label'       => 'Info-Card 2 – Text',
                'description' => '',
                'type'        => 'textarea',
                'default'     => 'Konfigurationsanleitungen und Best Practices für Microsoft Purview und Datenschutz.',
            ],
            'info_card2_link_text' => [
                'label'       => 'Info-Card 2 – Link-Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Compliance-Center →',
            ],
            'info_card2_link_url' => [
                'label'       => 'Info-Card 2 – Link URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '/kategorie/compliance',
            ],
            'info_card2_style' => [
                'label'       => 'Info-Card 2 – Stil',
                'description' => '',
                'type'        => 'select',
                'options'     => ['default' => 'Standard (Blau)', 'gold' => 'Gold-Akzent'],
                'default'     => 'gold',
            ],

            // ── Info-Card 3 (Repo / optional) ──
            'show_info_card3' => [
                'label'       => '3. Kategorie-Card anzeigen',
                'description' => 'Zeigt eine dritte Card neben den beiden bestehenden (z. B. GitHub/GitLab Repo-Card).',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'info_card3_title' => [
                'label'       => 'Info-Card 3 – Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Open Source',
            ],
            'info_card3_text' => [
                'label'       => 'Info-Card 3 – Beschreibung',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Meine Projekte auf GitHub.',
            ],
            'info_card3_link_text' => [
                'label'       => 'Info-Card 3 – Button-Text',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Zum Repository →',
            ],
            'info_card3_link_url' => [
                'label'       => 'Info-Card 3 – Link URL',
                'description' => '',
                'type'        => 'text',
                'default'     => 'https://github.com/',
            ],
            'info_card3_badge' => [
                'label'       => 'Info-Card 3 – Badge-Text',
                'description' => 'Kleines Badge oben links (z. B. "GitHub").',
                'type'        => 'text',
                'default'     => 'GitHub',
            ],
            'info_card3_style' => [
                'label'       => 'Info-Card 3 – Stil',
                'description' => '',
                'type'        => 'select',
                'options'     => ['default' => 'Standard (Blau)', 'gold' => 'Gold-Akzent', 'repo' => 'Repo (Dark)'],
                'default'     => 'repo',
            ],

            // ── 3er-Grid (Deep-Dive) ──
            'show_tile_grid' => [
                'label'       => 'Deep-Dive Archiv-Grid anzeigen',
                'description' => 'Zeigt das 3-spaltige Kachel-Grid mit älteren Artikeln.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'tile_grid_label' => [
                'label'       => 'Grid-Sektion Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Deep-Dive Archiv',
            ],
            'tile_grid_count' => [
                'label'       => 'Anzahl Kacheln im Grid',
                'description' => 'Wie viele Artikel im 3er-Kachel-Grid angezeigt werden.',
                'type'        => 'number',
                'default'     => 6,
            ],
            'tile_grid_columns' => [
                'label'       => 'Grid-Spaltenanzahl',
                'description' => '',
                'type'        => 'select',
                'options'     => ['2' => '2 Spalten', '3' => '3 Spalten (Standard)', '4' => '4 Spalten'],
                'default'     => '3',
            ],
            'show_tile_excerpt' => [
                'label'       => 'Grid: Auszug anzeigen',
                'description' => 'Kurze Beschreibung unter dem Titel in den Kacheln.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_tile_category' => [
                'label'       => 'Grid: Kategorie-Badge anzeigen',
                'description' => 'Kategorie-Badge über dem Titel in den Kacheln.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_tile_date' => [
                'label'       => 'Grid: Datum anzeigen',
                'description' => 'Veröffentlichungsdatum in der Kachel-Metazeile.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'tile_grid_link_url' => [
                'label'       => 'Grid – „Archiv“ URL',
                'description' => 'Ziel des „Archiv →“-Links im Grid-Header (leer = kein Link).',
                'type'        => 'text',
                'default'     => '/archiv',
            ],
            'show_feed_section' => [
                'label'       => 'RSS-Feed-Sektion anzeigen',
                'description' => 'Zeigt externe RSS-Feed-Blöcke (z. B. Borns Blog, Heise).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'feed1_channel_id' => [
                'label'       => '📡 Feed 1 – Kanal (cms-feed)',
                'description' => 'Wähle einen Feed-Kanal aus dem cms-feed Plugin. Kanäle werden dort unter Plugins › Feed-Manager verwaltet.',
                'type'        => 'select',
                'options'     => ['0' => '— Kein Feed —'],
                'default'     => '0',
            ],
            'feed1_count' => [
                'label'       => 'Feed 1 – Anzahl Einträge',
                'description' => '',
                'type'        => 'number',
                'default'     => 5,
            ],
            'feed2_channel_id' => [
                'label'       => '📡 Feed 2 – Kanal (cms-feed)',
                'description' => 'Wähle einen zweiten Feed-Kanal aus dem cms-feed Plugin.',
                'type'        => 'select',
                'options'     => ['0' => '— Kein Feed —'],
                'default'     => '0',
            ],
            'feed2_count' => [
                'label'       => 'Feed 2 – Anzahl Einträge',
                'description' => '',
                'type'        => 'number',
                'default'     => 5,
            ],

            // ── Sektionen-Abstände (individuell) ──
            'spacing_repo_card' => [
                'label'       => 'Abstand nach Repo-Card (px)',
                'description' => 'Margin-Bottom der Repo-Card Sektion.',
                'type'        => 'number',
                'default'     => 32,
            ],
            'spacing_article_list' => [
                'label'       => 'Abstand nach Artikel-Liste (px)',
                'description' => 'Margin-Bottom der Artikel-Listen-Sektion.',
                'type'        => 'number',
                'default'     => 32,
            ],
            'spacing_info_cards' => [
                'label'       => 'Abstand nach Kategorie-Cards (px)',
                'description' => 'Margin-Bottom der Kategorie-Cards Sektion.',
                'type'        => 'number',
                'default'     => 32,
            ],
            'spacing_tile_grid' => [
                'label'       => 'Abstand nach Kachel-Grid (px)',
                'description' => 'Margin-Bottom des Deep-Dive-Grids.',
                'type'        => 'number',
                'default'     => 32,
            ],
            'spacing_rss_feeds' => [
                'label'       => 'Abstand nach RSS-Feeds (px)',
                'description' => 'Margin-Bottom der RSS-Feed Sektion.',
                'type'        => 'number',
                'default'     => 0,
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // BEITRÄGE (POST)
    // ═══════════════════════════════════════════════════════════════════════
    'posts' => [
        'title' => '📝 Beiträge',
        'sections' => [

            // ── Hero / Kopfbereich ──
            'post_hero_height' => [
                'label'       => 'Post-Hero Höhe (px)',
                'description' => 'Höhe des großen Artikelheaders mit Bild und Titel.',
                'type'        => 'number',
                'default'     => 340,
            ],
            'show_post_hero' => [
                'label'       => 'Post-Hero-Bild anzeigen',
                'description' => 'Zeigt das Titelbild des Artikels als großen Hero-Banner.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_post_meta' => [
                'label'       => 'Artikel-Meta anzeigen',
                'description' => 'Zeigt Autor, Datum, Lesezeit und Kommentaranzahl unter dem Titel.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_reading_time' => [
                'label'       => 'Lesezeit anzeigen',
                'description' => 'Geschätzte Lesezeit im Artikelkopf.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'reading_time_wpm' => [
                'label'       => 'Wörter pro Minute (Lesezeit)',
                'description' => 'Basis für die Lesezeit-Berechnung (Standard: 220 Wörter/Min).',
                'type'        => 'number',
                'default'     => 220,
            ],

            // ── Inhaltsverzeichnis (TOC) ──
            'show_toc' => [
                'label'       => 'Inhaltsverzeichnis (TOC) anzeigen',
                'description' => 'Zeigt das Inhaltsverzeichnis in der Sidebar des Artikels.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'toc_sticky' => [
                'label'       => 'TOC sticky (haftet beim Scrollen)',
                'description' => 'Inhaltsverzeichnis folgt beim Scrollen nach unten.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'toc_min_headings' => [
                'label'       => 'TOC mind. X Überschriften',
                'description' => 'Erst ab dieser Anzahl von H2/H3 wird das TOC eingeblendet.',
                'type'        => 'number',
                'default'     => 2,
            ],
            'toc_header_text' => [
                'label'       => 'TOC Widget-Titel',
                'description' => 'Bezeichnung des Inhaltsverzeichnis-Widgets.',
                'type'        => 'text',
                'default'     => 'Inhaltsverzeichnis',
            ],

            // ── Sidebar-Widgets ──
            'show_sidebar_social' => [
                'label'       => 'Social-Widget in Sidebar anzeigen',
                'description' => 'Zeigt Social-Media-Buttons in der Artikel-Sidebar.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_social_header' => [
                'label'       => 'Social-Widget Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Folgen & Teilen',
            ],
            'show_sidebar_related' => [
                'label'       => 'Verwandte Artikel in Sidebar',
                'description' => 'Zeigt verwandte Artikel-Links in der Sidebar.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'sidebar_related_header' => [
                'label'       => 'Verwandte Artikel Widget-Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Verwandte Artikel',
            ],
            'related_count' => [
                'label'       => 'Anzahl verwandter Artikel',
                'description' => '',
                'type'        => 'number',
                'default'     => 4,
            ],

            // ── Share-Buttons ──
            'show_share_buttons' => [
                'label'       => 'Share-Buttons im Artikel anzeigen',
                'description' => 'Zeigt Link-Kopierer und Social-Share am Ende des Artikels.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_share_linkedin' => [
                'label'       => 'LinkedIn Share-Button',
                'description' => '',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_share_twitter' => [
                'label'       => 'Twitter/X Share-Button',
                'description' => '',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_share_email' => [
                'label'       => 'E-Mail Share-Button',
                'description' => '',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_share_copy' => [
                'label'       => 'Link-Kopierer anzeigen',
                'description' => 'Kopiert die Artikel-URL in die Zwischenablage.',
                'type'        => 'checkbox',
                'default'     => true,
            ],

            // ── Tech-Card (für post-tech Template) ──
            'show_tech_card' => [
                'label'       => 'Tech-Infocard (post-tech Template)',
                'description' => 'Zeigt die Tech-Sidebar mit OS, Version, Schwierigkeitsgrad.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'tech_card_header' => [
                'label'       => 'Tech-Card Widget-Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Tech-Details',
            ],

            // ── Kommentare ──
            'show_comments' => [
                'label'       => 'Kommentarbereich anzeigen',
                'description' => 'Zeigt das Kommentarformular und bestehende Kommentare.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'comments_header' => [
                'label'       => 'Kommentare Abschnittstitel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Kommentare',
            ],
            'comment_form_header' => [
                'label'       => 'Kommentarformular Titel',
                'description' => '',
                'type'        => 'text',
                'default'     => 'Kommentar hinterlassen',
            ],

            // ── Tags ──
            'show_post_tags' => [
                'label'       => 'Schlagwörter (Tags) anzeigen',
                'description' => 'Zeigt die Tags-Badges am Ende des Artikelinhalts.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // SEITEN (PAGES)
    // ═══════════════════════════════════════════════════════════════════════
    'pages' => [
        'title' => '📄 Seiten',
        'sections' => [
            'show_page_title' => [
                'label'       => 'Seiten-Titel anzeigen',
                'description' => 'Zeigt den Seitentitel (h1) oben auf der Seite.',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_page_updated_date' => [
                'label'       => 'Aktualisierungsdatum anzeigen',
                'description' => 'Zeigt das Datum der letzten Änderung auf statischen Seiten.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'page_layout' => [
                'label'       => 'Seiten-Layout',
                'description' => 'Standard-Layout für alle statischen Seiten.',
                'type'        => 'select',
                'options'     => ['full' => 'Volle Breite', 'narrow' => 'Schmal (860 px, zentriert)', 'two-col' => 'Zweispaltig (Inhalt + Sidebar)'],
                'default'     => 'full',
            ],
            'show_page_sidebar' => [
                'label'       => 'Sidebar auf Seiten anzeigen',
                'description' => 'Gilt nur für Layout „Zweispaltig".',
                'type'        => 'checkbox',
                'default'     => false,
            ],
            'page_sidebar_show_nav' => [
                'label'       => 'Sidebar: Navigations-Widget',
                'description' => 'Zeigt eine Seitennavigation in der Sidebar (Primary-Menü).',
                'type'        => 'checkbox',
                'default'     => true,
            ],
            'show_page_toc' => [
                'label'       => 'Inhaltsverzeichnis auf Seiten',
                'description' => 'Automatisch generiertes TOC aus H2/H3-Überschriften auf statischen Seiten.',
                'type'        => 'checkbox',
                'default'     => false,
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // SOCIAL MEDIA
    // ═══════════════════════════════════════════════════════════════════════
    'social' => [
        'title' => '🌐 Social Media',
        'sections' => [
            'social_linkedin' => [
                'label'       => 'LinkedIn URL',
                'description' => 'Vollständige LinkedIn-Profil-URL (leer = ausgeblendet).',
                'type'        => 'text',
                'default'     => 'https://www.linkedin.com/in/andreashepp/',
            ],
            'social_github' => [
                'label'       => 'GitHub URL',
                'description' => 'Vollständige GitHub-Profil oder Organisations-URL.',
                'type'        => 'text',
                'default'     => 'https://github.com/phinit/',
            ],
            'social_twitter' => [
                'label'       => 'Twitter/X URL',
                'description' => 'Vollständige Twitter/X-Profil-URL.',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_mastodon' => [
                'label'       => 'Mastodon URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_rss' => [
                'label'       => 'RSS-Feed URL',
                'description' => 'URL des RSS-Feeds dieses Blogs.',
                'type'        => 'text',
                'default'     => '/feed.xml',
            ],
            'social_youtube' => [
                'label'       => 'YouTube URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_xing' => [
                'label'       => 'Xing URL',
                'description' => '',
                'type'        => 'text',
                'default'     => '',
            ],
            'social_label_linkedin' => [
                'label'       => 'LinkedIn Button-Label',
                'description' => 'Text-Label im Social-Widget der Sidebar.',
                'type'        => 'text',
                'default'     => 'LinkedIn',
            ],
            'social_label_github' => [
                'label'       => 'GitHub Button-Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'GitHub',
            ],
            'social_label_rss' => [
                'label'       => 'RSS Button-Label',
                'description' => '',
                'type'        => 'text',
                'default'     => 'RSS Feed',
            ],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // ERWEITERT
    // ═══════════════════════════════════════════════════════════════════════
    'advanced' => [
        'title' => '🔧 Erweitert',
        'sections' => [
            'custom_css' => [
                'label'       => 'Eigenes CSS',
                'description' => 'Zusätzliches CSS, das ans Ende der Theme-Styles angehängt wird. Überschreibt alle Theme-Styles.',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'custom_head_code' => [
                'label'       => 'Custom Head Code',
                'description' => 'Wird im &lt;head&gt; ausgegeben (Tracking-Pixel, Fonts, Meta-Tags). Nur vertrauenswürdigen Code einfügen!',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'custom_footer_code' => [
                'label'       => 'Custom Footer Code',
                'description' => 'Wird vor &lt;/body&gt; ausgegeben (Analytics, Widget-Scripts).',
                'type'        => 'textarea',
                'default'     => '',
            ],
            'google_analytics_id' => [
                'label'       => 'Google Analytics Measurement-ID',
                'description' => 'Format: G-XXXXXXXXXX. Leer = kein Tracking.',
                'type'        => 'text',
                'default'     => '',
            ],
            'cache_buster_css' => [
                'label'       => 'CSS Cache-Buster Version',
                'description' => 'Manueller Versionswert für das CSS (Standard: Datei-Timestamp).',
                'type'        => 'text',
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

// cms-feed Kanal-Optionen dynamisch laden
$_cmsFeedChannelOptions = ['0' => '— Kein Feed —'];
if (class_exists('CMS_Feed_Database')) {
    try {
        foreach (CMS_Feed_Database::instance()->get_channels(0) as $_ch) {
            if (!empty($_ch['is_active'])) {
                $_cmsFeedChannelOptions[(string)$_ch['id']] = htmlspecialchars(
                    $_ch['name'] . (isset($_ch['category_name']) ? ' (' . $_ch['category_name'] . ')' : ''),
                    ENT_QUOTES
                );
            }
        }
    } catch (\Throwable $_e) {}
}
$config['homepage']['sections']['feed1_channel_id']['options'] = $_cmsFeedChannelOptions;
$config['homepage']['sections']['feed2_channel_id']['options'] = $_cmsFeedChannelOptions;

$activeTab = $_GET['tab'] ?? 'colors';
if (!isset($config[$activeTab])) {
    $activeTab = 'colors';
}

// ── 3. Speichern & Zurücksetzen ──────────────────────────────────────────────
$success = null;
$error   = null;

// -- Reset --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset_theme_tab') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'phinit_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        $resetTab = $_POST['active_section'] ?? $activeTab;
        if (!isset($config[$resetTab])) {
            $resetTab = $activeTab;
        }
        $resetFailed = false;
        foreach ($config[$resetTab]['sections'] as $fieldKey => $fieldConfig) {
            $def = $fieldConfig['default'] ?? '';
            if (is_bool($def)) {
                $def = $def ? '1' : '0';
            }
            if (!$customizer->set($resetTab, $fieldKey, (string)$def)) {
                $resetFailed = true;
            }
        }
        if ($resetFailed) {
            $error = 'Einige Einstellungen konnten nicht zurückgesetzt werden.';
        } else {
            $success = 'Einstellungen für &bdquo;' . htmlspecialchars($config[$resetTab]['title']) . '&ldquo; auf Standardwerte zurückgesetzt.';
        }
    }
}

// -- Save --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_theme_options') {
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'phinit_customizer')) {
        $error = 'Sicherheitscheck fehlgeschlagen. Bitte erneut versuchen.';
    } else {
        $savedTab = $_POST['active_section'] ?? $activeTab;
        if (!isset($config[$savedTab])) {
            $savedTab = $activeTab;
        }
        $saveFailed = false;
        foreach ($config[$savedTab]['sections'] as $fieldKey => $fieldConfig) {
            $inputName = "{$savedTab}_{$fieldKey}";
            if ($fieldConfig['type'] === 'checkbox') {
                $val = isset($_POST[$inputName]) ? '1' : '0';
            } else {
                $val = $_POST[$inputName] ?? '';
                // Textarea: nur für trusted-Felder (advanced) kein strip_tags
                if ($fieldConfig['type'] === 'textarea' && !in_array($savedTab, ['advanced'], true)) {
                    $val = strip_tags((string)$val);
                }
            }
            if (!$customizer->set($savedTab, $fieldKey, (string)$val)) {
                $saveFailed = true;
            }
        }
        if ($saveFailed) {
            $error = 'Einige Einstellungen konnten nicht gespeichert werden. Bitte Fehler-Log prüfen.';
        } else {
            $success = 'Einstellungen für &bdquo;' . htmlspecialchars($config[$savedTab]['title'] ?? $savedTab) . '&ldquo; gespeichert.';
        }
    }
}

// CSRF-Token NACH den POST-Handlern generieren (verhindert Token-Überschreibung vor Prüfung)
$csrfToken = Security::instance()->generateToken('phinit_customizer');

// ── Helper: Einzelfeld rendern ───────────────────────────────────────────────
function phinit_render_field(string $tab, string $fieldKey, array $field, mixed $val): void
{
    $inputId   = "field_{$tab}_{$fieldKey}";
    $inputName = "{$tab}_{$fieldKey}";
    $val       = (string)$val;
    ?>
    <div class="form-group" style="margin-bottom:1.1rem;">
        <label for="<?php echo $inputId; ?>" class="form-label">
            <?php echo htmlspecialchars($field['label']); ?>
        </label>

        <?php if ($field['type'] === 'color'): ?>
            <div style="display:flex;align-items:center;gap:10px;margin-top:4px;">
                <input type="color"
                       id="<?php echo $inputId; ?>"
                       name="<?php echo $inputName; ?>"
                       value="<?php echo htmlspecialchars($val ?: '#000000'); ?>"
                       style="height:38px;width:52px;padding:2px;border:1px solid #dde3ea;border-radius:4px;cursor:pointer;"
                       oninput="document.getElementById('<?php echo $inputId; ?>_text').value=this.value;">
                <input type="text"
                       id="<?php echo $inputId; ?>_text"
                       value="<?php echo htmlspecialchars($val); ?>"
                       class="form-control"
                       style="width:130px;font-family:monospace;"
                       oninput="document.getElementById('<?php echo $inputId; ?>').value=this.value;"
                       onchange="document.getElementById('<?php echo $inputName; ?>_hidden').value=this.value;"
                       name="">
                <input type="hidden" id="<?php echo $inputName; ?>_hidden" name="<?php echo $inputName; ?>" value="<?php echo htmlspecialchars($val); ?>">
            </div>

        <?php elseif ($field['type'] === 'checkbox'): ?>
            <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;">
                <input type="checkbox"
                       id="<?php echo $inputId; ?>"
                       name="<?php echo $inputName; ?>"
                       value="1"
                       <?php echo $val && $val !== '0' ? 'checked' : ''; ?>>
                <label for="<?php echo $inputId; ?>" style="cursor:pointer;font-weight:400;">Aktivieren</label>
            </div>

        <?php elseif ($field['type'] === 'select'): ?>
            <select id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>" class="form-control" style="margin-top:4px;">
                <?php foreach ($field['options'] as $optVal => $optLabel): ?>
                <option value="<?php echo htmlspecialchars((string)$optVal); ?>"
                    <?php echo $val === (string)$optVal ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($optLabel); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($field['type'] === 'textarea'): ?>
            <textarea id="<?php echo $inputId; ?>"
                      name="<?php echo $inputName; ?>"
                      class="form-control"
                      rows="3"
                      style="margin-top:4px;"><?php echo htmlspecialchars($val); ?></textarea>

        <?php elseif ($field['type'] === 'number'): ?>
            <input type="number"
                   id="<?php echo $inputId; ?>"
                   name="<?php echo $inputName; ?>"
                   value="<?php echo htmlspecialchars($val); ?>"
                   class="form-control"
                   step="<?php echo $field['step'] ?? 'any'; ?>"
                   <?php echo isset($field['min']) ? 'min="' . $field['min'] . '"' : ''; ?>
                   <?php echo isset($field['max']) ? 'max="' . $field['max'] . '"' : ''; ?>
                   style="max-width:140px;margin-top:4px;">

        <?php elseif ($field['type'] === 'post_picker'): ?>
            <?php
            $pickerPosts = [];
            try {
                $_ppDb     = \CMS\Database::instance();
                $_ppPrefix = $_ppDb->getPrefix();
                $_ppRows   = $_ppDb->get_results(
                    "SELECT id, title FROM {$_ppPrefix}posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 300"
                );
                $pickerPosts = array_map(fn($r) => (array)$r, $_ppRows ?: []);
            } catch (\Throwable $_ppe) {}
            ?>
            <select id="<?php echo $inputId; ?>" name="<?php echo $inputName; ?>" class="form-control" style="margin-top:4px;">
                <option value="">— Kein Beitrag ausgewählt —</option>
                <?php foreach ($pickerPosts as $_ppPost): ?>
                <option value="<?php echo (int)$_ppPost['id']; ?>"
                    <?php echo (string)$val === (string)$_ppPost['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($_ppPost['title']); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php else: ?>
            <input type="text"
                   id="<?php echo $inputId; ?>"
                   name="<?php echo $inputName; ?>"
                   value="<?php echo htmlspecialchars($val); ?>"
                   class="form-control"
                   style="margin-top:4px;">
        <?php endif; ?>

        <?php if (!empty($field['description'])): ?>
            <small class="form-text"><?php echo htmlspecialchars($field['description']); ?></small>
        <?php endif; ?>
    </div>
    <?php
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Phinit Customizer – <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : '365CMS'); ?></title>
    <link rel="stylesheet" href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo defined('SITE_URL') ? SITE_URL : ''; ?>/assets/css/admin.css?v=20260401">
    <?php if (function_exists('renderAdminSidebarStyles')) { renderAdminSidebarStyles(); } ?>
    <style>
        /* ── Customizer Layout ─────────────────── */
        .customizer-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        .customizer-nav {
            background: var(--card-bg, #fff);
            border: var(--card-border, 1px solid #e2e8f0);
            border-radius: var(--card-radius, 10px);
            padding: .5rem 0;
            position: sticky;
            top: 1rem;
        }
        .customizer-nav a {
            display: flex;
            align-items: center;
            padding: .6rem 1rem;
            font-size: .875rem;
            color: #475569;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .customizer-nav a:hover {
            background: #f8fafc;
            color: var(--admin-primary, #3b82f6);
        }
        .customizer-nav a.active {
            background: var(--admin-primary-light, #eff6ff);
            color: var(--admin-primary, #3b82f6);
            border-left-color: var(--admin-primary, #3b82f6);
            font-weight: 600;
        }
        .customizer-nav a.nav-sub {
            padding-left: 1.5rem;
            font-size: .82rem;
        }
        .customizer-nav-group {
            padding: .5rem 1rem .25rem;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            margin-top: .25rem;
        }
        .customizer-content { min-width: 0; }

        /* ── Farb-Grid ─────────────────────────── */
        .color-cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        .color-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.1rem;
        }
        .color-card h4 {
            font-size: .82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #475569;
            margin-bottom: .8rem;
            padding-bottom: .4rem;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ── Section-Grid ──────────────────────── */
        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
        .field-group-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.1rem;
        }
        .field-group-card h4 {
            font-size: .82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #475569;
            margin-bottom: .8rem;
            padding-bottom: .4rem;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ── Form-Actions ──────────────────────── */
        .customizer-actions {
            display: flex;
            align-items: center;
            gap: .75rem;
            background: var(--card-bg, #fff);
            border: var(--card-border, 1px solid #e2e8f0);
            border-radius: var(--card-radius, 10px);
            padding: 1.1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        .customizer-actions span {
            font-size: .85rem;
            color: #64748b;
            margin-left: auto;
        }

        @media (max-width: 960px) {
            .customizer-layout { grid-template-columns: 1fr; }
            .customizer-nav {
                position: static;
                display: flex;
                flex-wrap: wrap;
                padding: .25rem;
            }
            .customizer-nav a { border-left: none; border-bottom: 2px solid transparent; padding: .5rem .75rem; font-size: .78rem; }
            .customizer-nav a.active { border-bottom-color: var(--admin-primary, #3b82f6); border-left: none; }
            .customizer-nav-group { display: none; }
            .color-cards-grid { grid-template-columns: 1fr; }
            .field-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="admin-body">

    <?php if (function_exists('renderAdminSidebar')) { renderAdminSidebar('theme-customizer'); } ?>

    <div class="admin-content">

        <div class="admin-page-header">
            <div>
                <h2>🎨 Theme Customizer – CMS Phinit</h2>
                <p>Passe Farben, Typografie, Header, Footer und alle Seitenbereiche individuell an.</p>
            </div>
            <div class="header-actions">
                <a href="<?php echo defined('SITE_URL') ? htmlspecialchars(SITE_URL) : '/'; ?>/" target="_blank" class="btn btn-secondary">🌐 Seite ansehen</a>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST"
              action="?tab=<?php echo htmlspecialchars($activeTab); ?>"
              id="customizer-form">
            <input type="hidden" name="action" value="save_theme_options">
            <input type="hidden" name="active_section" value="<?php echo htmlspecialchars($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <!-- Save-Bar -->
            <div class="customizer-actions">
                <button type="submit" name="action" value="save_theme_options" class="btn btn-primary">
                    💾 Einstellungen speichern
                </button>
                <button type="submit" name="action" value="reset_theme_tab"
                        class="btn btn-secondary"
                        onclick="return confirm('Alle Einstellungen dieses Tabs auf Standardwerte zurücksetzen?');">
                    ↩️ Tab zurücksetzen
                </button>
                <span id="unsaved-hint" style="display:none;color:#f59e0b;">⚠️ Ungespeicherte Änderungen</span>
            </div>

            <div class="customizer-layout">

                <!-- ── Tab-Navigation ── -->
                <nav class="customizer-nav">
                    <?php
                    $navGroups = [
                        null      => ['colors', 'typography', 'layout'],
                        'Design'  => ['header', 'footer'],
                        'Inhalte' => ['homepage', 'posts', 'pages'],
                        'Extra'   => ['social', 'advanced'],
                    ];
                    foreach ($navGroups as $groupLabel => $tabs):
                        if ($groupLabel !== null): ?>
                            <div class="customizer-nav-group">📂 <?php echo htmlspecialchars($groupLabel); ?></div>
                        <?php endif;
                        foreach ($tabs as $tabKey):
                            if (!isset($config[$tabKey])) { continue; }
                    ?>
                        <a href="?tab=<?php echo $tabKey; ?>"
                           class="<?php echo $activeTab === $tabKey ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($config[$tabKey]['title']); ?>
                        </a>
                    <?php endforeach;
                    endforeach; ?>
                </nav>

                <!-- ── Tab-Inhalt ── -->
                <div class="customizer-content">
                    <?php if (isset($config[$activeTab])): ?>
                    <?php $currentTab = $config[$activeTab]; ?>

                    <?php if ($activeTab === 'colors'):
                        // Farben in thematische Gruppen aufteilen
                        $colorGroups = [
                            '🎨 Markenfarben'        => ['primary_color', 'primary_dark', 'primary_mid', 'primary_light', 'accent_color', 'accent_hover'],
                            '💻 Tech-Akzente'        => ['accent_blue', 'accent_blue2', 'accent_teal', 'accent_teal_light'],
                            '🖼️ Header-Hintergründe' => ['bg_header1', 'bg_header2', 'bg_header3'],
                            '📄 Seite & Content'     => ['bg_primary', 'bg_secondary', 'bg_dark'],
                            '📝 Textfarben'          => ['text_primary', 'text_secondary', 'text_muted'],
                            '🧭 Navigationsfarben'   => ['text_nav', 'text_nav_member', 'text_nav_main', 'text_nav_quicklinks', 'text_nav_dropdown', 'logo_suffix_color'],
                            '🔲 Rahmen'              => ['border_light'],
                            '🔻 Footer'              => ['footer_bg', 'footer_bottom_bg', 'footer_border'],
                            '✅ Status-Farben'       => ['success_color', 'error_color'],
                            '📊 Progress Bar'        => ['progress_bar_start', 'progress_bar_end'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🎨 Farben</h3>
                        <div class="color-cards-grid">
                            <?php foreach ($colorGroups as $groupTitle => $groupKeys): ?>
                            <div class="color-card">
                                <h4><?php echo $groupTitle; ?></h4>
                                <?php foreach ($groupKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'header'):
                        $headerGroups = [
                            '🏷️ Logo & Marke'      => ['logo_text_part1', 'logo_text_part2', 'logo_text_suffix', 'logo_url', 'show_logo_text_with_image', 'logo_max_height', 'logo_accent_color'],
                            '� Member-Bar (Bar 1)' => ['show_member_bar', 'member_bar_height'],
                            '🧭 Hauptnavigation (Bar 2)' => ['main_nav_height', 'show_search_bar', 'search_placeholder', 'show_rss_link'],
                            '⚡ Quicklinks (Bar 3)' => ['show_quicklinks', 'sub_bar_height'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🖥️ Header & Navigation</h3>
                        <div class="field-grid">
                            <?php foreach ($headerGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>

                            <!-- Hinweis: Menü-Einträge über Menü-Editor -->
                            <div class="field-group-card" style="background:linear-gradient(135deg,#eff6ff,#f0f9ff);border:1px dashed #93c5fd;">
                                <h4>📋 Menü-Einträge bearbeiten</h4>
                                <p style="color:#1e40af;font-size:.85rem;line-height:1.5;margin:8px 0 12px;">
                                    Die <strong>Einträge</strong> aller Navigationen (Hauptmenü, Quicklinks, Footer-Themen, Footer-Seiten)
                                    werden über den <strong>Menü-Editor</strong> gepflegt – nicht hier im Design-Editor.<br>
                                    Hier steuerst du nur das <strong>Aussehen</strong> (Höhen, Farben, Sichtbarkeit).
                                </p>
                                <a href="<?php echo defined('SITE_URL') ? htmlspecialchars(SITE_URL) : ''; ?>/admin/menus.php"
                                   class="btn btn-sm btn-primary" style="margin-top:4px;">📝 Zum Menü-Editor →</a>
                            </div>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'homepage'):
                        $homepageGroups = [
                            '📌 Repo-Card'         => ['show_repo_card', 'repo_card_title', 'repo_card_description', 'repo_card_badge', 'repo_card_btn_text', 'repo_card_btn_url'],
                            '📰 Artikel-Liste'     => ['show_article_list', 'article_list_label', 'article_list_count', 'article_list_link_url', 'article_thumb_width', 'article_thumb_height', 'show_article_excerpt', 'show_article_meta', 'show_article_badge', 'show_meta_category', 'show_meta_date', 'show_meta_readtime', 'show_list_sidebar', 'list_sidebar_width', 'list_sidebar_title', 'list_sidebar_content'],
                            '🔧 Sidebar-Widgets'   => ['sidebar_show_identity', 'sidebar_identity_logo_url', 'sidebar_identity_tagline', 'sidebar_identity_link_url', 'sidebar_show_projects', 'sidebar_project1_name', 'sidebar_project1_logo_url', 'sidebar_project1_desc', 'sidebar_project1_url', 'sidebar_project2_name', 'sidebar_project2_logo_url', 'sidebar_project2_desc', 'sidebar_project2_url', 'sidebar_show_status', 'sidebar_status_label', 'sidebar_status_services', 'sidebar_show_downloads', 'sidebar_downloads_label', 'sidebar_downloads_items', 'sidebar_show_social', 'sidebar_social_label', 'sidebar_show_notice', 'sidebar_notice_title', 'sidebar_notice_text', 'sidebar_notice_url', 'sidebar_notice_url_text', 'sidebar_show_featured_posts', 'sidebar_featured_posts_label', 'sidebar_featured_post_1', 'sidebar_featured_post_2', 'sidebar_featured_post_3'],
                            '�🗂️ Kategorie-Cards'   => ['show_info_grid', 'info_card1_title', 'info_card1_text', 'info_card1_link_text', 'info_card1_link_url', 'info_card1_style', 'info_card2_title', 'info_card2_text', 'info_card2_link_text', 'info_card2_link_url', 'info_card2_style', 'show_info_card3', 'info_card3_title', 'info_card3_text', 'info_card3_link_text', 'info_card3_link_url', 'info_card3_badge', 'info_card3_style'],
                            '🧱 Kachel-Grid'       => ['show_tile_grid', 'tile_grid_label', 'tile_grid_count', 'tile_grid_columns', 'show_tile_excerpt', 'show_tile_category', 'show_tile_date', 'tile_grid_link_url'],
                            '📡 RSS-Feeds'         => ['show_feed_section', 'feed1_channel_id', 'feed1_count', 'feed2_channel_id', 'feed2_count'],
                            '📏 Sektionen-Abstände' => ['spacing_repo_card', 'spacing_article_list', 'spacing_info_cards', 'spacing_tile_grid', 'spacing_rss_feeds'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🏠 Startseite</h3>
                        <div class="field-grid">
                            <?php foreach ($homepageGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'posts'):
                        $postGroups = [
                            '🖼️ Hero & Meta'        => ['post_hero_height', 'show_post_hero', 'show_post_meta', 'show_reading_time', 'reading_time_wpm'],
                            '📖 Inhaltsverzeichnis'  => ['show_toc', 'toc_sticky', 'toc_min_headings', 'toc_header_text'],
                            '📌 Sidebar-Widgets'     => ['show_sidebar_social', 'sidebar_social_header', 'show_sidebar_related', 'sidebar_related_header', 'related_count'],
                            '🔗 Share-Buttons'       => ['show_share_buttons', 'show_share_linkedin', 'show_share_twitter', 'show_share_email', 'show_share_copy'],
                            '💻 Tech-Card'           => ['show_tech_card', 'tech_card_header'],
                            '💬 Kommentare & Tags'   => ['show_comments', 'comments_header', 'comment_form_header', 'show_post_tags'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>📝 Beiträge</h3>
                        <div class="field-grid">
                            <?php foreach ($postGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'pages'):
                        $pageGroups = [
                            '📄 Seiteneinstellungen' => ['show_page_title', 'show_page_updated_date', 'page_layout', 'show_page_sidebar', 'page_sidebar_show_nav', 'show_page_toc'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>📄 Seiten</h3>
                        <div class="field-grid">
                            <?php foreach ($pageGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'footer'):
                        $footerGroups = [
                            '🏷️ Brand & Text'    => ['footer_brand_name', 'footer_tagline', 'footer_col2_title', 'footer_col3_title', 'footer_col4_title', 'copyright_text', 'show_footer_social'],
                            '🍪 Cookie-Consent-Banner' => ['show_consent_banner', 'consent_text', 'consent_privacy_url'],
                            '🔗 Network/Partner-Bar' => ['show_network_bar', 'network_bar_link1_label', 'network_bar_link1_url', 'network_bar_link2_label', 'network_bar_link2_url', 'network_bar_link3_label', 'network_bar_link3_url', 'network_bar_link4_label', 'network_bar_link4_url', 'network_bar_link5_label', 'network_bar_link5_url'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🔻 Footer</h3>
                        <div class="field-grid">
                            <?php foreach ($footerGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'typography'):
                        $typoGroups = [
                            '🔤 Schriftarten'        => ['font_family_ui', 'font_family_brand', 'font_family_code'],
                            '📏 Größen & Abstände'   => ['font_size_base', 'font_size_post', 'line_height_base', 'line_height_post', 'font_weight_heading', 'font_weight_nav'],
                            '📝 Teaser-Texte (Cards)' => ['article_title_fontsize', 'tile_title_fontsize', 'article_excerpt_fontsize', 'article_excerpt_length', 'tile_excerpt_fontsize', 'tile_excerpt_length'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🔤 Typografie</h3>
                        <div class="field-grid">
                            <?php foreach ($typoGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'layout'):
                        $layoutGroups = [
                            '📐 Maße & Abstände'     => ['container_width', 'sidebar_width', 'border_radius', 'border_radius_md', 'content_gap', 'spacing_header_content', 'spacing_content_footer', 'spacing_sections'],
                            '🔤 Breadcrumb'           => ['show_breadcrumb', 'breadcrumb_on_posts', 'breadcrumb_on_pages'],
                            '⚙️ Funktionen & Optionen' => ['sidebar_position', 'enable_sticky_header', 'enable_progress_bar', 'enable_back_to_top', 'enable_dark_mode_toggle', 'enable_scroll_animations'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>📐 Layout</h3>
                        <div class="field-grid">
                            <?php foreach ($layoutGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'social'):
                        $socialGroups = [
                            '🔗 Profile & URLs'      => ['social_linkedin', 'social_github', 'social_twitter', 'social_mastodon', 'social_rss', 'social_youtube', 'social_xing'],
                            '🏷️ Button-Labels'       => ['social_label_linkedin', 'social_label_github', 'social_label_rss'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🌐 Social Media</h3>
                        <div class="field-grid">
                            <?php foreach ($socialGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'advanced'):
                        $advancedGroups = [
                            '🎨 Custom Code'          => ['custom_css', 'custom_head_code', 'custom_footer_code'],
                            '📊 Tracking & Cache'     => ['google_analytics_id', 'cache_buster_css'],
                        ];
                    ?>
                    <div class="admin-card">
                        <h3>🔧 Erweitert</h3>
                        <div class="field-grid">
                            <?php foreach ($advancedGroups as $grpTitle => $grpKeys): ?>
                            <div class="field-group-card">
                                <h4><?php echo $grpTitle; ?></h4>
                                <?php foreach ($grpKeys as $fk):
                                    if (!isset($currentTab['sections'][$fk])) { continue; }
                                    $f   = $currentTab['sections'][$fk];
                                    $val = $customizer->get($activeTab, $fk, $f['default']);
                                    if ($f['type'] === 'textarea') { $f['rows'] = 8; }
                                    phinit_render_field($activeTab, $fk, $f, $val);
                                endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php endif; ?>
                </div><!-- /.customizer-content -->

            </div><!-- /.customizer-layout -->
        </form>

    </div><!-- /.admin-content -->

    <script src="<?php echo defined('SITE_URL') ? htmlspecialchars(SITE_URL) : ''; ?>/assets/js/admin.js"></script>
    <script>
    // Ungespeicherte Änderungen anzeigen
    (function () {
        const form    = document.getElementById('customizer-form');
        const hint    = document.getElementById('unsaved-hint');
        const inputs  = form ? form.querySelectorAll('input, select, textarea') : [];
        let   changed = false;
        inputs.forEach(el => {
            el.addEventListener('change', () => { if (!changed) { changed = true; if (hint) hint.style.display = 'inline'; } });
            el.addEventListener('input',  () => { if (!changed) { changed = true; if (hint) hint.style.display = 'inline'; } });
        });
        form && form.addEventListener('submit', () => { changed = false; if (hint) hint.style.display = 'none'; });

        // Farbfelder: Text-Input ↔ color-Input synchronisieren
        document.querySelectorAll('input[type="color"]').forEach(cp => {
            const id     = cp.id;
            const textEl = document.getElementById(id + '_text');
            const hidden = document.getElementById(cp.name + '_hidden');
            if (textEl) {
                cp.addEventListener('input', () => {
                    textEl.value = cp.value;
                    if (hidden) hidden.value = cp.value;
                });
            }
        });
    })();
    </script>
</body>
</html>
