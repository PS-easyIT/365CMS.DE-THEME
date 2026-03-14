<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$gridPosts = isset($gridPosts) && is_array($gridPosts) ? $gridPosts : [];
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;
$siteUrl = isset($siteUrl) ? (string) $siteUrl : SITE_URL;
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';

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
            <?php
            $postDateRaw = $post['published_at'] ?? ($post['created_at'] ?? '');
            $tileReadTime = !empty($post['read_time']) ? (int) $post['read_time'] : 0;
            if ($tileReadTime < 1 && !empty($post['content'])) {
                $tileReadTime = function_exists('phinit_reading_time')
                    ? phinit_reading_time((string) $post['content'])
                    : max(1, (int) round(str_word_count(strip_tags((string) $post['content'])) / 220));
            }
            ?>

            <?php if (!empty($post['featured_image'])): ?>
            <div class="post-card-thumb">
                <?php if (!empty($_showTileCat) && !empty($post['category_name'])): ?>
                <span class="post-card-badge"><?php echo phinit_escape_text($post['category_name'] ?? ''); ?></span>
                <?php endif; ?>
                <img src="<?php echo htmlspecialchars((string) $post['featured_image'], ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars((string) ($post['title'] ?? ''), ENT_QUOTES); ?>"
                     <?php echo phinit_image_loading_attributes(); ?>>
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
                <?php if (!empty($_showTileDate) && !empty($postDateRaw)): ?>
                <div class="post-card-top-meta">
                    <span class="post-card-top-meta__date"><?php echo htmlspecialchars(phinit_format_date((string) $postDateRaw, 'long', $currentLocale), ENT_QUOTES); ?></span>
                    <?php if ($tileReadTime > 0): ?>
                    <span class="post-card-top-meta__read" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                </div>
                <?php elseif ($tileReadTime > 0): ?>
                <div class="post-card-top-meta">
                    <span class="post-card-top-meta__read" aria-label="<?php echo htmlspecialchars(phinit_t('read_time_aria', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?>"><?php echo htmlspecialchars(phinit_t('read_time_short', ['minutes' => $tileReadTime], $currentLocale), ENT_QUOTES); ?></span>
                </div>
                <?php endif; ?>
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
                    </div>
                    <a class="post-card-meta__more"
                       href="<?php echo htmlspecialchars((string) ($post['permalink'] ?? ($siteUrl . '/blog/' . ($post['slug'] ?? ''))), ENT_QUOTES); ?>">
                        <span class="post-card-meta__more-label post-card-meta__more-label--desktop"><?php echo htmlspecialchars(phinit_t('continue_reading', [], $currentLocale), ENT_QUOTES); ?></span>
                        <span class="post-card-meta__more-label post-card-meta__more-label--mobile">Weiter</span>
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