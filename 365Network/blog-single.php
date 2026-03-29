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
    header('Location: ' . SITE_URL . '/blog');
    exit;
}

$siteUrl = SITE_URL;

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
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/">Startseite</a>
            <span class="sep">›</span>
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog">Blog</a>
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
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog">← Zurück zum Blog</a>
        </div>

    </div>
</main>

<style>
/* ===== Blog Single – 365Network Theme ===== */
.blog-single-page {
    padding: 2rem 0 3rem;
    min-height: 60vh;
}
.blog-single-container {
    max-width: 820px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Breadcrumb */
.blog-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    gap: .25rem;
    font-size: .75rem;
    color: var(--muted-color, #94a3b8);
    margin-bottom: 2rem;
}
.blog-breadcrumb a {
    color: var(--secondary-color, #64748b);
    text-decoration: none;
}
.blog-breadcrumb a:hover {
    color: var(--accent-color, #e8a838);
}
.blog-breadcrumb .sep {
    color: var(--muted-color, #94a3b8);
}
.blog-breadcrumb .current {
    color: var(--text-primary, #1e293b);
    font-weight: 500;
}

/* Article */
.blog-article {
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: var(--border-radius-lg, 16px);
    overflow: hidden;
}
.blog-article__header {
    padding: 2rem 2rem 1.5rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
}
.blog-article__category {
    display: inline-block;
    padding: .1875rem .625rem;
    background: rgba(200,149,46,.12);
    color: var(--accent-color, #e8a838);
    border-radius: 4px;
    font-size: .6875rem;
    font-weight: 600;
    margin-bottom: .75rem;
}
.blog-article__title {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--heading-color, var(--text-primary));
    margin: 0 0 .75rem;
    line-height: 1.3;
}
.blog-article__intro {
    font-size: 1rem;
    color: var(--secondary-color, #64748b);
    line-height: 1.6;
    margin: 0 0 1rem;
}
.blog-article__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .5rem;
    font-size: .78rem;
    color: var(--muted-color, #94a3b8);
}
.blog-article__author {
    display: flex;
    align-items: center;
    gap: .5rem;
}
.author-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary-color, #1e3a5f);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .75rem;
    flex-shrink: 0;
}
.author-avatar--lg {
    width: 56px;
    height: 56px;
    font-size: 1rem;
}
.author-name {
    display: block;
    font-weight: 600;
    color: var(--text-primary, #1e293b);
    font-size: .8125rem;
}
.author-role {
    display: block;
    font-size: .6875rem;
    color: var(--muted-color, #94a3b8);
}
.meta-sep {
    color: var(--muted-color, #94a3b8);
    font-size: .65rem;
}

/* Content */
.blog-article__content {
    padding: 2rem;
    font-size: .9375rem;
    line-height: 1.8;
    color: var(--text-primary, #1e293b);
}
.blog-article__content h2 {
    font-size: 1.375rem;
    font-weight: 700;
    margin: 2rem 0 .75rem;
    color: var(--heading-color, var(--text-primary));
}
.blog-article__content h3 {
    font-size: 1.125rem;
    font-weight: 700;
    margin: 1.5rem 0 .5rem;
}
.blog-article__content p {
    margin: 0 0 1rem;
}
.blog-article__content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--border-radius, 8px);
    margin: 1rem 0;
}
.blog-article__content a {
    color: var(--accent-color, #e8a838);
}
.blog-article__content blockquote {
    border-left: 3px solid var(--accent-color, #e8a838);
    margin: 1.5rem 0;
    padding: .75rem 1.25rem;
    background: var(--bg-secondary, #f1f5f9);
    border-radius: 0 var(--border-radius, 8px) var(--border-radius, 8px) 0;
    font-style: italic;
    color: var(--secondary-color, #64748b);
}
.blog-article__content pre {
    background: #0f172a;
    color: #e2e8f0;
    padding: 1rem 1.25rem;
    border-radius: var(--border-radius, 8px);
    overflow-x: auto;
    font-size: .8125rem;
    margin: 1rem 0;
}
.blog-article__content code {
    background: var(--bg-secondary, #f1f5f9);
    padding: .125rem .375rem;
    border-radius: 4px;
    font-size: .8125rem;
}
.blog-article__content pre code {
    background: none;
    padding: 0;
}

/* Footer / Tags */
.blog-article__footer {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1.25rem 2rem;
    border-top: 1px solid var(--border-color, #e2e8f0);
}
.tags-label {
    font-size: .8125rem;
    font-weight: 600;
    color: var(--secondary-color, #64748b);
    white-space: nowrap;
}
.blog-article__tags {
    display: flex;
    flex-wrap: wrap;
    gap: .375rem;
}
.tag-pill {
    display: inline-block;
    padding: .1875rem .5rem;
    background: var(--bg-secondary, #f1f5f9);
    color: var(--secondary-color, #64748b);
    border-radius: 4px;
    font-size: .6875rem;
    text-decoration: none;
    transition: var(--transition-fast, all .15s ease);
}
.tag-pill:hover {
    background: var(--accent-color, #e8a838);
    color: #fff;
}

/* Author Box */
.blog-author-box {
    display: flex;
    gap: 1rem;
    align-items: center;
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: var(--border-radius, 8px);
    padding: 1.5rem;
    margin: 2rem 0;
}
.blog-author-box__info .label {
    font-size: .6875rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--muted-color, #94a3b8);
    margin-bottom: .25rem;
    display: block;
}
.blog-author-box__info h4 {
    margin: 0 0 .25rem;
    font-size: 1rem;
    color: var(--heading-color, var(--text-primary));
}
.blog-author-box__info p {
    margin: 0;
    font-size: .8125rem;
    color: var(--secondary-color, #64748b);
}

/* Related Posts */
.blog-related {
    margin: 2rem 0;
}
.blog-related h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--heading-color, var(--text-primary));
    margin: 0 0 1rem;
}
.blog-related__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.blog-related__card {
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: var(--border-radius, 8px);
    overflow: hidden;
    text-decoration: none;
    transition: var(--transition, all .3s ease);
}
.blog-related__card:hover {
    box-shadow: var(--shadow-md, 0 4px 12px rgba(0,0,0,.12));
    transform: translateY(-2px);
}
.blog-related__image {
    aspect-ratio: 16/9;
    overflow: hidden;
}
.blog-related__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.blog-related__image--placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--primary-color,#1e3a5f), #0f172a);
    font-size: 2rem;
}
.blog-related__body {
    padding: 1rem;
}
.blog-related__cat {
    font-size: .6875rem;
    font-weight: 600;
    color: var(--accent-color, #e8a838);
}
.blog-related__body h4 {
    font-size: .875rem;
    font-weight: 700;
    color: var(--heading-color, var(--text-primary));
    margin: .25rem 0;
    line-height: 1.4;
}
.blog-related__date {
    font-size: .6875rem;
    color: var(--muted-color, #94a3b8);
}

/* Back Link */
.blog-back {
    text-align: center;
    margin: 2rem 0;
}
.blog-back a {
    color: var(--accent-color, #e8a838);
    text-decoration: none;
    font-size: .875rem;
    font-weight: 600;
}
.blog-back a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .blog-single-container {
        padding: 0 1rem;
    }
    .blog-article__header {
        padding: 1.5rem 1.25rem 1rem;
    }
    .blog-article__title {
        font-size: 1.375rem;
    }
    .blog-article__content {
        padding: 1.25rem;
    }
    .blog-article__footer {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem 1.25rem;
    }
    .blog-author-box {
        flex-direction: column;
        text-align: center;
    }
    .blog-related__grid {
        grid-template-columns: 1fr;
    }
}
</style>
