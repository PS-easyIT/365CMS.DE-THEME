# academy365 Theme

Version: 3.0.1
Target: 365CMS v3.x.x, PHP 8.4

## Focus

E-Learning- und Weiterbildungs-Theme: Kurskatalog, Dozenten-Profile, Fortschrittsbalken, Zertifikat-Badges, Gamification-Hinweise und konversionsstarke Lern-CTAs. Identität: Violett als akademischer Mantel, Indigo als Wissenstiefe, Gold als Zertifikat/Achievement.

## Architektur

- `functions.php` — `Academy365_Theme::instance()` registriert Hooks (`init` + `cms_init`), liefert Stylesheet als `preload`+`stylesheet`, schreibt alle Customizer-Werte als CSS-Variablen in den `<head>`.
- `header.php`, `footer.php`, `home.php`, `page.php`, `index.php`, `404.php`, `error.php` — semantische Templates, alle URLs über `theme_route_url`/`SITE_URL`-Helper, alle Ausgaben über `htmlspecialchars` + `safeHeadline`/`slugify`.
- `style.css` — Token-System (`--primary-color`, `--accent-color`, `--hero-depth`, `--hero-brand`, `--hero-highlight`, …), identitätsstiftender Hero-Verlauf, `:focus-visible`-Outlines, `prefers-reduced-motion`-Guard.
- `js/navigation.js` — Vanilla-JS: Sticky-Header, Mobile-Nav, Suchpanel mit `aria-hidden`/Fokus-Rückgabe, Progress-Bar-Animation (IntersectionObserver + Reduced-Motion-Fallback), Counter-Animation, Rating-Sterne aus `data-rating`.

## Customizer-Mapping

`theme.json` ist Single Source of Truth. `functions.php::outputCustomStyles()` mappt:

- `colors.*` → `--primary-color`, `--accent-color`, `--badge-*`, `--cat-*`, `--text-primary`, `--border-color`, `--focus-ring`.
- `typography.font_family_base|heading|display` → `--font-body|heading|ui` (über `mapFontChoice()` mit Fallback-Stack).
- `typography.font_size_base|line_height_base|font_weight_heading` → `--font-size-base|line-height-base|font-weight-heading`.
- `layout.container_width|content_padding|border_radius|section_spacing|course_grid_columns|enable_sticky_header` → `--container-max|content-padding|radius-md|section-spacing|course-grid-min|header-position`.
- `header.header_bg_color|header_text_color|header_height|logo_max_height|show_header_shadow` → `--header-bg|header-text|header-height|logo-max-height|header-shadow`.
- `header.show_search_btn` → Lupe-Icon im Header (Toggle).
- `footer.footer_bg_color|footer_text_color|footer_link_color` → `--footer-bg|footer-text|footer-link`.
- `buttons.button_border_radius|button_padding_x|button_padding_y|button_font_weight|button_transform` → `--btn-radius|btn-padding-x|btn-padding-y|btn-weight|btn-transform`.

Boolean-Werte laufen ausschließlich über `filter_var(..., FILTER_VALIDATE_BOOLEAN)`.

## Modernization Highlights (3.0.1)

- Stylesheet jetzt als `preload` + `stylesheet` mit Cache-Busting-Querystring (`?v=3.0.1`) aus zentralem `enqueueStyles()`-Hook.
- `THEME_VERSION` und `ACADEMY365_THEME_VERSION` als Konstanten; `style.css`, `theme.json`, `update.json` synchron auf 3.0.1.
- `style.css` deklariert vollständigen Theme-Header inkl. `Requires PHP: 8.4`.
- Vollständiges Customizer-Mapping: Header- und Footer-Farben, Container-Breite, Border-Radius, Section-Spacing, Button-Padding/-Radius/-Transform, Font-Size-Basis und Line-Height werden jetzt tatsächlich angewendet (vorher: nur Farben + 3 Fonts).
- `theme.json` ergänzt um `header.show_search_btn`, das vom Template seit jeher gelesen, aber nicht deklariert wurde.
- Alle Inline-`style=""`-Attribute aus `home.php`, `page.php`, `index.php`, `404.php`, `error.php` entfernt; Rating-Sterne über `data-rating`, Progress-Bars über `data-progress` (JS setzt `--rating`/`width`).
- `home.php` baut Routen über `theme_route_url` (mit Fallback auf `SITE_URL`/Pfad-Helper) statt String-Konkatenation.
- Hero-Bereich verwendet jetzt einen identitätsstiftenden Drei-Stop-Verlauf (Indigo-Tiefe → Violett-Brand → Gold-Korona) statt eines generischen 135°-Two-Stop-Gradients.
- Progress-Bar verläuft Violett → Indigo → Gold und visualisiert damit den Lernpfad „Studium → Fortschritt → Zertifikat".
- `js/navigation.js` setzt Progress-Bar-Breite direkt (kein Animations-Run) wenn `prefers-reduced-motion: reduce` aktiv ist.
- `:focus-visible`-Outline, `:focus:not(:focus-visible){outline:none}` und globaler Reduced-Motion-Reset im Stylesheet.

## Files

- `theme.json` — Metadaten + Customizer-Schema (Single Source of Truth).
- `functions.php` — Theme-Bootstrap, Hook-Registrierung, Customizer-zu-CSS-Mapping.
- `header.php`, `footer.php` — Site-Frame mit Logo, Mobile-Toggle, Suchpanel und Footer-Spalten.
- `home.php` — Startseiten-Hero, Featured-Courses-Grid, Tutoren-Sektion, Subscription-CTA.
- `index.php`, `page.php` — Archiv- und Seiten-Loops.
- `404.php`, `error.php` — Fehlerseiten mit konsistentem `.ac-error-page`-Layout.
- `style.css` — Token-System, Hero-Layering, Card-/Badge-/Progress-Styles, Responsive- und Reduced-Motion-Regeln.
- `js/navigation.js` — Vanilla-JS für Header, Suche, Progress-Bars, Rating, Reveal- und Counter-Animationen.
- `update.json` — Update-Manifest für 365CMS.
