<?php
/**
 * Statische Seite – Vollbreite-Template (ohne Sidebar, volle Container-Breite)
 *
 * Template-ID: page-wide
 * Unterschiede zu page.php:
 *  - Kein 2-Spalten-Layout, kein .sidebar
 *  - Volle Container-Breite (kein max-width)
 *  - Geeignet für Landing-Seiten, Portfolio, Showcases
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;

$formatPageWideDate = static function (?string $value, string $format = 'j. F Y'): string {
    $timestamp = strtotime((string) $value);

    return $timestamp !== false ? date($format, $timestamp) : '—';
};

$pageProvidedByRouter = isset($page) && !empty($page);

if ($pageProvidedByRouter) {
    $page = is_object($page) ? (array)$page : (array)$page;
} else {
    try {
        $page = function_exists('phinit_get_page_by_request_path')
            ? phinit_get_page_by_request_path(phinit_current_request_path())
            : null;
        if (!$page) {
            http_response_code(404);
            get_theme_part('404');
            exit;
        }
    } catch (\Throwable $e) {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }
}

$pageContent = (string)($page['content'] ?? '');
if (!$pageProvidedByRouter) {
    $pageContent = phinit_prepare_renderable_content($pageContent, 'page', (int)($page['id'] ?? 0));
}
$pageContent = phinit_sanitize_renderable_content($pageContent, 'default');
$pageHeadingData = phinit_with_heading_ids($pageContent, [2, 3, 4, 5, 6]);
$pageContent = phinit_enhance_content_images($pageHeadingData['html']);
$safePageContent = $pageContent;
?>

<div class="container page-shell page-shell--wide">

    <!-- Breadcrumb + Seitentitel -->
    <div class="page-header-block page-header-block--wide" data-anim>
        <div class="breadcrumbs">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>">Startseite</a>
            <span>/</span>
            <span><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></span>
        </div>
        <h1><?php echo htmlspecialchars($page['title'] ?? '', ENT_QUOTES); ?></h1>
        <?php if (!empty($page['updated_at'])): ?>
        <p class="page-last-updated">
            Zuletzt aktualisiert: <?php echo htmlspecialchars($formatPageWideDate((string) ($page['updated_at'] ?? '')), ENT_QUOTES); ?>
        </p>
        <?php endif; ?>
    </div>

    <!-- Seiteninhalt volle Breite -->
    <div class="page-content page-content--full" data-anim data-anim-delay="1">
        <?php phinit_render_prepared_content($safePageContent); ?>
    </div>

</div><!-- /.container -->
