# Changelog

## 1.0.1 - 2026-05-19 Nachtrag

### Abschlussdokumentation v3/PHP 8.4

- `README.md`, `CHANGELOG.md`, `partials/candidate-card.php`, `functions.php`, Templates, `style.css`, `theme.json`, `update.json` und `js/navigation.js` sind als finaler PersonalFlow-Completion-/Re-Audit-Stand dokumentiert.
- Nachgetragen sind die technischen Details zu Pipeline-Stage-Labels, Candidate-Card-Partial, Body-Class-Rendering, Search-/Mobile-Drawer-A11y und vollständigem Customizer-Key-Mapping.
- Der Update-Feed trägt das Nachtragsdatum vom 19.05.2026, ohne die Version `1.0.1` erneut zu erhöhen.

## 1.0.1 - 2026-05-18

### Inventory-Pfad
- Stand bei Audit-Beginn: partiell v3-strukturiertes Theme bei Version
  1.0.0 mit substanziellen Lücken (hartcodiertes `<link>` im Header,
  fehlende `enqueueStyles()`, Customizer-Key-Mismatches zwischen
  `theme.json` und Templates, viele Inline-Styles, kein Helper-Set,
  falsche Beschreibung in `update.json`, kein README/CHANGELOG).
- Gewählter Pfad: **Completion + Re-Audit** – fehlende Konventions-
  Bausteine ergänzt und bestehende Stellen gegen die Logilink-/
  MedCarePro-/CMS-Newspaper-Vorbilder begradigt.

### v3 / PHP 8.4 / Hardening
- `PERSONALFLOW_THEME_VERSION` und `PERSONALFLOW_THEME_SLUG` als
  Theme-Konstanten eingeführt; `theme.json`, `style.css`, `update.json`
  und `functions.php` auf `1.0.1` synchronisiert.
- `style.css`-Header erweitert: `Requires PHP: 8.4`,
  `License: GPL-2.0-or-later`, `Text Domain: personalflow`,
  `Tested up to CMS: 3.x`.
- `enqueueStyles()` (`head:1`) und `enqueueScripts()`
  (`before_footer:99`) eingeführt – versioniert via
  `?v=PERSONALFLOW_THEME_VERSION`, Stylesheet zusätzlich als
  `<link rel="preload" as="style">`. Hartcodiertes
  `<link rel="stylesheet">` aus `header.php` entfernt.
- `outputMetaTags()` (`head:10`) schreibt Description, OG-URL,
  Theme-Color und Robots.
- `outputCustomStyles()` mappt jetzt **jeden** in `theme.json`
  deklarierten Customizer-Schlüssel auf eine CSS-Variable – Brand,
  Pipeline-Status-Paare (sechs fg/bg-Paare), Flächen-/Text-/Rule-Tokens,
  Status (success/warning/error), Typografie (Schriftart/Größe/
  Zeilenhöhe/Heading-Weight), Layout (Container/Padding/Radius/
  Section-Spacing), Header (bg/text/border/height/logo-max), Footer
  (bg/text/link), Buttons (radius/padding/weight/transform), Focus-Ring
  und Focus-Ring-Shadow. Booleans (Sticky-Header, Header-Schatten)
  gehen durch `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.
- `mapFontChoice()` neu eingeführt mit allen in `theme.json`
  deklarierten Body-/Heading-/Stats-Familien (Nunito Sans, Nunito,
  Inter, Open Sans, Lato, Roboto, Poppins, Manrope, Raleway,
  Montserrat, Playfair Display, JetBrains Mono, Roboto Mono, Roboto
  Condensed, Oswald, System Monospace). Slot-spezifische
  Fallback-Ketten für `body` / `heading` / `stats`.
- Google-Fonts-URL lädt nur tatsächlich gewählte Familien. Bei reinem
  System-Stack (`system-mono`) entfällt der ganze `<link>`-Tag.
  `preconnect`-Hinweise sind HTML-escaped.
- `custom_css` wird über `sanitizeCustomCss()` defensiv gegen
  `</style>`-Injection regex-gefiltert.
- `cms_init` + `init` Doppelregistrierung der Menü-Locations;
  zusätzlich `register_menu_locations`-Filter.

### Customizer-Key-Konsistenz (war: vorher gebrochen)
- Vorher las `home.php` `hr_hero.cta_jobseeker_label`,
  `cta_jobseeker_url`, `cta_employer_label`, `cta_employer_url`,
  `show_stats_bar` und `hr_content.candidates_section_title`,
  `match_score_label`, `register_cta_label` – **keine** dieser Keys
  existierte in `theme.json`. Jetzt:
  - Hero-Keys in `theme.json` als `hero_badge`, `hero_headline`
    (mit `<span class="hl">`-Support), `hero_subline`,
    `cta_jobseeker_label/url`, `cta_employer_label/url`,
    `hero_aside_title`, `show_pipeline_ribbon` deklariert und in
    `home.php` exakt so gelesen.
  - Content-Keys: `talent_section_title/subtitle`,
    `jobs_section_title/subtitle`, `salary_label`, `remote_label`,
    `available_label`, `kpi_section_title`, `kpi_*_label/value` (vier
    KPI-Paare), `cta_section_title/text`, `cta_register_label`.
- `header.show_search`, `header.search_placeholder`,
  `header.show_header_shadow`, `footer.show_pipeline_links`,
  `footer.show_employer_links` – früher gelesen, nicht deklariert –
  jetzt in `theme.json` und allen Templates konsistent.

### Helper-Set
- Theme-lokales Helper-Set hinzugefügt, parallel zu MedCare Pro /
  LogiLink: `theme_is_logged_in`, `theme_route_url`, `theme_nav_menu`,
  `get_header`, `get_footer`, `pf_get_setting`, `pf_get_flash`,
  `pf_set_flash`, `pf_href`, `pf_site_url`, `pf_site_title`,
  `pf_body_class`, `pf_safe_headline`.
- `pf_stage_label()` löst Pipeline-Stage-Slugs (`applied` … `archived`)
  auf die Customizer-Labels auf.
- `pf_body_class()` rendert Sticky-Header- und Shadow-Mode-Klassen
  serverseitig (kein JS-Class-Mutation vor Paint).

### Templates
- `header.php`: hartcodierter Stylesheet entfernt; `pf_body_class()`
  rendert die Body-Klasse server-seitig; Logo mit `pf-logo-mark`-
  Initial-Plate (kein Emoji-Dependency mehr); Search-Toggle und
  Mobile-Toggle tragen `type="button"`, `aria-expanded`,
  `aria-controls`, `aria-label`; Search-Panel hat
  `aria-hidden="true"` per Default. Neuer `<nav id="pfMobileDrawer">`
  als separater Drawer mit eigenem `aria-hidden`-Toggle und
  Overlay-Element für Outside-Click.
- `footer.php`: nutzt `gmdate('Y')`-Template `{year}` / `{site_title}`,
  alle Links durch `theme_route_url()` aufgelöst,
  `theme_nav_menu('footer-nav')` und `theme_nav_menu('footer-legal')`
  eingebunden, keine Inline-Styles.
- `home.php`: vollständig neu strukturiert. Hero mit zweispaltiger
  Komposition (Copy + Talent-Aside), Pipeline-Ribbon direkt unter dem
  Hero, KPI-Strip mit JetBrains-Mono-tnum-Zahlen, Talent-Pool-Grid
  über `partials/candidate-card.php`, Job-Board-Grid mit
  Job-Card-Pattern, Registrierungs-CTA. Alle Daten aus dem
  Customizer; Demo-Records inline und klar als Platzhalter markiert.
- `index.php`, `page.php`, `404.php`, `error.php`: alle Inline-
  `style=""`-Attribute entfernt; durch semantische CSS-Klassen ersetzt
  (`pf-page-shell`, `pf-error-shell`, `pf-error-card`,
  `pf-error-code`, `pf-error-actions`, `pf-prose-wrap`, `pf-prose`,
  `pf-meta-line`); URLs `htmlspecialchars`-escaped und über
  `theme_route_url()` aufgelöst.
- Neu: `partials/candidate-card.php` – wiederverwendbare Talent-Card
  mit Avatar-Plate (Image oder Initialen), Name, Rolle, Standort,
  Stage-Badge, Skill-Chips, Verfügbarkeits-Indikator und Action-Row.

### CSS
- Komplette Neuschreibung von `style.css` mit `--pf-*`-Token-System.
- Pipeline-Status-System mit sechs WCAG-AA-konformen fg/bg-Paaren
  gegen `--pf-surface = #ffffff`:
  - `applied   #475569 / #e2e8f0`
  - `screened  #b45309 / #fef3c7`
  - `interview #1d4ed8 / #dbeafe`
  - `offer     #6d28d9 / #ede9fe`
  - `hired     #15803d / #dcfce7`
  - `archived  #57534e / #f5f5f4`
- Pipeline-Step-Komponente (`.pf-pipeline-step--*`) rendert eine
  5-stufige Ribbon-Sicht mit eigener Stage-Farbe je Spalte.
- Talent-Card-Pattern (`.pf-talent-card`) mit Avatar-Spalte (Initialen
  via CSS oder Image-Override), Meta-Zeile, Stage-Row und
  Action-Border.
- Job-Card-Pattern (`.pf-job-card`) mit linksbündigem Titel,
  rechts-anchored Remote-Badge, Gehalts-Display in Stats-Font,
  Tag-Row und CTA-Border.
- KPI-Strip mit `font-feature-settings: "tnum","lnum"` für
  tabellarische Ziffern.
- Eyebrow-Pille im Hero und Section-Eyebrow für Sektions-Kicker
  (mit `font-stats` und Caps-Tracking).
- Skip-Link, `.pf-skip-link` als erstes fokussierbares Element.
- Fokus-Token `--focus-ring` und `--focus-ring-shadow`;
  `:focus:not(:focus-visible)` neutralisiert; sichtbarer
  `:focus-visible`-Ring + `.pf-focus-shadow`-Utility für Inputs/
  Buttons/Logos.
- Mobile-Drawer mit Overlay (`.pf-mobile-overlay`) und
  Scroll-Lock-Pattern. Search-Panel als separate Komponente.
- Responsive Brüche bei 480 / 768 / 960 Pixel; Container-Padding
  reduziert sich progressiv. `prefers-reduced-motion: reduce`
  deaktiviert Hover-Translate und Reveal-Transitions.
- Print-Styles entfernen Header/Footer/CTAs.

### JS
- `js/navigation.js` neu nach dem LogiLink-/CMS-Newspaper-Pattern:
  Sticky-Header-Scroll-State, Mobile-Drawer mit `aria-hidden`/
  `aria-expanded` + Outside-Click + Escape + Focus-Restore +
  Body-Scroll-Lock, separates Search-Panel mit Focus-Restore zum
  Opener, Reveal nur bei
  `!prefersReducedMotion && 'IntersectionObserver' in window`, sonst
  unmittelbares `is-visible`-Fallback. IDs umbenannt zu
  `pfMobileToggle`, `pfMobileDrawer`, `pfMobileOverlay`,
  `pfSearchToggle`, `pfSearchPanel`, `pfSearchClose`.

### Dokumentation
- `README.md` neu (vorher nicht vorhanden) – Designprofil,
  Pipeline-Status-Tabelle, Konstanten/Hooks, Helper-Set,
  Customizer-Sektionen, Asset-Pipeline, Sicherheits- und A11y-
  Praktiken, Verwendung der Candidate-Card.
- `CHANGELOG.md` neu (vorher nicht vorhanden).
- `update.json` korrigiert: vorher fälschlich
  "Portfolio & Personal Brand Theme", jetzt HR-/Personalvermittlungs-
  Beschreibung; `min_cms_version` von `0.20.0` auf `3.0.0` angehoben.

## 1.0.0 - 2026-02-21

- Erstveröffentlichung: HR- & Personalvermittlungs-Theme.
