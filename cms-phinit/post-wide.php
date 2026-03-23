<?php
/**
 * Einzelartikel – Vollbreite-Template (ohne Sidebar)
 *
 * Template-ID: post-wide
 * Unterschiede zu post.php:
 *  - Kein 2-Spalten-Layout, kein .sidebar
 *  - TOC als eingebautes, aufklappbares <details> vor dem Artikel-Body
 *  - Breiter zentrierter Artikel (max. 860 px)
 *  - Für lange Leseartikel, Tutorials, Guides geeignet
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$db      = \CMS\Database::instance();
$pfx     = $db->getPrefix();

// ── Customizer ─────────────────────────────────────────────────────────
$cz = null;
try { $cz = \CMS\Services\ThemeCustomizer::instance(); } catch (\Throwable) {}
$czGet = function (string $cat, string $key, mixed $default = '') use ($cz): mixed {
    if (!$cz) return $default;
    try { return $cz->get($cat, $key, $default); } catch (\Throwable) { return $default; }
};
$czBool = function (string $cat, string $key, bool $default = true) use ($czGet): bool {
    return filter_var($czGet($cat, $key, $default), FILTER_VALIDATE_BOOLEAN);
};

// Customizer-Werte (posts / layout)
$showPostHero     = $czBool('posts', 'show_post_hero', true);
$showPostMeta     = $czBool('posts', 'show_post_meta', true);
$showReadingTime  = $czBool('posts', 'show_reading_time', true);
$readingTimeWpm   = max(100, (int)$czGet('posts', 'reading_time_wpm', 200));
$showToc          = $czBool('posts', 'show_toc', true);
$tocMinHeadings   = max(1, (int)$czGet('posts', 'toc_min_headings', 3));
$tocHeaderText    = (string)$czGet('posts', 'toc_header_text', 'Inhaltsverzeichnis');
$showShareButtons = $czBool('posts', 'show_share_buttons', true);
$showShareLinkedin = $czBool('posts', 'show_share_linkedin', true);
$showShareTwitter = $czBool('posts', 'show_share_twitter', true);
$showShareEmail = $czBool('posts', 'show_share_email', true);
$showShareCopy = $czBool('posts', 'show_share_copy', true);
$showShareMastodon = $czBool('posts', 'show_share_mastodon', true);
$showSharePrint = $czBool('posts', 'show_share_print', true);
$showComments     = $czBool('posts', 'show_comments', true);
$commentsHeader   = (string)$czGet('posts', 'comments_header', '💬 Kommentare');
$commentFormHeader = (string)$czGet('posts', 'comment_form_header', 'Kommentar hinterlassen');
$showPostTags     = $czBool('posts', 'show_post_tags', true);

// ── Daten laden ────────────────────────────────────────────────────────
if (isset($post) && !empty($post)) {
    $post = is_object($post) ? (array)$post : (array)$post;
} else {
    $rawPath = (string)preg_replace('#^/blog/#i', '', parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
    $slug    = trim($rawPath, '/');
    if (empty($slug)) { http_response_code(404); get_theme_part('404'); exit; }
    try {
        $postObj = $db->get_row(
            "SELECT p.*, COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name, c.name AS category_name, c.slug AS category_slug
             FROM {$pfx}posts p
             LEFT JOIN {$pfx}users u ON u.id = p.author_id
             LEFT JOIN {$pfx}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND " . phinit_post_publication_where('p'),
            [$slug]
        );
        $post = $postObj ? (array)$postObj : null;
    } catch (\Throwable $e) { $post = null; }
}
if (!$post) { http_response_code(404); get_theme_part('404'); exit; }
try { $db->execute("UPDATE {$pfx}posts SET views = views + 1 WHERE id = ?", [(int)($post['id'] ?? 0)]); } catch (\Throwable) {}
try {
    $prevOb = $db->get_row("SELECT id, title, slug FROM {$pfx}posts WHERE " . phinit_post_publication_where() . " AND COALESCE(published_at, created_at) < ? AND id != ? ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT 1", [$post['published_at'] ?? ($post['created_at'] ?? '9999-12-31'), (int)($post['id'] ?? 0)]);
    $nextOb = $db->get_row("SELECT id, title, slug FROM {$pfx}posts WHERE " . phinit_post_publication_where() . " AND COALESCE(published_at, created_at) > ? AND id != ? ORDER BY COALESCE(published_at, created_at) ASC, id ASC LIMIT 1",  [$post['published_at'] ?? ($post['created_at'] ?? '0001-01-01'), (int)($post['id'] ?? 0)]);
    $prevPost = $prevOb ? (array)$prevOb : null;
    $nextPost = $nextOb ? (array)$nextOb : null;
} catch (\Throwable) { $prevPost = null; $nextPost = null; }
try {
    $commentRows  = \CMS\Services\CommentService::getInstance()->getApprovedForPost((int)($post['id'] ?? 0));
    $comments     = array_map(fn($c) => (array)$c, $commentRows);
    $commentCount = count($comments);
} catch (\Throwable) { $comments = []; $commentCount = 0; }

// ── Auto-ID-Injection + TOC ────────────────────────────────────────────
$content = (string) ($post['content'] ?? '');
$headingData = phinit_with_heading_ids($content, [2, 3, 4, 5, 6]);
$tocItems = [];
if ($showToc) {
    $tocItems = $headingData['toc'];
    if (count($tocItems) < $tocMinHeadings) {
        $tocItems = [];
    }
}

$content = phinit_enhance_content_images($headingData['html']);

// ── Kommentarstatus & CSRF für /comments/post ─────────────────────────
$commentError = $commentSuccess = '';
if ((int) ($_GET['commented'] ?? 0) === 1) {
    $commentSuccess = '✅ Danke! Dein Kommentar wurde gespeichert und wartet auf Freigabe.';
}
try { $csrfToken = \CMS\Security::instance()->generateToken('comment_' . ($post['id'] ?? 0)); } catch (\Throwable $e) { $csrfToken = ''; }
// ── Reading Time ───────────────────────────────────────────────────────
$readTime = function_exists('phinit_reading_time') ? phinit_reading_time($content, $readingTimeWpm) : 0;
$readingTime = $readTime;
$commentLinkTarget = '#comments';
$favoriteControl = phinit_get_favorite_control('post', (int) ($post['id'] ?? 0), [
    'title' => (string) ($post['title'] ?? 'Beitrag'),
    'url' => (string) (parse_url(function_exists('phinit_build_post_url') ? phinit_build_post_url($post, $currentLocale) : ('/blog/' . rawurlencode((string) ($post['slug'] ?? ''))), PHP_URL_PATH) ?: '/'),
    'excerpt' => trim((string) ($post['excerpt'] ?? '')),
    'featured_image' => (string) ($post['featured_image'] ?? ''),
    'badge' => (string) ($post['category_name'] ?? 'Beitrag'),
]);

$postTags = [];
if ($showPostTags) {
    $postTags = phinit_parse_post_tags((string) ($post['tags'] ?? ''));
}
?>

<div class="container post-container">
<article class="article-layout--wide" itemscope itemtype="https://schema.org/BlogPosting">

    <!-- ── Post-Header ───────────────────────────────────────────────── -->
    <?php include __DIR__ . '/partials/post-header.php'; ?>

    <!-- ── Inline-TOC (aufklappbar) ──────────────────────────────────── -->
    <?php include __DIR__ . '/partials/post-inline-toc.php'; ?>

    <!-- ── Artikel-Body ──────────────────────────────────────────────── -->
    <div class="post-body" itemprop="articleBody" data-photoswipe data-anim data-anim-delay="2">
        <?php echo $content; ?>

        <!-- Share-Buttons -->
        <?php if ($showShareButtons): ?>
        <div class="post-share">
            <span>Teilen:</span>
            <?php
            $postUrlRaw = rtrim($siteUrl, '/') . phinit_current_request_path();
            $postUrl = htmlspecialchars(rawurlencode($postUrlRaw), ENT_QUOTES);
            $postTitleRaw = (string) ($post['title'] ?? '');
            $postTitle = htmlspecialchars(rawurlencode($postTitleRaw), ENT_QUOTES);
            ?>
            <?php if ($showShareLinkedin): ?>
            <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in LinkedIn</a>
            <?php endif; ?>
            <?php if ($showShareTwitter): ?>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">𝕏 Twitter</a>
            <?php endif; ?>
            <?php if ($showShareMastodon): ?>
            <a href="<?php echo htmlspecialchars(phinit_get_mastodon_share_url($postUrlRaw, $postTitleRaw, (string) $czGet('social', 'social_mastodon', '')), ENT_QUOTES); ?>" class="share-btn ma" target="_blank" rel="noopener noreferrer" aria-label="Mastodon">🦣 Mastodon</a>
            <?php endif; ?>
            <?php if ($showShareEmail): ?>
            <a href="mailto:?subject=<?php echo $postTitle; ?>&body=<?php echo $postUrl; ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
            <?php endif; ?>
            <?php if ($showShareCopy): ?>
            <button type="button" class="share-btn cp" data-share-copy="1" aria-label="Link kopieren">📋 Link kopieren</button>
            <?php endif; ?>
            <?php if ($showSharePrint): ?>
            <button type="button" class="share-btn pr" data-share-print="1" aria-label="Artikel drucken">🖨 Drucken</button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── Vor-/Nächster Artikel ──────────────────────────────────────── -->
    <?php include __DIR__ . '/partials/post-navigation.php'; ?>

    <!-- ── Kommentare ─────────────────────────────────────────────────── -->
    <?php include __DIR__ . '/partials/post-comments.php'; ?>

    <?php include __DIR__ . '/partials/post-tags.php'; ?>

</article>
</div><!-- /.container -->
