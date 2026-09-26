<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}


// Der Core übergibt die Beiträge ($posts, Objekte) samt Pagination-Daten an das Template.
$posts = isset($posts) && is_array($posts) ? $posts : [];
$safe  = fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$siteUrl = buildbase_safe_url((string) SITE_URL, '/');
?>
<main id="main" class="bb-main bb-page-section" role="main">
    <div class="bb-container">
        <h1 class="bb-page-title">Beiträge</h1>

        <?php if (!empty($posts)) : ?>
            <div class="bb-grid">
                <?php foreach ($posts as $post) :
                    $postUrl   = buildbase_safe_url(theme_post_url($post), $siteUrl);
                    $postTitle = isset($post->title)   ? (string) $post->title   : '';
                    $excerpt   = isset($post->excerpt) ? (string) $post->excerpt : '';
                ?>
                    <article class="bb-card">
                        <h2 class="bb-post-title">
                            <a href="<?php echo $safe($postUrl); ?>"><?php echo $safe($postTitle); ?></a>
                        </h2>
                        <?php if ($excerpt !== '') : ?>
                            <p class="bb-post-excerpt"><?php echo $safe($excerpt); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="bb-empty">Noch keine Beiträge.</p>
        <?php endif; ?>
    </div>
</main>
