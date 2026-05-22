# PTC Theme – Changelog

## 3.0.0 Nachtrag (2026-05-19)

### Abschlussdokumentation der Generisierung

- `PTC-Corporate.color-theme.json` ist bewusst entfernt; das Theme nutzt nur noch neutrale Customizer-/CSS-Tokens statt firmenspezifischer CI-Artefakte.
- `admin/customizer.php`, Templates, `js/navigation.js` und `style.css` sind als generisches Branchen-Theme dokumentiert: Personalvermittlung, Arbeitnehmerüberlassung und Weiterbildung ohne Betreiber-Festverdrahtung.
- `README.md`, `theme.json`, `update.json`, `style.css` und `functions.php` bilden den finalen v3/PHP-8.4-Stand `3.0.0` ab.

## 3.0.0 (2026-05-18)

### Generalisierung (Breaking für alte CI)

- Entfernt: firmenspezifische Namen, Slogans, Adressen, Logos und Navy/Gold-CI-Bezüge
- Neutrale Platzhaltertexte für Personalvermittlung & Weiterbildung
- CSS-Variablen: `--color-primary`, `--color-accent`, `--color-*` statt `--ptc-navy` / `--ptc-gold`
- Gelöscht: `PTC-Corporate.color-theme.json`
- Network-Bar-Defaults leer (optional)

### v3 / PHP 8.4

- `PTC_THEME_VERSION` 3.0.0, sync mit `theme.json`, `style.css`, `update.json`
- Hooks: `head`, `before_footer`, `init`, `cms_init`
- Styles: preload + versioniert; `navigation.js` defer
- `mapFontChoice()`, Google Fonts mit preconnect
- `outputCustomStyles()` für alle Customizer-Farben/Layout-Tokens
- `filter_var`, `htmlspecialchars`, `sanitizeCustomCss`
- `theme_nav_menu`, `get_header`, `get_footer`, `ptc_get_setting`, `ptc_href`, `ptc_body_class`, `ptc_safe_headline`
- Events-Query: prepared `LIMIT`
- `--focus-ring`, `:focus-visible`, `prefers-reduced-motion`
- Mobile-Menü: Fokus-Wiederherstellung nach Schließen

### Design

- Pipeline-Sektion (Vermittlungsprozess)
- Dual-CTA-Karten (Kandidaten / Arbeitgeber)
- Keine generischen SaaS-Gradienten; vertrauensorientierte Farbpalette

## 1.1.0 (2026-03-01)

- Services 12 Karten, MS Booking, Network Bar, CTA-/Seitenrand-Farben

## 1.0.0 (2026-02-21)

- Erstveröffentlichung
