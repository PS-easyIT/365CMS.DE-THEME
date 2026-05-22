# Changelog

## 3.0.2 - 2026-05-19 Nachtrag

- Abschlussdokumentation für den Security-/Robustheits-Pass: Template-Guards, URL-Fallbacks, Customizer-CSS-Validierung und defensive `advanced.custom_css`-Bereinigung sind als zusammengehöriger v3/PHP-8.4-Release-Stand dokumentiert.
- `theme.json`, `update.json`, `style.css`, `functions.php` und `CHANGELOG.md` bleiben auf `3.0.2` synchron; der Update-Feed trägt das Nachtragsdatum vom 19.05.2026.

## 3.0.2 - 2026-05-18

Security-/Robustheits-Pass für academy365 (365CMS v3.x.x / PHP 8.4):

- **Customizer-CSS gehärtet:** Farbwerte laufen über `academy365_css_color()`, numerische Tokens werden mit `academy365_css_number()` auf die Schema-Grenzen geclamped, Select-Werte über Allowlists geprüft.
- **Custom CSS defensiv bereinigt:** `advanced.custom_css` entfernt nun `<style>`-Tags, `@import`, `expression()`, `javascript:`-URLs sowie alte Browser-Sinks wie `behavior`/`binding` vor der Ausgabe.
- **URL-Ausgaben fail-closed:** Header-Logo, Header-Routen, Footer-Links sowie 404/Error-Aktionen nutzen `academy365_safe_url()` mit sicheren Fallbacks.
- **Template-Standard:** `header.php`, `footer.php`, `home.php`, `page.php`, `index.php`, `404.php` und `error.php` besitzen jetzt `declare(strict_types=1)` und `ABSPATH`-Guard.
- **Version & Manifest:** `ACADEMY365_THEME_VERSION`, `style.css`, `theme.json` und `update.json` auf `3.0.2`; Manifest-Anforderungen auf 365CMS `3.0.0` und PHP `8.4` gesetzt.

## 3.0.1 - 2026-05-17

Re-Audit-Pass für academy365 (365CMS v3.x.x / PHP 8.4):

- **Version & Metadaten:** `theme.json`, `update.json`, `style.css`-Header und neue Konstante `ACADEMY365_THEME_VERSION` (`functions.php`) sind synchron auf 3.0.1. `style.css` deklariert jetzt einen vollständigen Theme-Header inklusive `Requires PHP: 8.4`.
- **Customizer-Vollausbau:** `outputCustomStyles()` mappt jetzt Header-Farben, Header-Höhe, Logo-Max-Höhe, Header-Shadow-Switch, Sticky-Header-Switch, Footer-Farben, Container-Breite, Content-Padding, Border-Radius, Section-Spacing, Button-Padding/-Radius/-Weight/-Transform sowie `font_size_base` / `line_height_base` / `font_weight_heading`. Vorher wurden nur Farben und drei Fonts ausgewertet — der Rest der `theme.json`-Settings war wirkungslos.
- **Schema-Lücke geschlossen:** `header.show_search_btn` ist jetzt in `theme.json` deklariert; das Template las den Key bereits, ohne dass er im Customizer-Schema existierte.
- **Stylesheet-Auslieferung:** `header.php` linkt das Stylesheet nicht mehr direkt. Stattdessen liefert `Academy365_Theme::enqueueStyles()` `preload` + `stylesheet` mit Cache-Busting-Querystring (`?v=3.0.1`) — passend zum Pattern in 365Network.
- **Inline-Styles entfernt:** `home.php` (`style="--rating:..."` und `style="width:X%"`), `page.php` (`font-family:var(--font-body)`), `index.php`, `404.php`, `error.php` (jeweils `font-size`/`display`/`margin-top`). Dynamische Daten laufen jetzt über `data-rating` / `data-progress`; alles andere wurde in CSS-Klassen (`.ac-error-emoji`, `.ac-error-actions`, `.ac-empty-emoji`, `.ac-page-body`) extrahiert.
- **URL-Helper:** `home.php` baut alle Lern-, Registrierungs-, Kurs- und Tutoren-URLs über `theme_route_url()` mit `SITE_URL`-Pfad-Fallback statt String-Konkatenation.
- **JS-Hardening:** `js/navigation.js` setzt Progress-Bar-Breite direkt (kein Animations-Run) wenn `prefers-reduced-motion: reduce` aktiv ist; Rating-Sterne werden aus `data-rating` gelesen, mit `parseFloat` (Komma-tolerant) und Clamping `[0..5]`.
- **Design / Anti-KI-Slop:** Der Hero verwendet jetzt einen drei-schichtigen Verlauf — Indigo-Tiefe (Studium) unten links, Violett-Brand (Lernweg) in der Mitte, Gold-Korona (Zertifikat) oben rechts. Damit ersetzt die academy365-Identität den generischen 135°-Two-Stop-Verlauf des Vorgängers. Auch der Progress-Bar verläuft jetzt Violett → Indigo → Gold und visualisiert „Studium → Fortschritt → Zertifikat" anstelle eines beliebigen Zwei-Farben-Verlaufs.
- **Card-Affordanz:** Kurs-Card-Titel bekommen on-hover eine Border-Bottom-Linie in Primärfarbe statt nur eines generischen `translateY(-4px)`. Lift bleibt, aber das Lese-Signal („tiefer hineinklicken") ist jetzt explizit.
- **Responsiveness:** Neue Breakpoint-Regeln für 540 px (Section-Heads stapeln, Error-Action-Buttons stapeln) und für 900 px (Footer-Spalten kollabieren) sind explizit definiert. Hero-Headline und Container nutzen `clamp(...)` bzw. `--container-max` aus dem Customizer.

## 3.0.0 - 2026-05-17

- Compatibility Hook für Menü-Registrierung in `cms_init`.
- Customizer-Key-Mapping für Farben, Typografie, Labels und Sektions-Toggles korrigiert.
- Safe Headline / Slug Helper für robuste Ausgabe und Route-Bau.
- Inline-Template-Styles in dedizierte Stylesheet-Klassen refaktoriert.
- JS-Verhalten für Motion-Präferenzen und robuste Observer-/Search-State-Behandlung verbessert.

## 1.0.0 - 2026-02-21

- Erstveröffentlichung: E-Learning & Kursplattform-Theme.
