<?php
declare(strict_types=1);

/**
 * 365CMS Showcase – Fehlerseite
 *
 * Wird vom Fatal-Handler in index.php direkt eingebunden (ohne header/footer) und
 * rendert deshalb ein eigenständiges Dokument – bewusst ohne functions.php.
 *
 * @package Showcase_Theme
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

$inner = '<section class="sc-notfound"><div class="sc-hero__bg" aria-hidden="true"></div><div class="sc-container sc-notfound__inner">'
    . '<p class="sc-notfound__code" aria-hidden="true">' . $errorCode . '</p>'
    . '<p class="sc-eyebrow sc-eyebrow--light">Fehler ' . $errorCode . '</p>'
    . '<h1 class="sc-notfound__title">Hier ist etwas schiefgelaufen.</h1>'
    . '<p class="sc-notfound__text">' . $esc($errorMessage) . '</p>'
    . '<p class="sc-notfound__actions"><a class="sc-button sc-button--primary" href="' . $esc($siteUrl . '/') . '"><span>Zur Startseite</span></a></p>'
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
    <link rel="stylesheet" href="<?php echo $esc($siteUrl . '/themes/cms-showcase/style.css'); ?>">
</head>
<body class="cms-showcase is-error">
<main id="main-content" class="sc-main">
    <?php echo $inner; ?>
</main>
</body>
</html>
