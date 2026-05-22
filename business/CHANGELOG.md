# Changelog

## 3.0.2 – 2026-05-19 Nachtrag

- Abschlussdokumentation für den B2B-Hardening-Pass: `biz_safe_url()`, `biz_href()`, `biz_nav_menu()`, `biz_sanitize_content_html()`, Mobile-Body-Scroll-Lock und die strict_types/ABSPATH-Template-Guards sind als gemeinsamer Release-Stand nachgetragen.
- README, `theme.json`, `update.json`, `style.css`, `functions.php` und `CHANGELOG.md` bleiben auf `3.0.2` synchron; der Update-Feed trägt das Nachtragsdatum vom 19.05.2026.

## 3.0.2 – 2026-05-18

### Security-/Robustheits-Pass (365CMS v3.x.x / PHP 8.4)

- Customizer-CSS-Werte werden vor der Ausgabe als CSS-Variablen über Farb- und Zahlen-Helper validiert bzw. auf die `theme.json`-Grenzen begrenzt.
- `advanced.custom_css` entfernt jetzt zusätzlich zu `</style>` auch `<style>`-Tags, `@import`, `expression()`, `javascript:`-URLs sowie Legacy-Sinks wie `behavior`/`binding`.
- `biz_safe_url()` schützt interne, externe, Anchor-, `mailto:`- und `tel:`-URLs fail-closed; protocol-relative URLs werden verworfen.
- `biz_href()` und `biz_nav_menu()` verwenden die gehärtete URL-Normalisierung, inklusive sicherer Fallbacks für Menüeinträge.
- CMS-Seitencontent läuft über `biz_sanitize_content_html()` und entfernt Event-Handler-/Style-Attribute sowie gefährliche `href`-/`src`-Protokolle in erlaubtem HTML.
- Header-Logo und Über-uns-Bild validieren ihre Customizer-URLs vor der Ausgabe.
- Alle PHP-Templates besitzen jetzt `declare(strict_types=1)` plus `ABSPATH`-Guard.
- Das Mobile-Menü sperrt Scroll über `body.mobile-menu-open` statt per direktem `document.body.style.overflow`; Anchor-Scrolling nutzt sichere ID-Lookups statt ungeprüfter CSS-Selector-Strings.
- `BUSINESS_THEME_VERSION`, `style.css`, `theme.json`, `update.json` und README auf `3.0.2` synchronisiert; Manifest-Anforderungen deklarieren CMS `3.0.0` und PHP `8.4`.

## 3.0.1 – 2026-05-17

### v3 / PHP 8.4 / Hardening

- `BUSINESS_THEME_VERSION`-Konstante eingeführt; synchron mit `theme.json`,
  `style.css`-Header und `update.json`.
- `style.css`-Header trägt `Requires PHP: 8.4`, `License: GPL-2.0-or-later`
  und `Text Domain: biz-theme`.
- Menü-Registrierung läuft jetzt auf BEIDEN Hooks `init` und `cms_init` für
  v3-Bootstrap-Kompatibilität.
- `getThemeUrl('business')` mit defensivem Fallback auf `getThemeUrl()` –
  konsistent mit den anderen v3-Themes.
- `theme.json` um vollständiges `customization`-Schema erweitert
  (`colors`, `typography`, `layout`, `header`, `footer`, `biz_hero`,
  `biz_content`, `advanced`). Der bisherige `settings`-Block bleibt als
  Default-Fallback für `biz_config()` erhalten.
- `IT_Business_Theme::outputCustomStyles()` schreibt Customizer-getriebene
  CSS-Custom-Properties (Farben, Typografie, Layout, Focus-Ring) und filtert
  `custom_css` defensiv gegen `</style>`-Injection.
- `enable_sticky_header` wird über `filter_var(..., FILTER_VALIDATE_BOOLEAN)`
  gelesen.
- Google-Fonts-Output mit `preconnect` zu googleapis.com und gstatic.com
  (crossorigin), wird nur ausgegeben, wenn eine nicht-System-Schrift
  gewählt ist – inklusive aller via Customizer wählbaren Familien.
- `biz_href()` unterstützt zusätzlich `mailto:` und `tel:` (vorher als
  relativer Pfad behandelt).
- `biz_nav_menu()` cast Item-Label sicher zu String und behandelt fehlende
  URLs als `#`.
- `biz_body_class()`-Helper rendert `has-sticky-header` serverseitig in das
  `<body class="…">` statt per JS-Mutation.

### Accessibility

- Neuer `--focus-ring`-CSS-Token (an Customizer-Primary gebunden).
- `:focus:not(:focus-visible) { outline: none; }` +
  sichtbarer `*:focus-visible`-Ring.
- `.biz-focus-shadow:focus-visible` als Button-/Link-Focus-Shadow-Utility.
- `prefers-reduced-motion: reduce` schaltet `scroll-behavior`,
  globale Animationen, Drawer-Transitions und Karten-Animationen ab.
- Dekorative SVG-Icons (Header- und Footer-Logo) erhalten zusätzlich
  `focusable="false"`.
- Service-Icons und Emojis in Listen mit `aria-hidden="true"` markiert.
- Footer-Brand-Link mit `biz-focus-shadow` versehen, damit der Logo-Link
  fokussierbar und sichtbar ist.

### Template-Cleanups

- Sämtliche `style="…"`-Inline-Attribute aus `page.php`, `404.php`,
  `error.php` und `index.php` entfernt und durch dedizierte CSS-Klassen
  (`biz-page-eyebrow`, `biz-prose`, `biz-prose-empty`, `biz-error-wrap`,
  `biz-error-wrap-narrow`, `biz-error-icon`, `biz-error-title`,
  `biz-error-text`, `biz-error-actions`) ersetzt.
- Footer-Service- und -Kontaktliste verwenden jetzt `biz_href()` +
  `htmlspecialchars()` für alle internen Links.
- Hero-CTA-Mail/Telefon-Links laufen über `biz_href()` (das auch
  `mailto:`/`tel:` schützt).
- `home.php` liest `about_image` aus Customizer-Gruppe `biz_content`
  (statt der nicht-deklarierten Gruppe `business`).
- Footer-Copyright unterstützt jetzt das Template
  `© {year} {site_title}. Alle Rechte vorbehalten.` aus dem Customizer
  mit `gmdate('Y')` und HTML-Escaping.

### Design – Editorial B2B-Identität

- Komplettes Repalette: Generischer Indigo `#6366f1` → deliberater
  Kupfer-Gold `#c08a2e`; Navy `#0f172a` → tieferes Slate-Ink `#0c1320`;
  weißer Section-Hintergrund → warmes Papier `#f7f5f0`.
- Hero: generische Purple-Radial-Glow-Orbs entfernt; ersetzt durch
  vertikale Gold-Rule + Hairline-Diagonal-Pattern und ein redaktionelles
  Eyebrow mit Rule-Line.
- CTA-Sektion: Indigo-Linear-Gradient entfernt; jetzt solide Slate-Fläche
  mit Gold-Top-Rule und Eyebrow.
- Service-Cards: Generischer 3px-Top-Gradient-Strich und translate-Y-Hover
  entfernt; ersetzt durch Gold-Left-Rule + Paper-Hover.
- Buttons: tighteres Padding, Akzent jetzt Gold-on-Ink, kein Bounce,
  Focus-Shadow-Doppelring.
- Stats: linksbündig, Hairline-Trenner zwischen Spalten, tabular-nums.
- Eyebrow-Komponente (`biz-eyebrow`) ersetzt überall die generische
  Tag-Pill (`biz-section-tag`).
- Highlight im Hero-H1: subtile Italic-Variante mit Underline-Rule statt
  reiner Indigo-Farbe.
- Mobile-Drawer: Gold-Left-Rule auf aktivem Item statt voller
  Indigo-Hintergrund.

## 3.0.0 – 2026-05-17

- Optimiertes Asset-Loading mit Preload/Defer und escaped Cache-Busting
  Query-Parametern.
- URL- und Safe-Headline-Helper hinzugefügt (`biz_href`, `biz_safe_headline`).
- Interne Links und Pfadkonkatenation in Templates vereinheitlicht.
- Inline-Style-Attribute durch CSS-Klassen ersetzt.
- Navigation und Reduced-Motion-Handling für bessere Accessibility.
