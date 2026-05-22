<?php
/**
 * Fallback-Listing – MedCare Pro Theme
 *
 * @package MedCarePro_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

try {
    $posts = \CMS\Services\PostService::getPosts(['per_page' => 12]);
} catch (\Throwable) {
    $posts = [];
}
?>
<main id="main" class="mc-main mc-page" role="main">
    <div class="mc-container mc-page__container">
        <h1 class="mc-page__title">Beiträge</h1>

        <?php if (!empty($posts)) : ?>
        <div class="mc-grid mc-grid--blog">
            <?php foreach ($posts as $p) :
                $url     = $safe((string) ($p->url ?? mc_href('/')));
                $title   = $safe((string) ($p->title ?? ''));
                $excerpt = $safe((string) ($p->excerpt ?? ''));
            ?>
            <article class="mc-card mc-post-card">
                <div class="mc-post-card__body">
                    <h2 class="mc-post-card__title"><a href="<?php echo $url; ?>"><?php echo $title; ?></a></h2>
                    <?php if ($excerpt !== '') : ?>
                    <p class="mc-post-card__excerpt"><?php echo $excerpt; ?></p>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <p class="mc-page__empty">Noch keine Beiträge.</p>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
