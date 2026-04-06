<?php
/**
 * Registrierung Template – CMS Phinit Theme
 *
 * POST /register wird vom CMS Router (Router::handleRegister) verarbeitet.
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Bereits eingeloggt → weiterleiten
if (function_exists('theme_is_logged_in') && theme_is_logged_in()) {
    header('Location: /member');
    exit;
}

// Flash-Messages
$regError   = $_SESSION['error']   ?? '';
$regSuccess = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

$csrfToken = '';
try {
    $csrfToken = \CMS\Security::instance()->generateToken('register');
} catch (\Throwable $e) {}

$siteUrl   = SITE_URL;
$siteTitle = defined('SITE_NAME') ? SITE_NAME : '365CMS';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$homeUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/', $currentLocale, '')
    : '/';
$registerAction = function_exists('phinit_localized_href')
    ? phinit_localized_href('/register', $currentLocale, '')
    : '/register';
$privacyUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/datenschutz', $currentLocale, '')
    : '/datenschutz';
$loginUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/login', $currentLocale, '')
    : '/login';
?>

<div class="auth-wrapper">
    <div class="auth-card auth-card--narrow">

        <!-- Logo -->
        <div class="auth-header">
            <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES); ?>" class="auth-logo-link">
                <span class="logo-icon" aria-hidden="true">P</span>
                <span class="auth-site-name"><?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?></span>
            </a>
            <p class="auth-subtitle">Erstelle dein kostenloses Konto</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($regError): ?>
        <div class="auth-alert auth-alert--error" role="alert">
            ❌ <?php echo htmlspecialchars($regError, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <?php if ($regSuccess): ?>
        <div class="auth-alert auth-alert--success" role="alert">
            ✅ <?php echo htmlspecialchars($regSuccess, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <!-- Registrierungsformular -->
        <form method="POST" action="<?php echo htmlspecialchars($registerAction, ENT_QUOTES); ?>" novalidate class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

            <div class="auth-form-group">
                <label for="regEmail" class="auth-label">E-Mail-Adresse</label>
                <input type="email" id="regEmail" name="email" class="auth-input"
                       autocomplete="email" required autofocus
                       placeholder="deine@email.de">
            </div>

            <div class="auth-form-group">
                <label for="regUsername" class="auth-label">Benutzername</label>
                <input type="text" id="regUsername" name="username" class="auth-input"
                       autocomplete="username" required minlength="3" maxlength="40"
                       pattern="[a-zA-Z0-9_\-.]+"
                       placeholder="mein_name">
                <small class="auth-helper">Nur Buchstaben, Ziffern, Bindestrich, Punkt und Unterstrich</small>
            </div>

            <div class="auth-form-row">
                <div class="auth-form-group">
                    <label for="regPassword" class="auth-label">Passwort</label>
                    <input type="password" id="regPassword" name="password" class="auth-input"
                           autocomplete="new-password" required minlength="12"
                           placeholder="••••••••••••">
                </div>
                <div class="auth-form-group">
                    <label for="regPasswordConfirm" class="auth-label">Passwort bestätigen</label>
                    <input type="password" id="regPasswordConfirm" name="password_confirm" class="auth-input"
                           autocomplete="new-password" required minlength="12"
                           placeholder="••••••••••••">
                </div>
            </div>
            <small class="auth-helper auth-helper--tight">Mind. 12 Zeichen, Groß-/Kleinbuchstaben, Zahl und Sonderzeichen</small>

            <div class="auth-form-group">
                <label for="regName" class="auth-label">Anzeigename <span class="field-optional">(optional)</span></label>
                <input type="text" id="regName" name="display_name" class="auth-input"
                       autocomplete="name" maxlength="100"
                       placeholder="Dein Name">
            </div>

            <div class="auth-remember">
                <label class="auth-checkbox-label">
                    <input type="checkbox" name="accept_terms" value="1" required>
                    <span>Ich akzeptiere die <a href="<?php echo htmlspecialchars($privacyUrl, ENT_QUOTES); ?>" target="_blank" rel="noopener">Datenschutzerklärung</a></span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">📝 Konto erstellen</button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Bereits registriert? <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES); ?>">Jetzt anmelden</a></p>
        </div>

    </div>
</div>
