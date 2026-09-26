# PTC Theme – Changelog

## 3.1.0 – 2026-09-26

### Kompatibilität mit 365CMS 3.4.00 (CSP, Trusted Types, Layout-Hooks)

- Kein doppelter Seitenrahmen mehr: Templates binden `header.php`/`footer.php` nicht mehr selbst ein – der Core rendert beide in `ThemeManager::render()`. `header.php`/`footer.php` sind zusätzlich gegen Doppel-Einbindung geschützt.
- `header.php` lädt die CSP-Runtime des Cores (`cms_csp_runtime_tags()`: DOMPurify + Trusted-Types-Policy) als erstes Script.
- Customizer-CSS (`<style id="…-custom-vars">`) trägt den CSP-Nonce (`theme_csp_nonce_attr()`); die Produktiv-CSP hatte den Block bisher verworfen, Farben/Typografie/Layout aus dem Customizer blieben wirkungslos.
- `footer.php` löst `body_end` vor `</body>` aus (Core-Cookie-Consent, Web-Vitals, PhotoSwipe). Das Navigations-Script wird nur noch einmal geladen (bisher doppelt über `before_footer`, wodurch Toggles sofort wieder schlossen).
- Google Fonts werden nicht mehr geladen, wenn im Core „Schriften lokal einbinden“ (`privacy_use_local_fonts`) aktiv ist (DSGVO).
- `error.php` lädt die Theme-Helfer nach, wenn der Fatal-Handler (`index.php`) die Datei direkt einbindet.
- Theme-Customizer: Aufruf der entfernten Legacy-Funktionen `renderAdminSidebar()`/`renderAdminSidebarStyles()` beseitigt (Fatal Error im Theme Editor); der Customizer bettet sich jetzt in das Admin-Layout ein.
- Theme-Customizer: Inline-Handler (`onclick`/`onchange`/`oninput`) und Inline-`<script>`/`<style>` nach `js/customizer-admin.js` und `css/customizer-admin.css` ausgelagert; Bildvorschauen per DOM-API statt `innerHTML`.
- Theme-Customizer (Sicherheit): SVG-Uploads entfernt (Stored-XSS), MIME-Prüfung per `finfo`, `is_uploaded_file()`, 2-MB-Limit und zufällige Dateinamen; Bild-URLs nur noch als http(s) oder wurzelrelativer Pfad; CSRF-Prüfung kompatibel mit der Section-Shell des Theme Editors.
- Manifest: `requires_cms`/`min_cms_version` 3.4.00, `tested_up_to` 3.4.00.

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
