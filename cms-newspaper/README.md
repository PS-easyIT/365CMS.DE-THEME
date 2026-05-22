# CMS Newspaper Theme

**Version:** 1.0.0
**Ziel:** 365CMS v3.x.x · PHP 8.4
**Slug:** `cms-newspaper`
**Text-Domain:** `cms-newspaper`

Editoriales News-/Magazin-Theme – Broadsheet-Typografie (Playfair Display
Masthead-Serif + Source Sans 3 Body + IBM Plex Mono Kicker/Meta), Ink-
Schwarz als Anker, sparsam eingesetztes Signal-Rot als Kategorie- und
Breaking-Akzent, Rule-Lines statt Card-Shadows. Bewusst _kein_ generischer
SaaS-Indigo, _keine_ Glassmorphism-Tendenz.

## Sektionen der Startseite

`Hero/Repo-Card → Breaking-Stack → Info-Box-Grid → Archive-Grid → Feed-Grid`

Sticky 3-Bar-Masthead (Utility-Bar + Wordmark/Navigation + Themen-Schnell­
navigation), Search-Toggle mit Focus-Restore, Mobile-Drawer mit
`aria-hidden` und Escape-Close.

## Bootstrap & Hooks

- `\CMS\Hooks::addAction('head', …)` registriert Stylesheet, Google Fonts,
  Meta-Tags und Customizer-getriebene CSS-Variablen.
- `\CMS\Hooks::addAction('before_footer', …)` lädt `js/navigation.js` mit
  `defer`-Attribut und versionierter URL.
- Menü-Registrierung läuft auf BEIDEN Hooks `init` und `cms_init`
  (v3-Bootstrap-Kompatibilität) sowie über den Filter
  `register_menu_locations`.
- Konstante `CMSNEWSPAPER_THEME_VERSION` synchron mit `theme.json`,
  `style.css`-Header und `update.json`.

## Customizer-Schema (`theme.json` → `customization`)

| Gruppe          | Inhalt                                                     |
| --------------- | ---------------------------------------------------------- |
| `colors`        | Ink-Stufen, Papier/Surface, Akzent-Rot, Hairline-Rule, Live-Grün |
| `typography`    | Display/Body/Mono-Familie, Größe, Zeilenhöhe              |
| `layout`        | Container, Sektionsabstand, Eckenradius                   |
| `header`        | Logo, Sticky-Toggle, Utility-Bar/Themen-Bar/Search-Toggle, Statustext |
| `footer`        | Tagline, Copyright-Template, Partner-Strip-Toggle         |
| `buttons`       | Padding, Eckenradius, Text-Transform                      |
| `news_hero`     | Repo-Card-Kicker, Headline, Lead, CTA-Label/URL           |
| `news_content`  | Sektionsüberschriften, Info-Boxen, Feed-Headings, Live-Indikator |
| `advanced`      | `custom_css` (defensiv gefiltert gegen `</style>`-Injection) |

Menü-Positionen: `primary-nav`, `footer-nav`, `footer-legal`,
`topics-nav` (Themen-Schnellnavigation in der dritten Header-Leiste).

## Sicherheit & Robustheit

- Alle Asset-URLs (`style.css`, `js/navigation.js`, Google Fonts) werden
  HTML-escaped ausgegeben und mit versionierter Cache-Bust-Query versehen.
- `enqueueStyles()` liefert `<link rel="preload">` + `<link rel="stylesheet">`.
- `enqueueScripts()` setzt `defer`.
- `news_href()` normalisiert relative/absolute/Anchor-/`mailto:`/`tel:`-URLs.
- `news_brand_html()` erlaubt einzig den inneren `<span>`-Highlight im
  Wordmark (z. B. `PHIN<span>IT</span>.DE`); alle anderen Tags werden
  escaped.
- `theme_nav_menu()` validiert URLs (`FILTER_VALIDATE_URL`) und akzeptiert
  ansonsten nur relative Pfade und Anchors.
- `custom_css` wird gegen `</style>`-Injection gefiltert.
- Alle Boolean-Customizer-Werte (`enable_sticky_header`, `show_utility_bar`,
  `show_topics_bar`, `show_search`, `show_partner_strip`,
  `show_breaking_label`) werden ausnahmslos über
  `filter_var(…, FILTER_VALIDATE_BOOLEAN)` gelesen.
- Google Fonts laden nur die tatsächlich vom Customizer gewählten Familien
  und nutzen Preconnect zu `googleapis.com` und `gstatic.com` (crossorigin).

## Accessibility

- Skip-Link zu `#main-content`.
- `:focus:not(:focus-visible) { outline: none; }` + sichtbarer
  `*:focus-visible`-Ring mit `--focus-ring`-Token.
- `.news-focus-shadow` als Doppelring-Utility für Brand-Links, Suche,
  Toggles.
- `prefers-reduced-motion: reduce` schaltet Smooth-Scroll, Reveal-
  Animationen, Live-Dot-Pulse und Hover-Image-Scales ab.
- Header-Toggles (Suche, Mobile-Menü) tragen `type="button"`, `aria-label`,
  `aria-expanded` und `aria-controls`.
- Suche restauriert den ursprünglichen Fokus beim Schließen.
- Mobile-Drawer toggelt `aria-hidden`.
- Dekorative SVGs nutzen `aria-hidden="true"` und `focusable="false"`.

## Design-Identität

- **Anker:** Ink-Schwarz `#0a0b0c` – Druckschwarz für Masthead, Headlines,
  Repo-Card und Footer.
- **Akzent:** Signal-Rot `#e32d12` – ausschließlich für Kategorie-Badges,
  Breaking-Indikator, das Quadrat-Mark in Section-Headers und Card-Top-Rule
  der Hero-Repo-Karte. Keine generische SaaS-Indigo-Palette.
- **Surface:** Off-White `#f8f9fa` (Seite) + reines Weiß `#ffffff` (Karten).
  Erzeugt ein „Papier auf Druckmaschine“-Gefühl.
- **Editorial Trinity:**
  - **Playfair Display** als Display-Schrift – starke Masthead-Serif mit
    deutlichem Kontrast, geeignet für klassische Zeitungs-Headlines.
  - **Source Sans 3** als Body-Schrift – moderner humanistischer Sans, an
    digitalen Bildschirmen erprobt von Adobe Source und Mozilla.
  - **IBM Plex Mono** als Kicker-/Meta-Schrift – das technische
    Selbstverständnis eines IT-Portals wird sofort sichtbar.
- **Print-derivierte Hierarchie:** Kicker (uppercase mono mit Rule-Prefix)
  → Display-Headline → Lead → Meta-Line. Alle Sektionen werden durch eine
  2 px starke Ink-Bottom-Rule mit Akzent-Quadrat eingeleitet, nicht durch
  Card-Shadows.
- **Buttons:** Ink-Rechtecke mit 4 px Radius, Uppercase, Hover wechselt
  auf Signal-Rot. Keine Hochglanz-Pillen.
- **Breaking-/Live-Indikator:** kleiner pulsierender roter Punkt im
  Section-Header der Breaking-Sektion – sparsam, nicht über die ganze
  Seite gestreut, sofort abschaltbar per Customizer.
- **Responsive Walk:** 1440 → 1024 → 768 → 320 produziert
  4-Spalten-Footer / 3-Spalten-Grid → 3-Spalten-Footer / 2-Spalten-Grid →
  1-Spalten-Stack (Drawer aktiviert, Utility- und Topics-Bar versteckt) →
  Single-Column-Search-Form.

## Dateistruktur

```
cms-newspaper/
├── 404.php
├── CHANGELOG.md
├── error.php
├── footer.php
├── functions.php
├── header.php
├── home.php
├── index.php
├── js/
│   └── navigation.js
├── newspaper.html           (Original-Prototyp, dient als Designreferenz)
├── page.php
├── README.md
├── style.css
├── theme.json
└── update.json
```

## Migrationshinweis

Die Datei `newspaper.html` ist nach diesem Release **kein** aktiver
Templatekandidat mehr und wird nur noch als Designreferenz beibehalten.
Die produktiv ausgelieferte Startseite ist `home.php` und liest alle
relevanten Inhalte aus dem Customizer.
