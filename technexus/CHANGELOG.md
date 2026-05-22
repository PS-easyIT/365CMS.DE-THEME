# Changelog

## 1.0.1 – 2026-05-19 Nachtrag

### Abschlussdokumentation v3/PHP 8.4

- `README.md`, `CHANGELOG.md`, `functions.php`, Templates, `style.css`, `theme.json`, `update.json` und `js/navigation.js` sind als finaler TechNexus-Hardening- und Design-Pass dokumentiert.
- Nachgetragen sind Helper-Set, `tn_body_class()`, Customizer-CSS-Variablen, Dark-Mode-/Search-/Mobile-Menü-Verhalten, Reduced-Motion-Fallbacks und die Charcoal/Cyan-Designidentität.
- Der Update-Feed trägt das Nachtragsdatum vom 19.05.2026, ohne die Version `1.0.1` erneut zu erhöhen.

## 1.0.1 – 2026-05-18

### 365CMS-v3-Härtung & Design-Pass

Teilweise vorhandenes TechNexus-Theme auf vollständige v3-Konventionen
gebracht (Referenz: `business`, `logilink`, `cms-newspaper`).

### functions.php

- Singleton `TechNexus_Theme` (final), Konstanten `TECHNEXUS_THEME_VERSION`
  / `TECHNEXUS_THEME_SLUG`.
- `enqueueStyles()` mit Preload; versionierte Assets statt `filemtime`.
- Dynamische Google Fonts + Preconnect; Skip bei System-Stacks.
- `outputCustomStyles()` mappt alle `theme.json`-Keys auf CSS-Variablen
  (`--tn-*` + Legacy-Aliase).
- Menü-Registrierung auf `init` + `cms_init`.
- Helper-Set: `tn_get_setting`, `tn_href`, `tn_body_class`, Flash, usw.

### Templates

- Alle `style=""` entfernt; semantische Utility-Klassen in `style.css`.
- Inline-`<script>` aus `footer.php` entfernt (Logik nur in `navigation.js`).
- `header.php`: `tn_body_class()`, `aria-controls`, SVG-Icons, kein hardcoded
  Stylesheet.
- `home.php`, `index.php`, `page.php`, `404.php`, `error.php` bereinigt.

### Design

- Palette: Charcoal (`#0c1220`) + Electric Cyan (`#0891b2` / `#22d3ee`), kein
  generisches Indigo-Gradient-UI.
- Hero: dezentes Netz-Mesh statt Partikel/Glassmorphism.
- Card-Hover-Standard: farbiger Rahmen (`border`), nicht Lift+Shadow.
- `prefers-reduced-motion` in CSS und JS.

### Dokumentation

- `README.md`, `DOC/technexus/README.md` ergänzt.

## 1.0.0 – 2026-02-21

Erstveröffentlichung mit `theme.json`, Basis-Templates und Tech-Styling.
