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
    header('Location: /member');
    exit;
}

// Flash-Messages
$themeGetFlashAvailable = function_exists('theme_get_flash');
$loginError = $themeGetFlashAvailable
    ? trim((string) theme_get_flash('error'))
    : trim((string) ($_SESSION['error'] ?? ''));
$loginSuccess = $themeGetFlashAvailable
    ? trim((string) theme_get_flash('success'))
    : trim((string) ($_SESSION['success'] ?? ''));

if (!$themeGetFlashAvailable) {
    unset($_SESSION['error'], $_SESSION['success']);
}

$siteUrl   = SITE_URL;
$siteTitle = defined('SITE_NAME') ? SITE_NAME : '365CMS';
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';
$siteBase = rtrim((string) $siteUrl, '/');
$homeUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/', $currentLocale, $siteUrl)
    : ($siteBase !== '' ? $siteBase . '/' : '/');
$loginAction = function_exists('phinit_localized_href')
    ? phinit_localized_href('/login', $currentLocale, $siteUrl)
    : ($siteBase !== '' ? $siteBase . '/login' : '/login');
$forgotPasswordUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/forgot-password', $currentLocale, $siteUrl)
    : ($siteBase !== '' ? $siteBase . '/forgot-password' : '/forgot-password');
$registerUrl = function_exists('phinit_localized_href')
    ? phinit_localized_href('/register', $currentLocale, $siteUrl)
    : ($siteBase !== '' ? $siteBase . '/register' : '/register');
$loginRedirect = trim((string) ($login_redirect ?? ''));
$loginValue = trim((string)($_POST['username'] ?? $_POST['email'] ?? ''));
?>

<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Logo -->
        <div class="auth-header">
            <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES); ?>" class="auth-logo-link">
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
        <form method="POST" action="<?php echo htmlspecialchars($loginAction, ENT_QUOTES); ?>" novalidate class="auth-form">
            <?php theme_csrf_field('login'); ?>

            <div class="auth-form-group">
                <label for="loginUsername" class="auth-label">Benutzername oder E-Mail-Adresse</label>
                <input type="text" id="loginUsername" name="username" class="auth-input"
                       autocomplete="username" required autofocus
                       value="<?php echo htmlspecialchars($loginValue, ENT_QUOTES); ?>"
                       placeholder="deinname oder deine@email.de">
            </div>

            <div class="auth-form-group">
                <label for="loginPassword" class="auth-label">
                    Passwort
                    <a href="<?php echo htmlspecialchars($forgotPasswordUrl, ENT_QUOTES); ?>" class="auth-forgot-link">Vergessen?</a>
                </label>
                <input type="password" id="loginPassword" name="password" class="auth-input"
                       autocomplete="current-password" required
                       placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary auth-submit">Anmelden</button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            <p>Noch kein Konto? <a href="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES); ?>">Jetzt registrieren</a></p>
        </div>

    </div>
</div>
