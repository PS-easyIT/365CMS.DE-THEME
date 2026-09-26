<?php
declare(strict_types=1);

/**
 * Business Theme – Index / Fallback
 *
 * @package IT_Business_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Fallback für Blog-/Kategorie-/Schlagwort-Archive: Der Core übergibt dann $posts.
if (isset($posts) && is_array($posts)) {
    require __DIR__ . '/blog.php';
    return;
}
?>

<section class="biz-page-hero">
    <div class="biz-container">
        <h1>Seite nicht gefunden</h1>
        <p>Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
    </div>
</section>

<div class="biz-page-content">
    <div class="biz-container">
        <div class="biz-error-wrap-narrow">
            <p class="biz-error-text">Bitte nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.</p>
            <a href="<?php echo htmlspecialchars(biz_href('/'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-biz btn-biz-primary">Zur Startseite</a>
        </div>
    </div>
</div>
