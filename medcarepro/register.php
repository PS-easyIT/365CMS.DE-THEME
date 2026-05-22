<?php
/**
 * Registrierung – MedCare Pro Theme
 *
 * Unterstützt Patienten- und Arzt-Registrierung via GET-Parameter ?type=doctor
 *
 * @package MedCarePro_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (theme_is_logged_in()) {
    header('Location: ' . theme_route_url('member'));
    exit;
}

$isDoctor       = ($_GET['type'] ?? '') === 'doctor';
$error          = null;
$success        = null;
$safe           = static fn(mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$postedEmail    = filter_var((string) ($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$postedUsername = trim(strip_tags((string) ($_POST['username'] ?? '')));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_register'])) {
    try {
        if (empty($_POST['honeypot_field'])) {
            if (!\CMS\Security::instance()->verifyToken((string) ($_POST['csrf_token'] ?? ''), 'mc_register')) {
                $error = 'Sicherheitscheck fehlgeschlagen. Bitte laden Sie die Seite neu.';
            } else {
                $email    = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
                $username = trim(strip_tags((string) ($_POST['username'] ?? '')));
                $password = (string) ($_POST['password'] ?? '');
                $passConf = (string) ($_POST['password_confirm'] ?? '');
                $privacyAgreed = !empty($_POST['privacy']);
                $regType  = (($_POST['register_type'] ?? 'patient') === 'doctor') ? 'doctor' : 'patient';

                if (!$email) {
                    $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
                } elseif (strlen($username) < 3) {
                    $error = 'Der Benutzername muss mindestens 3 Zeichen lang sein.';
                } elseif (strlen($password) < 8) {
                    $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
                } elseif ($password !== $passConf) {
                    $error = 'Die Passwörter stimmen nicht überein.';
                } elseif (!$privacyAgreed) {
                    $error = 'Bitte akzeptieren Sie die Datenschutzerklärung.';
                } else {
                    $result = \CMS\Auth::instance()->register($email, $username, $password, ['role' => $regType]);
                    if ($result === true) {
                        $success = 'Registrierung erfolgreich! Sie können sich jetzt anmelden.';
                    } else {
                        $error = is_string($result) ? $result : 'Registrierung fehlgeschlagen. Bitte versuchen Sie es erneut.';
                    }
                }
            }
        }
    } catch (\Throwable) {
        $error = 'Registrierung fehlgeschlagen. Bitte versuchen Sie es erneut.';
    }
}

try {
    $csrfToken = \CMS\Security::instance()->generateToken('mc_register');
} catch (\Throwable) {
    $csrfToken = '';
}

$privText   = (string) mc_get_setting('dsgvo_medical', 'privacy_form_text', 'Ihre Daten werden gemäß DSGVO und § 203 StGB vertraulich behandelt.');
$loginUrl   = $safe(theme_route_url('login'));
$privacyUrl = $safe(theme_route_url('privacy'));

get_header();
?>
<main id="main" class="mc-main mc-auth-page" role="main">
    <div class="mc-container">
        <div class="mc-form-card mc-auth-card mc-auth-card--wide">

            <div class="mc-auth-card__head">
                <span class="mc-auth-card__icon" aria-hidden="true">
                    <?php if ($isDoctor) : ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                             focusable="false" aria-hidden="true">
                            <circle cx="12" cy="7" r="3"/>
                            <path d="M5 21c0-3.866 3.134-7 7-7s7 3.134 7 7"/>
                            <path d="M16 14h2v3h-2zM16 17h2v3a1 1 0 0 1-1 1 1 1 0 0 1-1-1z"/>
                        </svg>
                    <?php else : ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                             focusable="false" aria-hidden="true">
                            <path d="M10 4h4v4h4v4h-4v4h-4v-4H6V8h4z"/>
                            <path d="M4 21V12a8 8 0 0 1 16 0v9"/>
                        </svg>
                    <?php endif; ?>
                </span>
                <h1 class="mc-auth-card__title">
                    <?php echo $isDoctor ? 'Als Arzt registrieren' : 'Konto erstellen'; ?>
                </h1>
                <p class="mc-auth-card__lead">
                    <?php echo $isDoctor
                        ? 'Erstellen Sie Ihr Arztprofil und erreichen Sie neue Patienten.'
                        : 'Kostenlos anmelden und Ärzte finden.'; ?>
                </p>
            </div>

            <div class="mc-auth-toggle" role="tablist" aria-label="Registrierungs-Typ">
                <a href="?type=patient"
                   class="mc-auth-toggle__btn<?php echo !$isDoctor ? ' is-active' : ''; ?>"
                   role="tab"
                   aria-selected="<?php echo !$isDoctor ? 'true' : 'false'; ?>">
                    Patient
                </a>
                <a href="?type=doctor"
                   class="mc-auth-toggle__btn<?php echo $isDoctor ? ' is-active' : ''; ?>"
                   role="tab"
                   aria-selected="<?php echo $isDoctor ? 'true' : 'false'; ?>">
                    Arzt / Therapeut
                </a>
            </div>

            <?php if (!empty($error)) : ?>
            <div class="mc-alert mc-alert-error" role="alert"><?php echo $safe($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
            <div class="mc-alert mc-alert-success" role="status">
                <?php echo $safe($success); ?><br>
                <a href="<?php echo $loginUrl; ?>" class="mc-alert-link">Jetzt anmelden →</a>
            </div>
            <?php endif; ?>

            <?php if (empty($success)) : ?>
            <form method="POST" novalidate class="mc-auth-form">
                <input type="hidden" name="mc_register" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo $safe($csrfToken); ?>">
                <input type="hidden" name="register_type" value="<?php echo $isDoctor ? 'doctor' : 'patient'; ?>">

                <div class="mc-honeypot" aria-hidden="true">
                    <label for="honeypot_field">Dieses Feld leer lassen</label>
                    <input id="honeypot_field" type="text" name="honeypot_field" tabindex="-1" autocomplete="off">
                </div>

                <div class="mc-form-group">
                    <label for="reg-email" class="mc-label">
                        E-Mail-Adresse <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                    <input id="reg-email"
                           type="email"
                           name="email"
                           class="mc-input"
                           value="<?php echo $safe($postedEmail); ?>"
                           autocomplete="email"
                           required
                           aria-required="true"
                           placeholder="ihre@email.de">
                </div>

                <div class="mc-form-group">
                    <label for="reg-username" class="mc-label">
                        <?php echo $isDoctor ? 'Name / Praxisname' : 'Benutzername'; ?>
                        <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                    <input id="reg-username"
                           type="text"
                           name="username"
                           class="mc-input"
                           value="<?php echo $safe($postedUsername); ?>"
                           autocomplete="name"
                           required
                           aria-required="true"
                           placeholder="<?php echo $isDoctor ? 'Dr. med. Mustermann' : 'max_mustermann'; ?>"
                           minlength="3">
                </div>

                <div class="mc-form-group">
                    <label for="reg-password" class="mc-label">
                        Passwort <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                    <input id="reg-password"
                           type="password"
                           name="password"
                           class="mc-input"
                           autocomplete="new-password"
                           required
                           aria-required="true"
                           placeholder="Mindestens 8 Zeichen"
                           minlength="8">
                    <p class="mc-form-hint">Min. 8 Zeichen – nutzen Sie Groß-/Kleinbuchstaben und Zahlen.</p>
                </div>

                <div class="mc-form-group">
                    <label for="reg-password-confirm" class="mc-label">
                        Passwort bestätigen <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                    <input id="reg-password-confirm"
                           type="password"
                           name="password_confirm"
                           class="mc-input"
                           autocomplete="new-password"
                           required
                           aria-required="true"
                           placeholder="Passwort wiederholen">
                </div>

                <div class="mc-form-group mc-form-check">
                    <input id="reg-privacy"
                           type="checkbox"
                           name="privacy"
                           value="1"
                           required
                           aria-required="true"
                           class="mc-checkbox">
                    <label for="reg-privacy" class="mc-checkbox-label">
                        Ich habe die
                        <a href="<?php echo $privacyUrl; ?>" target="_blank" rel="noopener noreferrer">Datenschutzerklärung</a>
                        gelesen und stimme der Verarbeitung meiner Daten zu.
                        <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                </div>

                <button type="submit" class="mc-btn mc-btn-primary mc-btn-block">
                    <?php echo $isDoctor ? 'Arztprofil erstellen' : 'Konto erstellen'; ?>
                </button>
            </form>
            <?php endif; ?>

            <hr class="mc-form-divider">
            <div class="mc-form-links mc-form-links--single">
                <a href="<?php echo $loginUrl; ?>">Bereits registriert? Anmelden</a>
            </div>
            <p class="mc-dsgvo-note">
                <span aria-hidden="true">🔒</span> <?php echo $safe($privText); ?>
            </p>
        </div>
    </div>
</main>
<?php get_footer(); ?>
