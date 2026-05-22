<?php
/**
 * LogiLink Theme – Singular Page Template
 *
 * @package LogiLink_Theme
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

$safe    = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$homeUrl = htmlspecialchars(theme_route_url('home'), ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="ll-main ll-prose-wrap" role="main">
    <div class="ll-container">
        <?php if (!empty($page)) : ?>
            <article class="ll-card ll-page-card">
                <?php if (!empty($page->title)) : ?>
                    <h1><?php echo $safe((string) $page->title); ?></h1>
                <?php endif; ?>

                <?php if (!empty($page->content)) : ?>
                    <div class="ll-prose">
                        <?php echo \CMS\Helpers\ContentHelper::processContent((string) $page->content); ?>
                    </div>
                <?php endif; ?>
            </article>
        <?php else : ?>
            <div class="ll-error-card ll-card">
                <p>Seite nicht gefunden.</p>
                <div class="ll-error-actions">
                    <a href="<?php echo $homeUrl; ?>" class="ll-btn ll-btn-primary">Zur Startseite</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
