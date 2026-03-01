# 365Network Theme – Homepage Hooks Referenz

> Vollständige Referenz aller Action- und Filter-Hooks, die in `home.php` verfügbar sind.
> Plugins und andere Themes können über diese Hooks jeden Bereich der Startseite erweitern.

---

## Inhaltsverzeichnis

- [Übersicht: Hook-Architektur](#übersicht-hook-architektur)
- [Action Hooks](#action-hooks)
  - [1. home_before_hero](#1-home_before_hero)
  - [2. home_after_hero](#2-home_after_hero)
  - [3. home_after_stats](#3-home_after_stats)
  - [4. home_before_content](#4-home_before_content)
  - [5. home_main_top](#5-home_main_top)
  - [6. home_after_experts](#6-home_after_experts)
  - [7. home_after_events](#7-home_after_events)
  - [8. home_after_companies](#8-home_after_companies)
  - [9. home_content (Legacy)](#9-home_content-legacy)
  - [10. home_main_bottom](#10-home_main_bottom)
  - [11. home_sidebar_top](#11-home_sidebar_top)
  - [12. home_sidebar_widget](#12-home_sidebar_widget)
  - [13. home_sidebar_bottom](#13-home_sidebar_bottom)
  - [14. home_after_content](#14-home_after_content)
  - [15. home_before_strip](#15-home_before_strip)
  - [16. home_after_strip](#16-home_after_strip)
- [Filter Hooks](#filter-hooks)
  - [1. home_hero_title](#1-home_hero_title)
  - [2. home_hero_subtitle](#2-home_hero_subtitle)
  - [3. home_stats](#3-home_stats)
- [Ausführungsreihenfolge](#ausführungsreihenfolge)
- [CSS-Kontext für Hooks](#css-kontext-für-hooks)

---

## Übersicht: Hook-Architektur

```
╔═══════════════════════════════════════════════════════════╗
║ home_before_hero                                         ║
╠═══════════════════════════════════════════════════════════╣
║ HERO-SEKTION                                             ║
║   Filter: home_hero_title  → Überschrift                 ║
║   Filter: home_hero_subtitle → Untertitel                ║
║   [Suchformular]                                         ║
╠═══════════════════════════════════════════════════════════╣
║ home_after_hero                                          ║
╠═══════════════════════════════════════════════════════════╣
║ STATISTIK-LEISTE                                         ║
║   Filter: home_stats → Array der Stat-Kacheln            ║
╠═══════════════════════════════════════════════════════════╣
║ home_after_stats                                         ║
╠═══════════════════════════════════════════════════════════╣
║ home_before_content                                      ║
╠════════════════════════════╦══════════════════════════════╣
║ MAIN-SPALTE                ║ SIDEBAR                      ║
╠════════════════════════════╬══════════════════════════════╣
║ home_main_top              ║ home_sidebar_top             ║
║ [Experten-Sektion]         ║ home_sidebar_widget ×N       ║
║ home_after_experts         ║ home_sidebar_bottom          ║
║ [Events-Sektion]           ║                              ║
║ home_after_events          ║                              ║
║ [Firmen-Sektion]           ║                              ║
║ home_after_companies       ║                              ║
║ home_content (Legacy)      ║                              ║
║ home_main_bottom           ║                              ║
╠════════════════════════════╩══════════════════════════════╣
║ home_after_content                                       ║
╠═══════════════════════════════════════════════════════════╣
║ home_before_strip                                        ║
╠═══════════════════════════════════════════════════════════╣
║ EVENTS-FUSSLEISTE                                        ║
╠═══════════════════════════════════════════════════════════╣
║ home_after_strip                                         ║
╚═══════════════════════════════════════════════════════════╝
```

---

## Action Hooks

### 1. home_before_hero

**Position:** Ganz oben, vor der Hero-Sektion  
**HTML-Kontext:** Außerhalb jeder Sektion, direkt im `<body>`-Kontext  
**Anwendung:** Benachrichtigungsleisten, Promotion-Banner, Countdowns

```php
\CMS\Hooks::addAction('home_before_hero', function () {
    echo '<div class="promo-banner" style="background:#1e40af;color:white;text-align:center;padding:0.75rem;">
        🎉 Neue Features verfügbar! <a href="/changelog" style="color:#fbbf24;">Mehr erfahren</a>
    </div>';
}, 10);
```

---

### 2. home_after_hero

**Position:** Nach der Hero-Sektion inkl. Suchformular  
**HTML-Kontext:** Zwischen Hero und Stats-Leiste  
**Anwendung:** Breadcrumbs, Sponsor-Logos, Featured-Content-Slider

```php
\CMS\Hooks::addAction('home_after_hero', function () {
    echo '<div class="sponsored-logos" style="text-align:center;padding:1rem;">
        <span style="color:#94a3b8;font-size:0.8rem;">Unterstützt von:</span>
        <!-- Logo-Images -->
    </div>';
}, 10);
```

---

### 3. home_after_stats

**Position:** Nach der Statistik-Leiste  
**HTML-Kontext:** Zwischen Stats und Dashboard-Grid  
**Anwendung:** Zusätzliche KPI-Zeilen, Graphen, Zusammenfassungen

```php
\CMS\Hooks::addAction('home_after_stats', function () {
    echo '<div class="growth-indicator" style="text-align:center;padding:0.5rem;color:#22c55e;">
        📈 +15% neue Experten diese Woche
    </div>';
}, 10);
```

---

### 4. home_before_content

**Position:** Vor dem Dashboard-Grid (Zweispalten-Layout)  
**HTML-Kontext:** Volle Seitenbreite  
**Anwendung:** Ankündigungen, Featured-Content vor dem Grid

```php
\CMS\Hooks::addAction('home_before_content', function () {
    echo '<div class="featured-announcement" style="max-width:var(--container-max-width);margin:0 auto;padding:0 var(--container-padding);">
        <div class="admin-card">Wichtige Ankündigung hier...</div>
    </div>';
}, 10);
```

---

### 5. home_main_top

**Position:** Erster Hook innerhalb der Main-Spalte  
**HTML-Kontext:** Innerhalb `.dashboard-main`  
**Anwendung:** Plugin-Willkommensnachrichten, Schnellzugriff-Leiste

```php
\CMS\Hooks::addAction('home_main_top', function () {
    if (\CMS\Auth::instance()->isLoggedIn()) {
        $user = \CMS\Auth::instance()->getCurrentUser();
        echo '<div class="welcome-bar" style="background:#eff6ff;padding:1rem;border-radius:8px;margin-bottom:1.5rem;">
            Willkommen zurück, <strong>' . htmlspecialchars($user->display_name ?? '') . '</strong>!
        </div>';
    }
}, 10);
```

---

### 6. home_after_experts

**Position:** Nach der Experten-Karten-Sektion  
**HTML-Kontext:** Innerhalb `.dashboard-main`, nach der Experten-Grid  
**Anwendung:** „Weitere Experten"-CTA, Experten-Kategorien, Filter

```php
\CMS\Hooks::addAction('home_after_experts', function () {
    echo '<div class="dashboard-section">
        <div class="section-header"><h2>📚 Wissensgebiete</h2></div>
        <div class="tag-cloud">
            <span class="tag">Cloud</span>
            <span class="tag">DevOps</span>
            <span class="tag">Security</span>
        </div>
    </div>';
}, 10);
```

---

### 7. home_after_events

**Position:** Nach der Events-Karten-Sektion  
**HTML-Kontext:** Innerhalb `.dashboard-main`  
**Anwendung:** Event-Kategorien, Kalender-Widget, Event-Registrierung

```php
\CMS\Hooks::addAction('home_after_events', function () {
    echo '<div class="dashboard-section">
        <a href="/events" class="btn btn-primary" style="width:100%;text-align:center;">
            📅 Alle Events anzeigen
        </a>
    </div>';
}, 10);
```

---

### 8. home_after_companies

**Position:** Nach der Firmen-Karten-Sektion  
**HTML-Kontext:** Innerhalb `.dashboard-main`  
**Anwendung:** Firmen-Kategorien, Branchen-Filter, Werbebanner

```php
\CMS\Hooks::addAction('home_after_companies', function () {
    echo '<div class="dashboard-section">
        <div class="section-header"><h2>🏷️ Branchen</h2></div>
        <div class="industry-tags"><!-- Branchen-Buttons --></div>
    </div>';
}, 10);
```

---

### 9. home_content (Legacy)

**Position:** Nach `home_after_companies`, vor `home_main_bottom`  
**HTML-Kontext:** Innerhalb `.dashboard-main`  
**Anwendung:** Rückwärtskompatibilität – bestehende Plugins, die bereits `home_content` nutzen  
**Hinweis:** Dieser Hook existiert für Abwärtskompatibilität. Neue Plugins sollten spezifischere Hooks verwenden.

```php
// Legacy-kompatibel:
\CMS\Hooks::addAction('home_content', function () {
    echo '<div class="dashboard-section">Legacy-Plugin-Inhalt</div>';
}, 10);
```

---

### 10. home_main_bottom

**Position:** Letzter Hook in der Main-Spalte  
**HTML-Kontext:** Innerhalb `.dashboard-main`, ganz unten  
**Anwendung:** Newsletter-Signup, Abschließende CTAs

```php
\CMS\Hooks::addAction('home_main_bottom', function () {
    echo '<div class="dashboard-section">
        <div class="newsletter-signup">
            <h3>📬 Newsletter abonnieren</h3>
            <form method="POST"><input type="email" placeholder="E-Mail..."><button>Abonnieren</button></form>
        </div>
    </div>';
}, 10);
```

---

### 11. home_sidebar_top

**Position:** Erster Hook in der Sidebar  
**HTML-Kontext:** Innerhalb `.dashboard-sidebar`  
**Anwendung:** Profilkarte, Benutzer-Schnellzugriff

```php
\CMS\Hooks::addAction('home_sidebar_top', function () {
    if (\CMS\Auth::instance()->isLoggedIn()) {
        echo '<div class="sidebar-panel">
            <h3>👤 Mein Profil</h3>
            <p>Schnellzugriff auf dein Dashboard</p>
        </div>';
    }
}, 10);
```

---

### 12. home_sidebar_widget

**⭐ HAUPT-INJEKTIONSPUNKT für Plugin-Widgets**

**Position:** Mittlerer Bereich der Sidebar (zwischen `home_sidebar_top` und `home_sidebar_bottom`)  
**HTML-Kontext:** Innerhalb `.dashboard-sidebar`  
**Anwendung:** Feed-Aggregator, Job-Anzeigen, Buchungsportal, beliebige Sidebar-Widgets

Das Theme registriert 3 Default-Widgets mit folgenden Prioritäten:

| Priorität | Widget | Klasse-Methode |
|---|---|---|
| 10 | 📅 Buchungsportal (Placeholder) | `renderSidebarBookingWidget()` |
| 20 | 📰 Feed-Aggregator (Placeholder) | `renderSidebarFeedWidget()` |
| 30 | 💼 Job-Anzeigen (Placeholder) | `renderSidebarJobWidget()` |

**Plugin ersetzt ein Default-Widget:**

```php
// 1. Theme-Default entfernen
\CMS\Hooks::removeAction(
    'home_sidebar_widget',
    [IT_Expert_Network_Theme::instance(), 'renderSidebarFeedWidget'],
    20
);

// 2. Eigenes Widget registrieren (gleiche oder andere Priorität)
\CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderHomeSidebarWidget'], 20);
```

**Plugin fügt zusätzliches Widget hinzu (ohne Default zu ersetzen):**

```php
// Neues Widget mit Priorität 25 (zwischen Feed=20 und Jobs=30)
\CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderMyWidget'], 25);
```

**HTML-Struktur eines Sidebar-Widgets:**

```html
<div class="sidebar-panel" data-widget="mein-plugin">
    <div class="sidebar-panel-header">
        <h3>📊 Mein Widget</h3>
        <a href="/mein-plugin" class="sidebar-panel-link">Alle →</a>
    </div>
    <div class="sidebar-panel-body">
        <!-- Widget-Inhalt -->
    </div>
</div>
```

**CSS-Klassen für Sidebar-Widgets:**

| Klasse | Zweck |
|---|---|
| `.sidebar-panel` | Standard-Container (dunkel, mit Padding & Border-Radius) |
| `.sidebar-panel-header` | Kopfzeile mit Titel + Link |
| `.sidebar-panel-body` | Inhaltsbereich |
| `.widget-zone` | Placeholder-Widget (vor Plugin-Aktivierung) |
| `.widget-zone__placeholder` | Gestrichelter Platzhalter-Bereich |

---

### 13. home_sidebar_bottom

**Position:** Letzter Hook in der Sidebar  
**HTML-Kontext:** Innerhalb `.dashboard-sidebar`  
**Anwendung:** Werbebanner, Support-Links, Social-Media-Widgets

```php
\CMS\Hooks::addAction('home_sidebar_bottom', function () {
    echo '<div class="sidebar-panel">
        <h3>🔗 Social Media</h3>
        <div class="social-links">
            <a href="https://twitter.com/...">Twitter</a>
            <a href="https://linkedin.com/...">LinkedIn</a>
        </div>
    </div>';
}, 10);
```

---

### 14. home_after_content

**Position:** Nach dem kompletten Dashboard-Grid  
**HTML-Kontext:** Volle Seitenbreite  
**Anwendung:** Vollbreiter Content-Bereich, Testimonials, Partner-Logos

```php
\CMS\Hooks::addAction('home_after_content', function () {
    echo '<section class="testimonials-section" style="padding:2rem var(--container-padding);max-width:var(--container-max-width);margin:0 auto;">
        <h2>⭐ Was unsere User sagen</h2>
        <!-- Testimonial-Cards -->
    </section>';
}, 10);
```

---

### 15. home_before_strip

**Position:** Vor der Events-Fussleiste  
**HTML-Kontext:** Volle Seitenbreite  
**Anwendung:** Trennlinie, Partner-Logos

```php
\CMS\Hooks::addAction('home_before_strip', function () {
    echo '<hr style="max-width:var(--container-max-width);margin:2rem auto;border-color:#e2e8f0;">';
}, 10);
```

---

### 16. home_after_strip

**Position:** Nach der Events-Fussleiste, ganz unten  
**HTML-Kontext:** Volle Seitenbreite, vor dem Footer  
**Anwendung:** Letzte CTAs, Back-to-top-Trigger

```php
\CMS\Hooks::addAction('home_after_strip', function () {
    echo '<div style="text-align:center;padding:2rem;">
        <a href="#top" class="btn btn-secondary">⬆️ Nach oben</a>
    </div>';
}, 10);
```

---

## Filter Hooks

### 1. home_hero_title

**Zweck:** Überschrift der Hero-Sektion ändern  
**Standard:** Customizer-Wert `homepage.hero_title` (oder LandingPage-Override)  
**Rückgabetyp:** `string`

```php
\CMS\Hooks::addFilter('home_hero_title', function (string $title): string {
    // Saisonale Begrüßung
    $month = (int) date('m');
    if ($month === 12) {
        return '🎄 ' . $title . ' – Frohe Feiertage!';
    }
    return $title;
}, 10);
```

---

### 2. home_hero_subtitle

**Zweck:** Untertitel der Hero-Sektion ändern oder dynamisch setzen  
**Standard:** Customizer-Wert `homepage.hero_subtitle` (leer = ausgeblendet)  
**Rückgabetyp:** `string`

```php
\CMS\Hooks::addFilter('home_hero_subtitle', function (string $subtitle): string {
    $db = \CMS\Database::instance();
    $count = $db->execute("SELECT COUNT(*) as cnt FROM {$db->getPrefix()}experts WHERE status='active'")->fetch();
    return 'Aktuell ' . number_format((int)($count->cnt ?? 0), 0, ',', '.') . ' aktive Experten im Netzwerk';
}, 10);
```

---

### 3. home_stats

**⭐ HAUPT-FILTER für die Statistik-Leiste**

**Zweck:** Array der Statistik-Kacheln modifizieren (bestehende ändern oder neue hinzufügen)  
**Standard:** Internes Array mit Experten/Firmen/Events/Speaker-Zählern  
**Rückgabetyp:** `array` aus assoziativen Arrays

**Struktur eines Stat-Elements:**

```php
[
    'label'   => 'Experten',        // Anzeigename
    'value'   => 142,               // Zahlenwert
    'icon'    => '👤',              // Emoji-Icon
    'visible' => true,              // true=anzeigen, false=ausblenden
]
```

**Beispiel: Eigene Statistik hinzufügen**

```php
\CMS\Hooks::addFilter('home_stats', function (array $stats): array {
    $db = \CMS\Database::instance();
    $prefix = $db->getPrefix();
    
    // Eigene Statistik
    $result = $db->execute("SELECT COUNT(*) as cnt FROM {$prefix}my_plugin_items WHERE status='active'")->fetch();
    
    $stats[] = [
        'label'   => 'Projekte',
        'value'   => (int)($result->cnt ?? 0),
        'icon'    => '🚀',
        'visible' => true,
    ];
    
    return $stats;
}, 10);
```

**Beispiel: Bestehende Statistik ausblenden**

```php
\CMS\Hooks::addFilter('home_stats', function (array $stats): array {
    foreach ($stats as &$stat) {
        if ($stat['label'] === 'Speaker') {
            $stat['visible'] = false;
        }
    }
    return $stats;
}, 20); // Priorität 20 = nach dem Default (10)
```

**Beispiel: Bestehenden Wert überschreiben**

```php
\CMS\Hooks::addFilter('home_stats', function (array $stats): array {
    foreach ($stats as &$stat) {
        if ($stat['label'] === 'Experten') {
            $stat['value'] = $stat['value'] + 50; // Bonus-Wert
            $stat['label'] = 'Registrierte Experten';
        }
    }
    return $stats;
}, 15);
```

---

## Ausführungsreihenfolge

Vollständige Reihenfolge aller Hooks beim Laden der Homepage:

```
 1. home_before_hero          ← Volle Breite
 2. [Hero-Sektion rendern]
    ├── Filter: home_hero_title
    └── Filter: home_hero_subtitle
 3. home_after_hero            ← Volle Breite
 4. [Stats-Leiste rendern]
    └── Filter: home_stats
 5. home_after_stats           ← Volle Breite
 6. home_before_content        ← Volle Breite
 7. ┌── MAIN-SPALTE ──────────────────────
 8. │ home_main_top
 9. │ [Experten-Sektion]
10. │ home_after_experts
11. │ [Events-Sektion]
12. │ home_after_events
13. │ [Firmen-Sektion]
14. │ home_after_companies
15. │ home_content  (Legacy)
16. │ home_main_bottom
17. │
18. ├── SIDEBAR ──────────────────────────
19. │ home_sidebar_top
20. │ home_sidebar_widget (×N Callbacks)
21. │ home_sidebar_bottom
22. └──────────────────────────────────────
23. home_after_content         ← Volle Breite
24. home_before_strip          ← Volle Breite
25. [Events-Strip rendern]
26. home_after_strip           ← Volle Breite
```

---

## CSS-Kontext für Hooks

Beim Implementieren von Hook-Callbacks ist es wichtig zu wissen, in welchem CSS-Container der Output landet:

| Hook-Bereich | CSS-Container | Max-Breite |
|---|---|---|
| `home_before_hero`, `home_after_hero` | Keiner (volle Breite) | 100% |
| `home_after_stats`, `home_before_content` | Keiner | 100% |
| `home_main_*` | `.dashboard-main` | Grid-Auto (≈70%) |
| `home_sidebar_*` | `.dashboard-sidebar` | 320px / 280px |
| `home_after_content` | Keiner | 100% |
| `home_before_strip`, `home_after_strip` | Keiner | 100% |

**Tipp für volle Breite:** Bei Hooks außerhalb des Grids immer eigenen Container mit `max-width: var(--container-max-width)` und `margin: 0 auto` verwenden:

```php
echo '<div style="max-width:var(--container-max-width);margin:0 auto;padding:0 var(--container-padding);">';
// Content...
echo '</div>';
```
