<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$page    = \CMS\Services\PageService::getCurrent();
$safe    = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl = SITE_URL;
?>
<main id="main" class="bb-main bb-page-section" role="main">
    <div class="bb-container bb-container--narrow">
        <?php if (!empty($page)) : ?>
            <article class="bb-card bb-page-article">
                <?php if (!empty($page->title)) : ?>
                    <h1><?php echo $safe((string) $page->title); ?></h1>
                <?php endif; ?>
                <?php if (!empty($page->content)) : ?>
                    <div class="prose"><?php echo \CMS\Helpers\ContentHelper::processContent((string) $page->content); ?></div>
                <?php endif; ?>
            </article>
        <?php else : ?>
            <div class="bb-card bb-error-card">
                <p class="bb-error-text">Seite nicht gefunden.</p>
                <div class="bb-error-actions">
                    <a href="<?php echo $safe($siteUrl); ?>" class="bb-btn bb-btn-primary">Zur Startseite</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
