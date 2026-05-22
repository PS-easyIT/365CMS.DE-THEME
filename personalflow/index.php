<?php
/**
 * PersonalFlow Theme – Generic Posts Listing Fallback
 *
 * @package PersonalFlow_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

try {
    $posts = \CMS\Services\PostService::getPosts(['per_page' => 12]);
} catch (\Throwable) {
    $posts = [];
}

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="pf-main pf-page-shell" role="main">
    <div class="pf-container">
        <div class="pf-section-header">
            <span class="pf-section-eyebrow">Magazin</span>
            <h1>Beiträge</h1>
        </div>

        <?php if (!empty($posts)) : ?>
            <div class="pf-grid-3">
                <?php foreach ($posts as $post) :
                    $title   = (string) ($post->title   ?? '');
                    $excerpt = (string) ($post->excerpt ?? '');
                    $url     = (string) ($post->url     ?? '#');
                    $created = (string) ($post->created_at ?? '');
                    $dateStr = '';
                    if ($created !== '') {
                        $ts = strtotime($created);
                        if ($ts !== false) {
                            $dateStr = gmdate('d.m.Y', $ts);
                        }
                    }
                    ?>
                    <article class="pf-card pf-reveal">
                        <?php if ($dateStr !== '') : ?>
                            <div class="pf-meta-line"><?php echo $safe($dateStr); ?></div>
                        <?php endif; ?>
                        <h2 class="pf-job-title">
                            <a href="<?php echo $safe(pf_href($url)); ?>" class="pf-focus-shadow"><?php echo $safe($title); ?></a>
                        </h2>
                        <?php if ($excerpt !== '') : ?>
                            <p><?php echo $safe($excerpt); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="pf-card pf-prose-wrap">
                <p>Noch keine Beiträge vorhanden.</p>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
