<?php
/**
 * Statische Seite – MedCare Pro Theme
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe    = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$homeUrl = $safe(theme_route_url('home'));

try {
    $page = \CMS\Services\PageService::getCurrent();
} catch (\Throwable) {
    $page = null;
}
?>
<main id="main" class="mc-main mc-page" role="main">
    <div class="mc-container mc-page__container">
        <?php if (!empty($page)) : ?>
        <article class="mc-card mc-page__article">
            <?php if (!empty($page->title)) : ?>
                <h1 class="mc-page__title"><?php echo $safe((string) $page->title); ?></h1>
            <?php endif; ?>
            <?php if (!empty($page->content)) : ?>
                <div class="prose mc-page__content">
                    <?php echo \CMS\Helpers\ContentHelper::processContent((string) $page->content); ?>
                </div>
            <?php endif; ?>
        </article>
        <?php else : ?>
        <div class="mc-card mc-empty-state">
            <div class="mc-empty-state__icon" aria-hidden="true">📄</div>
            <h1 class="mc-empty-state__title">Seite nicht gefunden</h1>
            <p class="mc-empty-state__text">Diese Seite konnte nicht geladen werden.</p>
            <a href="<?php echo $homeUrl; ?>" class="mc-btn mc-btn-primary mc-empty-state__action">Zur Startseite</a>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
