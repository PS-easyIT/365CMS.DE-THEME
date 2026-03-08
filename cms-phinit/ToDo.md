# cms-phinit Theme – Umbau-Plan (365CMS v2.5.4 Migration)

> Erstellt: 2026-03-08  
> Zuletzt aktualisiert: 2025-07-14  
> Ziel: Theme-Modernisierung, Customizer-Redesign, Hook-Standardisierung

---

## Status-Legende
- [ ] Offen
- [x] Erledigt
- [-] In Arbeit

---

## Phase 0: Vorbereitung
- [x] TNTSearch Deprecation Fix in `CMS/core/Services/SearchService.php`
- [x] ToDo.md erstellen
- [x] `cms_favorites` Tabelle in SchemaManager ergänzt (v15→v16)
- [x] `cms_security_log` Tabelle in SchemaManager ergänzt (v16→v17)

## Phase 1: admin/customizer.php – Komplett-Redesign
- [x] 1.1 Eigenes `<!DOCTYPE html>` entfernen – Fragment für Admin-Embed
- [x] 1.2 Tabler-CSS-Klassen verwenden statt eigene Inline-Styles
- [ ] 1.3 Neue Tabs: `seo` + `performance`
- [x] 1.4 Export/Import-Funktion (JSON) über ThemeCustomizer-API
- [ ] 1.5 Live-Preview-Iframe mit Device-Umschalter (Desktop/Tablet/Mobile)
- [ ] 1.6 Font-Preview-Widget für Typografie-Tab
- [ ] 1.7 Farb-Palette-Preset-Auswahl (Quick-Theme-Presets)
- [x] 1.9 Keyboard-Shortcut (Strg+S) für Speichern
- [x] 1.10 Unsaved-Changes-Warnung bei Navigation

## Phase 2: functions.php – Modernisierung
- [x] 2.1 Neue CSS-Variablen aus Layout-Tab einbinden (`content_gap`, `spacing_*`, `sidebar_position`)
- [x] 2.2 `generatePhinitCSS()` um neue Customizer-Felder erweitern (`sidebar_position` als `--sidebar-position`)
- [x] 2.3 Performance: `font-display: swap` in Google-Fonts-Einbindung (war bereits implementiert via `&display=swap`)
- [x] 2.4 SEO-OG-Image-Default aus Customizer (`og_default_image` in customizer.php advanced-Tab ergänzt)
- [x] 2.5 `phinit_reading_time()` liest WPM aus Customizer (`posts.reading_time_wpm`) statt hartkodiertem Wert

## Phase 3: Templates – Hook-Standardisierung
- [x] 3.1 header.php: `doAction('body_start')` nach `<body>` – war bereits vorhanden ✅
- [x] 3.2 footer.php: `doAction('before_footer')` vor `<footer>` + `doAction('footer')` nach `</footer>` ergänzt
- [x] 3.3 index.php: `doAction('home_content')` vor Content-Container ergänzt; `doAction('after_header')` wird von header.php gefeuert ✅
- [ ] 3.4 post.php: Hooks prüfen, CommentService-Aufruf verifizieren
- [ ] 3.5 blog.php: `doAction('after_header')` per header.php bereits aktiv – Inhalts-Hooks prüfen
- [ ] 3.6 page.php: Hooks prüfen
- [ ] 3.7 search.php: Hooks standardisieren
- [ ] 3.8 404.php: Hooks standardisieren
- [x] 3.9 Alle Templates: `<main id="main-content">` in header.php, `</main>` in footer.php ✅

## Phase 4: theme.json
- [x] 4.1 `requires_cms` auf `"2.5.0"` angehoben
- [x] 4.2 `og_default_image` war bereits im Customization-Schema vorhanden ✅
- [x] 4.3 Version bump → `1.1.0`

## Phase 5: Verbesserungen
- [ ] 5.1 `partials/post-card.php` extrahieren (wiederverwendbar für index + blog)
- [ ] 5.2 `partials/sidebar.php` als eigenständiges Partial
- [ ] 5.3 `partials/author-box.php` für Post-Templates
- [x] 5.4 update.json aktualisiert (v1.1.0, tested_up_to 2.5.4, release_date 2025-07-14)
- [ ] 5.5 365CMS/assets einbinden die sinnvoll sind!
- [ ] 5.6 365CMS Consent Banner einbinden!
