<?php
/**
 * Login Template – CMS Phinit Theme
 *
 * POST /login wird vom CMS Router (Router::handleLogin) verarbeitet.
 * Fehler/Erfolg kommen via $_SESSION['error'] / $_SESSION['success'] zurück.
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Bereits eingeloggt → weiterleiten
if (function_exists('theme_is_logged_in') && theme_is_logged_in()) {
    header('Location: ' . SITE_URL . '/member');
    exit;
}

// Flash-Messages
$_flash       = function_exists('theme_get_flash') ? theme_get_flash() : null;
$loginError   = ($_flash && ($_flash['type'] ?? '') === 'error')   ? $_flash['message'] : ($_SESSION['error']   ?? '');
$loginSuccess = ($_flash && ($_flash['type'] ?? '') === 'success') ? $_flash['message'] : ($_SESSION['success'] ?? '');
unset($_SESSION['error'], $_SESSION['success']);

$csrfToken = '';
try {
    $csrfToken = \CMS\Security::instance()->generateToken('login');
} catch (\Throwable $e) {}

$siteUrl   = SITE_URL;
$siteTitle = defined('SITE_NAME') ? SITE_NAME : '365CMS';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$forgotPasswordUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/forgot-password', $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/forgot-password';
$registerUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/register', $currentLocale, $siteUrl)
    : rtrim($siteUrl, '/') . '/register';
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-header">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="auth-logo-link">
                <span class="logo-icon" aria-hidden="true">P</span>
                <span class="auth-site-name"><?php echo htmlspecialchars($siteTitle, ENT_QUOTES); ?></span>
            </a>
            <p class="auth-subtitle">Melde dich mit deinem Konto an</p>
        </div>

        <!-- Flash Messages -->
        <?php if ($loginError && trim($loginError) !== ''): ?>
        <div class="auth-alert auth-alert--error" role="alert">
            ❌ <?php echo htmlspecialchars($loginError, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <?php if ($loginSuccess && trim($loginSuccess) !== ''): ?>
        <div class="auth-alert auth-alert--success" role="alert">
            ✅ <?php echo htmlspecialchars($loginSuccess, ENT_QUOTES); ?>
        </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/login" novalidate class="auth-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES); ?>">

            <div class="auth-form-group">
                <label for="loginEmail" class="auth-label">E-Mail-Adresse</label>
                <input type="email" id="loginEmail" name="email" class="auth-input"
                       autocomplete="email" required autofocus
                       placeholder="deine@email.de">
            </div>

            <div class="auth-form-group">
                <label for="loginPassword" class="auth-label">
                    Passwort
                    <a href="<?php echo htmlspecialchars($forgotPasswordUrl, ENT_QUOTES); ?>" class="auth-forgot-link">Vergessen?</a>
                </label>
                <input type="password" id="loginPassword" name="password" class="auth-input"
                       autocomplete="current-password" required minlength="8"
                       placeholder="••••••••">
            </div>

            <div class="auth-remember">
                <label class="auth-checkbox-label">
                    <input type="checkbox" name="remember" value="1">
                    <span>Angemeldet bleiben</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary auth-submit">🔑 Anmelden</button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Noch kein Konto? <a href="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES); ?>">Jetzt registrieren</a></p>
        </div>

    </div>
</div>
