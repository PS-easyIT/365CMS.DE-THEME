<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe    = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl = SITE_URL;
?>
<main id="main" class="bb-main bb-error-screen" role="main">
    <div class="bb-card bb-error-card">
        <div class="bb-error-code">404</div>
        <h1 class="bb-error-title">Seite nicht gefunden</h1>
        <p class="bb-error-text">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
        <div class="bb-error-actions">
            <a href="<?php echo $safe($siteUrl); ?>" class="bb-btn bb-btn-primary">Zur Startseite</a>
            <a href="<?php echo $safe(rtrim($siteUrl, '/') . '/handwerker'); ?>" class="bb-btn bb-btn-outline">Handwerker finden</a>
        </div>
    </div>
</main>
<?php get_footer(); ?>
