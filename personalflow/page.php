<?php
/**
 * PersonalFlow Theme – Generic Page Template
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

try {
    $page = \CMS\Services\PageService::getCurrent();
} catch (\Throwable) {
    $page = null;
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="pf-main pf-page-shell" role="main">
    <div class="pf-container">
        <?php if (!empty($page)) : ?>
            <article class="pf-card pf-prose-wrap">
                <?php if (!empty($page->title)) : ?>
                    <h1><?php echo $safe((string) $page->title); ?></h1>
                <?php endif; ?>
                <?php if (!empty($page->content)) : ?>
                    <div class="pf-prose">
                        <?php echo \CMS\Helpers\ContentHelper::processContent((string) $page->content); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php else : ?>
            <div class="pf-error-shell">
                <div class="pf-error-card">
                    <p>Seite nicht gefunden.</p>
                    <div class="pf-error-actions">
                        <a href="<?php echo $safe(theme_route_url('home')); ?>" class="pf-btn pf-btn-primary">Zur Startseite</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
