<?php
/**
 * Generische Fehlerseite – CMS Phinit Theme
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl    = SITE_URL;
$errorCode  = isset($error_code) ? (int)$error_code : 500;
$errorTitle = $error_title ?? 'Ein Fehler ist aufgetreten';
$errorMsg   = $error_message ?? 'Bitte versuche es später erneut oder kontaktiere den Administrator.';
?>

<div class="container" style="padding-top:48px;padding-bottom:48px;">

    <div class="error-hero" data-anim>
        <div class="error-code"><?php echo $errorCode; ?></div>
        <h1 class="error-title"><?php echo htmlspecialchars($errorTitle, ENT_QUOTES); ?></h1>
        <p class="error-desc"><?php echo htmlspecialchars($errorMsg, ENT_QUOTES); ?></p>
        <div class="error-actions">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/" class="btn btn-primary">← Zur Startseite</a>
            <a href="javascript:history.back()" class="btn btn-outline">↩ Zurück</a>
        </div>
    </div>

</div><!-- /.container -->
