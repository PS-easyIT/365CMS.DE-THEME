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
            "SELECT p.*, COALESCE(NULLIF(p.author_display_name, ''), NULLIF(u.display_name, ''), NULLIF(u.username, ''), 'Autor') AS author_name, c.name AS category_name
             FROM {$pfx}posts p
             LEFT JOIN {$pfx}users u ON u.id = p.author_id
             LEFT JOIN {$pfx}post_categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.status = 'published'",
            [$slug]
        );
        $post = $postObj ? (array)$postObj : null;
    } catch (\Throwable $e) { $post = null; }
}
if (!$post) { http_response_code(404); get_theme_part('404'); exit; }
try { $db->execute("UPDATE {$pfx}posts SET views = views + 1 WHERE id = ?", [(int)($post['id'] ?? 0)]); } catch (\Throwable) {}
try {
    $prevOb = $db->get_row("SELECT id, title, slug FROM {$pfx}posts WHERE status='published' AND published_at < ? AND id != ? ORDER BY published_at DESC LIMIT 1", [$post['published_at'] ?? '9999-12-31', (int)($post['id'] ?? 0)]);
    $nextOb = $db->get_row("SELECT id, title, slug FROM {$pfx}posts WHERE status='published' AND published_at > ? AND id != ? ORDER BY published_at ASC LIMIT 1",  [$post['published_at'] ?? '0001-01-01', (int)($post['id'] ?? 0)]);
    $prevPost = $prevOb ? (array)$prevOb : null;
    $nextPost = $nextOb ? (array)$nextOb : null;
} catch (\Throwable) { $prevPost = null; $nextPost = null; }
try {
    $commentRows  = \CMS\Services\CommentService::getInstance()->getApprovedForPost((int)($post['id'] ?? 0));
    $comments     = array_map(fn($c) => (array)$c, $commentRows);
    $commentCount = count($comments);
} catch (\Throwable) { $comments = []; $commentCount = 0; }

// ── Auto-ID-Injection + TOC ────────────────────────────────────────────
$content   = $post['content'] ?? '';
$usedSlugs = [];
$content   = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h\1>/i', function ($m) use (&$usedSlugs) {
    $level = $m[1]; $attrs = $m[2]; $inner = $m[3];
    if (preg_match('/id="[^"]+"/i', $attrs)) return $m[0];
    $text = strip_tags($inner);
    $slug = mb_strtolower(trim($text));
    $slug = str_replace(['ä','ö','ü','ß'], ['ae','oe','ue','ss'], $slug);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-') ?: 'h';
    $base = $slug;
    $i = 2;
    while (isset($usedSlugs[$slug])) { $slug = $base . '-' . $i++; }
    $usedSlugs[$slug] = true;
    return "<h{$level}{$attrs} id=\"{$slug}\">{$inner}</h{$level}>";
}, $content) ?? $content;

$tocItems = [];
if ($showToc) {
    preg_match_all('/<h([23])[^>]*id="([^"]+)"[^>]*>(.*?)<\/h\1>/i', $content, $tm, PREG_SET_ORDER);
    foreach ($tm as $match) {
        $tocItems[] = ['level' => (int)$match[1], 'id' => $match[2], 'text' => strip_tags($match[3])];
    }
    if (count($tocItems) < $tocMinHeadings) $tocItems = [];
}

$content = phinit_enhance_content_images($content);

// ── CSRF & Kommentar-Handler ───────────────────────────────────────────
$commentError = $commentSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $honeypot = trim((string) ($_POST['comment_hp'] ?? ''));
    if ($honeypot !== '') {
        header('Location: ' . $siteUrl . '/blog/' . rawurlencode((string) ($post['slug'] ?? '')) . '?commented=1#comments');
        exit;
    }
    if (!\CMS\Security::instance()->verifyToken($_POST['csrf_token'] ?? '', 'comment_post_' . ($post['id'] ?? 0))) {
        $commentError = 'Sicherheitscheck fehlgeschlagen. Bitte Seite neu laden.';
    } else {
        $commentUser = \CMS\Auth::isLoggedIn() ? \CMS\Auth::getCurrentUser() : null;
        $commentUserId = !empty($commentUser->id) ? (int) $commentUser->id : null;
        $name    = trim((string) ($_POST['comment_name'] ?? ''));
        $emailRaw = trim((string) ($_POST['comment_email'] ?? ''));
        $email   = $commentUserId ? $emailRaw : filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
        $text    = trim((string) ($_POST['comment_text'] ?? ''));
        if ($text === '' || ($commentUserId === null && ($name === '' || !$email))) {
            $commentError = 'Bitte alle Pflichtfelder ausfüllen.';
        } else {
            try {
                $newId = \CMS\Services\CommentService::getInstance()->createPendingComment(
                    (int) ($post['id'] ?? 0),
                    $name,
                    (string) $email,
                    $text,
                    (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
                    $commentUserId
                );
                if ($newId === false) {
                    $commentError = 'Bitte alle Pflichtfelder korrekt ausfüllen.';
                } else {
                    header('Location: ' . $siteUrl . '/blog/' . rawurlencode((string) ($post['slug'] ?? '')) . '?commented=1#comments');
                    exit;
                }
            } catch (\Throwable $ex) { $commentError = 'Fehler beim Speichern des Kommentars.'; }
        }
    }
}
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
    'url' => '/blog/' . rawurlencode((string) ($post['slug'] ?? '')),
    'excerpt' => trim((string) ($post['excerpt'] ?? '')),
    'featured_image' => (string) ($post['featured_image'] ?? ''),
    'badge' => (string) ($post['category_name'] ?? 'Beitrag'),
]);

$postTags = [];
if ($showPostTags) {
    try {
        $tagRows = $db->get_results(
            "SELECT t.name, t.slug FROM {$pfx}tags t
             INNER JOIN {$pfx}post_tags pt ON pt.tag_id = t.id
             WHERE pt.post_id = ? ORDER BY t.name ASC",
            [(int)($post['id'] ?? 0)]
        ) ?: [];
        $postTags = array_map(fn($tag) => (array) $tag, $tagRows);
    } catch (\Throwable) {}
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
            $postUrl   = htmlspecialchars(urlencode($siteUrl . '/blog/' . ($post['slug'] ?? '')), ENT_QUOTES);
            $postTitle = htmlspecialchars(urlencode($post['title'] ?? ''), ENT_QUOTES);
            ?>
            <a href="https://www.linkedin.com/shareArticle?url=<?php echo $postUrl; ?>&title=<?php echo $postTitle; ?>" class="share-btn li" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">in LinkedIn</a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>&text=<?php echo $postTitle; ?>" class="share-btn tw" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X">𝕏 Twitter</a>
            <a href="mailto:?subject=<?php echo $postTitle; ?>&body=<?php echo $postUrl; ?>" class="share-btn em" aria-label="Per E-Mail senden">✉ E-Mail</a>
            <button class="share-btn cp" aria-label="Link kopieren">📋 Link kopieren</button>
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
