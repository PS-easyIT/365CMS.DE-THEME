# cms-phinit Theme – Umbau-Plan (365CMS v2.5.4 Migration)

> Erstellt: 2026-03-08  
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
- [-] 1.1 Eigenes `<!DOCTYPE html>` entfernen – Fragment für Admin-Embed
- [ ] 1.2 Tabler-CSS-Klassen verwenden statt eigene Inline-Styles
- [ ] 1.3 Neue Tabs: `seo` + `performance`
- [ ] 1.4 Export/Import-Funktion (JSON) über ThemeCustomizer-API
- [ ] 1.5 Live-Preview-Iframe mit Device-Umschalter (Desktop/Tablet/Mobile)
- [ ] 1.6 Font-Preview-Widget für Typografie-Tab
- [ ] 1.7 Farb-Palette-Preset-Auswahl (Quick-Theme-Presets)
- [ ] 1.8 Collapse-Sektionen pro Feld-Gruppe
- [ ] 1.9 Keyboard-Shortcut (Strg+S) für Speichern
- [ ] 1.10 Unsaved-Changes-Warnung bei Navigation

## Phase 2: functions.php – Modernisierung
- [ ] 2.1 Neue CSS-Variablen aus Layout-Tab einbinden (`content_gap`, `spacing_*`, `sidebar_position`)
- [ ] 2.2 `generatePhinitCSS()` um neue Customizer-Felder erweitern
- [ ] 2.3 Performance: `font-display: swap` in Google-Fonts-Einbindung  
- [ ] 2.4 SEO-OG-Image-Default aus Customizer

## Phase 3: Templates – Hook-Standardisierung
- [ ] 3.1 header.php: `doAction('body_start')` nach `<body>` einfügen
- [ ] 3.2 footer.php: `doAction('before_footer')` + `doAction('footer')` ergänzen
- [ ] 3.3 index.php: `doAction('after_header')` + `doAction('home_content')` ergänzen
- [ ] 3.4 post.php: Hooks prüfen, CommentService-Aufruf verifizieren
- [ ] 3.5 blog.php: `doAction('after_header')` ergänzen
- [ ] 3.6 page.php: Hooks prüfen
- [ ] 3.7 search.php: Hooks standardisieren
- [ ] 3.8 404.php: Hooks standardisieren
- [ ] 3.9 Alle Templates: konsistente `<main>` Nutzung sicherstellen

## Phase 4: theme.json
- [ ] 4.1 `requires_cms` auf `"2.5.0"` anheben
- [ ] 4.2 Customization-Schema mit allen neuen Feldern synchronisieren
- [ ] 4.3 Version bump → `1.1.0`

## Phase 5: Verbesserungen
- [ ] 5.1 `partials/post-card.php` extrahieren (wiederverwendbar für index + blog)
- [ ] 5.2 `partials/sidebar.php` als eigenständiges Partial
- [ ] 5.3 `partials/author-box.php` für Post-Templates
- [ ] 5.4 update.json aktualisieren
- [ ] 5.5 365CMS/assets einbinden die sinnvoll sind!
- [ ] 5.6 365CMS Consent Banner einbinden!
