<?php
/**
 * Member Feed-Abos – CMS Phinit Theme
 *
 * Persönliche Feed-Abos mit Mail-Zeitplan für ausgewählte Feeds.
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
$siteUrl     = SITE_URL;
$activePage  = 'feeds';
$themeDir    = \CMS\ThemeManager::instance()->getThemePath();

$success = '';
$error   = '';
$feedLoadError = '';

$weekdayOptions = [
    1 => 'Montag',
    2 => 'Dienstag',
    3 => 'Mittwoch',
    4 => 'Donnerstag',
    5 => 'Freitag',
    6 => 'Samstag',
    7 => 'Sonntag',
];

$dailyModeOptions = [
    '09' => 'Täglich um 09:00 Uhr',
    '15' => 'Täglich um 15:00 Uhr',
    '09_15' => 'Täglich um 09:00 und 15:00 Uhr',
];

$timeSlotOptions = [
    '09' => '09:00 Uhr',
    '15' => '15:00 Uhr',
];

$hasFeedPlugin = \CMS\PluginManager::instance()->isPluginActive('cms-feed')
    && class_exists('CMS_Feed_Database')
    && class_exists('CMS_Feed_Email_Digest');

$feedDb = $hasFeedPlugin ? CMS_Feed_Database::instance() : null;
$mailer = $hasFeedPlugin ? CMS_Feed_Email_Digest::instance() : null;

$subscription = null;
$categories = [];
$channels = [];
$channelsByCategory = [];
$channelIndex = [];

$normalizeCategoryId = static function (array $channel): int {
    $categoryId = (int) ($channel['category_id'] ?? 0);
    return $categoryId > 0 ? $categoryId : 0;
};

$buildFallbackCategories = static function (array $availableChannels): array {
    $fallbackCategories = [];

    foreach ($availableChannels as $channel) {
        $categoryId = (int) ($channel['category_id'] ?? 0);
        $categoryId = $categoryId > 0 ? $categoryId : 0;

        if (isset($fallbackCategories[$categoryId])) {
            continue;
        }

        $fallbackCategories[$categoryId] = [
            'id' => $categoryId,
            'name' => (string) ($channel['category_name'] ?? ($categoryId === 0 ? 'Allgemein' : 'Bereich')),
            'icon' => '📡',
            'description' => '',
        ];
    }

    return array_values($fallbackCategories);
};

if ($hasFeedPlugin && $feedDb !== null) {
    try {
        $categories = $feedDb->get_categories();
        $channels   = array_values(array_filter(
            $feedDb->get_channels(),
            static fn (array $channel): bool => (int) ($channel['is_active'] ?? 0) === 1
        ));

        foreach ($channels as $channel) {
            $channelId = (int) ($channel['id'] ?? 0);
            if ($channelId <= 0) {
                continue;
            }

            $channelIndex[$channelId] = $channel;
            $channelsByCategory[$normalizeCategoryId($channel)][] = $channel;
        }

        if ($channels !== [] && $categories === []) {
            $categories = $buildFallbackCategories($channels);
        }

        $subscription = $feedDb->get_member_subscription((int) ($currentUser->id ?? 0));
    } catch (\Throwable $throwable) {
        $feedLoadError = 'Die Feed-Konfiguration konnte gerade nicht vollständig geladen werden.';
        $error = $error !== '' ? $error : $feedLoadError;
        $categories = [];
        $channels = [];
        $channelsByCategory = [];
        $channelIndex = [];
        $subscription = null;

        error_log('cms-phinit member feeds: ' . $throwable->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasFeedPlugin && $feedDb !== null) {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_feeds')) {
        $error = 'Sicherheitscheck fehlgeschlagen.';
    } else {
        $email = filter_var(trim((string) ($_POST['subscription_email'] ?? ($currentUser->email ?? ''))), FILTER_VALIDATE_EMAIL);
        $frequency = in_array($_POST['subscription_frequency'] ?? 'daily', ['daily', 'weekly'], true)
            ? (string) $_POST['subscription_frequency']
            : 'daily';
        $dailyMode = in_array($_POST['daily_mode'] ?? '09', array_keys($dailyModeOptions), true)
            ? (string) $_POST['daily_mode']
            : '09';
        $weeklyDay = max(1, min(7, (int) ($_POST['weekly_day'] ?? 1)));
        $weeklyTime = in_array($_POST['weekly_time'] ?? '09', array_keys($timeSlotOptions), true)
            ? (string) $_POST['weekly_time']
            : '09';
        $isActive = !empty($_POST['subscription_is_active']);
        $selectedChannelIds = phinit_input_int_list($_POST, 'channel_ids', 1);

        if ($email === false) {
            $error = 'Bitte hinterlege eine gültige E-Mail-Adresse für dein Feed-Abo.';
        } elseif ($isActive && $selectedChannelIds === []) {
            $error = 'Wähle mindestens einen Feed aus, wenn das Abo aktiv sein soll.';
        } else {
            try {
                $feedDb->save_member_subscription([
                    'user_id' => (int) ($currentUser->id ?? 0),
                    'email' => (string) $email,
                    'channel_ids' => $selectedChannelIds,
                    'frequency' => $frequency,
                    'daily_mode' => $dailyMode,
                    'weekly_day' => $weeklyDay,
                    'weekly_time' => $weeklyTime,
                    'is_active' => $isActive ? 1 : 0,
                ]);

                $subscription = $feedDb->get_member_subscription((int) ($currentUser->id ?? 0));
                $success = $isActive
                    ? 'Dein Feed-Abo wurde gespeichert. Die nächsten Mails kommen pünktlich – ganz ohne manuelles Refresh-Yoga.'
                    : 'Dein Feed-Abo wurde pausiert. Auswahl und Zeitplan bleiben gespeichert.';
            } catch (\Throwable $throwable) {
                $error = 'Dein Feed-Abo konnte gerade nicht gespeichert werden.';
                error_log('cms-phinit member feeds save: ' . $throwable->getMessage());
            }
        }
    }
}

$csrfToken = \CMS\Security::instance()->generateToken('member_feeds');

$subscription = $subscription ?? [
    'email' => (string) ($currentUser->email ?? ''),
    'frequency' => 'daily',
    'daily_mode' => '09',
    'weekly_day' => 1,
    'weekly_time' => '09',
    'is_active' => 1,
    'channel_ids' => '[]',
    'last_sent_at' => null,
];

$renderCategories = [];

foreach ($categories as $category) {
    $categoryId = (int) ($category['id'] ?? 0);
    $renderCategories[$categoryId] = $category;
}

foreach ($buildFallbackCategories($channels) as $fallbackCategory) {
    $categoryId = (int) ($fallbackCategory['id'] ?? 0);
    if (!isset($renderCategories[$categoryId])) {
        $renderCategories[$categoryId] = $fallbackCategory;
    }
}

$renderCategories = array_values(array_filter(
    $renderCategories,
    static fn (array $category): bool => !empty($channelsByCategory[(int) ($category['id'] ?? 0)])
));

$selectedChannelIds = [];
if ($hasFeedPlugin && $feedDb !== null) {
    try {
        $selectedChannelIds = $feedDb->get_member_subscription_channel_ids($subscription);
    } catch (\Throwable $throwable) {
        $selectedChannelIds = [];
        if ($error === '') {
            $error = 'Die gespeicherte Feed-Auswahl konnte nicht vollständig gelesen werden.';
        }
        error_log('cms-phinit member feeds channel ids: ' . $throwable->getMessage());
    }
}
$selectedChannels = [];

$formatFeedDate = static function (?string $value, string $format = 'd.m.Y H:i', string $suffix = ' Uhr'): string {
    $timestamp = strtotime((string) $value);
    if ($timestamp === false) {
        return 'Noch kein Versand erfolgt';
    }

    return date($format, $timestamp) . $suffix;
};

foreach ($selectedChannelIds as $selectedChannelId) {
    if (isset($channelIndex[$selectedChannelId])) {
        $selectedChannels[] = $channelIndex[$selectedChannelId];
    }
}

$selectedCount = (int) count($selectedChannels);
$availableCount = (int) count($channels);
$categoryCount = (int) count(array_filter($renderCategories, static fn (array $category): bool => !empty($channelsByCategory[(int) ($category['id'] ?? 0)])));
$scheduleLabel = 'Noch kein Zeitplan definiert';
if ($hasFeedPlugin && $mailer !== null) {
    try {
        $scheduleLabel = $mailer->get_member_schedule_label($subscription);
    } catch (\Throwable $throwable) {
        $scheduleLabel = 'Zeitplan aktuell nicht lesbar';
        error_log('cms-phinit member feeds schedule: ' . $throwable->getMessage());
    }
}
$lastSentLabel = !empty($subscription['last_sent_at'])
    ? $formatFeedDate((string) $subscription['last_sent_at'])
    : 'Noch kein Versand erfolgt';
$isActiveSubscription = !empty($subscription['is_active']);

include $themeDir . 'header.php';
?>

<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main" id="main-content">
        <section class="member-page-title">
            <h1>📡 Feed-Abos</h1>
            <p>Wähle deine Lieblings-Feeds, lege einen Mail-Zeitplan fest und lass dir Updates täglich oder wöchentlich bequem ins Postfach schicken.</p>
        </section>

        <?php if ($success !== ''): ?>
        <div class="member-alert member-alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
        <div class="member-alert member-alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>

        <?php if (!$hasFeedPlugin): ?>
        <div class="member-empty-state">
            <p class="member-empty-state__icon">📡</p>
            <p><strong>Feed-System derzeit nicht verfügbar</strong></p>
            <p>Das Plugin <code>cms-feed</code> ist aktuell nicht aktiv oder noch nicht vollständig geladen.</p>
        </div>
        <?php elseif ($feedLoadError !== ''): ?>
        <div class="member-empty-state">
            <p class="member-empty-state__icon">⚠️</p>
            <p><strong>Feed-Bereich aktuell nicht vollständig verfügbar</strong></p>
            <p><?php echo htmlspecialchars($feedLoadError, ENT_QUOTES); ?> Bitte prüfe das Plugin oder lade die Seite erneut.</p>
        </div>
        <?php elseif ($channels === []): ?>
        <div class="member-empty-state">
            <p class="member-empty-state__icon">📭</p>
            <p><strong>Noch keine Feeds vorhanden</strong></p>
            <p>Sobald im Feed-Plugin aktive Quellen angelegt wurden, kannst du hier dein persönliches Mail-Abo konfigurieren.</p>
        </div>
        <?php else: ?>

        <section class="member-dashboard-hero member-dashboard-hero--notifications">
            <div>
                <span class="member-dashboard-hero__eyebrow">📬 Persönlicher Feed-Digest</span>
                <h1>Deine Feeds. Dein Takt. Dein Postfach.</h1>
                <p>Aktiviere einen täglichen oder wöchentlichen Versand, kombiniere mehrere Feeds in einer Mail und nutze bei Bedarf sogar den Doppelslot um 09:00 und 15:00 Uhr.</p>
                <div class="member-dashboard-hero__actions">
                    <a class="member-hero-action" href="#member-feed-subscription">Abo bearbeiten</a>
                    <a class="member-hero-action" href="#member-feed-selection">Feeds auswählen</a>
                </div>
            </div>
            <div class="member-dashboard-hero__panel">
                <h3>Auf einen Blick</h3>
                <dl class="member-dashboard-hero__facts">
                    <div>
                        <dt>Status</dt>
                        <dd><?php echo $isActiveSubscription ? 'Aktiv' : 'Pausiert'; ?></dd>
                    </div>
                    <div>
                        <dt>Ausgewählte Feeds</dt>
                        <dd><?php echo (int) $selectedCount; ?> von <?php echo (int) $availableCount; ?></dd>
                    </div>
                    <div>
                        <dt>Zeitplan</dt>
                        <dd><?php echo htmlspecialchars($scheduleLabel, ENT_QUOTES); ?></dd>
                    </div>
                    <div>
                        <dt>Letzter Versand</dt>
                        <dd><?php echo htmlspecialchars($lastSentLabel, ENT_QUOTES); ?></dd>
                    </div>
                </dl>
            </div>
        </section>

        <div class="member-grid-2 member-grid-2--notifications">
            <form class="member-card member-card--notification-settings" id="member-feed-subscription" method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/feeds#member-feed-subscription">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
                <?php foreach ($selectedChannelIds as $selectedChannelId): ?>
                <input type="hidden" name="channel_ids[]" value="<?php echo (int) $selectedChannelId; ?>">
                <?php endforeach; ?>

                <div class="member-card-header">
                    <h3>⚙️ Feed-Abo konfigurieren</h3>
                    <span class="member-badge-soft"><?php echo (int) $selectedCount; ?> Feed<?php echo $selectedCount === 1 ? '' : 's'; ?> aktiv gewählt</span>
                </div>

                <div class="member-form-group">
                    <label class="member-label" for="subscription_email">Empfänger-E-Mail</label>
                    <input class="member-input" id="subscription_email" name="subscription_email" type="email" required value="<?php echo htmlspecialchars((string) ($subscription['email'] ?? ($currentUser->email ?? '')), ENT_QUOTES); ?>">
                    <p class="member-form-hint">An diese Adresse werden deine Feed-Mails zugestellt. Standardmäßig nutzen wir deine Member-E-Mail.</p>
                </div>

                <div class="member-form-group">
                    <label class="member-switch-card" for="subscription_is_active">
                        <input id="subscription_is_active" type="checkbox" name="subscription_is_active" value="1" <?php echo $isActiveSubscription ? 'checked' : ''; ?>>
                        <span>
                            <strong>Feed-Abo aktiv</strong>
                            <small>Wenn deaktiviert, bleiben Auswahl und Zeitplan gespeichert, aber es werden keine Mails versendet.</small>
                        </span>
                    </label>
                </div>

                <div class="member-form-group">
                    <label class="member-label" for="subscription_frequency">Intervall</label>
                    <select class="member-input" id="subscription_frequency" name="subscription_frequency">
                        <option value="daily" <?php echo (($subscription['frequency'] ?? 'daily') === 'daily') ? 'selected' : ''; ?>>Täglich</option>
                        <option value="weekly" <?php echo (($subscription['frequency'] ?? 'daily') === 'weekly') ? 'selected' : ''; ?>>Wöchentlich</option>
                    </select>
                    <p class="member-form-hint">Täglich unterstützt 09:00 Uhr, 15:00 Uhr oder beide Versand-Slots. Wöchentlich kombiniert einen festen Wochentag mit einem Slot.</p>
                </div>

                <div class="member-form-row">
                    <div class="member-form-group">
                        <label class="member-label" for="daily_mode">Täglicher Versand</label>
                        <select class="member-input" id="daily_mode" name="daily_mode">
                            <?php foreach ($dailyModeOptions as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" <?php echo (($subscription['daily_mode'] ?? '09') === $value) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="member-form-group">
                        <label class="member-label" for="weekly_day">Wöchentlicher Versandtag</label>
                        <select class="member-input" id="weekly_day" name="weekly_day">
                            <?php foreach ($weekdayOptions as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo ((int) ($subscription['weekly_day'] ?? 1) === $value) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="member-form-group">
                    <label class="member-label" for="weekly_time">Wöchentlicher Versand-Slot</label>
                    <select class="member-input" id="weekly_time" name="weekly_time">
                        <?php foreach ($timeSlotOptions as $value => $label): ?>
                        <option value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" <?php echo (($subscription['weekly_time'] ?? '09') === $value) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="member-actions member-actions--compact">
                    <button type="submit" class="btn btn-primary">💾 Feed-Abo speichern</button>
                </div>
            </form>

            <section class="member-card member-card--notification-feed">
                <div class="member-card-header">
                    <h3>🧾 Abo-Zusammenfassung</h3>
                    <span class="member-badge-soft"><?php echo (int) $categoryCount; ?> Bereich<?php echo $categoryCount === 1 ? '' : 'e'; ?></span>
                </div>

                <div class="member-notification-feed">
                    <article class="member-notification-item">
                        <div class="member-notification-item__icon">📧</div>
                        <div class="member-notification-item__content">
                            <strong>Empfänger</strong>
                            <p><?php echo htmlspecialchars((string) ($subscription['email'] ?? ($currentUser->email ?? '')), ENT_QUOTES); ?></p>
                            <span><?php echo $isActiveSubscription ? 'Versand aktiv' : 'Versand pausiert'; ?></span>
                        </div>
                    </article>
                    <article class="member-notification-item">
                        <div class="member-notification-item__icon">⏰</div>
                        <div class="member-notification-item__content">
                            <strong>Zeitplan</strong>
                            <p><?php echo htmlspecialchars($scheduleLabel, ENT_QUOTES); ?></p>
                            <span>Letzter Versand: <?php echo htmlspecialchars($lastSentLabel, ENT_QUOTES); ?></span>
                        </div>
                    </article>
                    <article class="member-notification-item">
                        <div class="member-notification-item__icon">📡</div>
                        <div class="member-notification-item__content">
                            <strong>Feed-Auswahl</strong>
                            <p><?php echo (int) $selectedCount; ?> Feed<?php echo $selectedCount === 1 ? '' : 's'; ?> ausgewählt</p>
                            <span><?php echo (int) $availableCount; ?> aktive Quellen insgesamt verfügbar</span>
                        </div>
                    </article>
                </div>

                <?php if ($selectedChannels === []): ?>
                <div class="member-empty">
                    <p>📭 Noch keine Feed-Auswahl gespeichert.</p>
                </div>
                <?php else: ?>
                <div class="member-session-list member-session-list--analytics">
                    <?php foreach (array_slice($selectedChannels, 0, 6) as $selectedChannel): ?>
                    <article class="member-session-item">
                        <div class="member-session-item__main">
                            <strong><?php echo htmlspecialchars((string) ($selectedChannel['name'] ?? 'Feed'), ENT_QUOTES); ?></strong>
                            <span><?php echo htmlspecialchars((string) ($selectedChannel['category_name'] ?? 'Allgemein'), ENT_QUOTES); ?></span>
                        </div>
                        <div class="member-session-item__meta">
                            <span><?php echo (int) ($selectedChannel['fetch_interval'] ?? 0); ?> Min. Abrufintervall</span>
                            <span><?php echo (int) ($selectedChannel['item_count'] ?? 0); ?> gespeicherte Beiträge</span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <?php if ($selectedCount > 6): ?>
                <p class="member-form-hint">+<?php echo (int) ($selectedCount - 6); ?> weitere Feeds sind zusätzlich ausgewählt.</p>
                <?php endif; ?>
                <?php endif; ?>
            </section>
        </div>

        <form class="member-card member-card--spaced" id="member-feed-selection" method="post" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/feeds#member-feed-selection">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="subscription_email" value="<?php echo htmlspecialchars((string) ($subscription['email'] ?? ($currentUser->email ?? '')), ENT_QUOTES); ?>">
            <input type="hidden" name="subscription_frequency" value="<?php echo htmlspecialchars((string) ($subscription['frequency'] ?? 'daily'), ENT_QUOTES); ?>">
            <input type="hidden" name="daily_mode" value="<?php echo htmlspecialchars((string) ($subscription['daily_mode'] ?? '09'), ENT_QUOTES); ?>">
            <input type="hidden" name="weekly_day" value="<?php echo (int) ($subscription['weekly_day'] ?? 1); ?>">
            <input type="hidden" name="weekly_time" value="<?php echo htmlspecialchars((string) ($subscription['weekly_time'] ?? '09'), ENT_QUOTES); ?>">
            <?php if ($isActiveSubscription): ?>
            <input type="hidden" name="subscription_is_active" value="1">
            <?php endif; ?>

            <div class="member-card-header">
                <h3>📰 Verfügbare Feeds auswählen</h3>
                <span class="member-badge-soft"><?php echo (int) $availableCount; ?> aktive Feeds</span>
            </div>

                <?php foreach ($renderCategories as $category): ?>
                <?php $categoryChannels = $channelsByCategory[(int) ($category['id'] ?? 0)] ?? []; ?>
                <?php if ($categoryChannels === []): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <div class="member-form-group">
                    <div class="member-card-header">
                        <h3><?php echo htmlspecialchars((string) ($category['icon'] ?? '📰'), ENT_QUOTES); ?> <?php echo htmlspecialchars((string) ($category['name'] ?? 'Bereich'), ENT_QUOTES); ?></h3>
                        <?php $categoryChannelCount = (int) count($categoryChannels); ?>
                        <span class="member-badge-soft"><?php echo $categoryChannelCount; ?> Feed<?php echo $categoryChannelCount === 1 ? '' : 's'; ?></span>
                    </div>

                    <?php if (!empty($category['description'])): ?>
                    <p class="member-form-hint"><?php echo htmlspecialchars((string) $category['description'], ENT_QUOTES); ?></p>
                    <?php endif; ?>

                    <div class="member-settings-grid">
                        <?php foreach ($categoryChannels as $channel): ?>
                        <?php $channelId = (int) ($channel['id'] ?? 0); ?>
                        <label class="member-switch-card">
                            <input type="checkbox" name="channel_ids[]" value="<?php echo $channelId; ?>" <?php echo in_array($channelId, $selectedChannelIds, true) ? 'checked' : ''; ?>>
                            <span>
                                <strong><?php echo htmlspecialchars((string) ($channel['name'] ?? 'Feed'), ENT_QUOTES); ?></strong>
                                <small>
                                    <?php echo htmlspecialchars((string) ($channel['description'] ?? 'Aktiver Feed-Kanal für dein persönliches Mail-Abo.'), ENT_QUOTES); ?>
                                    <?php if (!empty($channel['last_fetched_at'])): ?>
                                     <?php $lastFetchedLabel = $formatFeedDate((string) $channel['last_fetched_at']); ?>
                                     <?php if ($lastFetchedLabel !== 'Noch kein Versand erfolgt'): ?>
                                     · Letzter Abruf: <?php echo htmlspecialchars($lastFetchedLabel, ENT_QUOTES); ?>
                                     <?php endif; ?>
                                    <?php endif; ?>
                                </small>
                            </span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="member-actions member-actions--compact">
                    <button type="submit" class="btn btn-primary">📡 Feed-Auswahl speichern</button>
                </div>

        </form>

        <?php endif; ?>
    </div>
</div>

<?php include $themeDir . 'footer.php'; ?>
