<?php
/**
 * Home Template – 365Network Dashboard-Startseite
 *
 * Vollständig hook-basierte Architektur: Jede Sektion bietet Hooks für Plugin-Injektionen.
 * Alle Sektionen sind über den Theme Customizer (Kategorie „homepage") steuerbar.
 *
 * Hook-Architektur (Ausführungsreihenfolge):
 *   home_before_hero          → Vor der Hero-Sektion
 *   home_hero_title (Filter)  → Hero-Überschrift filtern
 *   home_hero_subtitle (Filt) → Hero-Untertitel filtern
 *   home_after_hero           → Nach Hero, vor Stats
 *   home_stats (Filter)       → Stats-Array filtern (Plugins können eigene Stats registrieren)
 *   home_after_stats          → Nach Stats-Bar
 *   home_before_content       → Vor dem Dashboard-Grid
 *   home_main_top             → Anfang der Hauptspalte (z.B. Banner, Ankündigungen)
 *   home_after_experts        → Nach Experten-Sektion
 *   home_after_events         → Nach Events-Sektion
 *   home_after_companies      → Nach Firmen-Sektion
 *   home_content              → Allgemeine Inhalts-Hook (Abwärtskompatibel)
 *   home_main_bottom          → Ende der Hauptspalte
 *   home_sidebar_top          → Anfang der Sidebar
 *   home_sidebar_widget       → Haupt-Injektionspunkt für Sidebar-Widgets
 *   home_sidebar_bottom       → Ende der Sidebar
 *   home_after_content        → Nach dem Dashboard-Grid
 *   home_before_strip         → Vor der Events-Fußleiste
 *   home_after_strip          → Nach der Events-Fußleiste
 *
 * @package IT_Expert_Network_Theme
 * @version 3.1.0
 * @see     DOC/365Network/HOMEPAGE-HOOKS.md
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════════
// 1. GRUNDLEGENDE VARIABLEN
// ═══════════════════════════════════════════════════════════════════════════════

$siteUrl    = SITE_URL;
$isLoggedIn = theme_is_logged_in();
$db         = \CMS\Database::instance();
$prefix     = $db->prefix();
$pluginMgr  = \CMS\PluginManager::instance();

// ═══════════════════════════════════════════════════════════════════════════════
// 2. CUSTOMIZER-EINSTELLUNGEN LADEN (Kategorie: homepage)
// ═══════════════════════════════════════════════════════════════════════════════

$_hp = []; // Homepage-Customizer-Cache
try {
    $customizer = \CMS\Services\ThemeCustomizer::instance();
    $_hp = $customizer->getCategory('homepage');
} catch (\Throwable $e) {
    $_hp = [];
}

// Hilfsfunktion: Setting mit Fallback
$hpSetting = function (string $key, $default = '') use ($_hp) {
    return $_hp[$key] ?? $default;
};

// Hilfsfunktion: Sicher Boolean auslesen (robust gegen DB-Strings '0','1','true','false')
$hpBool = function (string $key, bool $default = true) use ($hpSetting): bool {
    $val = $hpSetting($key, $default);
    return filter_var($val, FILTER_VALIDATE_BOOLEAN);
};

// ── Sektions-Sichtbarkeit ──
$showHero       = $hpBool('show_hero', true);
$showHeroSearch = $hpBool('show_hero_search', true);
$showStatsBar   = $hpBool('show_stats_bar', true);
$showExperts    = $hpBool('show_experts_section', true);
$showEvents     = $hpBool('show_events_section', true);
$showCompanies  = $hpBool('show_companies_section', true);
$showSpeakers   = $hpBool('show_speakers_section', true);
$showJobs       = $hpBool('show_jobs_section', false);
$showSidebar    = $hpBool('show_sidebar', true);
$showStrip      = $hpBool('show_events_strip', true);

// ── Sektions-Texte ──
$heroTitle      = (string)$hpSetting('hero_title', 'Führendes Verzeichnis für IT-Experten & Unternehmen');
$heroSubtitle   = (string)$hpSetting('hero_subtitle', '');
$expertsTitle   = (string)$hpSetting('experts_section_title', 'Aktuelle Experten');
$eventsTitle    = (string)$hpSetting('events_section_title', 'Kommende Events & Konferenzen');
$companiesTitle = (string)$hpSetting('companies_section_title', 'Top Firmen im Fokus');
$speakersTitle  = (string)$hpSetting('speakers_section_title', 'Featured Speaker');
$jobsTitle      = (string)$hpSetting('jobs_section_title', 'Aktuelle Stellen');

// ── Limits ──
$expertsLimit   = max(1, min(12, (int)$hpSetting('experts_limit', 3)));
$eventsLimit    = max(1, min(12, (int)$hpSetting('events_limit', 4)));
$companiesLimit = max(1, min(12, (int)$hpSetting('companies_limit', 4)));
$speakersLimit  = max(1, min(12, (int)$hpSetting('speakers_limit', 4)));
$jobsLimit      = max(1, min(15, (int)$hpSetting('jobs_limit', 5)));

// ── Layout ──
$homepageLayout = (string)$hpSetting('homepage_layout', 'sidebar-right');

// ═══════════════════════════════════════════════════════════════════════════════
// 3. PLUGIN-VERFÜGBARKEIT
// ═══════════════════════════════════════════════════════════════════════════════

$hasExperts   = $pluginMgr->isPluginActive('cms-experts');
$hasCompanies = $pluginMgr->isPluginActive('cms-companies');
$hasEvents    = $pluginMgr->isPluginActive('cms-events');
$hasSpeakers  = $pluginMgr->isPluginActive('cms-speakers');
$hasFeed      = $pluginMgr->isPluginActive('cms-feed');
$hasJobs      = $pluginMgr->isPluginActive('cms-jobprofile-generator');
$hasBooking   = $pluginMgr->isPluginActive('cms-booking');

// ═══════════════════════════════════════════════════════════════════════════════
// 4. DATEN LADEN (nur wenn Sektionen aktiv)
// ═══════════════════════════════════════════════════════════════════════════════

// ── Statistiken ──
$stats = [
    'experts'   => ['icon' => '👤', 'label' => 'Experten',  'value' => 0, 'visible' => $hasExperts],
    'companies' => ['icon' => '🏢', 'label' => 'Firmen',    'value' => 0, 'visible' => $hasCompanies],
    'events'    => ['icon' => '📅', 'label' => 'Events',    'value' => 0, 'visible' => $hasEvents],
    'speakers'  => ['icon' => '🎤', 'label' => 'Speaker',   'value' => 0, 'visible' => $hasSpeakers],
];

if ($showStatsBar || $showHero) {
    $statQueries = [
        'experts'   => [$hasExperts,   "SELECT COUNT(*) as cnt FROM {$prefix}experts WHERE status = 'active'"],
        'companies' => [$hasCompanies, "SELECT COUNT(*) as cnt FROM {$prefix}companies WHERE status = 'active'"],
        'events'    => [$hasEvents,    "SELECT COUNT(*) as cnt FROM {$prefix}events WHERE status = 'active'"],
        'speakers'  => [$hasSpeakers,  "SELECT COUNT(*) as cnt FROM {$prefix}speakers WHERE status = 'active'"],
        'jobs'      => [$hasJobs,      "SELECT COUNT(*) as cnt FROM {$prefix}jpg_profiles WHERE status = 'published'"],
    ];
    if (isset($stats['jobs'])) {
        // Schon im Array vorhanden? Nein – hinzufügen
    }
    $stats['jobs'] = ['icon' => '💼', 'label' => 'Stellen', 'value' => 0, 'visible' => $hasJobs];
    foreach ($statQueries as $key => [$active, $sql]) {
        if ($active) {
            try {
                $r = $db->execute($sql)->fetch();
                $stats[$key]['value'] = $r ? (int)($r->cnt ?? $r['cnt'] ?? 0) : 0;
            } catch (\Throwable $e) { /* Tabelle existiert ggf. noch nicht */ }
        }
    }
}

// ── Filter: Plugins können eigene Stats hinzufügen / bestehende ändern ──
$stats = \CMS\Hooks::applyFilters('home_stats', $stats);

// ── Experten ──
$experts = [];
if ($showExperts && $hasExperts) {
    try {
        $stmt = $db->execute(
            "SELECT e.*,
                    CONCAT(e.first_name, ' ', e.last_name) AS display_name,
                    (SELECT GROUP_CONCAT(skill_name ORDER BY skill_name SEPARATOR ', ')
                     FROM {$prefix}expert_skills WHERE expert_id = e.id) AS skills
             FROM {$prefix}experts e
             WHERE e.status = 'active'
             ORDER BY e.created_at DESC
             LIMIT " . $expertsLimit
        );
        $experts = $stmt->fetchAll() ?: [];
    } catch (\Throwable $e) { /* */ }
}

// ── Firmen ──
$companies = [];
if ($showCompanies && $hasCompanies) {
    try {
        $stmt = $db->execute(
            "SELECT * FROM {$prefix}companies WHERE status = 'active' ORDER BY created_at DESC LIMIT " . $companiesLimit
        );
        $companies = $stmt->fetchAll() ?: [];
    } catch (\Throwable $e) { /* */ }
}

// ── Events ──
$events = [];
if (($showEvents || $showStrip) && $hasEvents) {
    try {
        $stmt = $db->execute(
            "SELECT * FROM {$prefix}events WHERE status = 'published' AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT " . $eventsLimit
        );
        $events = $stmt->fetchAll() ?: [];
    } catch (\Throwable $e) { /* */ }
}

// ── Speaker ──
$speakers = [];
if ($showSpeakers && $hasSpeakers) {
    try {
        $stmt = $db->execute(
            "SELECT s.*,
                    CONCAT(s.first_name, ' ', s.last_name) AS name,
                    s.photo_url AS photo,
                    (SELECT COUNT(*) FROM {$prefix}speaker_events se WHERE se.speaker_id = s.id) AS total_events,
                    (SELECT GROUP_CONCAT(topic_name ORDER BY sort_order SEPARATOR ', ')
                     FROM {$prefix}speaker_topics WHERE speaker_id = s.id LIMIT 3) AS topics
             FROM {$prefix}speakers s
             WHERE s.status = 'active'
             ORDER BY s.is_featured DESC, s.created_at DESC
             LIMIT " . $speakersLimit
        );
        $speakers = $stmt->fetchAll() ?: [];
    } catch (\Throwable $e) { /* */ }
}

// ── Jobs ──
$jobs = [];
if ($showJobs && $hasJobs) {
    try {
        $stmt = $db->execute(
            "SELECT p.id, p.title, p.slug, p.location, p.employment_type AS job_type,
                    p.salary_min, p.salary_max, p.salary_currency, p.published_at
             FROM {$prefix}jpg_profiles p
             WHERE p.status = 'published'
             ORDER BY p.published_at DESC, p.created_at DESC
             LIMIT " . $jobsLimit
        );
        $jobs = $stmt->fetchAll() ?: [];
    } catch (\Throwable $e) { /* */ }
}

// ── Blog/Feed-Fallback: Letzte Beiträge laden wenn Hauptsektionen deaktiviert ──
$fallbackPosts = [];
$showFallbackContent = (!$showExperts && !$showEvents && !$showCompanies && !$showSpeakers && !$showJobs);
if ($showFallbackContent) {
    // Priorität 1: Posts aus der CMS Blog-Tabelle
    try {
        $stmt = $db->execute(
            "SELECT id, title, slug, excerpt, featured_image, author_id, published_at
             FROM {$prefix}posts
             WHERE status = 'published'
             ORDER BY published_at DESC
             LIMIT 6"
        );
        $fallbackPosts = $stmt->fetchAll() ?: [];
    } catch (\Throwable $e) { /* posts-Tabelle ggf. nicht vorhanden */ }

    // Priorität 2: Feed-Items aus cms-feed Plugin (wenn Blog leer)
    if (empty($fallbackPosts) && $hasFeed) {
        try {
            $stmt = $db->execute(
                "SELECT fi.id, fi.title, fi.link, fi.description, fi.image_url, fi.author, fi.pub_date
                 FROM {$prefix}feed_items fi
                 WHERE fi.is_hidden = 0
                 ORDER BY fi.pub_date DESC
                 LIMIT 6"
            );
            $fallbackPosts = $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) { /* feed_items-Tabelle ggf. nicht vorhanden */ }
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// 5. LANDING-PAGE OVERRIDE (nur wenn Startseiten-Modus = 'landing_page')
// ═══════════════════════════════════════════════════════════════════════════════

$homepageMode = (string)$hpSetting('homepage_mode', 'theme');

if ($homepageMode === 'landing_page') {
    try {
        $landingService = \CMS\Services\LandingPageService::getInstance();
        $landingHeader  = $landingService->getHeader();
        if (!empty($landingHeader['title'])) {
            $heroTitle = $landingHeader['title'];
        }
        if (!empty($landingHeader['subtitle'])) {
            $heroSubtitle = $landingHeader['subtitle'];
        }
    } catch (\Throwable $e) { /* LandingPageService nicht verfügbar */ }
}

// ── Filter: Hero-Texte für Plugins filterbar ──
$heroTitle    = \CMS\Hooks::applyFilters('home_hero_title', $heroTitle);
$heroSubtitle = \CMS\Hooks::applyFilters('home_hero_subtitle', $heroSubtitle);

// ═══════════════════════════════════════════════════════════════════════════════
// 6. HELPER-FUNKTIONEN
// ═══════════════════════════════════════════════════════════════════════════════

/** Objektfeld sicher lesen (Array oder Object) */
if (!function_exists('_field')) {
    function _field($row, string $key, string $default = ''): string
    {
        if (is_array($row)) {
            return (string)($row[$key] ?? $default);
        }
        if (is_object($row)) {
            return (string)($row->{$key} ?? $default);
        }
        return $default;
    }
}

/** Deutsche Monatskürzel */
$monthsDE = ['', 'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];

// ═══════════════════════════════════════════════════════════════════════════════
// 7. TEMPLATE-AUSGABE
// ═══════════════════════════════════════════════════════════════════════════════

// Layout-Klassen berechnen
$layoutClass = match ($homepageLayout) {
    'sidebar-left'  => 'dashboard-layout dashboard-layout--sidebar-left',
    'full-width'    => 'dashboard-layout dashboard-layout--full-width',
    default         => 'dashboard-layout',
};
?>

<main id="main" class="site-main" role="main">

    <?php
    // ═════════════════════════════════════════════════════════════════════════
    // HOOK: home_before_hero
    // Plugins können hier Elemente VOR der Hero-Sektion einfügen,
    // z.B. Alert-Banner, Ankündigungen, Wartungshinweise.
    // ═════════════════════════════════════════════════════════════════════════
    \CMS\Hooks::doAction('home_before_hero');
    ?>

    <?php if ($showHero) : ?>
    <!-- ═══════════════════════════════════════════════════════════════════════
         HERO SECTION – Suchleiste & Statistiken
         Customizer: homepage.show_hero, homepage.hero_title, homepage.hero_subtitle
         Hooks: home_hero_title (Filter), home_hero_subtitle (Filter)
         ═══════════════════════════════════════════════════════════════════════ -->
    <section class="hero-section" data-section="hero">
        <div class="hero-inner">

            <h2 class="hero-title">
                <?php echo htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8'); ?>
            </h2>

            <?php if (!empty($heroSubtitle)) : ?>
                <p class="hero-subtitle">
                    <?php echo htmlspecialchars($heroSubtitle, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php endif; ?>

            <?php if ($showHeroSearch) : ?>
            <!-- Übergreifende Suche: Experten, Firmen, Speaker, Events (Customizer: homepage.show_hero_search) -->
            <form class="hero-search" action="<?php echo htmlspecialchars(theme_safe_url($siteUrl . '/search', $siteUrl . '/search'), ENT_QUOTES, 'UTF-8'); ?>" method="GET">
                <div class="hero-search-row">
                    <div class="hero-search-field hero-search-field--wide">
                        <label for="hero-q">Suchbegriff</label>
                        <input type="search" id="hero-q" name="q" placeholder="Experten, Firmen, Speaker, Events …" autocomplete="off">
                    </div>
                    <div class="hero-search-field">
                        <label for="hero-location">Standort</label>
                        <input type="text" id="hero-location" name="location" placeholder="Stadt oder Remote">
                    </div>
                    <div class="hero-search-field">
                        <label for="hero-type">Bereich</label>
                        <select id="hero-type" name="type">
                            <option value="">Alle Bereiche</option>
                            <?php if ($hasExperts) : ?><option value="experts">Experten</option><?php endif; ?>
                            <?php if ($hasCompanies) : ?><option value="companies">Firmen</option><?php endif; ?>
                            <?php if ($hasSpeakers) : ?><option value="speakers">Speaker</option><?php endif; ?>
                            <?php if ($hasEvents) : ?><option value="events">Events</option><?php endif; ?>
                        </select>
                    </div>
                    <div class="hero-search-field">
                        <label for="hero-filter">Filter</label>
                        <input type="text" id="hero-filter" name="filter" placeholder="Skills, Branche, Tags …">
                    </div>
                    <button type="submit" class="hero-search-btn" aria-label="Suchen">🔍</button>
                </div>
            </form>
            <?php endif; ?>

            <?php
            // ═════════════════════════════════════════════════════════════════
            // HOOK: home_after_hero
            // Plugins können hier Inhalte nach der Suche, aber vor den
            // Stats einfügen, z.B. Trending Tags, Quick-Filter-Chips.
            // ═════════════════════════════════════════════════════════════════
            \CMS\Hooks::doAction('home_after_hero');
            ?>

            <?php if ($showStatsBar) : ?>
            <!-- Statistik-Kacheln (Filter: home_stats) -->
            <div class="stats-bar">
                <?php foreach ($stats as $statKey => $stat) :
                    if (empty($stat['visible'])) { continue; }
                ?>
                    <div class="stat-tile" data-stat="<?php echo htmlspecialchars($statKey, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="stat-tile-icon"><?php echo $stat['icon']; ?></span>
                        <div class="stat-tile-info">
                            <span class="stat-tile-label"><?php echo htmlspecialchars($stat['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="stat-tile-value"><?php echo number_format((int)$stat['value']); ?>+</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

        </div>
    </section>
    <?php endif; ?>

    <?php
    // ═════════════════════════════════════════════════════════════════════════
    // HOOK: home_after_stats
    // Platz für Elemente zwischen Hero/Stats und dem Dashboard-Grid,
    // z.B. ein Featured-Banner, Promotion-Slider oder CTA-Leiste.
    // ═════════════════════════════════════════════════════════════════════════
    \CMS\Hooks::doAction('home_after_stats');
    ?>

    <?php
    // ═════════════════════════════════════════════════════════════════════════
    // HOOK: home_before_content
    // Letzte Hook-Möglichkeit vor dem Haupt-Dashboard-Grid.
    // ═════════════════════════════════════════════════════════════════════════
    \CMS\Hooks::doAction('home_before_content');
    ?>

    <!-- ═══════════════════════════════════════════════════════════════════════
         DASHBOARD GRID – Hauptinhalt + Sidebar
         Layout steuerbar per Customizer: sidebar-right | sidebar-left | full-width
         ═══════════════════════════════════════════════════════════════════════ -->
    <div class="<?php echo htmlspecialchars($layoutClass, ENT_QUOTES, 'UTF-8'); ?>">

        <!-- ═══════════════════════════════════════════════════════════════════
             HAUPTSPALTE
             ═══════════════════════════════════════════════════════════════════ -->
        <div class="dashboard-main">

            <?php
            // ═════════════════════════════════════════════════════════════════
            // HOOK: home_main_top
            // Plugins können hier Inhalte am Anfang der Hauptspalte einfügen,
            // z.B. einen News-Ticker, Willkommensbanner oder Feed-Highlights.
            //
            // Beispiel (in einem Plugin):
            //   \CMS\Hooks::addAction('home_main_top', function() {
            //       echo '<div class="dashboard-section">...Feed Widget...</div>';
            //   }, 10);
            // ═════════════════════════════════════════════════════════════════
            \CMS\Hooks::doAction('home_main_top');
            ?>

            <?php if ($showExperts) : ?>
            <!-- ─── Experten-Sektion ──────────────────────────────────────── -->
            <section class="dashboard-section" data-section="experts" id="home-experts">
                <div class="section-header">
                    <h2><?php echo htmlspecialchars($expertsTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <?php if ($hasExperts) : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/experts" class="section-link">Alle anzeigen →</a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($experts)) : ?>
                    <div class="experts-grid">
                        <?php foreach ($experts as $expert) :
                            $eName  = htmlspecialchars(trim(_field($expert, 'display_name', trim(_field($expert, 'first_name', '') . ' ' . _field($expert, 'last_name', '')))) ?: 'Unbekannt', ENT_QUOTES, 'UTF-8');
                            $eTitle = htmlspecialchars(_field($expert, 'position', ''), ENT_QUOTES, 'UTF-8');
                            $ePhoto = _field($expert, 'photo_url', '');
                            $eId    = (int)_field($expert, 'id', '0');
                            $eSkills = _field($expert, 'skills', '');
                            $eYears  = _field($expert, 'experience_years', '');
                            $eAvail  = _field($expert, 'availability', _field($expert, 'location_city', ''));
                            $skillTags = $eSkills ? array_slice(array_map('trim', explode(',', $eSkills)), 0, 3) : [];
                            $initials = mb_strtoupper(mb_substr($eName, 0, 2));
                        ?>
                            <article class="expert-card">
                                <div class="expert-card-header">
                                    <?php if ($ePhoto) : ?>
                                        <img class="expert-avatar"
                                             src="<?php echo htmlspecialchars($ePhoto, ENT_QUOTES, 'UTF-8'); ?>"
                                             alt="<?php echo $eName; ?>"
                                             loading="lazy" width="56" height="56">
                                    <?php else : ?>
                                        <div class="expert-avatar-placeholder"><?php echo $initials; ?></div>
                                    <?php endif; ?>
                                    <div class="expert-info">
                                        <h3 class="expert-name"><?php echo $eName; ?></h3>
                                        <?php if ($eTitle) : ?>
                                            <p class="expert-title"><?php echo $eTitle; ?></p>
                                        <?php endif; ?>
                                        <?php if ($eYears) : ?>
                                            <span class="expert-badge"><?php echo htmlspecialchars($eYears, ENT_QUOTES, 'UTF-8'); ?> Jahre</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if (!empty($skillTags)) : ?>
                                    <div class="expert-tags">
                                        <?php foreach ($skillTags as $tag) : ?>
                                            <span class="expert-tag"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="expert-meta">
                                    <?php if ($eAvail) : ?>
                                        <span>📍 <?php echo htmlspecialchars($eAvail, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="expert-card-footer">
                                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/experts/<?php echo $eId; ?>" class="btn btn-sm btn-primary">
                                        Profil ansehen
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">👤</div>
                        <p>Experten werden geladen, sobald das Experten-Modul aktiv ist.</p>
                    </div>
                <?php endif; ?>
            </section>

            <?php
            // HOOK: home_after_experts
            // z.B. „Experte der Woche"-Widget, Skill-Trends-Chart.
            \CMS\Hooks::doAction('home_after_experts');
            ?>
            <?php endif; ?>

            <?php if ($showEvents) : ?>
            <!-- ─── Events-Sektion ────────────────────────────────────────── -->
            <section class="dashboard-section" data-section="events" id="home-events">
                <div class="section-header">
                    <h2><?php echo htmlspecialchars($eventsTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <?php if ($hasEvents) : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/events" class="section-link">Alle anzeigen →</a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($events)) : ?>
                    <div class="events-list">
                        <?php foreach ($events as $event) :
                            $evTitle    = htmlspecialchars(_field($event, 'title', 'Event'), ENT_QUOTES, 'UTF-8');
                            $evDate     = _field($event, 'event_date', '');
                            $evCity     = _field($event, 'city', '');
                            $evLocation = htmlspecialchars(_field($event, 'location', $evCity), ENT_QUOTES, 'UTF-8');
                            $evId       = (int)_field($event, 'id', '0');
                            $evDay   = $evDate ? date('d', strtotime($evDate)) : '--';
                            $evMonth = $evDate ? ($monthsDE[(int)date('n', strtotime($evDate))] ?? '') : '';
                            $evYear  = $evDate ? date('Y', strtotime($evDate)) : '';
                            $evUrl   = htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8') . '/events/' . $evId;
                            $evDateLabel = $evDate ? $evDay . '. ' . $evMonth . ' ' . $evYear : '';
                        ?>
                            <a href="<?php echo $evUrl; ?>" class="event-card">
                                <?php if ($evDateLabel) : ?>
                                    <span class="event-date-pill">📅 <?php echo htmlspecialchars($evDateLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endif; ?>
                                <div class="event-info">
                                    <h3 class="event-title"><?php echo $evTitle; ?></h3>
                                    <?php if ($evLocation || $evYear) : ?>
                                        <div class="event-meta">
                                            <?php if ($evLocation) : ?>
                                                <span>📍 <?php echo $evLocation; ?></span>
                                            <?php endif; ?>
                                            <?php if ($evYear) : ?>
                                                <span>📅 <?php echo $evYear; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <span class="event-more">Mehr erfahren →</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📅</div>
                        <p>Kommende Events werden geladen, sobald das Events-Modul aktiv ist.</p>
                    </div>
                <?php endif; ?>
            </section>

            <?php
            // HOOK: home_after_events
            // z.B. Speaker-Highlights, Event-Kalender-Widget.
            \CMS\Hooks::doAction('home_after_events');
            ?>
            <?php endif; ?>

            <?php if ($showCompanies) : ?>
            <!-- ─── Firmen-Sektion ────────────────────────────────────────── -->
            <section class="dashboard-section" data-section="companies" id="home-companies">
                <div class="section-header">
                    <h2><?php echo htmlspecialchars($companiesTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <?php if ($hasCompanies) : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/companies" class="section-link">Alle anzeigen →</a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($companies)) : ?>
                    <div class="companies-grid">
                        <?php foreach ($companies as $company) :
                            $cName = htmlspecialchars(_field($company, 'name', 'Unbekannt'), ENT_QUOTES, 'UTF-8');
                            $cLogo = _field($company, 'logo_url', '');
                            $cDesc = htmlspecialchars(mb_substr(strip_tags(_field($company, 'description', '')), 0, 150), ENT_QUOTES, 'UTF-8');
                            $cId   = (int)_field($company, 'id', '0');
                        ?>
                            <article class="company-card">
                                <?php if ($cLogo) : ?>
                                    <img class="company-logo"
                                         src="<?php echo htmlspecialchars($cLogo, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo $cName; ?>"
                                         loading="lazy" width="120" height="40">
                                <?php else : ?>
                                    <div class="company-logo-placeholder">🏢</div>
                                <?php endif; ?>
                                <h3 class="company-name"><?php echo $cName; ?></h3>
                                <?php if ($cDesc) : ?>
                                    <p class="company-desc"><?php echo $cDesc; ?></p>
                                <?php endif; ?>
                                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/companies/<?php echo $cId; ?>"
                                   class="btn btn-sm btn-secondary">
                                    Kontakt anfragen
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🏢</div>
                        <p>Firmenprofile werden geladen, sobald das Firmen-Modul aktiv ist.</p>
                    </div>
                <?php endif; ?>
            </section>

            <?php
            // HOOK: home_after_companies
            // z.B. Branchen-Übersicht, Featured-Partner-Banner.
            \CMS\Hooks::doAction('home_after_companies');
            ?>
            <?php endif; ?>

            <?php if ($showSpeakers) : ?>
            <!-- ─── Speaker-Sektion ──────────────────────────────────────── -->
            <section class="dashboard-section" data-section="speakers" id="home-speakers">
                <div class="section-header">
                    <h2>🎤 <?php echo htmlspecialchars($speakersTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/speakers" class="section-link">Alle Speaker →</a>
                </div>

                <?php if ($hasSpeakers && !empty($speakers)) : ?>
                <div class="speakers-hp-grid">
                    <?php foreach ($speakers as $sp) :
                        $spId      = (int)(is_array($sp) ? ($sp['id'] ?? 0) : ($sp->id ?? 0));
                        $spName    = htmlspecialchars(is_array($sp) ? ($sp['name'] ?? '') : ($sp->name ?? ''), ENT_QUOTES, 'UTF-8');
                        $spTitle   = htmlspecialchars(is_array($sp) ? ($sp['title'] ?? '') : ($sp->title ?? ''), ENT_QUOTES, 'UTF-8');
                        $spPhoto   = is_array($sp) ? ($sp['photo'] ?? '') : ($sp->photo ?? '');
                        $spEvents  = (int)(is_array($sp) ? ($sp['total_events'] ?? 0) : ($sp->total_events ?? 0));
                        $spTopics  = is_array($sp) ? ($sp['topics'] ?? '') : ($sp->topics ?? '');
                        $topicArr  = $spTopics ? array_slice(array_map('trim', explode(',', $spTopics)), 0, 2) : [];
                        $initials  = mb_strtoupper(mb_substr($spName, 0, 2));
                    ?>
                    <?php
                        $speakerUrl = theme_safe_url($siteUrl . '/speakers/' . $spId, $siteUrl . '/speakers');
                        $speakerPhoto = theme_safe_url((string) $spPhoto);
                    ?>
                    <a href="<?php echo htmlspecialchars($speakerUrl, ENT_QUOTES, 'UTF-8'); ?>" class="speaker-hp-card">
                        <?php if ($speakerPhoto) : ?>
                            <img class="speaker-hp-avatar" src="<?php echo htmlspecialchars($speakerPhoto, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $spName; ?>" loading="lazy" width="64" height="64">
                        <?php else : ?>
                            <div class="speaker-dir-avatar-placeholder speaker-dir-avatar-placeholder--home"><?php echo $initials; ?></div>
                        <?php endif; ?>
                        <div class="speaker-hp-info">
                            <strong><?php echo $spName; ?></strong>
                            <?php if ($spTitle) : ?><small><?php echo $spTitle; ?></small><?php endif; ?>
                            <?php if (!empty($topicArr)) : ?>
                                <div class="speaker-hp-topics"><?php echo htmlspecialchars(implode(' · ', $topicArr), ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($spEvents > 0) : ?><span class="speaker-events-count">🎤 <?php echo $spEvents; ?></span><?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php elseif ($hasSpeakers) : ?>
                <div class="empty-state empty-state--compact">
                    <div class="empty-state-icon">🎤</div>
                    <p>Noch keine Speaker im Verzeichnis.</p>
                    <a href="<?php echo htmlspecialchars(theme_safe_url($siteUrl . '/speakers', $siteUrl . '/speakers'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary btn-sm">Verzeichnis öffnen</a>
                </div>
                <?php else : ?>
                <p class="plugin-hint">Das <strong>cms-speakers</strong> Plugin aktivieren, um Speaker anzuzeigen. <a href="<?php echo htmlspecialchars(theme_safe_url($siteUrl . '/speakers', $siteUrl . '/speakers'), ENT_QUOTES, 'UTF-8'); ?>" class="plugin-hint__link">Mehr erfahren →</a></p>
                <?php endif; ?>
            </section>

            <?php
            \CMS\Hooks::doAction('home_after_speakers');
            ?>
            <?php endif; ?>

            <?php if ($showJobs) : ?>
            <!-- ─── Stellenmarkt-Sektion ─────────────────────────────────── -->
            <section class="dashboard-section" data-section="jobs" id="home-jobs">
                <div class="section-header">
                    <h2>💼 <?php echo htmlspecialchars($jobsTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/jobs" class="section-link">Alle Stellen →</a>
                </div>

                <?php if ($hasJobs && !empty($jobs)) : ?>
                <div class="jobs-hp-list">
                    <?php foreach ($jobs as $job) :
                        $jId      = (int)(is_array($job) ? ($job['id'] ?? 0) : ($job->id ?? 0));
                        $jTitle   = htmlspecialchars(is_array($job) ? ($job['title'] ?? '') : ($job->title ?? ''), ENT_QUOTES, 'UTF-8');
                        $jCompany = htmlspecialchars(is_array($job) ? ($job['company_name'] ?? '') : ($job->company_name ?? ''), ENT_QUOTES, 'UTF-8');
                        $jType    = is_array($job) ? ($job['job_type'] ?? '') : ($job->job_type ?? '');
                        $jLoc     = htmlspecialchars(is_array($job) ? ($job['location'] ?? '') : ($job->location ?? ''), ENT_QUOTES, 'UTF-8');
                        $jobTypeLabels = ['fulltime' => '👔 Vollzeit', 'parttime' => '⏰ Teilzeit', 'freelance' => '🧑‍💻 Freelance', 'internship' => '🎓 Praktikum', 'remote' => '🏠 Remote'];
                        $jTypeLabel = $jobTypeLabels[$jType] ?? '';
                    ?>
                    <?php $jobUrl = theme_safe_url($siteUrl . '/jobs/' . $jId, $siteUrl . '/jobs'); ?>
                    <a href="<?php echo htmlspecialchars($jobUrl, ENT_QUOTES, 'UTF-8'); ?>" class="job-hp-card">
                        <div class="job-hp-info">
                            <strong><?php echo $jTitle; ?></strong>
                            <div class="job-hp-meta">
                                <?php if ($jCompany) : ?><span>🏢 <?php echo $jCompany; ?></span><?php endif; ?>
                                <?php if ($jLoc) : ?><span>📍 <?php echo $jLoc; ?></span><?php endif; ?>
                            </div>
                        </div>
                        <?php if ($jTypeLabel) : ?><span class="job-type-badge"><?php echo htmlspecialchars($jTypeLabel, ENT_QUOTES); ?></span><?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php elseif ($hasJobs) : ?>
                <div class="empty-state empty-state--compact">
                    <div class="empty-state-icon">💼</div>
                    <p>Keine offenen Stellen derzeit.</p>
                </div>
                <?php else : ?>
                <p class="plugin-hint">Das <strong>cms-jobprofile-generator</strong> Plugin aktivieren, um Stellen anzuzeigen. <a href="<?php echo htmlspecialchars(theme_safe_url($siteUrl . '/jobs', $siteUrl . '/jobs'), ENT_QUOTES, 'UTF-8'); ?>" class="plugin-hint__link">Zum Stellenmarkt →</a></p>
                <?php endif; ?>
            </section>

            <?php
            \CMS\Hooks::doAction('home_after_jobs');
            ?>
            <?php endif; ?>

            <?php if ($showFallbackContent && !empty($fallbackPosts)) : ?>
            <!-- ─── Blog/Feed-Fallback (Hero-Only Modus) ─────────────────── -->
            <section class="dashboard-section" data-section="feed-fallback" id="home-feed">
                <div class="section-header">
                    <h2>📰 Aktuelle Beiträge</h2>
                    <?php if ($hasFeed) : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/feeds" class="section-link">Alle anzeigen →</a>
                    <?php else : ?>
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog" class="section-link">Alle anzeigen →</a>
                    <?php endif; ?>
                </div>

                <div class="feed-grid">
                    <?php foreach ($fallbackPosts as $post) :
                        // Universelle Feld-Ablesung (Posts vs Feed-Items)
                        $pTitle = htmlspecialchars(_field($post, 'title', 'Beitrag'), ENT_QUOTES, 'UTF-8');
                        $pExcerpt = _field($post, 'excerpt', _field($post, 'description', ''));
                        if (mb_strlen($pExcerpt) > 160) {
                            $pExcerpt = mb_substr($pExcerpt, 0, 157) . '…';
                        }
                        $pExcerpt = htmlspecialchars(strip_tags($pExcerpt), ENT_QUOTES, 'UTF-8');
                        $pImage = _field($post, 'featured_image', _field($post, 'image_url', ''));
                        $pDate = _field($post, 'published_at', _field($post, 'pub_date', ''));
                        $pDateFormatted = $pDate ? date('d.m.Y', strtotime($pDate)) : '';
                        $pAuthor = htmlspecialchars(_field($post, 'author', ''), ENT_QUOTES, 'UTF-8');

                        // Link: Blog-Posts → /blogs/slug, Feed-Items → externer Link
                        $pSlug = _field($post, 'slug', '');
                        $pLink = _field($post, 'link', '');
                        if ($pSlug) {
                            $pSlugPath = implode('/', array_map('rawurlencode', array_filter(explode('/', trim((string) $pSlug, '/')), static fn(string $segment): bool => $segment !== '')));
                            $pUrl = htmlspecialchars(theme_safe_url($siteUrl . '/blog/' . $pSlugPath, $siteUrl . '/blog'), ENT_QUOTES, 'UTF-8');
                            $pTarget = '';
                        } elseif ($pLink) {
                            $pSafeExternal = theme_safe_external_url((string) $pLink);
                            $pUrl = htmlspecialchars($pSafeExternal !== '' ? $pSafeExternal : '#', ENT_QUOTES, 'UTF-8');
                            $pTarget = $pSafeExternal !== '' ? ' target="_blank" rel="noopener noreferrer"' : '';
                        } else {
                            $pUrl = '#';
                            $pTarget = '';
                        }
                        $pImageUrl = theme_safe_url((string) $pImage);
                    ?>
                        <article class="feed-card">
                            <?php if ($pImageUrl) : ?>
                                <div class="feed-card-image">
                                    <img src="<?php echo htmlspecialchars($pImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="<?php echo $pTitle; ?>"
                                         loading="lazy" width="400" height="200">
                                </div>
                            <?php endif; ?>
                            <div class="feed-card-body">
                                <h3 class="feed-card-title">
                                    <a href="<?php echo $pUrl; ?>"<?php echo $pTarget; ?> class="feed-card-link">
                                        <?php echo $pTitle; ?>
                                    </a>
                                </h3>
                                <?php if ($pExcerpt) : ?>
                                    <p class="feed-card-excerpt"><?php echo $pExcerpt; ?></p>
                                <?php endif; ?>
                                <div class="feed-card-meta">
                                    <?php if ($pDateFormatted) : ?>
                                        <span>📅 <?php echo $pDateFormatted; ?></span>
                                    <?php endif; ?>
                                    <?php if ($pAuthor) : ?>
                                        <span>✍️ <?php echo $pAuthor; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php
            // ═════════════════════════════════════════════════════════════════
            // HOOK: home_content (Abwärtskompatibel)
            // Allgemeiner Injektionspunkt für Plugin-Inhalte.
            // Bereits registrierte Callbacks bleiben aktiv.
            // ═════════════════════════════════════════════════════════════════
            \CMS\Hooks::doAction('home_content');
            ?>

            <?php
            // HOOK: home_main_bottom
            // „Mehr laden"-Buttons, Pagination, CTAs.
            \CMS\Hooks::doAction('home_main_bottom');
            ?>

        </div><!-- /.dashboard-main -->

        <?php if ($showSidebar && $homepageLayout !== 'full-width') : ?>
        <!-- ═══════════════════════════════════════════════════════════════════
             SIDEBAR – Vollständig hook-basiert
             Plugins registrieren Widgets über:
               home_sidebar_top    → Priorität < 10 (wichtige Widgets)
               home_sidebar_widget → Priorität 10–90 (Standard)
               home_sidebar_bottom → Priorität > 90 (sekundäre Widgets)

             Empfohlene Prioritäten für home_sidebar_widget:
               10-29: Prominente Widgets (News, Feeds)
               30-49: Interaktive Widgets (Buchungen, Tools)
               50-69: Info-Widgets (Statistiken, Links)
               70-89: Sekundäre Widgets (Werbung, Partner)
             ═══════════════════════════════════════════════════════════════════ -->
        <aside class="dashboard-sidebar" data-section="sidebar">

            <?php
            // HOOK: home_sidebar_top
            // z.B. Benutzer-Profil-Karte, wichtige Benachrichtigungen.
            \CMS\Hooks::doAction('home_sidebar_top');
            ?>

            <?php
            // HOOK: home_sidebar_widget (HAUPT-INJEKTIONSPUNKT)
            // Default-Widgets werden in functions.php registriert.
            // Plugins können sich hier einhängen:
            //   \CMS\Hooks::addAction('home_sidebar_widget', [$this, 'renderWidget'], 15);
            \CMS\Hooks::doAction('home_sidebar_widget');
            ?>

            <?php
            // HOOK: home_sidebar_bottom
            // z.B. Hilfe-Links, Footer-Badges.
            \CMS\Hooks::doAction('home_sidebar_bottom');
            ?>

        </aside><!-- /.dashboard-sidebar -->
        <?php endif; ?>

    </div><!-- /.dashboard-layout -->

    <?php
    // HOOK: home_after_content
    // Full-Width-Sektionen: CTA-Banner, Newsletter, Partner-Logos.
    \CMS\Hooks::doAction('home_after_content');
    ?>

    <?php
    // ═════════════════════════════════════════════════════════════════════════
    // CTA-SEKTION (Customizer: homepage_cta)
    // ═════════════════════════════════════════════════════════════════════════
    $_cta = [];
    try {
        $_cta = \CMS\Services\ThemeCustomizer::instance()->getCategory('homepage_cta');
    } catch (\Throwable $e) {
        $_cta = [];
    }
    $ctaEnabled      = filter_var($_cta['cta_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $ctaShowLoggedIn = filter_var($_cta['cta_show_logged_in'] ?? true, FILTER_VALIDATE_BOOLEAN);
    $ctaVisible      = $ctaEnabled && ($ctaShowLoggedIn || !$isLoggedIn);

    if ($ctaVisible) :
        $ctaTitle       = (string)($_cta['cta_title'] ?? 'Bereit, Teil unseres Netzwerks zu werden?');
        $ctaText        = (string)($_cta['cta_text'] ?? '');
        $ctaBtnText     = (string)($_cta['cta_button_text'] ?? 'Jetzt registrieren');
        $ctaBtnUrl      = (string)($_cta['cta_button_url'] ?? '/register');
        $ctaBtn2Text    = (string)($_cta['cta_button_secondary_text'] ?? '');
        $ctaBtn2Url     = (string)($_cta['cta_button_secondary_url'] ?? '');
        $ctaStyle       = (string)($_cta['cta_style'] ?? 'dark');
        $ctaAlignment   = (string)($_cta['cta_alignment'] ?? 'center');
        $ctaSize        = (string)($_cta['cta_size'] ?? 'normal');

        // URL-Auflösung (relative URLs → absolut)
        if ($ctaBtnUrl && $ctaBtnUrl[0] === '/') {
            $ctaBtnUrl = $siteUrl . $ctaBtnUrl;
        }
        if ($ctaBtn2Url && $ctaBtn2Url[0] === '/') {
            $ctaBtn2Url = $siteUrl . $ctaBtn2Url;
        }

        $ctaClasses = 'homepage-cta';
        $ctaClasses .= ' homepage-cta--' . htmlspecialchars($ctaStyle, ENT_QUOTES, 'UTF-8');
        $ctaClasses .= ' homepage-cta--' . htmlspecialchars($ctaAlignment, ENT_QUOTES, 'UTF-8');
        $ctaClasses .= ' homepage-cta--' . htmlspecialchars($ctaSize, ENT_QUOTES, 'UTF-8');
    ?>
    <section class="<?php echo $ctaClasses; ?>" data-section="cta">
        <div class="homepage-cta-inner">
            <?php if (!empty($ctaTitle)) : ?>
                <h2 class="homepage-cta-title"><?php echo htmlspecialchars($ctaTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php endif; ?>
            <?php if (!empty($ctaText)) : ?>
                <p class="homepage-cta-text"><?php echo htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <div class="homepage-cta-actions">
                <?php if (!empty($ctaBtnText)) : ?>
                    <a href="<?php echo htmlspecialchars($ctaBtnUrl, ENT_QUOTES, 'UTF-8'); ?>" class="homepage-cta-btn homepage-cta-btn--primary">
                        <?php echo htmlspecialchars($ctaBtnText, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($ctaBtn2Text)) : ?>
                    <a href="<?php echo htmlspecialchars($ctaBtn2Url, ENT_QUOTES, 'UTF-8'); ?>" class="homepage-cta-btn homepage-cta-btn--secondary">
                        <?php echo htmlspecialchars($ctaBtn2Text, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // HOOK: home_before_strip
    \CMS\Hooks::doAction('home_before_strip');
    ?>

    <?php if ($showStrip && !empty($events)) : ?>
    <!-- Events Footer Strip (Customizer: homepage.show_events_strip) -->
    <section class="events-strip" data-section="events-strip">
        <div class="events-strip-inner">
            <div class="events-strip-label">
                Kommende Events & Konferenzen
            </div>
            <?php foreach (array_slice($events, 0, 3) as $ev) :
                $evTitle = htmlspecialchars(_field($ev, 'title', 'Event'), ENT_QUOTES, 'UTF-8');
                $evLoc   = htmlspecialchars(_field($ev, 'location', ''), ENT_QUOTES, 'UTF-8');
                $evDate  = _field($ev, 'start_date', '');
                $evDay   = $evDate ? date('d', strtotime($evDate)) : '';
                $evMon   = $evDate ? ($monthsDE[(int)date('n', strtotime($evDate))] ?? '') : '';
            ?>
                <div class="events-strip-card">
                    <?php if ($evDay) : ?>
                        <div class="event-date-block event-date-block--compact">
                            <div class="event-date-day event-date-day--compact"><?php echo $evDay; ?></div>
                            <div class="event-date-month"><?php echo $evMon; ?></div>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="events-strip-title"><?php echo $evTitle; ?></div>
                        <?php if ($evLoc) : ?>
                            <div class="events-strip-location">📍 <?php echo $evLoc; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // HOOK: home_after_strip — Letzter Hook auf der Startseite
    \CMS\Hooks::doAction('home_after_strip');
    ?>

</main>
