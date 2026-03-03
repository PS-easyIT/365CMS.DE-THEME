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

---

## v3.4.0 — Juni 2026

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

## v3.3.0 — Juni 2026

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

## v3.2.0 — 15. Juni 2026

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

## v3.1.0 — Juni 2026

### Hook-basierte Homepage-Architektur

| Typ | Bereich | Beschreibung |
|-----|---------|-------------|
| 🟢 feat | Homepage | 15 Action-Hooks + 3 Filter-Hooks für Plugin-Injektionen. |
| 🟢 feat | Customizer | 17 neue Homepage-Settings (Sektions-Toggle, Texte, Limits, Layout). |
| 🟢 feat | Sidebar | Default-Sidebar-Widgets (Buchung, Feed, Jobs) in functions.php. |
| 🎨 style | CSS | Layout-Varianten (sidebar-right, sidebar-left, full-width), Widget-Zonen. |

---

## v3.0.0 — 07. Juni 2026

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
