<?php
declare(strict_types=1);

/**
 * Kontor – Fehlerseite
 *
 * Wird vom Fatal-Handler in index.php direkt eingebunden (ohne header/footer) und
 * rendert deshalb ein eigenständiges Dokument – bewusst ohne functions.php.
 *
 * @package Kontor_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$errorCode = (int) ($GLOBALS['error_code'] ?? $errorCode ?? 500);
$errorCode = $errorCode >= 400 && $errorCode < 600 ? $errorCode : 500;
$errorMessage = (string) ($GLOBALS['error_message'] ?? $errorMessage ?? 'Beim Laden der Seite ist ein unerwarteter Fehler aufgetreten.');
$siteUrl = rtrim(defined('SITE_URL') ? (string) SITE_URL : '', '/');
$siteName = defined('SITE_NAME') ? (string) SITE_NAME : '365CMS';
$esc = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

if (!headers_sent()) {
    http_response_code($errorCode);
}

$inner = '<section class="kt-notfound"><div class="kt-container kt-notfound__inner">'
    . '<p class="kt-notfound__code" aria-hidden="true">' . $errorCode . '</p>'
    . '<p class="kt-eyebrow kt-eyebrow--light">Fehler ' . $errorCode . '</p>'
    . '<h1 class="kt-hero__title">Hier ist etwas schiefgelaufen.</h1>'
    . '<p class="kt-hero__text">' . $esc($errorMessage) . '</p>'
    . '<div class="kt-hero__actions"><a class="kt-button kt-button--accent" href="' . $esc($siteUrl . '/') . '"><span>Zur Startseite</span></a></div>'
    . '</div></section>';

if (defined('CMS_THEME_HEADER_RENDERED')) {
    echo $inner;
    return;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?php echo $esc('Fehler ' . $errorCode . ' – ' . $siteName); ?></title>
    <link rel="stylesheet" href="<?php echo $esc($siteUrl . '/themes/kontor/style.css'); ?>">
</head>
<body class="kontor is-error">
<main id="main-content" class="kt-main">
    <?php echo $inner; ?>
</main>
</body>
</html>
