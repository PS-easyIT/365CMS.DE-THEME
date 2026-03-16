<?php
/**
 * Member-Bereich Sidebar-Navigation
 *
 * Wird von allen Member-Seiten eingebunden.
 * Variablen: $activePage (string) – aktiver Menüpunkt-Slug
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

use CMS\Services\ThemeCustomizer;

$activePage = $activePage ?? 'dashboard';
$siteUrl    = SITE_URL;
$themeCustomizer = ThemeCustomizer::instance();

$getMemberToggle = static function (string $key, bool $default = true) use ($themeCustomizer): bool {
    return filter_var($themeCustomizer->get('memberdashboard', $key, $default), FILTER_VALIDATE_BOOLEAN);
};

$getMemberText = static function (string $key, string $default = '') use ($themeCustomizer): string {
    return trim((string) $themeCustomizer->get('memberdashboard', $key, $default));
};

$sanitizeColor = static function (string $value, string $fallback): string {
    return preg_match('/^#[0-9a-f]{6}$/i', $value) ? $value : $fallback;
};

$memberMeta = [];
$memberDisplayName = trim((string) ($currentUser->display_name ?? $currentUser->username ?? 'Benutzer'));
$memberAvatarUrl = '';

try {
    $memberMeta = \CMS\Services\MemberService::getInstance()->getUserMeta((int) ($currentUser->id ?? 0));
    $fullName = trim((string) ($memberMeta['first_name'] ?? '') . ' ' . (string) ($memberMeta['last_name'] ?? ''));
    if ($fullName !== '') {
        $memberDisplayName = $fullName;
    }
    if (!empty($currentUser->display_name)) {
        $memberDisplayName = trim((string) $currentUser->display_name);
    }
    $memberAvatarUrl = trim((string) ($memberMeta['avatar'] ?? ''));
} catch (\Throwable $e) {}

$isAdmin = false;
try {
    $isAdmin = \CMS\Auth::instance()->isAdmin();
} catch (\Throwable $e) {}

$sidebarActiveColor = $sanitizeColor($getMemberText('sidebar_active_color', '#1e3a5f'), '#1e3a5f');
$sidebarStyle = '--member-sidebar-active-color: ' . $sidebarActiveColor . ';';
$memberPermissions = [];
$requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/member/dashboard');
$currentMenuSlug = $activePage;
$memberController = null;

if (preg_match('#/member/plugin/([a-zA-Z0-9_-]+)#', $requestUri, $matches) === 1) {
    $currentMenuSlug = 'plugin_' . $matches[1];
}

if (isset($controller) && is_object($controller) && method_exists($controller, 'getMenuItems')) {
    $memberController = $controller;
} else {
    try {
        if (!class_exists('CMS\\MemberArea\\MemberController')) {
            require_once ABSPATH . 'member/includes/class-member-controller.php';
        }

        if (class_exists('CMS\\MemberArea\\MemberController')) {
            $memberController = \CMS\MemberArea\MemberController::instance();
        }
    } catch (\Throwable $e) {
        $memberController = null;
    }
}

try {
    $memberPermissions = \CMS\Services\MemberService::getInstance()->getUserPermissions((int) ($currentUser->id ?? 0));
} catch (\Throwable $e) {}

$canSubmitPosts = !empty($memberPermissions['can_post']);

$memberNav = [
    ['slug' => 'dashboard',  'icon' => '📊', 'label' => 'Dashboard',     'url' => '/member/dashboard', 'visible' => $getMemberToggle('show_sidebar_dashboard', true)],
    ['slug' => 'profile',    'icon' => '👤', 'label' => 'Profil',        'url' => '/member/profile', 'visible' => $getMemberToggle('show_sidebar_profile', true)],
    ['slug' => 'posts',      'icon' => '✍️', 'label' => 'Artikel',       'url' => '/member/posts', 'visible' => $canSubmitPosts],
    ['slug' => 'privacy',    'icon' => '🔐', 'label' => 'Datenschutz',   'url' => '/member/privacy', 'visible' => $getMemberToggle('show_sidebar_privacy', true)],
    ['slug' => 'notifications', 'icon' => '🔔', 'label' => 'Benachrichtigungen', 'url' => '/member/notifications', 'visible' => $getMemberToggle('show_sidebar_notifications', true)],
    ['slug' => 'favorites',  'icon' => '⭐', 'label' => 'Favoriten',     'url' => '/member/favorites', 'visible' => $getMemberToggle('show_sidebar_favorites', true)],
    ['slug' => 'comments',   'icon' => '💬', 'label' => 'Kommentare',    'url' => '/member/comments', 'visible' => $getMemberToggle('show_sidebar_comments', true)],
    ['slug' => 'newsletter', 'icon' => '📧', 'label' => 'Newsletter',    'url' => '/member/newsletter', 'visible' => $getMemberToggle('show_sidebar_newsletter', true)],
    ['slug' => 'feeds',      'icon' => '📡', 'label' => 'Feed-Abos',     'url' => '/member/feeds', 'visible' => $getMemberToggle('show_sidebar_feeds', true)],
    ['slug' => 'forum',      'icon' => '🗣️', 'label' => 'Forum',         'url' => '/member/forum', 'visible' => $getMemberToggle('show_sidebar_forum', true)],
    ['slug' => 'security',   'icon' => '🔒', 'label' => 'Sicherheit',    'url' => '/member/security', 'visible' => $getMemberToggle('show_sidebar_security', true)],
];

if ($isAdmin && $getMemberToggle('show_sidebar_analytics', true)) {
    $memberNav[] = ['slug' => 'analytics', 'icon' => '📈', 'label' => 'Analytics', 'url' => '/member/analytics', 'visible' => true];
}

$memberNav = array_values(array_filter($memberNav, static fn (array $item): bool => (bool) ($item['visible'] ?? true)));

$pluginParentItems = [];
$pluginChildrenByParent = [];

if ($memberController !== null && method_exists($memberController, 'getMenuItems')) {
    try {
        $allMenuItems = $memberController->getMenuItems($currentMenuSlug);

        foreach ($allMenuItems as $item) {
            $slug = (string) ($item['slug'] ?? '');
            $category = (string) ($item['category'] ?? '');

            if ($slug === '' || ($category !== 'plugins' && !str_starts_with($slug, 'plugin_'))) {
                continue;
            }

            $parentSlug = (string) ($item['parent_slug'] ?? '');
            if ($parentSlug !== '') {
                $pluginChildrenByParent[$parentSlug][] = $item;
                continue;
            }

            $pluginParentItems[] = $item;
        }
    } catch (\Throwable $e) {
        $pluginParentItems = [];
        $pluginChildrenByParent = [];
    }
}

$showSidebarAdminLink = $isAdmin && $getMemberToggle('show_sidebar_admin_link', true);
$sidebarAdminLabel = $getMemberText('sidebar_admin_label', 'Zum Admincenter');
$sidebarAdminIcon = $getMemberText('sidebar_admin_icon', '⚙️');
?>
<aside class="member-sidebar" aria-label="Mitglieder-Navigation" style="<?php echo htmlspecialchars($sidebarStyle, ENT_QUOTES); ?>">
    <div class="member-sidebar-user">
        <div class="member-avatar">
            <?php if ($memberAvatarUrl !== ''): ?>
            <img src="<?php echo htmlspecialchars($memberAvatarUrl, ENT_QUOTES); ?>"
                 alt="<?php echo htmlspecialchars($memberDisplayName, ENT_QUOTES); ?>"
                 class="member-avatar__image"
                  <?php echo phinit_image_loading_attributes(true, false); ?>
                 width="44"
                 height="44">
            <?php else: ?>
            <?php echo strtoupper(mb_substr($memberDisplayName !== '' ? $memberDisplayName : (string) ($currentUser->username ?? 'U'), 0, 2)); ?>
            <?php endif; ?>
        </div>
        <div class="member-user-info">
            <strong><?php echo htmlspecialchars($memberDisplayName !== '' ? $memberDisplayName : (string) ($currentUser->username ?? 'Benutzer'), ENT_QUOTES); ?></strong>
            <span><?php echo htmlspecialchars($currentUser->email ?? '', ENT_QUOTES); ?></span>
        </div>
    </div>
    <nav class="member-nav">
        <?php foreach ($memberNav as $item): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . $item['url'], ENT_QUOTES); ?>"
           class="member-nav-link<?php echo $activePage === $item['slug'] ? ' active' : ''; ?>"
           <?php echo $activePage === $item['slug'] ? 'aria-current="page"' : ''; ?>>
            <span class="member-nav-icon"><?php echo $item['icon']; ?></span>
            <?php echo htmlspecialchars($item['label']); ?>
        </a>
        <?php endforeach; ?>

        <?php foreach ($pluginParentItems as $item): ?>
        <?php $pluginSlug = (string) ($item['slug'] ?? ''); ?>
        <a href="<?php echo htmlspecialchars($siteUrl . (string) ($item['url'] ?? '#'), ENT_QUOTES); ?>"
           class="member-nav-link<?php echo !empty($item['active']) ? ' active' : ''; ?>"
           <?php echo !empty($item['active']) ? 'aria-current="page"' : ''; ?>>
            <span class="member-nav-icon"><?php echo htmlspecialchars((string) ($item['icon'] ?? '🧩'), ENT_QUOTES); ?></span>
            <?php echo htmlspecialchars((string) ($item['label'] ?? 'Plugin'), ENT_QUOTES); ?>
        </a>

        <?php foreach ($pluginChildrenByParent[$pluginSlug] ?? [] as $childItem): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . (string) ($childItem['url'] ?? '#'), ENT_QUOTES); ?>"
           class="member-nav-link member-nav-link--child<?php echo !empty($childItem['active']) ? ' active' : ''; ?>"
           <?php echo !empty($childItem['active']) ? 'aria-current="page"' : ''; ?>>
            <span class="member-nav-icon"><?php echo htmlspecialchars((string) ($childItem['icon'] ?? '↳'), ENT_QUOTES); ?></span>
            <?php echo htmlspecialchars('- ' . (string) ($childItem['label'] ?? 'Unterpunkt'), ENT_QUOTES); ?>
        </a>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
    <div class="member-sidebar-bottom">
        <?php if ($showSidebarAdminLink): ?>
        <div class="member-admin-cta">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/admin/" class="member-nav-link member-admin-link">
                <span class="member-nav-icon"><?php echo htmlspecialchars($sidebarAdminIcon !== '' ? $sidebarAdminIcon : '⚙️', ENT_QUOTES); ?></span> <?php echo htmlspecialchars($sidebarAdminLabel !== '' ? $sidebarAdminLabel : 'Zum Admincenter', ENT_QUOTES); ?>
            </a>
        </div>
        <?php endif; ?>
        <div class="member-nav-footer">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/logout" class="member-nav-link member-nav-logout">
                <span class="member-nav-icon">🚪</span> Abmelden
            </a>
        </div>
    </div>
</aside>
