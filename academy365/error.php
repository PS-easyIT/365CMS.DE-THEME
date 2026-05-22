<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$statusCode = isset($errorCode) ? (int) $errorCode : 500;
http_response_code($statusCode);
get_header();
$safe = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl = rtrim(academy365_safe_url((string) SITE_URL, '/'), '/');
$siteUrl = $siteUrl !== '' ? $siteUrl : '/';
?>
<main id="main" class="ac-main-content" role="main">
    <div class="ac-container ac-error-page">
        <div class="ac-error-code" aria-hidden="true"><?php echo (int) $statusCode; ?></div>
        <span class="ac-error-emoji" aria-hidden="true">⚠️</span>
        <h1>Ein Fehler ist aufgetreten</h1>
        <p class="ac-muted">
            <?php if (!empty($errorMessage)) : ?>
                <?php echo $safe((string) $errorMessage); ?>
            <?php else : ?>
                Es ist ein technischer Fehler aufgetreten. Bitte versuche es später erneut.
            <?php endif; ?>
        </p>
        <div class="ac-error-actions">
            <a href="<?php echo $safe($siteUrl); ?>" class="ac-btn ac-btn-primary">Zurück zur Startseite</a>
        </div>
    </div>
</main>
<?php get_footer(); ?>
