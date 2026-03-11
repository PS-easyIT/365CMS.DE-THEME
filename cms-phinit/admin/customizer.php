<?php
/**
 * CMS Phinit – Theme Customizer (Admin Fragment)
 *
 * Wird von CMS/admin/theme-editor.php als Fragment eingebunden:
 *   partials/header.php + partials/sidebar.php → customizer.php → partials/footer.php
 * Kein eigenes <!DOCTYPE html> / <head> / <body>!
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

// ── 1. Konfigurations-Schema ─────────────────────────────────────────────────
$config = [

    // ═══════════════════════════════════════════════════════════════════════
    // FARBEN
    // ═══════════════════════════════════════════════════════════════════════
    'colors' => [
        'title' => '🎨 Farben',
        'sections' => [
            'primary_color'        => ['label' => 'Primärfarbe',              'type' => 'color',  'default' => '#1e3a5f'],
            'primary_dark'         => ['label' => 'Primär Dunkel',            'type' => 'color',  'default' => '#0f2340'],
            'primary_mid'          => ['label' => 'Primär Mitte',             'type' => 'color',  'default' => '#1a3255'],
            'primary_light'        => ['label' => 'Primär Hell',              'type' => 'color',  'default' => '#2a4f7c'],
            'accent_color'         => ['label' => 'Akzent (Gold)',            'type' => 'color',  'default' => '#e8a838'],
            'accent_hover'         => ['label' => 'Akzent Hover',             'type' => 'color',  'default' => '#d4922a'],
            'accent_blue'          => ['label' => 'Akzent Blau',              'type' => 'color',  'default' => '#4a9eff'],
            'accent_blue2'         => ['label' => 'Akzent Blau 2',            'type' => 'color',  'default' => '#2d7dd2'],
            'accent_teal'          => ['label' => 'Akzent Teal',              'type' => 'color',  'default' => '#0d9488'],
            'accent_teal_light'    => ['label' => 'Akzent Teal Hell',         'type' => 'color',  'default' => '#14b8a6'],
            'bg_header1'           => ['label' => 'Header-BG 1',              'type' => 'color',  'default' => '#111827'],
            'bg_header2'           => ['label' => 'Header-BG 2',              'type' => 'color',  'default' => '#162030'],
            'bg_header3'           => ['label' => 'Header-BG 3',              'type' => 'color',  'default' => '#0e1a28'],
            'bg_primary'           => ['label' => 'Seite BG',                 'type' => 'color',  'default' => '#ffffff'],
            'bg_secondary'         => ['label' => 'Seite BG sekundär',        'type' => 'color',  'default' => '#f1f5f9'],
            'bg_dark'              => ['label' => 'Dark-Mode BG',             'type' => 'color',  'default' => '#0a0f1a'],
            'text_primary'         => ['label' => 'Text Primär',              'type' => 'color',  'default' => '#1e293b'],
            'text_secondary'       => ['label' => 'Text Sekundär',            'type' => 'color',  'default' => '#4a5568'],
            'text_muted'           => ['label' => 'Text Gedimmt',             'type' => 'color',  'default' => '#7a8898'],
            'text_nav'             => ['label' => 'Nav Text (allgemein)',      'type' => 'color',  'default' => '#e2e8f0'],
            'text_nav_member'      => ['label' => 'Member-Bar Text',          'type' => 'color',  'default' => '#e2e8f0'],
            'text_nav_main'        => ['label' => 'Hauptnav Text',            'type' => 'color',  'default' => '#e2e8f0'],
            'text_nav_quicklinks'  => ['label' => 'Quicklinks Text',          'type' => 'color',  'default' => '#b0bec5'],
            'text_nav_dropdown'    => ['label' => 'Dropdown Text',            'type' => 'color',  'default' => '#1e293b'],
            'logo_suffix_color'    => ['label' => 'Logo-Suffix Farbe',        'type' => 'color',  'default' => '#e8a838'],
            'border_light'         => ['label' => 'Rahmenfarbe',              'type' => 'color',  'default' => '#dde3ea'],
            'footer_bg'            => ['label' => 'Footer BG',                'type' => 'color',  'default' => '#0d1828'],
            'footer_bottom_bg'     => ['label' => 'Footer Bottom BG',         'type' => 'color',  'default' => '#080d15'],
            'footer_border'        => ['label' => 'Footer Border',            'type' => 'color',  'default' => '#2d7dd2'],
            'success_color'        => ['label' => 'Status: Erfolg',           'type' => 'color',  'default' => '#16a34a'],
            'error_color'          => ['label' => 'Status: Fehler',           'type' => 'color',  'default' => '#dc2626'],
            'progress_bar_start'   => ['label' => 'Progress-Bar Start',       'type' => 'color',  'default' => '#2d7dd2'],
            'progress_bar_end'     => ['label' => 'Progress-Bar Ende',        'type' => 'color',  'default' => '#e8a838'],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // TYPOGRAFIE
    // ═══════════════════════════════════════════════════════════════════════
    'typography' => [
        'title' => '🔤 Typografie',
        'sections' => [
            'font_family_ui'          => ['label' => 'UI-Schrift',               'type' => 'select', 'default' => 'barlow',          'options' => ['barlow' => 'Barlow', 'system' => 'System', 'inter' => 'Inter', 'roboto' => 'Roboto', 'open-sans' => 'Open Sans', 'lato' => 'Lato', 'montserrat' => 'Montserrat', 'poppins' => 'Poppins', 'source-sans' => 'Source Sans', 'nunito' => 'Nunito']],
            'font_family_brand'       => ['label' => 'Brand-Schrift',            'type' => 'select', 'default' => 'barlow-condensed', 'options' => ['barlow-condensed' => 'Barlow Condensed', 'barlow' => 'Barlow', 'system' => 'System', 'roboto-condensed' => 'Roboto Condensed', 'oswald' => 'Oswald', 'montserrat' => 'Montserrat', 'rajdhani' => 'Rajdhani', 'exo2' => 'Exo 2']],
            'font_family_code'        => ['label' => 'Code-Schrift',             'type' => 'select', 'default' => 'jetbrains-mono',   'options' => ['jetbrains-mono' => 'JetBrains Mono', 'fira-code' => 'Fira Code', 'source-code' => 'Source Code Pro', 'cascadia' => 'Cascadia Code', 'system-mono' => 'System Mono']],
            'font_size_base'          => ['label' => 'Basis-Schriftgröße (px)',  'type' => 'number', 'default' => '14.5'],
            'font_size_post'          => ['label' => 'Artikel-Schriftgröße (px)','type' => 'number', 'default' => '15.5'],
            'line_height_base'        => ['label' => 'Zeilenhöhe (UI)',          'type' => 'number', 'default' => '1.55'],
            'line_height_post'        => ['label' => 'Zeilenhöhe (Post)',        'type' => 'number', 'default' => '1.8'],
            'font_weight_heading'     => ['label' => 'Überschriften-Gewicht',    'type' => 'select', 'default' => '700',              'options' => ['400' => '400 (Normal)', '500' => '500', '600' => '600 (Semibold)', '700' => '700 (Bold)', '800' => '800', '900' => '900 (Black)']],
            'font_weight_nav'         => ['label' => 'Navigation-Gewicht',       'type' => 'select', 'default' => '600',              'options' => ['400' => '400 (Normal)', '500' => '500', '600' => '600 (Semibold)', '700' => '700 (Bold)', '800' => '800']],
            'article_title_fontsize'  => ['label' => 'Artikel-Karte: Titel (px)','type' => 'number', 'default' => '16'],
            'tile_title_fontsize'     => ['label' => 'Kachel: Titel (px)',       'type' => 'number', 'default' => '15'],
            'article_excerpt_fontsize'=> ['label' => 'Artikel: Auszug (px)',     'type' => 'number', 'default' => '13'],
            'article_excerpt_length'  => ['label' => 'Artikel: Auszug-Länge',   'type' => 'number', 'default' => '180'],
            'tile_excerpt_fontsize'   => ['label' => 'Kachel: Auszug (px)',      'type' => 'number', 'default' => '12'],
            'tile_excerpt_length'     => ['label' => 'Kachel: Auszug-Länge',    'type' => 'number', 'default' => '160'],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // LAYOUT
    // ═══════════════════════════════════════════════════════════════════════
    'layout' => [
        'title' => '📐 Layout',
        'sections' => [
            'container_width'          => ['label' => 'Container-Breite (px)',       'type' => 'number',   'default' => '1060'],
            'sidebar_width'            => ['label' => 'Sidebar-Breite (px)',         'type' => 'number',   'default' => '280'],
            'border_radius'            => ['label' => 'Rahmen-Radius klein (px)',    'type' => 'number',   'default' => '4'],
            'border_radius_md'         => ['label' => 'Rahmen-Radius mittel (px)',   'type' => 'number',   'default' => '6'],
            'content_gap'              => ['label' => 'Content-Lücke (px)',          'type' => 'number',   'default' => '28'],
            'spacing_header_content'   => ['label' => 'Abstand Header→Content (px)', 'type' => 'number',   'default' => '25'],
            'spacing_content_footer'   => ['label' => 'Abstand Content→Footer (px)', 'type' => 'number',   'default' => '25'],
            'spacing_sections'         => ['label' => 'Abstand zwischen Sektionen', 'type' => 'number',   'default' => '40'],
            'show_breadcrumb'          => ['label' => 'Breadcrumb anzeigen',         'type' => 'checkbox', 'default' => true],
            'breadcrumb_on_posts'      => ['label' => 'Breadcrumb auf Posts',        'type' => 'checkbox', 'default' => true],
            'breadcrumb_on_pages'      => ['label' => 'Breadcrumb auf Seiten',       'type' => 'checkbox', 'default' => true],
            'sidebar_position'         => ['label' => 'Sidebar-Position',            'type' => 'select',   'default' => 'right', 'options' => ['right' => 'Rechts', 'left' => 'Links', 'none' => 'Keine Sidebar']],
            'enable_sticky_header'     => ['label' => 'Sticky Header',               'type' => 'checkbox', 'default' => true],
            'enable_progress_bar'      => ['label' => 'Lese-Progress-Bar',           'type' => 'checkbox', 'default' => true],
            'enable_back_to_top'       => ['label' => 'Back-to-Top Button',          'type' => 'checkbox', 'default' => true],
            'enable_dark_mode_toggle'  => ['label' => 'Dark Mode Toggle',            'type' => 'checkbox', 'default' => true],
            'enable_scroll_animations' => ['label' => 'Scroll-Animationen',          'type' => 'checkbox', 'default' => true],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // HEADER
    // ═══════════════════════════════════════════════════════════════════════
    'header' => [
        'title' => '🖥️ Header',
        'sections' => [
            'logo_text_part1'       => ['label' => 'Logo Teil 1',             'type' => 'text',     'default' => '365'],
            'logo_text_part2'       => ['label' => 'Logo Teil 2',             'type' => 'text',     'default' => 'CMS'],
            'logo_text_suffix'      => ['label' => 'Logo Suffix',             'type' => 'text',     'default' => '.DE'],
            'logo_url'              => ['label' => 'Logo Bild-URL',           'type' => 'text',     'default' => ''],
            'show_logo_text_with_image' => ['label' => 'Logo-Text + Bild zusammen', 'type' => 'checkbox', 'default' => false],
            'logo_max_height'       => ['label' => 'Logo Höhe (px)',          'type' => 'number',   'default' => '28'],
            'logo_accent_color'     => ['label' => 'Logo-Akzentfarbe',        'type' => 'color',    'default' => '#4a9eff'],
            'show_member_bar'       => ['label' => 'Member-Bar anzeigen',     'type' => 'checkbox', 'default' => true],
            'member_bar_height'     => ['label' => 'Member-Bar Höhe (px)',    'type' => 'number',   'default' => '36'],
            'main_nav_height'       => ['label' => 'Hauptnav Höhe (px)',      'type' => 'number',   'default' => '48'],
            'show_search_bar'       => ['label' => 'Suchleiste anzeigen',     'type' => 'checkbox', 'default' => true],
            'search_placeholder'    => ['label' => 'Suche Platzhalter',       'type' => 'text',     'default' => 'Suchen…'],
            'show_rss_link'         => ['label' => 'RSS-Link anzeigen',       'type' => 'checkbox', 'default' => true],
            'show_quicklinks'       => ['label' => 'Quicklinks-Bar anzeigen', 'type' => 'checkbox', 'default' => true],
            'sub_bar_height'        => ['label' => 'Quicklinks-Bar Höhe (px)','type' => 'number',   'default' => '30'],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // FOOTER
    // ═══════════════════════════════════════════════════════════════════════
    'footer' => [
        'title' => '🔻 Footer',
        'sections' => [
            'footer_brand_name'         => ['label' => 'Footer Brand Name',         'type' => 'text',     'default' => 'phinit.de'],
            'footer_tagline'            => ['label' => 'Footer Tagline',            'type' => 'text',     'default' => 'IT-Profi · M365 · Scripting · Open Source'],
            'footer_col2_title'         => ['label' => 'Spalte 2 Titel',            'type' => 'text',     'default' => 'Themen'],
            'footer_col3_title'         => ['label' => 'Spalte 3 Titel',            'type' => 'text',     'default' => 'Seiten'],
            'footer_col4_title'         => ['label' => 'Spalte 4 Titel',            'type' => 'text',     'default' => 'Kontakt'],
            'copyright_text'            => ['label' => 'Copyright-Text',            'type' => 'text',     'default' => '© {year} phinit.de'],
            'show_footer_social'        => ['label' => 'Social-Links im Footer',    'type' => 'checkbox', 'default' => true],
            'show_consent_banner'       => ['label' => 'Consent-Banner anzeigen',   'type' => 'checkbox', 'default' => true],
            'consent_text'              => ['label' => 'Consent Banner Text',       'type' => 'textarea', 'default' => 'Diese Website verwendet Cookies für Analysezwecke.'],
            'consent_privacy_url'       => ['label' => 'Datenschutz-URL',           'type' => 'text',     'default' => '/datenschutz'],
            'show_network_bar'          => ['label' => 'Network/Partner-Bar',       'type' => 'checkbox', 'default' => true],
            'network_bar_link1_label'   => ['label' => 'Network-Link 1 Label',      'type' => 'text',     'default' => 'phinit.de'],
            'network_bar_link1_url'     => ['label' => 'Network-Link 1 URL',        'type' => 'text',     'default' => 'https://phinit.de'],
            'network_bar_link2_label'   => ['label' => 'Network-Link 2 Label',      'type' => 'text',     'default' => 'phscripts.de'],
            'network_bar_link2_url'     => ['label' => 'Network-Link 2 URL',        'type' => 'text',     'default' => 'https://phscripts.de'],
            'network_bar_link3_label'   => ['label' => 'Network-Link 3 Label',      'type' => 'text',     'default' => '365network.de'],
            'network_bar_link3_url'     => ['label' => 'Network-Link 3 URL',        'type' => 'text',     'default' => 'https://365network.de'],
            'network_bar_link4_label'   => ['label' => 'Network-Link 4 Label (leer = aus)', 'type' => 'text', 'default' => 'ms365insights.de'],
            'network_bar_link4_url'     => ['label' => 'Network-Link 4 URL',        'type' => 'text',     'default' => 'https://ms365insights.de'],
            'network_bar_link5_label'   => ['label' => 'Network-Link 5 Label (leer = aus)', 'type' => 'text', 'default' => ''],
            'network_bar_link5_url'     => ['label' => 'Network-Link 5 URL',        'type' => 'text',     'default' => ''],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // STARTSEITE
    // ═══════════════════════════════════════════════════════════════════════
    'homepage' => [
        'title' => '🏠 Startseite',
        'sections' => [
            // Repo-Card
            'show_repo_card'          => ['label' => 'Repo-Card anzeigen',           'type' => 'checkbox', 'default' => true],
            'repo_card_title'         => ['label' => 'Repo-Card Titel',              'type' => 'text',     'default' => 'PS-easyIT Script-Repository'],
            'repo_card_description'   => ['label' => 'Repo-Card Beschreibung',       'type' => 'textarea', 'default' => '25+ Code-signierte PowerShell-Module für M365 Administration. Enterprise-ready & Open Source.'],
            'repo_card_badge'         => ['label' => 'Repo-Card Badge',              'type' => 'text',     'default' => '25+ Repos'],
            'repo_card_btn_text'      => ['label' => 'Repo-Card Button Text',        'type' => 'text',     'default' => 'Zum GitHub →'],
            'repo_card_btn_url'       => ['label' => 'Repo-Card Button URL',         'type' => 'text',     'default' => 'https://github.com/phinit/'],
            // Artikel-Liste
            'show_article_list'       => ['label' => 'Artikel-Liste anzeigen',       'type' => 'checkbox', 'default' => true],
            'article_list_label'      => ['label' => 'Sektion-Label',                'type' => 'text',     'default' => 'Aktuell'],
            'article_list_count'      => ['label' => 'Anzahl Artikel',               'type' => 'number',   'default' => '4'],
            'article_list_link_url'   => ['label' => '„Alle Beiträge" URL',          'type' => 'text',     'default' => '/blog'],
            'article_thumb_width'     => ['label' => 'Thumbnail Breite (px)',        'type' => 'number',   'default' => '190'],
            'article_thumb_height'    => ['label' => 'Thumbnail Höhe (px)',          'type' => 'number',   'default' => '115'],
            'show_article_excerpt'    => ['label' => 'Auszug anzeigen',              'type' => 'checkbox', 'default' => true],
            'show_article_meta'       => ['label' => 'Meta (Kat/Datum/Lesezeit)',    'type' => 'checkbox', 'default' => true],
            'show_article_badge'      => ['label' => 'Kategorie-Badge auf Thumb',    'type' => 'checkbox', 'default' => true],
            'show_meta_category'      => ['label' => 'Meta: Kategorie',              'type' => 'checkbox', 'default' => true],
            'show_meta_date'          => ['label' => 'Meta: Datum',                  'type' => 'checkbox', 'default' => true],
            'show_meta_readtime'      => ['label' => 'Meta: Lesezeit',               'type' => 'checkbox', 'default' => true],
            // Sidebar neben Artikel-Liste
            'show_list_sidebar'       => ['label' => 'Sidebar neben „Aktuell"',      'type' => 'checkbox', 'default' => false],
            'list_sidebar_width'      => ['label' => 'Sidebar Breite (px)',          'type' => 'number',   'default' => '260'],
            'list_sidebar_title'      => ['label' => 'Sidebar Titel',                'type' => 'text',     'default' => ''],
            'list_sidebar_content'    => ['label' => 'Sidebar Inhalt (HTML)',        'type' => 'textarea', 'default' => ''],
            // Sidebar-Widgets
            'sidebar_show_identity'          => ['label' => 'Widget: Site-Identity', 'type' => 'checkbox', 'default' => true],
            'sidebar_identity_logo_url'      => ['label' => 'Identity Logo-URL',     'type' => 'text',     'default' => ''],
            'sidebar_identity_tagline'       => ['label' => 'Identity Tagline',      'type' => 'text',     'default' => ''],
            'sidebar_identity_link_url'      => ['label' => 'Identity Link',         'type' => 'text',     'default' => '/'],
            'sidebar_show_projects'          => ['label' => 'Widget: Projekt-Hinweise', 'type' => 'checkbox', 'default' => true],
            'sidebar_project1_name'          => ['label' => 'Projekt 1 Name',        'type' => 'text',     'default' => '365CMS.DE'],
            'sidebar_project1_logo_url'      => ['label' => 'Projekt 1 Logo-URL',    'type' => 'text',     'default' => ''],
            'sidebar_project1_desc'          => ['label' => 'Projekt 1 Beschreibung','type' => 'text',     'default' => 'Das eigene CMS – modular & flexibel'],
            'sidebar_project1_url'           => ['label' => 'Projekt 1 URL',         'type' => 'text',     'default' => 'https://365cms.de'],
            'sidebar_project2_name'          => ['label' => 'Projekt 2 Name',        'type' => 'text',     'default' => '365NETWORK.DE'],
            'sidebar_project2_logo_url'      => ['label' => 'Projekt 2 Logo-URL',    'type' => 'text',     'default' => ''],
            'sidebar_project2_desc'          => ['label' => 'Projekt 2 Beschreibung','type' => 'text',     'default' => 'Business-Netzwerk-Plattform'],
            'sidebar_project2_url'           => ['label' => 'Projekt 2 URL',         'type' => 'text',     'default' => 'https://365network.de'],
            'sidebar_show_status'            => ['label' => 'Widget: Dienst-Status', 'type' => 'checkbox', 'default' => true],
            'sidebar_status_label'           => ['label' => 'Status Label',          'type' => 'text',     'default' => 'Dienst-Status'],
            'sidebar_status_services'        => ['label' => 'Status-Dienste (Name|URL|Kürzel)', 'type' => 'textarea', 'default' => "Microsoft 365|https://status.office365.com|M365\nAzure|https://status.azure.com|AZ\nGitHub|https://githubstatus.com|GH\nCloudflare|https://www.cloudflarestatus.com|CF"],
            'sidebar_show_downloads'         => ['label' => 'Widget: Downloads',     'type' => 'checkbox', 'default' => false],
            'sidebar_downloads_label'        => ['label' => 'Downloads Label',       'type' => 'text',     'default' => 'Downloads & Checklisten'],
            'sidebar_downloads_items'        => ['label' => 'Download-Links (Bezeichnung|URL)', 'type' => 'textarea', 'default' => ''],
            'sidebar_show_social'            => ['label' => 'Widget: Social-Links',  'type' => 'checkbox', 'default' => true],
            'sidebar_social_label'           => ['label' => 'Social Label',          'type' => 'text',     'default' => 'Folge uns'],
            'sidebar_show_notice'            => ['label' => 'Widget: Hinweis/Ankündigung', 'type' => 'checkbox', 'default' => false],
            'sidebar_notice_title'           => ['label' => 'Hinweis Titel',         'type' => 'text',     'default' => '💡 Aktueller Hinweis'],
            'sidebar_notice_text'            => ['label' => 'Hinweis Text',          'type' => 'textarea', 'default' => ''],
            'sidebar_notice_url'             => ['label' => 'Hinweis Link URL',      'type' => 'text',     'default' => ''],
            'sidebar_notice_url_text'        => ['label' => 'Hinweis Link Text',     'type' => 'text',     'default' => 'Mehr erfahren →'],
            'sidebar_show_featured_posts'    => ['label' => 'Widget: Empfohlene Artikel', 'type' => 'checkbox', 'default' => false],
            'sidebar_featured_posts_label'   => ['label' => 'Empfohlene Artikel Label', 'type' => 'text',  'default' => '📌 Empfohlene Artikel'],
            'sidebar_featured_post_1'        => ['label' => 'Empfohlener Beitrag 1', 'type' => 'post_picker', 'default' => ''],
            'sidebar_featured_post_2'        => ['label' => 'Empfohlener Beitrag 2', 'type' => 'post_picker', 'default' => ''],
            'sidebar_featured_post_3'        => ['label' => 'Empfohlener Beitrag 3', 'type' => 'post_picker', 'default' => ''],
            // Info-Cards
            'show_info_grid'         => ['label' => 'Kategorie-Cards anzeigen',      'type' => 'checkbox', 'default' => true],
            'info_card1_title'       => ['label' => 'Info-Card 1 Titel',             'type' => 'text',     'default' => '🖥️ Admin Anleitungen'],
            'info_card1_text'        => ['label' => 'Info-Card 1 Text',              'type' => 'textarea', 'default' => 'Schritt-für-Schritt-Tutorials für Microsoft 365 Administration.'],
            'info_card1_link_text'   => ['label' => 'Info-Card 1 Link-Text',         'type' => 'text',     'default' => 'Alle Anleitungen ansehen →'],
            'info_card1_link_url'    => ['label' => 'Info-Card 1 Link URL',          'type' => 'text',     'default' => '/kategorie/anleitungen'],
            'info_card1_style'       => ['label' => 'Info-Card 1 Stil',              'type' => 'select',   'default' => 'default', 'options' => ['default' => 'Standard (Blau)', 'gold' => 'Gold-Akzent']],
            'info_card2_title'       => ['label' => 'Info-Card 2 Titel',             'type' => 'text',     'default' => '🔒 DSGVO & Compliance'],
            'info_card2_text'        => ['label' => 'Info-Card 2 Text',              'type' => 'textarea', 'default' => 'Konfigurationsanleitungen und Best Practices für Microsoft Purview.'],
            'info_card2_link_text'   => ['label' => 'Info-Card 2 Link-Text',         'type' => 'text',     'default' => 'Compliance-Center →'],
            'info_card2_link_url'    => ['label' => 'Info-Card 2 Link URL',          'type' => 'text',     'default' => '/kategorie/compliance'],
            'info_card2_style'       => ['label' => 'Info-Card 2 Stil',              'type' => 'select',   'default' => 'gold',    'options' => ['default' => 'Standard (Blau)', 'gold' => 'Gold-Akzent']],
            'show_info_card3'        => ['label' => 'Info-Card 3 anzeigen',          'type' => 'checkbox', 'default' => false],
            'info_card3_title'       => ['label' => 'Info-Card 3 Titel',             'type' => 'text',     'default' => 'Open Source'],
            'info_card3_text'        => ['label' => 'Info-Card 3 Text',              'type' => 'text',     'default' => 'Meine Projekte auf GitHub.'],
            'info_card3_link_text'   => ['label' => 'Info-Card 3 Link-Text',         'type' => 'text',     'default' => 'Zum Repository →'],
            'info_card3_link_url'    => ['label' => 'Info-Card 3 Link URL',          'type' => 'text',     'default' => 'https://github.com/'],
            'info_card3_badge'       => ['label' => 'Info-Card 3 Badge',             'type' => 'text',     'default' => 'GitHub'],
            'info_card3_style'       => ['label' => 'Info-Card 3 Stil',              'type' => 'select',   'default' => 'repo',    'options' => ['default' => 'Standard (Blau)', 'gold' => 'Gold-Akzent', 'repo' => 'Repo (Dark)']],
            // Kachel-Grid
            'show_tile_grid'         => ['label' => 'Deep-Dive Grid anzeigen',       'type' => 'checkbox', 'default' => true],
            'tile_grid_label'        => ['label' => 'Grid Sektion-Label',            'type' => 'text',     'default' => 'Deep-Dive Archiv'],
            'tile_grid_count'        => ['label' => 'Anzahl Kacheln',               'type' => 'number',   'default' => '6'],
            'tile_grid_columns'      => ['label' => 'Grid-Spalten',                 'type' => 'select',   'default' => '3', 'options' => ['2' => '2 Spalten', '3' => '3 Spalten (Standard)', '4' => '4 Spalten']],
            'show_tile_excerpt'      => ['label' => 'Grid: Auszug anzeigen',        'type' => 'checkbox', 'default' => true],
            'show_tile_category'     => ['label' => 'Grid: Kategorie-Badge',         'type' => 'checkbox', 'default' => true],
            'show_tile_date'         => ['label' => 'Grid: Datum anzeigen',          'type' => 'checkbox', 'default' => true],
            'tile_grid_link_url'     => ['label' => '„Archiv" URL',                  'type' => 'text',     'default' => '/archiv'],
            // RSS-Feeds
            'show_feed_section'      => ['label' => 'RSS-Feed-Sektion anzeigen',     'type' => 'checkbox', 'default' => true],
            'feed1_channel_id'       => ['label' => 'Feed 1 – Kanal (cms-feed)',     'type' => 'select',   'default' => '0', 'options' => ['0' => '— Kein Feed —']],
            'feed1_count'            => ['label' => 'Feed 1 – Anzahl Einträge',      'type' => 'number',   'default' => '5'],
            'feed2_channel_id'       => ['label' => 'Feed 2 – Kanal (cms-feed)',     'type' => 'select',   'default' => '0', 'options' => ['0' => '— Kein Feed —']],
            'feed2_count'            => ['label' => 'Feed 2 – Anzahl Einträge',      'type' => 'number',   'default' => '5'],
            // Sektionen-Abstände
            'spacing_repo_card'      => ['label' => 'Abstand nach Repo-Card (px)',   'type' => 'number',   'default' => '32'],
            'spacing_article_list'   => ['label' => 'Abstand nach Artikel-Liste (px)','type' => 'number',  'default' => '32'],
            'spacing_info_cards'     => ['label' => 'Abstand nach Kategorie-Cards (px)', 'type' => 'number', 'default' => '32'],
            'spacing_tile_grid'      => ['label' => 'Abstand nach Kachel-Grid (px)', 'type' => 'number',   'default' => '32'],
            'spacing_rss_feeds'      => ['label' => 'Abstand nach RSS-Feeds (px)',   'type' => 'number',   'default' => '0'],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // BEITRÄGE
    // ═══════════════════════════════════════════════════════════════════════
    'posts' => [
        'title' => '📝 Beiträge',
        'sections' => [
            'post_hero_height'       => ['label' => 'Beitragsbild Höhe (px)',         'type' => 'number',   'default' => '215'],
            'post_hero_width'        => ['label' => 'Beitragsbild Breite (px)',        'type' => 'number',   'default' => '162'],
            'show_post_hero'         => ['label' => 'Beitragsbild links vom Titel',    'type' => 'checkbox', 'default' => true],
            'post_title_fontsize'    => ['label' => 'Beitrags-Titel Schriftgröße (px)', 'type' => 'number',   'default' => '28'],
            'show_post_meta'         => ['label' => 'Artikel-Meta anzeigen',         'type' => 'checkbox', 'default' => true],
            'show_reading_time'      => ['label' => 'Lesezeit anzeigen',             'type' => 'checkbox', 'default' => true],
            'reading_time_wpm'       => ['label' => 'Wörter pro Minute',             'type' => 'number',   'default' => '220'],
            'show_toc'               => ['label' => 'Inhaltsverzeichnis anzeigen',   'type' => 'checkbox', 'default' => true],
            'toc_sticky'             => ['label' => 'TOC sticky',                    'type' => 'checkbox', 'default' => true],
            'toc_min_headings'       => ['label' => 'TOC ab mind. X Überschriften',  'type' => 'number',   'default' => '2'],
            'toc_header_text'        => ['label' => 'TOC Widget-Titel',              'type' => 'text',     'default' => 'Inhaltsverzeichnis'],
            'show_sidebar_social'    => ['label' => 'Social-Widget in Sidebar',      'type' => 'checkbox', 'default' => true],
            'sidebar_social_header'  => ['label' => 'Social-Widget Titel',           'type' => 'text',     'default' => 'Folgen & Teilen'],
            'show_sidebar_related'   => ['label' => 'Verwandte Artikel in Sidebar',  'type' => 'checkbox', 'default' => true],
            'sidebar_related_header' => ['label' => 'Verwandte Artikel Titel',       'type' => 'text',     'default' => 'Verwandte Artikel'],
            'related_count'          => ['label' => 'Anzahl verwandter Artikel',     'type' => 'number',   'default' => '4'],
            'show_share_buttons'     => ['label' => 'Share-Buttons anzeigen',        'type' => 'checkbox', 'default' => true],
            'show_share_linkedin'    => ['label' => 'LinkedIn Share',                'type' => 'checkbox', 'default' => true],
            'show_share_twitter'     => ['label' => 'Twitter/X Share',               'type' => 'checkbox', 'default' => true],
            'show_share_email'       => ['label' => 'E-Mail Share',                  'type' => 'checkbox', 'default' => true],
            'show_share_copy'        => ['label' => 'Link kopieren',                 'type' => 'checkbox', 'default' => true],
            'show_tech_card'         => ['label' => 'Tech-Infocard (post-tech)',      'type' => 'checkbox', 'default' => true],
            'tech_card_header'       => ['label' => 'Tech-Card Titel',               'type' => 'text',     'default' => 'Tech-Details'],
            'show_comments'          => ['label' => 'Kommentarbereich anzeigen',     'type' => 'checkbox', 'default' => true],
            'comments_header'        => ['label' => 'Kommentare Titel',              'type' => 'text',     'default' => 'Kommentare'],
            'comment_form_header'    => ['label' => 'Kommentarformular Titel',       'type' => 'text',     'default' => 'Kommentar hinterlassen'],
            'show_post_tags'         => ['label' => 'Tags anzeigen',                 'type' => 'checkbox', 'default' => true],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // SEITEN
    // ═══════════════════════════════════════════════════════════════════════
    'pages' => [
        'title' => '📄 Seiten',
        'sections' => [
            'show_page_title'        => ['label' => 'Seiten-Titel anzeigen',         'type' => 'checkbox', 'default' => true],
            'page_title_fontsize'    => ['label' => 'Seiten-Titel Schriftgröße (px)',    'type' => 'number',   'default' => '28'],
            'show_page_hero'         => ['label' => 'Seiten-Bild links vom Titel',    'type' => 'checkbox', 'default' => true],
            'show_page_updated_date' => ['label' => 'Aktualisierungsdatum',          'type' => 'checkbox', 'default' => false],
            'page_layout'            => ['label' => 'Seiten-Layout',                 'type' => 'select',   'default' => 'full', 'options' => ['full' => 'Volle Breite', 'narrow' => 'Schmal (860 px)', 'two-col' => 'Zweispaltig']],
            'show_page_sidebar'      => ['label' => 'Sidebar auf Seiten',            'type' => 'checkbox', 'default' => false],
            'page_sidebar_show_nav'  => ['label' => 'Sidebar: Navigations-Widget',   'type' => 'checkbox', 'default' => true],
            'show_page_toc'          => ['label' => 'Inhaltsverzeichnis auf Seiten', 'type' => 'checkbox', 'default' => false],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // SOCIAL MEDIA
    // ═══════════════════════════════════════════════════════════════════════
    'social' => [
        'title' => '🌐 Social Media',
        'sections' => [
            'social_linkedin'        => ['label' => 'LinkedIn URL',         'type' => 'text', 'default' => 'https://www.linkedin.com/in/andreashepp/'],
            'social_github'          => ['label' => 'GitHub URL',           'type' => 'text', 'default' => 'https://github.com/phinit/'],
            'social_twitter'         => ['label' => 'Twitter/X URL',        'type' => 'text', 'default' => ''],
            'social_mastodon'        => ['label' => 'Mastodon URL',         'type' => 'text', 'default' => ''],
            'social_rss'             => ['label' => 'RSS-Feed URL',         'type' => 'text', 'default' => '/feed.xml'],
            'social_youtube'         => ['label' => 'YouTube URL',          'type' => 'text', 'default' => ''],
            'social_xing'            => ['label' => 'Xing URL',             'type' => 'text', 'default' => ''],
            'social_label_linkedin'  => ['label' => 'LinkedIn Button-Label','type' => 'text', 'default' => 'LinkedIn'],
            'social_label_github'    => ['label' => 'GitHub Button-Label',  'type' => 'text', 'default' => 'GitHub'],
            'social_label_rss'       => ['label' => 'RSS Button-Label',     'type' => 'text', 'default' => 'RSS Feed'],
        ],
    ],

    // ═══════════════════════════════════════════════════════════════════════
    // ERWEITERT
    // ═══════════════════════════════════════════════════════════════════════
    'advanced' => [
        'title' => '🔧 Erweitert',
        'sections' => [
            'custom_css'           => ['label' => 'Eigenes CSS',              'type' => 'textarea', 'default' => ''],
            'custom_head_code'     => ['label' => 'Custom Head Code',         'type' => 'textarea', 'default' => ''],
            'custom_footer_code'   => ['label' => 'Custom Footer Code',       'type' => 'textarea', 'default' => ''],
            'google_analytics_id'  => ['label' => 'Google Analytics ID',      'type' => 'text',     'default' => ''],
            'cache_buster_css'     => ['label' => 'CSS Cache-Buster Version', 'type' => 'text',     'default' => ''],
            'og_default_image'     => ['label' => 'Standard OG-Bild URL (Fallback für Social-Sharing)', 'type' => 'text', 'default' => ''],
        ],
    ],

    // ── SEO ─────────────────────────────────────────────────────────────────
    'seo' => [
        'title' => '🔍 SEO',
        'sections' => [
            'meta_robots'       => ['label' => 'Meta Robots (Standard)',        'type' => 'select',   'default' => 'index,follow', 'options' => ['index,follow' => 'index, follow', 'noindex,follow' => 'noindex, follow', 'noindex,nofollow' => 'noindex, nofollow']],
            'canonical_self'    => ['label' => 'Canonical-Tag auf eigene URL',  'type' => 'checkbox', 'default' => true],
            'og_site_name'      => ['label' => 'OG Site Name',                  'type' => 'text',     'default' => ''],
            'og_type_default'   => ['label' => 'OG Type (Standard)',            'type' => 'select',   'default' => 'website', 'options' => ['website' => 'website', 'blog' => 'blog', 'article' => 'article']],
            'twitter_card_type' => ['label' => 'Twitter Card Type',             'type' => 'select',   'default' => 'summary_large_image', 'options' => ['summary_large_image' => 'summary_large_image', 'summary' => 'summary']],
            'structured_data'   => ['label' => 'Schema.org Markup aktiv',       'type' => 'checkbox', 'default' => true],
            'breadcrumb_schema' => ['label' => 'Breadcrumb Schema aktiv',       'type' => 'checkbox', 'default' => true],
            'noindex_search'    => ['label' => 'Suchseite noindex',             'type' => 'checkbox', 'default' => true],
            'noindex_404'       => ['label' => '404-Seite noindex',             'type' => 'checkbox', 'default' => true],
        ],
    ],

    // ── PERFORMANCE ──────────────────────────────────────────────────────────
    'performance' => [
        'title' => '⚡ Performance',
        'sections' => [
            'enable_photoswipe' => ['label' => 'PhotoSwipe Lightbox aktiv',              'type' => 'checkbox', 'default' => true],
            'lazyload_images'   => ['label' => 'Bilder Lazy-Load (loading=lazy)',         'type' => 'checkbox', 'default' => true],
            'preconnect_fonts'  => ['label' => 'Preconnect Google Fonts',                'type' => 'checkbox', 'default' => true],
            'preconnect_extra'  => ['label' => 'Zusätzliche Preconnect-URLs (eine pro Zeile)', 'type' => 'textarea', 'default' => ''],
            'defer_scripts'     => ['label' => 'Theme-Scripts defer',                    'type' => 'checkbox', 'default' => true],
            'dns_prefetch'      => ['label' => 'DNS-Prefetch aktiv',                     'type' => 'checkbox', 'default' => true],
        ],
    ],
];

// ── 2. Customizer-Instanz ────────────────────────────────────────────────────
$customizer = ThemeCustomizer::instance();
$customizer->setTheme('cms-phinit');

// cms-feed Kanal-Optionen dynamisch laden
$_feedOpts = ['0' => '— Kein Feed —'];
if (class_exists('CMS_Feed_Database')) {
    try {
        foreach (CMS_Feed_Database::instance()->get_channels(0) as $_ch) {
            if (!empty($_ch['is_active'])) {
                $_feedOpts[(string)$_ch['id']] = htmlspecialchars(
                    $_ch['name'] . (isset($_ch['category_name']) ? ' (' . $_ch['category_name'] . ')' : ''),
                    ENT_QUOTES
                );
            }
        }
    } catch (\Throwable $_e) {}
}
$config['homepage']['sections']['feed1_channel_id']['options'] = $_feedOpts;
$config['homepage']['sections']['feed2_channel_id']['options'] = $_feedOpts;

// Aktiver Tab
$activeTab = $_GET['tab'] ?? 'colors';
if (!isset($config[$activeTab])) {
    $activeTab = 'colors';
}

// ── 3. POST-Handler ──────────────────────────────────────────────────────────
$alertMsg  = null;
$alertType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    // CSRF prüfen
    if (!Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'phinit_customizer')) {
        $alertMsg  = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
        $alertType = 'danger';

    // ── Tab zurücksetzen ──
    } elseif ($postAction === 'reset_theme_tab') {
        $resetTab = $_POST['active_section'] ?? $activeTab;
        if (!isset($config[$resetTab])) { $resetTab = $activeTab; }
        $ok = true;
        foreach ($config[$resetTab]['sections'] as $fk => $fc) {
            $def = $fc['default'] ?? '';
            if (is_bool($def)) { $def = $def ? '1' : '0'; }
            if (!$customizer->set($resetTab, $fk, (string)$def)) { $ok = false; }
        }
        $alertMsg  = $ok
            ? 'Tab &bdquo;' . htmlspecialchars($config[$resetTab]['title']) . '&ldquo; auf Standardwerte zurückgesetzt.'
            : 'Einige Felder konnten nicht zurückgesetzt werden.';
        $alertType = $ok ? 'success' : 'danger';

    // ── Einstellungen speichern ──
    } elseif ($postAction === 'save_theme_options') {
        $saveTab = $_POST['active_section'] ?? $activeTab;
        if (!isset($config[$saveTab])) { $saveTab = $activeTab; }
        $ok = true;
        foreach ($config[$saveTab]['sections'] as $fk => $fc) {
            $name = "{$saveTab}_{$fk}";
            if ($fc['type'] === 'checkbox') {
                $val = isset($_POST[$name]) ? '1' : '0';
            } elseif ($fc['type'] === 'textarea' && !in_array($saveTab, ['advanced'], true)) {
                $val = strip_tags($_POST[$name] ?? '');
            } else {
                $val = $_POST[$name] ?? '';
            }
            if (!$customizer->set($saveTab, $fk, (string)$val)) { $ok = false; }
        }
        $alertMsg  = $ok
            ? 'Einstellungen für &bdquo;' . htmlspecialchars($config[$saveTab]['title'] ?? $saveTab) . '&ldquo; gespeichert.'
            : 'Einige Einstellungen konnten nicht gespeichert werden.';
        $alertType = $ok ? 'success' : 'danger';

    // ── Export ──
    } elseif ($postAction === 'export_settings') {
        $export = json_encode($customizer->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="cms-phinit-customizer-' . date('Y-m-d') . '.json"');
        header('Content-Length: ' . strlen($export));
        echo $export;
        exit;

    // ── Import ──
    } elseif ($postAction === 'import_settings') {
        $file = $_FILES['import_file'] ?? null;
        if ($file && $file['error'] === UPLOAD_ERR_OK && $file['size'] < 524288) {
            $raw = file_get_contents($file['tmp_name']);
            $data = json_decode($raw ?: '', true);
            if (is_array($data) && $customizer->import($data)) {
                $alertMsg  = 'Einstellungen erfolgreich importiert.';
                $alertType = 'success';
            } else {
                $alertMsg  = 'Import fehlgeschlagen – ungültige JSON-Datei.';
                $alertType = 'danger';
            }
        } else {
            $alertMsg  = 'Datei-Upload fehlgeschlagen oder Datei zu groß (&gt;512 KB).';
            $alertType = 'danger';
        }
    }
}

// CSRF-Token nach POST-Handling generieren (verhindert Token-Überschreibung)
$csrfToken = Security::instance()->generateToken('phinit_customizer');

// ── 4. Feld-Renderer ─────────────────────────────────────────────────────────
/**
 * Rendert ein einzelnes Formularfeld mit Tabler-CSS-Klassen.
 */
function phinit_render_field(string $tab, string $fk, array $f, mixed $val): void
{
    $id   = "f_{$tab}_{$fk}";
    $name = "{$tab}_{$fk}";
    $val  = (string)$val;
    $desc = $f['description'] ?? '';
    ?>
    <div class="mb-3">
        <?php if ($f['type'] === 'checkbox'): ?>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox"
                       id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="1"
                       <?php echo ($val && $val !== '0') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="<?php echo $id; ?>">
                    <?php echo htmlspecialchars($f['label']); ?>
                </label>
            </div>

        <?php elseif ($f['type'] === 'color'): ?>
            <label class="form-label"><?php echo htmlspecialchars($f['label']); ?></label>
                 <div class="input-group phinit-customizer__color-group">
                <input type="color"
                       id="<?php echo $id; ?>"
                       value="<?php echo htmlspecialchars($val ?: '#000000'); ?>"
                      class="form-control form-control-color phinit-customizer__color-input"
                       oninput="syncColor('<?php echo $id; ?>','<?php echo $id; ?>_txt','<?php echo $name; ?>')">
                <input type="text"
                       id="<?php echo $id; ?>_txt"
                       value="<?php echo htmlspecialchars($val); ?>"
                      class="form-control font-monospace phinit-customizer__color-text"
                       oninput="syncColorTxt('<?php echo $id; ?>','<?php echo $id; ?>_txt','<?php echo $name; ?>')">
                <input type="hidden" name="<?php echo $name; ?>"
                       id="<?php echo $name; ?>"
                       value="<?php echo htmlspecialchars($val); ?>">
            </div>

        <?php elseif ($f['type'] === 'select'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars($f['label']); ?></label>
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="form-select">
                <?php foreach ($f['options'] as $ov => $ol): ?>
                <option value="<?php echo htmlspecialchars((string)$ov); ?>"
                    <?php echo $val === (string)$ov ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($ol); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($f['type'] === 'textarea'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars($f['label']); ?></label>
            <textarea id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                      class="form-control" rows="<?php echo $f['rows'] ?? 3; ?>"><?php echo htmlspecialchars($val); ?></textarea>

        <?php elseif ($f['type'] === 'number'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars($f['label']); ?></label>
            <input type="number" id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                   value="<?php echo htmlspecialchars($val); ?>"
                     class="form-control phinit-customizer__number-input"
                   step="<?php echo $f['step'] ?? 'any'; ?>"
                   <?php echo isset($f['min']) ? 'min="' . (int)$f['min'] . '"' : ''; ?>
                   <?php echo isset($f['max']) ? 'max="' . (int)$f['max'] . '"' : ''; ?>>

        <?php elseif ($f['type'] === 'post_picker'): ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars($f['label']); ?></label>
            <?php
            $ppRows = [];
            try {
                $_db = \CMS\Database::instance();
                $ppRows = array_map(
                    fn($r) => (array)$r,
                    $_db->get_results("SELECT id, title FROM {$_db->getPrefix()}posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 300") ?: []
                );
            } catch (\Throwable $_e) {}
            ?>
            <select id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="form-select">
                <option value="">— Kein Beitrag —</option>
                <?php foreach ($ppRows as $pp): ?>
                <option value="<?php echo (int)$pp['id']; ?>"
                    <?php echo (string)$val === (string)$pp['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($pp['title']); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php else: ?>
            <label class="form-label" for="<?php echo $id; ?>"><?php echo htmlspecialchars($f['label']); ?></label>
            <input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>"
                   value="<?php echo htmlspecialchars($val); ?>" class="form-control">
        <?php endif; ?>

        <?php if ($desc !== ''): ?>
            <div class="form-text"><?php echo htmlspecialchars($desc); ?></div>
        <?php endif; ?>
    </div>
    <?php
}

// ── 5. Tab-Gruppen ───────────────────────────────────────────────────────────
$tabGroups = [
    'colors'     => ['🎨 Markenfarben' => ['primary_color','primary_dark','primary_mid','primary_light','accent_color','accent_hover'], '💻 Tech-Akzente' => ['accent_blue','accent_blue2','accent_teal','accent_teal_light'], '🖼️ Header-BGs' => ['bg_header1','bg_header2','bg_header3'], '📄 Seite & Dark' => ['bg_primary','bg_secondary','bg_dark'], '📝 Textfarben' => ['text_primary','text_secondary','text_muted'], '🧭 Navigation' => ['text_nav','text_nav_member','text_nav_main','text_nav_quicklinks','text_nav_dropdown','logo_suffix_color'], '🔲 Rahmen & Footer' => ['border_light','footer_bg','footer_bottom_bg','footer_border'], '✅ Status & Progress' => ['success_color','error_color','progress_bar_start','progress_bar_end']],
    'typography' => ['🔤 Schriftarten' => ['font_family_ui','font_family_brand','font_family_code'], '📏 Größen & Abstände' => ['font_size_base','font_size_post','line_height_base','line_height_post','font_weight_heading','font_weight_nav'], '📝 Card-Texte' => ['article_title_fontsize','tile_title_fontsize','article_excerpt_fontsize','article_excerpt_length','tile_excerpt_fontsize','tile_excerpt_length']],
    'layout'     => ['📐 Maße' => ['container_width','sidebar_width','border_radius','border_radius_md','content_gap','spacing_header_content','spacing_content_footer','spacing_sections'], '🍞 Breadcrumb' => ['show_breadcrumb','breadcrumb_on_posts','breadcrumb_on_pages'], '⚙️ Funktionen' => ['sidebar_position','enable_sticky_header','enable_progress_bar','enable_back_to_top','enable_dark_mode_toggle','enable_scroll_animations']],
    'header'     => ['🏷️ Logo & Marke' => ['logo_text_part1','logo_text_part2','logo_text_suffix','logo_url','show_logo_text_with_image','logo_max_height','logo_accent_color'], '👤 Member-Bar' => ['show_member_bar','member_bar_height'], '🧭 Hauptnav' => ['main_nav_height','show_search_bar','search_placeholder','show_rss_link'], '⚡ Quicklinks' => ['show_quicklinks','sub_bar_height']],
    'footer'     => ['🏷️ Brand & Text' => ['footer_brand_name','footer_tagline','footer_col2_title','footer_col3_title','footer_col4_title','copyright_text','show_footer_social'], '🍪 Cookie-Consent' => ['show_consent_banner','consent_text','consent_privacy_url'], '🔗 Network-Bar' => ['show_network_bar','network_bar_link1_label','network_bar_link1_url','network_bar_link2_label','network_bar_link2_url','network_bar_link3_label','network_bar_link3_url','network_bar_link4_label','network_bar_link4_url','network_bar_link5_label','network_bar_link5_url']],
    'homepage'   => ['📌 Repo-Card' => ['show_repo_card','repo_card_title','repo_card_description','repo_card_badge','repo_card_btn_text','repo_card_btn_url'], '📰 Artikel-Liste' => ['show_article_list','article_list_label','article_list_count','article_list_link_url','article_thumb_width','article_thumb_height','show_article_excerpt','show_article_meta','show_article_badge','show_meta_category','show_meta_date','show_meta_readtime'], '📋 Sidebar neben Liste' => ['show_list_sidebar','list_sidebar_width','list_sidebar_title','list_sidebar_content'], '🔧 Sidebar-Widgets' => ['sidebar_show_identity','sidebar_identity_logo_url','sidebar_identity_tagline','sidebar_identity_link_url','sidebar_show_projects','sidebar_project1_name','sidebar_project1_logo_url','sidebar_project1_desc','sidebar_project1_url','sidebar_project2_name','sidebar_project2_logo_url','sidebar_project2_desc','sidebar_project2_url','sidebar_show_status','sidebar_status_label','sidebar_status_services','sidebar_show_downloads','sidebar_downloads_label','sidebar_downloads_items','sidebar_show_social','sidebar_social_label','sidebar_show_notice','sidebar_notice_title','sidebar_notice_text','sidebar_notice_url','sidebar_notice_url_text','sidebar_show_featured_posts','sidebar_featured_posts_label','sidebar_featured_post_1','sidebar_featured_post_2','sidebar_featured_post_3'], '🗂️ Kategorie-Cards' => ['show_info_grid','info_card1_title','info_card1_text','info_card1_link_text','info_card1_link_url','info_card1_style','info_card2_title','info_card2_text','info_card2_link_text','info_card2_link_url','info_card2_style','show_info_card3','info_card3_title','info_card3_text','info_card3_link_text','info_card3_link_url','info_card3_badge','info_card3_style'], '🧱 Kachel-Grid' => ['show_tile_grid','tile_grid_label','tile_grid_count','tile_grid_columns','show_tile_excerpt','show_tile_category','show_tile_date','tile_grid_link_url'], '📡 RSS-Feeds' => ['show_feed_section','feed1_channel_id','feed1_count','feed2_channel_id','feed2_count'], '📏 Sektionen-Abstände' => ['spacing_repo_card','spacing_article_list','spacing_info_cards','spacing_tile_grid','spacing_rss_feeds']],
    'posts'      => ['🖼️ Hero & Meta' => ['post_hero_height','post_hero_width','show_post_hero','post_title_fontsize','show_post_meta','show_reading_time','reading_time_wpm'], '📖 Inhaltsverzeichnis' => ['show_toc','toc_sticky','toc_min_headings','toc_header_text'], '📌 Sidebar-Widgets' => ['show_sidebar_social','sidebar_social_header','show_sidebar_related','sidebar_related_header','related_count'], '🔗 Share-Buttons' => ['show_share_buttons','show_share_linkedin','show_share_twitter','show_share_email','show_share_copy'], '💻 Tech-Card' => ['show_tech_card','tech_card_header'], '💬 Kommentare & Tags' => ['show_comments','comments_header','comment_form_header','show_post_tags']],
    'pages'      => ['📄 Seiteneinstellungen' => ['show_page_title','page_title_fontsize','show_page_hero','show_page_updated_date','page_layout','show_page_sidebar','page_sidebar_show_nav','show_page_toc']],
    'social'     => ['🔗 Profile & URLs' => ['social_linkedin','social_github','social_twitter','social_mastodon','social_rss','social_youtube','social_xing'], '🏷️ Button-Labels' => ['social_label_linkedin','social_label_github','social_label_rss']],
    'advanced'   => ['🎨 Custom Code' => ['custom_css','custom_head_code','custom_footer_code'], '📊 Tracking & Cache' => ['google_analytics_id','cache_buster_css'], '🖼️ SEO & Social' => ['og_default_image']],
    'seo'         => ['🔍 SEO-Einstellungen' => ['meta_robots','canonical_self','og_site_name','og_type_default','twitter_card_type','structured_data','breadcrumb_schema','noindex_search','noindex_404']],
    'performance' => ['⚡ Performance-Einstellungen' => ['enable_photoswipe','lazyload_images','preconnect_fonts','preconnect_extra','defer_scripts','dns_prefetch']],
];

// Nav-Gruppen für die Sidebar
$navGroups = [
    null        => ['colors', 'typography', 'layout'],
    '🖥️ Design' => ['header', 'footer'],
    '📝 Inhalte' => ['homepage', 'posts', 'pages'],
    '⚙️ Sonstiges' => ['social', 'advanced', 'seo', 'performance'],
];
?>

<style>
    .phinit-customizer__color-group { max-width: 260px; }
    .phinit-customizer__color-input { max-width: 52px; padding: 2px; }
    .phinit-customizer__color-text { max-width: 130px; }
    .phinit-customizer__number-input { max-width: 140px; }
    .phinit-customizer__sticky-card { top: 1rem; }
    .phinit-customizer__nav-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; background: #f8fafc; border: none; }
    .phinit-customizer__nav-link { font-size: .875rem; }
    .phinit-customizer__export-title { font-size: .85rem; }
    .phinit-customizer__unsaved-hint { display: none; font-size: .85rem; }
    .phinit-customizer__shortcut-hint { font-size: .8rem; }
    .phinit-customizer__preset-card { background: #1e293b; border-color: #334155; }
    .phinit-customizer__preset-label { font-size: .8rem; font-weight: 600; color: #94a3b8; }
    .phinit-customizer__preset-btn--phinit { background: #1e3a5f; color: #e8a838; border-color: #2a4f7c; }
    .phinit-customizer__preset-btn--bluesteel { background: #1a2744; color: #60a5fa; border-color: #233b6e; }
    .phinit-customizer__preset-btn--greentech { background: #064e3b; color: #10b981; border-color: #047857; }
    .phinit-customizer__preset-btn--slate { background: #1e293b; color: #f59e0b; border-color: #334155; }
    .phinit-customizer__preset-btn--ruby { background: #7f1d1d; color: #f87171; border-color: #991b1b; }
    .phinit-customizer__preset-note { color: #64748b; }
    .phinit-customizer__menu-icon { font-size: 1.5rem; }
    .phinit-customizer__drawer { display: none; position: fixed; top: 0; right: 0; bottom: 0; z-index: 9050; width: min(900px, 96vw); flex-direction: column; background: #111827; box-shadow: -6px 0 32px rgba(0,0,0,.6); }
    .phinit-customizer__drawer-toolbar { display: flex; align-items: center; gap: .5rem; padding: .5rem .875rem; background: #0d1528; border-bottom: 1px solid #1e3a5f; flex-shrink: 0; }
    .phinit-customizer__toolbar-btn, .phinit-customizer__toolbar-link { background: none; border: 1px solid #334155; color: #94a3b8; border-radius: 4px; padding: .2rem .6rem; cursor: pointer; font-size: .85rem; text-decoration: none; }
    .phinit-customizer__device-switcher { display: flex; gap: .375rem; margin: 0 auto; }
    .phinit-customizer__device-btn { background: none; border: 1px solid #334155; color: #94a3b8; border-radius: 4px; padding: .25rem .65rem; cursor: pointer; font-size: .8rem; }
    .phinit-customizer__device-btn.active { background: #1e293b; color: #e2e8f0; }
    .phinit-customizer__drawer-body { flex: 1; overflow: auto; display: flex; justify-content: center; align-items: flex-start; background: #475569; padding: 4px; }
    .phinit-customizer__iframe { background: #fff; border: none; border-radius: 2px; height: calc(100vh - 60px); width: 1280px; max-width: 100%; transition: width .25s ease; }
    .phinit-customizer__drawer-label { padding: .25rem .875rem; background: #0d1528; font-size: .7rem; color: #475569; text-align: center; }
</style>

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Theme-Editor</div>
                <h2 class="page-title">🎨 Theme Customizer – CMS Phinit</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button type="button" id="preview-toggle-btn" class="btn btn-outline-secondary me-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 7a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z"/>
                        <path d="M7 20l10 0"/><path d="M9 16l0 4"/><path d="M15 16l0 4"/>
                    </svg>
                    Live-Vorschau
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <?php if ($alertMsg !== null): ?>
        <div class="alert alert-<?php echo htmlspecialchars($alertType); ?> alert-dismissible" role="alert">
            <?php echo $alertMsg; ?>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        <?php endif; ?>

        <form method="POST" id="customizer-form"
              action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
            <input type="hidden" name="action" value="save_theme_options">
            <input type="hidden" name="active_section" id="active_section_input" value="<?php echo htmlspecialchars($activeTab); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">

            <div class="row g-3">

                <!-- ── Linke Spalte: Tab-Navigation ── -->
                <div class="col-12 col-md-3 col-lg-2">
                    <div class="card sticky-top phinit-customizer__sticky-card">
                        <div class="list-group list-group-flush">
                            <?php foreach ($navGroups as $grpLabel => $tabs):
                                if ($grpLabel !== null): ?>
                                <div class="list-group-item py-1 px-3 phinit-customizer__nav-label">
                                    <?php echo htmlspecialchars($grpLabel); ?>
                                </div>
                                <?php endif;
                                foreach ($tabs as $tk):
                                    if (!isset($config[$tk])) { continue; }
                            ?>
                                <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $tk); ?>"
                                   class="list-group-item list-group-item-action py-2 px-3 phinit-customizer__nav-link<?php echo $activeTab === $tk ? ' active' : ''; ?>">
                                    <?php echo htmlspecialchars($config[$tk]['title']); ?>
                                </a>
                            <?php endforeach; endforeach; ?>
                        </div>
                    </div>

                    <!-- Export / Import -->
                    <div class="card mt-3">
                        <div class="card-header py-2">
                            <h4 class="card-title phinit-customizer__export-title">Export / Import</h4>
                        </div>
                        <div class="card-body p-3">
                            <form method="POST"
                                  action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
                                <input type="hidden" name="action" value="export_settings">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                                    ⬇️ Exportieren
                                </button>
                            </form>
                            <form method="POST" enctype="multipart/form-data"
                                  action="<?php echo htmlspecialchars(SITE_URL . '/admin/theme-editor?tab=' . $activeTab); ?>">
                                <input type="hidden" name="action" value="import_settings">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <input type="file" name="import_file" accept=".json" class="form-control form-control-sm mb-2">
                                <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                    ⬆️ Importieren
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ── Rechte Spalte: Tab-Inhalt ── -->
                <div class="col-12 col-md-9 col-lg-10">

                    <!-- Aktionsleiste -->
                    <div class="card mb-3">
                        <div class="card-body py-2 d-flex align-items-center gap-2 flex-wrap">
                            <button type="submit" form="customizer-form" name="action" value="save_theme_options"
                                    class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                                     stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"/>
                                    <circle cx="12" cy="15" r="2"/><polyline points="14 4 14 8 8 8 8 4"/>
                                </svg>
                                Speichern
                            </button>
                            <button type="submit" form="customizer-form" name="action" value="reset_theme_tab"
                                    class="btn btn-outline-secondary"
                                    onclick="return confirm('Alle Felder dieses Tabs auf Standardwerte zurücksetzen?');">
                                ↩️ Tab zurücksetzen
                            </button>
                            <span id="unsaved-hint" class="ms-auto text-warning phinit-customizer__unsaved-hint">
                                ⚠️ Ungespeicherte Änderungen
                            </span>
                            <span class="text-muted ms-auto phinit-customizer__shortcut-hint">Strg+S zum Speichern</span>
                        </div>
                    </div>

                    <!-- Tab-Inhalte -->
                    <?php
                    $currentGroups = $tabGroups[$activeTab] ?? [];
                    $tabSections   = $config[$activeTab]['sections'] ?? [];
                    // Für advanced: größere Textareas
                    if ($activeTab === 'advanced') {
                        foreach (['custom_css', 'custom_head_code', 'custom_footer_code'] as $_fk) {
                            if (isset($tabSections[$_fk])) { $tabSections[$_fk]['rows'] = 8; }
                        }
                    }
                    ?>

                    <?php if ($activeTab === 'colors'): ?>
                        <div class="card mb-3 phinit-customizer__preset-card" id="color-presets-card">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="phinit-customizer__preset-label">🎨 Schnell-Presets:</span>
                            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--phinit" data-preset="phinit">Phinit (Standard)</button>
                            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--bluesteel" data-preset="bluesteel">Blue Steel</button>
                            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--greentech" data-preset="greentech">Green Tech</button>
                            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--slate" data-preset="slate">Slate Dark</button>
                            <button type="button" class="btn btn-sm color-preset-btn phinit-customizer__preset-btn--ruby" data-preset="ruby">Ruby Red</button>
                            <small class="ms-auto phinit-customizer__preset-note">↑ Klick füllt Felder – danach Speichern nicht vergessen!</small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="row row-cards">
                    <?php foreach ($currentGroups as $groupTitle => $fieldKeys): ?>
                        <div class="col-12 col-xl-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h4 class="card-title"><?php echo htmlspecialchars($groupTitle); ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($fieldKeys as $fk):
                                        if (!isset($tabSections[$fk])) { continue; }
                                        $f   = $tabSections[$fk];
                                        $val = $customizer->get($activeTab, $fk, $f['default'] ?? '');
                                        phinit_render_field($activeTab, $fk, $f, $val);
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                    <!-- Hinweis: Menü-Einträge über Menü-Editor (nur bei header-Tab) -->
                    <?php if ($activeTab === 'header'): ?>
                    <div class="card mt-3 border-primary">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="text-primary phinit-customizer__menu-icon">📋</div>
                            <div>
                                <strong>Menü-Einträge</strong> (Hauptmenü, Quicklinks, Footer-Menüs) werden im
                                <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/menu-editor'); ?>">Menü-Editor</a>
                                verwaltet – hier nur Aussehen (Höhen, Farben, Sichtbarkeit).
                            </div>
                            <a href="<?php echo htmlspecialchars(SITE_URL . '/admin/menu-editor'); ?>"
                               class="btn btn-sm btn-primary ms-auto">Menü-Editor →</a>
                        </div>
                    </div>
                    <?php endif; ?>

                </div><!-- /.col (rechts) -->
            </div><!-- /.row -->
        </form>

    </div>
</div>

<!-- ── Live-Vorschau Drawer ──────────────────────────────────────────────── -->
<div id="px-drawer" aria-hidden="true"
     class="phinit-customizer__drawer">
    <div class="phinit-customizer__drawer-toolbar">
        <button id="px-close-btn" type="button"
          class="phinit-customizer__toolbar-btn"
                title="Schließen (Esc)">✕</button>
     <div class="phinit-customizer__device-switcher">
         <button type="button" class="px-dev-btn phinit-customizer__device-btn active" data-width="1280">🖥️ Desktop</button>
         <button type="button" class="px-dev-btn phinit-customizer__device-btn" data-width="768">📱 Tablet</button>
         <button type="button" class="px-dev-btn phinit-customizer__device-btn" data-width="375">📲 Mobil</button>
        </div>
        <button id="px-refresh-btn" type="button"
          class="phinit-customizer__toolbar-btn"
                title="Neu laden">⟳</button>
        <a href="<?php echo htmlspecialchars(SITE_URL); ?>/" target="_blank" rel="noopener noreferrer"
        class="phinit-customizer__toolbar-link"
           title="In neuem Tab öffnen">↗</a>
    </div>
    <div class="phinit-customizer__drawer-body">
        <iframe id="px-iframe" src=""
          class="phinit-customizer__iframe"
                title="Theme Live-Vorschau"></iframe>
    </div>
    <div id="px-label" class="phinit-customizer__drawer-label">
        Desktop (1280 px)
    </div>
</div>

<script>
(function () {
    'use strict';

    // Unsaved-Changes-Warnung + Strg+S
    const form    = document.getElementById('customizer-form');
    const hint    = document.getElementById('unsaved-hint');
    const section = document.getElementById('active_section_input');
    let   changed = false;

    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('change', markChanged);
            el.addEventListener('input',  markChanged);
        });
        form.addEventListener('submit', () => { changed = false; });
    }

    function markChanged() {
        if (!changed) {
            changed = true;
            if (hint) hint.style.display = 'inline';
        }
    }

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            if (form) form.requestSubmit();
        }
    });

    window.addEventListener('beforeunload', function (e) {
        if (changed) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Farbfeld: color-Picker ↔ Text-Input synchronisieren
    window.syncColor = function (cpId, txtId, hiddenId) {
        const cp  = document.getElementById(cpId);
        const txt = document.getElementById(txtId);
        const hid = document.getElementById(hiddenId);
        if (cp && txt) { txt.value = cp.value; }
        if (hid && cp) { hid.value = cp.value; }
        markChanged();
    };
    window.syncColorTxt = function (cpId, txtId, hiddenId) {
        const cp  = document.getElementById(cpId);
        const txt = document.getElementById(txtId);
        const hid = document.getElementById(hiddenId);
        const v   = txt ? txt.value : '';
        if (cp && /^#[0-9a-f]{6}$/i.test(v)) { cp.value = v; }
        if (hid) { hid.value = v; }
        markChanged();
    };

    // ── 1.5 Live-Vorschau Drawer ─────────────────────────────────────────────
    const pxDrawer = document.getElementById('px-drawer');
    const pxIframe = document.getElementById('px-iframe');
    const pxLabel  = document.getElementById('px-label');
    const pxDevBtns = document.querySelectorAll('.px-dev-btn');
    const SITE_ORIGIN = <?php echo json_encode(rtrim(SITE_URL, '/') . '/'); ?>;

    function pxOpen() {
        if (!pxDrawer) return;
        pxDrawer.style.display = 'flex';
        pxDrawer.removeAttribute('aria-hidden');
        if (pxIframe && !pxIframe.src) { pxIframe.src = SITE_ORIGIN; }
    }
    function pxClose() {
        if (!pxDrawer) return;
        pxDrawer.style.display = 'none';
        pxDrawer.setAttribute('aria-hidden', 'true');
    }
    function pxRefresh() {
        if (!pxIframe) return;
        const s = pxIframe.src; pxIframe.src = ''; pxIframe.src = s;
    }
    function pxSetDevice(w) {
        if (!pxIframe) return;
        pxIframe.style.width = w + 'px';
        const L = {1280:'Desktop (1280 px)', 768:'Tablet (768 px)', 375:'Mobil (375 px)'};
        if (pxLabel) pxLabel.textContent = L[w] || (w + ' px');
        pxDevBtns.forEach(b => {
            const active = +b.dataset.width === w;
            b.style.background = active ? '#1e293b' : 'none';
            b.style.color = active ? '#e2e8f0' : '#94a3b8';
        });
    }

    document.getElementById('preview-toggle-btn')?.addEventListener('click', pxOpen);
    document.getElementById('px-close-btn')?.addEventListener('click', pxClose);
    document.getElementById('px-refresh-btn')?.addEventListener('click', pxRefresh);
    pxDevBtns.forEach(b => b.addEventListener('click', () => pxSetDevice(+b.dataset.width)));
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && pxDrawer && pxDrawer.style.display !== 'none') pxClose();
    });

    // ── 1.7 Farb-Palette Presets ─────────────────────────────────────────────
    const COLOR_PRESETS = {
        phinit:    {primary_color:'#1e3a5f',primary_dark:'#0f2340',primary_mid:'#1a3255',primary_light:'#2a4f7c',accent_color:'#e8a838',accent_hover:'#d4922a',accent_blue:'#4a9eff',accent_blue2:'#2d7dd2',accent_teal:'#0d9488',accent_teal_light:'#14b8a6',bg_header1:'#111827',bg_header2:'#162030',bg_header3:'#0e1a28',bg_primary:'#ffffff',bg_secondary:'#f1f5f9',bg_dark:'#0a0f1a',text_primary:'#1e293b',text_secondary:'#4a5568',text_muted:'#7a8898',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#b0bec5',text_nav_dropdown:'#1e293b',logo_suffix_color:'#e8a838',border_light:'#dde3ea',footer_bg:'#0d1828',footer_bottom_bg:'#080d15',footer_border:'#2d7dd2',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#2d7dd2',progress_bar_end:'#e8a838'},
        bluesteel: {primary_color:'#1a2744',primary_dark:'#0d1a33',primary_mid:'#162140',primary_light:'#233b6e',accent_color:'#3b82f6',accent_hover:'#2563eb',accent_blue:'#60a5fa',accent_blue2:'#3b82f6',accent_teal:'#0ea5e9',accent_teal_light:'#38bdf8',bg_header1:'#0d1a33',bg_header2:'#111f3d',bg_header3:'#091528',bg_primary:'#f8fafc',bg_secondary:'#eff6ff',bg_dark:'#060d1a',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#e2e8f0',text_nav_member:'#e2e8f0',text_nav_main:'#e2e8f0',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#0f172a',logo_suffix_color:'#60a5fa',border_light:'#e2e8f0',footer_bg:'#0b1630',footer_bottom_bg:'#060e1e',footer_border:'#3b82f6',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#3b82f6',progress_bar_end:'#60a5fa'},
        greentech: {primary_color:'#064e3b',primary_dark:'#022c22',primary_mid:'#065f46',primary_light:'#047857',accent_color:'#10b981',accent_hover:'#059669',accent_blue:'#34d399',accent_blue2:'#10b981',accent_teal:'#0d9488',accent_teal_light:'#2dd4bf',bg_header1:'#022c22',bg_header2:'#0a3728',bg_header3:'#001a14',bg_primary:'#f0fdf4',bg_secondary:'#ecfdf5',bg_dark:'#01110b',text_primary:'#064e3b',text_secondary:'#065f46',text_muted:'#6b7280',text_nav:'#d1fae5',text_nav_member:'#d1fae5',text_nav_main:'#d1fae5',text_nav_quicklinks:'#6ee7b7',text_nav_dropdown:'#064e3b',logo_suffix_color:'#10b981',border_light:'#d1fae5',footer_bg:'#031c15',footer_bottom_bg:'#010e0a',footer_border:'#10b981',success_color:'#10b981',error_color:'#ef4444',progress_bar_start:'#10b981',progress_bar_end:'#2dd4bf'},
        slate:     {primary_color:'#1e293b',primary_dark:'#0f172a',primary_mid:'#1c2944',primary_light:'#334155',accent_color:'#f59e0b',accent_hover:'#d97706',accent_blue:'#818cf8',accent_blue2:'#6366f1',accent_teal:'#06b6d4',accent_teal_light:'#22d3ee',bg_header1:'#0f172a',bg_header2:'#1e293b',bg_header3:'#0b1120',bg_primary:'#ffffff',bg_secondary:'#f8fafc',bg_dark:'#060c16',text_primary:'#0f172a',text_secondary:'#334155',text_muted:'#64748b',text_nav:'#f1f5f9',text_nav_member:'#f1f5f9',text_nav_main:'#f1f5f9',text_nav_quicklinks:'#94a3b8',text_nav_dropdown:'#1e293b',logo_suffix_color:'#f59e0b',border_light:'#e2e8f0',footer_bg:'#0c1527',footer_bottom_bg:'#060b15',footer_border:'#6366f1',success_color:'#22c55e',error_color:'#ef4444',progress_bar_start:'#6366f1',progress_bar_end:'#f59e0b'},
        ruby:      {primary_color:'#7f1d1d',primary_dark:'#450a0a',primary_mid:'#6b1b1b',primary_light:'#991b1b',accent_color:'#ef4444',accent_hover:'#dc2626',accent_blue:'#f87171',accent_blue2:'#ef4444',accent_teal:'#f59e0b',accent_teal_light:'#fbbf24',bg_header1:'#1c0a0a',bg_header2:'#280d0d',bg_header3:'#140707',bg_primary:'#fffbfb',bg_secondary:'#fef2f2',bg_dark:'#0a0404',text_primary:'#1c0707',text_secondary:'#450a0a',text_muted:'#6b7280',text_nav:'#fee2e2',text_nav_member:'#fee2e2',text_nav_main:'#fee2e2',text_nav_quicklinks:'#fca5a5',text_nav_dropdown:'#450a0a',logo_suffix_color:'#f59e0b',border_light:'#fecaca',footer_bg:'#1a0707',footer_bottom_bg:'#0d0404',footer_border:'#ef4444',success_color:'#16a34a',error_color:'#dc2626',progress_bar_start:'#ef4444',progress_bar_end:'#f59e0b'}
    };

    document.querySelectorAll('.color-preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const preset = COLOR_PRESETS[btn.dataset.preset];
            if (!preset) return;
            Object.entries(preset).forEach(([key, val]) => {
                const cp  = document.getElementById('f_colors_' + key);
                const txt = document.getElementById('f_colors_' + key + '_txt');
                const hid = document.getElementById('colors_' + key);
                if (cp)  cp.value  = val;
                if (txt) txt.value = val;
                if (hid) hid.value = val;
            });
            markChanged();
        });
    });

    // ── 1.6 Font-Preview Widget ───────────────────────────────────────────────
    (function initFontPreviews() {
        const loaded = new Set();
        const GF = {
            'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'Open+Sans',
            'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
            'source-sans':'Source+Sans+3','nunito':'Nunito',
            'barlow-condensed':'Barlow+Condensed','roboto-condensed':'Roboto+Condensed',
            'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'Exo+2',
            'jetbrains-mono':'JetBrains+Mono','fira-code':'Fira+Code','source-code':'Source+Code+Pro'
        };
        const CN = {
            'barlow':'Barlow','inter':'Inter','roboto':'Roboto','open-sans':'"Open Sans"',
            'lato':'Lato','montserrat':'Montserrat','poppins':'Poppins',
            'source-sans':'"Source Sans 3"','nunito':'Nunito','system':'system-ui,sans-serif',
            'barlow-condensed':'"Barlow Condensed"','roboto-condensed':'"Roboto Condensed"',
            'oswald':'Oswald','rajdhani':'Rajdhani','exo2':'"Exo 2"',
            'jetbrains-mono':'"JetBrains Mono",monospace','fira-code':'"Fira Code",monospace',
            'source-code':'"Source Code Pro",monospace','cascadia':'"Cascadia Code",monospace',
            'system-mono':'monospace'
        };
        function loadFont(slug) {
            if (!GF[slug] || loaded.has(slug)) return;
            loaded.add(slug);
            const l = document.createElement('link');
            l.rel  = 'stylesheet';
            l.href = 'https://fonts.googleapis.com/css2?family=' + GF[slug] + ':wght@400;700&display=swap';
            document.head.appendChild(l);
        }
        ['f_typography_font_family_ui','f_typography_font_family_brand','f_typography_font_family_code'].forEach(fid => {
            const sel = document.getElementById(fid);
            if (!sel) return;
            const prev = document.createElement('div');
            prev.style.cssText = 'margin-top:.4rem;padding:.4rem .7rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;font-size:1rem;color:#1e293b;';
            prev.textContent   = 'AaBbCc 0123 – PowerShell & M365 Administration';
            sel.after(prev);
            (function update(v) { loadFont(v); prev.style.fontFamily = CN[v] || 'inherit'; })(sel.value);
            sel.addEventListener('change', e => {
                loadFont(e.target.value);
                prev.style.fontFamily = CN[e.target.value] || 'inherit';
            });
        });
    })();

})();
</script>
