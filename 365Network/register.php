<?php
/**
 * Register Template
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
$logoUrl      = theme_safe_url((string) \CMS\Services\ThemeCustomizer::instance()->get('header', 'logo_url', ''), '');
$error        = theme_get_flash('error');
$success      = theme_get_flash('success');
$registerUrl  = theme_safe_url($siteUrl . '/register', $siteUrl . '/register');
$loginUrl     = theme_safe_url($siteUrl . '/login', $siteUrl . '/login');
$homeUrl      = theme_safe_url($siteUrl . '/', $siteUrl . '/');
$privacyUrl   = theme_safe_url($siteUrl . '/datenschutz', $siteUrl . '/datenschutz');
$termsUrl     = theme_safe_url($siteUrl . '/agb', $siteUrl . '/agb');

// Felder aus fehlgeschlagenem Submit wiederherstellen
$savedUsername = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
$savedEmail    = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<main id="main" class="auth-shell" role="main">
    <div class="auth-shell__inner auth-shell__inner--wide">

        <div class="auth-card auth-card--wide">

            <!-- Logo -->
            <div class="auth-logo">
                <?php if (!empty($logoUrl)) : ?>
                    <img src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo htmlspecialchars($siteTitle, ENT_QUOTES, 'UTF-8'); ?>"
                         loading="eager"
                         class="auth-logo-image">
                <?php else : ?>
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
                <?php endif; ?>
                <p>Erstelle dein kostenloses Konto</p>
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

            <!-- Register Form -->
            <form method="POST" action="<?php echo htmlspecialchars($registerUrl, ENT_QUOTES, 'UTF-8'); ?>" novalidate>
                <?php theme_csrf_field('register'); ?>

                <div class="form-group">
                    <label class="form-label" for="username">Benutzername</label>
                    <input class="form-control"
                           type="text"
                           id="username"
                           name="username"
                           autocomplete="username"
                           required
                           autofocus
                           minlength="3"
                           maxlength="60"
                           pattern="[a-zA-Z0-9_\-]+"
                           value="<?php echo $savedUsername; ?>">
                    <span class="form-hint">Nur Buchstaben, Zahlen, - und _ erlaubt.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">E-Mail-Adresse</label>
                    <input class="form-control"
                           type="email"
                           id="email"
                           name="email"
                           autocomplete="email"
                           required
                           value="<?php echo $savedEmail; ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Passwort</label>
                    <input class="form-control"
                           type="password"
                           id="password"
                           name="password"
                           autocomplete="new-password"
                           required
                           minlength="8">
                    <span class="form-hint">Mindestens 8 Zeichen, empfohlen: Buchstaben, Zahlen und Sonderzeichen.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirm">Passwort bestätigen</label>
                    <input class="form-control"
                           type="password"
                           id="password_confirm"
                           name="password_confirm"
                           autocomplete="new-password"
                           required>
                </div>

                <!-- Datenschutz Checkbox -->
                <div class="form-group auth-consent">
                    <input type="checkbox" id="privacy" name="privacy" required class="auth-consent__checkbox">
                    <label for="privacy" class="auth-consent__label">
                        Ich stimme der
                        <a href="<?php echo htmlspecialchars($privacyUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Datenschutzerklärung</a>
                        zu und akzeptiere die
                        <a href="<?php echo htmlspecialchars($termsUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">AGB</a>.
                    </label>
                </div>

                <button type="submit" class="btn btn-primary auth-submit auth-submit--compact">
                    Konto erstellen
                </button>
            </form>

            <div class="auth-footer">
                <p>Bereits registriert?
                    <a href="<?php echo htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8'); ?>">Jetzt anmelden</a>
                </p>
                <p class="auth-footer-note">
                    <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">← Zurück zur Startseite</a>
                </p>
            </div>

        </div>
    </div>
</main>
