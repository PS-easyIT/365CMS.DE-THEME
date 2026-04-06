<?php
/**
 * Passwort-zurücksetzen Template – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('theme_is_logged_in') && theme_is_logged_in()) {
    header('Location: /member');
    exit;
}

$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$isEnglish = function_exists('phinit_is_english_locale') ? phinit_is_english_locale($currentLocale) : false;
$siteUrl = SITE_URL;
$siteTitle = defined('SITE_NAME') ? SITE_NAME : '365CMS';
$loginUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/login', $currentLocale, '')
    : '/login';
$forgotPasswordUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/forgot-password', $currentLocale, '')
    : '/forgot-password';
$homeUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/', $currentLocale, '')
    : '/';

$step = (($_GET['step'] ?? 'request') === 'reset') ? 'reset' : 'request';
$resetToken = trim((string) ($_GET['token'] ?? ''));
$resetTokenEscaped = htmlspecialchars($resetToken, ENT_QUOTES);
$fpError = '';
$fpSuccess = '';
$fpEmail = '';

$messages = [
    'request_title' => $isEnglish ? 'Reset password' : 'Passwort zurücksetzen',
    'request_subtitle' => $isEnglish
        ? 'Enter your email address and we will send you a reset link.'
        : 'Gib deine E-Mail-Adresse ein – wir senden dir einen Reset-Link.',
    'request_button' => $isEnglish ? 'Send reset link' : 'Reset-Link senden',
    'reset_title' => $isEnglish ? 'Choose a new password' : 'Neues Passwort',
    'reset_subtitle' => $isEnglish
        ? 'Set a new secure password for your account.'
        : 'Lege ein neues sicheres Passwort fest.',
    'reset_button' => $isEnglish ? 'Change password' : 'Passwort ändern',
    'done_title' => $isEnglish ? 'Password updated' : 'Passwort geändert',
    'done_subtitle' => $isEnglish
        ? 'You can now sign in with your new password.'
        : 'Du kannst dich jetzt mit deinem neuen Passwort anmelden.',
    'done_button' => $isEnglish ? 'Go to login' : 'Jetzt anmelden',
    'email_label' => $isEnglish ? 'Email address' : 'E-Mail-Adresse',
    'password_label' => $isEnglish ? 'New password' : 'Neues Passwort',
    'password_confirm_label' => $isEnglish ? 'Repeat password' : 'Passwort wiederholen',
    'password_hint' => $isEnglish
        ? 'At least 12 characters, including upper/lowercase, number and special character.'
        : 'Mindestens 12 Zeichen, inklusive Groß-/Kleinbuchstaben, Zahl und Sonderzeichen.',
    'back_login' => $isEnglish ? '← Back to login' : '← Zurück zur Anmeldung',
    'back_home' => $isEnglish ? '← Back to homepage' : '← Zurück zur Startseite',
    'request_success' => $isEnglish
        ? 'If an account with this email exists, we have sent a reset link.'
        : 'Falls ein Konto mit dieser E-Mail-Adresse existiert, haben wir dir einen Reset-Link gesendet.',
    'csrf_error' => $isEnglish
        ? 'Security check failed. Please reload the page and try again.'
        : 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden und erneut versuchen.',
    'email_error' => $isEnglish
        ? 'Please enter a valid email address.'
        : 'Bitte eine gültige E-Mail-Adresse eingeben.',
    'request_error' => $isEnglish
        ? 'The request could not be processed right now. Please try again later.'
        : 'Die Anfrage konnte gerade nicht verarbeitet werden. Bitte später erneut versuchen.',
    'reset_invalid' => $isEnglish
        ? 'The reset link is invalid or has already been used.'
        : 'Der Reset-Link ist ungültig oder wurde bereits verwendet.',
    'reset_expired' => $isEnglish
        ? 'This reset link has expired. Please start again.'
        : 'Dieser Reset-Link ist abgelaufen. Bitte starte den Vorgang erneut.',
    'password_length_error' => $isEnglish
        ? 'The password must contain at least 12 characters.'
        : 'Das Passwort muss mindestens 12 Zeichen lang sein.',
    'password_policy_error' => $isEnglish
        ? 'Please use upper/lowercase letters, a number and a special character.'
        : 'Bitte verwende Groß-/Kleinbuchstaben, eine Zahl und ein Sonderzeichen.',
    'password_match_error' => $isEnglish
        ? 'The passwords do not match.'
        : 'Die Passwörter stimmen nicht überein.',
    'reset_success' => $isEnglish
        ? 'Your password has been updated successfully.'
        : 'Dein Passwort wurde erfolgreich geändert.',
    'reset_error' => $isEnglish
        ? 'The password could not be updated. Please try again.'
        : 'Das Passwort konnte nicht aktualisiert werden. Bitte versuche es erneut.',
    'mail_subject' => $isEnglish ? '[' . $siteTitle . '] Reset your password' : '[' . $siteTitle . '] Passwort zurücksetzen',
    'mail_body' => $isEnglish
        ? "Hello,\n\nyou requested a password reset for your account.\n\nUse the following link within the next 60 minutes:\n{reset_url}\n\nIf you did not request this, you can ignore this email.\n\nBest regards,\n{site_name}"
        : "Hallo,\n\ndu hast eine Anfrage zum Zurücksetzen deines Passworts gestellt.\n\nNutze den folgenden Link innerhalb der nächsten 60 Minuten:\n{reset_url}\n\nFalls du diese Anfrage nicht gestellt hast, kannst du diese E-Mail ignorieren.\n\nViele Grüße\n{site_name}",
];

$csrfToken = '';
try {
    $csrfToken = \CMS\Security::instance()->generateToken('forgot_password');
} catch (\Throwable) {
}

$isStrongPassword = static function (string $password): bool {
    return strlen($password) >= 12
        && preg_match('/[A-Z]/', $password) === 1
        && preg_match('/[a-z]/', $password) === 1
        && preg_match('/\d/', $password) === 1
        && preg_match('/[^A-Za-z0-9]/', $password) === 1;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fp_submit'])) {
    $csrfOk = true;

    try {
        $csrfOk = \CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'forgot_password');
    } catch (\Throwable) {
        $csrfOk = false;
    }

    if (!$csrfOk) {
        $fpError = $messages['csrf_error'];
    } else {
        $fpEmail = filter_var(trim((string) ($_POST['fp_email'] ?? '')), FILTER_VALIDATE_EMAIL) ?: '';

        if ($fpEmail === '') {
            $fpError = $messages['email_error'];
        } else {
            try {
                $db = \CMS\Database::instance();
                $prefix = $db->getPrefix();
                $userRow = $db->execute("SELECT id FROM {$prefix}users WHERE email = ? LIMIT 1", [$fpEmail])->fetch();

                if ($userRow) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', time() + 3600);
                    $db->execute("DELETE FROM {$prefix}password_resets WHERE email = ?", [$fpEmail]);
                    $db->execute(
                        "INSERT INTO {$prefix}password_resets (email, token, expires_at, created_at) VALUES (?, ?, ?, NOW())",
                        [$fpEmail, hash('sha256', $token), $expires]
                    );

                    $resetUrl = $forgotPasswordUrl . '?step=reset&token=' . rawurlencode($token);
                    $plainBody = strtr($messages['mail_body'], [
                        '{reset_url}' => $resetUrl,
                        '{site_name}' => $siteTitle,
                    ]);

                    if (class_exists('\\CMS\\Services\\MailService')) {
                        \CMS\Services\MailService::getInstance()->sendPlain(
                            $fpEmail,
                            $messages['mail_subject'],
                            $plainBody,
                            ['source' => 'auth-password-reset']
                        );
                    }
                }

                $fpSuccess = $messages['request_success'];
            } catch (\Throwable) {
                $fpError = $messages['request_error'];
            }
        }
    }

    try {
        $csrfToken = \CMS\Security::instance()->generateToken('forgot_password');
    } catch (\Throwable) {
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_submit'])) {
    $csrfOk = true;

    try {
        $csrfOk = \CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'forgot_password');
    } catch (\Throwable) {
        $csrfOk = false;
    }

    if (!$csrfOk) {
        $fpError = $messages['csrf_error'];
    } else {
        $token = trim((string) ($_POST['reset_token'] ?? ''));
        $password1 = (string) ($_POST['new_password'] ?? '');
        $password2 = (string) ($_POST['new_password2'] ?? '');

        if (strlen($password1) < 12) {
            $fpError = $messages['password_length_error'];
        } elseif (!$isStrongPassword($password1)) {
            $fpError = $messages['password_policy_error'];
        } elseif ($password1 !== $password2) {
            $fpError = $messages['password_match_error'];
        } elseif ($token === '') {
            $fpError = $messages['reset_invalid'];
        } else {
            try {
                $db = \CMS\Database::instance();
                $prefix = $db->getPrefix();
                $hashedToken = hash('sha256', $token);
                $row = $db->execute(
                    "SELECT email, expires_at FROM {$prefix}password_resets WHERE token = ? LIMIT 1",
                    [$hashedToken]
                )->fetch();

                if (!$row) {
                    $fpError = $messages['reset_invalid'];
                } elseif (strtotime((string) ($row->expires_at ?? '')) < time()) {
                    $fpError = $messages['reset_expired'];
                } else {
                    $hash = password_hash($password1, PASSWORD_BCRYPT);
                    $db->execute("UPDATE {$prefix}users SET password = ? WHERE email = ?", [$hash, $row->email]);
                    $db->execute("DELETE FROM {$prefix}password_resets WHERE email = ?", [$row->email]);
                    $fpSuccess = $messages['reset_success'];
                    $step = 'done';
                }
            } catch (\Throwable) {
                $fpError = $messages['reset_error'];
            }
        }
    }
}
?>

<div class="auth-wrapper">
    <div class="auth-card auth-card--narrow">
        <div class="auth-header">
            <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES); ?>" class="auth-logo-link">
                <span class="logo-icon" aria-hidden="true">P</span>
                <span class="auth-site-name"><?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?></span>
            </a>
            <p class="auth-subtitle">
                <?php echo htmlspecialchars($step === 'reset' ? $messages['reset_subtitle'] : ($step === 'done' ? $messages['done_subtitle'] : $messages['request_subtitle']), ENT_QUOTES); ?>
            </p>
        </div>

        <?php if ($fpError !== ''): ?>
        <div class="auth-alert auth-alert--error" role="alert">
            ❌ <?php echo htmlspecialchars($fpError, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <?php if ($fpSuccess !== '' && $step !== 'done' && $step !== 'reset'): ?>
        <div class="auth-alert auth-alert--success" role="status">
            ✅ <?php echo htmlspecialchars($fpSuccess, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <?php if ($step === 'done'): ?>
        <div class="auth-copy-block">
            <h1 class="auth-title"><?php echo htmlspecialchars($messages['done_title'], ENT_QUOTES); ?></h1>
            <p class="auth-copy-block__text"><?php echo htmlspecialchars($fpSuccess !== '' ? $fpSuccess : $messages['done_subtitle'], ENT_QUOTES); ?></p>
            <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES); ?>" class="btn btn-primary auth-submit auth-submit--link"><?php echo htmlspecialchars($messages['done_button'], ENT_QUOTES); ?></a>
        </div>
        <?php elseif ($step === 'reset' && $resetToken !== ''): ?>
        <h1 class="auth-title"><?php echo htmlspecialchars($messages['reset_title'], ENT_QUOTES); ?></h1>

        <form method="POST" action="<?php echo htmlspecialchars($forgotPasswordUrl, ENT_QUOTES); ?>" novalidate class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="reset_submit" value="1">
            <input type="hidden" name="reset_token" value="<?php echo $resetTokenEscaped; ?>">

            <div class="auth-form-group">
                <label for="newPassword" class="auth-label"><?php echo htmlspecialchars($messages['password_label'], ENT_QUOTES); ?></label>
                <input type="password" id="newPassword" name="new_password" class="auth-input"
                       autocomplete="new-password" required minlength="12" autofocus
                       placeholder="••••••••••••">
            </div>

            <div class="auth-form-group">
                <label for="newPasswordConfirm" class="auth-label"><?php echo htmlspecialchars($messages['password_confirm_label'], ENT_QUOTES); ?></label>
                <input type="password" id="newPasswordConfirm" name="new_password2" class="auth-input"
                       autocomplete="new-password" required minlength="12"
                       placeholder="••••••••••••">
            </div>

            <small class="auth-helper auth-helper--tight"><?php echo htmlspecialchars($messages['password_hint'], ENT_QUOTES); ?></small>
            <button type="submit" class="btn btn-primary auth-submit"><?php echo htmlspecialchars($messages['reset_button'], ENT_QUOTES); ?></button>
        </form>
        <?php else: ?>
        <h1 class="auth-title"><?php echo htmlspecialchars($messages['request_title'], ENT_QUOTES); ?></h1>

        <?php if ($fpSuccess === ''): ?>
        <form method="POST" action="<?php echo htmlspecialchars($forgotPasswordUrl, ENT_QUOTES); ?>" novalidate class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">
            <input type="hidden" name="fp_submit" value="1">

            <div class="auth-form-group">
                <label for="fpEmail" class="auth-label"><?php echo htmlspecialchars($messages['email_label'], ENT_QUOTES); ?></label>
                <input type="email" id="fpEmail" name="fp_email" class="auth-input"
                       value="<?php echo htmlspecialchars($fpEmail, ENT_QUOTES); ?>"
                       autocomplete="email" required autofocus
                       placeholder="deine@email.de">
            </div>

            <button type="submit" class="btn btn-primary auth-submit"><?php echo htmlspecialchars($messages['request_button'], ENT_QUOTES); ?></button>
        </form>
        <?php endif; ?>
        <?php endif; ?>

        <div class="auth-footer">
            <p><a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($messages['back_login'], ENT_QUOTES); ?></a></p>
            <p><a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($messages['back_home'], ENT_QUOTES); ?></a></p>
        </div>
    </div>
</div>