<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$homeUrl = theme_safe_url(SITE_URL . '/', SITE_URL . '/');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fehler – IT Expert Network</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(\CMS\ThemeManager::instance()->getThemeUrl(), ENT_QUOTES, 'UTF-8'); ?>/style.css?v=<?php echo file_exists(__DIR__ . '/style.css') ? (int) filemtime(__DIR__ . '/style.css') : 0; ?>">
</head>
<body class="theme-error-page">
    <main class="theme-error-page__shell">
        <article class="theme-error-card">
            <div class="theme-error-card__icon">⚠️</div>
            <h1 class="theme-error-card__title">Es ist ein Fehler aufgetreten</h1>
            <p class="theme-error-card__text">Ein interner Fehler hat die Verarbeitung deiner Anfrage verhindert. Bitte versuche es später erneut oder kontaktiere den Administrator.</p>
            <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>" class="btn theme-error-card__action">Zur Startseite</a>
        </article>
    </main>
</body>
</html>
