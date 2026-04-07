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
    header('Location: ' . theme_login_url());
    exit;
}

$currentUser = $auth->getCurrentUser();
$db          = \CMS\Database::instance();
$prefix      = $db->getPrefix();
$siteUrl     = SITE_URL;
$activePage  = 'favorites';

$favoriteSections = [
    'posts' => ['label' => 'Beiträge', 'icon' => '📝', 'items' => []],
    'pages' => ['label' => 'Seiten', 'icon' => '📄', 'items' => []],
    'other' => ['label' => 'Sonstiges', 'icon' => '🗂️', 'items' => []],
];

$allowedFavoriteStorage = ['post', 'page'];

// Favorit entfernen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    if (\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_favorites')) {
        $favoriteStorage = trim((string) ($_POST['favorite_storage'] ?? 'post'));
        if (!in_array($favoriteStorage, $allowedFavoriteStorage, true)) {
            $favoriteStorage = 'post';
        }

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
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';

$postFavorites = array_map(
    static function ($row) use ($siteUrl, $currentLocale): array {
        $favorite = (array) $row;
        $featuredImage = phinit_normalize_public_media_url((string) ($favorite['featured_image'] ?? ''), true, $siteUrl);

        $postData = [
            'slug' => (string) ($favorite['post_slug'] ?? ''),
            'slug_en' => (string) ($favorite['post_slug_en'] ?? ''),
            'published_at' => (string) ($favorite['post_published_at'] ?? ''),
            'created_at' => (string) ($favorite['post_created_at'] ?? ''),
        ];

        return [
            'storage' => 'post',
            'id' => (int) ($favorite['id'] ?? 0),
            'content_id' => (int) ($favorite['post_id'] ?? 0),
            'title' => (string) ($favorite['post_title'] ?? 'Unbekannter Beitrag'),
            'url' => function_exists('phinit_build_post_url') ? phinit_build_post_url($postData, $currentLocale) : ($siteUrl . '/blog/' . rawurlencode((string) ($favorite['post_slug'] ?? ''))),
            'excerpt' => trim((string) ($favorite['excerpt'] ?? '')),
            'featured_image' => $featuredImage,
            'badge' => (string) ($favorite['category_name'] ?? 'Beitrag'),
            'created_at' => (string) ($favorite['created_at'] ?? ''),
            'section' => 'posts',
            'type_label' => 'Beitrag',
        ];
    },
    $db->get_results(
        "SELECT f.*, p.title AS post_title, p.slug AS post_slug, p.slug_en AS post_slug_en, p.excerpt, p.featured_image,
            p.published_at AS post_published_at, p.created_at AS post_created_at, c.name AS category_name
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
        'url' => phinit_safe_public_url((string) ($favorite['url'] ?? ''), $siteUrl, ['http', 'https']) ?: '#',
        'excerpt' => (string) ($favorite['excerpt'] ?? ''),
        'featured_image' => phinit_normalize_public_media_url((string) ($favorite['featured_image'] ?? ''), true, $siteUrl),
        'badge' => (string) ($favorite['badge'] ?? 'Seite'),
        'created_at' => (string) ($favorite['created_at'] ?? ''),
        'section' => 'pages',
        'type_label' => 'Seite',
    ],
    phinit_get_page_favorites_for_user((int) $currentUser->id)
);

$allFavorites = array_merge($postFavorites, $pageFavorites);
usort($allFavorites, static function (array $a, array $b): int {
    return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
});

$total = count($allFavorites);

foreach ($allFavorites as $favorite) {
    $sectionKey = (string) ($favorite['section'] ?? 'other');
    if (!isset($favoriteSections[$sectionKey])) {
        $sectionKey = 'other';
    }

    $favoriteSections[$sectionKey]['items'][] = $favorite;
}

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>⭐ Favoriten</h1>
            <p><?php echo (int) $total; ?> gespeicherte Einträge – aufgeteilt in Beiträge, Seiten und sonstige Merkliste.</p>
        </div>

        <div class="member-dashboard-overview member-dashboard-overview--analytics" data-anim data-anim-delay=".5">
            <?php foreach ($favoriteSections as $section): ?>
            <div class="member-overview-card">
                <span class="member-overview-card__icon"><?php echo htmlspecialchars((string) ($section['icon'] ?? '⭐'), ENT_QUOTES); ?></span>
                <strong><?php echo count((array) ($section['items'] ?? [])); ?></strong>
                <span><?php echo htmlspecialchars((string) ($section['label'] ?? 'Favoriten'), ENT_QUOTES); ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total > 0): ?>
            <?php $sectionDelay = 1; ?>
            <?php foreach ($favoriteSections as $section): ?>
            <section class="member-card member-card--spaced" data-anim data-anim-delay="<?php echo htmlspecialchars((string) $sectionDelay, ENT_QUOTES); ?>">
                <div class="member-card-header member-card-header--stacked">
                    <div>
                        <h3><?php echo htmlspecialchars((string) ($section['icon'] ?? '⭐') . ' ' . ($section['label'] ?? 'Favoriten'), ENT_QUOTES); ?></h3>
                        <p class="member-card-subtitle"><?php echo count((array) ($section['items'] ?? [])); ?> Einträge</p>
                    </div>
                </div>

                <?php if (!empty($section['items'])): ?>
                <div class="member-fav-list member-fav-list--section">
                    <?php foreach ($section['items'] as $fav): ?>
                    <article class="member-fav-card member-fav-card--list">
                        <?php if (!empty($fav['featured_image'])): ?>
                        <div class="member-fav-img member-fav-img--list">
                            <img src="<?php echo htmlspecialchars((string) $fav['featured_image'], ENT_QUOTES); ?>"
                                 alt="<?php echo htmlspecialchars((string) ($fav['title'] ?? ''), ENT_QUOTES); ?>" <?php echo phinit_image_loading_attributes(); ?>>
                        </div>
                        <?php endif; ?>
                        <div class="member-fav-body member-fav-body--list">
                            <div class="member-fav-badges">
                                <span class="member-fav-badge"><?php echo htmlspecialchars((string) ($fav['type_label'] ?? 'Favorit'), ENT_QUOTES); ?></span>
                                <?php if (!empty($fav['badge'])): ?>
                                <span class="member-fav-badge member-fav-badge--muted"><?php echo htmlspecialchars((string) $fav['badge'], ENT_QUOTES); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3>
                                <a href="<?php echo htmlspecialchars((string) ($fav['url'] ?? '#'), ENT_QUOTES); ?>">
                                    <?php echo htmlspecialchars((string) ($fav['title'] ?? 'Unbekannter Favorit'), ENT_QUOTES); ?>
                                </a>
                            </h3>
                            <?php if (!empty($fav['excerpt'])): ?>
                            <p><?php echo htmlspecialchars(mb_substr(strip_tags((string) $fav['excerpt']), 0, 140), ENT_QUOTES); ?>…</p>
                            <?php else: ?>
                            <p>Gespeichert, damit du später blitzschnell wieder hier landest.</p>
                            <?php endif; ?>
                            <div class="member-fav-meta">
                                <?php $favoriteTimestamp = strtotime((string) ($fav['created_at'] ?? '')); ?>
                                <span><?php echo htmlspecialchars($favoriteTimestamp !== false ? date('d.m.Y', $favoriteTimestamp) : '—', ENT_QUOTES); ?></span>
                                <a href="<?php echo htmlspecialchars((string) ($fav['url'] ?? '#'), ENT_QUOTES); ?>" class="member-fav-open-link">Öffnen →</a>
                                <form method="post" class="member-fav-remove" onsubmit="return confirm('Favorit wirklich entfernen?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                                    <input type="hidden" name="favorite_id" value="<?php echo (int) ($fav['id'] ?? 0); ?>">
                                    <input type="hidden" name="favorite_storage" value="<?php echo htmlspecialchars((string) ($fav['storage'] ?? 'post'), ENT_QUOTES); ?>">
                                    <input type="hidden" name="favorite_content_id" value="<?php echo (int) ($fav['content_id'] ?? 0); ?>">
                                    <button type="submit" name="remove_favorite" value="1" class="member-fav-remove-btn" title="Entfernen">✕</button>
                                </form>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="member-empty member-empty--soft">
                    <p>Noch keine Einträge in „<?php echo htmlspecialchars((string) ($section['label'] ?? 'Favoriten'), ENT_QUOTES); ?>“ gespeichert.</p>
                </div>
                <?php endif; ?>
            </section>
            <?php $sectionDelay++; ?>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="member-empty-state" data-anim>
            <p class="member-empty-state__icon">📭</p>
            <p><strong>Noch keine Favoriten</strong></p>
            <p>Speichere Beiträge oder Seiten als Favoriten, um sie hier wiederzufinden.</p>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog" class="btn btn-primary member-empty-state__action">📖 Beiträge entdecken</a>
        </div>
        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
