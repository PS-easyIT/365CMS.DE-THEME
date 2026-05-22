<?php
/**
 * CMS Newspaper Theme – Generic Error Page
 *
 * @package CmsNewspaper_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$errorCode    = (int) ($GLOBALS['error_code']    ?? 500);
$errorMessage = (string) ($GLOBALS['error_message'] ?? 'Ein unerwarteter Fehler ist aufgetreten.');
http_response_code($errorCode);

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>

<section class="news-page-hero" aria-label="Fehler <?php echo $safe((string) $errorCode); ?>">
    <div class="news-container">
        <span class="news-kicker">Fehler <?php echo $safe((string) $errorCode); ?></span>
        <h1>Etwas ist schiefgelaufen.</h1>
        <p>Die Redaktion arbeitet bereits an einer Lösung. Bitte versuchen Sie es in Kürze erneut.</p>
    </div>
</section>

<div class="news-page-content">
    <div class="news-container">
        <div class="news-error-wrap">
            <div class="news-error-code" aria-hidden="true"><?php echo $safe((string) $errorCode); ?></div>
            <h2 class="news-error-title">Unerwartete Antwort des Servers</h2>
            <p class="news-error-text"><?php echo $safe($errorMessage); ?></p>
            <div class="news-error-actions">
                <a href="<?php echo $safe(news_href('/')); ?>" class="news-btn news-btn-primary">Zur Startseite</a>
            </div>
        </div>
    </div>
</div>
