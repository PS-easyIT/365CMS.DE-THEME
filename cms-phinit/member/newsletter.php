<?php
/**
 * Member Newsletter-Verwaltung – CMS Phinit Theme
 *
 * Newsletter-Abonnements verwalten (An-/Abmeldung).
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
$activePage  = 'newsletter';

$success = '';
$error   = '';

// Newsletter-Plugin prüfen
$hasNewsletterPlugin = \CMS\PluginManager::instance()->isPluginActive('cms-newsletter');

// POST: An-/Abmelden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasNewsletterPlugin) {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_newsletter')) {
        $error = 'Sicherheitscheck fehlgeschlagen.';
    } else {
        $action = $_POST['newsletter_action'] ?? '';
        $listId = (int)($_POST['list_id'] ?? 0);

        if ($action === 'subscribe' && $listId > 0) {
            $exists = $db->get_var(
                "SELECT COUNT(*) FROM {$prefix}newsletter_subscribers WHERE list_id = ? AND email = ?",
                [$listId, $currentUser->email]
            );
            if (!$exists) {
                $db->execute(
                    "INSERT INTO {$prefix}newsletter_subscribers (list_id, email, user_id, status, subscribed_at) VALUES (?, ?, ?, 'active', NOW())",
                    [$listId, $currentUser->email, (int)$currentUser->id]
                );
            }
            $success = 'Erfolgreich angemeldet!';
        } elseif ($action === 'unsubscribe' && $listId > 0) {
            $db->execute(
                "DELETE FROM {$prefix}newsletter_subscribers WHERE list_id = ? AND user_id = ?",
                [$listId, (int)$currentUser->id]
            );
            $success = 'Erfolgreich abgemeldet.';
        }
        $csrfToken = \CMS\Security::instance()->generateToken('member_newsletter');
    }
}

$csrfToken = $csrfToken ?? \CMS\Security::instance()->generateToken('member_newsletter');

// Newsletter-Listen laden
$lists = [];
$subscribed = [];
if ($hasNewsletterPlugin) {
    $lists = array_map(fn($r) => (array)$r, $db->get_results(
        "SELECT * FROM {$prefix}newsletter_lists WHERE status = 'active' ORDER BY name ASC"
    ) ?: []);

    $subRows = array_map(fn($r) => (array)$r, $db->get_results(
        "SELECT list_id FROM {$prefix}newsletter_subscribers WHERE user_id = ? AND status = 'active'",
        [(int)$currentUser->id]
    ) ?: []);
    foreach ($subRows as $sr) {
        $subscribed[] = (int)$sr['list_id'];
    }
}

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>📧 Newsletter</h1>
            <p>Verwalte deine Newsletter-Abonnements.</p>
        </div>

        <?php if ($success): ?>
        <div class="member-alert member-alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="member-alert member-alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$hasNewsletterPlugin): ?>
        <div class="member-empty-state" data-anim>
            <p style="font-size:2.5rem;">📧</p>
            <p><strong>Newsletter nicht verfügbar</strong></p>
            <p>Das Newsletter-Plugin ist derzeit nicht aktiviert.</p>
        </div>
        <?php elseif (empty($lists)): ?>
        <div class="member-empty-state" data-anim>
            <p style="font-size:2.5rem;">📭</p>
            <p><strong>Keine Newsletter-Listen vorhanden</strong></p>
            <p>Es sind derzeit keine Newsletter-Listen verfügbar.</p>
        </div>
        <?php else: ?>

        <div class="member-newsletter-grid" data-anim data-anim-delay="1">
            <?php foreach ($lists as $list):
                $isSub = in_array((int)$list['id'], $subscribed, true);
            ?>
            <div class="member-card member-newsletter-card<?php echo $isSub ? ' member-newsletter-card--active' : ''; ?>">
                <div class="member-card-header">
                    <h3><?php echo $isSub ? '✅' : '📧'; ?> <?php echo htmlspecialchars($list['name'] ?? '', ENT_QUOTES); ?></h3>
                </div>
                <?php if (!empty($list['description'])): ?>
                <p class="member-newsletter-desc"><?php echo htmlspecialchars($list['description']); ?></p>
                <?php endif; ?>
                <form method="POST" class="member-newsletter-action">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                    <input type="hidden" name="list_id" value="<?php echo (int)$list['id']; ?>">
                    <?php if ($isSub): ?>
                    <input type="hidden" name="newsletter_action" value="unsubscribe">
                    <button type="submit" class="btn btn-sm btn-secondary">Abmelden</button>
                    <span class="member-newsletter-status">Abonniert ✓</span>
                    <?php else: ?>
                    <input type="hidden" name="newsletter_action" value="subscribe">
                    <button type="submit" class="btn btn-sm btn-primary">Anmelden</button>
                    <?php endif; ?>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
