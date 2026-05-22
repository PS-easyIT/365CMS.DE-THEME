# Changelog

## 1.0.0 – 2026-05-19 Nachtrag

### Registry- und Release-Sync

- `cms-newspaper` ist als vollständiges neues Theme in der Theme-Registry `index.json` nachgetragen.
- Die neu angelegten Theme-Dateien (`functions.php`, Templates, `style.css`, `theme.json`, `update.json`, `js/navigation.js` und Admin-/CSS-Unterordner) bleiben als ein zusammengehöriger 365CMS-v3-Initialstand dokumentiert.
- README, Changelog und Update-Feed tragen den Nachtragsstand vom 19.05.2026, ohne die Initialversion `1.0.0` zu erhöhen.

## 1.0.0 – 2026-05-18

### Erstes funktionales 365CMS-v3-Release

`cms-newspaper` ist ab dieser Version ein vollständiges 365CMS-v3-Theme
auf PHP 8.4. Aus dem bisherigen Static-HTML-Prototyp (`newspaper.html`,
0.1.0) ist ein produktiv ausgelieferbares Theme mit Customizer-Schema,
PHP-Templates und Hook-Integration geworden.

### Neue Dateien

- `theme.json` – vollständiges Customizer-Schema (`colors`, `typography`,
  `layout`, `header`, `footer`, `buttons`, `news_hero`, `news_content`,
  `advanced`) inkl. Menüpositionen `primary-nav`, `footer-nav`,
  `footer-legal`, `topics-nav`.
- `functions.php` – Singleton `CmsNewspaper_Theme` mit registrierten
  Hooks (`head`, `before_footer`, `init`, `cms_init`), Konstante
  `CMSNEWSPAPER_THEME_VERSION`, Customizer-getriebene CSS-Variablen,
  Google-Fonts mit `preconnect`, defensiver `custom_css`-Filter,
  Helper-Set (`theme_is_logged_in`, `theme_route_url`, `theme_nav_menu`,
  `get_header`, `get_footer`, `news_get_setting`, `news_get_flash`,
  `news_set_flash`, `news_config`, `news_href`, `news_site_title`,
  `news_body_class`, `news_brand_html`).
- `header.php` – 3-Bar-Masthead (Utility-Bar + Wordmark/Nav + Themen-
  Schnellnavigation), Skip-Link, Suche-Toggle mit `aria-expanded` /
  `aria-controls`, Mobile-Toggle, Suchpanel, Mobile-Drawer-Container,
  Flash-Message-Render-Slot. Body-Klassen werden serverseitig über
  `news_body_class()` gerendert.
- `home.php` – Broadsheet-Front-Page: Hero-Repo-Card, Breaking-Stack
  mit Kategorie-Badges, 2-spaltiges Info-Box-Grid, 3×3 Archiv-Grid und
  2-spaltiges Feed-Grid. Inhalte werden über `news_config()` aus dem
  Customizer gelesen, Default-Artikel-Stubs spiegeln die Inhalte des
  Original-Prototyps.
- `index.php` – Fallback-Listing mit `news-page-hero` + Call-to-Action.
- `page.php` – generische CMS-Page mit `news-prose`-Container,
  `strip_tags()`-Whitelist und Last-Updated-Zeile.
- `404.php` – gebrandetes 404 mit großer 404-Ziffer und Akzent-Underline.
- `error.php` – generische Fehlerseite, Status-Code-getrieben.
- `footer.php` – 4-spaltiges Footer-Grid (Brand + Themen + Ressourcen +
  Portal), optionaler Partner-Strip, Copy-Zeile mit `gmdate('Y')`-basiertem
  Copyright und `footer-legal`-Menü.
- `style.css` – Theme-Header (Requires PHP 8.4, License GPL-2.0-or-later,
  Text Domain `cms-newspaper`), CSS-Custom-Properties als Design-Tokens,
  Reset/Base, Layout-Primitives, Header (3 Bars), Buttons (Ink-Rectangle),
  Hero-Repo-Card, Listen-Karten, Info-Boxen, Tile-Grid, Feed-Boxen,
  Page-Hero, Prose, Error-Layout, Footer (Multi-Tier-Slab),
  Reveal-Animationen (nur bei `prefers-reduced-motion: no-preference`),
  Responsive-Walk 1024 → 768 → 480, globaler Reduced-Motion-Safety-Net.
- `js/navigation.js` – IIFE, `prefersReducedMotion`-Guard, Sticky-Header-
  Scroll-Klasse, Mobile-Drawer-Toggle (Open/Close/Outside-Click/Escape),
  Search-Panel-Toggle mit Focus-Restore, optionaler `IntersectionObserver`-
  Reveal (nur wenn Motion erlaubt; Fallback aktiviert alle
  `.news-reveal`-Elemente direkt).
- `update.json` – Update-Feed mit `version: 1.0.0`, `min_cms_version:
  3.0.0`, `requires_php: 8.4`.

### Aktualisierte Dateien

- `README.md` – vollständig neu geschrieben: dokumentiert ab sofort den
  funktionalen Zustand, Customizer-Sektionen, Security-/A11y-Hardening
  und Design-Identität. Historischer Hinweis auf die 0.1.0-Static-Phase
  ist im CHANGELOG erhalten.
- `CHANGELOG.md` – diese Datei; 0.1.0-Eintrag bleibt als historischer
  Anker erhalten.

### Design – Editoriale Newspaper-Identität

- **Editorial Trinity:** Playfair Display (Masthead-Serif), Source Sans 3
  (Body) und IBM Plex Mono (Kicker/Meta) – serif gegen sans für
  Druck-Hierarchie, mono für technische Präzision.
- **Restpalette:** Ink-Schwarz `#0a0b0c`, Off-White-Papier `#f8f9fa`,
  reines Surface-Weiß; Signal-Rot `#e32d12` ausschließlich für
  Kategorie-Badges, Breaking-Live-Indikator, das rote Quadrat-Mark in
  Section-Headers und die Top-Rule der Hero-Repo-Karte. Bewusst kein
  generisches SaaS-Indigo, keine Glassmorphism-Optik.
- **Section-Header** mit 2 px Ink-Bottom-Rule und 14 px-Akzent-Quadrat
  ersetzen generische Tag-Pills.
- **Buttons** als Ink-Rechtecke mit 4 px Radius statt Hochglanz-Pillen;
  Hover wechselt auf Signal-Rot.
- **Breaking-/Live-Dot** sparsam in der Breaking-Sektion, abschaltbar
  via Customizer.
- **Container** auf 1040 px begrenzt (editorial-Lesetempo statt
  generischer SaaS-1200/1440).

### Sicherheit & Robustheit

- Konstante `CMSNEWSPAPER_THEME_VERSION` synchron mit `theme.json` und
  `update.json`.
- Asset-URLs HTML-escaped, mit `preload` und `defer`.
- Google Fonts laden nur tatsächlich gewählte Familien.
- Boolean-Customizer-Reads ausnahmslos über `filter_var(…,
  FILTER_VALIDATE_BOOLEAN)`.
- `custom_css` defensiv gegen `</style>`-Injection gefiltert.
- `theme_nav_menu()` validiert URLs (`FILTER_VALIDATE_URL`) und akzeptiert
  ansonsten nur relative Pfade oder Anchors.
- Externe Partner-Links setzen automatisch `target="_blank"` +
  `rel="noopener noreferrer"`.

### Accessibility

- `:focus:not(:focus-visible)` + sichtbarer `:focus-visible`-Ring an
  `--focus-ring` (Signal-Rot).
- Doppelring-Utility `.news-focus-shadow` für Brand-Links und Toggles.
- `prefers-reduced-motion: reduce` schaltet Reveal-Animationen,
  Live-Dot-Pulse und Image-Hover-Scales ab.
- Mobile-Drawer und Search-Panel toggeln `aria-hidden` und stellen Fokus
  korrekt wieder her.

## 0.1.0 – 2026-05-17

- Baseline-Dokumentation für das damals noch statische `cms-newspaper`-
  Verzeichnis hinzugefügt.
- Stand zu diesem Zeitpunkt: ausschließlich `newspaper.html`
  (Static-HTML-Prototyp), kein `theme.json`, kein `functions.php`, keine
  PHP-Templates.
- Migrationspfad zur 365CMS-v3-Themestruktur dokumentiert. Dieser Pfad
  wurde mit Release 1.0.0 abgeschlossen.
