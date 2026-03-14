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

$activePage = $activePage ?? 'dashboard';
$siteUrl    = SITE_URL;

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

$memberNav = [
    ['slug' => 'dashboard',  'icon' => '📊', 'label' => 'Dashboard',     'url' => '/member/dashboard'],
    ['slug' => 'profile',    'icon' => '👤', 'label' => 'Profil',        'url' => '/member/profile'],
    ['slug' => 'notifications', 'icon' => '🔔', 'label' => 'Benachrichtigungen', 'url' => '/member/notifications'],
    ['slug' => 'messages',   'icon' => '✉️', 'label' => 'Nachrichten',   'url' => '/member/messages'],
    ['slug' => 'favorites',  'icon' => '⭐', 'label' => 'Favoriten',     'url' => '/member/favorites'],
    ['slug' => 'comments',   'icon' => '💬', 'label' => 'Kommentare',    'url' => '/member/comments'],
    ['slug' => 'newsletter', 'icon' => '📧', 'label' => 'Newsletter',    'url' => '/member/newsletter'],
    ['slug' => 'feeds',      'icon' => '📡', 'label' => 'Feed-Abos',     'url' => '/member/feeds'],
    ['slug' => 'forum',      'icon' => '🗣️', 'label' => 'Forum',         'url' => '/member/forum'],
    ['slug' => 'security',   'icon' => '🔒', 'label' => 'Sicherheit',    'url' => '/member/security'],
];

if ($isAdmin) {
    $memberNav[] = ['slug' => 'analytics', 'icon' => '📈', 'label' => 'Analytics', 'url' => '/member/analytics'];
}
?>
<aside class="member-sidebar" aria-label="Mitglieder-Navigation">
    <div class="member-sidebar-user">
        <div class="member-avatar">
            <?php if ($memberAvatarUrl !== ''): ?>
            <img src="<?php echo htmlspecialchars($memberAvatarUrl, ENT_QUOTES); ?>"
                 alt="<?php echo htmlspecialchars($memberDisplayName, ENT_QUOTES); ?>"
                 class="member-avatar__image"
                 loading="lazy"
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
    <?php if ($isAdmin): ?>
    <div class="member-admin-cta">
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/admin/" class="btn btn-sm btn-accent member-admin-btn">
            ⚙️ Zum Admincenter
        </a>
    </div>
    <?php endif; ?>
    <nav class="member-nav">
        <?php foreach ($memberNav as $item): ?>
        <a href="<?php echo htmlspecialchars($siteUrl . $item['url'], ENT_QUOTES); ?>"
           class="member-nav-link<?php echo $activePage === $item['slug'] ? ' active' : ''; ?>"
           <?php echo $activePage === $item['slug'] ? 'aria-current="page"' : ''; ?>>
            <span class="member-nav-icon"><?php echo $item['icon']; ?></span>
            <?php echo htmlspecialchars($item['label']); ?>
        </a>
        <?php endforeach; ?>
    </nav>
    <div class="member-nav-footer">
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/logout" class="member-nav-link member-nav-logout">
            <span class="member-nav-icon">🚪</span> Abmelden
        </a>
    </div>
</aside>
