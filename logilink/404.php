<?php
/**
 * LogiLink Theme – 404 Template
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$homeUrl  = htmlspecialchars(theme_route_url('home'),     ENT_QUOTES, 'UTF-8');
$trackUrl = htmlspecialchars(theme_route_url('tracking'), ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="ll-main ll-error-wrap" role="main">
    <div class="ll-container">
        <div class="ll-card ll-error-card">
            <div class="ll-error-code">404</div>
            <h1>Seite nicht gefunden</h1>
            <p>Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
            <div class="ll-error-actions">
                <a href="<?php echo $homeUrl; ?>"  class="ll-btn ll-btn-primary">Zur Startseite</a>
                <a href="<?php echo $trackUrl; ?>" class="ll-btn ll-btn-accent">Sendung verfolgen</a>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
