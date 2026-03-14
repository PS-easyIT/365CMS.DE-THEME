<?php
/**
 * Member Dashboard – CMS Phinit Theme
 *
 * Persönliches Dashboard für eingeloggte Mitglieder.
 * Wird über Router /member aufgerufen (Theme-Override).
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$auth = \CMS\Auth::instance();
if (!$auth->isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$currentUser = $auth->getCurrentUser();
$db          = \CMS\Database::instance();
$prefix      = $db->getPrefix();
$siteUrl     = SITE_URL;
$activePage  = 'dashboard';
$pageFavorites = phinit_get_page_favorites_for_user((int) $currentUser->id);

// Hilfsfunktion: Tabelle existiert?
$_tableExists = function (string $table) use ($db): bool {
    try {
        $db->getPdo()->query("SELECT 1 FROM `{$table}` LIMIT 1");
        return true;
    } catch (\Throwable $e) {
        return false;
    }
};

$_hasFavorites = $_tableExists("{$prefix}favorites");
$_hasComments  = $_tableExists("{$prefix}comments");
$_hasPosts     = $_tableExists("{$prefix}posts");
$_hasUsers     = $_tableExists("{$prefix}users");

// Dashboard-Statistiken laden (fehlende Tabellen → 0)
$favCount = 0;
if ($_hasFavorites) {
    try {
        $favCount = (int)$db->get_var(
            "SELECT COUNT(*) FROM {$prefix}favorites WHERE user_id = ?",
            [(int)$currentUser->id]
        ) ?: 0;
    } catch (\Throwable $e) {}
}
$favCount += count($pageFavorites);

$commentCount = 0;
if ($_hasComments) {
    try {
        $commentCount = (int)$db->get_var(
            "SELECT COUNT(*) FROM {$prefix}comments WHERE user_id = ? AND status = 'approved'",
            [(int)$currentUser->id]
        ) ?: 0;
    } catch (\Throwable $e) {}
}

$postCount = 0;
if ($_hasPosts) {
    try {
        $postCount = (int)$db->get_var(
            "SELECT COUNT(*) FROM {$prefix}posts WHERE author_id = ? AND status = 'published'",
            [(int)$currentUser->id]
        ) ?: 0;
    } catch (\Throwable $e) {}
}

// Letzte Aktivitäten (neueste Kommentare)
$recentComments = [];
if ($_hasComments && $_hasPosts) {
    try {
        $recentComments = $db->get_results(
            "SELECT c.*, p.title AS post_title, p.slug AS post_slug
             FROM {$prefix}comments c
             LEFT JOIN {$prefix}posts p ON c.post_id = p.id
             WHERE c.user_id = ?
             ORDER BY c.post_date DESC
             LIMIT 5",
            [(int)$currentUser->id]
        ) ?: [];
        $recentComments = array_map(fn($r) => (array)$r, $recentComments);
    } catch (\Throwable $e) {}
}

// Letzte Favoriten
$recentFavorites = [];
if ($_hasFavorites && $_hasPosts) {
    try {
        $recentFavorites = $db->get_results(
            "SELECT f.*, p.title AS post_title, p.slug AS post_slug
             FROM {$prefix}favorites f
             LEFT JOIN {$prefix}posts p ON f.post_id = p.id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC
             LIMIT 5",
            [(int)$currentUser->id]
        ) ?: [];
        $recentFavorites = array_map(fn($r) => (array)$r, $recentFavorites);
    } catch (\Throwable $e) {}
}

$recentFavorites = array_merge(
    array_map(static fn(array $favorite): array => [
        'title' => (string) ($favorite['post_title'] ?? 'Beitrag'),
        'url' => '/blog/' . rawurlencode((string) ($favorite['post_slug'] ?? '')),
        'created_at' => (string) ($favorite['created_at'] ?? ''),
    ], $recentFavorites),
    array_map(static fn(array $favorite): array => [
        'title' => (string) ($favorite['title'] ?? 'Seite'),
        'url' => (string) ($favorite['url'] ?? '#'),
        'created_at' => (string) ($favorite['created_at'] ?? ''),
    ], $pageFavorites)
);

usort($recentFavorites, static function (array $a, array $b): int {
    return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
});

$recentFavorites = array_slice($recentFavorites, 0, 5);

// Begrüßung
$hour     = (int)date('H');
$greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');

$favoriteUrl = htmlspecialchars($siteUrl, ENT_QUOTES) . '/member/favorites';
$profileUrl = htmlspecialchars($siteUrl, ENT_QUOTES) . '/member/profile';
$securityUrl = htmlspecialchars($siteUrl, ENT_QUOTES) . '/member/security';
$commentsUrl = htmlspecialchars($siteUrl, ENT_QUOTES) . '/member/comments';
$analyticsUrl = htmlspecialchars($siteUrl, ENT_QUOTES) . '/member/analytics';

$isAdmin    = $auth->isAdmin();

// Theme Header
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <section class="member-dashboard-hero" data-anim>
            <div class="member-dashboard-hero__content">
                <span class="member-dashboard-hero__eyebrow">🏠 Member Home</span>
                <h1><?php echo $greeting; ?>, <?php echo htmlspecialchars(explode(' ', $currentUser->username)[0]); ?>! 👋</h1>
                <p>Dein persönlicher Startbereich mit den wichtigsten Inhalten, Sicherheitsinfos und schnellen Sprüngen zu deinen häufigsten Aufgaben.</p>

                <div class="member-dashboard-hero__actions">
                    <a href="<?php echo $favoriteUrl; ?>" class="member-hero-action">⭐ Favoriten</a>
                    <a href="<?php echo $profileUrl; ?>" class="member-hero-action">👤 Profil</a>
                    <a href="<?php echo $securityUrl; ?>" class="member-hero-action">🔒 Sicherheit</a>
                    <a href="<?php echo $commentsUrl; ?>" class="member-hero-action">💬 Kommentare</a>
                    <?php if ($isAdmin): ?>
                    <a href="<?php echo $analyticsUrl; ?>" class="member-hero-action">📈 Analytics</a>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="member-dashboard-hero__panel">
                <h3>Kontostatus</h3>
                <dl class="member-dashboard-hero__facts">
                    <div>
                        <dt>Mitglied seit</dt>
                        <dd><?php echo htmlspecialchars(date('d.m.Y', strtotime((string) ($currentUser->created_at ?? 'now'))), ENT_QUOTES); ?></dd>
                    </div>
                    <div>
                        <dt>Rolle</dt>
                        <dd><?php echo htmlspecialchars(ucfirst((string) ($currentUser->role ?? 'member')), ENT_QUOTES); ?></dd>
                    </div>
                    <div>
                        <dt>E-Mail</dt>
                        <dd><?php echo htmlspecialchars((string) ($currentUser->email ?? '–'), ENT_QUOTES); ?></dd>
                    </div>
                </dl>
            </aside>
        </section>

        <div class="member-dashboard-overview" data-anim data-anim-delay="1">
            <a href="<?php echo $favoriteUrl; ?>" class="member-overview-card member-overview-card--favorite">
                <span class="member-overview-card__icon">⭐</span>
                <strong><?php echo $favCount; ?></strong>
                <span>Gespeicherte Favoriten</span>
            </a>
            <a href="<?php echo $commentsUrl; ?>" class="member-overview-card member-overview-card--comment">
                <span class="member-overview-card__icon">💬</span>
                <strong><?php echo $commentCount; ?></strong>
                <span>Eigene Kommentare</span>
            </a>
            <div class="member-overview-card member-overview-card--post">
                <span class="member-overview-card__icon">📝</span>
                <strong><?php echo $postCount; ?></strong>
                <span>Veröffentlichte Beiträge</span>
            </div>
            <a href="<?php echo $securityUrl; ?>" class="member-overview-card member-overview-card--security">
                <span class="member-overview-card__icon">🔒</span>
                <strong><?php echo $isAdmin ? 'Admin' : 'Aktiv'; ?></strong>
                <span>Sicherheitsbereich öffnen</span>
            </a>
        </div>

        <!-- 2-Column Grid -->
        <div class="member-grid-2 member-grid-2--dashboard" data-anim data-anim-delay="2">

            <!-- Letzte Kommentare -->
            <div class="member-card">
                <div class="member-card-header">
                    <h3>💬 Letzte Kommentare</h3>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/comments" class="member-card-link">Alle →</a>
                </div>
                <?php if (!empty($recentComments)): ?>
                <ul class="member-activity-list">
                    <?php foreach ($recentComments as $c): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($c['post_slug'] ?? ''), ENT_QUOTES); ?>"><?php echo htmlspecialchars($c['post_title'] ?? 'Beitrag', ENT_QUOTES); ?></a>
                        <span class="member-activity-date"><?php echo date('d.m.Y', strtotime($c['post_date'] ?? '')); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="member-empty">
                    <p>📭 Noch keine Kommentare verfasst.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Letzte Favoriten -->
            <div class="member-card">
                <div class="member-card-header">
                    <h3>⭐ Letzte Favoriten</h3>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/favorites" class="member-card-link">Alle →</a>
                </div>
                <?php if (!empty($recentFavorites)): ?>
                <ul class="member-activity-list">
                    <?php foreach ($recentFavorites as $f): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars(str_starts_with((string) ($f['url'] ?? '#'), 'http') ? (string) ($f['url'] ?? '#') : ($siteUrl . (string) ($f['url'] ?? '#')), ENT_QUOTES); ?>"><?php echo htmlspecialchars($f['title'] ?? 'Favorit', ENT_QUOTES); ?></a>
                        <span class="member-activity-date"><?php echo date('d.m.Y', strtotime((string) ($f['created_at'] ?? 'now'))); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="member-empty">
                    <p>📭 Noch keine Favoriten gespeichert.</p>
                </div>
                <?php endif; ?>
            </div>

        </div><!-- /.member-grid-2 -->

        <!-- Schnellzugriff -->
        <div class="member-quicklinks member-quicklinks--dashboard" data-anim data-anim-delay="3">
            <h3>🚀 Schnellzugriff</h3>
            <div class="member-quicklinks-grid">
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/profile" class="member-quicklink-card">
                    <span>👤</span> Profil bearbeiten
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/newsletter" class="member-quicklink-card">
                    <span>📧</span> Newsletter
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/feeds" class="member-quicklink-card">
                    <span>📡</span> Feed-Abos
                </a>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/forum" class="member-quicklink-card">
                    <span>🗣️</span> Forum
                </a>
            </div>
        </div>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
