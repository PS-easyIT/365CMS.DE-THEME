<?php
/**
 * PTC Theme – Registrierung
 *
 * ThemeManager::render() bindet header.php / footer.php automatisch ein.
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl   = ptc_site_url();
$siteTitle = ptc_site_title();
$logoUrl   = ptc_customizer_get('header', 'logo_url', '');
$error     = theme_get_flash('error');
$success   = theme_get_flash('success');

// Felder aus fehlgeschlagenem Submit wiederherstellen
$savedUsername = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
$savedEmail    = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
?>

<section class="ptc-auth-section">
    <div class="ptc-auth-wrap">

        <!-- Auth Card -->
        <div class="ptc-auth-card">

            <!-- Logo -->
            <div class="ptc-auth-logo">
                <?php if (!empty($logoUrl)): ?>
                    <img src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo $siteTitle; ?>" class="ptc-auth-logo-img">
                <?php else: ?>
                    <svg width="52" height="52" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="18" cy="18" r="18" fill="var(--ptc-navy, #002D5D)"/>
                        <path d="M10 12h5v3h-5zM10 17h5v3h-5zM10 22h5v3h-5zM17 12h9v3h-9zM17 17h7v3h-7zM17 22h5v3h-5z" fill="var(--ptc-gold, #D4A017)"/>
                    </svg>
                <?php endif; ?>
                <h1><?php echo $siteTitle; ?></h1>
                <p>Erstelle dein kostenloses Konto</p>
            </div>

            <!-- Flash Messages -->
            <?php if ($error && trim($error) !== ''): ?>
                <div class="ptc-alert ptc-alert-error" role="alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($success && trim($success) !== ''): ?>
                <div class="ptc-alert ptc-alert-success" role="alert">
                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Register Form -->
            <form method="POST" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/register" novalidate>
                <?php theme_csrf_field('register'); ?>

                <div class="ptc-form-group">
                    <label class="ptc-form-label" for="username">Benutzername</label>
                    <input class="ptc-form-control"
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
                    <span class="ptc-form-hint">Nur Buchstaben, Zahlen, - und _ erlaubt.</span>
                </div>

                <div class="ptc-form-group">
                    <label class="ptc-form-label" for="email">E-Mail-Adresse</label>
                    <input class="ptc-form-control"
                           type="email"
                           id="email"
                           name="email"
                           autocomplete="email"
                           required
                           value="<?php echo $savedEmail; ?>">
                </div>

                <div class="ptc-form-group">
                    <label class="ptc-form-label" for="password">Passwort</label>
                    <input class="ptc-form-control"
                           type="password"
                           id="password"
                           name="password"
                           autocomplete="new-password"
                           required
                           minlength="12">
                    <span class="ptc-form-hint">Mindestens 12 Zeichen, Groß-/Kleinbuchstaben, Ziffer und Sonderzeichen.</span>
                </div>

                <div class="ptc-form-group">
                    <label class="ptc-form-label" for="password_confirm">Passwort bestätigen</label>
                    <input class="ptc-form-control"
                           type="password"
                           id="password_confirm"
                           name="password_confirm"
                           autocomplete="new-password"
                           required>
                </div>

                <!-- Datenschutz-Checkbox -->
                <div class="ptc-form-group ptc-form-checkbox">
                    <input type="checkbox" id="privacy" name="privacy" required>
                    <label for="privacy">
                        Ich stimme der
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/datenschutz" target="_blank" rel="noopener noreferrer">Datenschutzerklärung</a>
                        zu und akzeptiere die
                        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/agb" target="_blank" rel="noopener noreferrer">AGB</a>.
                    </label>
                </div>

                <button type="submit" class="btn-ptc btn-ptc-accent" style="width:100%;margin-top:1rem;">
                    Konto erstellen
                </button>
            </form>

            <!-- Footer Links -->
            <div class="ptc-auth-footer">
                <p>Bereits registriert?
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/login">Jetzt anmelden</a>
                </p>
                <p>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/">← Zurück zur Startseite</a>
                </p>
            </div>

        </div><!-- /.ptc-auth-card -->

    </div>
</section>
