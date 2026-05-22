<?php
/**
 * PersonalFlow Theme – 404 Template
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="pf-main pf-error-shell" role="main">
    <div class="pf-container">
        <div class="pf-error-card pf-reveal">
            <div class="pf-error-code">404</div>
            <h1>Seite nicht gefunden</h1>
            <p>Diese Seite oder dieses Profil existiert nicht (mehr) oder wurde an einen anderen Pfad verschoben.</p>
            <div class="pf-error-actions">
                <a href="<?php echo $safe(theme_route_url('home')); ?>" class="pf-btn pf-btn-primary">Zur Startseite</a>
                <a href="<?php echo $safe(theme_route_url('jobs')); ?>" class="pf-btn pf-btn-ghost">Offene Stellen</a>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
