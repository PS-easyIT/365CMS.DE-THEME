# 365Network Theme – Customizer Referenz

> Vollständige Dokumentation aller Customizer-Kategorien und Settings.
> Einstellbar unter **Admin → Design → Customizer** (`/admin/customizer.php`).

---

## Inhaltsverzeichnis

- [Übersicht](#übersicht)
- [1. 🎨 Farben (colors)](#1--farben-colors)
- [2. 🔤 Typografie (typography)](#2--typografie-typography)
- [3. 📐 Layout (layout)](#3--layout-layout)
- [4. 📌 Header (header)](#4--header-header)
- [5. 🦶 Footer (footer)](#5--footer-footer)
- [6. 🔘 Buttons (buttons)](#6--buttons-buttons)
- [7. 🏠 Startseite (homepage)](#7--startseite-homepage)
- [8. ✨ Effekte (effects)](#8--effekte-effects)
- [9. ⚙️ Erweitert (advanced)](#9-️-erweitert-advanced)
- [Technische Details](#technische-details)

---

## Übersicht

Der Customizer ist in **9 Kategorien** (Tabs) unterteilt. Jede Kategorie enthält eine Reihe von Settings, die über die ThemeCustomizer-API geladen werden.

**Zugriff im Code:**

```php
$customizer = new \CMS\Services\ThemeCustomizer();

// Einzelnes Setting
$value = $customizer->getSetting('colors', 'primary_color', '#0c1526');

// Gesamte Kategorie
$colors = $customizer->getCategory('colors');

// Kurzform im Theme (get-Methode)
$value = $customizer->get('homepage', 'hero_title', 'Standard-Titel');
```

---

## 1. 🎨 Farben (colors)

| Setting | Typ | Default | CSS-Variable | Beschreibung |
|---|---|---|---|---|
| `primary_color` | color | `#0c1526` | `--primary-color` | Haupt-Hintergrundfarbe (Hero, Sidebar-Panels) |
| `primary_hover` | color | `#1a2744` | `--primary-light` | Hover-Zustand der Primärfarbe |
| `accent_color` | color | `#c8952e` | `--accent-color` | Gold-Akzent (CTAs, Links, Highlights) |
| `accent_hover` | color | `#d4a94e` | `--accent-light` | Hover-Zustand der Akzentfarbe |
| `bg_color` | color | `#f1f5f9` | `--bg-secondary` | Seiten-Hintergrund (Body) |
| `card_bg` | color | `#ffffff` | `--bg-primary`, `--card-bg` | Karten-Hintergrund |
| `text_color` | color | `#1e293b` | `--text-color` | Primäre Textfarbe |
| `heading_color` | color | `#0f172a` | `--heading-color` | Überschriftenfarbe |
| `muted_color` | color | `#64748b` | `--muted-color` | Gedämpfte Textfarbe (Labels, Hints) |
| `border_color` | color | `#e2e8f0` | `--border-color` | Standard-Rahmenfarbe |
| `link_color` | color | `#c8952e` | `--link-color` | Link-Farbe (Standard = Akzent) |
| `link_hover_color` | color | `#d4a94e` | `--link-hover` | Link-Hover-Farbe |
| `nav_active_color` | color | `#c8952e` | `--nav-active-color` | Aktiver Navigationspunkt |

---

## 2. 🔤 Typografie (typography)

| Setting | Typ | Default | CSS-Variable | Beschreibung |
|---|---|---|---|---|
| `font_family_base` | select | `system` | `--font-family` | Basis-Schriftfamilie |
| `font_family_heading` | select | `system` | `--font-heading` | Überschriften-Schriftfamilie |
| `font_size_base` | number | `16` | `--font-size-base` | Basis-Schriftgröße in px |
| `line_height_base` | number | `1.65` | `--line-height-base` | Zeilenhöhe |
| `font_weight_body` | select | `400` | `--font-weight-body` | Schriftstärke Fließtext |

**Font-Family-Optionen:**

| Wert | Schriftstapel |
|---|---|
| `system` | `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif` |
| `inter` | `'Inter', sans-serif` |
| `poppins` | `'Poppins', sans-serif` |
| `open-sans` | `'Open Sans', sans-serif` |
| `roboto` | `'Roboto', sans-serif` |
| `lato` | `'Lato', sans-serif` |
| `georgia` | `'Georgia', 'Times New Roman', serif` |

---

## 3. 📐 Layout (layout)

| Setting | Typ | Default | CSS-Variable | Beschreibung |
|---|---|---|---|---|
| `container_width` | number | `1400` | `--container-max-width` | Max. Container-Breite in px |
| `border_radius` | number | `8` | `--radius-md` | Standard-Eckenradius in px |
| `border_radius_sm` | number | `6` | `--radius-sm` | Kleiner Eckenradius |
| `shadow_intensity` | select | `medium` | `--shadow-sm`, `--shadow-md` | Schatten-Intensität |

**Shadow-Intensität-Optionen:**

| Wert | `--shadow-sm` | `--shadow-md` |
|---|---|---|
| `none` | `none` | `none` |
| `light` | `0 1px 2px rgba(0,0,0,.04)` | `0 2px 8px rgba(0,0,0,.06)` |
| `medium` | `0 1px 3px rgba(0,0,0,.07)` | `0 4px 12px rgba(0,0,0,.12)` |
| `strong` | `0 2px 4px rgba(0,0,0,.1)` | `0 8px 24px rgba(0,0,0,.18)` |

---

## 4. 📌 Header (header)

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `header_bg` | color | `#0c1526` | Header-Hintergrundfarbe |
| `header_text_color` | color | `#ffffff` | Header-Textfarbe |
| `header_sticky` | checkbox | `true` | Sticky-Header bei Scroll |
| `show_login_btn` | checkbox | `true` | Login-Button anzeigen |
| `show_register_btn` | checkbox | `true` | Registrieren-Button anzeigen |

---

## 5. 🦶 Footer (footer)

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `footer_bg` | color | `#0c1526` | Footer-Hintergrundfarbe |
| `footer_text_color` | color | `#94a3b8` | Footer-Textfarbe |
| `footer_copyright` | text | `© 2025 365Network…` | Copyright-Text |

---

## 6. 🔘 Buttons (buttons)

| Setting | Typ | Default | CSS-Variable | Beschreibung |
|---|---|---|---|---|
| `btn_primary_bg` | color | `#c8952e` | `--btn-primary-bg` | Primärer Button-Hintergrund |
| `btn_primary_text` | color | `#ffffff` | `--btn-primary-text` | Primärer Button-Text |
| `btn_primary_hover` | color | `#d4a94e` | `--btn-primary-hover` | Primärer Button Hover |
| `btn_border_radius` | number | `8` | `--btn-radius` | Button-Eckenradius |
| `btn_font_weight` | select | `600` | `--btn-font-weight` | Button-Schriftstärke |

---

## 7. 🏠 Startseite (homepage)

**NEU in v3.0.0** – Vollständige Kontrolle über alle Homepage-Sektionen.

### Hero-Sektion

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `show_hero` | checkbox | `true` | Hero-Sektion anzeigen/verbergen |
| `hero_title` | text | `Führendes Verzeichnis für…` | Hero-Überschrift (filterbar via `home_hero_title`) |
| `hero_subtitle` | text | *(leer)* | Optionaler Untertitel (filterbar via `home_hero_subtitle`) |
| `show_hero_search` | checkbox | `true` | Suchformular in der Hero-Sektion |

### Statistik

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `show_stats_bar` | checkbox | `true` | Statistik-Leiste anzeigen |

### Experten-Sektion

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `show_experts_section` | checkbox | `true` | Experten-Grid anzeigen |
| `experts_section_title` | text | `Aktuelle Experten` | Überschrift über dem Experten-Grid |
| `experts_limit` | number | `3` | Anzahl Experten-Cards (1–12) |

### Events-Sektion

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `show_events_section` | checkbox | `true` | Events-Karten anzeigen |
| `events_section_title` | text | `Kommende Events & Konferenzen` | Überschrift über den Event-Cards |
| `events_limit` | number | `4` | Anzahl Event-Cards (1–12) |

### Firmen-Sektion

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `show_companies_section` | checkbox | `true` | Firmen-Cards anzeigen |
| `companies_section_title` | text | `Top Firmen im Fokus` | Überschrift über den Firmen-Cards |
| `companies_limit` | number | `4` | Anzahl Firmen-Cards (1–12) |

### Layout & Sidebar

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `show_sidebar` | checkbox | `true` | Sidebar anzeigen (Plugin-Widget-Zone) |
| `homepage_layout` | select | `sidebar-right` | Seitenlayout (siehe unten) |
| `show_events_strip` | checkbox | `true` | Events-Fussleiste anzeigen |

**Layout-Optionen:**

| Wert | Beschreibung | Grid |
|---|---|---|
| `sidebar-right` | Standard – Main links, Sidebar rechts | `1fr 320px` |
| `sidebar-left` | Sidebar links, Main rechts | `320px 1fr` |
| `full-width` | Keine Sidebar, volle Breite | `1fr` |

---

## 8. ✨ Effekte (effects)

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `enable_animations` | checkbox | `true` | Scroll-Animationen aktivieren |
| `animation_style` | select | `fade-up` | Animations-Stil |
| `animation_duration` | number | `600` | Animationsdauer in ms |
| `show_canvas_animation` | checkbox | `true` | Canvas-Netzwerk-Animation im Header |
| `enable_smooth_scroll` | checkbox | `true` | Smooth Scrolling |

---

## 9. ⚙️ Erweitert (advanced)

| Setting | Typ | Default | Beschreibung |
|---|---|---|---|
| `custom_css` | textarea | *(leer)* | Benutzerdefiniertes CSS |
| `custom_head_code` | textarea | *(leer)* | Code im `<head>` (Tracking, Meta) |
| `custom_footer_code` | textarea | *(leer)* | Code vor `</body>` (Analytics) |

---

## Technische Details

### CSS-Kaskade

Die Customizer-Werte werden in folgender Reihenfolge als CSS angewendet:

```
1. style.css (statische Defaults)
   ↓
2. <style id="cms-theme-customizer"> (Core generateCSS(), Priorität 20)
   ↓
3. <style id="cms-theme-derived"> (Theme outputDerivedStyles(), Priorität 25)
   ↓
4. <style> custom_css aus Customizer (Priorität 30)
```

### Datenbank-Speicherung

Customizer-Einstellungen werden in der Tabelle `{prefix}theme_customizations` gespeichert. Jede Kategorie wird als JSON-serialisiertes Objekt abgelegt:

| Spalte | Beispielwert |
|---|---|
| `category` | `homepage` |
| `settings` | `{"show_hero":true,"hero_title":"Mein Titel",...}` |

### Live-Vorschau

Der Customizer unterstützt eine Echtzeit-Vorschau per JavaScript (kein Seiten-Reload). Farb- und Typografie-Änderungen werden direkt auf dem Preview-Iframe angewendet.

### Settings im PHP-Code lesen

```php
$customizer = new \CMS\Services\ThemeCustomizer();

// Methode 1: Einzelwert mit Fallback
$showHero = $customizer->get('homepage', 'show_hero', true);

// Methode 2: Gesamte Kategorie
$homepage = $customizer->getCategory('homepage');
$heroTitle = $homepage['hero_title'] ?? 'Fallback-Titel';

// Methode 3: getSetting (gleichwertig mit get)
$limit = $customizer->getSetting('homepage', 'experts_limit', 3);
```

### Settings in JavaScript (Customizer-Preview)

```javascript
// Im Customizer-Preview werden Änderungen per postMessage übertragen:
window.addEventListener('message', (e) => {
    if (e.data.type === 'customizer-update') {
        const { category, key, value } = e.data;
        // Wert anwenden...
    }
});
```
