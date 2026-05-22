<?php
/**
 * TechNexus Theme – 404 Fehlerseite
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main" class="site-main error-page tn-section" role="main">
    <div class="container tn-error-layout">
        <div class="tech-card tn-error-card">
            <p class="tn-error-code" aria-hidden="true">404</p>
            <h1>Seite nicht gefunden</h1>
            <p class="tech-card__meta">
                Die gesuchte Seite existiert nicht oder wurde verschoben.
            </p>
            <div class="tn-error-actions">
                <a href="<?php echo tn_html_attr(theme_route_url('home')); ?>" class="btn btn-primary">Zurück zur Startseite</a>
                <a href="<?php echo tn_html_attr(theme_route_url('experts')); ?>" class="btn btn-outline">Experten suchen</a>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
