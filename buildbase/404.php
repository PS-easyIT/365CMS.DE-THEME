<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe    = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl = rtrim(buildbase_safe_url((string) SITE_URL, '/'), '/');
$siteUrl = $siteUrl !== '' ? $siteUrl : '/';
?>
<main id="main" class="bb-main bb-error-screen" role="main">
    <div class="bb-card bb-error-card">
        <div class="bb-error-code">404</div>
        <h1 class="bb-error-title">Seite nicht gefunden</h1>
        <p class="bb-error-text">Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
        <div class="bb-error-actions">
            <a href="<?php echo $safe($siteUrl); ?>" class="bb-btn bb-btn-primary">Zur Startseite</a>
            <a href="<?php echo $safe(buildbase_safe_url(rtrim($siteUrl, '/') . '/handwerker', $siteUrl)); ?>" class="bb-btn bb-btn-outline">Handwerker finden</a>
        </div>
    </div>
</main>
<?php get_footer(); ?>
