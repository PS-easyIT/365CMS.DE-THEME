<?php
/**
 * TechNexus Theme – Allgemeine Fehlerseite
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$errorCode    = isset($errorCode) ? (int) $errorCode : 500;
$errorMessage = isset($errorMessage)
    ? (string) $errorMessage
    : 'Ein unerwarteter Fehler ist aufgetreten.';
?>

<main id="main" class="site-main error-page tn-section" role="main">
    <div class="container tn-error-layout">
        <div class="tech-card tn-error-card">
            <p class="tn-error-code tn-error-code--system" aria-hidden="true"><?php echo $errorCode; ?></p>
            <h1>Systemfehler</h1>
            <p class="tech-card__meta"><?php echo tn_html_attr($errorMessage); ?></p>
            <div class="tn-error-actions">
                <a href="<?php echo tn_html_attr(theme_route_url('home')); ?>" class="btn btn-primary">Zur Startseite</a>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
