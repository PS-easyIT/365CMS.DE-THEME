<?php
declare(strict_types=1);

/**
 * Business Theme – 404 Fehlerseite
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

http_response_code(404);
?>

<section class="biz-page-hero">
    <div class="biz-container">
        <p class="biz-page-eyebrow">Fehler 404</p>
        <h1>Seite nicht gefunden</h1>
        <p>Die angeforderte Seite existiert nicht oder wurde verschoben.</p>
    </div>
</section>

<div class="biz-page-content">
    <div class="biz-container">
        <div class="biz-error-wrap">
            <div class="biz-error-icon" aria-hidden="true">🔍</div>
            <h2 class="biz-error-title">Ups – hier gibt es nichts zu sehen.</h2>
            <p class="biz-error-text">
                Möglicherweise wurde die Seite umbenannt oder gelöscht. Nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.
            </p>
            <div class="biz-error-actions">
                <a href="<?php echo htmlspecialchars(biz_href('/'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-primary btn-biz-lg">Zur Startseite</a>
                <a href="<?php echo htmlspecialchars(biz_href('#kontakt'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-ghost btn-biz-lg">Kontakt aufnehmen</a>
            </div>
        </div>
    </div>
</div>
