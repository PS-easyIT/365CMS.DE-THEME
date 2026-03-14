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

use CMS\Services\ThemeCustomizer;

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
$themeCustomizer = ThemeCustomizer::instance();

$getMemberToggle = static function (string $key, bool $default = true) use ($themeCustomizer): bool {
    return filter_var($themeCustomizer->get('memberdashboard', $key, $default), FILTER_VALIDATE_BOOLEAN);
};

$getMemberText = static function (string $key, string $default = '') use ($themeCustomizer): string {
    return trim((string) $themeCustomizer->get('memberdashboard', $key, $default));
};

$getMemberList = static function (string $key, string $default = '') use ($themeCustomizer): array {
    $raw = (string) $themeCustomizer->get('memberdashboard', $key, $default);
    $normalized = str_replace(["\r\n", "\r", ';', '|'], [",", ",", ",", ","], strtolower($raw));

    return array_values(array_filter(array_map(
        static fn (string $value): string => trim($value),
        explode(',', $normalized)
    )));
};

$normalizeMemberLink = static function (string $url) use ($siteUrl): string {
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $url) === 1) {
        return $url;
    }

    if (str_starts_with($url, '/')) {
        return $siteUrl . $url;
    }

    return $siteUrl . '/' . ltrim($url, '/');
};

$sanitizeColor = static function (string $value, string $fallback): string {
    return preg_match('/^#[0-9a-f]{6}$/i', $value) ? $value : $fallback;
};

$hexToRgba = static function (string $hexColor, float $alpha, string $fallback): string {
    if (preg_match('/^#([0-9a-f]{6})$/i', $hexColor, $matches) !== 1) {
        return $fallback;
    }

    $hex = $matches[1];
    $red = hexdec(substr($hex, 0, 2));
    $green = hexdec(substr($hex, 2, 2));
    $blue = hexdec(substr($hex, 4, 2));

    return sprintf('rgba(%d, %d, %d, %.2f)', $red, $green, $blue, $alpha);
};

$sortByConfiguredOrder = static function (array $items, string $order): array {
    $sequence = array_values(array_filter(array_map(static fn (string $slug): string => trim($slug), explode(',', strtolower($order)))));
    if ($sequence === []) {
        return $items;
    }

    $positions = array_flip($sequence);
    uasort($items, static function (array $left, array $right) use ($positions): int {
        $leftSlug = (string) ($left['slug'] ?? '');
        $rightSlug = (string) ($right['slug'] ?? '');
        $leftPos = $positions[$leftSlug] ?? 999;
        $rightPos = $positions[$rightSlug] ?? 999;

        if ($leftPos === $rightPos) {
            return $leftSlug <=> $rightSlug;
        }

        return $leftPos <=> $rightPos;
    });

    return $items;
};

$pageFavorites = phinit_get_page_favorites_for_user((int) $currentUser->id);
$memberService = \CMS\Services\MemberService::getInstance();

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

$notificationItems = [];
try {
    $notificationItems = $memberService->getRecentNotifications((int) $currentUser->id, 4);
} catch (\Throwable $e) {}

$notificationCount = count($notificationItems);

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
$notificationsUrl = htmlspecialchars($siteUrl, ENT_QUOTES) . '/member/notifications';

$isAdmin    = $auth->isAdmin();

$showAdminNotice = $getMemberToggle('show_admin_notice', false);
$adminNoticeRoles = $getMemberList('admin_notice_roles', 'all');
$adminNoticeVariant = strtolower($getMemberText('admin_notice_variant', 'info'));
$adminNoticeVariant = in_array($adminNoticeVariant, ['info', 'warning', 'success'], true) ? $adminNoticeVariant : 'info';
$adminNoticeKicker = $getMemberText('admin_notice_kicker', '📣 Admin-Benachrichtigung');
$adminNoticeTitle = $getMemberText('admin_notice_title', 'Wichtiger Hinweis für Mitglieder');
$adminNoticeText = $getMemberText('admin_notice_text', 'Hier kannst du wichtige Hinweise, Wartungsfenster oder kurze Updates für dein Memberdashboard platzieren.');
$adminNoticeLinkLabel = $getMemberText('admin_notice_link_label', 'Mehr erfahren');
$adminNoticeLinkUrl = $normalizeMemberLink($getMemberText('admin_notice_link_url', ''));
$adminNoticeRole = strtolower(trim((string) ($currentUser->role ?? 'member')));
$adminNoticeAllowedRoles = $adminNoticeRoles === [] ? ['all'] : $adminNoticeRoles;
$adminNoticeRoleAllowed = in_array('all', $adminNoticeAllowedRoles, true) || in_array($adminNoticeRole, $adminNoticeAllowedRoles, true);

$adminNoticeDefaults = [
    'info' => ['icon' => 'ℹ️', 'accent' => '#2563eb', 'background' => '#eff6ff'],
    'warning' => ['icon' => '⚠️', 'accent' => '#d97706', 'background' => '#fffbeb'],
    'success' => ['icon' => '✅', 'accent' => '#059669', 'background' => '#ecfdf5'],
];

$adminNoticeConfig = $adminNoticeDefaults[$adminNoticeVariant];
$adminNoticeAccentColor = $sanitizeColor($getMemberText('admin_notice_accent_color', $adminNoticeConfig['accent']), $adminNoticeConfig['accent']);
$adminNoticeBackgroundColor = $sanitizeColor($getMemberText('admin_notice_background_color', $adminNoticeConfig['background']), $adminNoticeConfig['background']);
$adminNoticeBorderColor = $hexToRgba($adminNoticeAccentColor, 0.28, 'rgba(37, 99, 235, 0.28)');
$adminNoticeSoftColor = $hexToRgba($adminNoticeAccentColor, 0.10, 'rgba(37, 99, 235, 0.10)');
$adminNoticeBadgeColor = $hexToRgba($adminNoticeAccentColor, 0.14, 'rgba(37, 99, 235, 0.14)');
$adminNoticeStyle = implode(' ', [
    '--member-admin-notice-accent: ' . $adminNoticeAccentColor . ';',
    '--member-admin-notice-background: ' . $adminNoticeBackgroundColor . ';',
    '--member-admin-notice-border: ' . $adminNoticeBorderColor . ';',
    '--member-admin-notice-soft: ' . $adminNoticeSoftColor . ';',
    '--member-admin-notice-badge-bg: ' . $adminNoticeBadgeColor . ';',
]);
$hasAdminNotice = $showAdminNotice && $adminNoticeRoleAllowed && ($adminNoticeTitle !== '' || $adminNoticeText !== '');

$showWelcome = $getMemberToggle('show_welcome', true);
$welcomeEyebrow = $getMemberText('welcome_eyebrow', '🏠 Member Home');
$welcomeTitleTemplate = $getMemberText('welcome_title', '{greeting}, {name}! 👋');
$welcomeText = $getMemberText('welcome_text', 'Dein persönlicher Startbereich mit den wichtigsten Inhalten, Sicherheitsinfos und schnellen Sprüngen zu deinen häufigsten Aufgaben.');
$heroPanelTitle = $getMemberText('hero_panel_title', 'Kontostatus');
$showHeroFavorites = $getMemberToggle('show_hero_favorites', true);
$showHeroProfile = $getMemberToggle('show_hero_profile', true);
$showHeroSecurity = $getMemberToggle('show_hero_security', true);
$showHeroNotifications = $getMemberToggle('show_hero_notifications', true);
$showHeroComments = $getMemberToggle('show_hero_comments', true);
$showHeroAnalytics = $getMemberToggle('show_hero_analytics', true);
$showCardFavorites = $getMemberToggle('show_card_favorites', true);
$showCardComments = $getMemberToggle('show_card_comments', true);
$showCardNotifications = $getMemberToggle('show_card_notifications', true);
$showCardPosts = $getMemberToggle('show_card_posts', true);
$showCardSecurity = $getMemberToggle('show_card_security', true);
$showRecentComments = $getMemberToggle('show_recent_comments', true);
$showRecentFavorites = $getMemberToggle('show_recent_favorites', true);
$showRecentNotifications = $getMemberToggle('show_recent_notifications', true);
$showQuicklinks = $getMemberToggle('show_quicklinks', true);
$showQuicklinkProfile = $getMemberToggle('show_quicklink_profile', true);
$showQuicklinkNotifications = $getMemberToggle('show_quicklink_notifications', true);
$showQuicklinkNewsletter = $getMemberToggle('show_quicklink_newsletter', true);
$showQuicklinkFeeds = $getMemberToggle('show_quicklink_feeds', true);
$showQuicklinkForum = $getMemberToggle('show_quicklink_forum', true);

$recentCommentsTitle = $getMemberText('recent_comments_title', 'Letzte Kommentare');
$recentCommentsIcon = $getMemberText('recent_comments_icon', '💬');
$recentFavoritesTitle = $getMemberText('recent_favorites_title', 'Letzte Favoriten');
$recentFavoritesIcon = $getMemberText('recent_favorites_icon', '⭐');
$recentNotificationsTitle = $getMemberText('recent_notifications_title', 'Letzte Benachrichtigungen');
$recentNotificationsIcon = $getMemberText('recent_notifications_icon', '🔔');
$quicklinksTitle = $getMemberText('quicklinks_title', 'Schnellzugriff');
$quicklinksIcon = $getMemberText('quicklinks_icon', '🚀');

$heroColorStart = $sanitizeColor($getMemberText('hero_color_start', '#0f2240'), '#0f2240');
$heroColorEnd = $sanitizeColor($getMemberText('hero_color_end', '#2563eb'), '#2563eb');
$cardIconColor = $sanitizeColor($getMemberText('card_icon_color', '#0d9488'), '#0d9488');
$cardHoverColor = $sanitizeColor($getMemberText('card_hover_color', '#0d9488'), '#0d9488');
$quicklinkIconColor = $sanitizeColor($getMemberText('quicklink_icon_color', '#1e3a5f'), '#1e3a5f');

$memberName = trim((string) (explode(' ', (string) ($currentUser->username ?? 'Mitglied'))[0] ?? 'Mitglied'));
$welcomeTitle = strtr($welcomeTitleTemplate, [
    '{greeting}' => $greeting,
    '{name}' => $memberName,
]);

$dashboardStyle = implode(' ', [
    '--member-dashboard-hero-start: ' . $heroColorStart . ';',
    '--member-dashboard-hero-end: ' . $heroColorEnd . ';',
    '--member-dashboard-card-icon: ' . $cardIconColor . ';',
    '--member-dashboard-card-hover: ' . $cardHoverColor . ';',
    '--member-dashboard-quicklink-icon: ' . $quicklinkIconColor . ';',
]);

$overviewCards = [
    'favorites' => ['slug' => 'favorites', 'enabled' => $showCardFavorites, 'icon' => $getMemberText('card_favorites_icon', '⭐'), 'title' => $getMemberText('card_favorites_title', 'Gespeicherte Favoriten'), 'value' => (string) $favCount, 'url' => $favoriteUrl, 'class' => 'member-overview-card--favorite'],
    'comments' => ['slug' => 'comments', 'enabled' => $showCardComments, 'icon' => $getMemberText('card_comments_icon', '💬'), 'title' => $getMemberText('card_comments_title', 'Eigene Kommentare'), 'value' => (string) $commentCount, 'url' => $commentsUrl, 'class' => 'member-overview-card--comment'],
    'notifications' => ['slug' => 'notifications', 'enabled' => $showCardNotifications, 'icon' => $getMemberText('card_notifications_icon', '🔔'), 'title' => $getMemberText('card_notifications_title', 'Letzte Benachrichtigungen'), 'value' => (string) $notificationCount, 'url' => $notificationsUrl, 'class' => 'member-overview-card--notification'],
    'posts' => ['slug' => 'posts', 'enabled' => $showCardPosts, 'icon' => $getMemberText('card_posts_icon', '📝'), 'title' => $getMemberText('card_posts_title', 'Veröffentlichte Beiträge'), 'value' => (string) $postCount, 'url' => '', 'class' => 'member-overview-card--post'],
    'security' => ['slug' => 'security', 'enabled' => $showCardSecurity, 'icon' => $getMemberText('card_security_icon', '🔒'), 'title' => $getMemberText('card_security_title', 'Sicherheitsbereich öffnen'), 'value' => $isAdmin ? 'Admin' : 'Aktiv', 'url' => $securityUrl, 'class' => 'member-overview-card--security'],
];

$overviewCards = array_filter($overviewCards, static fn (array $card): bool => (bool) ($card['enabled'] ?? false));
$overviewCards = $sortByConfiguredOrder($overviewCards, $getMemberText('overview_card_order', 'favorites,comments,notifications,posts,security'));

// Theme Header
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main member-main--dashboard" style="<?php echo htmlspecialchars($dashboardStyle, ENT_QUOTES); ?>">

        <?php if ($hasAdminNotice): ?>
        <section class="member-admin-notice member-admin-notice--<?php echo htmlspecialchars($adminNoticeVariant, ENT_QUOTES); ?>" data-anim style="<?php echo htmlspecialchars($adminNoticeStyle, ENT_QUOTES); ?>">
            <span class="member-admin-notice__icon" aria-hidden="true"><?php echo htmlspecialchars((string) $adminNoticeConfig['icon'], ENT_QUOTES); ?></span>
            <div class="member-admin-notice__content">
                <?php if ($adminNoticeKicker !== ''): ?>
                <span class="member-admin-notice__eyebrow"><?php echo htmlspecialchars($adminNoticeKicker, ENT_QUOTES); ?></span>
                <?php endif; ?>
                <?php if ($adminNoticeTitle !== ''): ?>
                <h2><?php echo htmlspecialchars($adminNoticeTitle, ENT_QUOTES); ?></h2>
                <?php endif; ?>
                <?php if ($adminNoticeText !== ''): ?>
                <p><?php echo htmlspecialchars($adminNoticeText, ENT_QUOTES); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($adminNoticeLinkUrl !== '' && $adminNoticeLinkLabel !== ''): ?>
            <a href="<?php echo htmlspecialchars($adminNoticeLinkUrl, ENT_QUOTES); ?>" class="member-admin-notice__action"><?php echo htmlspecialchars($adminNoticeLinkLabel, ENT_QUOTES); ?></a>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <?php if ($showWelcome): ?>
        <section class="member-dashboard-hero" data-anim>
            <div class="member-dashboard-hero__content">
                <span class="member-dashboard-hero__eyebrow"><?php echo htmlspecialchars($welcomeEyebrow !== '' ? $welcomeEyebrow : '🏠 Member Home', ENT_QUOTES); ?></span>
                <h1><?php echo htmlspecialchars($welcomeTitle !== '' ? $welcomeTitle : ($greeting . ', ' . $memberName . '! 👋'), ENT_QUOTES); ?></h1>
                <p><?php echo htmlspecialchars($welcomeText !== '' ? $welcomeText : 'Dein persönlicher Startbereich mit den wichtigsten Inhalten, Sicherheitsinfos und schnellen Sprüngen zu deinen häufigsten Aufgaben.', ENT_QUOTES); ?></p>

                <div class="member-dashboard-hero__actions">
                    <?php if ($showHeroFavorites): ?>
                    <a href="<?php echo $favoriteUrl; ?>" class="member-hero-action">⭐ Favoriten</a>
                    <?php endif; ?>
                    <?php if ($showHeroProfile): ?>
                    <a href="<?php echo $profileUrl; ?>" class="member-hero-action">👤 Profil</a>
                    <?php endif; ?>
                    <?php if ($showHeroSecurity): ?>
                    <a href="<?php echo $securityUrl; ?>" class="member-hero-action">🔒 Sicherheit</a>
                    <?php endif; ?>
                    <?php if ($showHeroNotifications): ?>
                    <a href="<?php echo $notificationsUrl; ?>" class="member-hero-action">🔔 Benachrichtigungen</a>
                    <?php endif; ?>
                    <?php if ($showHeroComments): ?>
                    <a href="<?php echo $commentsUrl; ?>" class="member-hero-action">💬 Kommentare</a>
                    <?php endif; ?>
                    <?php if ($isAdmin && $showHeroAnalytics): ?>
                    <a href="<?php echo $analyticsUrl; ?>" class="member-hero-action">📈 Analytics</a>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="member-dashboard-hero__panel">
                <h3><?php echo htmlspecialchars($heroPanelTitle !== '' ? $heroPanelTitle : 'Kontostatus', ENT_QUOTES); ?></h3>
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
        <?php endif; ?>

        <?php if ($overviewCards !== []): ?>
        <div class="member-dashboard-overview" data-anim data-anim-delay="1">
            <?php foreach ($overviewCards as $card): ?>
            <?php if ($card['url'] !== ''): ?>
            <a href="<?php echo htmlspecialchars((string) $card['url'], ENT_QUOTES); ?>" class="member-overview-card <?php echo htmlspecialchars((string) $card['class'], ENT_QUOTES); ?>">
            <?php else: ?>
            <div class="member-overview-card <?php echo htmlspecialchars((string) $card['class'], ENT_QUOTES); ?>">
            <?php endif; ?>
                <span class="member-overview-card__icon"><?php echo htmlspecialchars((string) ($card['icon'] ?? ''), ENT_QUOTES); ?></span>
                <strong><?php echo htmlspecialchars((string) ($card['value'] ?? ''), ENT_QUOTES); ?></strong>
                <span><?php echo htmlspecialchars((string) ($card['title'] ?? ''), ENT_QUOTES); ?></span>
            <?php if ($card['url'] !== ''): ?>
            </a>
            <?php else: ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- 2-Column Grid -->
        <?php if ($showRecentComments || $showRecentFavorites || $showRecentNotifications): ?>
        <div class="member-grid-2 member-grid-2--dashboard" data-anim data-anim-delay="2">

            <!-- Letzte Kommentare -->
            <?php if ($showRecentComments): ?>
            <div class="member-card">
                <div class="member-card-header">
                    <h3><?php echo htmlspecialchars(trim(($recentCommentsIcon !== '' ? $recentCommentsIcon . ' ' : '') . ($recentCommentsTitle !== '' ? $recentCommentsTitle : 'Letzte Kommentare')), ENT_QUOTES); ?></h3>
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
            <?php endif; ?>

            <!-- Letzte Favoriten -->
            <?php if ($showRecentFavorites): ?>
            <div class="member-card">
                <div class="member-card-header">
                    <h3><?php echo htmlspecialchars(trim(($recentFavoritesIcon !== '' ? $recentFavoritesIcon . ' ' : '') . ($recentFavoritesTitle !== '' ? $recentFavoritesTitle : 'Letzte Favoriten')), ENT_QUOTES); ?></h3>
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
            <?php endif; ?>

            <?php if ($showRecentNotifications): ?>
            <div class="member-card">
                <div class="member-card-header">
                    <h3><?php echo htmlspecialchars(trim(($recentNotificationsIcon !== '' ? $recentNotificationsIcon . ' ' : '') . ($recentNotificationsTitle !== '' ? $recentNotificationsTitle : 'Letzte Benachrichtigungen')), ENT_QUOTES); ?></h3>
                    <a href="<?php echo $notificationsUrl; ?>" class="member-card-link">Alle →</a>
                </div>
                <?php if (!empty($notificationItems)): ?>
                <ul class="member-activity-list member-activity-list--stacked">
                    <?php foreach ($notificationItems as $notification): ?>
                    <li>
                        <div>
                            <strong><?php echo htmlspecialchars((string) ($notification->title ?? $notification->message ?? 'Benachrichtigung'), ENT_QUOTES); ?></strong>
                            <?php if (!empty($notification->message) && !empty($notification->title)): ?>
                            <span class="member-activity-copy"><?php echo htmlspecialchars((string) $notification->message, ENT_QUOTES); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="member-activity-date"><?php echo htmlspecialchars((string) ($notification->created_at ?? ''), ENT_QUOTES); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="member-empty">
                    <p>📭 Keine neuen Benachrichtigungen.</p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div><!-- /.member-grid-2 -->
        <?php endif; ?>

        <!-- Schnellzugriff -->
        <?php if ($showQuicklinks): ?>
        <div class="member-quicklinks member-quicklinks--dashboard" data-anim data-anim-delay="3">
            <h3><?php echo htmlspecialchars(trim(($quicklinksIcon !== '' ? $quicklinksIcon . ' ' : '') . ($quicklinksTitle !== '' ? $quicklinksTitle : 'Schnellzugriff')), ENT_QUOTES); ?></h3>
            <div class="member-quicklinks-grid">
                <?php if ($showQuicklinkProfile): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/profile" class="member-quicklink-card">
                    <span>👤</span> Profil bearbeiten
                </a>
                <?php endif; ?>
                <?php if ($showQuicklinkNotifications): ?>
                <a href="<?php echo $notificationsUrl; ?>" class="member-quicklink-card">
                    <span>🔔</span> Benachrichtigungen
                </a>
                <?php endif; ?>
                <?php if ($showQuicklinkNewsletter): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/newsletter" class="member-quicklink-card">
                    <span>📧</span> Newsletter
                </a>
                <?php endif; ?>
                <?php if ($showQuicklinkFeeds): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/feeds" class="member-quicklink-card">
                    <span>📡</span> Feed-Abos
                </a>
                <?php endif; ?>
                <?php if ($showQuicklinkForum): ?>
                <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/forum" class="member-quicklink-card">
                    <span>🗣️</span> Forum
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.member-main -->
</div><!-- /.member-container -->

<?php include $themeDir . 'footer.php'; ?>
