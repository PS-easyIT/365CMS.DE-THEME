<?php
/**
 * TechNexus Theme – Index / Archiv
 *
 * @package TechNexus_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$posts = [];
try {
    $posts = \CMS\Services\PostService::getPosts(['per_page' => 12]);
} catch (\Throwable) {
    $posts = [];
}
if (!is_array($posts)) {
    $posts = [];
}
?>

<main id="main" class="site-main tn-section" role="main">
    <div class="container">
        <header class="tn-page-hero">
            <h1>Beiträge</h1>
        </header>

        <?php if ($posts !== []) : ?>
            <div class="tech-grid">
                <?php foreach ($posts as $post) :
                    $pTitle   = htmlspecialchars((string) ($post->title   ?? ''), ENT_QUOTES, 'UTF-8');
                    $pExcerpt = htmlspecialchars((string) ($post->excerpt ?? ''), ENT_QUOTES, 'UTF-8');
                    $pUrl     = htmlspecialchars((string) ($post->url     ?? theme_route_url('home')), ENT_QUOTES, 'UTF-8');
                    $pDate    = !empty($post->created_at)
                        ? gmdate('d.m.Y', strtotime((string) $post->created_at))
                        : '';
                ?>
                    <article class="tech-card tn-post-card">
                        <header>
                            <?php if ($pDate !== '') : ?>
                                <time class="tn-post-card__date" datetime="<?php echo tn_html_attr($pDate); ?>">
                                    <?php echo $pDate; ?>
                                </time>
                            <?php endif; ?>
                            <h2 class="tn-post-card__title">
                                <a href="<?php echo $pUrl; ?>"><?php echo $pTitle; ?></a>
                            </h2>
                        </header>
                        <?php if ($pExcerpt !== '') : ?>
                            <p class="tech-card__meta"><?php echo $pExcerpt; ?></p>
                        <?php endif; ?>
                        <a href="<?php echo $pUrl; ?>" class="tech-badge tn-read-more">Weiterlesen</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="tech-card tech-card--placeholder">
                <p>Noch keine Beiträge vorhanden.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
