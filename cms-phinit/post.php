<?php
/**
 * Einzelartikel-Template – CMS Phinit Theme
 *
 * Layout:
 *  - Post-Header: Bild, Titel, Datum, Kategorien
 *  - 2-spaltig: Artikel-Body (links) + Sticky Sidebar (rechts)
 *  - Sidebar: TOC + Social Icons + Related Posts + Kategorien
 *  - Share-Buttons unter dem Text
 *  - Zurück/Weiter Navigation
 *  - Kommentarbereich
 *
 * Wird direkt (via /blog/:slug) oder als blog-single.php-Include aufgerufen.
 * Feldnamen richten sich nach dem DB-Schema (cms_posts, cms_comments).
 *
 * @package CMS_Phinit_Theme
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$siteUrl = SITE_URL;
$db      = \CMS\Database::instance();
$prefix  = $db->getPrefix();
$currentLocale = function_exists('phinit_get_current_locale') ? phinit_get_current_locale() : 'de';

// ── Customizer-Einstellungen laden ──────────────────────────────────────────
try {
    $cz = \CMS\Services\ThemeCustomizer::instance();
} catch (\Throwable $e) {
    $cz = null;
}

/**
 * Customizer-Helfer: Wert lesen mit Fallback.
 */
$czGet = function (string $cat, string $key, mixed $default = '') use ($cz): mixed {
    if (!$cz) return $default;
    try { return $cz->get($cat, $key, $default); } catch (\Throwable) { return $default; }
};
$czBool = function (string $cat, string $key, bool $default = true) use ($czGet): bool {
    return filter_var($czGet($cat, $key, $default), FILTER_VALIDATE_BOOLEAN);
};

// Posts-Customizer
$showPostHero      = $czBool('posts', 'show_post_hero', true);
$showPostMeta      = $czBool('posts', 'show_post_meta', true);
$showReadingTime   = $czBool('posts', 'show_reading_time', true);
$readingTimeWpm    = max(50, (int)$czGet('posts', 'reading_time_wpm', 220));
$showToc           = $czBool('posts', 'show_toc', true);
$tocSticky         = $czBool('posts', 'toc_sticky', true);
$tocMinHeadings    = max(1, (int)$czGet('posts', 'toc_min_headings', 2));
$tocHeaderText     = (string)$czGet('posts', 'toc_header_text', '📋 ' . phinit_t('toc_title', [], $currentLocale));
$showSidebarSocial = $czBool('posts', 'show_sidebar_social', true);
$sidebarSocialHdr  = (string)$czGet('posts', 'sidebar_social_header', phinit_t('follow_us', [], $currentLocale));
$showSidebarRelated= $czBool('posts', 'show_sidebar_related', true);
$sidebarRelatedHdr = (string)$czGet('posts', 'sidebar_related_header', phinit_t('related_articles', [], $currentLocale));
$relatedCount      = max(1, min(10, (int)$czGet('posts', 'related_count', 4)));
$showShareButtons  = $czBool('posts', 'show_share_buttons', true);
$showShareLinkedin = $czBool('posts', 'show_share_linkedin', true);
$showShareTwitter  = $czBool('posts', 'show_share_twitter', true);
$showShareEmail    = $czBool('posts', 'show_share_email', true);
$showShareCopy     = $czBool('posts', 'show_share_copy', true);
$showShareMastodon = $czBool('posts', 'show_share_mastodon', true);
$showSharePrint    = $czBool('posts', 'show_share_print', true);
$showPostNav       = $czBool('posts', 'show_post_nav', true);
$showAuthorBox     = $czBool('posts', 'show_author_box', true);
$showComments      = $czBool('posts', 'show_comments', true);
$commentsHeader    = (string)$czGet('posts', 'comments_header', '💬 ' . phinit_t('comments', [], $currentLocale));
$commentFormHeader = (string)$czGet('posts', 'comment_form_header', phinit_t('leave_comment', [], $currentLocale));
$showPostTags      = $czBool('posts', 'show_post_tags', true);

// Social URLs (aus social-Kategorie)
$socialLinkedin = (string)$czGet('social', 'social_linkedin', '');
$socialGithub   = (string)$czGet('social', 'social_github', '');
$socialTwitter  = (string)$czGet('social', 'social_twitter', '');
$socialMastodon = (string)$czGet('social', 'social_mastodon', '');
$socialYoutube  = (string)$czGet('social', 'social_youtube', '');
$socialXing     = (string)$czGet('social', 'social_xing', '');
$socialRss      = (string)$czGet('social', 'social_rss', $siteUrl . '/feed');

// Layout
$sidebarPosition = (string)$czGet('layout', 'sidebar_position', 'right');

// ── Post-Daten laden ────────────────────────────────────────────────────────
if (isset($post) && !empty($post)) {
    $post = is_object($post) ? (array)$post : (array)$post;
    $postProvidedByRouter = true;
} else {
    $postProvidedByRouter = false;
    $rawPath = phinit_current_request_path();
    $rawPath = (string)preg_replace('#^/blog/#i', '', $rawPath);
    $slug    = trim($rawPath, '/');

    if (empty($slug)) {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }

    try {
        $postObj = $db->get_row(
            "SELECT p.*, COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name, c.name AS category_name, c.slug AS category_slug
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}users u ON u.id = p.author_id
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND " . phinit_post_publication_where('p'),
            [$slug]
        );
        $post = $postObj ? (array)$postObj : null;
    } catch (\Throwable $e) {
        $post = null;
    }
}

if (!$post) {
    http_response_code(404);
    get_theme_part('404');
    exit;
}

if (!$postProvidedByRouter && !empty($post['content'])) {
    $post['content'] = phinit_prepare_renderable_content((string)$post['content'], 'post', (int)($post['id'] ?? 0));
}

// Ansichten-Zähler nur bei direktem Fallback erhöhen (Router erledigt das bereits)
if (!$postProvidedByRouter) {
    try {
        $db->execute("UPDATE {$prefix}posts SET views = views + 1 WHERE id = ?", [(int)($post['id'] ?? 0)]);
    } catch (\Throwable) {}
}

// ── Inhalt final sanitizen, Lesezeit berechnen ─────────────────────────────
$content = phinit_sanitize_renderable_content((string) ($post['content'] ?? ''), 'default');
$readingTime = function_exists('phinit_reading_time')
    ? phinit_reading_time($content, $readingTimeWpm)
    : max(1, (int)ceil(str_word_count(strip_tags($content)) / $readingTimeWpm));

// ── Auto-ID Injection für h2–h6 (TOC-Voraussetzung) ────────────────────────
$headingData = phinit_with_heading_ids($content, [2, 3, 4, 5, 6]);
$content = phinit_enhance_content_images($headingData['html']);
$post['content'] = $content;

// ── TOC generieren ──────────────────────────────────────────────────────────
$tocItems = [];
if ($showToc) {
    $tocItems = $headingData['toc'];
    // Mindestanzahl prüfen
    if (count($tocItems) < $tocMinHeadings) {
        $tocItems = [];
    }
}

// ── Vor-/Nächster Post ──────────────────────────────────────────────────────
try {
    $prevPostObj = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts
         WHERE " . phinit_post_publication_where() . " AND COALESCE(published_at, created_at) < ? AND id != ?
         ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT 1",
        [$post['published_at'] ?? ($post['created_at'] ?? '9999-12-31'), (int)($post['id'] ?? 0)]
    );
    $nextPostObj = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts
         WHERE " . phinit_post_publication_where() . " AND COALESCE(published_at, created_at) > ? AND id != ?
         ORDER BY COALESCE(published_at, created_at) ASC, id ASC LIMIT 1",
        [$post['published_at'] ?? ($post['created_at'] ?? '0001-01-01'), (int)($post['id'] ?? 0)]
    );
    $prevPost = $prevPostObj ? (array)$prevPostObj : null;
    $nextPost = $nextPostObj ? (array)$nextPostObj : null;
} catch (\Throwable) {
    $prevPost = null;
    $nextPost = null;
}

// ── Related Posts laden ─────────────────────────────────────────────────────
$relatedPosts = [];
if ($showSidebarRelated) {
    try {
        $catId  = (int)($post['category_id'] ?? 0);
        $postId = (int)($post['id'] ?? 0);
        if ($catId > 0) {
            $relRows = $db->get_results(
                "SELECT id, title, slug, published_at FROM {$prefix}posts
                 WHERE " . phinit_post_publication_where() . " AND category_id = ? AND id != ?
                 ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT ?",
                [$catId, $postId, $relatedCount]
            ) ?: [];
        } else {
            $relRows = $db->get_results(
                "SELECT id, title, slug, published_at FROM {$prefix}posts
                 WHERE " . phinit_post_publication_where() . " AND id != ?
                 ORDER BY COALESCE(published_at, created_at) DESC, id DESC LIMIT ?",
                [$postId, $relatedCount]
            ) ?: [];
        }
        $relatedPosts = array_map(fn($r) => (array)$r, $relRows);
    } catch (\Throwable) {}
}

// ── Tags laden ──────────────────────────────────────────────────────────────
$postTags = $showPostTags ? phinit_parse_post_tags((string) ($post['tags'] ?? '')) : [];

// ── Kommentare laden ────────────────────────────────────────────────────────
$comments     = [];
$commentCount = 0;
if ($showComments) {
    try {
        $commentRows  = \CMS\Services\CommentService::getInstance()->getApprovedForPost((int)($post['id'] ?? 0));
        $comments     = array_map(fn($c) => (array)$c, $commentRows);
        $commentCount = count($comments);
    } catch (\Throwable) {}
}

if ($showComments && phinit_input_int($_GET, 'commented', 0, 0, 1) === 1) {
    $commentSuccess = '✅ Danke! Dein Kommentar wurde gespeichert und wartet auf Freigabe.';
}

$favoriteControl = phinit_get_favorite_control('post', (int) ($post['id'] ?? 0), [
    'title' => (string) ($post['title'] ?? 'Beitrag'),
    'url' => (string) (parse_url(function_exists('phinit_build_post_url') ? phinit_build_post_url($post, $currentLocale) : ('/blog/' . rawurlencode((string) ($post['slug'] ?? ''))), PHP_URL_PATH) ?: '/'),
    'excerpt' => trim((string) ($post['excerpt'] ?? '')),
    'featured_image' => (string) ($post['featured_image'] ?? ''),
    'badge' => (string) ($post['category_name'] ?? 'Beitrag'),
]);

$commentError = $commentError ?? '';
$commentSuccess = $commentSuccess ?? '';
$publishedAt = (string) ($post['published_at'] ?? '');
$updatedAt = (string) ($post['updated_at'] ?? '');
$showUpdatedBadge = $updatedAt !== '' && $updatedAt !== $publishedAt;

$authorId = (int) ($post['author_id'] ?? 0);
$authorBoxUrl = $authorId > 0
    ? (function_exists('phinit_localized_href') ? phinit_localized_href('/author/user-' . $authorId, $currentLocale, $siteUrl) : rtrim($siteUrl, '/') . '/author/user-' . $authorId)
    : '';
$authorBoxContext = function_exists('phinit_build_author_box_context')
    ? phinit_build_author_box_context([
        'category' => 'posts',
        'show_key' => 'show_author_box',
        'entity_name' => (string) ($post['author_name'] ?? ''),
        'author_url' => $authorBoxUrl,
        'site_url' => $siteUrl,
        'locale' => $currentLocale,
        'show_default' => $showAuthorBox,
    ])
    : [];
$renderAuthorBox = !empty($authorBoxContext['show']);

try {
    $csrfToken = \CMS\Security::instance()->generateToken('comment_' . ($post['id'] ?? 0));
} catch (\Throwable $e) {
    $csrfToken = '';
}

// ── Sidebar-Position CSS-Klasse ─────────────────────────────────────────────
$layoutClass = 'content-layout';
if ($sidebarPosition === 'left') {
    $layoutClass .= ' sidebar-left';
} elseif ($sidebarPosition === 'none') {
    $layoutClass .= ' sidebar-none';
}
?>

<div class="container post-container">
<div class="<?php echo $layoutClass; ?>">

    <!-- ── Haupt-Artikelspalte ────────────────────────────────── -->
    <div class="main-column">

        <!-- Post-Header -->
        <article itemscope itemtype="https://schema.org/BlogPosting">

            <?php get_theme_part('partials/post-header', [
                'showPostHero' => $showPostHero,
                'post' => $post,
                'siteUrl' => $siteUrl,
                'showPostMeta' => $showPostMeta,
                'showReadingTime' => $showReadingTime,
                'readingTime' => $readingTime,
                'commentCount' => $commentCount,
                'favoriteControl' => $favoriteControl,
            ]); ?>

            <!-- Artikel-Body -->
            <div class="post-body" itemprop="articleBody" data-photoswipe>
                <?php phinit_render_prepared_content($content); ?>

                <?php if ($showUpdatedBadge): ?>
                <div class="post-footer-meta" aria-label="Beitragsmetadaten">
                    <span class="post-footer-badge post-footer-badge--updated">
                        <span class="post-footer-badge__icon" aria-hidden="true">🔄</span>
                        <span class="post-footer-badge__label"><?php echo htmlspecialchars(phinit_t('updated_label', [], $currentLocale), ENT_QUOTES); ?></span>
                        <time datetime="<?php echo htmlspecialchars($updatedAt, ENT_QUOTES); ?>">
                            <?php echo htmlspecialchars(phinit_format_date($updatedAt, 'numeric', $currentLocale), ENT_QUOTES); ?>
                        </time>
                    </span>
                </div>
                <?php endif; ?>

                <!-- Share-Buttons -->
                <?php if ($showShareButtons): ?>
                <div class="post-share">
                    <span><?php echo htmlspecialchars(phinit_t('share', [], $currentLocale), ENT_QUOTES); ?>:</span>
                    <?php $postUrlRaw = rtrim($siteUrl, '/') . phinit_current_request_path(); ?>
                    <?php $postTitleRaw = (string) ($post['title'] ?? ''); ?>
                    <?php $linkedinShareHref = 'https://www.linkedin.com/shareArticle?' . http_build_query(['url' => $postUrlRaw, 'title' => $postTitleRaw], '', '&', PHP_QUERY_RFC3986); ?>
                    <?php $twitterShareHref = 'https://twitter.com/intent/tweet?' . http_build_query(['url' => $postUrlRaw, 'text' => $postTitleRaw], '', '&', PHP_QUERY_RFC3986); ?>
                    <?php $emailShareHref = 'mailto:?' . http_build_query(['subject' => $postTitleRaw, 'body' => $postUrlRaw], '', '&', PHP_QUERY_RFC3986); ?>
                    <?php if ($showShareLinkedin): ?>
                    <a href="<?php echo htmlspecialchars($linkedinShareHref, ENT_QUOTES); ?>" class="share-btn li" target="_blank" rel="noopener noreferrer" aria-label="Auf LinkedIn teilen">in LinkedIn</a>
                    <?php endif; ?>
                    <?php if ($showShareTwitter): ?>
                    <a href="<?php echo htmlspecialchars($twitterShareHref, ENT_QUOTES); ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer" aria-label="Auf Twitter/X teilen">𝕏 Twitter</a>
                    <?php endif; ?>
                    <?php if ($showShareMastodon): ?>
                    <a href="<?php echo htmlspecialchars(phinit_get_mastodon_share_url($postUrlRaw, $postTitleRaw, $socialMastodon), ENT_QUOTES); ?>" class="share-btn ma" target="_blank" rel="noopener noreferrer" aria-label="Auf Mastodon teilen">🦣 Mastodon</a>
                    <?php endif; ?>
                    <?php if ($showShareEmail): ?>
                    <a href="<?php echo htmlspecialchars($emailShareHref, ENT_QUOTES); ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
                    <?php endif; ?>
                    <?php if ($showShareCopy): ?>
                    <button type="button" class="share-btn cp" data-share-copy="1" aria-label="Link kopieren">📋 Kopieren</button>
                    <?php endif; ?>
                    <?php if ($showSharePrint): ?>
                    <button type="button" class="share-btn pr" data-share-print="1" aria-label="Artikel drucken">🖨 Drucken</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($post['also_available_lang'])): ?>
            <p class="post-alt-language">This post is also available in 🇬🇧 <a href="<?php echo htmlspecialchars($post['also_available_lang']['url'] ?? '#', ENT_QUOTES); ?>" class="post-alt-language__link">English</a></p>
            <?php endif; ?>

        </article>

        <!-- Vor-/Nächster Artikel -->
        <?php if ($showPostNav): ?>
        <?php get_theme_part('partials/post-navigation', [
            'prevPost' => $prevPost,
            'nextPost' => $nextPost,
            'siteUrl' => $siteUrl,
        ]); ?>
        <?php endif; ?>

        <?php if ($renderAuthorBox): ?>
        <?php get_theme_part('partials/post-author-box', $authorBoxContext); ?>
        <?php endif; ?>

        <!-- Kommentare -->
        <?php get_theme_part('partials/post-comments', [
            'showComments' => $showComments,
            'commentsHeader' => $commentsHeader,
            'comments' => $comments,
            'commentError' => $commentError,
            'commentSuccess' => $commentSuccess,
            'commentFormHeader' => $commentFormHeader,
            'csrfToken' => $csrfToken,
            'post' => $post,
        ]); ?>

    </div><!-- /.main-column -->

    <!-- ── Sticky Sidebar ────────────────────────────────────── -->
    <?php if ($sidebarPosition !== 'none'):
        get_theme_part('partials/sidebar', [
            'show_toc'       => $showToc,
            'toc_items'      => $tocItems,
            'toc_sticky'     => $tocSticky,
            'toc_header'     => $tocHeaderText,
            'show_social'    => $showSidebarSocial,
            'social_header'  => $sidebarSocialHdr,
            'social_linkedin'=> $socialLinkedin,
            'social_github'  => $socialGithub,
            'social_twitter' => $socialTwitter,
            'social_mastodon'=> $socialMastodon,
            'social_youtube' => $socialYoutube,
            'social_xing'    => $socialXing,
            'social_rss'     => $socialRss,
            'show_related'   => $showSidebarRelated,
            'related_header' => $sidebarRelatedHdr,
            'related_posts'  => $relatedPosts,
            'post_tags'      => $postTags,
            'site_url'       => $siteUrl,
            'post'           => $post,
        ]);
    endif; /* sidebar_position !== 'none' */ ?>

</div><!-- /.content-layout -->
</div><!-- /.container -->
