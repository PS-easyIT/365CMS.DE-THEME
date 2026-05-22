<?php
/**
 * PTC Theme – Login
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
                        <circle cx="18" cy="18" r="18" fill="var(--color-primary)"/>
                        <path d="M10 12h5v3h-5zM10 17h5v3h-5zM10 22h5v3h-5zM17 12h9v3h-9zM17 17h7v3h-7zM17 22h5v3h-5z" fill="var(--color-accent)"/>
                    </svg>
                <?php endif; ?>
                <p>Melde dich mit deinem Konto an</p>
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

            <!-- Login Form -->
            <form method="POST" action="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/login" novalidate>
                <?php theme_csrf_field('login'); ?>

                <div class="ptc-form-group">
                    <label class="ptc-form-label" for="username">Benutzername oder E-Mail</label>
                    <input class="ptc-form-control"
                           type="text"
                           id="username"
                           name="username"
                           autocomplete="username"
                           required
                           autofocus
                           value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="ptc-form-group">
                    <label class="ptc-form-label" for="password">Passwort</label>
                    <input class="ptc-form-control"
                           type="password"
                           id="password"
                           name="password"
                           autocomplete="current-password"
                           required>
                </div>

                <button type="submit" class="btn-ptc btn-ptc-accent" class="ptc-auth-submit">
                    Anmelden
                </button>
            </form>

            <!-- Footer Links -->
            <div class="ptc-auth-footer">
                <p>Noch kein Konto?
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/register">Jetzt registrieren</a>
                </p>
                <p>
                    <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/">← Zurück zur Startseite</a>
                </p>
            </div>

        </div><!-- /.ptc-auth-card -->

    </div>
</section>
