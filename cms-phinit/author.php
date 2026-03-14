<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$author = isset($author) ? (array) $author : [];
$posts = isset($posts) && is_array($posts) ? $posts : [];
$total = isset($total) ? (int) $total : 0;
$currentPage = isset($currentPage) ? (int) $currentPage : 1;
$totalPages = isset($totalPages) ? (int) $totalPages : 1;

$authorName = trim((string) ($author['display_name'] ?? 'Autor'));
$authorBio = trim((string) ($author['bio'] ?? ''));
$authorAvatar = trim((string) ($author['avatar_url'] ?? ''));
$authorDetails = isset($author['details']) && is_array($author['details']) ? $author['details'] : [];
$authorSlug = trim((string) ($author['slug'] ?? ''));
$authorProfileUrl = $siteUrl . '/author/' . rawurlencode($authorSlug !== '' ? $authorSlug : ('user-' . (int) ($author['id'] ?? 0)));
$showActivity = !empty($author['show_activity']);
$authorInitials = 'AU';

if ($authorName !== '') {
    $parts = preg_split('/\s+/u', $authorName) ?: [];
    $authorInitials = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $authorInitials .= mb_strtoupper((string) mb_substr((string) $part, 0, 1), 'UTF-8');
    }
    $authorInitials = $authorInitials !== '' ? $authorInitials : 'AU';
}

$permalinkService = \CMS\Services\PermalinkService::getInstance();

include __DIR__ . '/header.php';
?>

<main class="container blog-shell author-profile-shell">
    <section class="author-profile-hero" data-anim>
        <div class="author-profile-card">
            <div class="author-profile-card__inner">
                <?php if ($authorAvatar !== ''): ?>
                <img src="<?php echo htmlspecialchars($authorAvatar, ENT_QUOTES); ?>"
                     alt="<?php echo htmlspecialchars($authorName, ENT_QUOTES); ?>"
                     class="author-profile-avatar"
                     loading="lazy"
                     width="86"
                     height="86">
                <?php else: ?>
                <div class="author-profile-avatar-placeholder" aria-hidden="true"><?php echo htmlspecialchars($authorInitials, ENT_QUOTES); ?></div>
                <?php endif; ?>

                <div class="author-profile-card__content">
                    <div class="author-profile-card__eyebrow">Autorinnen & Autoren</div>
                    <h1><?php echo htmlspecialchars($authorName, ENT_QUOTES); ?></h1>
                    <?php if ($authorBio !== ''): ?>
                    <p class="author-profile-card__bio"><?php echo htmlspecialchars($authorBio, ENT_QUOTES); ?></p>
                    <?php else: ?>
                    <p class="author-profile-card__bio">Öffentliche Profilangaben dieses Accounts, freigegeben über den Datenschutz-Bereich im Member-Dashboard.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <aside class="author-profile-sidebar" aria-label="Profilangaben">
            <h2>Öffentliche Profilangaben</h2>
            <?php if (!empty($authorDetails)): ?>
            <div class="author-profile-details">
                <?php foreach ($authorDetails as $detail): ?>
                <?php
                    $detail = is_array($detail) ? $detail : [];
                    $detailLabel = trim((string) ($detail['label'] ?? '')); 
                    $detailValue = trim((string) ($detail['value'] ?? ''));
                    $detailType = trim((string) ($detail['type'] ?? 'text'));
                    if ($detailLabel === '' || $detailValue === '') {
                        continue;
                    }
                ?>
                <div class="author-profile-detail">
                    <span class="author-profile-detail__label"><?php echo htmlspecialchars($detailLabel, ENT_QUOTES); ?></span>
                    <div class="author-profile-detail__value">
                        <?php if (in_array($detailType, ['url', 'email'], true)): ?>
                            <?php $href = $detailType === 'email' ? 'mailto:' . $detailValue : $detailValue; ?>
                            <a href="<?php echo htmlspecialchars($href, ENT_QUOTES); ?>"<?php echo $detailType === 'url' ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>><?php echo htmlspecialchars($detailValue, ENT_QUOTES); ?></a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($detailValue, ENT_QUOTES); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="author-profile-posts__empty">Für diese Author-Seite wurden aktuell keine zusätzlichen Profilfelder freigegeben.</p>
            <?php endif; ?>
        </aside>
    </section>

    <section class="author-profile-posts" data-anim data-anim-delay="1">
        <h2><?php echo $showActivity ? 'Veröffentlichte Beiträge' : 'Beiträge dieses Autors'; ?></h2>

        <?php if (!empty($posts)): ?>
        <div class="article-list article-list--framed">
            <?php foreach ($posts as $post): ?>
            <?php
                $post = (array) $post;
                $postTitle = trim((string) ($post['title'] ?? 'Beitrag'));
                $postExcerpt = trim((string) ($post['excerpt'] ?? ''));
                if ($postExcerpt === '') {
                    $postExcerpt = mb_substr(trim(strip_tags((string) ($post['content'] ?? ''))), 0, 180);
                }
                $postCategory = trim((string) ($post['category_name'] ?? 'Beitrag'));
                $postPath = $permalinkService->buildPostPath($post);
                $postUrl = $siteUrl . $postPath;
                $postDate = trim((string) ($post['published_at'] ?? $post['created_at'] ?? ''));
                $postTimestamp = $postDate !== '' ? strtotime($postDate) : false;
                $postImage = trim((string) ($post['featured_image'] ?? ''));
            ?>
            <article class="article-card">
                <a class="article-thumb" href="<?php echo htmlspecialchars($postUrl, ENT_QUOTES); ?>" aria-label="<?php echo htmlspecialchars($postTitle, ENT_QUOTES); ?> öffnen">
                    <?php if ($postImage !== ''): ?>
                    <img src="<?php echo htmlspecialchars($postImage, ENT_QUOTES); ?>"
                         alt="<?php echo htmlspecialchars($postTitle, ENT_QUOTES); ?>"
                         loading="lazy"
                         width="162"
                         height="215">
                    <?php else: ?>
                    <div class="article-thumb-placeholder" aria-hidden="true">✍</div>
                    <?php endif; ?>
                    <span class="thumb-badge badge badge-teal"><?php echo htmlspecialchars($postCategory, ENT_QUOTES); ?></span>
                </a>
                <div class="article-body">
                    <h4><a href="<?php echo htmlspecialchars($postUrl, ENT_QUOTES); ?>"><?php echo htmlspecialchars($postTitle, ENT_QUOTES); ?></a></h4>
                    <p><?php echo htmlspecialchars($postExcerpt, ENT_QUOTES); ?></p>
                    <div class="article-meta">
                        <?php if ($postTimestamp !== false): ?>
                        <time datetime="<?php echo htmlspecialchars(date(DATE_ATOM, $postTimestamp), ENT_QUOTES); ?>"><?php echo htmlspecialchars(date('j. F Y', $postTimestamp), ENT_QUOTES); ?></time>
                        <?php endif; ?>
                        <a class="article-meta__more" href="<?php echo htmlspecialchars($postUrl, ENT_QUOTES); ?>">Artikel lesen →</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="blog-pagination" aria-label="Seitennavigation Author-Seite">
            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                <?php if ($page === $currentPage): ?>
                <span class="current" aria-current="page"><?php echo $page; ?></span>
                <?php else: ?>
                <a href="<?php echo htmlspecialchars($authorProfileUrl . '?page=' . $page, ENT_QUOTES); ?>"><?php echo $page; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php else: ?>
        <p class="author-profile-posts__empty"><?php echo htmlspecialchars($authorName, ENT_QUOTES); ?> hat aktuell noch keine veröffentlichten Beiträge.</p>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/footer.php';
