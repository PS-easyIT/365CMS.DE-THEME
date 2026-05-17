# 365Network Theme – Changelog

## 📋 Legende

| Symbol | Typ | Bedeutung |
|--------|-----|-----------|
| 🟢 | `feat` | Neues Feature |
| 🔴 | `fix` | Bugfix |
| 🟡 | `refactor` | Code-Umbau ohne Funktionsänderung |
| 🎨 | `style` | Design- / UI-Änderungen |
| 🔵 | `docs` | Dokumentation |

---

## v3.4.14 — Mai 2026

### Re-Audit-Pass: prepared LIMIT/OFFSET in allen Directory-Templates, Markup-Fix, identitätsstiftender CTA-Gradient

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | SQL/Directories | `experts.php`, `companies.php`, `events.php`, `jobs.php`, `speakers.php`, `feeds.php` und `booking.php` binden `LIMIT`/`OFFSET` jetzt als parametrisierte Werte (`LIMIT ? OFFSET ?` mit angehängtem Spread `[...$params, $perPage, $offset]`), statt die Integer in den SQL-String zu interpolieren. Damit folgen die Directory-Listen demselben Prepared-Statement-Muster wie die Sidebar-Widgets. |
| 🔴 fix | Robustheit/Directories | Count-Row-Zugriffe auf `$cRow->cnt` waren zuvor in fünf Templates als `(($cRow)->cnt ?? 0)` formuliert und konnten bei einer leeren Ergebnismenge (`fetch()` liefert `false`) einen Fehlerzustand auslösen. Jetzt überall einheitlich `is_array($cRow) ? ($cRow['cnt'] ?? 0) : ($cRow->cnt ?? 0)`. |
| 🔴 fix | Markup | `blog-single.php` Related-Posts-Bild hatte einen gebrochenen `<img>`-Tag (Attribute `width`/`height` leakten als Text in das nachfolgende Markup). Korrekt geschlossen und auf separate Zeilen normalisiert. |
| 🎨 style | Design/CTA | `.homepage-cta--gradient` nutzte zuvor `linear-gradient(135deg, navy, #1e40af)` — ein generischer Navy→Royal-Blue-Verlauf, der nicht zur Theme-Identität passt. Ersetzt durch `Navy → Primary-Light → Gold (accent-hover)` mit einem dezenten Gold-Glow rechts via `radial-gradient`. Das passt zur Markenpalette und vermeidet das KI-Slop-Muster „Standard-Blau-Verlauf für jede CTA". |
| 🔵 docs | Customizer | Customizer-Label von „Gradient (Navy → Blau)" auf „Gradient (Navy → Gold)" in `theme.json` und `admin/customizer.php` synchronisiert, damit Backend und CSS-Realität übereinstimmen. |
| 🟡 refactor | Versions | `THEME_VERSION` (`functions.php`), `style.css`, `theme.json` und `update.json` synchron auf `3.4.14` angehoben; `update.json` um den 3.4.14-Eintrag und ein aktuelles Release-Datum ergänzt. |

---

## v3.4.13 — Mai 2026

### Theme-CSS-Cache-Busting synchronisiert

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Cache-Busting | `THEME_VERSION` in `functions.php` auf `3.4.13` synchronisiert, damit Browser die geänderte `style.css` nicht weiter über die alte `3.4.11`-URL laden. |

## v3.4.12 — Mai 2026

### Plugin-Body-Klassen im Header verfügbar

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Header | `header.php` gibt den `body_class`-Filter jetzt am `<body>` aus. Dadurch greifen Plugin-spezifische Body-Klassen und Layout-Resets auf öffentlichen Pluginseiten zuverlässig. |

## v3.4.10 — März 2026
---

## v3.4.11 — März 2026

### Menüeditor erkennt 365Network-Menüpositionen jetzt auch im Admin korrekt

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Menüeditor/Admin | `theme.json` deklariert die 365Network-Menüpositionen `primary`, `mobile`, `footer` und `speaker` jetzt explizit. Dadurch kann der Core-Menüeditor die Slots auch im Admin-Modus erkennen, obwohl dort `functions.php` des aktiven Themes nicht vollständig gebootet wird. |
| 🟡 refactor | Theme-Metadaten | Theme-Versionen in `theme.json`, `functions.php`, `style.css` und `update.json` auf `3.4.11` synchronisiert, damit der Menüeditor-Fix konsistent ausgerollt wird. |

## v3.4.10 — März 2026

### Footer- und Fallback-Konsistenz nachgeschärft

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟡 refactor | Footer | `footer.php` bündelt Verzeichnis-, Ressourcen-, Dashboard- und Legal-Links jetzt in vorab normalisierten Safe-URL-Variablen statt dieselben `theme_safe_url()`-Aufrufe mehrfach inline zu wiederholen. |
| 🔴 fix | Security/Head | `functions.php` normalisiert `og:url` jetzt explizit über `theme_safe_external_url()` und escaped die Google-Preconnect-Hints konsistent als HTML-Attribute. |
| 🟡 refactor | Templates | `index.php` folgt jetzt ebenfalls dem `declare(strict_types=1)`-Standard des Repositories; `footer.php` bündelt seine Safe-URL-Ziele zentral statt sie mehrfach inline zusammenzusetzen. |

## v3.4.9 — März 2026

### Konsistentes Attribut-Escaping für Theme-Assets

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Security/Assets | `functions.php` escaped die statischen Theme-Asset-URLs für `style.css`, `js/navigation.js` und `js/theme.js` jetzt explizit vor der Ausgabe in `href`/`src`-Attributen. |
| 🟡 refactor | Output-Consistency | Die Head-/Footer-Asset-Ausgabe folgt damit demselben Attribut-Escaping-Standard wie Meta-Tags, Route-Links und die übrigen fail-closed URL-Pfade im Theme. |

## v3.4.8 — März 2026

### Homepage-Links und Medien fail-closed gehärtet

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Security/Homepage | `home.php` baut Startseiten-Links zu Experten, Events, Firmen, Speakern, Jobs, Feed und Blog jetzt konsistent über `theme_safe_url()` statt über rohe `SITE_URL`-Verkettungen auf. |
| 🔴 fix | Security/Homepage | Expertenfotos und Firmenlogos werden im Homepage-Listing vor der Ausgabe normalisiert; Detail-Buttons für Experten, Events und Firmen fallen bei ungültigen Zielen sauber auf sichere Bereichs-URLs zurück. |
| 🔴 fix | Security/Homepage CTA | Die CTA-Sektion normalisiert primäre und sekundäre Button-Ziele jetzt ebenfalls über `theme_safe_url()`, statt relative Werte manuell zu absoluten URLs zu verketten. |

## v3.4.7 — März 2026

### Hotfix für den Theme-Editor

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Customizer/Bootstrap | `admin/customizer.php` ruft beim Speichern von `sidebar_custom_html` nicht mehr ungeschützt `theme_sanitize_html()` auf. Stattdessen greift jetzt ein lokaler Fallback, der zuerst `theme_sanitize_html()`, dann `sanitize_html()` und zuletzt eine restriktive `strip_tags()`-Allowlist verwendet. |
| 🟢 ops | Release | Theme-Version auf **3.4.7** angehoben und `update.json` für den Hotfix synchronisiert. |

## v3.4.6 — März 2026

### Header-Branding im Theme-Editor steuerbar

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Customizer/Header | Im 365Network-Customizer gibt es jetzt das Feld **„Header-Sitetitel“**. Darüber lässt sich der im Header gerenderte Branding-Titel direkt im Theme-Editor pflegen, ohne den globalen CMS-Sitetitel ändern zu müssen. |
| 🔴 fix | Header/Branding | `header.php` nutzt für Logo-Alt, Branding-Link und sichtbaren Header-Titel jetzt zuerst den Customizer-Wert `header.site_title_text` und fällt nur bei leerem Wert auf den CMS-Sitetitel zurück. |

## v3.4.5 — März 2026

### Dritte Audit-Welle: fail-closed URL-Härtung, Cookie-Banner ohne Inline-Script, sichere Seiten-/Blog-Ausgabe

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Security/Helpers | `functions.php` schärft `theme_safe_url()` jetzt fail-closed nach: Nur relative Ziele sowie `http`/`https` bleiben erlaubt; protokollfremde oder doppelschrägige Ziele fallen konsequent auf sichere Fallbacks zurück. `theme_nav_menu()` rendert gespeicherte Menü-URLs nicht mehr roh. |
| 🔴 fix | Security/Page | `page.php` nutzt für Seiteninhalte jetzt den Core-HTML-Sanitizer statt einer nackten `strip_tags()`-Allowlist. Dadurch werden erlaubte Inhalte weiter gerendert, aber riskante Attribut-/URL-Konstellationen nicht mehr direkt aus DB-Content durchgereicht. |
| 🔴 fix | Security/Blog | `blog.php` normalisiert Post-URLs, Bilder, Filter- und Pagination-Links jetzt über sichere Theme-Helper, trennt rohe Filterwerte von escaped Anzeige-Werten und vermeidet damit inkonsistente aktive Zustände und unvalidierte Linkziele. |
| 🔴 fix | Security/Directories | `experts.php`, `jobs.php`, `speakers.php`, `events.php`, `companies.php` und `booking.php` bauen Breadcrumb-, Reset-, Detail-, View- und Pagination-Links jetzt konsistent fail-closed über `theme_safe_url()` bzw. `theme_build_query_url()` statt rohe Pfadverkettungen oder direkte `$_GET`-Merges zu rendern. |
| 🔴 fix | Security/Feeds | `feeds.php` nutzt jetzt sichere Basis-URLs in Breadcrumbs/Formularen, formatiert Datumswerte fail-closed und vervollständigt den bereits vorhandenen Kategorien-Parameter durch eine echte Filter-UI samt sauber erhaltenem Query-State. |
| 🟡 refactor | JS/Cookie | Das Cookie-Banner läuft nun ohne eingebettetes Inline-`<script>` direkt aus `functions.php`; die Consent-Logik sitzt in `js/theme.js`, nutzt robuste `localStorage`-Wrapper und respektiert weiterhin fail-closed Sichtbarkeit. |
| 🟡 refactor | Templates/CSS | `page.php`, `blog.php`, `blog-single.php`, `home.php`, `index.php`, `booking.php`, `error.php` und die Sidebar-Widgets in `functions.php` bauen mehrere Rest-Inline-Stile ab; damit verbleiben in den Theme-PHP-Templates keine eingebetteten `<style>`-Blöcke mehr. `style.css`, `theme.json`, `update.json` und `THEME_VERSION` wurden auf `3.4.5` synchronisiert. |
| 🟡 refactor | JS/Robustheit | `js/navigation.js` ersetzt direkte `body.style.overflow`-Manipulationen durch eine zentrale Scroll-Lock-Klasse und nutzt für Media-Query-Listener einen Legacy-Fallback; `js/theme.js` verhindert doppelte Scroll-to-top-Buttons. |

---

## v3.4.4 — März 2026

### Feinschliff-Nachtrag: gemeinsame Route-Helper, Fokusführung und leichtere Overlay-Interaktionen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Customizer/Admin | `admin/customizer.php` erkennt jetzt, wenn der eingebettete Admin-Section-Shell den `theme_customizer`-Token bereits verifiziert hat, und vermeidet dadurch den doppelten CSRF-Check, der zuvor beim Speichern fälschlich „Sicherheitscheck fehlgeschlagen“ auslöste. |
| 🟡 refactor | Routing/Helpers | `functions.php` ergänzt mit `theme_route_path()` und `theme_route_url()` eine gemeinsame interne Link-Factory für wiederkehrende Frontend-Routen. Erste Hotspots wie Header-Dropdown, 404-Fallbacks sowie Sidebar-Blog-/Speaker-Widgets nutzen diese Helfer bereits statt mehrfacher `SITE_URL`-Verkettungen. |
| 🔴 fix | Accessibility/Header | `header.php` schärft Search-Overlay und Mobile-Drawer mit `aria-modal`, `aria-labelledby` und fokussierbaren Container-Fallbacks nach, damit seltene Fokuspfade in Dialog-/Offcanvas-Zuständen robuster bleiben. |
| 🔴 fix | Accessibility/JS | `js/navigation.js` kapselt Fokusfalle, Fokus-Rückgabe und zentrales Scroll-Locking jetzt sauber für Mobile-Menü und Search-Overlay; ESC- und Tab-Navigation verhalten sich damit konsistenter in Randpfaden. |
| 🟡 refactor | Performance/JS | `js/theme.js` entfernt das Cookie-Banner nach vorhandenem Consent sofort aus dem DOM, blendet es nach Auswahl kontrolliert aus und pausiert die Header-Canvas-Animation außerhalb des Viewports bzw. bei verstecktem Tab. |

### Dritte Audit-Welle: fail-closed URL-Härtung, Cookie-Banner ohne Inline-Script, sichere Seiten-/Blog-Ausgabe

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Security/Auth | `login.php` und `register.php` laufen jetzt mit `declare(strict_types=1)`, sicheren Ziel-URLs und ausgelagerten CSS-Klassen statt verstreuten Inline-Styles; externe Rechtstexte im Register-Formular öffnen mit `rel="noopener noreferrer"`. |
| 🔴 fix | Security/Header/Footer | `header.php` normalisiert Header-Logo- und Such-URLs fail-closed, `footer.php` validiert Social-Links jetzt explizit via `theme_safe_external_url()` und rendert lokale Footer-Ziele konsistent über sichere Theme-URLs. |
| 🟡 refactor | JS/Performance | Die Canvas-Netzwerk-Animation wurde aus dem Inline-`<script>` in `header.php` nach `js/theme.js` verschoben und per `data-*` konfigurierbar gemacht; der Scroll-to-Top-Button nutzt jetzt CSS-Klassen statt Laufzeit-`style.cssText`. |
| 🟡 refactor | Templates/Search | `search.php` kapselt Badge-Farben, Formular-Layout und leere Zustände jetzt in `style.css`, escaped Suchtreffer-URLs strikter und vermeidet dynamische Inline-Farb-Styles im Markup. |
| 🔵 docs | Audit | Neues Dokument `DOC/365Network/THEME-AUDIT.md` ergänzt Audit-Scorecard, Prüfumfang, abgearbeitete Maßnahmen, Validierungsstand und offene Restpunkte für die nächsten Batches. |

---

## v3.4.3 — März 2026

### Security-Hardening, sichere Query-Links und robuster Customizer

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🔴 fix | Security/Theme | Neue Theme-Helper härten URL- und Query-Generierung (`theme_safe_url()`, `theme_safe_external_url()`, `theme_build_query_url()`) und werden jetzt in `booking.php`, `companies.php`, `events.php`, `feeds.php` sowie Sidebar-Widgets genutzt, statt rohe `$_GET`-Merges oder unvalidierte Fremd-URLs zu rendern. |
| 🔴 fix | Security/Content | `blog-single.php` sanitisiert Artikelinhalt jetzt explizit über den Core-HTML-Sanitizer und escaped relative Zeitangaben/Related-Links konsistent, wodurch die gemeldeten XSS-Hotspots aus DB-Inhalten geschlossen wurden. |
| 🔴 fix | Security/Customizer | `admin/customizer.php` validiert Tab-/Feldnamen strikter, escaped dynamische Formular-Attribute konsequent und prüft Logo-Uploads zusätzlich auf echten Upload-Status, MIME-Type und 2-MB-Limit. |
| 🟡 refactor | Customizer/Content | Sidebar-Custom-HTML wird jetzt vor der Ausgabe über den Core-Sanitizer gefiltert; der Header-Logo-Upload erlaubt aus Sicherheitsgründen nur noch JPG/PNG/GIF/WebP statt SVG-Uploads. |
| 🟡 refactor | JS/UX | `js/theme.js` nutzt jetzt den projektweiten Dark-Mode-Storage-Key `cms365-theme` (mit Legacy-Migration von `cms_dark_mode`) und der Sort-Select in `booking.php` submitet ohne Inline-`onchange`. |
| 🔴 fix | Admin/Customizer | `admin/customizer.php` unterstützt jetzt den eingebetteten Admin-Modus (`embedInAdminLayout`) des Core-Theme-Editors und lädt dabei nur noch die theme-spezifischen Assets nach; damit kollidiert der 365Network-Customizer nach dem Deployment nicht mehr mit der umgebenden Admin-Shell. |

---

## v3.4.2 — März 2026

### Customizer-Admin ohne Inline-CSS/-JS

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟡 refactor | Customizer/Admin | `admin/customizer.php` bindet den 365Network-Customizer jetzt über ausgelagerte Assets `css/customizer-admin.css` und `js/customizer-admin.js` an, statt Layout-Regeln und Verhaltenslogik direkt inline auszuliefern. |
| 🔴 fix | Customizer/Security | Reset-Bestätigung, Logo-Vorschau und Farb-Synchronisierung arbeiten jetzt datengetrieben ohne `onclick`-, `oninput`- oder `onchange`-Handler; die URL-Vorschau rendert Fehlerzustände DOM-basiert statt über inline injiziertes `onerror`. |
| 🔴 fix | Admin | Der 365Network-Customizer prüft jetzt explizit `Auth::instance()->isAdmin()` vor dem Rendern der Oberfläche und leitet Nicht-Admins sauber zurück zur Site. |
| 🟡 refactor | Templates/JS | Directory-Filter in `companies.php`, `experts.php`, `events.php`, `feeds.php`, `jobs.php` und `speakers.php` submitten jetzt zentral über `js/theme.js` via `data-auto-submit-filter`; auch der Zurück-Button in `404.php` läuft nun ohne Inline-`history.back()` und mit Fallback-URL. |
| 🎨 style | Templates/CSS | Wiederkehrende statische Inline-Stile für Filter-Link-Listen, Reset-Buttons und die 404-Shell wurden in `style.css` nach `.filter-link-list`, `.filter-reset-btn`, `.empty-state-reset-btn` und `.error-page-shell` überführt. |

---

## v3.4.0 — März 2026

### Design-Overhaul: Mobiler Filter-Drawer, 4-Breakpoint-System, Card-Polish

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Mobile | **Filter-Drawer** für alle 5 Verzeichnis-Seiten (experts, companies, events, speakers, jobs): Slide-in-Panel von links, Dark-Overlay, Escape/Klick-außen schließt, ARIA `aria-expanded`. |
| 🟢 feat | JS | `initFilterDrawer()` in `navigation.js` als eigenes Modul integriert, aufgerufen aus `init()`. |
| 🎨 style | Responsive | **4-stufiges Breakpoint-System**: 1280px (Container), 1024px (Drawer-Breakpoint, 2-col Grids), 640px (1-col), 480px (Phone-Optimierungen). |
| 🎨 style | Touch | Min-Height 42–44px für alle interaktiven Elemente auf ≤ 1023px (Filter-Selects, Inputs, Apply-Btn, Row-Toggle, Event-Chips). |
| 🎨 style | Events | Events-Tab-Bar horizontal scrollbar auf mobil (kein Wrapping mehr). |
| 🎨 style | Cards | Accent-Left-Border-Hover bei List-View-Karten (experts, companies, events, speakers); goldfarbene Top-Linie bei Grid-Karten-Hover; Job-Karte mit Border-Left-Accent. |
| 🎨 style | Feed | Bild-Zoom-Effekt (`scale(1.04)`) bei Feed-Card-Hover. |
| 🟢 feat | HTML | `id="directoryFilters"` & `id="filterForm"` ergänzt in events.php, speakers.php, jobs.php. Drawer-Header (Titel + Schließen-Button) in allen 5 Filterpanels. |
| 🟢 feat | Homepage | Speaker-HP-Karte mit Avatar-Placeholder (Initialen-Basis), Job-HP-Karte mit `border-left: 3px solid transparent` Hover-Accent. |
| 🟢 feat | Styling | `.active-filters-strip` und `.active-filter-chip` als wiederverwendbare Komponenten für aktive Filter-Anzeige. |

## v3.3.0 — März 2026

### Vollständige Verzeichnis-Plattform: Jobs, Feeds, Buchung, Speaker-Widget

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Templates | **7 neue Page-Templates**: `experts.php`, `companies.php`, `events.php`, `speakers.php`, `jobs.php`, `feeds.php`, `booking.php` — alle mit konsistentem Directory-Layout (Hero, Filterpanel, Toolbar, Grid/List-Toggle, Pagination). |
| 🟢 feat | Stellenmarkt | **`jobs.php`**: Remote-Toggle, Jobtyp-Filter, Sektor-Dropdown (DB-basiert), Gehaltsbadge, Skill-Tags, Job-Alert-Button (Login-gated). Tabelle `{prefix}job_profiles`. |
| 🟢 feat | Feed-Aggregator | **`feeds.php`**: Quellen-Sidebar statt Filter, LEFT JOIN auf `{prefix}feeds`, Kategorie-Filter, externe Links mit `target="_blank"`. |
| 🟢 feat | Buchungsportal | **`booking.php`**: Radio-Servicepicker mit Dauer/Preis, Experten-Dropdown, Datums-/Uhrzeitauswahl, Nachrichtenfeld, CSRF-Schutz, Buchungsverlauf-Sidebar (letzte 10 Buchungen), Status-Badges. |
| 🟢 feat | Speaker | **`speakers.php`**: Topic-Tag-Cloud (Top 15, frequenzbasiert), Events-Zähler-Badge, Avatar-Initialen-Fallback. |
| 🟢 feat | Events | **`events.php`**: Upcoming/Past-Tabs, Typ-Chips (conference/summit/workshop/webinar/meetup), Monats-Filter, Datumsblock-Darstellung. |
| 🟢 feat | Homepage | **Speakers- & Jobs-Sektion** auf der Startseite: Avatar-Grid für Speaker, kompakte Listenform für Jobs. Neue Hooks: `home_after_speakers`, `home_after_jobs`. |
| 🟢 feat | Sidebar | **Speaker-Widget** (`renderSidebarSpeakersWidget`, Priority 25): Zeigt Featured Speaker mit Avatar-Initial, Hauptthema und Event-Zahl. Nur sichtbar wenn `cms-speakers` aktiv. |
| 🟢 feat | Navigation | **Header-Dropdown** erweitert: Jobs (💼 Stellenmarkt) und Buchungen (📅 Meine Buchungen) — per Customizer steuerbar (`profile_show_jobs`, `profile_show_booking`). |
| 🟢 feat | Footer | Verzeichnis-Spalte um Jobs, Feeds und Buchungsportal erweitert. |
| 🟢 feat | Customizer | **8 neue Settings** in `homepage`: `show_speakers_section`, `speakers_section_title`, `speakers_limit`, `show_jobs_section`, `jobs_section_title`, `jobs_limit`. |
| 🟢 feat | Customizer | **2 neue Settings** in `header`: `profile_show_jobs`, `profile_show_booking`. |
| 🟢 feat | Customizer | **4 neue Kategorien**: `speakers` (5 Settings), `jobs` (7 Settings), `feeds` (6 Settings), `booking` (8 Settings). |
| 🎨 style | CSS | **Directory-Layout-System**: `.directory-hero`, `.directory-layout`, `.filter-panel`, `.filter-tag-cloud`, `.directory-toolbar`, `.view-toggle`, `.directory-pagination` — alle neuen Card-Typen und Dark Mode für alle Verzeichnisseiten. |
| 🟢 feat | Menü | `seedDefaultMenus()` um Jobs und Feeds erweitert (werden beim ersten Start angelegt). |
| 🔵 docs | Theme | `theme.json` v3.3.0, `update.json` aktualisiert, neue Customizer-Kategorien in `theme.json` dokumentiert. |

---

## v3.2.0 — 15. März 2026

### Profil-Dropdown, Blog/Feed-Fallback, Customizer-Verbesserungen

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Header | **Profil-Dropdown**: Login-Button wird nach Login zum aufklappbaren Profil-Menü mit Dashboard, Experten-Profil, Firmenprofil, Events und Speaker-Links. Einträge per Customizer steuerbar (`header.profile_show_*`). Plugin-Links erscheinen nur wenn das jeweilige Plugin aktiv ist. |
| 🟢 feat | Homepage | **Blog/Feed-Fallback**: Wenn alle Content-Sektionen (Experten, Events, Firmen) deaktiviert sind, werden automatisch die letzten Blog-Posts oder Feed-Items (cms-feed Plugin) angezeigt. |
| 🎨 style | Customizer | **Farben 3-Spalten-Layout**: Farbeinstellungen werden als 3 Karten nebeneinander angezeigt (Markenfarben, Text & Links, Hintergrund & Status). |
| 🔴 fix | Homepage | **Boolean-Bindings**: `(bool)` Cast durch `filter_var(FILTER_VALIDATE_BOOLEAN)` ersetzt für robuste Auswertung von DB-Werten (`'0'`, `'1'`, `'true'`, `'false'`). |
| 🔴 fix | Header | **Boolean-Bindings**: Gleicher Fix für Header-Customizer-Settings (Search, Login, Register Buttons). |
| 🟢 feat | Customizer | 6 neue Settings in Kategorie `header`: `profile_show_dashboard`, `profile_show_expert`, `profile_show_company`, `profile_show_events`, `profile_show_speaker`. |
| 🎨 style | CSS | Neue Klassen: `.profile-dropdown`, `.profile-dropdown-menu`, `.profile-dropdown-item`, `.feed-grid`, `.feed-card`. Mobile-optimiertes Bottom-Sheet für Profil-Dropdown. |
| 🟢 feat | JS | `navigation.js`: Profil-Dropdown Toggle mit ARIA-Attributen, ESC-Schließung, Click-Outside-Handling. |
| 🔵 docs | Theme | CHANGELOG.md erstellt, update.json aktualisiert, theme.json v3.2.0. |

---

## v3.1.0 — März 2026

### Hook-basierte Homepage-Architektur

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Homepage | 15 Action-Hooks + 3 Filter-Hooks für Plugin-Injektionen. |
| 🟢 feat | Customizer | 17 neue Homepage-Settings (Sektions-Toggle, Texte, Limits, Layout). |
| 🟢 feat | Sidebar | Default-Sidebar-Widgets (Buchung, Feed, Jobs) in functions.php. |
| 🎨 style | CSS | Layout-Varianten (sidebar-right, sidebar-left, full-width), Widget-Zonen. |

---

## v3.0.0 — 07. März 2026

### Dashboard-Redesign: Deep Navy & Gold

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🎨 style | Komplett | Deep Navy (#0c1526) + Gold (#c8952e) Farbschema. |
| 🟢 feat | Komplett | Modulares Kachel-Layout für Experten, Firmen, Events & Speaker. |
| 🟢 feat | Header | Netzwerk-Animation (Canvas), Dark Mode mit localStorage-Persistenz. |
| 🟢 feat | Footer | 4-Spalten Widget-Footer mit Social-Media-Links. |
| 🟢 feat | Customizer | 9 Kategorien: Farben, Typografie, Layout, Header, Footer, Buttons, Homepage, Effekte, Erweitert. |

---

## v2.1.0

Performance-Verbesserungen, neue Widget-Bereiche.

## v2.0.0

Komplettes Rewrite für CMS v2.

## v1.0.0

Erstveröffentlichung.
