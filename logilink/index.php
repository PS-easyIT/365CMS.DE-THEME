<?php
/**
 * LogiLink Theme – Index / Blog Archive
 *
 * @package LogiLink_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}


// Der Core übergibt die Beiträge ($posts, Objekte) samt Pagination-Daten an das Template.
$posts = isset($posts) && is_array($posts) ? $posts : [];

$safe = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
?>
<main id="main" class="ll-main ll-prose-wrap" role="main">
    <div class="ll-container">
        <header class="ll-section-header">
            <span class="ll-eyebrow">Beiträge</span>
            <h1>Beiträge &amp; Updates</h1>
        </header>

        <?php if (!empty($posts)) : ?>
            <div class="ll-grid">
                <?php foreach ($posts as $p) :
                    $url      = theme_post_url($p);
                    $title    = (string) ($p->title   ?? '');
                    $excerpt  = (string) ($p->excerpt ?? '');
                ?>
                    <article class="ll-card ll-reveal">
                        <h2 class="ll-card-title"><a href="<?php echo $safe($url); ?>"><?php echo $safe($title); ?></a></h2>
                        <?php if ($excerpt !== '') : ?>
                            <p class="ll-card-excerpt"><?php echo $safe($excerpt); ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="ll-card-list-empty">Noch keine Beiträge.</p>
        <?php endif; ?>
    </div>
</main>
