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
