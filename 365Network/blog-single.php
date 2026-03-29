<?php
/**
 * Single Blog Post Template – 365Network Dark Navy Dashboard
 *
 * Vom Router bereitgestellte Variablen:
 *   $post – stdObject mit Post-Daten
 *
 * @package IT_Expert_Network_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (empty($post)) {
    header('Location: ' . theme_safe_url(SITE_URL . '/blog', SITE_URL . '/blog'));
    exit;
}

$siteUrl = SITE_URL;
$homeUrl = theme_safe_url($siteUrl . '/', $siteUrl . '/');
$blogUrl = theme_safe_url($siteUrl . '/blog', $siteUrl . '/blog');

// Variablen vorbereiten
$pTitle   = htmlspecialchars($post->title ?? '', ENT_QUOTES, 'UTF-8');
$pSlug    = $post->slug ?? '';
$pContent = theme_sanitize_html((string)($post->content ?? ''), 'default');
$pExcerpt = htmlspecialchars($post->excerpt ?? '', ENT_QUOTES, 'UTF-8');
$pDate    = isset($post->published_at) ? htmlspecialchars((string)time_ago($post->published_at), ENT_QUOTES, 'UTF-8') : '';
$pDateLong = $pDate;
$pAuthor  = htmlspecialchars($post->author_name ?? 'Redaktion', ENT_QUOTES, 'UTF-8');
$pAuthIni = mb_strtoupper(mb_substr($pAuthor, 0, 2));
$pCat     = htmlspecialchars($post->category_name ?? '', ENT_QUOTES, 'UTF-8');
$pCatSlug = $post->category_slug ?? '';
$pViews   = (int)($post->views ?? 0);
$pTags    = $post->tags ?? [];
if (is_string($pTags)) {
    $pTags = array_filter(array_map('trim', explode(',', $pTags)));
}

// Lesezeit schätzen
$wordCount = str_word_count(strip_tags($pContent));
$readMins  = max(1, (int)ceil($wordCount / 200));

// Verwandte Beiträge
$relatedPosts = [];
try {
    $db = \CMS\Database::instance();
    $prefix = $db->getPrefix();
    $catId = (int)($post->category_id ?? 0);
    $postId = (int)($post->id ?? 0);
    if ($catId > 0) {
        $stmt = $db->execute(
            "SELECT p.id, p.title, p.slug, p.excerpt, p.published_at, p.featured_image, c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.category_id = ? AND p.id != ? AND p.status = 'published'
             ORDER BY p.published_at DESC
             LIMIT 3",
            [$catId, $postId]
        );
        $relatedPosts = $stmt->fetchAll() ?: [];
    }
} catch (\Throwable $e) {
    // Keine verwandten Beiträge bei Fehler
}
?>

<main id="content" class="blog-single-page">
    <div class="blog-single-container">

        <!-- Breadcrumb -->
        <nav class="blog-breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">Startseite</a>
            <span class="sep">›</span>
            <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8'); ?>">Blog</a>
            <?php if ($pCat) : ?>
                <span class="sep">›</span>
                <a href="<?php echo htmlspecialchars(theme_build_query_url('/blog', ['category' => $pCatSlug]), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $pCat; ?></a>
            <?php endif; ?>
            <span class="sep">›</span>
            <span class="current"><?php echo $pTitle; ?></span>
        </nav>

        <article class="blog-article">

            <!-- Post Header -->
            <header class="blog-article__header">
                <?php if ($pCat) : ?>
                    <span class="blog-article__category"><?php echo $pCat; ?></span>
                <?php endif; ?>

                <h1 class="blog-article__title"><?php echo $pTitle; ?></h1>

                <?php if ($pExcerpt) : ?>
                    <p class="blog-article__intro"><?php echo $pExcerpt; ?></p>
                <?php endif; ?>

                <div class="blog-article__meta">
                    <div class="blog-article__author">
                        <div class="author-avatar"><?php echo $pAuthIni; ?></div>
                        <div>
                            <span class="author-name"><?php echo $pAuthor; ?></span>
                            <span class="author-role">Autor</span>
                        </div>
                    </div>
                    <span class="meta-sep">·</span>
                    <time datetime="<?php echo htmlspecialchars($post->published_at ?? '', ENT_QUOTES, 'UTF-8'); ?>">📅 <?php echo $pDateLong; ?></time>
                    <span class="meta-sep">·</span>
                    <span>📖 <?php echo $readMins; ?> Min. Lesezeit</span>
                    <?php if ($pViews > 0) : ?>
                        <span class="meta-sep">·</span>
                        <span>👁️ <?php echo number_format($pViews); ?> Aufrufe</span>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Post Content -->
            <div class="blog-article__content sun-editor-editable">
                <?php echo $pContent; ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($pTags)) : ?>
            <footer class="blog-article__footer">
                <span class="tags-label">🏷️ Tags:</span>
                <div class="blog-article__tags">
                    <?php foreach ($pTags as $tag) : ?>
                    <a href="<?php echo htmlspecialchars(theme_build_query_url('/blog', ['tag' => $tag]), ENT_QUOTES, 'UTF-8'); ?>" class="tag-pill">
                        <?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </footer>
            <?php endif; ?>

        </article>

        <!-- Autor-Box -->
        <div class="blog-author-box">
            <div class="author-avatar author-avatar--lg"><?php echo $pAuthIni; ?></div>
            <div class="blog-author-box__info">
                <span class="label">Über den Autor</span>
                <h4><?php echo $pAuthor; ?></h4>
                <?php if ($pCat) : ?>
                    <p>Schreibt über <?php echo $pCat; ?> und verwandte Themen.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verwandte Artikel -->
        <?php if (!empty($relatedPosts)) : ?>
        <section class="blog-related">
            <h3>📰 Verwandte Artikel</h3>
            <div class="blog-related__grid">
                <?php foreach ($relatedPosts as $rp) :
                    $rArr   = (array)$rp;
                    $rTitle = htmlspecialchars($rArr['title'] ?? '', ENT_QUOTES, 'UTF-8');
                    $rSlug  = rawurlencode((string)($rArr['slug'] ?? ''));
                    $rCat   = htmlspecialchars($rArr['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                    $rUrl   = htmlspecialchars(theme_safe_url($siteUrl . '/blog/' . $rSlug, $siteUrl . '/blog'), ENT_QUOTES, 'UTF-8');
                    $rImage = theme_safe_url((string)($rArr['featured_image'] ?? ''));
                    $rDate  = isset($rArr['published_at']) ? htmlspecialchars((string)time_ago($rArr['published_at']), ENT_QUOTES, 'UTF-8') : '';
                ?>
                <a href="<?php echo $rUrl; ?>" class="blog-related__card">
                    <?php if ($rImage) : ?>
                    <div class="blog-related__image">
                        <img src="<?php echo htmlspecialchars($rImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $rTitle; ?>" loading="lazy">
                             width="640" height="360">
                    </div>
                    <?php else : ?>
                    <div class="blog-related__image blog-related__image--placeholder">
                        <span>📄</span>
                    </div>
                    <?php endif; ?>
                    <div class="blog-related__body">
                        <?php if ($rCat) : ?>
                            <span class="blog-related__cat"><?php echo $rCat; ?></span>
                        <?php endif; ?>
                        <h4><?php echo $rTitle; ?></h4>
                        <?php if ($rDate) : ?>
                            <span class="blog-related__date">📅 <?php echo $rDate; ?></span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Zurück-Link -->
        <div class="blog-back">
            <a href="<?php echo htmlspecialchars($blogUrl, ENT_QUOTES, 'UTF-8'); ?>">← Zurück zum Blog</a>
        </div>

    </div>
</main>
