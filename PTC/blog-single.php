<?php
/**
 * PTC Theme – Einzelner Blogbeitrag
 *
 * Vom Router bereitgestellte Variable:
 *   $post – stdObject mit Post-Daten
 *
 * @package PTC_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (empty($post)) {
    header('Location: ' . SITE_URL . '/blog');
    exit;
}

$siteUrl = ptc_site_url();

/* ─── Customizer-Einstellungen ─── */
$showFeatured    = ptc_customizer_get('blog', 'blog_single_show_featured',     '1');
$showAuthor      = ptc_customizer_get('blog', 'blog_single_show_author',       '1');
$showDate        = ptc_customizer_get('blog', 'blog_single_show_date',         '1');
$showTags        = ptc_customizer_get('blog', 'blog_single_show_tags',         '1');
$showRelated     = ptc_customizer_get('blog', 'blog_single_show_related',      '1');
$showReadingTime = ptc_customizer_get('blog', 'blog_single_show_reading_time', '1');
$maxWidth        = (int) ptc_customizer_get('blog', 'blog_single_max_width',   '820');

/* ─── Post-Daten aufbereiten ─── */
$pTitle    = htmlspecialchars($post->title ?? '', ENT_QUOTES, 'UTF-8');
$pSlug     = $post->slug ?? '';
$pContent  = $post->content ?? '';
$pExcerpt  = htmlspecialchars($post->excerpt ?? '', ENT_QUOTES, 'UTF-8');
$pDate     = isset($post->published_at) ? date('d.m.Y', strtotime($post->published_at)) : '';
$pDateLong = isset($post->published_at) ? date('d. F Y', strtotime($post->published_at)) : '';
$pDateIso  = htmlspecialchars($post->published_at ?? '', ENT_QUOTES, 'UTF-8');
$pAuthor   = htmlspecialchars($post->author_name ?? 'Redaktion', ENT_QUOTES, 'UTF-8');
$pAuthIni  = mb_strtoupper(mb_substr($pAuthor, 0, 2));
$pCat      = htmlspecialchars($post->category_name ?? '', ENT_QUOTES, 'UTF-8');
$pCatSlug  = $post->category_slug ?? '';
$pViews    = (int) ($post->views ?? 0);
$pImage    = $post->featured_image ?? '';
$pTags     = $post->tags ?? [];
if (is_string($pTags)) {
    $pTags = array_filter(array_map('trim', explode(',', $pTags)));
}

/* ─── Lesezeit ─── */
$wordCount = str_word_count(strip_tags($pContent));
$readMins  = max(1, (int) ceil($wordCount / 200));

/* ─── Verwandte Beiträge ─── */
$relatedPosts = [];
if ($showRelated) {
    try {
        $db     = \CMS\Database::instance();
        $prefix = $db->getPrefix();
        $catId  = (int) ($post->category_id ?? 0);
        $postId = (int) ($post->id ?? 0);
        if ($catId > 0) {
            $stmt = $db->execute(
                "SELECT p.id, p.title, p.slug, p.excerpt, p.published_at, p.featured_image,
                        c.name AS category_name
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
}

$allowedTags = '<p><br><strong><b><em><i><u><s>'
    . '<h1><h2><h3><h4><h5><h6>'
    . '<ul><ol><li><dl><dt><dd>'
    . '<a><img>'
    . '<blockquote><pre><code>'
    . '<table><thead><tbody><tr><th><td>'
    . '<div><span><section><article><aside>'
    . '<hr><figure><figcaption><iframe><video><audio><source>';
?>

<!-- ── Breadcrumb ── -->
<nav class="ptc-breadcrumb" aria-label="Breadcrumb">
    <div class="ptc-container">
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/">Startseite</a>
        <span class="ptc-breadcrumb__sep">›</span>
        <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog">Blog</a>
        <?php if ($pCat) : ?>
            <span class="ptc-breadcrumb__sep">›</span>
            <a href="<?php echo htmlspecialchars($siteUrl . '/blog?category=' . urlencode($pCatSlug), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $pCat; ?></a>
        <?php endif; ?>
        <span class="ptc-breadcrumb__sep">›</span>
        <span class="ptc-breadcrumb__current"><?php echo $pTitle; ?></span>
    </div>
</nav>

<!-- ── Article ── -->
<article class="ptc-article ptc-prose--constrained">
    <div class="ptc-container">

        <!-- Article Header -->
        <header class="ptc-article__header">
            <?php if ($pCat) : ?>
                <a href="<?php echo htmlspecialchars($siteUrl . '/blog?category=' . urlencode($pCatSlug), ENT_QUOTES, 'UTF-8'); ?>"
                   class="ptc-article__category"><?php echo $pCat; ?></a>
            <?php endif; ?>

            <h1 class="ptc-article__title"><?php echo $pTitle; ?></h1>

            <?php if ($pExcerpt) : ?>
                <p class="ptc-article__intro"><?php echo $pExcerpt; ?></p>
            <?php endif; ?>

            <div class="ptc-article__meta">
                <?php if ($showAuthor) : ?>
                    <div class="ptc-article__author">
                        <span class="ptc-article__avatar"><?php echo $pAuthIni; ?></span>
                        <span><?php echo $pAuthor; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($showDate && $pDateLong) : ?>
                    <time datetime="<?php echo $pDateIso; ?>">📅 <?php echo $pDateLong; ?></time>
                <?php endif; ?>
                <?php if ($showReadingTime) : ?>
                    <span>📖 <?php echo $readMins; ?> Min. Lesezeit</span>
                <?php endif; ?>
                <?php if ($pViews > 0) : ?>
                    <span>👁️ <?php echo number_format($pViews); ?> Aufrufe</span>
                <?php endif; ?>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if ($showFeatured && $pImage) : ?>
        <figure class="ptc-article__featured">
            <img src="<?php echo htmlspecialchars($pImage, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="<?php echo $pTitle; ?>" loading="lazy">
        </figure>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="ptc-article__content ptc-prose sun-editor-editable">
            <?php echo strip_tags($pContent, $allowedTags); ?>
        </div>

        <!-- Tags -->
        <?php if ($showTags && !empty($pTags)) : ?>
        <div class="ptc-article__tags">
            <span>🏷️ Tags:</span>
            <?php foreach ($pTags as $tag) :
                $tEsc = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');
            ?>
            <a href="<?php echo htmlspecialchars($siteUrl . '/blog?tag=' . urlencode($tag), ENT_QUOTES, 'UTF-8'); ?>"
               class="ptc-tag"><?php echo $tEsc; ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Share / Back -->
        <div class="ptc-article__actions">
            <a href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>/blog"
               class="ptc-btn ptc-btn--outline">← Zurück zum Blog</a>
        </div>

    </div>
</article>

<!-- ── Verwandte Beiträge ── -->
<?php if ($showRelated && !empty($relatedPosts)) : ?>
<section class="ptc-related">
    <div class="ptc-container">
        <h2>📌 Ähnliche Beiträge</h2>
        <div class="ptc-related__grid">
            <?php foreach ($relatedPosts as $rel) :
                $rArr   = (array) $rel;
                $rTitle = htmlspecialchars($rArr['title'] ?? '', ENT_QUOTES, 'UTF-8');
                $rSlug  = $rArr['slug'] ?? '';
                $rImage = $rArr['featured_image'] ?? '';
                $rDate  = isset($rArr['published_at']) ? date('d.m.Y', strtotime($rArr['published_at'])) : '';
                $rCat   = htmlspecialchars($rArr['category_name'] ?? '', ENT_QUOTES, 'UTF-8');
                $rUrl   = htmlspecialchars($siteUrl . '/blog/' . $rSlug, ENT_QUOTES, 'UTF-8');
                $rExcerpt = $rArr['excerpt'] ?? '';
                if (mb_strlen($rExcerpt) > 100) {
                    $rExcerpt = mb_substr($rExcerpt, 0, 97) . '…';
                }
                $rExcerpt = htmlspecialchars(strip_tags($rExcerpt), ENT_QUOTES, 'UTF-8');
            ?>
            <article class="ptc-related__card">
                <?php if ($rImage) : ?>
                <a href="<?php echo $rUrl; ?>" class="ptc-related__image">
                    <img src="<?php echo htmlspecialchars($rImage, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?php echo $rTitle; ?>" loading="lazy">
                </a>
                <?php endif; ?>
                <div class="ptc-related__body">
                    <?php if ($rCat) : ?>
                        <span class="ptc-blog-card__category"><?php echo $rCat; ?></span>
                    <?php endif; ?>
                    <h3><a href="<?php echo $rUrl; ?>"><?php echo $rTitle; ?></a></h3>
                    <?php if ($rExcerpt) : ?>
                        <p><?php echo $rExcerpt; ?></p>
                    <?php endif; ?>
                    <?php if ($rDate) : ?>
                        <span class="ptc-related__date">📅 <?php echo $rDate; ?></span>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
