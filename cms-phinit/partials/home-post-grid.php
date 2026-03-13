<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$gridPosts = isset($gridPosts) && is_array($gridPosts) ? $gridPosts : [];
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;

if (empty($_showTileGrid) || $gridPosts === []) {
    return;
}
?>
<section class="content-section home-section home-section--grid" data-anim data-anim-delay="2">
    <div class="section-header">
        <span class="section-label">📰 <?php echo htmlspecialchars((string) $_tileLabel, ENT_QUOTES); ?></span>
    </div>

    <div class="posts-grid posts-grid--cols-<?php echo (int) $_tileCols; ?>">
        <?php foreach ($gridPosts as $i => $post): ?>
        <article class="post-card" data-anim data-anim-delay="<?php echo min((int) $i + 1, 4); ?>">

            <?php if (!empty($post['featured_image'])): ?>
            <div class="post-card-thumb">
                <?php if (!empty($_showTileCat) && !empty($post['category_name'])): ?>
                <span class="post-card-badge"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                <?php endif; ?>
                <img src="<?php echo htmlspecialchars((string) $post['featured_image'], ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars((string) ($post['title'] ?? ''), ENT_QUOTES); ?>"
                     loading="lazy">
            </div>
            <?php else: ?>
            <div class="post-card-thumb post-card-thumb--placeholder">
                <?php if (!empty($_showTileCat) && !empty($post['category_name'])): ?>
                <span class="post-card-badge"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                <?php endif; ?>
                <span class="post-card-thumb__icon">📄</span>
            </div>
            <?php endif; ?>

            <div class="post-card-body">
                <h3 class="post-card-title">
                    <a href="<?php echo htmlspecialchars((string) ($post['permalink'] ?? ($siteUrl . '/blog/' . ($post['slug'] ?? ''))), ENT_QUOTES); ?>">
                        <?php echo phinit_escape_text($post['title'] ?? ''); ?>
                    </a>
                </h3>
                <?php
                $_tileExc = function_exists('phinit_excerpt_plain_text')
                    ? phinit_excerpt_plain_text((string) ($post['excerpt'] ?? ''))
                    : strip_tags((string) ($post['excerpt'] ?? ''));
                if (trim($_tileExc) === '' && !empty($post['content'])) {
                    $_tileExc = function_exists('phinit_excerpt_plain_text')
                        ? phinit_excerpt_plain_text((string) $post['content'])
                        : strip_tags((string) $post['content']);
                }
                if (!empty($_showTileExc) && trim($_tileExc) !== ''): ?>
                <p class="post-card-excerpt"><?php echo htmlspecialchars(mb_strimwidth($_tileExc, 0, (int) $_tileExcLen, '…'), ENT_QUOTES); ?></p>
                <?php endif; ?>
                <div class="post-card-meta">
                    <div class="post-card-meta__left">
                    <?php if (!empty($_showTileCat) && !empty($post['category_name'])): ?>
                    <span class="cat"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($_showTileDate)): ?>
                    <?php $postDateRaw = $post['published_at'] ?? ($post['created_at'] ?? ''); ?>
                    <span><?php echo htmlspecialchars(!empty($postDateRaw) ? date('j. M. Y', strtotime((string) $postDateRaw)) : '', ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    </div>
                    <a class="post-card-meta__more"
                       href="<?php echo htmlspecialchars((string) ($post['permalink'] ?? ($siteUrl . '/blog/' . ($post['slug'] ?? ''))), ENT_QUOTES); ?>">
                        &hellip; Weiter &rarr;
                    </a>
                </div>
            </div>

        </article>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="pagination" aria-label="Seitennavigation">
        <?php if ($currentPage > 1): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $currentPage - 1; ?>" class="page-link" aria-label="Vorherige Seite">← Zurück</a>
        <?php endif; ?>

        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
            <?php if ($page === 1 || $page === $totalPages || abs($page - $currentPage) <= 2): ?>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $page; ?>" class="page-link <?php echo $page === $currentPage ? 'active' : ''; ?>" <?php echo $page === $currentPage ? 'aria-current="page"' : ''; ?>><?php echo $page; ?></a>
            <?php elseif (abs($page - $currentPage) === 3): ?>
            <span class="page-link dots" aria-hidden="true">…</span>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES); ?>/blog?page=<?php echo $currentPage + 1; ?>" class="page-link" aria-label="Nächste Seite">Weiter →</a>
        <?php endif; ?>
    </nav>
    <?php endif; ?>
</section>