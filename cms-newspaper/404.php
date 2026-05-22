<?php
/**
 * CMS Newspaper Theme – 404 Error Page
 *
 * @package CmsNewspaper_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

http_response_code(404);

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>

<section class="news-page-hero" aria-label="Fehler 404">
    <div class="news-container">
        <span class="news-kicker">Fehler 404</span>
        <h1>Seite nicht gefunden</h1>
        <p>Die angeforderte Seite existiert nicht oder wurde verschoben.</p>
    </div>
</section>

<div class="news-page-content">
    <div class="news-container">
        <div class="news-error-wrap">
            <div class="news-error-code" aria-hidden="true">404</div>
            <h2 class="news-error-title">Diese Ausgabe steht nicht im Archiv.</h2>
            <p class="news-error-text">
                Möglicherweise wurde der Artikel umbenannt, archiviert oder es liegt ein Tippfehler in der URL vor.
                Nutzen Sie die Themen-Schnellnavigation oder kehren Sie zur Startseite zurück.
            </p>
            <div class="news-error-actions">
                <a href="<?php echo $safe(news_href('/')); ?>" class="news-btn news-btn-primary">Zur Startseite</a>
                <a href="<?php echo $safe(theme_route_url('search')); ?>" class="news-btn news-btn-ghost">Artikel suchen</a>
            </div>
        </div>
    </div>
</div>
