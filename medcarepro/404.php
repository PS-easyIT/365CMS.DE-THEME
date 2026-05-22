<?php
/**
 * 404 – MedCare Pro Theme
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe       = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$homeUrl    = $safe(theme_route_url('home'));
$doctorsUrl = $safe(theme_route_url('doctors'));
?>
<main id="main" class="mc-main mc-status-page" role="main">
    <div class="mc-container mc-status-page__container">
        <div class="mc-card mc-status-card">
            <div class="mc-status-code mc-status-code--info">404</div>
            <h1 class="mc-status-title">Seite nicht gefunden</h1>
            <p class="mc-status-text">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
            <div class="mc-status-actions">
                <a href="<?php echo $homeUrl; ?>" class="mc-btn mc-btn-primary">Zur Startseite</a>
                <a href="<?php echo $doctorsUrl; ?>" class="mc-btn mc-btn-outline">Arzt suchen</a>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
