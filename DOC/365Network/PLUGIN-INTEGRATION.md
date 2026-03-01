# 365Network Theme – Plugin-Integration

> Anleitung für Plugin-Entwickler, die Inhalte in die Homepage des 365Network-Themes injizieren möchten.
> Enthält vollständige Code-Beispiele für die häufigsten Integrationsszenarien.

---

## Inhaltsverzeichnis

- [Grundprinzip](#grundprinzip)
- [1. Sidebar-Widget registrieren](#1-sidebar-widget-registrieren)
- [2. Default-Widget ersetzen](#2-default-widget-ersetzen)
- [3. Statistik-Kachel hinzufügen](#3-statistik-kachel-hinzufügen)
- [4. Content-Sektion hinzufügen](#4-content-sektion-hinzufügen)
- [5. Hero-Inhalte modifizieren](#5-hero-inhalte-modifizieren)
- [Praxis-Beispiel: Feed-Aggregator](#praxis-beispiel-feed-aggregator)
- [Praxis-Beispiel: Job-Anzeigen Ersteller](#praxis-beispiel-job-anzeigen-ersteller)
- [Praxis-Beispiel: Buchungsportal](#praxis-beispiel-buchungsportal)
- [Prioritäten-Guide](#prioritäten-guide)
- [Theme-Erkennung & Absicherung](#theme-erkennung--absicherung)
- [CSS-Klassen-Referenz](#css-klassen-referenz)

---

## Grundprinzip

Das 365Network-Theme stellt Homepage-Hooks über das `\CMS\Hooks`-System bereit. Plugins registrieren ihre Callbacks in der `__construct()`-Methode oder einem Init-Hook:

```php
class CMS_MyPlugin
{
    private function __construct()
    {
        // ... andere Plugin-Initialisierung ...
        
        // Homepage-Integration
        $this->register_homepage_hooks();
    }
    
    private function register_homepage_hooks(): void
    {
        // Sidebar-Widget
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'render_homepage_widget'], 25);
        
        // Statistik
        \CMS\Hooks::addFilter('home_stats', [$this, 'add_homepage_stats'], 10);
        
        // Content-Sektion
        \CMS\Hooks::addAction('home_after_companies', [$this, 'render_homepage_section'], 10);
    }
}
```

**Wichtig:** Hooks sind theme-agnostisch. Wenn ein anderes Theme aktiv ist, das diese Hooks nicht auslöst (`doAction`/`applyFilters`), passiert einfach nichts – der Code bleibt inaktiv.

---

## 1. Sidebar-Widget registrieren

### Minimales Widget

```php
public function render_homepage_widget(): void
{
    ?>
    <div class="sidebar-panel" data-widget="mein-plugin">
        <h3>🔧 Mein Widget</h3>
        <div class="sidebar-panel-body">
            <p>Plugin-Inhalt hier.</p>
        </div>
    </div>
    <?php
}
```

### Widget mit Header und Link

```php
public function render_homepage_widget(): void
{
    ?>
    <div class="sidebar-panel" data-widget="mein-plugin">
        <div class="sidebar-panel-header">
            <h3>🔧 Mein Widget</h3>
            <a href="/mein-plugin" class="section-link">Alle anzeigen →</a>
        </div>
        <div class="sidebar-panel-body">
            <?php $this->render_widget_items(); ?>
        </div>
    </div>
    <?php
}

private function render_widget_items(): void
{
    $db = \CMS\Database::instance();
    $prefix = $db->getPrefix();
    $items = $db->execute("SELECT title, created_at FROM {$prefix}my_items ORDER BY created_at DESC LIMIT 5")->fetchAll();
    
    if (empty($items)) {
        echo '<p style="color:var(--muted-color);font-size:0.8rem;">Keine Einträge vorhanden.</p>';
        return;
    }
    
    foreach ($items as $item) {
        echo '<div style="padding:0.5rem 0;border-bottom:1px solid rgb(255 255 255 / 0.06);">';
        echo '<strong style="color:#fff;font-size:0.8125rem;">' . htmlspecialchars($item->title) . '</strong>';
        echo '<span style="display:block;color:var(--muted-color);font-size:0.75rem;">' . date('d.m.Y', strtotime($item->created_at)) . '</span>';
        echo '</div>';
    }
}
```

### Widget mit Tabs

```php
public function render_homepage_widget(): void
{
    ?>
    <div class="sidebar-panel" data-widget="mein-plugin">
        <div class="sidebar-tabs" style="display:flex;gap:0;margin-bottom:1rem;">
            <button class="sidebar-tab active" onclick="switchSidebarTab(this, 'tab-recent')"
                    style="flex:1;padding:0.5rem;background:rgb(255 255 255/0.08);border:none;color:#fff;font-size:0.75rem;cursor:pointer;">
                Neueste
            </button>
            <button class="sidebar-tab" onclick="switchSidebarTab(this, 'tab-popular')"
                    style="flex:1;padding:0.5rem;background:transparent;border:none;color:var(--muted-color);font-size:0.75rem;cursor:pointer;">
                Beliebt
            </button>
        </div>
        <div id="tab-recent" class="sidebar-tab-content active">
            <!-- Neueste Items -->
        </div>
        <div id="tab-popular" class="sidebar-tab-content" style="display:none;">
            <!-- Beliebte Items -->
        </div>
    </div>
    <script>
    function switchSidebarTab(btn, tabId) {
        const panel = btn.closest('.sidebar-panel');
        panel.querySelectorAll('.sidebar-tab').forEach(t => {
            t.style.background = 'transparent';
            t.style.color = 'var(--muted-color)';
            t.classList.remove('active');
        });
        panel.querySelectorAll('.sidebar-tab-content').forEach(c => c.style.display = 'none');
        btn.style.background = 'rgb(255 255 255/0.08)';
        btn.style.color = '#fff';
        btn.classList.add('active');
        document.getElementById(tabId).style.display = 'block';
    }
    </script>
    <?php
}
```

---

## 2. Default-Widget ersetzen

Das Theme registriert 3 Placeholder-Widgets:

| Priorität | Default-Widget | Theme-Methode |
|---|---|---|
| 10 | 📅 Buchungsportal | `renderSidebarBookingWidget()` |
| 20 | 📰 Feed-Aggregator | `renderSidebarFeedWidget()` |
| 30 | 💼 Job-Anzeigen | `renderSidebarJobWidget()` |

### Schritt 1: Default entfernen

```php
// In der Plugin-__construct() oder init_hooks():
\CMS\Hooks::removeAction(
    'home_sidebar_widget',
    [IT_Expert_Network_Theme::instance(), 'renderSidebarFeedWidget'],
    20  // MUSS die exakte Priorität des Originals sein!
);
```

### Schritt 2: Eigenes Widget registrieren

```php
\CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderHomeSidebarWidget'], 20);
```

### Sicherheitshinweis

Die Theme-Klasse `IT_Expert_Network_Theme` ist nur verfügbar, wenn das 365Network-Theme aktiv ist. Immer absichern:

```php
if (class_exists('IT_Expert_Network_Theme')) {
    \CMS\Hooks::removeAction(
        'home_sidebar_widget',
        [IT_Expert_Network_Theme::instance(), 'renderSidebarFeedWidget'],
        20
    );
}
\CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderHomeSidebarWidget'], 20);
```

---

## 3. Statistik-Kachel hinzufügen

### Neue Kachel hinzufügen

```php
\CMS\Hooks::addFilter('home_stats', function (array $stats): array {
    $db = \CMS\Database::instance();
    $prefix = $db->getPrefix();
    
    $count = $db->execute(
        "SELECT COUNT(*) as cnt FROM {$prefix}my_items WHERE status = 'active'"
    )->fetch();
    
    $stats[] = [
        'label'   => 'Projekte',
        'value'   => (int)($count->cnt ?? 0),
        'icon'    => '🚀',
        'visible' => true,
    ];
    
    return $stats;
}, 10);
```

### Bestehende Kachel deaktivieren

```php
\CMS\Hooks::addFilter('home_stats', function (array $stats): array {
    foreach ($stats as &$stat) {
        if ($stat['label'] === 'Speaker') {
            $stat['visible'] = false;
        }
    }
    return $stats;
}, 20);
```

### Kachel-Wert modifizieren

```php
\CMS\Hooks::addFilter('home_stats', function (array $stats): array {
    foreach ($stats as &$stat) {
        if ($stat['label'] === 'Experten') {
            // Zeige auch inaktive mit an
            $db = \CMS\Database::instance();
            $all = $db->execute("SELECT COUNT(*) as cnt FROM {$db->getPrefix()}experts")->fetch();
            $stat['value'] = (int)($all->cnt ?? $stat['value']);
            $stat['label'] = 'Gesamte Experten';
        }
    }
    return $stats;
}, 15);
```

---

## 4. Content-Sektion hinzufügen

### Nach der Experten-Sektion

```php
\CMS\Hooks::addAction('home_after_experts', [$this, 'render_courses_section'], 10);

public function render_courses_section(): void
{
    $db = \CMS\Database::instance();
    $courses = $db->execute(
        "SELECT * FROM {$db->getPrefix()}courses WHERE status='active' ORDER BY start_date ASC LIMIT 4"
    )->fetchAll();
    
    if (empty($courses)) {
        return; // Nichts anzeigen, wenn keine Kurse vorhanden
    }
    ?>
    <div class="dashboard-section">
        <div class="section-header">
            <h2>📚 Aktuelle Kurse</h2>
            <a href="/kurse" class="section-link">Alle Kurse →</a>
        </div>
        <div class="experts-grid">
            <?php foreach ($courses as $course): ?>
            <div class="expert-card">
                <h3 style="font-size:0.9375rem;font-weight:700;margin:0 0 0.5rem 0;">
                    <?php echo htmlspecialchars($course->title); ?>
                </h3>
                <p style="font-size:0.8125rem;color:var(--muted-color);margin:0;">
                    Beginn: <?php echo date('d.m.Y', strtotime($course->start_date)); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
```

### Volle Breite (nach dem Grid)

```php
\CMS\Hooks::addAction('home_after_content', function () {
    ?>
    <section style="max-width:var(--container-max-width);margin:2rem auto;padding:0 var(--container-padding);">
        <div style="background:#eff6ff;border-radius:var(--radius-md);padding:2rem;text-align:center;">
            <h2 style="margin:0 0 0.5rem 0;">🎓 Werde zertifizierter Experte</h2>
            <p style="color:var(--muted-color);">Starte jetzt deine Professional-Zertifizierung.</p>
            <a href="/zertifizierung" class="btn btn-primary" style="margin-top:1rem;">Jetzt starten →</a>
        </div>
    </section>
    <?php
}, 10);
```

---

## 5. Hero-Inhalte modifizieren

### Titel dynamisch ändern

```php
\CMS\Hooks::addFilter('home_hero_title', function (string $title): string {
    // A/B-Test
    if (rand(0, 1) === 0) {
        return 'Finde deinen nächsten IT-Experten';
    }
    return $title;
}, 10);
```

### Dynamischen Untertitel setzen

```php
\CMS\Hooks::addFilter('home_hero_subtitle', function (string $subtitle): string {
    $db = \CMS\Database::instance();
    $prefix = $db->getPrefix();
    $count = $db->execute("SELECT COUNT(*) as cnt FROM {$prefix}experts WHERE status='active'")->fetch();
    return number_format((int)($count->cnt ?? 0), 0, ',', '.') . ' aktive Experten in unserem Netzwerk';
}, 10);
```

---

## Praxis-Beispiel: Feed-Aggregator

Vollständige Integration des `cms-feed` Plugins in die Homepage-Sidebar.

```php
<?php
// In: cms-feed/includes/class-home-integration.php

declare(strict_types=1);
if (!defined('ABSPATH')) exit;

class CMS_Feed_Home_Integration
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        // Default-Placeholder entfernen (wenn 365Network-Theme aktiv)
        if (class_exists('IT_Expert_Network_Theme')) {
            \CMS\Hooks::removeAction(
                'home_sidebar_widget',
                [IT_Expert_Network_Theme::instance(), 'renderSidebarFeedWidget'],
                20
            );
        }

        // Eigenes Widget registrieren
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'render_feed_widget'], 20);
        
        // Statistik hinzufügen
        \CMS\Hooks::addFilter('home_stats', [$this, 'add_feed_stats'], 10);
    }

    /**
     * Feed-Widget für die Homepage-Sidebar
     */
    public function render_feed_widget(): void
    {
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();

        // Neueste Feed-Items laden
        $items = $db->execute(
            "SELECT title, source_name, published_at, url 
             FROM {$prefix}feed_items 
             WHERE status = 'active'
             ORDER BY published_at DESC 
             LIMIT 5"
        )->fetchAll();
        ?>
        <div class="sidebar-panel" data-widget="feed-aggregator">
            <div class="sidebar-panel-header">
                <h3>📰 News-Feed</h3>
                <a href="/feed" class="section-link">Alle News →</a>
            </div>
            <div class="sidebar-panel-body">
                <?php if (empty($items)): ?>
                    <p style="color:var(--muted-color);font-size:0.8rem;">
                        Noch keine Feed-Items vorhanden.
                    </p>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                    <div class="feed-item" style="padding:0.625rem 0;border-bottom:1px solid rgb(255 255 255/0.06);">
                        <a href="<?php echo htmlspecialchars($item->url); ?>" 
                           target="_blank" rel="noopener noreferrer"
                           style="color:#ffffff;font-size:0.8125rem;font-weight:600;text-decoration:none;display:block;margin-bottom:0.25rem;">
                            <?php echo htmlspecialchars($item->title); ?>
                        </a>
                        <div style="display:flex;gap:0.5rem;font-size:0.75rem;color:var(--muted-color);">
                            <span><?php echo htmlspecialchars($item->source_name); ?></span>
                            <span>·</span>
                            <span><?php echo date('d.m.', strtotime($item->published_at)); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Feed-Statistik zur Stats-Leiste hinzufügen
     */
    public function add_feed_stats(array $stats): array
    {
        $db = \CMS\Database::instance();
        $count = $db->execute(
            "SELECT COUNT(*) as cnt FROM {$db->getPrefix()}feed_items WHERE status='active'"
        )->fetch();

        $stats[] = [
            'label'   => 'News',
            'value'   => (int)($count->cnt ?? 0),
            'icon'    => '📰',
            'visible' => true,
        ];

        return $stats;
    }
}

// Initialisieren
CMS_Feed_Home_Integration::instance();
```

---

## Praxis-Beispiel: Job-Anzeigen Ersteller

Vollständige Integration des `cms-jobprofile-generator` Plugins.

```php
<?php
// In: cms-jobprofile-generator/includes/class-home-integration.php

declare(strict_types=1);
if (!defined('ABSPATH')) exit;

class CMS_JPG_Home_Integration
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        // Default-Placeholder entfernen
        if (class_exists('IT_Expert_Network_Theme')) {
            \CMS\Hooks::removeAction(
                'home_sidebar_widget',
                [IT_Expert_Network_Theme::instance(), 'renderSidebarJobWidget'],
                30
            );
        }

        // Eigenes Widget registrieren
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'render_jobs_widget'], 30);

        // Content-Sektion nach Events
        \CMS\Hooks::addAction('home_after_events', [$this, 'render_featured_jobs'], 10);

        // Statistik
        \CMS\Hooks::addFilter('home_stats', [$this, 'add_job_stats'], 10);
    }

    /**
     * Job-Widget für die Sidebar
     */
    public function render_jobs_widget(): void
    {
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();

        $jobs = $db->execute(
            "SELECT j.title, j.company_name, j.location, j.created_at
             FROM {$prefix}job_profiles j
             WHERE j.status = 'published'
             ORDER BY j.created_at DESC
             LIMIT 4"
        )->fetchAll();
        ?>
        <div class="sidebar-panel" data-widget="job-profiles">
            <div class="sidebar-panel-header">
                <h3>💼 Aktuelle Jobs</h3>
                <a href="/jobs" class="section-link">Alle Jobs →</a>
            </div>
            <div class="sidebar-panel-body">
                <?php if (empty($jobs)): ?>
                    <p style="color:var(--muted-color);font-size:0.8rem;">
                        Keine offenen Stellen verfügbar.
                    </p>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                    <div style="padding:0.625rem 0;border-bottom:1px solid rgb(255 255 255/0.06);">
                        <strong style="color:#fff;font-size:0.8125rem;display:block;">
                            <?php echo htmlspecialchars($job->title); ?>
                        </strong>
                        <div style="display:flex;gap:0.5rem;font-size:0.75rem;color:var(--muted-color);margin-top:0.25rem;">
                            <span>🏢 <?php echo htmlspecialchars($job->company_name); ?></span>
                            <?php if (!empty($job->location)): ?>
                                <span>📍 <?php echo htmlspecialchars($job->location); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Featured Jobs Sektion im Hauptbereich
     */
    public function render_featured_jobs(): void
    {
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();

        $jobs = $db->execute(
            "SELECT * FROM {$prefix}job_profiles 
             WHERE status = 'published' AND is_featured = 1
             ORDER BY created_at DESC LIMIT 3"
        )->fetchAll();

        if (empty($jobs)) return;
        ?>
        <div class="dashboard-section">
            <div class="section-header">
                <h2>💼 Featured Jobs</h2>
                <a href="/jobs" class="section-link">Alle Jobs →</a>
            </div>
            <div class="experts-grid">
                <?php foreach ($jobs as $job): ?>
                <div class="expert-card">
                    <h4 style="font-size:0.9375rem;font-weight:700;margin:0 0 0.5rem 0;">
                        <?php echo htmlspecialchars($job->title); ?>
                    </h4>
                    <p style="font-size:0.8125rem;color:var(--muted-color);margin:0 0 0.5rem 0;">
                        🏢 <?php echo htmlspecialchars($job->company_name); ?>
                    </p>
                    <a href="/jobs/<?php echo (int)$job->id; ?>" class="btn btn-sm btn-primary">
                        Details →
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Job-Statistik
     */
    public function add_job_stats(array $stats): array
    {
        $db = \CMS\Database::instance();
        $count = $db->execute(
            "SELECT COUNT(*) as cnt FROM {$db->getPrefix()}job_profiles WHERE status='published'"
        )->fetch();

        $stats[] = [
            'label'   => 'Stellenanzeigen',
            'value'   => (int)($count->cnt ?? 0),
            'icon'    => '💼',
            'visible' => true,
        ];

        return $stats;
    }
}

CMS_JPG_Home_Integration::instance();
```

---

## Praxis-Beispiel: Buchungsportal

```php
<?php
// In: cms-booking/includes/class-home-integration.php

declare(strict_types=1);
if (!defined('ABSPATH')) exit;

class CMS_Booking_Home_Integration
{
    private static ?self $instance = null;

    public static function instance(): self
    {
        return self::$instance ??= new self();
    }

    private function __construct()
    {
        // Default-Placeholder entfernen
        if (class_exists('IT_Expert_Network_Theme')) {
            \CMS\Hooks::removeAction(
                'home_sidebar_widget',
                [IT_Expert_Network_Theme::instance(), 'renderSidebarBookingWidget'],
                10
            );
        }

        // Eigenes Buchungs-Widget registrieren
        \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'render_booking_widget'], 10);
    }

    public function render_booking_widget(): void
    {
        $db = \CMS\Database::instance();
        $prefix = $db->getPrefix();

        // Nächste verfügbare Slots laden
        $slots = $db->execute(
            "SELECT slot_date, slot_time, available_spots 
             FROM {$prefix}booking_slots 
             WHERE slot_date >= CURDATE() AND available_spots > 0
             ORDER BY slot_date ASC, slot_time ASC
             LIMIT 8"
        )->fetchAll();
        ?>
        <div class="sidebar-panel" data-widget="booking">
            <div class="sidebar-panel-header">
                <h3>📅 Buchungsportal</h3>
                <a href="/booking" class="section-link">Kalender →</a>
            </div>
            <div class="sidebar-panel-body">
                <?php if (empty($slots)): ?>
                    <p style="color:var(--muted-color);font-size:0.8rem;">
                        Aktuell keine freien Termine.
                    </p>
                <?php else: ?>
                    <div class="calendar-slots">
                        <?php foreach ($slots as $slot): ?>
                        <div class="calendar-slot" 
                             title="<?php echo date('d.m.', strtotime($slot->slot_date)) . ' ' . $slot->slot_time; ?>">
                            <?php echo $slot->slot_time; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}

CMS_Booking_Home_Integration::instance();
```

---

## Prioritäten-Guide

Prioritätswerte bestimmen die Reihenfolge der Ausführung (niedriger = früher):

| Bereich | Empfohlene Prioritäten |
|---|---|
| 1–9 | Systemkritisch (Core, Security) |
| **10** | **Standard (Theme-Defaults, Plugin-Primärinhalt)** |
| 11–19 | Ergänzungen zu Standardinhalten |
| **20** | **Sekundäre Inhalte (Feed, Sidebar)** |
| 21–29 | Ergänzungen zu sekundären Inhalten |
| **30** | **Tertiäre Inhalte (Jobs, Werbung)** |
| 31–49 | Zusätzliche Widgets |
| **50** | **Niedrige Priorität (Analytics, Tracking)** |
| 90–99 | Abschließend (Cleanup, Footer-Code) |

### Sidebar-Widget-Prioritäten

```
 10  📅 Buchungsportal (Theme-Default / cms-booking Plugin)
 20  📰 Feed-Aggregator (Theme-Default / cms-feed Plugin)
 25  ← Hier einfügen: Zwischen Feed und Jobs
 30  💼 Job-Anzeigen (Theme-Default / cms-jobprofile-generator Plugin)
 40  ← Hier einfügen: Nach allen Defaults
 50  🔗 Social Media / Werbung
```

---

## Theme-Erkennung & Absicherung

### Prüfen ob 365Network-Theme aktiv ist

```php
// Methode 1: Theme-Klasse prüfen
if (class_exists('IT_Expert_Network_Theme')) {
    // 365Network-Theme ist aktiv
}

// Methode 2: ThemeManager prüfen
$activeTheme = \CMS\ThemeManager::instance()->getActiveTheme();
if ($activeTheme === '365Network') {
    // 365Network-Theme ist aktiv
}
```

### Defensive Hook-Registrierung

Hooks funktionieren auch ohne das 365Network-Theme – `addAction()` auf einen Hook, der nie ausgelöst wird, ist ein No-Op:

```php
// Sicher – funktioniert mit JEDEM Theme
\CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderWidget'], 20);

// Nur Theme-spezifische Aktionen (wie removeAction) absichern:
if (class_exists('IT_Expert_Network_Theme')) {
    \CMS\Hooks::removeAction(
        'home_sidebar_widget',
        [IT_Expert_Network_Theme::instance(), 'renderSidebarFeedWidget'],
        20
    );
}
```

---

## CSS-Klassen-Referenz

### Sidebar-Widgets

| Klasse | Beschreibung |
|---|---|
| `.sidebar-panel` | Standard dark-Panel-Container |
| `.sidebar-panel-header` | Flex-Header (Titel + Link) |
| `.sidebar-panel-header h3` | Panel-Titel (uppercase, weiß) |
| `.sidebar-panel-body` | Inhaltsbereich |
| `.section-link` | Gold-farbener Link (Akzentfarbe) |
| `.calendar-slots` | CSS-Grid für Zeitslots |
| `.calendar-slot` | Einzelner Zeitslot-Button |

### Content-Sektionen

| Klasse | Beschreibung |
|---|---|
| `.dashboard-section` | Standard-Sektion (margin-bottom: 2rem) |
| `.section-header` | Flex-Header mit Titel + Link |
| `.section-header h2` | Section-Titel (uppercase, 0.875rem) |
| `.experts-grid` | Auto-Fill-Grid für Cards |
| `.expert-card` | Standard-Card mit Hover-Effekt |

### Layout-Variablen

| CSS-Variable | Beschreibung |
|---|---|
| `--container-max-width` | Max. Container-Breite (1400px) |
| `--container-padding` | Seitenrand (2rem) |
| `--radius-md` | Standard Border-Radius (8px) |
| `--muted-color` | Gedämpfte Textfarbe (#64748b) |
| `--accent-color` | Gold-Akzent (#c8952e) |
