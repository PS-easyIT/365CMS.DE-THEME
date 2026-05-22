<?php
/**
 * CMS Newspaper Theme – Index / Fallback Listing
 *
 * Lightweight fallback view for routes that have no dedicated template.
 *
 * @package CmsNewspaper_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>

<section class="news-page-hero" aria-label="Übersicht">
    <div class="news-container">
        <span class="news-kicker">Übersicht</span>
        <h1>Aktuelle Beiträge</h1>
        <p>Eine Übersicht aller verfügbaren Artikel und Ressourcen. Nutzen Sie die Navigation oder kehren Sie zur Startseite zurück.</p>
    </div>
</section>

<div class="news-page-content">
    <div class="news-container">
        <div class="news-prose">
            <p>Es wurden keine spezifischen Inhalte zu diesem Pfad gefunden. Beginnen Sie auf der Startseite oder verwenden Sie die Themen-Schnellnavigation, um zu einem bekannten Ressort zu wechseln.</p>
        </div>

        <div class="news-actions-row">
            <a href="<?php echo $safe(news_href('/')); ?>" class="news-btn news-btn-primary">Zur Startseite</a>
            <a href="<?php echo $safe(theme_route_url('archive')); ?>" class="news-btn news-btn-ghost">Archiv durchsuchen</a>
        </div>
    </div>
</div>
