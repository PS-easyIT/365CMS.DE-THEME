# cms-phinit Theme – Umbau-Plan (365CMS v2.5.4 Migration)

> Erstellt: 2026-03-08  
> Zuletzt aktualisiert: 2025-07-16  
> Ziel: Theme-Modernisierung, Customizer-Redesign, Hook-Standardisierung

---

## Status-Legende
- [ ] Offen
- [x] Erledigt
- [-] In Arbeit
- [~] N/A (nicht anwendbar / nicht nötig)

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
- [x] 2.1 Neue CSS-Variablen aus Layout-Tab einbinden (`sidebar_position`)
- [x] 2.2 `generatePhinitCSS()` um neue Customizer-Felder erweitern
- [x] 2.3 Performance: `font-display: swap` in Google-Fonts-Einbindung ✅
- [x] 2.4 SEO-OG-Image-Default aus Customizer (`og_default_image`)
- [x] 2.5 `phinit_reading_time()` liest WPM aus Customizer statt hartkodiertem Wert
- [x] 2.6 PhotoSwipe CSS + JS (CMS-Asset) in `enqueueStyles()` / `enqueueScripts()` eingebunden

## Phase 3: Templates – Hook-Standardisierung
- [x] 3.1 header.php: `doAction('body_start')` nach `<body>` ✅
- [x] 3.2 footer.php: `doAction('before_footer')` + `doAction('footer')` ergänzt
- [x] 3.3 index.php: `doAction('home_content')` ergänzt
- [x] 3.4 post.php: Hooks vorhanden ✅; CommentService-API verifiziert ✅; WPM-Default 200→220 gefixt
- [x] 3.5 blog.php: WPM-Fix + Partial-Integration ✅; after_header via header.php aktiv ✅
- [x] 3.6 page.php: Hooks per header.php/footer.php aktiv ✅
- [x] 3.7 search.php: Hooks aktiv ✅
- [x] 3.8 404.php: Hooks aktiv ✅
- [x] 3.9 Alle Templates: `<main id="main-content">` in header.php, `</main>` in footer.php ✅

## Phase 4: theme.json
- [x] 4.1 `requires_cms` auf `"2.5.0"` angehoben
- [x] 4.2 `og_default_image` im Customization-Schema vorhanden ✅
- [x] 4.3 Version bump → `1.2.0`

## Phase 5: Verbesserungen
- [x] 5.1 `partials/post-card.php` erstellt; in `index.php` + `blog.php` via `get_theme_part()` eingebunden
- [x] 5.2 `partials/sidebar.php` erstellt; TOC + Social + Related Posts + Kategorien extrahiert; in `post.php` eingebunden
- [~] 5.3 `partials/author-box.php` – kein Author-Box-Block in post.php; Autorenname nur inline in Post-Meta (nicht separat extrahierbar)
- [x] 5.4 update.json aktualisiert (v1.1.0, release_date 2025-07-14)
- [x] 5.5 CMS-Assets eingebunden: PhotoSwipe CSS + JS über ASSETS_PATH mit filemtime-Cache-Buster (graceful fallback wenn Datei fehlt); `data-photoswipe` auf `.post-body` in post.php
- [x] 5.6 CMS Consent Banner – vollständig integriert via `Bootstrap.php:345` → `body_end` Hook → `CookieConsentService::render()`; Theme hat kein Legacy-Banner, kein zusätzlicher Code nötig

---

## Offene

### Phase 1 (zurückgestellt)
- [x] 1.3 Neue Tabs: `seo` + `performance` im Customizer
- [ ] 1.5 Live-Preview-Iframe mit Device-Umschalter
- [ ] 1.6 Font-Preview-Widget für Typografie-Tab
- [ ] 1.7 Farb-Palette-Preset-Auswahl

### Zukünftige Verbesserungen
- [~] 6.1 `partials/post-card.php` in `search.php` integrieren – Mixed-Type Suchergebnisse (post/page/company/event/speakers) erfordern eigenes List-Layout; post-card Partial passt strukturell nicht (kein Bild, anderes Markup), daher N/A
- [x] 6.2 `post-tech.php` + `post-wide.php`: `data-photoswipe` auf `.post-body` ergänzt
- [x] 6.3 Customizer-Tab `performance`: `enable_photoswipe` Toggle ergänzt; `functions.php` liest den Wert via `ThemeCustomizer::instance()->get()` und bedingt PhotoSwipe CSS/JS
- [x] 6.4 Version bump → `1.2.0` + update.json aktualisiert (Changelog-Eintrag v1.2.0)

