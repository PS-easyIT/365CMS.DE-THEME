<?php
/**
 * 404 Not Found Template
 *
 * @package IT_Expert_Network_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$homeUrl = theme_route_url('home');
$searchUrl = theme_route_url('search');
?>

<main id="main" class="site-main" role="main">
    <div class="container">
        <div class="error-page error-page-shell">
            <div class="error-page-code">404</div>
            <h1>Seite nicht gefunden</h1>
            <p>Die gesuchte Seite existiert nicht oder wurde verschoben.</p>
            <div class="error-page-actions">
                <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn btn-primary">
                    Zur Startseite
                </a>
                <a href="<?php echo htmlspecialchars($searchUrl, ENT_QUOTES, 'UTF-8'); ?>"
                   class="btn btn-secondary">
                    Suche öffnen
                </a>
                <button class="btn btn-outline" type="button" data-history-back data-history-fallback="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">
                    ← Zurück
                </button>
            </div>
        </div>
    </div>
</main>
