<?php
/**
 * Generische Fehlerseite – MedCare Pro Theme
 *
 * Erwartet optional: $errorCode (int), $errorMessage (string)
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe         = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$errorCode    = (int) ($errorCode ?? 500);
$errorMessage = (string) ($errorMessage ?? 'Ein Fehler ist aufgetreten.');
$homeUrl      = $safe(theme_route_url('home'));
?>
<main id="main" class="mc-main mc-status-page" role="main">
    <div class="mc-container mc-status-page__container">
        <div class="mc-card mc-status-card">
            <div class="mc-status-code mc-status-code--error"><?php echo $errorCode; ?></div>
            <h1 class="mc-status-title">Systemfehler</h1>
            <p class="mc-status-text"><?php echo $safe($errorMessage); ?></p>
            <div class="mc-status-actions">
                <a href="<?php echo $homeUrl; ?>" class="mc-btn mc-btn-primary">Zur Startseite</a>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
