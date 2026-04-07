<?php
/**
 * Member Kommentare – CMS Phinit Theme
 *
 * Alle Kommentare des eingeloggten Benutzers gesammelt anzeigen.
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
$activePage  = 'comments';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';

$formatCommentDate = static function (?string $value, string $format = 'd.m.Y H:i') : string {
    $timestamp = strtotime((string) $value);

    return $timestamp !== false ? date($format, $timestamp) : '—';
};

$buildCommentPostUrl = static function (array $comment) use ($currentLocale, $siteUrl): string {
    $postData = [
        'slug' => (string) ($comment['post_slug'] ?? ''),
        'slug_en' => (string) ($comment['post_slug_en'] ?? ''),
        'published_at' => (string) ($comment['post_published_at'] ?? ''),
        'created_at' => (string) ($comment['post_created_at'] ?? ''),
    ];

    $url = function_exists('phinit_build_post_url')
        ? phinit_build_post_url($postData, $currentLocale)
        : ('/blog/' . rawurlencode((string) ($comment['post_slug'] ?? '')));

    if (function_exists('phinit_safe_public_url')) {
        return phinit_safe_public_url($url, $siteUrl, ['http', 'https']) ?: '#';
    }

    return $url !== '' ? $url : '#';
};

// Filter
$filter = (string) ($_GET['filter'] ?? 'all'); // all, approved, pending
$allowedFilters = ['all', 'approved', 'pending'];
if (!in_array($filter, $allowedFilters, true)) {
    $filter = 'all';
}
$filterSQL = '';
if ($filter === 'approved') {
    $filterSQL = "AND c.status = 'approved'";
} elseif ($filter === 'pending') {
    $filterSQL = "AND c.status = 'pending'";
}

// Paginierung
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;
$commentsBasePath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ($siteUrl . '/member/comments'));
$buildCommentsUrl = static function (array $params) use ($commentsBasePath): string {
    $query = http_build_query($params);

    return $query !== '' ? $commentsBasePath . '?' . $query : $commentsBasePath;
};

$total = (int)$db->get_var(
    "SELECT COUNT(*) FROM {$prefix}comments c WHERE c.user_id = ? {$filterSQL}",
    [(int)$currentUser->id]
) ?: 0;

$pages = (int)ceil($total / $perPage);

$comments = array_map(
    fn($r) => (array)$r,
    $db->get_results(
        "SELECT c.*, p.title AS post_title, p.slug AS post_slug, p.slug_en AS post_slug_en, p.published_at AS post_published_at, p.created_at AS post_created_at
         FROM {$prefix}comments c
         LEFT JOIN {$prefix}posts p ON c.post_id = p.id
         WHERE c.user_id = ? {$filterSQL}
         ORDER BY c.post_date DESC
         LIMIT {$perPage} OFFSET {$offset}",
        [(int)$currentUser->id]
    ) ?: []
);

// Zähler pro Status
$countAll      = (int)$db->get_var("SELECT COUNT(*) FROM {$prefix}comments WHERE user_id = ?", [(int)$currentUser->id]) ?: 0;
$countApproved = (int)$db->get_var("SELECT COUNT(*) FROM {$prefix}comments WHERE user_id = ? AND status = 'approved'", [(int)$currentUser->id]) ?: 0;
$countPending  = (int)$db->get_var("SELECT COUNT(*) FROM {$prefix}comments WHERE user_id = ? AND status = 'pending'", [(int)$currentUser->id]) ?: 0;

$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main">

        <div class="member-page-title" data-anim>
            <h1>💬 Meine Kommentare</h1>
            <p><?php echo (int) $countAll; ?> Kommentare insgesamt</p>
        </div>

        <!-- Filter-Tabs -->
        <div class="member-filter-tabs" data-anim data-anim-delay="1">
            <a href="<?php echo htmlspecialchars($buildCommentsUrl(['filter' => 'all']), ENT_QUOTES, 'UTF-8'); ?>" class="member-filter-tab<?php echo $filter === 'all' ? ' active' : ''; ?>">Alle (<?php echo (int) $countAll; ?>)</a>
            <a href="<?php echo htmlspecialchars($buildCommentsUrl(['filter' => 'approved']), ENT_QUOTES, 'UTF-8'); ?>" class="member-filter-tab<?php echo $filter === 'approved' ? ' active' : ''; ?>">Veröffentlicht (<?php echo (int) $countApproved; ?>)</a>
            <a href="<?php echo htmlspecialchars($buildCommentsUrl(['filter' => 'pending']), ENT_QUOTES, 'UTF-8'); ?>" class="member-filter-tab<?php echo $filter === 'pending' ? ' active' : ''; ?>">Ausstehend (<?php echo (int) $countPending; ?>)</a>
        </div>

        <?php if (!empty($comments)): ?>
        <div class="member-comments-list" data-anim data-anim-delay="2">
            <?php foreach ($comments as $c): ?>
            <div class="member-comment-item">
                <div class="member-comment-header">
                    <a href="<?php echo htmlspecialchars($buildCommentPostUrl($c), ENT_QUOTES); ?>" class="member-comment-post-link">
                        📄 <?php echo htmlspecialchars($c['post_title'] ?? 'Beitrag', ENT_QUOTES); ?>
                    </a>
                    <div class="member-comment-meta">
                        <span class="member-comment-status member-comment-status--<?php echo htmlspecialchars($c['status'] ?? 'pending'); ?>">
                            <?php echo ($c['status'] ?? 'pending') === 'approved' ? '✅ Veröffentlicht' : '⏳ Ausstehend'; ?>
                        </span>
                        <span class="member-comment-date"><?php echo htmlspecialchars($formatCommentDate((string) ($c['post_date'] ?? '')), ENT_QUOTES); ?></span>
                    </div>
                </div>
                <div class="member-comment-body">
                    <?php echo nl2br(htmlspecialchars(mb_substr($c['content'] ?? '', 0, 300))); ?>
                    <?php if (mb_strlen($c['content'] ?? '') > 300): ?>
                    <span class="member-comment-more">…</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
        <div class="member-pagination">
            <?php if ($page > 1): ?>
            <a href="<?php echo htmlspecialchars($buildCommentsUrl(['filter' => $filter, 'page' => $page - 1]), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-secondary">← Zurück</a>
            <?php endif; ?>
            <span>Seite <?php echo (int) $page; ?> von <?php echo (int) $pages; ?></span>
            <?php if ($page < $pages): ?>
            <a href="<?php echo htmlspecialchars($buildCommentsUrl(['filter' => $filter, 'page' => $page + 1]), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-secondary">Weiter →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="member-empty-state" data-anim>
            <p class="member-empty-state__icon">📭</p>
            <p><strong>Noch keine Kommentare</strong></p>
            <p>Teile deine Gedanken unter Beiträgen, um sie hier gesammelt einzusehen.</p>
        </div>
        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
