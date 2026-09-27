<?php
declare(strict_types=1);

/**
 * Kompass – Fehlerseite
 *
 * Wird vom Fatal-Handler in index.php direkt eingebunden (ohne header/footer) und
 * rendert deshalb ein eigenständiges Dokument – bewusst ohne functions.php.
 *
 * @package Kompass_Theme
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

$inner = '<header class="kp-pagehead kp-pagehead--center"><div class="kp-container kp-notfound">'
    . '<p class="kp-eyebrow">Fehler ' . $errorCode . '</p>'
    . '<h1 class="kp-pagehead__title">Hier ist etwas schiefgelaufen.</h1>'
    . '<p class="kp-pagehead__lead">' . $esc($errorMessage) . '</p>'
    . '<p class="kp-notfound__actions"><a class="kp-button kp-button--primary" href="' . $esc($siteUrl . '/') . '"><span>Zur Startseite</span></a></p>'
    . '</div></header>';

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
    <link rel="stylesheet" href="<?php echo $esc($siteUrl . '/themes/kompass/style.css'); ?>">
</head>
<body class="kompass is-error">
<main id="main-content" class="kp-main">
    <?php echo $inner; ?>
</main>
</body>
</html>
