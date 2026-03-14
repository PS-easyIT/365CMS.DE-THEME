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
$tocHeaderText     = (string)$czGet('posts', 'toc_header_text', '📋 Inhaltsverzeichnis');
$showSidebarSocial = $czBool('posts', 'show_sidebar_social', true);
$sidebarSocialHdr  = (string)$czGet('posts', 'sidebar_social_header', 'Folge uns');
$showSidebarRelated= $czBool('posts', 'show_sidebar_related', true);
$sidebarRelatedHdr = (string)$czGet('posts', 'sidebar_related_header', 'Ähnliche Artikel');
$relatedCount      = max(1, min(10, (int)$czGet('posts', 'related_count', 4)));
$showShareButtons  = $czBool('posts', 'show_share_buttons', true);
$showShareLinkedin = $czBool('posts', 'show_share_linkedin', true);
$showShareTwitter  = $czBool('posts', 'show_share_twitter', true);
$showShareEmail    = $czBool('posts', 'show_share_email', true);
$showShareCopy     = $czBool('posts', 'show_share_copy', true);
$showComments      = $czBool('posts', 'show_comments', true);
$commentsHeader    = (string)$czGet('posts', 'comments_header', '💬 Kommentare');
$commentFormHeader = (string)$czGet('posts', 'comment_form_header', 'Kommentar hinterlassen');
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
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $rawPath = (string)preg_replace('#^/blog/#i', '', $rawPath);
    $slug    = trim($rawPath, '/');

    if (empty($slug)) {
        http_response_code(404);
        get_theme_part('404');
        exit;
    }

    try {
        $postObj = $db->get_row(
            "SELECT p.*, COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name, c.name AS category_name
             FROM {$prefix}posts p
             LEFT JOIN {$prefix}users u ON u.id = p.author_id
             LEFT JOIN {$prefix}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'",
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

// ── Lesezeit berechnen ──────────────────────────────────────────────────────
$content     = $post['content'] ?? '';
$readingTime = function_exists('phinit_reading_time')
    ? phinit_reading_time($content, $readingTimeWpm)
    : max(1, (int)ceil(str_word_count(strip_tags($content)) / $readingTimeWpm));

// ── Auto-ID Injection für h2/h3 (TOC-Voraussetzung) ────────────────────────
$headingData = phinit_with_heading_ids($content, [2, 3]);
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
         WHERE status = 'published' AND published_at < ? AND id != ?
         ORDER BY published_at DESC LIMIT 1",
        [$post['published_at'] ?? '9999-12-31', (int)($post['id'] ?? 0)]
    );
    $nextPostObj = $db->get_row(
        "SELECT id, title, slug FROM {$prefix}posts
         WHERE status = 'published' AND published_at > ? AND id != ?
         ORDER BY published_at ASC LIMIT 1",
        [$post['published_at'] ?? '0001-01-01', (int)($post['id'] ?? 0)]
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
                 WHERE status = 'published' AND category_id = ? AND id != ?
                 ORDER BY published_at DESC LIMIT ?",
                [$catId, $postId, $relatedCount]
            ) ?: [];
        } else {
            $relRows = $db->get_results(
                "SELECT id, title, slug, published_at FROM {$prefix}posts
                 WHERE status = 'published' AND id != ?
                 ORDER BY published_at DESC LIMIT ?",
                [$postId, $relatedCount]
            ) ?: [];
        }
        $relatedPosts = array_map(fn($r) => (array)$r, $relRows);
    } catch (\Throwable) {}
}

// ── Tags laden ──────────────────────────────────────────────────────────────
$postTags = [];
if ($showPostTags) {
    try {
        $tagRows = $db->get_results(
            "SELECT t.id, t.name, t.slug
             FROM {$prefix}post_tags t
             INNER JOIN {$prefix}post_tag_rel ptr ON ptr.tag_id = t.id
             WHERE ptr.post_id = ?
             ORDER BY t.name ASC",
            [(int)($post['id'] ?? 0)]
        ) ?: [];
        $postTags = array_map(fn($r) => (array)$r, $tagRows);
    } catch (\Throwable) {}
}

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

// ── Kommentar abschicken ────────────────────────────────────────────────────
if ($showComments && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $commentError   = '';
    $commentSuccess = '';
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $commentUser = \CMS\Auth::isLoggedIn() ? \CMS\Auth::getCurrentUser() : null;
        $commentUserId = !empty($commentUser->id) ? (int) $commentUser->id : null;
        $name     = trim((string)($_POST['comment_name']  ?? ''));
        $emailRaw = trim((string)($_POST['comment_email'] ?? ''));
        $email    = $commentUserId ? $emailRaw : filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
        $text     = trim((string)($_POST['comment_text'] ?? ''));
        $honeypot = trim((string)($_POST['comment_hp'] ?? ''));

        if ($honeypot !== '') {
            header('Location: ' . $siteUrl . '/blog/' . rawurlencode((string)($post['slug'] ?? '')) . '?commented=1#comments');
            exit;
        }

        if ($text === '' || ($commentUserId === null && ($name === '' || !$email))) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $newId = \CMS\Services\CommentService::getInstance()->createPendingComment(
                    (int)($post['id'] ?? 0),
                    $name,
                    (string)$email,
                    $text,
                    (string)($_SERVER['REMOTE_ADDR'] ?? ''),
                    $commentUserId
                );

                if ($newId === false) {
                    $commentError = 'Bitte alle Pflichtfelder korrekt ausfüllen.';
                } else {
                    header('Location: ' . $siteUrl . '/blog/' . rawurlencode((string)($post['slug'] ?? '')) . '?commented=1#comments');
                    exit;
                }
            } catch (\Throwable $ex) {
                $commentError = 'Fehler beim Speichern des Kommentars.';
            }
        }
    }
}

if ($showComments && (int)($_GET['commented'] ?? 0) === 1) {
    $commentSuccess = '✅ Danke! Dein Kommentar wurde gespeichert und wartet auf Freigabe.';
}

$favoriteControl = phinit_get_favorite_control('post', (int) ($post['id'] ?? 0), [
    'title' => (string) ($post['title'] ?? 'Beitrag'),
    'url' => '/blog/' . rawurlencode((string) ($post['slug'] ?? '')),
    'excerpt' => trim((string) ($post['excerpt'] ?? '')),
    'featured_image' => (string) ($post['featured_image'] ?? ''),
    'badge' => (string) ($post['category_name'] ?? 'Beitrag'),
]);

$commentError = $commentError ?? '';
$commentSuccess = $commentSuccess ?? '';

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
                <?php echo $content; ?>

                <!-- Tags -->
                <?php if ($showPostTags && !empty($postTags)): ?>
                <div class="post-tags">
                    <?php foreach ($postTags as $tag): ?>
                    <a href="<?php echo htmlspecialchars($siteUrl . '/tag/' . urlencode(phinit_display_text($tag['slug'] ?? '')), ENT_QUOTES); ?>" class="post-tag">#<?php echo phinit_escape_text($tag['name'] ?? ''); ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Share-Buttons -->
                <?php if ($showShareButtons): ?>
                <div class="post-share">
                    <span>Teilen:</span>
                    <?php $postUrl = htmlspecialchars(urlencode($siteUrl . '/blog/' . ($post['slug'] ?? '')), ENT_QUOTES); ?>
                    <?php $postTitle = htmlspecialchars(urlencode($post['title'] ?? ''), ENT_QUOTES); ?>
                    <?php if ($showShareLinkedin): ?>
                    <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer" aria-label="Auf LinkedIn teilen">in LinkedIn</a>
                    <?php endif; ?>
                    <?php if ($showShareTwitter): ?>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer" aria-label="Auf Twitter/X teilen">𝕏 Twitter</a>
                    <?php endif; ?>
                    <?php if ($showShareEmail): ?>
                    <a href="mailto:?subject=<?php echo $postTitle; ?>&body=<?php echo $postUrl; ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
                    <?php endif; ?>
                    <?php if ($showShareCopy): ?>
                    <button class="share-btn cp" aria-label="Link kopieren">📋 Kopieren</button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($post['also_available_lang'])): ?>
            <p class="post-alt-language">This post is also available in 🇬🇧 <a href="<?php echo htmlspecialchars($post['also_available_lang']['url'] ?? '#', ENT_QUOTES); ?>" class="post-alt-language__link">English</a></p>
            <?php endif; ?>

        </article>

        <!-- Vor-/Nächster Artikel -->
        <?php get_theme_part('partials/post-navigation', [
            'prevPost' => $prevPost,
            'nextPost' => $nextPost,
            'siteUrl' => $siteUrl,
        ]); ?>

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
        ]);
    endif; /* sidebar_position !== 'none' */ ?>

</div><!-- /.content-layout -->
</div><!-- /.container -->
