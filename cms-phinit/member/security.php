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
        $sessions = $db->get_results(
            "SELECT id, ip_address, user_agent, created_at, last_activity
             FROM {$prefix}user_sessions
             WHERE user_id = ?
             ORDER BY last_activity DESC
             LIMIT 10",
            [(int)$currentUser->id]
        );
    }
} catch (\Throwable $e) {}
?>
<div class="member-layout">

    <?php include __DIR__ . '/partials/member-nav.php'; ?>

    <main class="member-main" id="main-content">

        <div class="member-page-header">
            <h1>🔒 Sicherheit</h1>
            <p>Passwort ändern und Anmelde-Sitzungen verwalten.</p>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>

        <!-- Passwort ändern -->
        <div class="member-card">
            <h3>🔑 Passwort ändern</h3>

            <form method="POST" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/member/security" class="member-form">
                <input type="hidden" name="action_security" value="change_password">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

                <div class="form-group">
                    <label for="current_password" class="form-label">Aktuelles Passwort <span class="field-required">*</span></label>
                    <input type="password" id="current_password" name="current_password"
                           class="form-control" autocomplete="current-password" required>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Neues Passwort <span class="field-required">*</span></label>
                    <input type="password" id="new_password" name="new_password"
                           class="form-control" autocomplete="new-password"
                           minlength="12" required>
                    <small class="form-text">Mindestens 12 Zeichen, Groß-/Kleinbuchstaben, Zahl und Sonderzeichen.</small>
                </div>

                <div class="form-group">
                    <label for="new_password_repeat" class="form-label">Neues Passwort wiederholen <span class="field-required">*</span></label>
                    <input type="password" id="new_password_repeat" name="new_password_repeat"
                           class="form-control" autocomplete="new-password"
                           minlength="12" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">🔒 Passwort ändern</button>
                </div>
            </form>
        </div>

        <?php if (!empty($sessions)): ?>
        <!-- Aktive Sitzungen -->
        <div class="member-card member-card--spaced">
            <h3>📱 Aktive Sitzungen</h3>
            <div class="users-table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>IP-Adresse</th>
                            <th>Browser / Gerät</th>
                            <th>Zuletzt aktiv</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $session):
                            $s = is_object($session) ? (array)$session : (array)$session;
                            $ua = $s['user_agent'] ?? '';
                            // Kurzes UA-Label
                            $uaLabel = mb_strlen($ua) > 60 ? mb_substr($ua, 0, 60) . '…' : $ua;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['ip_address'] ?? '–', ENT_QUOTES); ?></td>
                            <td title="<?php echo htmlspecialchars($ua, ENT_QUOTES); ?>"><?php echo htmlspecialchars($uaLabel, ENT_QUOTES); ?></td>
                            <td><?php echo htmlspecialchars(
                                !empty($s['last_activity']) ? date('j. M Y H:i', strtotime($s['last_activity'])) : '–',
                                ENT_QUOTES
                            ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>
