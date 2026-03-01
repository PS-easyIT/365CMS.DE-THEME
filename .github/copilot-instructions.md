# 365CMS Theme-Repository – GitHub Copilot Anweisungen

> Gilt für **alle Dateien** in diesem Repository.  
> Pfade beziehen sich auf das Verzeichnis `365CMS.DE-THEME/`.

---

## 1. Allgemeine Architektur-Konventionen

### 1.1 Repository-Struktur

```
365CMS.DE-THEME/
├── index.json              ← Theme-Registry (alle verfügbaren Themes)
├── cms-default/            ← Aktives Standard-Theme
├── 365Network/             ← Networking-Platform Theme
├── academy365/             ← Lernplattform Theme
├── buildbase/              ← Minimalist Builder Theme
├── business/               ← Firmen-Präsentation Theme
├── logilink/               ← IT & Logistik Theme
├── medcarepro/             ← Medizin & Gesundheit Theme
├── personalflow/           ← Portfolio Theme
└── technexus/              ← Tech & Startup Theme
```

### 1.2 Theme-Verzeichnis-Struktur (pro Theme)

```
themes/mein-theme/
├── theme.json          ← Pflicht: Metadaten, Design-Tokens, Customization
├── update.json         ← Pflicht: Versionsinformation für Auto-Updates
├── style.css           ← Pflicht: Haupt-CSS mit Theme-Header-Kommentar
├── functions.php       ← Pflicht: Theme-Klasse, Hooks, Setup
├── header.php          ← Pflicht: HTML-Head + Navigation
├── footer.php          ← Pflicht: Footer + Scripts
├── home.php            ← Pflicht: Homepage-Template
├── index.php           ← Pflicht: Fallback-Template
├── page.php            ← Empfohlen: Einzel-Seite
├── login.php           ← Empfohlen: Login-Seite
├── register.php        ← Empfohlen: Registrierungs-Seite
├── 404.php             ← Empfohlen: 404-Fehlerseite
├── error.php           ← Empfohlen: Generische Fehlerseite
├── js/
│   ├── navigation.js   ← Burger-Menü, Sticky Header, Dark Mode
│   └── theme.js        ← Theme-spezifisches JS (optional)
├── admin/
│   └── customizer.php  ← Theme-Customizer-Integration (optional)
├── partials/           ← Wiederverwendbare Template-Teile (optional)
└── screenshot.png      ← Vorschaubild (400×300 px, empfohlen)
```

### 1.3 Jede PHP-Datei

```php
<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}
```

### 1.4 Singleton-Pattern (Pflicht für Theme-Hauptklasse)

```php
final class MeinTheme
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        $this->init_hooks();
    }
}
MeinTheme::instance();
```

---

## 2. Hooks-System

**Immer** `CMS\Hooks` verwenden:

```php
// Head-Assets registrieren
\CMS\Hooks::addAction('head', [$this, 'enqueueStyles'], 10);
\CMS\Hooks::addAction('head', [$this, 'outputCustomStyles'], 20);

// Footer-Scripts registrieren
\CMS\Hooks::addAction('before_footer', [$this, 'enqueueScripts'], 10);

// Menüpositionen registrieren
\CMS\Hooks::addFilter('register_menu_locations', [$this, 'registerMenuLocations']);

// In Templates aufrufen
CMS\Hooks::doAction('head');           // Im <head>
CMS\Hooks::doAction('body_start');     // Nach <body>
CMS\Hooks::doAction('after_header');   // Nach Header
CMS\Hooks::doAction('before_footer');  // Vor Footer
CMS\Hooks::doAction('body_end');       // Vor </body>
```

**Standard-Hooks in Themes:**

| Hook | Wo | Zweck |
|------|-----|-------|
| `head` | `<head>` | CSS, Meta-Tags, Fonts einbinden |
| `body_start` | Nach `<body>` | Tracking-Pixel, Skip-Links |
| `after_header` | Nach Header | Banner, Breadcrumbs |
| `before_footer` | Vor Footer | Scripts, Cookie-Banner |
| `footer` | Im Footer | Footer-Widgets |
| `body_end` | Vor `</body>` | Plugin-Scripts |
| `home_content` | Homepage | Plugin-Inhalte auf Homepage |
| `main_nav` | Navigation | Zusätzliche Menü-Items |

---

## 3. Theme-Manager-API

```php
$theme = CMS\ThemeManager::instance();

// Theme-Pfad (Dateisystem)
$path = $theme->getThemePath();

// Theme-URL (für Assets)
$url = $theme->getThemeUrl();

// Template rendern
$theme->render('home', ['data' => 'value']);

// Asset-URL mit Cache-Busting
$cssUrl = $theme->getAssetUrl('css/style.css');

// Theme-Setting aus Customizer
$primaryColor = $theme->getSetting('color_primary', '#2563eb');
```

---

## 4. ThemeCustomizer-API

```php
use CMS\Services\ThemeCustomizer;

$customizer = new ThemeCustomizer();

// Einzelnes Setting laden
$color = $customizer->getSetting('colors', 'primary_color', '#2563eb');

// Gesamte Kategorie laden
$colors = $customizer->getCategory('colors');

// CSS generieren
$css = $customizer->generateCSS();
```

**Kategorien:** `colors`, `typography`, `layout`, `header`, `footer`, `buttons`, `performance`, `advanced`

---

## 5. Sicherheit

### 5.1 Ausgabe-Escaping (Pflicht)

```php
// Text-Nodes
<?php echo htmlspecialchars($value); ?>

// Attribute
<?php echo htmlspecialchars($value, ENT_QUOTES); ?>

// URLs
<?php echo htmlspecialchars($url); ?>

// Ganzzahlen
<?php echo (int)$id; ?>

// Vertrauenswürdiges HTML (z. B. aus WYSIWYG, nach strip_tags)
<?php echo $sanitized_html; ?>
```

### 5.2 CSRF-Token in Formularen

```php
<input type="hidden" name="csrf_token" 
       value="<?php echo CMS\Security::instance()->generateToken('login'); ?>">
```

### 5.3 Niemals rohe Benutzereingaben ausgeben

```php
// ❌ VERBOTEN
<?php echo $_GET['q']; ?>

// ✅ RICHTIG
<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>
```

---

## 6. CSS-Konventionen

### 6.1 Theme-Header in style.css

```css
/*
Theme Name: Mein Theme
Description: Kurze Beschreibung
Version: 1.0.0
Author: Euer Name
Author URI: https://example.com
*/
```

### 6.2 CSS Custom Properties

```css
:root {
    /* Brand Colors */
    --primary-color:    #1e3a5f;
    --primary-dark:     #0f2240;
    --primary-light:    #2563eb;
    --accent-color:     #e8a838;
    --secondary-color:  #64748b;

    /* Text */
    --text-primary:     #1e293b;
    --text-secondary:   #64748b;
    --text-light:       #94a3b8;

    /* Backgrounds */
    --bg-primary:       #ffffff;
    --bg-secondary:     #f1f5f9;
    --bg-tertiary:      #e2e8f0;

    /* Borders */
    --border-color:     #e2e8f0;

    /* Shadows */
    --shadow-sm:    0 1px 3px 0 rgb(0 0 0 / 0.07);
    --shadow-md:    0 4px 12px -1px rgb(0 0 0 / 0.12);

    /* Radius */
    --border-radius:    8px;
    --border-radius-lg: 16px;

    /* Transitions */
    --transition:       all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-fast:  all 0.15s ease;

    /* Layout */
    --container-width:  1280px;

    /* Z-Index Scale */
    --z-header:      1000;
    --z-mobile-menu: 1100;
    --z-overlay:     1200;
    --z-modal:       1300;
}
```

### 6.3 Schriftfamilie

```css
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}
```

### 6.4 Breakpoints

| Breakpoint | Breite | Verhalten |
|---|---|---|
| Desktop | `> 1024px` | Volle Navigation, Desktop-Layout |
| Tablet | `≤ 1024px` | Reduzierter Padding |
| Mobile | `≤ 768px` | Burger-Menü, vertikale Layouts |
| Small Mobile | `≤ 480px` | Minimaler Padding, Stack-Layouts |

### 6.5 Dark Mode

```css
body.dark-mode {
    --bg-primary:    #1e293b;
    --bg-secondary:  #0f172a;
    --text-primary:  #f1f5f9;
    --border-color:  #334155;
}
```

Persistiert via `localStorage` Key: `cms365-theme` (Werte: `'dark'` | `'light'`)

---

## 7. JavaScript-Konventionen

- **Kein jQuery** – ausschließlich Vanilla JavaScript (ES2020+)
- Alle Module in IIFE gekapselt: `(function(){...})()`
- Initialisierung nach `DOMContentLoaded`
- `<script defer>` für nicht-kritische Scripts
- Passive Event-Listener für Scroll-Events

### Standard-Module in navigation.js

| Modul | Funktion |
|-------|----------|
| `initStickyHeader()` | `.scrolled` Klasse bei `scrollY > 60` |
| `initBurgerMenu()` | Mobile-Menü Toggle mit ARIA |
| `initDarkMode()` | System-Präferenz + manueller Toggle |
| `initBackToTop()` | Scroll-to-top Button bei `scrollY > 400` |
| `initScrollAnimations()` | IntersectionObserver für `[data-anim]` |
| `initActiveNav()` | `.active` Klasse basierend auf Pathname |
| `initFlashMessages()` | Auto-dismiss für `[data-auto-dismiss]` |

---

## 8. Semantisches HTML

```html
<!-- ✅ RICHTIG -->
<header class="site-header">...</header>
<nav class="main-nav" aria-label="Hauptnavigation">...</nav>
<main>...</main>
<article>...</article>
<aside>...</aside>
<footer class="site-footer">...</footer>

<!-- ❌ FALSCH -->
<div class="header">...</div>
<div class="nav">...</div>
<div class="main">...</div>
```

---

## 9. Accessibility-Pflicht

- `alt`-Attribut auf allen Bildern (leer `alt=""` nur bei dekorativen)
- `aria-label` auf Icon-only-Buttons
- `aria-expanded` / `aria-hidden` für Burger-Menü
- `aria-current="page"` für aktive Navigation
- Fokus-Management bei Menü-Toggle (Escape → Fokus zurück)
- `loading="lazy"` auf allen Bildern (außer above-the-fold)
- Externe Links: `target="_blank" rel="noopener noreferrer"`

---

## 10. Namenskonventionen

| Element | Konvention | Beispiel |
|---------|-----------|---------|
| Theme-Verzeichnis | `kebab-case` | `mein-theme/` |
| Theme-Klasse | `PascalCase` | `MeridianCMSDefaultTheme` |
| CSS-Klassen | `kebab-case` mit Kontext-Prefix | `site-header`, `main-nav` |
| JS-Funktionen | `camelCase` | `initStickyHeader()` |
| PHP-Methoden | `camelCase` | `enqueueStyles()` |
| Konstanten | `UPPER_SNAKE_CASE` | `MERIDIAN_THEME_VERSION` |
| Template-Dateien | `kebab-case.php` | `blog-single.php` |
| Partials | `kebab-case.php` in `partials/` | `partials/sidebar.php` |

---

## 11. theme.json Pflichtfelder

```json
{
  "name": "Theme Name",
  "slug": "theme-slug",
  "version": "1.0.0",
  "author": "Autor",
  "description": "Beschreibung",
  "templates": {
    "home": "home.php",
    "page": "page.php",
    "404":  "404.php"
  },
  "partials": {
    "header": "header.php",
    "footer": "footer.php"
  }
}
```

---

## 12. Checkliste vor dem Commit

- [ ] `declare(strict_types=1)` und `ABSPATH`-Guard in jeder PHP-Datei
- [ ] `style.css` mit vollständigem Theme-Header-Kommentar
- [ ] `theme.json` mit korrekten Metadaten und Version
- [ ] `update.json` mit aktueller Version + Datum
- [ ] Alle Ausgaben per `htmlspecialchars()` escaped
- [ ] CSRF-Token in Formularen (Login, Register, Kontakt)
- [ ] Semantisches HTML (`<header>`, `<nav>`, `<main>`, `<article>`, `<footer>`)
- [ ] Accessibility: `alt`, `aria-label`, `aria-expanded`, Fokus-Management
- [ ] Responsive getestet (≤ 480px, ≤ 768px, ≤ 1024px, > 1024px)
- [ ] Dark Mode funktioniert (CSS-Variablen angepasst)
- [ ] Kein jQuery – nur Vanilla JS
- [ ] Keine Inline-Styles für statische Design-Werte
- [ ] `loading="lazy"` auf Bildern (außer above-the-fold)
- [ ] Hooks vorhanden: `head`, `body_start`, `after_header`, `before_footer`, `body_end`
- [ ] `index.json` im Repository-Root aktualisiert (bei neuem Theme)
