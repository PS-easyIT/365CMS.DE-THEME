# PersonalFlow Theme

Version: 1.0.1
Target: 365CMS v3.x.x, PHP 8.4
Constant: `PERSONALFLOW_THEME_VERSION`

PersonalFlow ist ein HR- und Personalvermittlungs-Theme für 365CMS. Es ist
für Personalagenturen, Recruiting-Plattformen und Karriere-Portale gebaut
und macht eine Sache explizit: den Status eines Menschen in einer
Recruiting-Pipeline.

## Designprofil

- **Anker**: warmes Amber (`#b45309`) statt des branchenüblichen
  SaaS-Indigo / -Violett. Der Amber-Ton liest sich menschlich und
  zugewandt – passend für eine Plattform, auf der Karriere-Entscheidungen
  getroffen werden – und ist mit Weiß WCAG-AA-tauglich (4.9:1).
- **Akzent**: Teal `#0f766e` als komplementärer „Growth"-Akzent –
  reserviert für aktive Pipeline-Punkte und den Hero-Live-Indikator.
- **Hero**: zweispaltige redaktionelle Komposition mit Copy + CTAs links,
  einer Hero-Aside (Talent-Card-Stack) rechts und einem 5-stufigen
  Pipeline-Ribbon direkt unter dem Hero – kein zentriertes
  Headline-CTA-Klischee.
- **Pipeline-Status-System**: sechs semantische Stufen mit eigenen
  Vordergrund-/Hintergrund-Paaren, einzeln gegen `#ffffff` AA-konform:

  | Stage | Foreground | Background |
  |---|---|---|
  | Beworben (`applied`) | `#475569` | `#e2e8f0` |
  | Screening (`screened`) | `#b45309` | `#fef3c7` |
  | Interview (`interview`) | `#1d4ed8` | `#dbeafe` |
  | Angebot (`offer`) | `#6d28d9` | `#ede9fe` |
  | Eingestellt (`hired`) | `#15803d` | `#dcfce7` |
  | Archiviert (`archived`) | `#57534e` | `#f5f5f4` |

- **Typografie**: Nunito Sans (Body, default), Nunito (Headlines –
  freundlich, rund), JetBrains Mono (KPI-Zahlen, Gehaltsangaben,
  Pipeline-Counts). Stats werden mit `font-feature-settings: "tnum","lnum"`
  in tabellarische Ziffern gerendert – Recruiter:innen können
  KPI-Spalten vergleichen.
- **Person-Card-Pattern**: Avatar (Image oder Initialen-Plate), Name,
  Rolle, Standort, Stage-Badge, Skill-Chips, Action-Row (Profil ansehen
  / Kontakt aufnehmen). Wiederverwendbar über
  `partials/candidate-card.php`.

## Dateistruktur

```
personalflow/
├── functions.php           # Bootstrap, Asset-Pipeline, Customizer, Helper-Set
├── header.php              # DOCTYPE, <head>, sticky Header, Mobile-Drawer, Search-Panel
├── footer.php              # 4-spaltiger Footer, footer-nav, footer-legal
├── home.php                # Hero, Pipeline-Ribbon, KPI-Strip, Talent-Pool, Job-Board, CTA
├── index.php               # Beitrags-Fallback
├── page.php                # Statische CMS-Seiten (Prose)
├── 404.php                 # Nicht-gefunden-Seite mit Direktlink zu /jobs
├── error.php               # Generische 50x-Seite
├── style.css               # WordPress-konformer Header, Tokens, Komponenten, Responsive
├── theme.json              # Manifest + Customizer-Schema
├── update.json             # Update-Feed
├── README.md
├── CHANGELOG.md
├── js/
│   └── navigation.js       # Mobile-Drawer, Search-Toggle, IntersectionObserver-Reveal
└── partials/
    └── candidate-card.php  # Wiederverwendbare Talent-Card
```

## Konstanten & Hooks

| Konstante | Wert |
|---|---|
| `PERSONALFLOW_THEME_VERSION` | `1.0.1` |
| `PERSONALFLOW_THEME_SLUG` | `personalflow` |

| Hook | Priorität | Aktion |
|---|---|---|
| `head` | 1  | `enqueueStyles()` – preload + stylesheet mit `?v=`-Cache-Bust |
| `head` | 5  | `outputGoogleFonts()` – nur tatsächlich gewählte Familien |
| `head` | 10 | `outputMetaTags()` – Description, og:url, theme-color, robots |
| `head` | 15 | `outputCustomStyles()` – Customizer → CSS-Variablen + custom_css |
| `before_footer` | 99 | `enqueueScripts()` – `navigation.js` defer + Cache-Bust |
| `init` / `cms_init` | 10 | `registerNavMenus()` – beide Hooks für v3-Bootstrap-Kompatibilität |
| `register_menu_locations` (Filter) | – | Menü-Locations für ThemeManager |

## Helper-Funktionen (`functions.php`)

| Funktion | Rückgabe | Zweck |
|---|---|---|
| `theme_is_logged_in()` | `bool` | CMS-Auth-Status |
| `theme_route_url(string $name)` | `string` | Theme-lokaler Routen-Resolver |
| `theme_nav_menu(string $location)` | `void` | Rendert ein `<ul>`-Navigationsmenü mit Active-State |
| `get_header()` / `get_footer()` | `void` | Template-Inkludes über `ThemeManager` |
| `pf_get_setting($sec, $key, $def)` | `mixed` | Sicherer Customizer-Lese-Shortcut |
| `pf_get_flash()` / `pf_set_flash()` | – | Session-basierte Flash-Messages |
| `pf_href(string $target)` | `string` | Normalisiert relative/absolute Links auf SITE_URL |
| `pf_site_url()` / `pf_site_title()` | `string` | Convenience-Wrapper |
| `pf_body_class(...$extra)` | `string` | Server-rendered Body-Klasse (`pf-body` + Modifier) |
| `pf_safe_headline(string $raw)` | `string` | HTML-escaped Headline mit erlaubtem `<span class="hl">` |
| `pf_stage_label(string $stage)` | `string` | Customizer-Label zu Pipeline-Stage-Slug |

`theme_route_url` kennt: `home`, `search`, `login`, `register`, `member`,
`jobs`, `candidates`, `employers`, `pipeline`, `pricing`, `imprint`,
`privacy`, `terms`, sonst SITE_URL + Pfad.

## Customizer-Sektionen

| Sektion | Inhalt | Wirkt auf |
|---|---|---|
| `colors` | Brand- und Akzentfarben, Pipeline-Stage-Paare, Flächen-/Text-/Border-Tokens, Status (success/warning/error) | CSS-Variablen `--pf-*`, alle Komponenten |
| `typography` | Body/Heading/Stats-Schriftart, Basis-Schriftgröße, Zeilenhöhe, Heading-Weight | `--pf-font-*`, `outputGoogleFonts()` |
| `layout` | Container-Breite, Padding, Eckenradius, Sektionsabstand, Sticky-Header-Toggle | `--pf-max-width`, `--pf-radius`, `--pf-section-pad` |
| `header` | Logo, Header-Farben, Höhe, Logo-Max, Shadow-Toggle, Such-Toggle + Platzhalter | Header + Search-Panel |
| `footer` | Hintergrund / Text / Link, Beschreibung, Pipeline-/Arbeitgeber-Spalten-Toggle, Copyright-Template | Footer |
| `buttons` | Radius, Padding, Schriftgewicht, Text-Transform | `.pf-btn` |
| `hr_hero` | Hero-Badge, Headline (`<span class="hl">` erlaubt), Subline, CTA-Labels/URLs (für Kandidaten + Arbeitgeber), Pipeline-Ribbon-Toggle, Aside-Titel | `home.php` |
| `pipeline` | Stage-Labels (Beworben → Screening → Interview → Angebot → Eingestellt → Archiv) + Beispiel-Counts für das Ribbon | `home.php`, `partials/candidate-card.php` |
| `hr_content` | Sektions-Titel und -Subtitles, Salary-/Remote-/Available-Labels, KPI-Texte (4 Kennzahlen mit Label + Wert), CTA-Texte | `home.php` |
| `advanced` | Custom CSS (gegen `</style>`-Injection gefiltert) | `outputCustomStyles()` |

Jeder in `theme.json` deklarierte Schlüssel wird tatsächlich als
CSS-Variable ausgegeben oder im Template gelesen – keine Dead-Keys.

## Asset-Pipeline

- `enqueueStyles()` (`head:1`) emittiert `<link rel="preload" as="style">`
  und `<link rel="stylesheet">` für
  `style.css?v=PERSONALFLOW_THEME_VERSION`. URLs sind
  `htmlspecialchars`-escaped.
- `outputGoogleFonts()` (`head:5`) baut die Family-URL dynamisch nur aus
  Customizer-Auswahlen. Bei reinen System-Stacks (`system-mono`) wird
  der Request übersprungen. `preconnect` zu `fonts.googleapis.com`
  und `fonts.gstatic.com` (crossorigin) wird nur ausgegeben, wenn
  überhaupt eine Web-Font gebraucht wird.
- `outputMetaTags()` (`head:10`) – Description, OG-URL, Theme-Color,
  Robots.
- `outputCustomStyles()` (`head:15`) – CSS-Variablen aus Customizer +
  Custom-CSS (regex-gefiltert gegen `</style>`).
- `enqueueScripts()` (`before_footer:99`) – defer + Cache-Bust.

## Sicherheit & Accessibility

- Alle externen Asset-URLs (Google Fonts, Theme-CSS, Navigation-JS) sind
  `htmlspecialchars`-escaped.
- Boolean-Customizer-Werte gehen durch `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.
- `custom_css` wird via `sanitizeCustomCss()` gegen `</style>`-Injection
  regex-gefiltert.
- Skip-Link auf `#main` als erstes fokussierbares Element.
- `:focus:not(:focus-visible) { outline: none }` + sichtbarer
  `:focus-visible`-Ring mit `--focus-ring`-Token, plus
  `.pf-focus-shadow`-Utility für Buttons / Links / Inputs.
- Mobile-Drawer und Search-Panel mit `aria-hidden` / `aria-expanded` /
  `aria-controls`-Synchronisation, Focus-Restore an den Opener, Escape-
  und Outside-Click-Schluss.
- Body-Scroll-Lock bei offenem Drawer.
- Reduced-Motion-Aware: keine Reveal-Transitions und keine
  Hover-`translateY` wenn `prefers-reduced-motion: reduce`; JS fällt auf
  sofortige Sichtbarkeit zurück.
- Dekorative SVG-Icons mit `focusable="false"` + `aria-hidden="true"`.
- Mobile- und Search-Toggle deklarieren `type="button"`,
  `aria-expanded`, `aria-controls`.
- Touch-Targets ≥ 44 × 44 px (`.pf-btn`, `.pf-icon-btn`,
  Search-Input/Button).

## Pipeline-Stages – Anwendung

```php
<?php $candidate = [
    'name'        => 'Lena Brandt',
    'role'        => 'Senior Product Designer',
    'location'    => 'Berlin',
    'stage'       => 'interview',   // applied|screened|interview|offer|hired|archived
    'skills'      => ['Figma', 'Design Systems'],
    'available'   => true,
    'profile_url' => '/kandidaten/lena-brandt',
];
include THEME_PATH . 'personalflow/partials/candidate-card.php';
```

Stage-Badge ohne Karten-Wrapper:

```html
<span class="pf-stage-badge pf-stage-badge--interview">Interview</span>
```

## Templates

- `home.php` – Hero (Copy + Talent-Aside) → Pipeline-Ribbon → KPI-Strip
  → Talent-Pool-Grid → Job-Board-Grid → CTA. Alle Inhalte aus
  Customizer; Demo-Datensätze stehen inline und sind klar als
  Platzhalter markiert.
- `index.php` / `page.php` – generische Beiträge- bzw. Seitenansicht,
  Prose-Layout, keine Inline-Styles.
- `404.php` – Routet zur Startseite und zu `/jobs`.
- `error.php` – Generische 50x-Seite mit Error-Code in Stats-Font.
- `partials/candidate-card.php` – Wiederverwendbare Talent-Card.

## Versionsabgleich

- `theme.json` → `"version": "1.0.1"`
- `style.css` → `Version: 1.0.1`, `Requires PHP: 8.4`,
  `License: GPL-2.0-or-later`, `Text Domain: personalflow`
- `update.json` → `"version": "1.0.1"`, `"min_cms_version": "3.0.0"`
- `functions.php` → `define('PERSONALFLOW_THEME_VERSION', '1.0.1')`
