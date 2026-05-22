<?php
/**
 * Blog-Übersicht – MedCare Pro Theme
 *
 * Erwartet optional: $posts (array), $total (int), $currentPage (int), $totalPages (int)
 *
 * @package MedCarePro_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$safe       = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
$blogUrl    = $safe(theme_route_url('blog'));
$page       = max(1, (int) ($_GET['page'] ?? $currentPage ?? 1));

try {
    if (empty($posts)) {
        $posts = \CMS\Services\PostService::getPosts(['per_page' => 12, 'page' => $page, 'status' => 'published']);
    }
    $total      = $total      ?? \CMS\Services\PostService::getCount(['status' => 'published']);
    $totalPages = $totalPages ?? (int) ceil(($total ?: 0) / 12);
} catch (\Throwable) {
    $posts = [];
    $total = 0;
    $totalPages = 1;
}
?>
<main id="main" class="mc-main mc-blog-archive" role="main">
    <div class="mc-section mc-section--blog">
        <div class="mc-container">

            <div class="mc-section-header mc-blog-header">
                <h1 id="blog-heading" class="mc-blog-title">
                    <span class="mc-blog-title-eyebrow">Gesundheitsratgeber</span>
                    <span class="mc-blog-title-line">Medizinische Informationen &amp; Praxistipps</span>
                </h1>
                <p>Beiträge von zertifizierten Experten – kuratiert für Patienten und Praxisteams.</p>
            </div>

            <?php if (!empty($posts)) : ?>
            <div class="mc-grid mc-grid--blog">
                <?php foreach ($posts as $p) :
                    $fallback = mc_href('/blog/' . ($p->slug ?? (string) ($p->id ?? '')));
                    $url      = $safe((string) ($p->url ?? $fallback));
                    $title    = $safe((string) ($p->title ?? ''));
                    $excerpt  = $safe((string) ($p->excerpt ?? ''));
                    $date     = isset($p->created_at) ? date('d.m.Y', strtotime((string) $p->created_at)) : '';
                    $author   = $safe((string) ($p->author_name ?? ''));
                    $category = $safe((string) ($p->category_name ?? ''));
                    $imgUrl   = $safe((string) ($p->thumbnail_url ?? ''));
                    $postId   = (int) ($p->id ?? 0);
                ?>
                <article class="mc-card mc-post-card" aria-labelledby="post-<?php echo $postId; ?>">
                    <?php if ($imgUrl !== '') : ?>
                    <a href="<?php echo $url; ?>" class="mc-post-card__media" tabindex="-1" aria-hidden="true">
                        <img src="<?php echo $imgUrl; ?>" alt="" class="mc-post-card__img">
                    </a>
                    <?php endif; ?>

                    <div class="mc-post-card__body">
                        <?php if ($category !== '') : ?>
                        <span class="mc-specialty-badge mc-post-card__category"><?php echo $category; ?></span>
                        <?php endif; ?>

                        <h2 id="post-<?php echo $postId; ?>" class="mc-post-card__title">
                            <a href="<?php echo $url; ?>"><?php echo $title; ?></a>
                        </h2>
                        <?php if ($excerpt !== '') : ?>
                        <p class="mc-post-card__excerpt"><?php echo $excerpt; ?></p>
                        <?php endif; ?>

                        <footer class="mc-post-card__meta">
                            <?php if ($author !== '') : ?><span class="mc-post-card__author"><?php echo $author; ?></span><?php endif; ?>
                            <?php if ($date !== '') : ?><time class="mc-post-card__date" datetime="<?php echo $safe($date); ?>"><?php echo $date; ?></time><?php endif; ?>
                            <a href="<?php echo $url; ?>" class="mc-btn mc-btn-outline mc-btn-sm mc-post-card__more">
                                Weiterlesen
                            </a>
                        </footer>
                    </div>
                </article>
                <?php endforeach; ?>
            </div><!-- /.mc-grid -->

            <?php else : ?>
            <div class="mc-card mc-empty-state">
                <div class="mc-empty-state__icon" aria-hidden="true">📋</div>
                <h2 class="mc-empty-state__title">Noch keine Beiträge</h2>
                <p class="mc-empty-state__text">Bald erscheinen hier medizinische Fachartikel und Gesundheitstipps.</p>
            </div>
            <?php endif; ?>

            <?php if ($totalPages > 1) : ?>
            <nav class="mc-pagination" aria-label="Seitennavigation">
                <?php if ($page > 1) : ?>
                    <a href="<?php echo $blogUrl; ?>?page=<?php echo $page - 1; ?>" class="mc-btn mc-btn-outline mc-btn-sm" rel="prev">← Zurück</a>
                <?php endif; ?>
                <span class="mc-pagination__status">Seite <?php echo $page; ?> von <?php echo (int) $totalPages; ?></span>
                <?php if ($page < $totalPages) : ?>
                    <a href="<?php echo $blogUrl; ?>?page=<?php echo $page + 1; ?>" class="mc-btn mc-btn-outline mc-btn-sm" rel="next">Weiter →</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>

        </div>
    </div>
</main>
<?php get_footer(); ?>
