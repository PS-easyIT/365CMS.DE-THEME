# Changelog

## 3.0.1 – 2026-05-17

### Re-Audit-Pass (PHP 8.4 / 365CMS v3.x.x)

**Bootstrap & Konfiguration**
- Konstante `BUILDBASE_THEME_VERSION` (`3.0.1`) hinzugefügt; synchron zu `theme.json`, `style.css`-Header und `update.json`.
- `style.css` mit vollem Theme-Header inkl. `Requires PHP: 8.4`, `License`, `Text Domain`, `Tags`.
- `enqueueStyles()` als neuer `head`-Hook (Priorität 1): Preload + Stylesheet, beide mit `?v=VERSION` Cache-Busting.
- Hardcodierter `<link rel="stylesheet">` aus `header.php` entfernt.
- Google-Fonts-URL erweitert auf alle in `theme.json` deklarierten Optionen (Roboto, Roboto Condensed, Oswald, Open Sans, Lato, Inter, Bebas Neue).
- Navigation-Script bekommt jetzt ebenfalls `?v=VERSION`.

**Customizer-Verkabelung**
- `outputCustomStyles()` schreibt jetzt alle in `theme.json` deklarierten Tokens als CSS-Variablen:
  Header-/Footer-Farben, Hero-Gradient-Start/-End, Button-Radius/-Padding/-Transform,
  Container-Breite, Section-Spacing, Border-Radius, Font-Size-Base, Line-Height-Base, Font-Weight-Heading,
  Safety/Success/Error/Link-Color.
- `custom_css` aus Customizer wird gegen `</style>`-Injection gefiltert.
- `theme.json`: neue Keys `header.show_search_btn`, `build_hero.show_hero_stats`, `build_hero.hero_stat_{1,2,3}_{value,label}`, `build_content.cta_button_label`.
- `footer.php`: liest jetzt `footer.footer_show_crafts` (korrekt) statt nicht-deklariertem `show_trade_links`; Boolean via `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.
- `home.php`: liest jetzt `cta_section_title` / `cta_section_text` / `cta_button_label` (alle in `theme.json` deklariert) statt nicht-deklarierter Keys `quote_section_*` / `register_cta_label`.
- Hero-Kennzahlen sind jetzt vollständig Customizer-getrieben (mit `show_hero_stats` Toggle) – keine fest verdrahteten Marketing-Zahlen mehr.

**Templates / Härtung**
- Inline `style=""` aus `index.php`, `page.php`, `404.php`, `error.php` komplett entfernt – ersetzt durch neue CSS-Utility-Klassen `.bb-page-section`, `.bb-page-title`, `.bb-page-article`, `.bb-container--narrow`, `.bb-post-title`, `.bb-post-excerpt`, `.bb-empty`, `.bb-error-screen`, `.bb-error-card`, `.bb-error-code`, `.bb-error-code--system`, `.bb-error-title`, `.bb-error-text`, `.bb-error-actions`.
- Inline `<script>document.body.classList.add('has-emergency-banner')</script>` aus `header.php` entfernt – `has-emergency-banner` wird jetzt serverseitig per `$bodyClasses[]` gesetzt.
- Body-Klassen werden gesammelt und einmal escaped ausgegeben.
- Tel-Link-Sanitation (`/[^0-9+]/`) beibehalten und vereinheitlicht.
- Alle `<a href="…">` jetzt durch `$safe()` escaped (auch interne Pfade auf Basis von `SITE_URL`).
- `SVG`-Icons bekommen `focusable="false"` zusätzlich zu `aria-hidden="true"`.
- `aria-controls="searchPanel"` / `aria-controls="site-navigation"` ergänzt für bessere Screenreader-Unterstützung.

**Design (KI-Slop Review)**
- Glassmorphism-Badge im Hero (`backdrop-filter: blur(4px)`) ersetzt durch solides Hi-Vis-Tag in Sicherheits-Amber mit schwarzen Seitenleisten – referenziert Baustellen-Schilder.
- Hero-Gradient nutzt jetzt Customizer-Werte (`--hero-gradient-start`/`--hero-gradient-end`) statt hardcodiertem Sekundär-Farbverlauf.
- Hero bekommt dezentes 36 × 36 px Blueprint-Raster als CSS-only Pseudo-Element mit Mask-Fade – direkter Verweis auf Architekten-Plan.
- Hero-Bodenkante: 45° Hazard-Stripe in Sicherheits-Amber/Schwarz als Baustellen-Markierung.
- Buttons mit solidem 2-px-Bottom-Shadow statt floating Card-Schatten – "Gewicht" statt "weicher Pill-Look".
- Cards bekommen 3-px-Top-Border (Primary → Safety-Amber im Hover) als Eck-Stempel-Referenz.
- Section-Header H2 mit 56 × 4 px Safety-Amber-Unterstreichung als Mess-Signatur.
- Standardpalette in `style.css` jetzt deckungsgleich mit `theme.json`-Defaults (vorher: Orange `#f97316` vs. Amber `#b45309`).
- Stats-Block bekommt Top-Border-Akzent und schrumpft auf 320 px sauber (responsive Tests).
- Mobile Menu (768 px): Hauptnavigation wird jetzt in dunkler Header-Farbe ausgeklappt statt heller Karten-BG – kontrastiert besser zur Sticky-Header-Optik.

**Accessibility**
- `:focus-visible` Outline + Offset bleibt; zusätzlich `.bb-focus-shadow` Utility für Form-Inputs (Search).
- `prefers-reduced-motion: reduce` Guard für Karten-Entry-Animation in `style.css`; bereits in `js/navigation.js` für IntersectionObserver vorhanden.
- `scroll-behavior: smooth` wird unter `prefers-reduced-motion: reduce` auf `auto` zurückgesetzt.
- Skip-Link bekommt Safety-Amber Hintergrund + Großbuchstaben – bewusst auffällig.

**Hinweis:** Theme enthält keine eigenen SQL-Statements (alle DB-Zugriffe via `\CMS\Services\*`); SQL-Härtungs-Audit daher nicht erforderlich.

## 3.0.0 – 2026-04-30

- Erstes Modernisierungs-Pass auf 365CMS v3.x.x: `cms_init`-Menüregistrierung,
  Font-Mapping-Helper, Reduced-Motion-Guard in Navigation, ARIA-Sync für Search-Panel.

## 1.0.0 – 2026-02-21

- Erstveröffentlichung des Bau- & Handwerk-Themes.
