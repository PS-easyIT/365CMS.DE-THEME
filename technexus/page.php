<?php
/**
 * TechNexus Theme – Einzelseite
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$page = null;
try {
    $page = \CMS\Services\PageService::getCurrent();
} catch (\Throwable) {
    $page = null;
}
?>

<main id="main" class="site-main tn-section" role="main">
    <div class="container container--narrow">
        <?php if ($page !== null && !empty($page->title ?? '')) : ?>
            <article class="page-content tech-card tn-prose-card">
                <h1 class="page-title"><?php echo tn_html_attr((string) $page->title); ?></h1>

                <?php if (!empty($page->content ?? '')) : ?>
                    <div class="page-body prose">
                        <?php echo \CMS\Helpers\ContentHelper::processContent($page->content); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php else : ?>
            <div class="tech-card tech-card--placeholder">
                <p>Diese Seite wurde nicht gefunden.</p>
                <a href="<?php echo tn_html_attr(theme_route_url('home')); ?>" class="btn btn-primary">Zur Startseite</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
