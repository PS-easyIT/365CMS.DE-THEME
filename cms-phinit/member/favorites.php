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
$pageFavorites = [];

// Favorit entfernen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    if (\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_favorites')) {
        $favoriteStorage = trim((string) ($_POST['favorite_storage'] ?? 'post'));
        $favId = (int)($_POST['favorite_id'] ?? 0);
        if ($favoriteStorage === 'page') {
            $pageFavoriteId = (int) ($_POST['favorite_content_id'] ?? 0);
            if ($pageFavoriteId > 0) {
                $pageFavorites = array_values(array_filter(
                    phinit_get_page_favorites_for_user((int) $currentUser->id),
                    static fn(array $favorite): bool => (int) ($favorite['content_id'] ?? 0) !== $pageFavoriteId
                ));
                phinit_store_page_favorites_for_user((int) $currentUser->id, $pageFavorites);
            }
        } elseif ($favId > 0) {
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

$postFavorites = array_map(
    static function ($row) use ($siteUrl): array {
        $favorite = (array) $row;

        return [
            'storage' => 'post',
            'id' => (int) ($favorite['id'] ?? 0),
            'content_id' => (int) ($favorite['post_id'] ?? 0),
            'title' => (string) ($favorite['post_title'] ?? 'Unbekannter Beitrag'),
            'url' => $siteUrl . '/blog/' . rawurlencode((string) ($favorite['post_slug'] ?? '')),
            'excerpt' => trim((string) ($favorite['excerpt'] ?? '')),
            'featured_image' => (string) ($favorite['featured_image'] ?? ''),
            'badge' => (string) ($favorite['category_name'] ?? 'Beitrag'),
            'created_at' => (string) ($favorite['created_at'] ?? ''),
        ];
    },
    $db->get_results(
        "SELECT f.*, p.title AS post_title, p.slug AS post_slug, p.excerpt, p.featured_image,
                p.created_at AS post_date, c.name AS category_name
         FROM {$prefix}favorites f
         LEFT JOIN {$prefix}posts p ON f.post_id = p.id
         LEFT JOIN {$prefix}post_categories c ON p.category_id = c.id
         WHERE f.user_id = ?
         ORDER BY f.created_at DESC",
        [(int)$currentUser->id]
    ) ?: []
);

$pageFavorites = array_map(
    static fn(array $favorite): array => [
        'storage' => 'page',
        'id' => 0,
        'content_id' => (int) ($favorite['content_id'] ?? 0),
        'title' => (string) ($favorite['title'] ?? 'Seite'),
        'url' => (string) ($favorite['url'] ?? '#'),
        'excerpt' => (string) ($favorite['excerpt'] ?? ''),
        'featured_image' => (string) ($favorite['featured_image'] ?? ''),
        'badge' => (string) ($favorite['badge'] ?? 'Seite'),
        'created_at' => (string) ($favorite['created_at'] ?? ''),
    ],
    phinit_get_page_favorites_for_user((int) $currentUser->id)
);

$allFavorites = array_merge($postFavorites, $pageFavorites);
usort($allFavorites, static function (array $a, array $b): int {
    return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
});

$total = count($allFavorites);
$pages = max(1, (int) ceil($total / $perPage));
$favorites = array_slice($allFavorites, $offset, $perPage);

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>⭐ Favoriten</h1>
            <p><?php echo $total; ?> gespeicherte Einträge</p>
        </div>

        <?php if (!empty($favorites)): ?>
        <div class="member-fav-grid" data-anim data-anim-delay="1">
            <?php foreach ($favorites as $fav): ?>
            <article class="member-fav-card">
                <?php if (!empty($fav['featured_image'])): ?>
                <div class="member-fav-img">
                    <img src="<?php echo htmlspecialchars($fav['featured_image'], ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($fav['title'] ?? '', ENT_QUOTES); ?>" <?php echo phinit_image_loading_attributes(); ?>>
                </div>
                <?php endif; ?>
                <div class="member-fav-body">
                    <?php if (!empty($fav['badge'])): ?>
                    <span class="member-fav-badge"><?php echo htmlspecialchars($fav['badge']); ?></span>
                    <?php endif; ?>
                    <h3>
                        <a href="<?php echo htmlspecialchars((string) ($fav['url'] ?? '#'), ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars($fav['title'] ?? 'Unbekannter Favorit', ENT_QUOTES); ?>
                        </a>
                    </h3>
                    <?php if (!empty($fav['excerpt'])): ?>
                    <p><?php echo htmlspecialchars(mb_substr(strip_tags($fav['excerpt']), 0, 120)); ?>…</p>
                    <?php endif; ?>
                    <div class="member-fav-meta">
                        <span><?php echo date('d.m.Y', strtotime((string) ($fav['created_at'] ?? 'now'))); ?></span>
                        <form method="POST" class="member-fav-remove" onsubmit="return confirm('Favorit wirklich entfernen?');">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                            <input type="hidden" name="favorite_id" value="<?php echo (int)$fav['id']; ?>">
                            <input type="hidden" name="favorite_storage" value="<?php echo htmlspecialchars((string) ($fav['storage'] ?? 'post'), ENT_QUOTES); ?>">
                            <input type="hidden" name="favorite_content_id" value="<?php echo (int) ($fav['content_id'] ?? 0); ?>">
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
