# cms-phinit Theme – Umbau-Plan (365CMS v2.5.4 Migration)

> Erstellt: 2026-03-08  
> Zuletzt aktualisiert: 2026-03-08  
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
- [x] 1.5 Live-Preview-Iframe mit Device-Umschalter (Desktop/Tablet/Mobile) → Drawer-Panel (fixed right, per JS, Esc zum Schließen)
- [x] 1.6 Font-Preview-Widget für Typografie-Tab → Vorschau-Div nach jedem Font-Select (Google Fonts on-demand laden)
- [x] 1.7 Farb-Palette-Preset-Auswahl → 5 Presets (Phinit, Blue Steel, Green Tech, Slate Dark, Ruby Red) als Buttons im Farben-Tab
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
- [x] 1.5 Live-Preview-Iframe mit Device-Umschalter
- [x] 1.6 Font-Preview-Widget für Typografie-Tab
- [x] 1.7 Farb-Palette-Preset-Auswahl

### Zukünftige Verbesserungen
- [~] 6.1 `partials/post-card.php` in `search.php` integrieren – Mixed-Type Suchergebnisse (post/page/company/event/speakers) erfordern eigenes List-Layout; post-card Partial passt strukturell nicht (kein Bild, anderes Markup), daher N/A
- [x] 6.2 `post-tech.php` + `post-wide.php`: `data-photoswipe` auf `.post-body` ergänzt
- [x] 6.3 Customizer-Tab `performance`: `enable_photoswipe` Toggle ergänzt; `functions.php` liest den Wert via `ThemeCustomizer::instance()->get()` und bedingt PhotoSwipe CSS/JS
- [x] 6.4 Version bump → `1.2.0` + update.json aktualisiert (Changelog-Eintrag v1.2.0)

---

## Bugfixes (2026-03-08)
- [x] B1 `member/profile.php`: `CMS\Database::get_results()` gibt `stdClass` zurück (FETCH_OBJ) – `$metaRows`-Schleife mit `array_map(fn($r) => (array)$r, ...)` gefixt
- [x] B2 `member/favorites.php`: `{prefix}categories` existiert nicht → korrekte Tabelle `{prefix}post_categories`; `$favorites`-Query auf `array_map`-Cast umgestellt
- [x] B3 `member/comments.php`: `ORDER BY c.created_at` → `c.post_date` (korrekte Spalte laut SchemaManager); Template-Feld `$c['created_at']` → `$c['post_date']`; `$comments`-Cast auf Array
- [x] B4 `member/feeds.php`: `WHERE status = 'active'` → `WHERE is_active = 1`; `ORDER BY title` → `ORDER BY name`; `$channels` + `$subRows` auf Array gecastet; Template `$ch['title']` → `$ch['name']`
- [x] B5 `admin/customizer.php`: Tabs `seo` + `performance` fehlten in `$navGroups` und `$tabGroups` → ergänzt unter „⚙️ Sonstiges"

## Neue Features (2026-03-08)
- [x] F1 `admin/customizer.php`: Live-Vorschau-Drawer (fixed-right, Iframe, Device-Switcher Desktop/Tablet/Mobil, Esc-Taste, Refresh-Button)
- [x] F2 `admin/customizer.php`: Farb-Palette Schnell-Presets im Farben-Tab (5 Presets: Phinit Standard, Blue Steel, Green Tech, Slate Dark, Ruby Red)
- [x] F3 `admin/customizer.php`: Font-Preview-Widget im Typografie-Tab (Google Fonts on-demand, Live-Text unter jedem Font-Select)
- [x] F4 Version bump → `1.3.0` (theme.json, update.json, functions.php)

## Bugfixes (2026-03-08) – Runde 2
- [x] B6 `member/dashboard.php`: `$recentComments` + `$recentFavorites` ohne `array_map`-Cast; `ORDER BY c.created_at` → `c.post_date`; Template `$c['created_at']` → `$c['post_date']`
- [x] B7 `member/newsletter.php`: `$lists` + `$subRows` ohne Cast; `$sr['list_id']` → Fatal Error → beides `array_map(fn($r)=>(array)$r, ...)`
- [x] B8 `member/forum.php`: `$threads` + `$myThreads` ohne Cast → beides `array_map`
- [x] B9 `member/security.php`: Template nutzte bereits `is_object($session) ? (array)$session : ...` → kein Fix nötig ✓

---

## Neue Features (2026-03-09)
- [x] F5 `functions.php – SEO-Customizer-Anbindung vollständig`: `<meta name="robots">` aus `seo.meta_robots` + `noindex_search`/`noindex_404`; `canonical_self` Toggle; `og_site_name`; `twitter_card_type`; `og_type_default`; `structured_data` Toggle in `outputSchemaOrg()`; `breadcrumb_schema` Toggle in `outputBreadcrumb()`
- [x] F6 `functions.php – DNS-Prefetch`: `outputPreconnect()` erweitert um `dns_prefetch` Toggle + `<link rel="dns-prefetch">` für Google Fonts + eigene URLs
- [x] F7 `style.css – Dark Mode Member- & Auth-Bereich`: `.auth-card`, `.auth-wrapper`, `.member-sidebar`, `.member-nav-link`, `.search-result-item`, `.member-content-card` ergänzt
- [x] F8 `style.css – Version im Header auf 1.4.0`

## Versionsbump (2026-03-09)
- [x] V1 `theme.json` Version → 1.4.0
- [x] V2 `functions.php` `CMS_PHINIT_THEME_VERSION` → 1.4.0
- [x] V3 `update.json` Version + release_date + Changelog-Eintrag v1.4.0

