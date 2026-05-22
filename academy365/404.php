<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

http_response_code(404);
get_header();
$siteUrl = rtrim(academy365_safe_url((string) SITE_URL, '/'), '/');
$siteUrl = $siteUrl !== '' ? $siteUrl : '/';
$safe = fn(string $v) => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
try {
    $c = \CMS\Services\ThemeCustomizer::instance();
    $notFoundTitle    = $c->get('error_pages', 'not_found_title',   'Seite nicht gefunden');
    $notFoundMessage  = $c->get('error_pages', 'not_found_message', 'Diese Seite existiert leider nicht. Entdecke stattdessen unsere Kurse!');
    $notFoundCta      = $c->get('error_pages', 'not_found_cta',     'Kurse entdecken');
} catch (\Throwable $e) {
    $notFoundTitle = 'Seite nicht gefunden'; $notFoundMessage = 'Diese Seite existiert leider nicht.'; $notFoundCta = 'Kurse entdecken';
}
?>
<main id="main" class="ac-main-content" role="main">
    <div class="ac-container ac-error-page">
        <div class="ac-error-code" aria-hidden="true">404</div>
        <span class="ac-error-emoji" aria-hidden="true">🎓</span>
        <h1><?php echo $safe($notFoundTitle); ?></h1>
        <p class="ac-muted"><?php echo $safe($notFoundMessage); ?></p>
        <div class="ac-error-actions">
            <a href="<?php echo $safe(academy365_safe_url($siteUrl . '/courses', $siteUrl)); ?>" class="ac-btn ac-btn-primary"><?php echo $safe($notFoundCta); ?></a>
            <a href="<?php echo $safe($siteUrl); ?>" class="ac-btn ac-btn-ghost">Zur Startseite</a>
        </div>
    </div>
</main>
<?php get_footer(); ?>
