<?php
/**
 * Member Sicherheit – CMS Phinit Theme
 *
 * Passwort ändern, aktive Sessions anzeigen.
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
$activePage  = 'security';
$themeDir    = \CMS\ThemeManager::instance()->getThemePath();

$success = '';
$error   = '';

// CSRF
$csrfToken = \CMS\Security::instance()->generateToken('member_security');

// POST-Verarbeitung: Passwort ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_security'])) {
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'member_security')) {
        $error = 'Sicherheitscheck fehlgeschlagen.';
    } else {
        $currentPw = $_POST['current_password'] ?? '';
        $newPw     = $_POST['new_password'] ?? '';
        $newPwRep  = $_POST['new_password_repeat'] ?? '';

        if (empty($currentPw) || empty($newPw) || empty($newPwRep)) {
            $error = 'Bitte alle Felder ausfüllen.';
        } elseif ($newPw !== $newPwRep) {
            $error = 'Die neuen Passwörter stimmen nicht überein.';
        } elseif (strlen($newPw) < 12) {
            $error = 'Das neue Passwort muss mindestens 12 Zeichen lang sein.';
        } else {
            // Passwortrichtlinie prüfen (nutzt CMS::Auth wenn verfügbar)
            $policyError = '';
            if (method_exists($auth, 'validatePasswordPolicy')) {
                $policyError = $auth->validatePasswordPolicy($newPw);
            }
            if (!empty($policyError)) {
                $error = $policyError;
            } else {
                try {
                    // Aktuelles Passwort verifizieren
                    $row = $db->get_var(
                        "SELECT password FROM {$prefix}users WHERE id = ?",
                        [(int)$currentUser->id]
                    );
                    if (!$row || !password_verify($currentPw, (string)$row)) {
                        $error = 'Das aktuelle Passwort ist falsch.';
                    } else {
                        $hash = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
                        $db->execute(
                            "UPDATE {$prefix}users SET password = ?, updated_at = NOW() WHERE id = ?",
                            [$hash, (int)$currentUser->id]
                        );
                        $success = 'Passwort wurde erfolgreich geändert.';
                    }
                } catch (\Throwable $e) {
                    $error = 'Fehler beim Speichern. Bitte erneut versuchen.';
                }
            }
        }
    }
    // Neues CSRF-Token nach POST
    $csrfToken = \CMS\Security::instance()->generateToken('member_security');
}

// Aktive Sessions laden (optional, wenn Tabelle vorhanden)
$sessions = [];
try {
    $hasSessions = (bool)$db->getPdo()->query("SELECT 1 FROM `{$prefix}user_sessions` LIMIT 1")->fetch();
    if ($hasSessions) {
        $sessions = array_map(
            static fn($session) => (array) $session,
            $db->get_results(
            "SELECT id, ip_address, user_agent, created_at, last_activity
             FROM {$prefix}user_sessions
             WHERE user_id = ?
             ORDER BY last_activity DESC
             LIMIT 10",
            [(int)$currentUser->id]
        ) ?: []
        );
    }
} catch (\Throwable $e) {}

$sessionCount = count($sessions);
$lastActivity = $sessionCount > 0 ? (string) ($sessions[0]['last_activity'] ?? '') : '';
$securityTips = [
    'Mindestens 12 Zeichen mit Groß-/Kleinbuchstaben, Zahl und Sonderzeichen nutzen.',
    'Passwort nicht an anderer Stelle wiederverwenden.',
    'Nach sensiblen Änderungen aktive Sitzungen prüfen und alte Geräte ausloggen.',
];

include $themeDir . 'header.php';
?>
<div class="container member-container">
    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <div class="member-main" id="main-content">

        <section class="member-security-hero" data-anim>
            <div class="member-security-hero__content">
                <span class="member-security-hero__eyebrow">🔒 Sicherheitscenter</span>
                <h1>Sicherheit & Sitzungen</h1>
                <p>Verwalte dein Passwort, prüfe zuletzt aktive Geräte und halte dein Konto mit wenigen Schritten sauber abgesichert.</p>
            </div>
            <div class="member-security-hero__stats">
                <div class="member-security-stat">
                    <span class="member-security-stat__label">Aktive Sitzungen</span>
                    <strong><?php echo $sessionCount; ?></strong>
                </div>
                <div class="member-security-stat">
                    <span class="member-security-stat__label">Zuletzt aktiv</span>
                    <strong><?php echo htmlspecialchars($lastActivity !== '' ? date('d.m.Y H:i', strtotime($lastActivity)) : 'Gerade eben', ENT_QUOTES); ?></strong>
                </div>
            </div>
        </section>

        <?php if ($success): ?>
        <div class="member-alert member-alert-success" data-anim data-anim-delay="1"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="member-alert member-alert-error" data-anim data-anim-delay="1"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>

        <div class="member-grid-2 member-grid-2--security" data-anim data-anim-delay="1.5">
            <div class="member-card member-card--security-form">
                <div class="member-card-header">
                    <h3>🔑 Passwort ändern</h3>
                </div>

                <form method="POST" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security" class="member-security-form">
                    <input type="hidden" name="action_security" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

                    <div class="member-form-group">
                        <label for="current_password" class="member-label">Aktuelles Passwort <span class="req">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="member-input" autocomplete="current-password" required>
                    </div>

                    <div class="member-form-group">
                        <label for="new_password" class="member-label">Neues Passwort <span class="req">*</span></label>
                        <input type="password" id="new_password" name="new_password" class="member-input" autocomplete="new-password" minlength="12" required>
                        <p class="member-form-hint">Mindestens 12 Zeichen, Groß-/Kleinbuchstaben, Zahl und Sonderzeichen.</p>
                    </div>

                    <div class="member-form-group">
                        <label for="new_password_repeat" class="member-label">Neues Passwort wiederholen <span class="req">*</span></label>
                        <input type="password" id="new_password_repeat" name="new_password_repeat" class="member-input" autocomplete="new-password" minlength="12" required>
                    </div>

                    <div class="member-actions member-actions--compact">
                        <button type="submit" class="btn btn-primary">🔒 Passwort ändern</button>
                    </div>
                </form>
            </div>

            <div class="member-card member-card--security-side">
                <div class="member-card-header">
                    <h3>🛡️ Schnellcheck</h3>
                </div>

                <ul class="member-security-checklist">
                    <?php foreach ($securityTips as $tip): ?>
                    <li><?php echo htmlspecialchars($tip, ENT_QUOTES); ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="member-security-note">
                    <strong>Hinweis:</strong>
                    <p>Wenn dir ein Gerät unbekannt vorkommt, ändere sofort dein Passwort und überprüfe offene Browser-Sitzungen.</p>
                </div>
            </div>
        </div>

        <div class="member-card member-card--spaced" data-anim data-anim-delay="2">
            <div class="member-card-header">
                <h3>📱 Aktive Sitzungen</h3>
            </div>

            <?php if (!empty($sessions)): ?>
            <div class="member-session-list">
                <?php foreach ($sessions as $session):
                    $ua = (string) ($session['user_agent'] ?? 'Unbekanntes Gerät');
                    $uaLabel = mb_strlen($ua) > 70 ? mb_substr($ua, 0, 70) . '…' : $ua;
                ?>
                <article class="member-session-item">
                    <div class="member-session-item__main">
                        <strong><?php echo htmlspecialchars($uaLabel, ENT_QUOTES); ?></strong>
                        <span><?php echo htmlspecialchars((string) ($session['ip_address'] ?? '–'), ENT_QUOTES); ?></span>
                    </div>
                    <div class="member-session-item__meta">
                        <span>Erstellt: <?php echo htmlspecialchars(!empty($session['created_at']) ? date('d.m.Y H:i', strtotime((string) $session['created_at'])) : '–', ENT_QUOTES); ?></span>
                        <span>Zuletzt aktiv: <?php echo htmlspecialchars(!empty($session['last_activity']) ? date('d.m.Y H:i', strtotime((string) $session['last_activity'])) : '–', ENT_QUOTES); ?></span>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="member-empty">
                <p>📭 Zurzeit wurden keine zusätzlichen aktiven Sitzungen gefunden.</p>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include $themeDir . 'footer.php'; ?>
