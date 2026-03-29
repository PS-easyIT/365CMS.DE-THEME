<?php
/**
 * Login Template
 *
 * Kein header/footer-wrap nötig - dieser wird durch ThemeManager::render() automatisch eingebunden.
 *
 * @package IT_Expert_Network_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl      = SITE_URL;
$themeManager = \CMS\ThemeManager::instance();
$siteTitle    = $themeManager->getSiteTitle();
$error        = theme_get_flash('error');
$success      = theme_get_flash('success');
$loginUrl     = theme_safe_url($siteUrl . '/login', $siteUrl . '/login');
$registerUrl  = theme_safe_url($siteUrl . '/register', $siteUrl . '/register');
$homeUrl      = theme_safe_url($siteUrl . '/', $siteUrl . '/');
?>

<main id="main" class="auth-shell" role="main">
    <div class="auth-shell__inner">

        <!-- Auth Card -->
        <div class="auth-card">

            <!-- Logo -->
            <div class="auth-logo">
                <svg class="network-icon auth-logo-icon"
                     viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="30" cy="30" r="6" fill="currentColor"/>
                    <circle cx="15" cy="15" r="5" fill="currentColor"/>
                    <circle cx="45" cy="15" r="5" fill="currentColor"/>
                    <circle cx="15" cy="45" r="5" fill="currentColor"/>
                    <circle cx="45" cy="45" r="5" fill="currentColor"/>
                    <line x1="30" y1="30" x2="15" y2="15" stroke="currentColor" stroke-width="2"/>
                    <line x1="30" y1="30" x2="45" y2="15" stroke="currentColor" stroke-width="2"/>
                    <line x1="30" y1="30" x2="15" y2="45" stroke="currentColor" stroke-width="2"/>
                    <line x1="30" y1="30" x2="45" y2="45" stroke="currentColor" stroke-width="2"/>
                </svg>
                <h1><?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p>Melde dich mit deinem Konto an</p>
            </div>

            <!-- Flash Messages -->
            <?php if ($error && trim($error) !== '') : ?>
                <div class="alert alert-error" role="alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($success && trim($success) !== '') : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>" novalidate>
                <?php theme_csrf_field('login'); ?>

                <div class="form-group">
                    <label class="form-label" for="username">Benutzername oder E-Mail</label>
                    <input class="form-control"
                           type="text"
                           id="username"
                           name="username"
                           autocomplete="username"
                           required
                           autofocus
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Passwort</label>
                    <input class="form-control"
                           type="password"
                           id="password"
                           name="password"
                           autocomplete="current-password"
                           required>
                </div>

                <button type="submit" class="btn btn-primary auth-submit">
                    Anmelden
                </button>
            </form>

            <!-- Footer Links -->
            <div class="auth-footer">
                <p>Noch kein Konto?
                    <a href="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8'); ?>">Jetzt registrieren</a>
                </p>
                <p class="auth-footer-note">
                    <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">← Zurück zur Startseite</a>
                </p>
            </div>

        </div>
    </div>
</main>
