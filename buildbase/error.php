<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$errorCode    = isset($errorCode)    ? (int) $errorCode    : 500;
$errorMessage = isset($errorMessage) ? (string) $errorMessage : 'Ein Fehler ist aufgetreten.';
$safe         = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl      = buildbase_safe_url((string) SITE_URL, '/');
?>
<main id="main" class="bb-main bb-error-screen" role="main">
    <div class="bb-card bb-error-card">
        <div class="bb-error-code bb-error-code--system"><?php echo (int) $errorCode; ?></div>
        <h1 class="bb-error-title">Systemfehler</h1>
        <p class="bb-error-text"><?php echo $safe($errorMessage); ?></p>
        <div class="bb-error-actions">
            <a href="<?php echo $safe($siteUrl); ?>" class="bb-btn bb-btn-primary">Zur Startseite</a>
        </div>
    </div>
</main>
<?php get_footer(); ?>
