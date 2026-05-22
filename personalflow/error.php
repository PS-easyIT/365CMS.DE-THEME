<?php
/**
 * PersonalFlow Theme – Generic Error Template
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$errorCode    = isset($errorCode)    ? (int)    $errorCode    : 500;
$errorMessage = isset($errorMessage) ? (string) $errorMessage : 'Ein unerwarteter Fehler ist aufgetreten.';

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="pf-main pf-error-shell" role="main">
    <div class="pf-container">
        <div class="pf-error-card pf-reveal">
            <div class="pf-error-code pf-error-code--system"><?php echo (int) $errorCode; ?></div>
            <h1>Systemfehler</h1>
            <p><?php echo $safe($errorMessage); ?></p>
            <div class="pf-error-actions">
                <a href="<?php echo $safe(theme_route_url('home')); ?>" class="pf-btn pf-btn-primary">Zur Startseite</a>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
