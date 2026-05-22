# MedCare Pro – Theme für 365 CMS

**Version:** 1.0.3
**Autor:** PHINIT.DE / Andreas Hepp
**Lizenz:** GPL-2.0-or-later
**Requires PHP:** 8.4
**Requires 365CMS:** 3.0.0+
**Zielgruppe:** Arztpraxen, Kliniken, Therapeuten, Pflegedienste, Gesundheitsportale

---

## Identität

MedCare Pro ist ein medizinisches Praxis-Theme. Es folgt dem Grundsatz
**„Vertrauen durch Ruhe"**: ein tiefes Klinik-Teal als Marken-Anker, ein
mintgrüner Bestätigungs-Akzent, sowie warmes Amber für Hinweise und ein
medizinisches Rot, das **ausschließlich** für Notfall- und kritische
Signale reserviert ist. Eine editoriale Source-Serif-4 trägt
Disclaimer, Patienteninformationen und Hero-Subline – die UI bleibt in
einer ruhigen Source Sans 3.

Das Theme ist DSGVO/§ 203-StGB-orientiert (verschärfte
Default-Datenschutz-Posture für medizinische Websites) und nach
WCAG 2.2 AA gestaltet: 17 px Body-Standardgröße, sichtbare
:focus-visible-States, Schriftgrößen- & Hochkontrast-Toggle,
Notfall-Banner mit AA-konformen Farben, `prefers-reduced-motion`-Fallback.

### Farbpalette (Customizer-Defaults)

| Token | Wert | Verwendung |
|---|---|---|
| `--mc-primary` | `#0e5b6b` | Klinik-Anker-Teal, Buttons, Header-Akzente |
| `--mc-primary-hover` | `#0c4f5d` | Hover-/Aktiv-Zustand |
| `--mc-primary-ink` | `#082f38` | Footer, Hero-Hintergrund, dunkle Sektionen |
| `--mc-accent` | `#0f9d6a` | Bestätigung („Befund OK", Success-Alerts) |
| `--mc-warning` | `#b45309` | Hinweise (Amber, AA gegen Weiß) |
| `--mc-urgent` | `#b91c1c` | Notfall-Banner, ärztlicher Hinweis – nicht dekorativ |
| `--mc-paper` | `#f6f8fa` | Seitenhintergrund (sea-salt, nicht eisig) |
| `--mc-surface` | `#ffffff` | Cards |
| `--focus-ring` | `#0e5b6b` | Outline für :focus-visible |

Spezialgebiet-Badges (`mc-specialty-badge--cardio` etc.) erhalten je
eigene Vordergrund/Hintergrund-Paare, die einzeln gegen WCAG-AA geprüft sind.

---

## Dateistruktur

```
medcarepro/
├── functions.php           # Bootstrap, Asset-Pipeline, Customizer-Wiring, Helper
├── header.php              # DOCTYPE, <head>, sticky Header + optionaler Notfall-Banner
├── footer.php              # 4-spaltiger Footer, Disclaimer, Legal-Nav, Copyright
├── home.php                # Landing-Page (Hero, Fachgebiete, Termin-CTA, Trust, CTA)
├── index.php               # Fallback-Listing
├── page.php                # Statische Seiten (Prose-Content)
├── 404.php                 # Status-Seite (nicht gefunden)
├── error.php               # Generische Fehlerseite (50x)
├── blog.php                # Ratgeber-Übersicht mit Paginierung
├── blog-single.php         # Artikel mit Breadcrumb + medizinischem Hinweis
├── search.php              # Suchergebnisse (Ärzte + Artikel)
├── login.php               # CSRF-geschütztes Anmeldeformular
├── register.php            # Patient/Arzt-Registrierung mit CSRF + Honeypot
├── style.css               # WordPress-konformer Header, Tokens, Komponenten, Responsive
├── theme.json              # Manifest, Customizer-Schema, Templates-Registry
├── update.json             # Update-Feed
├── README.md
├── CHANGELOG.md
├── js/
│   └── navigation.js       # Mobile-Drawer, Suchpanel, A11y-Toggles, IntersectionObserver
└── partials/
    └── doctor-card.php     # Wiederverwendbare Arzt-Karte (Avatar, Badge, Termin-CTA)
```

---

## Konstanten & Hooks

| Konstante | Wert |
|---|---|
| `MEDCAREPRO_THEME_VERSION` | `1.0.3` |
| `MEDCAREPRO_THEME_SLUG` | `medcarepro` |

| Hook | Priorität | Aktion |
|---|---|---|
| `head` | 1 | `enqueueStyles()` – preload + stylesheet mit `?v=`-Cache-Bust |
| `head` | 5 | `outputGoogleFonts()` – nur tatsächlich gewählte Familien |
| `head` | 10 | `outputMetaTags()` – Description, og:url, theme-color, robots |
| `head` | 15 | `outputCustomStyles()` – Customizer → CSS-Variablen + custom_css |
| `before_footer` | 99 | `enqueueScripts()` – `navigation.js` defer + Cache-Bust |
| `init` / `cms_init` | 10 | `registerNavMenus()` – beide Hooks für v3-Bootstrap-Kompatibilität |
| `register_menu_locations` (Filter) | – | Menü-Locations für ThemeManager |

---

## Helper-Funktionen (`functions.php`)

| Funktion | Rückgabe | Zweck |
|---|---|---|
| `theme_is_logged_in()` | `bool` | CMS-Auth-Status |
| `theme_route_url(string $name)` | `string` | Theme-lokaler Routen-Resolver |
| `theme_nav_menu(string $location)` | `void` | Rendert ein `<ul>`-Navigationsmenü mit Active-State |
| `get_header()` / `get_footer()` | `void` | Template-Inkludes über ThemeManager |
| `mc_get_setting($sec, $key, $def)` | `mixed` | Sicherer Customizer-Lese-Shortcut |
| `mc_get_flash()` / `mc_set_flash()` | – | Session-basierte Flash-Messages |
| `mc_href(string $target)` | `string` | Normalisiert relative/absolute Links auf SITE_URL |
| `mc_site_url()` / `mc_site_title()` | `string` | Convenience-Wrapper |
| `mc_body_class(...$extra)` | `string` | Server-rendered Body-Klasse (sticky/no-sticky, has-emergency-banner, is-logged-in) – verhindert FOUC |
| `mc_safe_headline(string $raw)` | `string` | HTML-escaped Headline mit erlaubtem `<span class="hl">` |
| `mc_tel_sanitize(string $raw)` | `string` | Sicherer `tel:`-URI (nur `+` und Ziffern) |

---

## Template-Referenz

| Template | Aufruf | Erwartet |
|---|---|---|
| `home.php` | Route `/` | – (liest aus Customizer) |
| `page.php` | Jede statische CMS-Seite | `$page` (object) |
| `blog.php` | Route `/blog`, `/ratgeber` | optional `$posts`, `$total`, `$currentPage`, `$totalPages` |
| `blog-single.php` | Einzelner Blog-Beitrag | `$post` (object) |
| `search.php` | Route `/search?q=…` | GET-Parameter `q`, optional `$results` |
| `login.php` | Route `/login` | – |
| `register.php` | Route `/register?type=patient\|doctor` | – |
| `404.php` | Seite nicht gefunden | – |
| `error.php` | Serverfehler (50x) | optional `$errorCode`, `$errorMessage` |
| `partials/doctor-card.php` | Inkludiert wo benötigt | `$doctor` (array|object) |

---

## Customizer-Sektionen (`theme.json`)

| Sektion | Inhalt | Wirkt auf |
|---|---|---|
| `colors` | Primär-/Akzent-/Warning-/Urgent-Farben, Spezialgebiet-Paletten, Text-/Hintergrund-Tokens | CSS-Variablen, alle Komponenten |
| `typography` | Body/Heading/Medical-Text-Schriftart, Größe, Zeilenhöhe, Heading-Weight | `--mc-font-*`, `outputGoogleFonts()` |
| `layout` | Container-Breite, Content-Padding, Eckenradius, Sektionsabstand, Sticky-Header-Toggle | `--mc-max-width`, `--mc-radius`, `--mc-section-pad` |
| `header` | Logo-URL, Header-Höhe, Farben, Trennlinie, Notfall-Banner-Toggle/Text/Telefon | Header + emergency banner |
| `footer` | Hintergrund/Text/Link-Farben, Disclaimer, Copyright-Template | Footer |
| `buttons` | Radius, Padding, Schriftgewicht, Text-Transform | `.mc-btn` |
| `medical_hero` | Hero-Texte (Badge/Headline/Subline), CTA-Labels & URLs, Statistik-Leiste | `home.php` |
| `medical_content` | Section-Titel, Buchungstexte, Versicherungs-Labels, Notfall-Info, CTA-Texte | `home.php` |
| `dsgvo_medical` | DSGVO-Mode, Cookie-Banner, Datenschutz-Formulartext, Social-Sharing-Off, Impressum-Title | Footer, Formulare, Blog-Single |
| `accessibility` | Mindest-Schriftgröße, Font-Size-Toggle, Kontrast-Toggle, Vorlese-Hinweis | Header-Toggles, A11y |
| `advanced` | Custom CSS (gegen `</style>`-Injection gefiltert) | `outputCustomStyles()` |

---

## Responsive Breakpoints

| Breakpoint | Verhalten |
|---|---|
| `> 1024px` | Vollansicht, Hero-Stats mit Trennstrichen |
| `≤ 1024px` | Stats kompakter |
| `≤ 960px` | Booking-CTA einspaltig, Aktionen in Zeile |
| `≤ 768px` | Mobile-Nav-Drawer mit `aria-hidden`-Toggle, Header-Buttons reduziert, Specialty-Grid 3-spaltig |
| `≤ 480px` | Specialty-Grid 2-spaltig, Hero-Suche vertikal, Trust-Grid einspaltig, alles full-width |
| `≤ 360px` | Specialty-Grid 1-spaltig, Emergency-Phone bricht um |

---

## Barrierefreiheit (WCAG 2.2 AA)

- Skip-Link „Zum Inhalt springen" als erstes fokussierbares Element
- `role="banner" / navigation / main / contentinfo` auf allen Landmarks
- Body 17 px Standardgröße (über `--mc-font-size-base`), `--mc-font-size-min` als
  Sockel für `mc-large-text`
- `:focus-visible` mit 3 px Outline + `--focus-ring`-Token, `:focus:not(:focus-visible)`-Reset
- Suchpanel und Mobile-Drawer mit `aria-expanded` / `aria-controls` /
  `aria-hidden`-Synchronisation, Escape schließt, Outside-Click schließt,
  Focus kehrt zum Auslöser zurück
- Toggle-Buttons (`fontSizeToggle`, `contrastToggle`) tragen `aria-pressed`,
  Preferences werden pre-paint aus `localStorage` gelesen (kein FOUC)
- Dekorative SVG-Icons mit `focusable="false"` + `aria-hidden="true"`
- `prefers-reduced-motion: reduce` deaktiviert Reveal-Animation,
  `IntersectionObserver`-Fallback markiert Karten sofort als sichtbar
- Spezialgebiet-Badge-Paletten je AA-geprüftes fg/bg-Paar
- Touch-Targets ≥ 44 px (`.mc-btn`, `.mc-input`)
- `mc-high-contrast` schaltet auf Schwarz/Weiß mit 2 px Rändern

---

## DSGVO / § 203 StGB

- Notfall-Banner nur on-demand, `tel:`-Link wird über `mc_tel_sanitize()` auf `+` und Ziffern reduziert
- Footer-Disclaimer als Pflichthinweis-Slot (`footer.footer_disclaimer`)
- Impressum-Label „Ärztlicher Leiter / Verantwortlicher gem. § 5 TMG" über
  `dsgvo_medical.imprint_doctor_title`
- Login & Register mit CSRF-Token sowie Honeypot
- Custom-CSS wird defensiv gegen `</style>`-Injection gefiltert
- Datenschutz-Hinweis auf Registrierungsformularen aus `dsgvo_medical.privacy_form_text`
- Theme-Default deaktiviert Social-Sharing-Buttons

---

## Entwicklung

### Customizer-Wert lesen

```php
$primary = mc_get_setting('colors', 'primary_color', '#0e5b6b');
```

### URL bauen

```php
$href = mc_href('/aerzte');                 // → SITE_URL/aerzte
$href = mc_href('https://example.test');    // → unverändert (External)
$href = theme_route_url('booking');         // → SITE_URL/termin
```

### Arzt-Karte einbinden

```php
<?php
$doctor = [
    'id'              => 1,
    'name'            => 'Mustermann',
    'title'           => 'Dr. med.',
    'specialty'       => 'Allgemeinmedizin',
    'specialty_slug'  => 'general',
    'gkv'             => true,
    'pkv'             => true,
    'rating'          => 4.7,
    'review_count'    => 23,
    'location'        => 'München',
    'verified'        => true,
];
include THEME_PATH . 'medcarepro/partials/doctor-card.php';
?>
```

---

## Changelog

### 1.0.3 (2026-05-19) – Nachtrag
- **Eigene CHANGELOG-Datei ergänzt:** `CHANGELOG.md` dokumentiert den Healthcare-Re-Audit jetzt separat statt nur im README.
- **Doku-/Update-Sync:** README, Update-Feed und Changelog benennen die Detailänderungen an `partials/doctor-card.php`, Blog-/Such-/Login-/Register-Templates, Notfallbanner, A11y-Toggles und Customizer-Mapping als finalen Abschlussstand.
- **Version bleibt stabil:** Kein erneuter Versionssprung; `1.0.3` bleibt die Release-Version, der 19.05. ist als Nachtragsdatum für die fehlende Dokumentation geführt.

### 1.0.3 (2026-05-18) – Re-Audit
- **Design-Identität neu gesetzt:** Klinik-Anker-Teal (`#0e5b6b` / `#082f38`)
  als Markenanker statt generischem SaaS-Sky-Blue; medizinisches Rot
  (`#b91c1c`) ausschließlich für Notfälle reserviert; Source Serif 4 für
  editoriale Akzente (Hero-Subline, Disclaimer, DSGVO-Notes).
- **Spezialgebiet-System:** Eigene fg/bg-Paare je Fachrichtung,
  einzeln WCAG-AA-geprüft.
- **Asset-Pipeline:** `enqueueStyles()` und `enqueueScripts()` mit
  versionierter URL (`?v=MEDCAREPRO_THEME_VERSION`), `<link rel="preload">`
  + `<link rel="stylesheet">`. Hardcoded `<link rel="stylesheet">` aus
  `header.php` entfernt.
- **Customizer-Verkabelung vollständig:** Header (bg/text/border/height/logo-max),
  Footer (bg/text/link), Buttons (radius/padding/weight/transform),
  Layout (container/padding/radius/section), Typography (size/line-height/weight),
  Accessibility (`min_font_size_accessibility`), Spezialgebiet-Farben.
- **Google Fonts dynamisch:** URL listet nur tatsächlich gewählte Familien.
  Bei reinem System-Stack (z. B. Georgia) wird der Request übersprungen.
  `preconnect` zu `fonts.googleapis.com` + `fonts.gstatic.com` (crossorigin).
- **`has-emergency-banner` Body-Klasse:** Inline-`<style>`-Block in
  `header.php` entfernt; Header-Höhe wird in `style.css` über
  `body.has-emergency-banner` adjustiert.
- **Inline-Styles eliminiert:** Alle `style=""`-Attribute aus `home.php`,
  `blog.php`, `blog-single.php`, `login.php`, `register.php`, `search.php`,
  `page.php`, `index.php`, `404.php`, `error.php` und
  `partials/doctor-card.php` durch semantische CSS-Klassen ersetzt.
- **A11y-Härtung:** Mobile-Menu jetzt mit `aria-controls`, `aria-hidden`,
  Escape-Close, Outside-Click-Close, Focus-Restore. Search-Panel identisch.
  Font-Size- & Kontrast-Toggles werden pre-paint aus `localStorage`
  gelesen (kein FOUC mehr).
- **Sicherheit:** `mc_tel_sanitize()` für Notfall-Telefonnummern,
  `mc_safe_headline()` für kontrollierte Inline-Hervorhebungen, Custom-CSS
  defensiv gegen `</style>`-Injection gefiltert.
- **Neue Helper:** `theme_route_url`, `mc_href`, `mc_site_url`,
  `mc_site_title`, `mc_body_class`.
- **Version synchronisiert:** `theme.json`, `style.css` (WP-konformer Header
  mit `Requires PHP: 8.4`, License, Text Domain), `update.json` und
  `MEDCAREPRO_THEME_VERSION`-Konstante auf `1.0.3`.
- **README erneuert.**

### 1.0.2 (2026-05-17)
- v3-Kompatibilität: Menü-Registrierung zusätzlich über `cms_init` abgesichert.
- Customizer-Fixes: Key-Mapping in `functions.php` auf die tatsächlichen `theme.json`-Keys korrigiert.
- Sicherheit: Google-Fonts-URL escaped; Header-/CTA-Links konsistent.
- A11y: `aria-hidden`-Toggling für Suche, sichtbare `:focus-visible`-States, reduzierte Bewegung.

### 1.0.1 (2026-02-23)
- Fix: `theme_is_logged_in()`, `theme_nav_menu()`, `get_header()`, `get_footer()` implementiert.
- Fix: Customizer-Key-Mismatches (Font-Size-/Kontrast-Toggle, Hero-Stats, Doctor-Section-Title).
- Neu: Notfall-Banner, 4-spaltiger Footer mit Disclaimer und Legal-Nav.
- Neu: `home.php` Hero-Suchform, Fachgebiete-Grid, Versicherungsfilter, Termin-CTA, Trust-Grid.
- Neu: `style.css` Komponenten und Responsive-Breakpoints.
- Neu: `blog.php`, `blog-single.php`, `search.php`, `login.php`, `register.php`, `doctor-card.php`, `navigation.js`.

### 1.0.0
- Erstveröffentlichung: Healthcare- und Praxis-Theme.
