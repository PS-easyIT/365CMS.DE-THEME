<?php
/**
 * LogiLink Theme – Generic Error Template
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Wird auch vom Fatal-Handler (index.php) direkt eingebunden – Theme-Helfer dann nachladen.
if (!defined('LOGILINK_THEME_VERSION') && is_file(__DIR__ . '/functions.php')) {
    require_once __DIR__ . '/functions.php';
}

if (function_exists('get_header')) {
    get_header();
}

$errorCode    = isset($errorCode)    ? (int) $errorCode             : 500;
$errorMessage = isset($errorMessage) ? (string) $errorMessage       : 'Ein Fehler ist aufgetreten.';
$homeUrl      = htmlspecialchars(theme_route_url('home'), ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="ll-main ll-error-wrap" role="main">
    <div class="ll-container">
        <div class="ll-card ll-error-card">
            <div class="ll-error-code ll-error-code--alert"><?php echo (int) $errorCode; ?></div>
            <h1>Systemfehler</h1>
            <p><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="ll-error-actions">
                <a href="<?php echo $homeUrl; ?>" class="ll-btn ll-btn-primary">Zur Startseite</a>
            </div>
        </div>
    </div>
</main>
<?php if (function_exists('get_footer')) { get_footer(); } ?>
