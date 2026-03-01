# 365Network Theme – Entwickler-Leitfaden

> Technische Architektur, Code-Konventionen und Entwicklungsreferenz für das 365Network-Theme.

---

## Inhaltsverzeichnis

- [1. Architektur-Überblick](#1-architektur-überblick)
- [2. Theme-Klasse (functions.php)](#2-theme-klasse-functionsphp)
- [3. Template-Hierarchie](#3-template-hierarchie)
- [4. CSS-Architektur & Design-System](#4-css-architektur--design-system)
- [5. JavaScript-Module](#5-javascript-module)
- [6. Customizer-Integration](#6-customizer-integration)
- [7. Homepage-Architektur](#7-homepage-architektur)
- [8. Dark-Mode-Implementierung](#8-dark-mode-implementierung)
- [9. Canvas-Netzwerk-Animation](#9-canvas-netzwerk-animation)
- [10. Performance-Optimierungen](#10-performance-optimierungen)
- [11. Sicherheits-Patterns](#11-sicherheits-patterns)
- [12. Checkliste vor dem Commit](#12-checkliste-vor-dem-commit)

---

## 1. Architektur-Überblick

```
┌─────────────────────────────────────────────────────────────────┐
│                        CMS Core                                 │
│  ThemeManager  │  ThemeCustomizer  │  Hooks  │  Database        │
├─────────────────────────────────────────────────────────────────┤
│                    365Network Theme                              │
│                                                                 │
│  functions.php ──→ IT_Expert_Network_Theme (Singleton)          │
│     ├── enqueueStyles()      → CSS <link> Tags                  │
│     ├── outputDerivedStyles() → <style id="cms-theme-derived">  │
│     ├── enqueueScripts()     → JS <script> Tags                 │
│     ├── registerMenuLocations() → Menü-Positionen               │
│     ├── seedDefaultMenus()   → Standard-Menüeinträge            │
│     ├── outputCookieBanner() → Cookie-Consent-Banner            │
│     └── renderSidebar*Widget() → Homepage-Default-Widgets       │
│                                                                 │
│  Templates:                                                     │
│     header.php → home.php / page.php / index.php → footer.php   │
│                                                                 │
│  Customizer:                                                    │
│     admin/customizer.php → 9 Tabs × ~100 Settings               │
│     theme.json → Setting-Definitionen + Defaults                 │
├─────────────────────────────────────────────────────────────────┤
│                        Plugins                                   │
│  Hook-Registrierung → home_sidebar_widget, home_stats, etc.     │
└─────────────────────────────────────────────────────────────────┘
```

### Schlüssel-Klassen & Services

| Klasse | Pfad | Zweck |
|---|---|---|
| `IT_Expert_Network_Theme` | `functions.php` | Theme-Bootstrap (Singleton) |
| `\CMS\ThemeManager` | Core | Template-Rendering, Asset-URLs |
| `\CMS\Services\ThemeCustomizer` | Core | Settings CRUD, CSS-Generierung |
| `\CMS\Hooks` | Core | Hook-System (Action + Filter) |
| `\CMS\Database` | Core | PDO-Wrapper, Prepared Statements |
| `\CMS\Auth` | Core | Authentifizierung |
| `\CMS\Security` | Core | CSRF, Sanitierung |

---

## 2. Theme-Klasse (functions.php)

### Singleton-Pattern

```php
final class IT_Expert_Network_Theme
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
        // Hook-Registrierung hier
    }
}

IT_Expert_Network_Theme::instance();
```

### Registrierte Hooks (Reihenfolge)

| Hook | Callback | Priorität | Zweck |
|---|---|---|---|
| `head` | `enqueueStyles()` | 10 | CSS-Dateien |
| `head` | `outputMetaTags()` | 10 | Mobile-Meta-Tags |
| `head` | `outputGoogleFonts()` | 5 | Google-Fonts-Links |
| `head` | `outputCustomStyles()` | 20 | Customizer-CSS (Core) |
| `head` | `outputDerivedStyles()` | 25 | Abgeleitete CSS-Overrides |
| `head` | `outputPreconnect()` | 1 | Resource-Hints |
| `head` | `outputCustomHeaderCode()` | 99 | SEO-Header-Code |
| `head` | `outputCustomizerHeadCode()` | 98 | Tracking-Code |
| `before_footer` | `enqueueScripts()` | 10 | JS-Dateien |
| `before_footer` | `outputCookieBanner()` | 10 | Cookie-Banner |
| `body_end` | `outputCustomizerFooterCode()` | 50 | Footer-Code |
| `register_menu_locations` | `registerMenuLocations()` | 10 | Menü-Positionen |
| `cms_init` | `seedDefaultMenus()` | 10 | Standard-Menüpunkte |
| `home_sidebar_widget` | `renderSidebarBookingWidget()` | 10 | Buchungs-Placeholder |
| `home_sidebar_widget` | `renderSidebarFeedWidget()` | 20 | Feed-Placeholder |
| `home_sidebar_widget` | `renderSidebarJobWidget()` | 30 | Job-Placeholder |

### CSS-Kaskade im Detail

```
Priorität 1:  outputPreconnect()        ← <link rel="preconnect">
Priorität 5:  outputGoogleFonts()       ← <link href="fonts...">
Priorität 10: enqueueStyles()           ← <link href="style.css">
Priorität 20: outputCustomStyles()      ← <style id="cms-theme-customizer"> (via Core)
Priorität 25: outputDerivedStyles()     ← <style id="cms-theme-derived"> (Theme-Overrides)
```

**Warum `outputDerivedStyles()` existiert:**

Der Core `ThemeCustomizer::generateCSS()` hat eine starre Zuordnung von Settings zu CSS-Variablen. Manche Mappings passen nicht zum 365Network-Design:

- `secondary_color` wird vom Core als `--primary-light` gemappt → Theme überschreibt mit dem tatsächlichen Customizer-Wert `primary_hover`
- `bg_color` wird dreifach gemappt → Theme setzt `--bg-secondary` explizit
- Abgeleitete Werte (Banner-Gradient, Footer-BG) werden dynamisch aus den Primärfarben berechnet

---

## 3. Template-Hierarchie

```
Anfrage an /
  → ThemeManager prüft: home.php existiert? → JA → home.php
  
Anfrage an /seite-slug
  → ThemeManager prüft: page.php existiert? → JA → page.php

Anfrage an /nicht-vorhanden
  → 404.php

Fehler:
  → error.php

Login/Register:
  → login.php / register.php

Fallback:
  → index.php
```

### Template-Einbindung

Jedes Template bindet Header und Footer ein:

```php
<?php
// Am Anfang:
$theme = \CMS\ThemeManager::instance();
$theme->render('header', ['pageTitle' => 'Mein Titel']);

// Am Ende:
$theme->render('footer');
```

Oder direkt:

```php
include THEME_DIR . 'header.php';
// ... Seiteninhalt ...
include THEME_DIR . 'footer.php';
```

---

## 4. CSS-Architektur & Design-System

### Datei: `style.css` (Haupt-Stylesheet)

Aufbau in Sektionen:

```
1.  Theme-Header-Kommentar
2.  CSS Reset & Custom Properties (:root)
3.  Base Styles (body, a, img, etc.)
4.  Utility Classes (.container, .btn, .sr-only)
5.  Layout Components
    5a. Site-Header & Navigation
    5b. Hero-Section
    5c. Search-Overlay
    5d. Stats-Bar
    5e. Dashboard Grid
    5f. Expert Cards
    5g. Event Cards
    5h. Company Cards
    5i. Sidebar Panels + Widget-Zones
    5j. Events Strip
6.  Page Components (Content, Auth, 404)
7.  Dark Mode Overrides
8.  Responsive Breakpoints
9.  Accessibility (High Contrast, Print)
```

### Design-Tokens (CSS Custom Properties)

```css
:root {
    /* Farben */
    --primary-color: #0c1526;
    --primary-dark: #060b14;
    --primary-light: #1a2744;
    --accent-color: #c8952e;
    --accent-light: #d4a94e;
    
    /* Hintergründe */
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --bg-tertiary: #f1f5f9;
    
    /* Texte */
    --text-color: #1e293b;
    --heading-color: #0f172a;
    --muted-color: #64748b;
    --text-light: #e2e8f0;
    
    /* Borders & Schatten */
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.07);
    --shadow-md: 0 4px 12px -1px rgb(0 0 0 / 0.12);
    
    /* Radien */
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 16px;
    
    /* Layout */
    --container-max-width: 1400px;
    --container-padding: 2rem;
    
    /* Buttons */
    --btn-primary-bg: #c8952e;
    --btn-primary-text: #ffffff;
    --btn-primary-hover: #d4a94e;
    --btn-radius: 8px;
    
    /* Z-Index-Skala */
    --z-header: 1000;
    --z-mobile-menu: 1100;
    --z-overlay: 1200;
    --z-modal: 1300;
    
    /* Transitions */
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast: all 0.15s ease;
}
```

### Breakpoints

| Breakpoint | Breite | Verhalten |
|---|---|---|
| XL | `> 1280px` | Standard 2-Spalten-Grid |
| LG | `≤ 1280px` | Schmalere Sidebar (280px) |
| MD | `≤ 1024px` | 1 Spalte, Burger-Menü |
| SM | `≤ 768px` | Reduzierter Padding |
| XS | `≤ 480px` | Stack-Layout |

### Layout-Varianten (Homepage)

```css
/* Standard: Sidebar rechts */
.dashboard-layout { grid-template-columns: 1fr 320px; }

/* Sidebar links */
.dashboard-layout--sidebar-left { grid-template-columns: 320px 1fr; }
.dashboard-layout--sidebar-left .dashboard-sidebar { order: -1; }

/* Volle Breite */
.dashboard-layout--full-width { grid-template-columns: 1fr; }
.dashboard-layout--full-width .dashboard-sidebar { display: none; }
```

---

## 5. JavaScript-Module

### navigation.js – Kern-Module

| Modul | Funktion | Trigger |
|---|---|---|
| `initStickyHeader()` | `.scrolled` Klasse bei `scrollY > 60` | `scroll` Event |
| `initBurgerMenu()` | Mobile-Menü Toggle + ARIA | `click` auf `.mobile-menu-toggle` |
| `initDarkMode()` | System-Präferenz + Toggle | `click` auf `#dark-mode-toggle` |
| `initBackToTop()` | Scroll-to-top Button | `scrollY > 400` |
| `initScrollAnimations()` | `[data-anim]` IntersectionObserver | DOM-Load |
| `initActiveNav()` | `.active` auf aktuellem Menüpunkt | Pathname-Match |
| `initFlashMessages()` | Auto-dismiss `[data-auto-dismiss]` | Timeout |

### Dark-Mode-Persistierung

```javascript
// Speicherung
localStorage.setItem('cms365-theme', 'dark'); // oder 'light'

// Wiederherstellung (vor DOMContentLoaded, im <head>)
const saved = localStorage.getItem('cms365-theme');
if (saved === 'dark' || (!saved && matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.body.classList.add('dark-mode');
}
```

### IIFE-Pattern (Pflicht)

```javascript
(function () {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', () => {
        initStickyHeader();
        initBurgerMenu();
        // ...
    });
    
    function initStickyHeader() { /* ... */ }
    function initBurgerMenu() { /* ... */ }
})();
```

---

## 6. Customizer-Integration

### Architektur

```
theme.json (Setting-Definitionen + Defaults)
    ↓
ThemeCustomizer (Core-Service, liest theme.json)
    ↓
admin/customizer.php (Admin-UI, liest $config Array)
    ↓
{prefix}theme_customizations (DB-Tabelle)
    ↓
functions.php (liest Settings, generiert CSS)
```

### Setting im Theme verwenden

```php
$customizer = new \CMS\Services\ThemeCustomizer();

// Einzelwert (Kategorie, Key, Default)
$showHero = $customizer->get('homepage', 'show_hero', true);

// Boolean-Auswertung (Checkbox)
$showHero = filter_var($showHero, FILTER_VALIDATE_BOOLEAN);

// Numerisch mit Clamping
$limit = max(1, min(12, (int)$customizer->get('homepage', 'experts_limit', 3)));
```

### Neues Setting hinzufügen (Schritt für Schritt)

1. **theme.json**: Setting in der passenden Kategorie definieren
2. **admin/customizer.php**: `$config['kategorie']['sections']['setting_key']` hinzufügen
3. **Template**: Setting auslesen und im HTML verwenden
4. **style.css**: Ggf. CSS-Variable/Klasse dafür anlegen

---

## 7. Homepage-Architektur

### Datenfluss

```php
// 1. Customizer-Settings laden
$customizer = new \CMS\Services\ThemeCustomizer();
$homepage = $customizer->getCategory('homepage');

// 2. Sichtbarkeit prüfen
$showHero = filter_var($homepage['show_hero'] ?? true, FILTER_VALIDATE_BOOLEAN);
$showStats = filter_var($homepage['show_stats_bar'] ?? true, FILTER_VALIDATE_BOOLEAN);
// ...

// 3. Limits clampen
$expertsLimit = max(1, min(12, (int)($homepage['experts_limit'] ?? 3)));

// 4. Layout-Klasse berechnen
$layout = $homepage['homepage_layout'] ?? 'sidebar-right';
$layoutClass = match ($layout) {
    'sidebar-left' => 'dashboard-layout--sidebar-left',
    'full-width'   => 'dashboard-layout--full-width',
    default        => '',
};

// 5. Daten aus DB laden (nur wenn Sektion sichtbar)
if ($showExperts) { /* ... */ }
if ($showEvents)  { /* ... */ }

// 6. Hooks auslösen (doAction / applyFilters)
$heroTitle = \CMS\Hooks::applyFilters('home_hero_title', $heroTitle);
$stats = \CMS\Hooks::applyFilters('home_stats', $stats);
```

### Sektion-Anatomie (Muster)

Jede Sektion in home.php folgt diesem Pattern:

```php
<?php if ($showExpertsSection): ?>
    <div class="dashboard-section" data-section="experts">
        <div class="section-header">
            <h2><?php echo htmlspecialchars($expertsSectionTitle); ?></h2>
            <a href="/experten" class="section-link">Alle Experten →</a>
        </div>
        
        <?php if (!empty($latestExperts)): ?>
            <div class="experts-grid">
                <?php foreach ($latestExperts as $expert): ?>
                    <!-- Expert Card -->
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php \CMS\Hooks::doAction('home_after_experts'); ?>
<?php endif; ?>
```

**Key-Patterns:**
- `if ($showSection)` → Customizer-kontrollierte Sichtbarkeit
- `data-section="..."` → CSS-Targeting und JS-Hooks
- `htmlspecialchars()` → immer bei dynamischen Titeln
- `!empty($data)` → leere Sektionen nicht rendern
- `doAction()` → Hook nach jeder Sektion

---

## 8. Dark-Mode-Implementierung

### CSS-Overrides

```css
body.dark-mode {
    --bg-primary: #1e293b;
    --bg-secondary: #0f172a;
    --bg-tertiary: #334155;
    --text-color: #e2e8f0;
    --heading-color: #f1f5f9;
    --border-color: #334155;
    --muted-color: #94a3b8;
}
```

### Komponenten-spezifische Dark-Mode-Regeln

Bestimmte Komponenten brauchen explizite Overrides:

```css
body.dark-mode .sidebar-panel { background: #020617; border: 1px solid #1e293b; }
body.dark-mode .hero-section { background: #020617; }
body.dark-mode .expert-card { background: #1e293b; border-color: #334155; }
body.dark-mode .form-control { background: #0f172a; border-color: #334155; }
```

### Toggle-Button

Im Header (`header.php`):

```html
<button id="dark-mode-toggle" class="dark-mode-toggle" aria-label="Farbmodus umschalten">
    <span class="dm-icon-light">☀️</span>
    <span class="dm-icon-dark">🌙</span>
</button>
```

---

## 9. Canvas-Netzwerk-Animation

Die Hero-Sektion enthält eine Canvas-basierte Netzwerk-Animation:

```html
<canvas id="hero-canvas" style="position:absolute;inset:0;pointer-events:none;"></canvas>
```

### Funktionsweise

- Zufällige Partikel (Punkte) bewegen sich langsam über das Canvas
- Partikel, die sich nahe genug sind, werden durch Linien verbunden
- Erzeugt einen „Netzwerk/Verbindung"-Effekt passend zum Thema
- **Deaktivierbar** über Customizer: `effects.show_canvas_animation`

### Performance

- `requestAnimationFrame()` für flüssige Animation
- Nur aktiv, wenn Canvas im Viewport sichtbar
- Partikelanzahl skaliert mit Canvas-Größe
- Bei `prefers-reduced-motion` automatisch deaktiviert

---

## 10. Performance-Optimierungen

### Implementierte Maßnahmen

| Maßnahme | Implementation |
|---|---|
| **Preconnect** | `outputPreconnect()` → Google Fonts Domains |
| **CSS Cache-Busting** | `filemtime()` als Query-Parameter |
| **JS defer** | `<script defer>` für nicht-kritische Scripts |
| **Lazy Loading** | `loading="lazy"` auf allen Bildern |
| **Passive Listeners** | `{ passive: true }` auf Scroll-Events |
| **Conditional Queries** | DB-Queries nur wenn Sektion sichtbar |
| **IntersectionObserver** | Scroll-Animationen nur bei Sichtbarkeit |

### Datenbankabfragen minimieren

```php
// ❌ FALSCH: Immer alle Sektionen laden
$experts = $db->execute("...")->fetchAll();
$events = $db->execute("...")->fetchAll();
$companies = $db->execute("...")->fetchAll();

// ✅ RICHTIG: Nur wenn die Sektion sichtbar ist
if ($showExperts) {
    $experts = $db->execute("...")->fetchAll();
}
```

---

## 11. Sicherheits-Patterns

### Ausgabe-Escaping

```php
// Text-Nodes
<?php echo htmlspecialchars($title); ?>

// Attribute
<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>

// Ganzzahlen
<?php echo (int)$id; ?>

// Vertrauenswürdiges HTML (nach strip_tags)
<?php echo $sanitized_html; ?>
```

### CSRF in Formularen

```php
<input type="hidden" name="csrf_token" 
       value="<?php echo \CMS\Security::instance()->generateToken('action_name'); ?>">
```

### Datenbank-Queries

```php
// ✅ Prepared Statement
$stmt = $db->prepare("SELECT * FROM {$prefix}experts WHERE id = ?");
$stmt->execute([$id]);

// ❌ VERBOTEN: String-Interpolation
$db->execute("SELECT * FROM experts WHERE id = $id");
```

---

## 12. Checkliste vor dem Commit

### PHP

- [ ] `declare(strict_types=1)` in jeder Datei
- [ ] `if (!defined('ABSPATH')) exit;` in jeder Datei
- [ ] Alle Ausgaben per `htmlspecialchars()` escaped
- [ ] Alle DB-Queries als Prepared Statements
- [ ] CSRF-Token in Formularen
- [ ] `$_POST`/`$_GET`-Werte sanitiert

### CSS

- [ ] Theme-Header-Kommentar in `style.css`
- [ ] Keine generischen Klassen (`.card`, `.button`)
- [ ] Dark-Mode-Overrides für neue Komponenten
- [ ] Responsive-Tests (480px, 768px, 1024px, 1280px)
- [ ] Keine `!important` (außer Print/SR)

### JavaScript

- [ ] Kein jQuery – nur Vanilla JS
- [ ] IIFE-Kapselung
- [ ] `'use strict'`
- [ ] Passive Event-Listener für Scroll
- [ ] ARIA-Attribute auf interaktiven Elementen

### Theme-Dateien

- [ ] `theme.json` mit korrekten Defaults
- [ ] `update.json` mit aktueller Version
- [ ] `style.css` Header-Version = `theme.json` Version
- [ ] Alle Hooks in `HOMEPAGE-HOOKS.md` dokumentiert
- [ ] Alle Settings in `CUSTOMIZER.md` dokumentiert
- [ ] Plugin-Integration in `PLUGIN-INTEGRATION.md` aktualisiert

### Accessibility

- [ ] `alt` auf allen Bildern
- [ ] `aria-label` auf Icon-only-Buttons
- [ ] `aria-expanded` auf Toggles
- [ ] Fokus-Management bei Modal/Menü
- [ ] `loading="lazy"` auf Bildern (außer above-the-fold)
- [ ] Externe Links: `target="_blank" rel="noopener noreferrer"`
