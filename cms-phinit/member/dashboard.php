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

// Dashboard-Statistiken laden
$favCount = (int)$db->get_var(
    "SELECT COUNT(*) FROM {$prefix}favorites WHERE user_id = ?",
    [(int)$currentUser->id]
) ?: 0;

$commentCount = (int)$db->get_var(
    "SELECT COUNT(*) FROM {$prefix}comments WHERE user_id = ? AND status = 'approved'",
    [(int)$currentUser->id]
) ?: 0;

$postCount = (int)$db->get_var(
    "SELECT COUNT(*) FROM {$prefix}posts WHERE author_id = ? AND status = 'published'",
    [(int)$currentUser->id]
) ?: 0;

// Letzte Aktivitäten (neueste Kommentare)
$recentComments = $db->get_results(
    "SELECT c.*, p.title AS post_title, p.slug AS post_slug
     FROM {$prefix}comments c
     LEFT JOIN {$prefix}posts p ON c.post_id = p.id
     WHERE c.user_id = ?
     ORDER BY c.created_at DESC
     LIMIT 5",
    [(int)$currentUser->id]
) ?: [];

// Letzte Favoriten
$recentFavorites = $db->get_results(
    "SELECT f.*, p.title AS post_title, p.slug AS post_slug
     FROM {$prefix}favorites f
     LEFT JOIN {$prefix}posts p ON f.post_id = p.id
     WHERE f.user_id = ?
     ORDER BY f.created_at DESC
     LIMIT 5",
    [(int)$currentUser->id]
) ?: [];

// Begrüßung
$hour     = (int)date('H');
$greeting = $hour < 12 ? 'Guten Morgen' : ($hour < 18 ? 'Guten Tag' : 'Guten Abend');

// Admin-Analysen laden (nur für Admins)
$isAdmin    = $auth->isAdmin();
$adminStats = [];
if ($isAdmin) {
    $adminStats['total_users'] = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}users"
    ) ?: 0;
    $adminStats['total_posts'] = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}posts WHERE status = 'published'"
    ) ?: 0;
    $adminStats['total_comments'] = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}comments"
    ) ?: 0;
    $adminStats['pending_comments'] = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}comments WHERE status = 'pending'"
    ) ?: 0;
    $adminStats['total_views'] = (int)$db->get_var(
        "SELECT COALESCE(SUM(views), 0) FROM {$prefix}posts"
    ) ?: 0;
    $adminStats['posts_today'] = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}posts WHERE status = 'published' AND DATE(created_at) = CURDATE()"
    ) ?: 0;
    $adminStats['users_today'] = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}users WHERE DATE(created_at) = CURDATE()"
    ) ?: 0;
}

// Theme Header
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <!-- Begrüßung -->
        <div class="member-welcome" data-anim>
            <h1><?php echo $greeting; ?>, <?php echo htmlspecialchars(explode(' ', $currentUser->username)[0]); ?>! 👋</h1>
            <p>Willkommen in deinem persönlichen Bereich. Hier findest du deine Favoriten, Kommentare und Einstellungen.</p>
        </div>

        <!-- Stat-Cards -->
        <div class="member-stats" data-anim data-anim-delay="1">
            <div class="member-stat-card">
                <div class="member-stat-icon">⭐</div>
                <div class="member-stat-value"><?php echo $favCount; ?></div>
                <div class="member-stat-label">Favoriten</div>
            </div>
            <div class="member-stat-card">
                <div class="member-stat-icon">💬</div>
                <div class="member-stat-value"><?php echo $commentCount; ?></div>
                <div class="member-stat-label">Kommentare</div>
            </div>
            <div class="member-stat-card">
                <div class="member-stat-icon">📝</div>
                <div class="member-stat-value"><?php echo $postCount; ?></div>
                <div class="member-stat-label">Beiträge</div>
            </div>
        </div>

        <?php if ($isAdmin): ?>
        <!-- Admin-Analysen -->
        <div class="member-card member-admin-analytics" data-anim data-anim-delay="1.5">
            <div class="member-card-header">
                <h3>📈 CMS-Analysen</h3>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/admin/" class="member-card-link">Admincenter →</a>
            </div>
            <div class="member-stats member-stats--admin">
                <div class="member-stat-card member-stat-card--admin">
                    <div class="member-stat-icon">👥</div>
                    <div class="member-stat-value"><?php echo number_format($adminStats['total_users']); ?></div>
                    <div class="member-stat-label">Benutzer gesamt</div>
                    <?php if ($adminStats['users_today'] > 0): ?>
                    <div class="member-stat-badge">+<?php echo $adminStats['users_today']; ?> heute</div>
                    <?php endif; ?>
                </div>
                <div class="member-stat-card member-stat-card--admin">
                    <div class="member-stat-icon">📄</div>
                    <div class="member-stat-value"><?php echo number_format($adminStats['total_posts']); ?></div>
                    <div class="member-stat-label">Veröffentlicht</div>
                    <?php if ($adminStats['posts_today'] > 0): ?>
                    <div class="member-stat-badge">+<?php echo $adminStats['posts_today']; ?> heute</div>
                    <?php endif; ?>
                </div>
                <div class="member-stat-card member-stat-card--admin">
                    <div class="member-stat-icon">💬</div>
                    <div class="member-stat-value"><?php echo number_format($adminStats['total_comments']); ?></div>
                    <div class="member-stat-label">Kommentare</div>
                    <?php if ($adminStats['pending_comments'] > 0): ?>
                    <div class="member-stat-badge member-stat-badge--warn"><?php echo $adminStats['pending_comments']; ?> ausstehend</div>
                    <?php endif; ?>
                </div>
                <div class="member-stat-card member-stat-card--admin">
                    <div class="member-stat-icon">👁️</div>
                    <div class="member-stat-value"><?php echo number_format($adminStats['total_views']); ?></div>
                    <div class="member-stat-label">Seitenaufrufe</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 2-Column Grid -->
        <div class="member-grid-2" data-anim data-anim-delay="2">

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
                        <span class="member-activity-date"><?php echo date('d.m.Y', strtotime($c['created_at'])); ?></span>
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
                        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($f['post_slug'] ?? ''), ENT_QUOTES); ?>"><?php echo htmlspecialchars($f['post_title'] ?? 'Beitrag', ENT_QUOTES); ?></a>
                        <span class="member-activity-date"><?php echo date('d.m.Y', strtotime($f['created_at'])); ?></span>
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
        <div class="member-quicklinks" data-anim data-anim-delay="3">
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
