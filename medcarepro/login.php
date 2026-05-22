<?php
/**
 * Login-Seite – MedCare Pro Theme
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

$error   = null;
$success = null;
$safe    = static fn(mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_login'])) {
    try {
        if (!\CMS\Security::instance()->verifyToken((string) ($_POST['csrf_token'] ?? ''), 'mc_login')) {
            $error = 'Sicherheitscheck fehlgeschlagen. Bitte laden Sie die Seite neu.';
        } else {
            $email    = filter_var(trim((string) ($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
            $password = (string) ($_POST['password'] ?? '');

            if (!$email || $password === '') {
                $error = 'Bitte E-Mail und Passwort eingeben.';
            } else {
                $result = \CMS\Auth::instance()->login($email, $password);
                if ($result === true) {
                    header('Location: ' . theme_route_url('member'));
                    exit;
                }
                $error = is_string($result) ? $result : 'Ungültige E-Mail-Adresse oder falsches Passwort.';
            }
        }
    } catch (\Throwable) {
        $error = 'Anmeldung fehlgeschlagen. Bitte versuchen Sie es erneut.';
    }
}

try {
    $csrfToken = \CMS\Security::instance()->generateToken('mc_login');
} catch (\Throwable) {
    $csrfToken = '';
}

$postedEmail   = filter_var((string) ($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$forgotUrl     = $safe(mc_href('/forgot-password'));
$registerUrl   = $safe(theme_route_url('register'));

get_header();
?>
<main id="main" class="mc-main mc-auth-page" role="main">
    <div class="mc-container">
        <div class="mc-form-card mc-auth-card">

            <div class="mc-auth-card__head">
                <span class="mc-auth-card__icon" aria-hidden="true">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                         focusable="false" aria-hidden="true">
                        <rect x="5" y="11" width="14" height="9" rx="2"/>
                        <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                    </svg>
                </span>
                <h1 class="mc-auth-card__title">Anmelden</h1>
                <p class="mc-auth-card__lead">Willkommen zurück bei MedCare Pro</p>
            </div>

            <?php if (!empty($error)) : ?>
            <div class="mc-alert mc-alert-error" role="alert"><?php echo $safe($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
            <div class="mc-alert mc-alert-success" role="status"><?php echo $safe($success); ?></div>
            <?php endif; ?>

            <form method="POST" novalidate class="mc-auth-form">
                <input type="hidden" name="mc_login" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo $safe($csrfToken); ?>">

                <div class="mc-form-group">
                    <label for="login-email" class="mc-label">
                        E-Mail-Adresse <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                    <input id="login-email"
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
                    <label for="login-password" class="mc-label">
                        Passwort <span class="mc-required" aria-hidden="true">*</span>
                    </label>
                    <input id="login-password"
                           type="password"
                           name="password"
                           class="mc-input"
                           autocomplete="current-password"
                           required
                           aria-required="true"
                           placeholder="Ihr Passwort">
                </div>

                <button type="submit" class="mc-btn mc-btn-primary mc-btn-block">
                    Anmelden
                </button>
            </form>

            <hr class="mc-form-divider">

            <div class="mc-form-links">
                <a href="<?php echo $forgotUrl; ?>">Passwort vergessen?</a>
                <a href="<?php echo $registerUrl; ?>">Jetzt registrieren</a>
            </div>

            <p class="mc-dsgvo-note">
                <span aria-hidden="true">🔒</span>
                Ihre Anmeldedaten werden verschlüsselt übertragen und gemäß DSGVO &amp; § 203 StGB geschützt.
            </p>
        </div>
    </div>
</main>
<?php get_footer(); ?>
