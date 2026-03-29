# 365Network Theme – Dokumentation

> **Theme-Slug:** `365Network`  
> **Version:** 3.4.4  
> **Design:** Deep Navy (#0c1526) + Gold (#c8952e)  
> **Typ:** Dashboard-orientiertes IT-Experten-Netzwerk-Theme

---

## Inhaltsverzeichnis

| Datei | Inhalt |
|---|---|
| [README.md](README.md) | Diese Übersicht |
| [HOMEPAGE-HOOKS.md](HOMEPAGE-HOOKS.md) | Alle Homepage-Hooks (15 Actions, 3 Filter) |
| [CUSTOMIZER.md](CUSTOMIZER.md) | Alle Customizer-Kategorien & Settings |
| [PLUGIN-INTEGRATION.md](PLUGIN-INTEGRATION.md) | Plugin-Anbindung mit Code-Beispielen |
| [DEVELOPMENT.md](DEVELOPMENT.md) | Entwickler-Leitfaden & Architektur |
| [THEME-AUDIT.md](THEME-AUDIT.md) | Laufende Audit-Dokumentation mit Scorecard, Findings und erledigten Maßnahmen |

---

## 1. Theme-Überblick

Das 365Network-Theme ist ein **Dashboard-Style-Theme** für die 365CMS-Plattform. Es kombiniert ein dunkles Navy-Design mit goldenen Akzenten und bietet eine vollständig hook-basierte Homepage-Architektur, die es Plugins ermöglicht, eigene Inhalte zu injizieren.

### Aktueller Audit-Stand (März 2026)

- Security-Hotspots in Blog, Booking, Events, Feeds, Companies und Customizer wurden in `v3.4.3` gehärtet.
- Query-Link-Generierung läuft jetzt zentralisiert und fail-closed über Theme-Helper statt rohe `$_GET`-Merges zu rendern.
- Der Customizer escaped dynamische Feldattribute explizit und prüft Uploads zusätzlich auf MIME-Type und Größenlimit.
- Zweite Audit-Welle in `v3.4.4`: Auth-/Search-Templates bereinigt, Header-Canvas-Script in `js/theme.js` verlagert, Footer-/Logo-/Social-URLs fail-closed gehärtet.
- Aktuelle manuelle Audit-Scorecard: **Security 94/100 · Best Practice 92/100 · Performance 89/100 · Maintainability 91/100**.

### Design-Philosophie

- **Deep Navy Background** (`#0c1526`) – Professioneller, moderner Look
- **Gold Accents** (`#c8952e`) – Hochwertige Akzentfarbe für CTAs und Highlights
- **Dashboard-Layout** – Zweispaltiges Grid mit Hauptinhalt + Sidebar
- **Plugin-Ready** – Jede Sektion über CMS-Hooks erweiterbar

### Zielgruppe

- IT-Experten-Netzwerke
- Firmen-Verzeichnisse
- Event- & Konferenz-Plattformen
- Branchen-Portale mit Experten-Profilen

---

## 2. Dateistruktur

```
365Network/
├── theme.json              ← Metadaten, Design-Tokens, 9 Customizer-Kategorien
├── update.json             ← Auto-Update-Versionsdaten
├── style.css               ← Haupt-CSS (Deep Navy + Gold Design-System)
├── functions.php           ← Theme-Klasse, Hooks, Default-Sidebar-Widgets
├── header.php              ← HTML-Head, Navigation, Canvas-Animation
├── footer.php              ← Footer, Scripts
├── home.php                ← Homepage-Template (Hook-Architektur)
├── page.php                ← Einzel-Seiten-Template
├── index.php               ← Fallback-Template
├── login.php               ← Login-Seite
├── register.php            ← Registrierungs-Seite
├── search.php              ← Suchergebnisse
├── 404.php                 ← 404-Fehlerseite
├── error.php               ← Generische Fehlerseite
├── js/
│   └── navigation.js       ← Burger-Menü, Sticky Header, Dark Mode, Scroll-Animationen
└── admin/
    └── customizer.php      ← Theme-Customizer (9 Tabs, ~100 Settings)
```

---

## 3. Homepage-Architektur (Kurzfassung)

Die Homepage (`home.php`) ist in folgende Sektionen unterteilt:

```
┌─────────────────────────────────────────────┐
│ 🦸 Hero-Sektion (+ Suche)                   │ ← home_before_hero / home_after_hero
├─────────────────────────────────────────────┤
│ 📊 Statistik-Leiste                         │ ← home_after_stats, Filter: home_stats
├────────────────────┬────────────────────────┤
│ 👤 Experten        │ 📅 Sidebar-Widget #1   │
│ 📅 Events          │ 📰 Sidebar-Widget #2   │
│ 🏢 Firmen          │ 💼 Sidebar-Widget #3   │
│ [Plugin-Content]   │ [Plugin-Widgets]       │
├────────────────────┴────────────────────────┤
│ 📆 Events-Fussleiste                        │ ← home_before_strip / home_after_strip
└─────────────────────────────────────────────┘
```

Jede Sektion ist:
- **Über den Customizer ein-/ausschaltbar** (17 Homepage-Settings)
- **Per Hook erweiterbar** (15 Action-Hooks + 3 Filter-Hooks)
- **Layout-flexibel** (Sidebar rechts/links/ohne)

→ Vollständige Hook-Referenz: [HOMEPAGE-HOOKS.md](HOMEPAGE-HOOKS.md)

---

## 4. Customizer-Kategorien

| Kategorie | Tab-Label | Settings |
|---|---|---|
| `colors` | 🎨 Farben | 13 Farb-Variablen |
| `typography` | 🔤 Typografie | 5 Font-Settings |
| `layout` | 📐 Layout | 4 Container/Radius/Schatten |
| `header` | 📌 Header | 5 Header-Optionen |
| `footer` | 🦶 Footer | 3 Footer-Texte |
| `buttons` | 🔘 Buttons | 5 Button-Styles |
| **`homepage`** | **🏠 Startseite** | **17 Homepage-Settings** |
| `effects` | ✨ Effekte | 5 Animations-Settings |
| `advanced` | ⚙️ Erweitert | 3 Code-Injection-Felder |

→ Vollständige Settings-Referenz: [CUSTOMIZER.md](CUSTOMIZER.md)

---

## 5. Plugin-Kompatibilität

Das Theme ist für folgende Plugins vorbereitet:

| Plugin | Integration |
|---|---|
| `cms-experts` | Experten-Cards auf Homepage (DB-Query in home.php) |
| `cms-events` | Events-Karten + Events-Strip (DB-Query in home.php) |
| `cms-companies` | Firmen-Cards auf Homepage (DB-Query in home.php) |
| `cms-speakers` | Stats-Zähler (DB-Query in home.php) |
| `cms-feed` | Sidebar-Widget via `home_sidebar_widget` Hook |
| `cms-jobprofile-generator` | Sidebar-Widget via `home_sidebar_widget` Hook |
| Beliebiges Plugin | Kann über beliebige Hooks Inhalte injizieren |

→ Vollständige Plugin-Integrations-Anleitung: [PLUGIN-INTEGRATION.md](PLUGIN-INTEGRATION.md)

---

## 6. Design-System (Kurzfassung)

### Farb-Palette

| Token | Hex | Verwendung |
|---|---|---|
| `--primary-color` | `#0c1526` | Haupt-Hintergrund, Sidebar-Panels |
| `--primary-dark` | `#060b14` | Dunklerer Variante |
| `--primary-light` | `#1a2744` | Aufgehellte Variante (Hover) |
| `--accent-color` | `#c8952e` | Gold-Akzent, CTAs, Highlights |
| `--accent-light` | `#d4a94e` | Hellerer Gold-Ton |
| `--bg-primary` | `#ffffff` | Karten-Hintergrund |
| `--bg-secondary` | `#f8fafc` | Sekundärer Hintergrund |
| `--text-color` | `#1e293b` | Primärtext |
| `--heading-color` | `#0f172a` | Überschriften |
| `--muted-color` | `#64748b` | Sekundärtext |

### Breakpoints

| Breakpoint | Breite | Layout |
|---|---|---|
| XL | `> 1280px` | 2-Spalten (1fr + 320px / 280px) |
| LG | `≤ 1280px` | 2-Spalten (1fr + 280px) |
| MD | `≤ 1024px` | 1-Spalte, Sidebar darunter |
| SM | `≤ 768px` | Burger-Menü, reduziertes Padding |
| XS | `≤ 480px` | Minimal-Layout |

---

## 7. Quick-Start für Entwickler

### Theme aktivieren

1. Theme-Ordner `365Network/` in `CMS/themes/` kopieren
2. Im Admin unter **Design → Themes** das Theme aktivieren
3. Unter **Design → Customizer** die Homepage-Einstellungen konfigurieren

### Plugin-Widget in Sidebar registrieren

```php
// In der Plugin-Hauptklasse:
\CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderWidget'], 25);
```

### Eigenen Content-Bereich auf der Homepage einfügen

```php
// Nach der Experten-Sektion:
\CMS\Hooks::addAction('home_after_experts', function () {
    echo '<div class="dashboard-section">';
    echo '<div class="section-header"><h2>Mein Plugin-Bereich</h2></div>';
    echo '<p>Plugin-Inhalt hier...</p>';
    echo '</div>';
}, 10);
```

→ Vollständige Entwickler-Referenz: [DEVELOPMENT.md](DEVELOPMENT.md)
