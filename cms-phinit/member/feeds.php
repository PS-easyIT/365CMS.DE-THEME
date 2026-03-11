<?php
/**
 * Member Feed-Abos – CMS Phinit Theme
 *
 * RSS/Feed-Abonnements verwalten (E-Mail-Benachrichtigungen).
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
$activePage  = 'feeds';

$success = '';
$error   = '';

// Feed-Plugin prüfen
$hasFeedPlugin = \CMS\PluginManager::instance()->isPluginActive('cms-feed');

// POST: An-/Abmelden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasFeedPlugin) {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_feeds')) {
        $error = 'Sicherheitscheck fehlgeschlagen.';
    } else {
        $action    = $_POST['feed_action'] ?? '';
        $channelId = (int)($_POST['channel_id'] ?? 0);

        if ($action === 'subscribe' && $channelId > 0) {
            $exists = $db->get_var(
                "SELECT COUNT(*) FROM {$prefix}feed_subscriptions WHERE channel_id = ? AND user_id = ?",
                [$channelId, (int)$currentUser->id]
            );
            if (!$exists) {
                $db->execute(
                    "INSERT INTO {$prefix}feed_subscriptions (channel_id, user_id, email, notify_email, created_at) VALUES (?, ?, ?, 1, NOW())",
                    [$channelId, (int)$currentUser->id, $currentUser->email]
                );
            }
            $success = 'Feed-Abo aktiviert!';
        } elseif ($action === 'unsubscribe' && $channelId > 0) {
            $db->execute(
                "DELETE FROM {$prefix}feed_subscriptions WHERE channel_id = ? AND user_id = ?",
                [$channelId, (int)$currentUser->id]
            );
            $success = 'Feed-Abo deaktiviert.';
        } elseif ($action === 'toggle_notify' && $channelId > 0) {
            $current = (int)$db->get_var(
                "SELECT notify_email FROM {$prefix}feed_subscriptions WHERE channel_id = ? AND user_id = ?",
                [$channelId, (int)$currentUser->id]
            );
            $db->execute(
                "UPDATE {$prefix}feed_subscriptions SET notify_email = ? WHERE channel_id = ? AND user_id = ?",
                [$current ? 0 : 1, $channelId, (int)$currentUser->id]
            );
            $success = 'E-Mail-Benachrichtigung ' . ($current ? 'deaktiviert' : 'aktiviert') . '.';
        }
        $csrfToken = \CMS\Security::instance()->generateToken('member_feeds');
    }
}

$csrfToken = $csrfToken ?? \CMS\Security::instance()->generateToken('member_feeds');

// Feed-Kanäle und Abos laden
$channels   = [];
$subscribed = [];
if ($hasFeedPlugin) {
    $channels = array_map(
        fn($r) => (array)$r,
        $db->get_results(
            "SELECT * FROM {$prefix}feed_channels WHERE is_active = 1 ORDER BY name ASC"
        ) ?: []
    );

    $subRows = array_map(
        fn($r) => (array)$r,
        $db->get_results(
            "SELECT channel_id, notify_email FROM {$prefix}feed_subscriptions WHERE user_id = ?",
            [(int)$currentUser->id]
        ) ?: []
    );
    foreach ($subRows as $sr) {
        $subscribed[(int)$sr['channel_id']] = (bool)$sr['notify_email'];
    }
}

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>📡 Feed-Abos</h1>
            <p>Verwalte deine RSS-Feed-Abonnements und E-Mail-Benachrichtigungen.</p>
        </div>

        <?php if ($success): ?>
        <div class="member-alert member-alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="member-alert member-alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$hasFeedPlugin): ?>
        <div class="member-empty-state" data-anim>
            <p class="member-empty-state__icon">📡</p>
            <p><strong>Feed-System nicht verfügbar</strong></p>
            <p>Das Feed-Plugin ist derzeit nicht aktiviert.</p>
        </div>
        <?php elseif (empty($channels)): ?>
        <div class="member-empty-state" data-anim>
            <p class="member-empty-state__icon">📭</p>
            <p><strong>Keine Feed-Kanäle vorhanden</strong></p>
            <p>Es sind derzeit keine Kanäle verfügbar.</p>
        </div>
        <?php else: ?>

        <div class="member-newsletter-grid" data-anim data-anim-delay="1">
            <?php foreach ($channels as $ch):
                $chId  = (int)$ch['id'];
                $isSub = array_key_exists($chId, $subscribed);
                $notify = $isSub ? $subscribed[$chId] : false;
            ?>
            <div class="member-card member-newsletter-card<?php echo $isSub ? ' member-newsletter-card--active' : ''; ?>">
                <div class="member-card-header">
                    <h3><?php echo $isSub ? '📡' : '📰'; ?> <?php echo htmlspecialchars($ch['name'] ?? '', ENT_QUOTES); ?></h3>
                </div>
                <?php if (!empty($ch['description'])): ?>
                <p class="member-newsletter-desc"><?php echo htmlspecialchars($ch['description']); ?></p>
                <?php endif; ?>
                <?php if (!empty($ch['feed_url'])): ?>
                <p class="member-feed-url">
                    <a href="<?php echo htmlspecialchars($ch['feed_url'], ENT_QUOTES); ?>" target="_blank" rel="noopener noreferrer" title="Feed-URL">
                        🔗 <?php echo htmlspecialchars(parse_url($ch['feed_url'], PHP_URL_HOST) ?? ''); ?>
                    </a>
                </p>
                <?php endif; ?>
                <div class="member-newsletter-action">
                    <?php if ($isSub): ?>
                    <form method="POST" class="member-inline-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                        <input type="hidden" name="channel_id" value="<?php echo $chId; ?>">
                        <input type="hidden" name="feed_action" value="toggle_notify">
                        <button type="submit" class="btn btn-sm <?php echo $notify ? 'btn-primary' : 'btn-secondary'; ?>" title="E-Mail-Benachrichtigung">
                            <?php echo $notify ? '🔔 Mail aktiv' : '🔕 Mail aus'; ?>
                        </button>
                    </form>
                    <form method="POST" class="member-inline-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                        <input type="hidden" name="channel_id" value="<?php echo $chId; ?>">
                        <input type="hidden" name="feed_action" value="unsubscribe">
                        <button type="submit" class="btn btn-sm btn-secondary">Deabonnieren</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" class="member-inline-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                        <input type="hidden" name="channel_id" value="<?php echo $chId; ?>">
                        <input type="hidden" name="feed_action" value="subscribe">
                        <button type="submit" class="btn btn-sm btn-primary">Abonnieren</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
