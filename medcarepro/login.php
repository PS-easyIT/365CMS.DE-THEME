<?php
/**
 * Login-Seite – MedCare Pro Theme
 *
 * @package MedCarePro
 */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;

// Bereits eingeloggt → Weiterleitung
if (theme_is_logged_in()) {
    header('Location: ' . SITE_URL . '/member');
    exit;
}

$error   = null;
$success = null;
$safe    = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$normalizeRedirectPath = static function (mixed $value): string {
    $candidate = trim((string)$value);
    if ($candidate === '' || preg_match('/[\x00-\x1F\x7F]/', $candidate) === 1 || str_starts_with($candidate, '//')) {
        return '';
    }

    $siteHost = (string)(parse_url((string)SITE_URL, PHP_URL_HOST) ?? '');
    $parts = parse_url($candidate);
    if (is_array($parts) && isset($parts['scheme'])) {
        $scheme = strtolower((string)$parts['scheme']);
        $host = (string)($parts['host'] ?? '');
        if (!in_array($scheme, ['http', 'https'], true) || strcasecmp($host, $siteHost) !== 0) {
            return '';
        }
        $candidate = (string)($parts['path'] ?? '/');
        if (!empty($parts['query'])) {
            $candidate .= '?' . (string)$parts['query'];
        }
    }

    if (!str_starts_with($candidate, '/')) {
        $candidate = '/' . ltrim($candidate, '/');
    }

    if (preg_match('#^/[A-Za-z0-9\-._~/%]*(?:\?[A-Za-z0-9\-._~%!$&()*+,;=:@/?%]*)?$#', $candidate) !== 1) {
        return '';
    }

    return str_contains($candidate, '/../') || str_contains($candidate, '/./') ? '' : $candidate;
};

// POST-Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mc_login'])) {
    try {
        if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'mc_login')) {
            $error = 'Sicherheitscheck fehlgeschlagen. Bitte laden Sie die Seite neu.';
        } else {
            $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || empty($password)) {
                $error = 'Bitte E-Mail und Passwort eingeben.';
            } else {
                $result = \CMS\Auth::instance()->login($email, $password);
                if ($result === true) {
                    header('Location: ' . SITE_URL . '/member');
                    exit;
                } else {
                    $error = is_string($result) ? $result : 'Ungültige E-Mail-Adresse oder falsches Passwort.';
                }
            }
        }
    } catch (\Throwable $e) {
        $error = 'Anmeldung fehlgeschlagen. Bitte versuchen Sie es erneut.';
    }
}

try {
    $csrfToken = \CMS\Security::instance()->generateToken('mc_login');
} catch (\Throwable $e) {
    $csrfToken = '';
}

$siteUrl = SITE_URL;
$postedEmail = filter_var((string)($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

get_header();
?>
<main id="main" class="mc-main" role="main"
      style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:var(--spacing-2xl) 0;">
    <div class="mc-container">
        <div class="mc-form-card">
            <!-- Logo / Branding -->
            <div style="text-align:center;margin-bottom:1.75rem;">
                <span style="font-size:2.5rem;" aria-hidden="true">🔒</span>
                <h1 style="font-size:var(--font-2xl);color:var(--secondary-color);margin:.5rem 0 .25rem;">Anmelden</h1>
                <p style="color:var(--muted-color);font-size:var(--font-sm);">Willkommen zurück bei MedCare Pro</p>
            </div>

            <?php if (!empty($error)) : ?>
            <div class="mc-alert mc-alert-error" role="alert"><?php echo htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
            <div class="mc-alert mc-alert-success" role="status"><?php echo htmlspecialchars((string)$success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <input type="hidden" name="mc_login" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((string)$csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="mc-form-group">
                    <label for="login-email" class="mc-label">
                        E-Mail-Adresse <span aria-hidden="true" style="color:#ef4444;">*</span>
                    </label>
                    <input id="login-email" type="email" name="email" class="mc-input"
                              value="<?php echo htmlspecialchars((string)$postedEmail, ENT_QUOTES, 'UTF-8'); ?>"
                           autocomplete="email" required
                           aria-required="true"
                           placeholder="ihre@email.de">
                </div>

                <div class="mc-form-group">
                    <label for="login-password" class="mc-label">
                        Passwort <span aria-hidden="true" style="color:#ef4444;">*</span>
                    </label>
                    <input id="login-password" type="password" name="password" class="mc-input"
                           autocomplete="current-password" required
                           aria-required="true"
                           placeholder="Ihr Passwort">
                </div>

                <button type="submit" class="mc-btn mc-btn-primary" style="width:100%;justify-content:center;margin-top:.5rem;">
                    🔑 Anmelden
                </button>
            </form>

            <hr class="mc-form-divider">

            <div class="mc-form-links">
                <a href="<?php echo htmlspecialchars((string)$siteUrl, ENT_QUOTES, 'UTF-8'); ?>/forgot-password">Passwort vergessen?</a>
                <a href="<?php echo htmlspecialchars((string)$siteUrl, ENT_QUOTES, 'UTF-8'); ?>/register">Jetzt registrieren</a>
            </div>

            <p class="mc-dsgvo-note">
                🔒 Ihre Anmeldedaten werden verschlüsselt übertragen und gemäß DSGVO &amp; § 203 StGB geschützt.
            </p>
        </div>
    </div>
</main>
<?php get_footer(); ?>
