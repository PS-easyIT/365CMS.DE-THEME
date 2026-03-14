<?php
/**
 * Member Benachrichtigungen – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'member/includes/bootstrap.php';

$controller->handleNotificationsRequest();

$memberService = \CMS\Services\MemberService::getInstance();
$siteUrl = SITE_URL;
$activePage = 'notifications';
$themeDir = \CMS\ThemeManager::instance()->getThemePath();
$preferences = $memberService->getNotificationPreferences($controller->getUserId());
$recentNotifications = $memberService->getRecentNotifications($controller->getUserId(), 20);
$flash = $controller->consumeFlash();

$checkboxes = [
    'email_notifications' => 'Allgemeine E-Mail-Benachrichtigungen',
    'email_updates' => 'Produkt- und System-Updates',
    'email_security' => 'Sicherheitsrelevante Meldungen',
    'email_marketing' => 'Marketing-E-Mails',
    'browser_notifications' => 'Browser-Benachrichtigungen',
    'desktop_notifications' => 'Desktop-Hinweise',
    'mobile_notifications' => 'Mobile Benachrichtigungen',
    'notify_new_features' => 'Hinweise auf neue Funktionen',
    'notify_promotions' => 'Aktionen und Angebote',
];

include $themeDir . 'header.php';
?>
<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main" id="main-content">
        <section class="member-page-title" data-anim>
            <h1>🔔 Benachrichtigungen</h1>
            <p>Lege fest, welche Hinweise dich erreichen und wann dein Member-Dashboard lieber schweigt als pingt.</p>
        </section>

        <?php echo phinit_render_member_flash($flash); ?>

        <section class="member-dashboard-hero member-dashboard-hero--notifications" data-anim data-anim-delay="1">
            <div>
                <span class="member-dashboard-hero__eyebrow">📣 Notification Center</span>
                <h1>Hinweise, Updates und Sicherheitsinfos</h1>
                <p>Aktiviere nur die Kanäle, die für dich sinnvoll sind — von produktiven Systemmeldungen bis zu eher charmant optionalem Marketing.</p>
                <div class="member-dashboard-hero__actions">
                    <a class="member-hero-action" href="#member-notification-settings">Einstellungen bearbeiten</a>
                    <a class="member-hero-action" href="#member-notification-feed">Letzte Meldungen</a>
                </div>
            </div>
            <div class="member-dashboard-hero__panel">
                <h3>Auf einen Blick</h3>
                <dl class="member-dashboard-hero__facts">
                    <div>
                        <dt>Aktive Kanäle</dt>
                        <dd><?php echo count(array_filter($preferences, static fn ($value): bool => (bool) $value)); ?></dd>
                    </div>
                    <div>
                        <dt>Frequenz</dt>
                        <dd><?php echo htmlspecialchars((string) ($preferences['notification_frequency'] ?? 'immediate'), ENT_QUOTES); ?></dd>
                    </div>
                    <div>
                        <dt>Letzte Meldungen</dt>
                        <dd><?php echo count($recentNotifications); ?></dd>
                    </div>
                </dl>
            </div>
        </section>

        <div class="member-grid-2 member-grid-2--notifications" data-anim data-anim-delay="1.5">
            <form class="member-card member-card--notification-settings" id="member-notification-settings" method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/notifications">
                <input type="hidden" name="action" value="notifications_save">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($controller->csrfToken('notifications_save'), ENT_QUOTES); ?>">
                <div class="member-card-header">
                    <h3>⚙️ Einstellungen</h3>
                </div>
                <div class="member-settings-grid">
                    <?php foreach ($checkboxes as $key => $label): ?>
                    <label class="member-switch-card">
                        <input type="checkbox" name="<?php echo htmlspecialchars($key, ENT_QUOTES); ?>" value="1" <?php echo !empty($preferences[$key]) ? 'checked' : ''; ?>>
                        <span>
                            <strong><?php echo htmlspecialchars($label, ENT_QUOTES); ?></strong>
                            <small><?php echo !empty($preferences[$key]) ? 'Aktiv' : 'Inaktiv'; ?></small>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="member-form-group">
                    <label class="member-label" for="notification_frequency">Frequenz</label>
                    <select class="member-input" id="notification_frequency" name="notification_frequency">
                        <?php foreach (['immediate' => 'Sofort', 'daily' => 'Täglich', 'weekly' => 'Wöchentlich'] as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" <?php echo (($preferences['notification_frequency'] ?? 'immediate') === $value) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="member-actions member-actions--compact">
                    <button type="submit" class="btn btn-primary">Einstellungen speichern</button>
                </div>
            </form>

            <section class="member-card member-card--notification-feed" id="member-notification-feed">
                <div class="member-card-header">
                    <h3>📰 Letzte Meldungen</h3>
                    <span class="member-badge-soft"><?php echo count($recentNotifications); ?> Einträge</span>
                </div>

                <?php if ($recentNotifications === []): ?>
                <div class="member-empty">
                    <p>📭 Zurzeit liegen keine Benachrichtigungen vor.</p>
                </div>
                <?php else: ?>
                <div class="member-notification-feed">
                    <?php foreach ($recentNotifications as $notification): ?>
                    <?php
                    $title = (string) ($notification->title ?? $notification->message ?? 'Benachrichtigung');
                    $message = (string) ($notification->message ?? '');
                    ?>
                    <article class="member-notification-item">
                        <div class="member-notification-item__icon">🔔</div>
                        <div class="member-notification-item__content">
                            <strong><?php echo htmlspecialchars($title, ENT_QUOTES); ?></strong>
                            <?php if ($message !== '' && $message !== $title): ?>
                            <p><?php echo htmlspecialchars($message, ENT_QUOTES); ?></p>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars((string) ($notification->created_at ?? ''), ENT_QUOTES); ?></span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>
<?php include $themeDir . 'footer.php';
