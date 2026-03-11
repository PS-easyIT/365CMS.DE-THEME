<?php
/**
 * Member Favoriten – CMS Phinit Theme
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
$activePage  = 'favorites';

// Favorit entfernen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    if (\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_favorites')) {
        $favId = (int)($_POST['favorite_id'] ?? 0);
        if ($favId > 0) {
            $db->execute(
                "DELETE FROM {$prefix}favorites WHERE id = ? AND user_id = ?",
                [$favId, (int)$currentUser->id]
            );
        }
    }
    header('Location: ' . $siteUrl . '/member/favorites');
    exit;
}

$csrfToken = \CMS\Security::instance()->generateToken('member_favorites');

// Paginierung
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset  = ($page - 1) * $perPage;

$total = (int)$db->get_var(
    "SELECT COUNT(*) FROM {$prefix}favorites WHERE user_id = ?",
    [(int)$currentUser->id]
) ?: 0;

$pages = (int)ceil($total / $perPage);

$favorites = array_map(
    fn($r) => (array)$r,
    $db->get_results(
        "SELECT f.*, p.title AS post_title, p.slug AS post_slug, p.excerpt, p.featured_image,
                p.created_at AS post_date, c.name AS category_name
         FROM {$prefix}favorites f
         LEFT JOIN {$prefix}posts p ON f.post_id = p.id
         LEFT JOIN {$prefix}post_categories c ON p.category_id = c.id
         WHERE f.user_id = ?
         ORDER BY f.created_at DESC
         LIMIT {$perPage} OFFSET {$offset}",
        [(int)$currentUser->id]
    ) ?: []
);

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>⭐ Favoriten</h1>
            <p><?php echo $total; ?> gespeicherte Beiträge</p>
        </div>

        <?php if (!empty($favorites)): ?>
        <div class="member-fav-grid" data-anim data-anim-delay="1">
            <?php foreach ($favorites as $fav): ?>
            <article class="member-fav-card">
                <?php if (!empty($fav['featured_image'])): ?>
                <div class="member-fav-img">
                    <img src="<?php echo htmlspecialchars($fav['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($fav['post_title'] ?? '', ENT_QUOTES); ?>" loading="lazy">
                </div>
                <?php endif; ?>
                <div class="member-fav-body">
                    <?php if (!empty($fav['category_name'])): ?>
                    <span class="member-fav-badge"><?php echo htmlspecialchars($fav['category_name']); ?></span>
                    <?php endif; ?>
                    <h3>
                        <a href="<?php echo htmlspecialchars($siteUrl . '/' . ($fav['post_slug'] ?? ''), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($fav['post_title'] ?? 'Unbekannter Beitrag', ENT_QUOTES); ?>
                        </a>
                    </h3>
                    <?php if (!empty($fav['excerpt'])): ?>
                    <p><?php echo htmlspecialchars(mb_substr(strip_tags($fav['excerpt']), 0, 120)); ?>…</p>
                    <?php endif; ?>
                    <div class="member-fav-meta">
                        <span><?php echo date('d.m.Y', strtotime($fav['post_date'] ?? $fav['created_at'])); ?></span>
                        <form method="POST" class="member-fav-remove" onsubmit="return confirm('Favorit wirklich entfernen?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="favorite_id" value="<?php echo (int)$fav['id']; ?>">
                            <button type="submit" name="remove_favorite" value="1" class="member-fav-remove-btn" title="Entfernen">✕</button>
                        </form>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
        <div class="member-pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="btn btn-sm btn-secondary">← Zurück</a>
            <?php endif; ?>
            <span>Seite <?php echo $page; ?> von <?php echo $pages; ?></span>
            <?php if ($page < $pages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="btn btn-sm btn-secondary">Weiter →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="member-empty-state" data-anim>
            <p class="member-empty-state__icon">📭</p>
            <p><strong>Noch keine Favoriten</strong></p>
            <p>Speichere Beiträge als Favoriten, um sie hier wiederzufinden.</p>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog" class="btn btn-primary member-empty-state__action">📖 Beiträge entdecken</a>
        </div>
        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
