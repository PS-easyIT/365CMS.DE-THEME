<?php
/**
 * Member Forum – CMS Phinit Theme
 *
 * Forum-Übersicht mit den neuesten Diskussionen und eigenen Beiträgen.
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
$activePage  = 'forum';

// Forum-Plugin prüfen
$hasForumPlugin = \CMS\PluginManager::instance()->isPluginActive('cms-forum');

// Tab: all (neueste) oder mine (eigene)
$tab = $_GET['tab'] ?? 'all';

$threads     = [];
$myThreads   = [];
$myReplies   = 0;

if ($hasForumPlugin) {
    // Neueste Threads
    $threads = $db->get_results(
        "SELECT t.*, u.username AS author_name,
                (SELECT COUNT(*) FROM {$prefix}forum_replies r WHERE r.thread_id = t.id) AS reply_count
         FROM {$prefix}forum_threads t
         LEFT JOIN {$prefix}users u ON t.user_id = u.id
         WHERE t.status = 'open'
         ORDER BY t.updated_at DESC
         LIMIT 20"
    ) ?: [];

    // Eigene Threads
    $myThreads = $db->get_results(
        "SELECT t.*,
                (SELECT COUNT(*) FROM {$prefix}forum_replies r WHERE r.thread_id = t.id) AS reply_count
         FROM {$prefix}forum_threads t
         WHERE t.user_id = ?
         ORDER BY t.updated_at DESC
         LIMIT 20",
        [(int)$currentUser->id]
    ) ?: [];

    // Eigene Antworten
    $myReplies = (int)$db->get_var(
        "SELECT COUNT(*) FROM {$prefix}forum_replies WHERE user_id = ?",
        [(int)$currentUser->id]
    ) ?: 0;
}

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>🗣️ Forum</h1>
            <p>Diskutiere mit der Community über IT-Themen.</p>
        </div>

        <?php if (!$hasForumPlugin): ?>
        <div class="member-empty-state" data-anim>
            <p style="font-size:2.5rem;">🗣️</p>
            <p><strong>Forum nicht verfügbar</strong></p>
            <p>Das Forum-Plugin ist derzeit nicht aktiviert.</p>
        </div>
        <?php else: ?>

        <!-- Stats -->
        <div class="member-stats member-stats--compact" data-anim data-anim-delay="1">
            <div class="member-stat-card">
                <div class="member-stat-icon">📝</div>
                <div class="member-stat-value"><?php echo count($myThreads); ?></div>
                <div class="member-stat-label">Eigene Threads</div>
            </div>
            <div class="member-stat-card">
                <div class="member-stat-icon">💬</div>
                <div class="member-stat-value"><?php echo $myReplies; ?></div>
                <div class="member-stat-label">Meine Antworten</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="member-filter-tabs" data-anim data-anim-delay="2">
            <a href="?tab=all"  class="member-filter-tab<?php echo $tab === 'all' ? ' active' : ''; ?>">🌐 Neueste Diskussionen</a>
            <a href="?tab=mine" class="member-filter-tab<?php echo $tab === 'mine' ? ' active' : ''; ?>">👤 Meine Threads</a>
        </div>

        <?php
        $displayThreads = ($tab === 'mine') ? $myThreads : $threads;
        ?>

        <?php if (!empty($displayThreads)): ?>
        <div class="member-forum-list" data-anim data-anim-delay="3">
            <?php foreach ($displayThreads as $t): ?>
            <a href="<?php echo htmlspecialchars($siteUrl . '/member/plugin/cms-forum/thread/' . (int)$t['id'], ENT_QUOTES); ?>" class="member-forum-thread">
                <div class="member-forum-thread-main">
                    <h3><?php echo htmlspecialchars($t['title'] ?? '', ENT_QUOTES); ?></h3>
                    <div class="member-forum-thread-meta">
                        <?php if ($tab === 'all' && !empty($t['author_name'])): ?>
                        <span>von <?php echo htmlspecialchars($t['author_name']); ?></span>
                        <?php endif; ?>
                        <span><?php echo date('d.m.Y', strtotime($t['updated_at'] ?? $t['created_at'])); ?></span>
                    </div>
                </div>
                <div class="member-forum-thread-replies">
                    <span class="member-forum-reply-count"><?php echo (int)$t['reply_count']; ?></span>
                    <span class="member-forum-reply-label">Antworten</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="member-empty-state">
            <p style="font-size:2rem;">📭</p>
            <p><strong><?php echo $tab === 'mine' ? 'Noch keine eigenen Threads' : 'Noch keine Diskussionen'; ?></strong></p>
        </div>
        <?php endif; ?>

        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
